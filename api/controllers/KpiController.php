<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';

final class KpiController
{
    // Ditulis tanpa constructor property promotion agar kompatibel dengan
    // PHP 7.4 yang umum dipakai pada deployment AAPanel.
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function input(array $input): array { return $input; }

    private function canManage(array $user): bool
    {
        // Tahap awal: seluruh user yang sudah login boleh mengatur KPI.
        // Pembatasan berdasarkan role dapat ditambahkan setelah alur bisnis stabil.
        return true;
    }

    private function json(int $status, string $message, array $data = []): void
    {
        sendResponse($status, $message, $data);
    }

    private function latestClosingDates(int $year): array
    {
        $sql = "SELECT DISTINCT DATE(created) AS closing_date
                FROM nominatif
                WHERE YEAR(created)=:year AND DATE(created)=LAST_DAY(DATE(created))
                ORDER BY closing_date";
        $st = $this->pdo->prepare($sql);
        $st->execute([':year'=>$year]);
        return array_map(static fn(array $r): string => $r['closing_date'], $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function bootstrap(array $input, array $user): void
    {
        $year = max(2020, min(2100, (int)($input['year'] ?? date('Y'))));
        $jabatan = $this->pdo->query("SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan WHERE aktif=1 ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        $ind = $this->pdo->prepare("SELECT i.*,j.kode AS jabatan_kode,j.nama AS jabatan_nama
                                    FROM kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
                                    WHERE i.status IN ('PILOT','AKTIF') ORDER BY j.nama,i.urutan,i.id");
        $ind->execute();
        $score = $this->pdo->query("SELECT skor,min_indeks,max_indeks,predikat FROM kpi_parameter_skor WHERE aktif=1 ORDER BY skor")->fetchAll(PDO::FETCH_ASSOC);
        $risk = $this->pdo->query("SELECT kode,nama,faktor,perlakuan FROM kpi_risk_gate WHERE aktif=1 ORDER BY faktor DESC")->fetchAll(PDO::FETCH_ASSOC);
        $ao = $this->pdo->query("SELECT kode_group2 AS kode_ao,MAX(nama_ao) AS nama_ao,LPAD(CAST(MAX(kode_kantor) AS CHAR),3,'0') AS kode_kantor
                                 FROM ao_kredit WHERE status=1 GROUP BY kode_group2 ORDER BY nama_ao")->fetchAll(PDO::FETCH_ASSOC);
        $kantor = $this->pdo->query("SELECT kode_kantor,nama_kantor FROM kode_kantor WHERE LPAD(CAST(kode_kantor AS CHAR),3,'0') BETWEEN '001' AND '028' ORDER BY kode_kantor")->fetchAll(PDO::FETCH_ASSOC);
        $this->json(200, 'Bootstrap KPI berhasil dimuat', [
            'year'=>$year,
            'closing_dates'=>$this->latestClosingDates($year),
            'jabatan'=>$jabatan,
            'indikator'=>$ind->fetchAll(PDO::FETCH_ASSOC),
            'parameter_skor'=>$score,
            'risk_gate'=>$risk,
            'ao_kredit'=>$ao,
            'kantor'=>$kantor,
            'can_manage'=>$this->canManage($user),
        ]);
    }

    public function evaluation(array $input, array $user): void
    {
        $year = max(2020, min(2100, (int)($input['year'] ?? date('Y'))));
        $idPeg = trim((string)($input['id_peg'] ?? ''));
        $ao = trim((string)($input['kode_ao'] ?? ''));
        $sql = "SELECT p.id,p.id_peg,p.kode_ao,p.nama_ao,LPAD(CAST(ao.kode_kantor AS CHAR),3,'0') AS kode_kantor,p.tahun,p.bulan,p.closing_date,
                       p.nilai_dasar,p.faktor_risiko,p.nilai_akhir,p.predikat,p.status,
                       COUNT(d.id) AS indikator_terisi
                FROM kpi_penilaian p LEFT JOIN kpi_penilaian_detail d ON d.penilaian_id=p.id
                LEFT JOIN ao_kredit ao ON ao.kode_group2=p.kode_ao AND ao.status=1
                WHERE p.tahun=:tahun AND p.jabatan_id=(SELECT id FROM kpi_jabatan WHERE kode='AO_KREDIT')";
        $params=[':tahun'=>$year];
        if ($idPeg !== '') { $sql .= ' AND p.id_peg=:id_peg'; $params[':id_peg']=$idPeg; }
        if ($ao !== '') { $sql .= ' AND p.kode_ao=:kode_ao'; $params[':kode_ao']=$ao; }
        if (!empty($input['kode_kantor']) && $input['kode_kantor'] !== '000') { $sql .= ' AND LPAD(CAST(ao.kode_kantor AS CHAR),3,\'0\')=:kode_kantor'; $params[':kode_kantor']=str_pad((string)$input['kode_kantor'],3,'0',STR_PAD_LEFT); }
        if (!empty($input['closing_date'])) { $sql .= ' AND p.closing_date=:closing_date'; $params[':closing_date']=$input['closing_date']; }
        $sql .= ' GROUP BY p.id ORDER BY p.bulan';
        $st=$this->pdo->prepare($sql); $st->execute($params);
        $rows=$st->fetchAll(PDO::FETCH_ASSOC);
        $this->json(200,'Penilaian KPI berhasil dimuat',['year'=>$year,'closing_dates'=>$this->latestClosingDates($year),'data'=>$rows]);
    }

    public function calculate(array $input, array $user): void
    {
        $year=max(2020,min(2100,(int)($input['year']??date('Y'))));
        $kodeAo=trim((string)($input['kode_ao']??''));
        if($kodeAo===''){$this->json(422,'AO Kredit wajib dipilih');return;}
        $jab=$this->pdo->query("SELECT id FROM kpi_jabatan WHERE kode='AO_KREDIT' LIMIT 1")->fetchColumn();
        $indSt=$this->pdo->prepare("SELECT * FROM kpi_indikator WHERE jabatan_id=:jab AND status IN ('PILOT','AKTIF') ORDER BY urutan,id");$indSt->execute([':jab'=>$jab]);$indikator=$indSt->fetchAll(PDO::FETCH_ASSOC);
        $selectedCodes = array_values(array_filter(array_map(static fn($value): string => strtoupper(trim((string)$value)), (array)($input['indicator_codes'] ?? []))));
        if ($selectedCodes) {
            $indikator = array_values(array_filter($indikator, static function (array $item) use ($selectedCodes): bool {
                return in_array(strtoupper((string)$item['kode']), $selectedCodes, true)
                    || in_array(strtoupper((string)$item['formula_key']), $selectedCodes, true);
            }));
        }
        if (!$indikator) {$this->json(422,'Indikator KPI belum aktif');return;}
        $scoreRows=$this->pdo->query("SELECT skor,min_indeks,max_indeks,predikat FROM kpi_parameter_skor WHERE aktif=1 ORDER BY skor")->fetchAll(PDO::FETCH_ASSOC);
        $risk=$this->pdo->query("SELECT kode,faktor FROM kpi_risk_gate WHERE aktif=1 ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
        $aoSt=$this->pdo->prepare("SELECT kode_group2,nama_ao,id_peg,LPAD(CAST(kode_kantor AS CHAR),3,'0') kode_kantor FROM ao_kredit WHERE kode_group2=:ao AND status=1 ORDER BY id DESC LIMIT 1");$aoSt->execute([':ao'=>$kodeAo]);$ao=$aoSt->fetch(PDO::FETCH_ASSOC);
        if(!$ao){$this->json(404,'AO Kredit tidak ditemukan');return;}
        $dates=!empty($input['closing_date'])?[(string)$input['closing_date']]:$this->latestClosingDates($year);$saved=[];
        foreach($dates as $closing){
            // Jangan mengurangi satu bulan langsung dari tanggal 29/30/31
            // karena PHP dapat overflow ke bulan berjalan. Ambil hari pertama
            // bulan closing, lalu mundur satu hari untuk memperoleh closing sebelumnya.
            $prev=date('Y-m-d',strtotime(date('Y-m-01',strtotime($closing)).' -1 day'));
            $actual=$this->actualMetrics($kodeAo,$closing,$prev);
            $targetSt=$this->pdo->prepare("SELECT indikator_id,target FROM kpi_target_bulanan WHERE jabatan_id=:jab AND tahun=:tahun AND bulan=:bulan AND (id_peg=:idpeg OR id_peg IS NULL) AND (kode_kantor=:kantor OR kode_kantor IS NULL) ORDER BY id_peg DESC,kode_kantor DESC");
            $targetSt->execute([':jab'=>$jab,':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing)),':idpeg'=>(string)($ao['id_peg']?:$ao['kode_group2']),':kantor'=>$ao['kode_kantor']]);$targets=[];foreach($targetSt as $t){$targets[(int)$t['indikator_id']] = (float)$t['target'];}
            $ready=true;$weighted=0;$details=[];$gate='NORMAL';
            foreach($indikator as $i){$key=$i['formula_key'];$target=$targets[(int)$i['id']]??0;if($target<=0&&$key==='REALISASI_KREDIT'&&!empty($input['target_realisasi']))$target=(float)$input['target_realisasi'];if($target<=0&&$key==='NOA_REALISASI'&&!empty($input['target_noa']))$target=(float)$input['target_noa'];if($target<=0&&$key==='MOB_6')$target=0.05;if($target<=0&&$key==='REPAYMENT_RATE')$target=1.0;if($target<=0&&$key==='EARLY_RUN_OFF')$target=0.01;if($target<=0&&$key==='PIPELINE')$target=1.0;$real=$actual[$key]??null;$note='';$score=0;$idx=0;$value100=0;
                if($real===null){$ready=false;$note='Sumber data indikator belum dikonfigurasi';}
                elseif($target<=0){$ready=false;$note='Target periode belum diisi';}
                else{if($key==='MOB_6'){$idx=(float)$real;$score=$real<=0.05?5:($real<=0.06?4:($real<=0.07?3:($real<=0.08?2:1)));$note='OS menunggak MOB 1–6 / total OS MOB 1–6';}elseif($key==='EARLY_RUN_OFF'){$idx=(float)$real;$score=$real<=0.01?5:($real<=0.0125?4:($real<=0.015?3:($real<=0.02?2:1)));$note='OS pelunasan murni / OS DPD 0 closing sebelumnya; refinancing/top-up dikecualikan';}elseif($key==='PIPELINE'){$idx=(float)$real;$score=min(5,(int)$real+1);$note='NOA pipeline yang cair/realisasi pada periode berjalan';}else{$idx=strtoupper($i['arah'])==='LOWER'?($real==0?1.5:min($target/$real,1.5)):min($real/$target,1.5);foreach($scoreRows as $s){if($idx>=(float)$s['min_indeks']&&$idx<(float)$s['max_indeks']||((int)$s['skor']===5&&$idx>=(float)$s['min_indeks'])){$score=(int)$s['skor'];break;}}}$weighted=(float)$i['bobot']*$score;$value100=min((float)$i['bobot']*100,$weighted/5*100);}
                $details[]=['indikator'=>$i,'target'=>$target,'realisasi'=>$real,'indeks'=>$idx,'skor'=>$score,'nilai_tertimbang'=>$weighted,'nilai_100'=>$value100,'os_mob_menunggak'=>$key==='MOB_6'?(float)($actual['OS_MOB_MENUNGGAK']??0):0,'os_mob_total'=>$key==='MOB_6'?(float)($actual['OS_MOB_TOTAL']??0):0,'os_dpd0'=>$key==='REPAYMENT_RATE'?(float)($actual['OS_DPD0']??0):0,'os_kelolaan'=>$key==='REPAYMENT_RATE'?(float)($actual['OS_KELOLAAN']??0):0,'os_run_off'=>$key==='EARLY_RUN_OFF'?(float)($actual['OS_RUN_OFF']??0):0,'os_dpd0_m1'=>$key==='EARLY_RUN_OFF'?(float)($actual['OS_DPD0_M1']??0):0,'catatan'=>$note];
            }
            $base=$ready?array_sum(array_column($details,'nilai_100')):0;$factor=(float)($risk[$gate]??1);$final=$base*$factor;$partial=(bool)$selectedCodes;$status=$ready&&!$partial?'DISETUJUI':'DRAFT';
            $this->pdo->beginTransaction();try{
                $up=$this->pdo->prepare("INSERT INTO kpi_penilaian(jabatan_id,id_peg,kode_ao,nama_ao,tahun,bulan,closing_date,nilai_dasar,risk_gate,faktor_risiko,nilai_akhir,predikat,status,generated_at) VALUES(:jab,:idpeg,:ao,:nama,:tahun,:bulan,:closing,:base,:gate,:factor,:final,:predikat,:status,NOW()) ON DUPLICATE KEY UPDATE nilai_dasar=VALUES(nilai_dasar),risk_gate=VALUES(risk_gate),faktor_risiko=VALUES(faktor_risiko),nilai_akhir=VALUES(nilai_akhir),predikat=VALUES(predikat),status=VALUES(status),generated_at=NOW()");
                $targetId=(string)($ao['id_peg']?:$ao['kode_group2']);$up->execute([':jab'=>$jab,':idpeg'=>$targetId,':ao'=>$kodeAo,':nama'=>$ao['nama_ao'],':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing)),':closing'=>$closing,':base'=>$base,':gate'=>$gate,':factor'=>$factor,':final'=>$final,':predikat'=>$partial?'Fokus 2 indikator':($ready?($final>=90?'Istimewa':($final>=80?'Melampaui target':($final>=60?'Memenuhi target':'Perlu perbaikan'))):'Belum lengkap'),':status'=>$status]);
                $idSt=$this->pdo->prepare("SELECT id FROM kpi_penilaian WHERE jabatan_id=:jab AND id_peg=:idpeg AND tahun=:tahun AND bulan=:bulan");$idSt->execute([':jab'=>$jab,':idpeg'=>$targetId,':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing))]);$pid=(int)$idSt->fetchColumn();
                $this->pdo->prepare('DELETE FROM kpi_penilaian_detail WHERE penilaian_id=:pid')->execute([':pid'=>$pid]);$det=$this->pdo->prepare("INSERT INTO kpi_penilaian_detail(penilaian_id,indikator_id,target,realisasi,indeks,skor,nilai_tertimbang,nilai_100,os_mob_menunggak,os_mob_total,os_dpd0,os_kelolaan,os_run_off,os_dpd0_m1,sumber_snapshot,catatan) VALUES(:pid,:iid,:target,:real,:idx,:score,:weighted,:value100,:os_bad,:os_total,:os_dpd0,:os_kelolaan,:os_run_off,:os_dpd0_m1,:source,:note)");foreach($details as $d){$det->execute([':pid'=>$pid,':iid'=>$d['indikator']['id'],':target'=>$d['target'],':real'=>(float)($d['realisasi']??0),':idx'=>(float)$d['indeks'],':score'=>$d['skor'],':weighted'=>$d['nilai_tertimbang'],':value100'=>$d['nilai_100'],':os_bad'=>$d['os_mob_menunggak'],':os_total'=>$d['os_mob_total'],':os_dpd0'=>$d['os_dpd0'],':os_kelolaan'=>$d['os_kelolaan'],':os_run_off'=>$d['os_run_off'],':os_dpd0_m1'=>$d['os_dpd0_m1'],':source'=>$d['indikator']['sumber_data'],':note'=>$d['catatan']]);}$this->pdo->commit();$saved[]=['closing_date'=>$closing,'status'=>$status,'nilai_dasar'=>$base,'nilai_akhir'=>$final];
            }catch(Throwable $e){$this->pdo->rollBack();throw $e;}
        }
        $this->json(200,'KPI AO berhasil dihitung',['year'=>$year,'kode_ao'=>$kodeAo,'data'=>$saved]);
    }

    private function actualMetrics(string $kodeAo,string $closing,string $prev): array
    {
        $st=$this->pdo->prepare("SELECT COALESCE(SUM(t.realisasi_pokok),0) total_realisasi,COUNT(DISTINCT t.no_rekening) total_noa,COUNT(DISTINCT t.no_rekening) pipeline_noa,COUNT(DISTINCT CASE WHEN UPPER(TRIM(COALESCE(t.baru_lama,''))) IN ('BARU','NASABAH BARU','NEW','NEW CIF') THEN t.no_rekening END) noa_baru FROM update_realisasi_kredit t WHERE t.tanggal_realisasi>:prev AND t.tanggal_realisasi<=:closing AND t.kode_trans=110 AND t.kode_group2=:ao");$st->execute([':prev'=>$prev,':closing'=>$closing,':ao'=>$kodeAo]);$r=$st->fetch(PDO::FETCH_ASSOC)?:[];
        // Samakan dengan mob.php: MOB 1-6 berarti enam bulan penuh sebelum
        // bulan snapshot. Contoh closing Agustus: realisasi Februari-Juli.
        $mobEnd=date('Y-m-d',strtotime(date('Y-m-01',strtotime($closing)).' -1 day'));
        $mobStart=date('Y-m-01',strtotime(date('Y-m-01',strtotime($mobEnd)).' -5 month'));
        $n=$this->pdo->prepare("SELECT COALESCE(SUM(baki_debet),0) total_os,COALESCE(SUM(CASE WHEN COALESCE(hari_menunggak,0)=0 THEN baki_debet ELSE 0 END),0) lancar_os,COALESCE(SUM(baki_debet),0) mob_os,COALESCE(SUM(CASE WHEN COALESCE(hari_menunggak,0)>0 THEN baki_debet ELSE 0 END),0) mob_bad_os,COALESCE(SUM(CASE WHEN kolektibilitas IN ('KL','D','M') THEN baki_debet ELSE 0 END),0) vintage_npl_os FROM nominatif WHERE created=:closing AND kode_group2=:ao AND tgl_realisasi BETWEEN :mob_start AND :mob_end");
        $n->execute([':closing'=>$closing,':ao'=>$kodeAo,':mob_start'=>$mobStart,':mob_end'=>$mobEnd]);
        $x=$n->fetch(PDO::FETCH_ASSOC)?:[];$mob=(float)($x['mob_os']??0);
        $current=$this->pdo->prepare("SELECT COALESCE(SUM(baki_debet),0) total_os,COALESCE(SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('L','1') AND COALESCE(hari_menunggak,0)=0 THEN baki_debet ELSE 0 END),0) os_dpd0 FROM nominatif WHERE created=:current_closing AND kode_group2=:current_ao");
        $current->execute([':current_closing'=>$closing,':current_ao'=>$kodeAo]);$currentRow=$current->fetch(PDO::FETCH_ASSOC)?:[];$totalCurrent=(float)($currentRow['total_os']??0);
        $runOff=$this->pdo->prepare("SELECT COALESCE(SUM(CASE WHEN n.no_rekening IS NULL OR n.baki_debet<=0 THEN c.baki_debet ELSE 0 END),0) os_run_off,COALESCE(SUM(c.baki_debet),0) os_dpd0_m1 FROM nominatif c LEFT JOIN nominatif n ON n.no_rekening=c.no_rekening AND n.created=:run_closing WHERE c.created=:run_prev AND c.kode_group2=:run_ao AND c.kolektibilitas IN ('L','1') AND COALESCE(c.hari_menunggak,0)=0 AND c.baki_debet>0 AND NOT EXISTS (SELECT 1 FROM nominatif nx WHERE nx.created=:run_closing2 AND nx.nasabah_id=c.nasabah_id AND nx.no_rekening<>c.no_rekening AND nx.tgl_realisasi>:run_prev2 AND nx.tgl_realisasi<=:run_closing3)");
        $runOff->execute([':run_closing'=>$closing,':run_prev'=>$prev,':run_ao'=>$kodeAo,':run_closing2'=>$closing,':run_prev2'=>$prev,':run_closing3'=>$closing]);$runRow=$runOff->fetch(PDO::FETCH_ASSOC)?:[];$osDpd0M1=(float)($runRow['os_dpd0_m1']??0);$osRunOff=(float)($runRow['os_run_off']??0);
        return ['REALISASI_KREDIT'=>(float)($r['total_realisasi']??0),'NOA_REALISASI'=>(float)($r['noa_baru']??0),'NOA_TOTAL'=>(float)($r['total_noa']??0),'PIPELINE'=>(float)($r['pipeline_noa']??0),'REPAYMENT_RATE'=>$totalCurrent>0?(float)$currentRow['os_dpd0']/$totalCurrent:0,'OS_DPD0'=>(float)($currentRow['os_dpd0']??0),'OS_KELOLAAN'=>$totalCurrent,'MOB_6'=>$mob>0?(float)$x['mob_bad_os']/$mob:0,'OS_MOB_MENUNGGAK'=>(float)($x['mob_bad_os']??0),'OS_MOB_TOTAL'=>$mob,'EARLY_RUN_OFF'=>$osDpd0M1>0?$osRunOff/$osDpd0M1:0,'OS_RUN_OFF'=>$osRunOff,'OS_DPD0_M1'=>$osDpd0M1,'NPL_VINTAGE'=>$mob>0?(float)$x['vintage_npl_os']/$mob:0];
    }

    public function setting(array $input, array $user): void
    {
        $jabatan = $this->pdo->query("SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        $st=$this->pdo->query("SELECT i.*,j.kode AS jabatan_kode,j.nama AS jabatan_nama
                               FROM kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
                               ORDER BY j.nama,i.urutan,i.id");
        $this->json(200,'Setting KPI berhasil dimuat',['jabatan'=>$jabatan,'indikator'=>$st->fetchAll(PDO::FETCH_ASSOC),'parameter_skor'=>$this->pdo->query("SELECT * FROM kpi_parameter_skor ORDER BY skor")->fetchAll(PDO::FETCH_ASSOC),'risk_gate'=>$this->pdo->query("SELECT * FROM kpi_risk_gate ORDER BY id")->fetchAll(PDO::FETCH_ASSOC),'can_manage'=>$this->canManage($user)]);
    }

    public function detail(array $input, array $user): void
    {
        $id=(int)($input['penilaian_id']??0); if(!$id){$this->json(422,'ID penilaian wajib diisi');return;}
        $st=$this->pdo->prepare("SELECT d.*,i.kelompok,i.nama,i.bobot,i.arah,i.unit,i.formula_key,i.sumber_data FROM kpi_penilaian_detail d JOIN kpi_indikator i ON i.id=d.indikator_id WHERE d.penilaian_id=:id ORDER BY i.urutan,i.id");$st->execute([':id'=>$id]);
        $this->json(200,'Detail KPI berhasil dimuat',['data'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function quarterly(array $input, array $user): void
    {
        $year=max(2020,min(2100,(int)($input['year']??date('Y'))));
        $quarter=max(1,min(4,(int)($input['quarter']??1)));$start=(($quarter-1)*3)+1;$end=$start+2;
        $ao=trim((string)($input['kode_ao']??''));
        $kantor=trim((string)($input['kode_kantor']??''));
        $sql="SELECT i.kode,i.nama,i.kelompok,i.unit,i.bobot,i.formula_key,
                     CASE WHEN i.unit IN ('RUPIAH','NOA') OR i.formula_key='PIPELINE' THEN SUM(d.target) ELSE AVG(d.target) END target,
                     CASE WHEN i.unit IN ('RUPIAH','NOA') OR i.formula_key='PIPELINE' THEN SUM(d.realisasi) ELSE AVG(d.realisasi) END realisasi,
                     AVG(d.indeks) indeks,ROUND(AVG(d.skor),2) skor,AVG(d.nilai_100) nilai_100,COUNT(d.id) bulan_terisi,
                     GROUP_CONCAT(DISTINCT p.closing_date ORDER BY p.bulan SEPARATOR ', ') closing_dates
              FROM kpi_penilaian_detail d JOIN kpi_penilaian p ON p.id=d.penilaian_id
              JOIN ao_kredit ao ON ao.kode_group2=p.kode_ao AND ao.status=1
              JOIN kpi_indikator i ON i.id=d.indikator_id
              WHERE p.jabatan_id=(SELECT id FROM kpi_jabatan WHERE kode='AO_KREDIT') AND p.tahun=:tahun AND p.bulan BETWEEN :start AND :end";
        $params=[':tahun'=>$year,':start'=>$start,':end'=>$end];if($ao!==''){$sql.=' AND p.kode_ao=:ao';$params[':ao']=$ao;}if($kantor!==''){$sql.=" AND LPAD(CAST(ao.kode_kantor AS CHAR),3,'0')=:kantor";$params[':kantor']=str_pad($kantor,3,'0',STR_PAD_LEFT);}$sql.=' GROUP BY i.id ORDER BY i.urutan,i.id';$st=$this->pdo->prepare($sql);$st->execute($params);
        $head=$this->pdo->prepare("SELECT p.kode_ao,p.nama_ao,ROUND(AVG(p.nilai_akhir),2) nilai_akhir,MIN(p.status) status,COUNT(*) bulan_terisi FROM kpi_penilaian p JOIN ao_kredit ao ON ao.kode_group2=p.kode_ao AND ao.status=1 WHERE p.jabatan_id=(SELECT id FROM kpi_jabatan WHERE kode='AO_KREDIT') AND p.tahun=:tahun AND p.bulan BETWEEN :start AND :end".($ao!==''?' AND p.kode_ao=:ao':'').($kantor!==''?" AND LPAD(CAST(ao.kode_kantor AS CHAR),3,'0')=:kantor":'').' GROUP BY p.kode_ao,p.nama_ao ORDER BY p.nama_ao');$head->execute($params);$aoRows=$head->fetchAll(PDO::FETCH_ASSOC);
        $rows=$st->fetchAll(PDO::FETCH_ASSOC);
        // Total periode adalah jumlah nilai berbobot seluruh indikator yang
        // tampil. Ini juga memperbaiki data lama yang header nilai_akhir-nya
        // masih kosong atau belum tersinkron.
        $totalNilai=$rows?round(array_sum(array_map(static fn(array $row): float=>(float)$row['nilai_100'],$rows)),2):null;
        $this->json(200,'Rekap KPI triwulan berhasil dimuat',['year'=>$year,'quarter'=>$quarter,'bulan'=>[$start,$end],'nilai_akhir'=>$totalNilai,'ao'=>$aoRows,'data'=>$rows]);
    }

    public function saveIndicator(array $input, array $user): void
    {
        if (!$this->canManage($user)) { $this->json(403,'Tidak memiliki hak mengubah setting KPI'); return; }
        $id=(int)($input['id']??0); $fields=['bobot','arah','unit','frekuensi','status','definisi','sumber_data','validator']; $set=[];$params=[':id'=>$id];
        foreach($fields as $field){ if(array_key_exists($field,$input)){ $set[]="{$field}=:{$field}"; $params[":{$field}"]=$input[$field]; }}
        if(!$id||!$set){$this->json(422,'Data indikator tidak lengkap');return;}
        $st=$this->pdo->prepare('UPDATE kpi_indikator SET '.implode(',',$set).' WHERE id=:id');$st->execute($params);
        $this->json(200,'Parameter indikator disimpan');
    }

    public function saveScoreParameter(array $input, array $user): void
    {
        if (!$this->canManage($user)) { $this->json(403,'Tidak memiliki hak mengubah parameter skor'); return; }
        $id=(int)($input['id']??0); if(!$id){$this->json(422,'ID parameter wajib diisi');return;}
        $st=$this->pdo->prepare('UPDATE kpi_parameter_skor SET min_indeks=:min,max_indeks=:max,predikat=:predikat,aktif=:aktif WHERE id=:id');
        $st->execute([':min'=>(float)$input['min_indeks'],':max'=>(float)$input['max_indeks'],':predikat'=>(string)$input['predikat'],':aktif'=>(int)($input['aktif']??1),':id'=>$id]);
        $this->json(200,'Parameter skor disimpan');
    }

    public function saveRiskGate(array $input, array $user): void
    {
        if (!$this->canManage($user)) { $this->json(403,'Tidak memiliki hak mengubah risk gate'); return; }
        $id=(int)($input['id']??0); if(!$id){$this->json(422,'ID risk gate wajib diisi');return;}
        $st=$this->pdo->prepare('UPDATE kpi_risk_gate SET faktor=:faktor,perlakuan=:perlakuan,aktif=:aktif WHERE id=:id');
        $st->execute([':faktor'=>(float)$input['faktor'],':perlakuan'=>(string)$input['perlakuan'],':aktif'=>(int)($input['aktif']??1),':id'=>$id]);
        $this->json(200,'Risk gate disimpan');
    }
}
