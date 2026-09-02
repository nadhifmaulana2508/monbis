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

    private function requestedJabatan(array $input): array
    {
        $kode = strtoupper(trim((string)($input['jabatan_kode'] ?? $input['jabatan'] ?? 'AO_KREDIT')));
        $st = $this->pdo->prepare('SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan WHERE kode=:kode AND aktif=1 LIMIT 1');
        $st->execute([':kode'=>$kode]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $st = $this->pdo->prepare("SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan WHERE kode='AO_KREDIT' AND aktif=1 LIMIT 1");
            $st->execute();
            $row = $st->fetch(PDO::FETCH_ASSOC);
        }
        return $row ?: ['id'=>0,'kode'=>'AO_KREDIT','nama'=>'AO Kredit','deskripsi'=>'','aktif'=>1];
    }

    /** Daftar AO dari master masing-masing jenis kelolaan. */
    private function aoDirectory(string $jabatanKode, ?string $kodeKantor = null, bool $requireOffice = false): array
    {
        $jabatanKode = strtoupper($jabatanKode);
        if ($requireOffice && trim((string)$kodeKantor) === '') return [];
        $office = preg_replace('/\D+/', '', (string)$kodeKantor);
        $officeWhere = $office !== '' ? " AND LPAD(CAST(kode_kantor AS CHAR),3,'0')='".str_pad($office,3,'0',STR_PAD_LEFT)."'" : '';
        if ($jabatanKode === 'AO_REMEDIAL') {
            return $this->pdo->query("SELECT id_peg AS kode_ao,nama AS nama_ao,id_peg,
                                             LPAD(CAST(kode_kantor AS CHAR),3,'0') AS kode_kantor,
                                             UPPER(COALESCE(NULLIF(remedial,''),'FE')) AS spesialisasi
                                      FROM ao_remedial
                                      WHERE (status IS NULL OR TRIM(status)='' OR UPPER(status) IN ('1','AKTIF','ACTIVE')){$officeWhere}
                                      ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($jabatanKode === 'AO_DANA') {
            // AO Dana memiliki master pegawai sendiri. Kode grup tabungan dan
            // deposito disimpan sebagai atribut pendukung, bukan sebagai ID AO.
            return $this->pdo->query("SELECT id_peg AS kode_ao,
                                             nama AS nama_ao,
                                             id_peg,
                                             kode_group2_tab,
                                             kode_group2_dep,
                                             LPAD(CAST(kode_kantor AS CHAR),3,'0') AS kode_kantor
                                      FROM ao_dana
                                      WHERE status=1
                                        AND LPAD(CAST(kode_kantor AS CHAR),3,'0') BETWEEN '001' AND '028'{$officeWhere}
                                      ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->pdo->query("SELECT kode_group2 AS kode_ao,MAX(nama_ao) AS nama_ao,MAX(id_peg) AS id_peg,
                                         LPAD(CAST(MAX(kode_kantor) AS CHAR),3,'0') AS kode_kantor
                                  FROM ao_kredit WHERE status=1{$officeWhere} GROUP BY kode_group2 ORDER BY nama_ao")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function allAoDirectories(): array
    {
        return [
            'AO_KREDIT'=>$this->aoDirectory('AO_KREDIT'),
            'AO_DANA'=>$this->aoDirectory('AO_DANA'),
            'AO_REMEDIAL'=>$this->aoDirectory('AO_REMEDIAL'),
        ];
    }

    private function findAo(string $jabatanKode, string $kodeAo): ?array
    {
        foreach ($this->aoDirectory($jabatanKode) as $row) {
            if ((string)($row['kode_ao'] ?? '') === $kodeAo) return $row;
        }
        return null;
    }

    /** Periode yang sudah pernah dibuat; dipakai FE untuk menghindari generate ulang. */
    private function generatedPeriods(int $jabatanId, int $year): array
    {
        $st = $this->pdo->prepare("SELECT id_peg,kode_ao,kode_kantor,closing_date,nilai_akhir,status
                                   FROM kpi_penilaian
                                   WHERE jabatan_id=:jabatan_id AND tahun=:tahun
                                   ORDER BY closing_date,id_peg");
        $st->execute([':jabatan_id'=>$jabatanId, ':tahun'=>$year]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Data ringan untuk filter halaman KPI; tidak memuat indikator dan range skor. */
    public function directory(array $input, array $user): void
    {
        $year=max(2020,min(2100,(int)($input['year']??date('Y'))));
        $selectedJabatan=$this->requestedJabatan($input);
        $jabatan=$this->pdo->query("SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan WHERE aktif=1 ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        $kantor=$this->pdo->query("SELECT kode_kantor,nama_kantor FROM kode_kantor WHERE LPAD(CAST(kode_kantor AS CHAR),3,'0') BETWEEN '001' AND '028' ORDER BY kode_kantor")->fetchAll(PDO::FETCH_ASSOC);
        $tukin=$this->pdo->query("SELECT min_skor,max_skor,min_nilai,max_nilai,faktor_persen,label,urutan FROM kpi_parameter_tukin WHERE aktif=1 ORDER BY urutan,id")->fetchAll(PDO::FETCH_ASSOC);
        $includeAll=!empty($input['include_all_ao']);
        $requiresOffice=$selectedJabatan['kode']!=='AO_KREDIT'&&!$includeAll;
        $ao=$this->aoDirectory((string)$selectedJabatan['kode'],(string)($input['kode_kantor']??''),$requiresOffice);
        $generated=($input['include_generated']??true)===false?[]:$this->generatedPeriods((int)$selectedJabatan['id'],$year);
        $this->json(200,'Direktori KPI berhasil dimuat',[
            'year'=>$year,'closing_dates'=>$this->latestClosingDates($year),'jabatan'=>$jabatan,
            'jabatan_terpilih'=>$selectedJabatan,'ao'=>$ao,'kantor'=>$kantor,'generated'=>$generated,'tukin_rules'=>$tukin
        ]);
    }

    /** Daftar AO saja ketika kantor berubah, agar tidak mengulang bootstrap penuh. */
    public function aoList(array $input, array $user): void
    {
        $selectedJabatan=$this->requestedJabatan($input);
        $office=(string)($input['kode_kantor']??'');
        $ao=$this->aoDirectory((string)$selectedJabatan['kode'],$office,$selectedJabatan['kode']!=='AO_KREDIT');
        $this->json(200,'Daftar AO berhasil dimuat',['jabatan_terpilih'=>$selectedJabatan,'ao'=>$ao]);
    }

    public function bootstrap(array $input, array $user): void
    {
        $year = max(2020, min(2100, (int)($input['year'] ?? date('Y'))));
        $selectedJabatan = $this->requestedJabatan($input);
        $jabatan = $this->pdo->query("SELECT id,kode,nama,deskripsi,aktif FROM kpi_jabatan WHERE aktif=1 ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        $ind = $this->pdo->prepare("SELECT i.*,j.kode AS jabatan_kode,j.nama AS jabatan_nama
                                    FROM kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
                                    WHERE i.jabatan_id=:jabatan_id AND i.status IN ('PILOT','AKTIF') ORDER BY i.urutan,i.id");
        $ind->execute([':jabatan_id'=>(int)$selectedJabatan['id']]);
        $scoreSt = $this->pdo->prepare("SELECT s.id,s.jabatan_id,s.indikator_id,s.skor,s.min_indeks,s.max_indeks,s.predikat,s.aktif,j.kode AS jabatan_kode FROM kpi_parameter_skor_jabatan s JOIN kpi_jabatan j ON j.id=s.jabatan_id WHERE s.jabatan_id=:jabatan_id AND s.aktif=1 ORDER BY s.indikator_id,s.skor");
        $scoreSt->execute([':jabatan_id'=>(int)$selectedJabatan['id']]);
        $score = $scoreSt->fetchAll(PDO::FETCH_ASSOC);
        $risk = $this->pdo->query("SELECT kode,nama,faktor,perlakuan FROM kpi_risk_gate WHERE aktif=1 ORDER BY faktor DESC")->fetchAll(PDO::FETCH_ASSOC);
        $tukin = $this->pdo->query("SELECT min_skor,max_skor,min_nilai,max_nilai,faktor_persen,label,urutan FROM kpi_parameter_tukin WHERE aktif=1 ORDER BY urutan,id")->fetchAll(PDO::FETCH_ASSOC);
        $kantor = $this->pdo->query("SELECT kode_kantor,nama_kantor FROM kode_kantor WHERE LPAD(CAST(kode_kantor AS CHAR),3,'0') BETWEEN '001' AND '028' ORDER BY kode_kantor")->fetchAll(PDO::FETCH_ASSOC);
        // Kirim hanya master AO dari jabatan yang sedang dipilih. Selain lebih
        // ringan, ini mencegah daftar AO dari jabatan lain ikut terbaca di FE.
        // AO Kredit tetap dikirim tanpa filter untuk kompatibilitas halaman
        // rekap lama. Halaman hitung tetap mengunci pilihan AO sampai kantor
        // dipilih; Dana/Remedial langsung kosong sebelum ada kantor.
        $requiresOffice = $selectedJabatan['kode'] !== 'AO_KREDIT' && empty($input['include_all_ao']);
        $ao = $this->aoDirectory((string)$selectedJabatan['kode'], (string)($input['kode_kantor'] ?? ''), $requiresOffice);
        $this->json(200, 'Bootstrap KPI berhasil dimuat', [
            'year'=>$year,
            'closing_dates'=>$this->latestClosingDates($year),
            'jabatan'=>$jabatan,
            'indikator'=>$ind->fetchAll(PDO::FETCH_ASSOC),
            'parameter_skor'=>$score,
            'risk_gate'=>$risk,
            'tukin_rules'=>$tukin,
            'jabatan_terpilih'=>$selectedJabatan,
            'generated'=>$this->generatedPeriods((int)$selectedJabatan['id'], $year),
            'ao'=>$ao,
            'ao_kredit'=>$selectedJabatan['kode']==='AO_KREDIT' ? $ao : [],
            'ao_dana'=>$selectedJabatan['kode']==='AO_DANA' ? $ao : [],
            'ao_remedial'=>$selectedJabatan['kode']==='AO_REMEDIAL' ? $ao : [],
            'kantor'=>$kantor,
            'can_manage'=>$this->canManage($user),
        ]);
    }

    public function evaluation(array $input, array $user): void
    {
        $year = max(2020, min(2100, (int)($input['year'] ?? date('Y'))));
        $selectedJabatan = $this->requestedJabatan($input);
        $idPeg = trim((string)($input['id_peg'] ?? ''));
        $ao = trim((string)($input['kode_ao'] ?? ''));
        $sql = "SELECT p.id,p.id_peg,p.kode_ao,p.nama_ao,COALESCE(p.kode_kantor,LPAD(CAST(ao.kode_kantor AS CHAR),3,'0')) AS kode_kantor,p.tahun,p.bulan,p.closing_date,
                       p.nilai_dasar,p.faktor_risiko,p.nilai_akhir,p.predikat,p.status,
                       COUNT(d.id) AS indikator_terisi
                FROM kpi_penilaian p LEFT JOIN kpi_penilaian_detail d ON d.penilaian_id=p.id
                LEFT JOIN ao_kredit ao ON ao.kode_group2=p.kode_ao AND ao.status=1
                 WHERE p.tahun=:tahun AND p.jabatan_id=:jabatan_id";
        $params=[':tahun'=>$year,':jabatan_id'=>(int)$selectedJabatan['id']];
        if ($idPeg !== '') { $sql .= ' AND p.id_peg=:id_peg'; $params[':id_peg']=$idPeg; }
        if ($ao !== '') { $sql .= ' AND p.kode_ao=:kode_ao'; $params[':kode_ao']=$ao; }
        if (!empty($input['kode_kantor']) && $input['kode_kantor'] !== '000' && $selectedJabatan['kode']==='AO_KREDIT') { $sql .= ' AND LPAD(CAST(ao.kode_kantor AS CHAR),3,\'0\')=:kode_kantor'; $params[':kode_kantor']=str_pad((string)$input['kode_kantor'],3,'0',STR_PAD_LEFT); }
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
        if($kodeAo===''){$this->json(422,'AO wajib dipilih');return;}
        $selectedJabatan=$this->requestedJabatan($input);$jab=(int)$selectedJabatan['id'];$jabatanKode=(string)$selectedJabatan['kode'];
        // Hanya indikator yang benar-benar aktif yang masuk perhitungan.
        // Indikator NONAKTIF/PILOT tetap disimpan untuk histori dan konfigurasi.
        $indSt=$this->pdo->prepare("SELECT * FROM kpi_indikator WHERE jabatan_id=:jab AND status='AKTIF' ORDER BY urutan,id");$indSt->execute([':jab'=>$jab]);$indikator=$indSt->fetchAll(PDO::FETCH_ASSOC);
        $selectedCodes = array_values(array_filter(array_map(static fn($value): string => strtoupper(trim((string)$value)), (array)($input['indicator_codes'] ?? []))));
        if ($selectedCodes) {
            $indikator = array_values(array_filter($indikator, static function (array $item) use ($selectedCodes): bool {
                return in_array(strtoupper((string)$item['kode']), $selectedCodes, true)
                    || in_array(strtoupper((string)$item['formula_key']), $selectedCodes, true);
            }));
        }
        if (!$indikator) {$this->json(422,'Indikator KPI belum aktif');return;}
        $scoreRows=$this->pdo->prepare("SELECT indikator_id,skor,min_indeks,max_indeks,predikat FROM kpi_parameter_skor_jabatan WHERE jabatan_id=:jab AND aktif=1 ORDER BY indikator_id,skor");$scoreRows->execute([':jab'=>$jab]);$scoreRows=$scoreRows->fetchAll(PDO::FETCH_ASSOC);$scoreByIndicator=[];foreach($scoreRows as $scoreRow){$scoreByIndicator[(int)($scoreRow['indikator_id']??0)][]=$scoreRow;}
        $scoreFor=static function(int $indikatorId,float $value,array $ranges,bool $isCount=false,bool $isLower=false): int {
            $items=array_values($ranges[$indikatorId]??[]);
            if(!$items)return 0;
            $matches=static function(float $current,float $min,float $max,int $score): bool {
                if($max>=999)return $current>=$min;
                return $current>=$min&&($current<$max||($score===5&&abs($current-$max)<0.0000001));
            };
            if($isCount){
                foreach($items as $range){
                    $min=(float)$range['min_indeks'];$max=(float)$range['max_indeks'];$score=(int)$range['skor'];
                    if(abs($max-$min)<0.0000001&&abs($value-$min)<0.0000001)return $score;
                    if($value>=$min&&($max>=999||$value<=$max))return $score;
                }
                return 0;
            }
            if($isLower){
                $byScore=[];foreach($items as $range)$byScore[(int)$range['skor']]=$range;
                $scoreKeys=array_keys($byScore);sort($scoreKeys,SORT_NUMERIC);
                $lowestScore=(int)reset($scoreKeys);$highestScore=(int)end($scoreKeys);
                $lowestMin=(float)$byScore[$lowestScore]['min_indeks'];$highestMin=(float)$byScore[$highestScore]['min_indeks'];
                // Sebagian range LOWER disimpan dari nilai terbaik ke terburuk
                // (skor 5 memiliki batas bawah paling kecil). Sebagian data lama
                // tersimpan seperti range HIGHER, sehingga skor perlu dibalik.
                $bestToWorst=$highestMin<=$lowestMin;
                if($bestToWorst){
                    usort($items,static fn(array $a,array $b): int => (int)$b['skor']<=>(int)$a['skor']);
                    foreach($items as $range){
                        $min=(float)$range['min_indeks'];$max=(float)$range['max_indeks'];$score=(int)$range['skor'];
                        if($max>=999){if($value>=$min)return $score;continue;}
                        // Nilai di bawah batas minimum skor terbaik tetap masuk
                        // skor terbaik (contoh Early Run Off 0,74% <= 1%).
                        if($value<=$max)return $score;
                    }
                    return $lowestScore;
                }
                foreach($items as $range){
                    $min=(float)$range['min_indeks'];$max=(float)$range['max_indeks'];$score=(int)$range['skor'];
                    if($matches($value,$min,$max,$score))return max($lowestScore,min($highestScore,$lowestScore+$highestScore-$score));
                }
                // Nilai yang lebih kecil dari range pertama adalah kondisi
                // terbaik untuk indikator LOWER.
                $firstMin=min(array_map(static fn(array $row): float => (float)$row['min_indeks'],$items));
                return $value<$firstMin?$highestScore:$lowestScore;
            }
            foreach($items as $range){
                $min=(float)$range['min_indeks'];$max=(float)$range['max_indeks'];$score=(int)$range['skor'];
                if($matches($value,$min,$max,$score))return $score;
            }
            return 0;
        };
        $risk=$this->pdo->query("SELECT kode,faktor FROM kpi_risk_gate WHERE aktif=1 ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR);
        $ao=$this->findAo($jabatanKode,$kodeAo);
        if(!$ao){$this->json(404,'AO tidak ditemukan pada jabatan yang dipilih');return;}
        // Sumber data real AO Dana dan AO Remedial belum tersedia. Izinkan
        // generate lebih dulu memakai data dummy agar alur skor, bobot,
        // penyimpanan, dan tampilan dapat diuji. Catatan dummy ikut disimpan
        // pada detail supaya tidak disalahartikan sebagai nilai real.
        $dummyMode=$jabatanKode!=='AO_KREDIT';
        $dummyTargets=[
            'TABUNGAN_AO'=>35000000,
            'DEPOSITO_AO'=>15000000,
            'NOA_BARU_DANA'=>3,
            'NOA_DANA_REALISASI'=>3,
            'PIPELINE_AO_DANA'=>1,
            'PIPELINE_DANA'=>1,
            'BACKFLOW'=>0.10,
            'AMOUNT_COLLECTION'=>100000000,
            'PTP_DIPENUHI'=>1,
            'PENURUNAN_NPL'=>0.08,
            'PENYELESAIAN_KREDIT'=>0.75,
            'DOKUMENTASI_PENAGIHAN'=>1,
            'RECOVERY'=>0.75,
            'PIPELINE_DANA'=>20,
        ];
        $dates=!empty($input['closing_date'])?[(string)$input['closing_date']]:$this->latestClosingDates($year);$saved=[];$targetId=(string)($ao['id_peg']?:$ao['kode_group2']);
        foreach($dates as $closing){
            if (!empty($input['skip_existing'])) {
                $existingSt=$this->pdo->prepare('SELECT nilai_akhir,status FROM kpi_penilaian WHERE jabatan_id=:jab AND id_peg=:idpeg AND tahun=:tahun AND bulan=:bulan LIMIT 1');
                $existingSt->execute([':jab'=>$jab,':idpeg'=>$targetId,':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing))]);
                $existing=$existingSt->fetch(PDO::FETCH_ASSOC);
                if ($existing) { $saved[]=['closing_date'=>$closing,'status'=>'SUDAH ADA','nilai_dasar'=>null,'nilai_akhir'=>(float)$existing['nilai_akhir']]; continue; }
            }
            // Jangan mengurangi satu bulan langsung dari tanggal 29/30/31
            // karena PHP dapat overflow ke bulan berjalan. Ambil hari pertama
            // bulan closing, lalu mundur satu hari untuk memperoleh closing sebelumnya.
            $prev=date('Y-m-d',strtotime(date('Y-m-01',strtotime($closing)).' -1 day'));
            $actual=$dummyMode?[]:$this->actualMetrics($kodeAo,$closing,$prev);
            // Prioritas target: target periode/AO/kantor, lalu target default global
            // (tahun=0, bulan=0). Target default dikelola dari halaman setting.
            $targetSt=$this->pdo->prepare("SELECT indikator_id,target FROM kpi_target_bulanan
                WHERE jabatan_id=:jab AND ( (tahun=:tahun AND bulan=:bulan) OR (tahun=0 AND bulan=0) )
                  AND (id_peg=:idpeg OR id_peg IS NULL)
                  AND (kode_kantor=:kantor OR kode_kantor IS NULL)
                ORDER BY (tahun=0) DESC, (id_peg IS NULL) DESC, (kode_kantor IS NULL) DESC, id ASC");
            $targetSt->execute([':jab'=>$jab,':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing)),':idpeg'=>(string)($ao['id_peg']?:$ao['kode_group2']),':kantor'=>$ao['kode_kantor']]);$targets=[];foreach($targetSt as $t){$targets[(int)$t['indikator_id']] = (float)$t['target'];}
            $ready=true;$weighted=0;$details=[];$gate='NORMAL';
            foreach($indikator as $i){$key=$i['formula_key'];$target=$targets[(int)$i['id']]??0;if($target<=0&&$key==='REALISASI_KREDIT'&&!empty($input['target_realisasi']))$target=(float)$input['target_realisasi'];if($target<=0&&$key==='NOA_REALISASI'&&!empty($input['target_noa']))$target=(float)$input['target_noa'];if($target<=0&&$key==='MOB_6')$target=0.05;if($target<=0&&$key==='REPAYMENT_RATE')$target=0.65;if($target<=0&&$key==='EARLY_RUN_OFF')$target=0.01;if($target<=0&&$key==='PIPELINE')$target=1.0;if($dummyMode&&$target<=0)$target=(float)($dummyTargets[$key]??(strtoupper((string)$i['unit'])==='PERSEN'?1:1));$real=$actual[$key]??null;$note='';$score=0;$idx=0;$value100=0;$dummyIndex=null;
                if($dummyMode){foreach($scoreByIndicator[(int)$i['id']]??[] as $dummyRange){if((int)$dummyRange['skor']===3){$dummyMin=(float)$dummyRange['min_indeks'];$dummyMax=(float)$dummyRange['max_indeks'];$dummyIndex=$dummyMax>=999?$dummyMin+0.25:($dummyMin+$dummyMax)/2;break;}}if($dummyIndex===null||$dummyIndex<=0)$dummyIndex=0.80;$real=strtoupper((string)$i['arah'])==='LOWER'?$target/$dummyIndex:$target*$dummyIndex;$note='DUMMY - sumber data real '.$selectedJabatan['nama'].' belum tersedia';}
                if($dummyMode){$idx=(float)$dummyIndex;}
                elseif($real===null){$ready=false;$note='Sumber data indikator belum dikonfigurasi';}
                elseif($target<=0){$ready=false;$note='Target periode belum diisi';}
                else{if($key==='MOB_6'){$idx=(float)$real;$score=$real<=0.05?5:($real<=0.06?4:($real<=0.07?3:($real<=0.08?2:1)));$note='OS menunggak MOB 1–6 / total OS MOB 1–6';}elseif($key==='EARLY_RUN_OFF'){$idx=(float)$real;$score=$real<=0.01?5:($real<=0.0125?4:($real<=0.015?3:($real<=0.02?2:1)));$note='OS pelunasan murni / OS DPD 0 closing sebelumnya; refinancing/top-up dikecualikan';}elseif($key==='PIPELINE'){$idx=(float)$real;$score=min(5,(int)$real+1);$note='NOA pipeline yang cair/realisasi pada periode berjalan';}else{$idx=strtoupper($i['arah'])==='LOWER'?($real==0?1.5:min($target/$real,1.5)):min($real/$target,1.5);foreach($scoreRows as $s){if($idx>=(float)$s['min_indeks']&&$idx<(float)$s['max_indeks']||((int)$s['skor']===5&&$idx>=(float)$s['min_indeks'])){$score=(int)$s['skor'];break;}}}$weighted=(float)$i['bobot']*$score;$value100=min((float)$i['bobot']*100,$weighted/5*100);}
                $isCount=in_array(strtoupper((string)($i['unit']??'')),['NOA','JUMLAH'],true);$isLower=strtoupper((string)($i['arah']??''))==='LOWER';$scoreBasis=$isCount?(float)$real:(float)$idx;$score=$scoreFor((int)$i['id'],$scoreBasis,$scoreByIndicator,$isCount,$isLower);$weighted=(float)$i['bobot']*$score;$value100=min((float)$i['bobot']*100,$weighted/5*100);$details[]=['indikator'=>$i,'target'=>$target,'realisasi'=>$real,'indeks'=>$idx,'skor'=>$score,'nilai_tertimbang'=>$weighted,'nilai_100'=>$value100,'os_mob_menunggak'=>$key==='MOB_6'?(float)($actual['OS_MOB_MENUNGGAK']??0):0,'os_mob_total'=>$key==='MOB_6'?(float)($actual['OS_MOB_TOTAL']??0):0,'os_dpd0'=>$key==='REPAYMENT_RATE'?(float)($actual['OS_DPD0']??0):0,'os_kelolaan'=>$key==='REPAYMENT_RATE'?(float)($actual['OS_KELOLAAN']??0):0,'os_run_off'=>$key==='EARLY_RUN_OFF'?(float)($actual['OS_RUN_OFF']??0):0,'os_dpd0_m1'=>$key==='EARLY_RUN_OFF'?(float)($actual['OS_DPD0_M1']??0):0,'catatan'=>$note];
            }
            $base=$ready?array_sum(array_column($details,'nilai_100')):0;$factor=(float)($risk[$gate]??1);$final=$base*$factor;$partial=(bool)$selectedCodes;$status=$ready&&!$partial&&!$dummyMode?'DISETUJUI':'DRAFT';
            $this->pdo->beginTransaction();try{
                $up=$this->pdo->prepare("INSERT INTO kpi_penilaian(jabatan_id,id_peg,kode_kantor,kode_ao,nama_ao,tahun,bulan,closing_date,nilai_dasar,risk_gate,faktor_risiko,nilai_akhir,predikat,status,generated_at) VALUES(:jab,:idpeg,:kantor,:ao,:nama,:tahun,:bulan,:closing,:base,:gate,:factor,:final,:predikat,:status,NOW()) ON DUPLICATE KEY UPDATE kode_kantor=VALUES(kode_kantor),kode_ao=VALUES(kode_ao),nama_ao=VALUES(nama_ao),closing_date=VALUES(closing_date),nilai_dasar=VALUES(nilai_dasar),risk_gate=VALUES(risk_gate),faktor_risiko=VALUES(faktor_risiko),nilai_akhir=VALUES(nilai_akhir),predikat=VALUES(predikat),status=VALUES(status),generated_at=NOW()");
                $up->execute([':jab'=>$jab,':idpeg'=>$targetId,':kantor'=>$ao['kode_kantor']??null,':ao'=>$kodeAo,':nama'=>$ao['nama_ao'],':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing)),':closing'=>$closing,':base'=>$base,':gate'=>$gate,':factor'=>$factor,':final'=>$final,':predikat'=>$dummyMode?'DUMMY - sumber data real belum tersedia':($partial?'Fokus 2 indikator':($ready?($final>=90?'Istimewa':($final>=80?'Melampaui target':($final>=60?'Memenuhi target':'Perlu perbaikan'))):'Belum lengkap')),':status'=>$status]);
                $idSt=$this->pdo->prepare("SELECT id FROM kpi_penilaian WHERE jabatan_id=:jab AND id_peg=:idpeg AND tahun=:tahun AND bulan=:bulan");$idSt->execute([':jab'=>$jab,':idpeg'=>$targetId,':tahun'=>(int)date('Y',strtotime($closing)),':bulan'=>(int)date('n',strtotime($closing))]);$pid=(int)$idSt->fetchColumn();
                $this->pdo->prepare('DELETE FROM kpi_penilaian_detail WHERE penilaian_id=:pid')->execute([':pid'=>$pid]);$det=$this->pdo->prepare("INSERT INTO kpi_penilaian_detail(penilaian_id,indikator_id,target,realisasi,indeks,skor,nilai_tertimbang,nilai_100,os_mob_menunggak,os_mob_total,os_dpd0,os_kelolaan,os_run_off,os_dpd0_m1,sumber_snapshot,catatan) VALUES(:pid,:iid,:target,:real,:idx,:score,:weighted,:value100,:os_bad,:os_total,:os_dpd0,:os_kelolaan,:os_run_off,:os_dpd0_m1,:source,:note)");foreach($details as $d){$det->execute([':pid'=>$pid,':iid'=>$d['indikator']['id'],':target'=>$d['target'],':real'=>(float)($d['realisasi']??0),':idx'=>(float)$d['indeks'],':score'=>$d['skor'],':weighted'=>$d['nilai_tertimbang'],':value100'=>$d['nilai_100'],':os_bad'=>$d['os_mob_menunggak'],':os_total'=>$d['os_mob_total'],':os_dpd0'=>$d['os_dpd0'],':os_kelolaan'=>$d['os_kelolaan'],':os_run_off'=>$d['os_run_off'],':os_dpd0_m1'=>$d['os_dpd0_m1'],':source'=>$d['indikator']['sumber_data'],':note'=>$d['catatan']]);}$this->pdo->commit();$saved[]=['closing_date'=>$closing,'status'=>$status,'nilai_dasar'=>$base,'nilai_akhir'=>$final,'keterangan'=>$dummyMode?'DUMMY - sumber data real belum tersedia':null];
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
        $st=$this->pdo->query("SELECT i.*,j.kode AS jabatan_kode,j.nama AS jabatan_nama,
                                      CASE WHEN i.formula_key='PIPELINE' THEN 'NOA' ELSE i.unit END AS unit,
                                      COALESCE((SELECT t.target FROM kpi_target_bulanan t
                                                WHERE t.indikator_id=i.id AND t.jabatan_id=i.jabatan_id
                                                  AND t.tahun=0 AND t.bulan=0
                                                  AND t.id_peg IS NULL AND t.kode_kantor IS NULL
                                                ORDER BY t.id DESC LIMIT 1),0) AS target_default
                               FROM kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
                               ORDER BY j.nama,i.urutan,i.id");
        $this->json(200,'Setting KPI berhasil dimuat',['jabatan'=>$jabatan,'indikator'=>$st->fetchAll(PDO::FETCH_ASSOC),'parameter_skor'=>$this->pdo->query("SELECT s.id,s.jabatan_id,s.indikator_id,s.skor,s.min_indeks,s.max_indeks,s.predikat,s.aktif,j.kode AS jabatan_kode,j.nama AS jabatan_nama,i.unit FROM kpi_parameter_skor_jabatan s JOIN kpi_jabatan j ON j.id=s.jabatan_id JOIN kpi_indikator i ON i.id=s.indikator_id ORDER BY j.nama,s.indikator_id,s.skor")->fetchAll(PDO::FETCH_ASSOC),'can_manage'=>$this->canManage($user)]);
    }

    /** Range skor saja untuk editor inline di halaman setting. */
    public function settingScores(array $input, array $user): void
    {
        $rows=$this->pdo->query("SELECT s.id,s.jabatan_id,s.indikator_id,s.skor,s.min_indeks,s.max_indeks,s.predikat,s.aktif,
                                        j.kode AS jabatan_kode,j.nama AS jabatan_nama,i.unit
                                 FROM kpi_parameter_skor_jabatan s
                                 JOIN kpi_jabatan j ON j.id=s.jabatan_id
                                 JOIN kpi_indikator i ON i.id=s.indikator_id
                                 WHERE j.aktif=1
                                 ORDER BY j.nama,s.indikator_id,s.skor")->fetchAll(PDO::FETCH_ASSOC);
        $this->json(200,'Range skor berhasil dimuat',['parameter_skor'=>$rows]);
    }

    public function detail(array $input, array $user): void
    {
        $id=(int)($input['penilaian_id']??0); if(!$id){$this->json(422,'ID penilaian wajib diisi');return;}
        $st=$this->pdo->prepare("SELECT d.*,i.kelompok,i.nama,i.bobot,i.arah,i.unit,i.formula_key,i.sumber_data,i.input_pa FROM kpi_penilaian_detail d JOIN kpi_indikator i ON i.id=d.indikator_id WHERE d.penilaian_id=:id ORDER BY i.urutan,i.id");$st->execute([':id'=>$id]);
        $this->json(200,'Detail KPI berhasil dimuat',['data'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
    }

    /**
     * Rekap tahunan KPI semua jabatan AO.
     *
     * Satu baris mewakili satu AO, sedangkan nilai tiap bulan diambil dari
     * kpi_penilaian yang memang sudah pernah digenerate. Bulan yang belum
     * digenerate dibiarkan kosong agar tidak dianggap bernilai nol.
     */
    public function annual(array $input, array $user): void
    {
        $year=max(2020,min(2100,(int)($input['year']??date('Y'))));
        $ao=trim((string)($input['kode_ao']??''));
        $kantor=trim((string)($input['kode_kantor']??''));
        $selectedJabatan=$this->requestedJabatan($input);
        $jabatanKode=(string)$selectedJabatan['kode'];
        if($jabatanKode==='AO_DANA'){
            $aoJoin="JOIN ao_dana ao ON ao.id_peg=COALESCE(NULLIF(p.id_peg,''),p.kode_ao) AND ao.status=1";
        }elseif($jabatanKode==='AO_REMEDIAL'){
            $aoJoin="JOIN ao_remedial ao ON ao.id_peg=COALESCE(NULLIF(p.id_peg,''),p.kode_ao)
                     AND (ao.status IS NULL OR TRIM(ao.status)='' OR UPPER(ao.status) IN ('1','AKTIF','ACTIVE'))";
        }else{
            $aoJoin="JOIN ao_kredit ao ON ao.kode_group2=p.kode_ao AND ao.status=1";
        }
        $params=[':tahun'=>$year,':jabatan_id'=>(int)$selectedJabatan['id']];
        $where=' WHERE p.jabatan_id=:jabatan_id AND p.tahun=:tahun';
        if($ao!==''){$where.=' AND p.kode_ao=:ao';$params[':ao']=$ao;}
        if($kantor!=='' && $kantor!=='000'){
            $where.=" AND LPAD(CAST(ao.kode_kantor AS CHAR),3,'0')=:kantor";
            $params[':kantor']=str_pad($kantor,3,'0',STR_PAD_LEFT);
        }
        $sql="SELECT p.id,p.id_peg,p.kode_ao,p.nama_ao,
                     LPAD(CAST(ao.kode_kantor AS CHAR),3,'0') AS kode_kantor,
                     p.bulan,p.closing_date,p.nilai_akhir,p.status
              FROM kpi_penilaian p
              {$aoJoin}
              {$where}
              ORDER BY p.nama_ao,p.bulan";
        $st=$this->pdo->prepare($sql);$st->execute($params);
        $raw=$st->fetchAll(PDO::FETCH_ASSOC);

        $tukinRows=$this->pdo->query("SELECT id,min_skor,max_skor,min_nilai,max_nilai,faktor_persen,label,urutan
                                      FROM kpi_parameter_tukin WHERE aktif=1 ORDER BY urutan,id")->fetchAll(PDO::FETCH_ASSOC);
        $tukin=static function(float $score) use ($tukinRows): array {
            foreach($tukinRows as $rule){
                $min=(float)$rule['min_skor'];$max=$rule['max_skor']===null?null:(float)$rule['max_skor'];
                if($score>=$min&&($max===null||$score<$max))return ['persen'=>(float)$rule['faktor_persen'],'label'=>$rule['label']];
            }
            return ['persen'=>0,'label'=>'Belum ada parameter'];
        };

        $byAo=[];$monthly=[];
        foreach($raw as $row){
            $key=(string)$row['id_peg'];
            if(!isset($byAo[$key]))$byAo[$key]=[
                'id_peg'=>$row['id_peg'],'kode_ao'=>$row['kode_ao'],'nama_ao'=>$row['nama_ao'],
                'kode_kantor'=>$row['kode_kantor'],'bulan_terisi'=>0,'nilai_akhir'=>null,
                'skor_final'=>null,'tukin_persen'=>null,'tukin_label'=>null,'monthly'=>[]
            ];
            $value=(float)$row['nilai_akhir'];
            $byAo[$key]['monthly'][(int)$row['bulan']]=[
                'nilai_akhir'=>$value,'skor'=>round($value/20,2),'tukin_persen'=>$tukin($value/20)['persen'],
                'status'=>$row['status'],'closing_date'=>$row['closing_date'],'penilaian_id'=>(int)$row['id']
            ];
        }
        foreach($byAo as &$item){
            $values=array_column($item['monthly'],'nilai_akhir');
            $item['bulan_terisi']=count($values);
            if($values){
                $item['nilai_akhir']=round(array_sum($values)/count($values),2);
                $item['skor_final']=round($item['nilai_akhir']/20,2);
                $item['tukin_persen']=$tukin($item['skor_final'])['persen'];
                $item['tukin_label']=$tukin($item['skor_final'])['label'];
            }
        }
        unset($item);
        $rows=array_values($byAo);
        usort($rows,static function(array $a,array $b): int {
            $scoreA=$a['nilai_akhir']===null?-1:(float)$a['nilai_akhir'];
            $scoreB=$b['nilai_akhir']===null?-1:(float)$b['nilai_akhir'];
            return $scoreA===$scoreB?strcasecmp((string)$a['nama_ao'],(string)$b['nama_ao']):($scoreB<=>$scoreA);
        });
        $totalAo=count($rows);
        $isConsolidated=$kantor===''||$kantor==='000';
        $visibleAoKeys=null;
        if($isConsolidated){
            $rows=array_slice($rows,0,5);
            $visibleAoKeys=[];
            foreach($rows as $item)$visibleAoKeys[(string)$item['id_peg']]=true;
        }

        $monthSummary=[];
        for($month=1;$month<=12;$month++){
            $values=[];
            foreach($rows as $item)if(isset($item['monthly'][$month]))$values[]=(float)$item['monthly'][$month]['nilai_akhir'];
            $avg=$values?round(array_sum($values)/count($values),2):null;
            $monthSummary[]=[
                'bulan'=>$month,'terisi'=>count($values),'nilai_akhir'=>$avg,
                'skor'=>$avg===null?null:round($avg/20,2),
                'tukin_persen'=>$avg===null?null:$tukin($avg/20)['persen']
            ];
        }
        $breakdownSql="SELECT p.id AS penilaian_id,p.bulan,p.closing_date,p.id_peg,p.nama_ao,
                              LPAD(CAST(ao.kode_kantor AS CHAR),3,'0') AS kode_kantor,
                              i.kode,i.nama,i.kelompok,i.unit,i.bobot,i.input_pa,
                              d.target,d.realisasi,d.indeks,d.skor,d.nilai_100,d.catatan
                       FROM kpi_penilaian_detail d
                       JOIN kpi_penilaian p ON p.id=d.penilaian_id
                       {$aoJoin}
                       JOIN kpi_indikator i ON i.id=d.indikator_id
                       {$where}
                       ORDER BY p.nama_ao,p.bulan,i.urutan,i.id";
        $breakdownSt=$this->pdo->prepare($breakdownSql);$breakdownSt->execute($params);
        $breakdown=$breakdownSt->fetchAll(PDO::FETCH_ASSOC);
        $monthlyMeta=[];
        foreach($rows as $item){
            foreach($item['monthly'] as $month=>$monthlyValue){
                $monthlyMeta[(string)$item['id_peg'].'|'.(int)$month]=[
                    'nilai_akhir_bulan'=>$monthlyValue['nilai_akhir'],
                    'tukin_persen_bulan'=>$monthlyValue['tukin_persen']
                ];
            }
        }
        foreach($breakdown as &$item){
            $meta=$monthlyMeta[(string)$item['id_peg'].'|'.(int)$item['bulan']]??[];
            $item['nilai_akhir_bulan']=$meta['nilai_akhir_bulan']??null;
            $item['tukin_persen_bulan']=$meta['tukin_persen_bulan']??null;
        }
        unset($item);
        if($visibleAoKeys!==null){
            $breakdown=array_values(array_filter(
                $breakdown,
                static fn(array $item): bool => isset($visibleAoKeys[(string)$item['id_peg']])
            ));
        }
        $this->json(200,'Rekap KPI tahunan berhasil dimuat',[
            'year'=>$year,'kode_ao'=>$ao,'kode_kantor'=>$kantor,
            'jabatan'=>$selectedJabatan,'total_ao'=>$totalAo,'is_konsolidasi'=>$isConsolidated,'konsolidasi_top'=>5,
            'ao'=>$rows,'months'=>$monthSummary,
            'indicator_breakdown'=>$breakdown,'indicator_breakdown_monthly'=>$breakdown,
            'tukin_rules'=>$tukinRows
        ]);
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
        $id=(int)($input['id']??0); $fields=['bobot','arah','unit','frekuensi','status','definisi','sumber_data','validator','input_pa']; $set=[];$params=[':id'=>$id];
        foreach($fields as $field){ if(array_key_exists($field,$input)){ $set[]="{$field}=:{$field}"; $params[":{$field}"]=$input[$field]; }}
        if(!$id||!$set){$this->json(422,'Data indikator tidak lengkap');return;}
        $this->pdo->beginTransaction();
        try {
            $st=$this->pdo->prepare('UPDATE kpi_indikator SET '.implode(',',$set).' WHERE id=:id');$st->execute($params);
            if (array_key_exists('target',$input)) {
                $raw=trim((string)$input['target']);
                if ($raw==='' || !is_numeric($raw)) { $this->pdo->rollBack(); $this->json(422,'Target default harus berupa angka'); return; }
                $target=max(0,(float)$raw);
                $meta=$this->pdo->prepare('SELECT jabatan_id FROM kpi_indikator WHERE id=:id LIMIT 1');$meta->execute([':id'=>$id]);$jabatanId=(int)$meta->fetchColumn();
                if (!$jabatanId) { $this->pdo->rollBack(); $this->json(404,'Indikator KPI tidak ditemukan'); return; }
                $del=$this->pdo->prepare('DELETE FROM kpi_target_bulanan WHERE jabatan_id=:jab AND indikator_id=:indikator AND tahun=0 AND bulan=0 AND id_peg IS NULL AND kode_kantor IS NULL');
                $del->execute([':jab'=>$jabatanId,':indikator'=>$id]);
                $ins=$this->pdo->prepare('INSERT INTO kpi_target_bulanan (jabatan_id,indikator_id,tahun,bulan,id_peg,kode_kantor,target,catatan) VALUES (:jab,:indikator,0,0,NULL,NULL,:target,:catatan)');
                $ins->execute([':jab'=>$jabatanId,':indikator'=>$id,':target'=>$target,':catatan'=>'Target default dari Setting KPI Jabatan']);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack(); throw $e;
        }
        $this->json(200,'Parameter indikator disimpan');
    }

    public function saveTargetDefault(array $input, array $user): void
    {
        if (!$this->canManage($user)) { $this->json(403,'Tidak memiliki hak mengubah target KPI'); return; }
        $indicatorId=(int)($input['indikator_id']??0);
        if (!$indicatorId || !array_key_exists('target',$input) || !is_numeric($input['target'])) {
            $this->json(422,'Indikator dan target wajib diisi'); return;
        }
        $target=max(0,(float)$input['target']);
        $st=$this->pdo->prepare('SELECT jabatan_id FROM kpi_indikator WHERE id=:id LIMIT 1');
        $st->execute([':id'=>$indicatorId]); $jabatanId=(int)$st->fetchColumn();
        if (!$jabatanId) { $this->json(404,'Indikator KPI tidak ditemukan'); return; }
        $this->pdo->beginTransaction();
        try {
            // Unique key pada kolom nullable dapat mengizinkan duplikasi NULL,
            // jadi hapus scope default lebih dulu agar selalu hanya ada satu nilai aktif.
            $del=$this->pdo->prepare('DELETE FROM kpi_target_bulanan WHERE jabatan_id=:jab AND indikator_id=:indikator AND tahun=0 AND bulan=0 AND id_peg IS NULL AND kode_kantor IS NULL');
            $del->execute([':jab'=>$jabatanId,':indikator'=>$indicatorId]);
            $ins=$this->pdo->prepare('INSERT INTO kpi_target_bulanan (jabatan_id,indikator_id,tahun,bulan,id_peg,kode_kantor,target,catatan) VALUES (:jab,:indikator,0,0,NULL,NULL,:target,:catatan)');
            $ins->execute([':jab'=>$jabatanId,':indikator'=>$indicatorId,':target'=>$target,':catatan'=>'Target default dari Setting KPI Jabatan']);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack(); throw $e;
        }
        $this->json(200,'Target default indikator disimpan',['indikator_id'=>$indicatorId,'target'=>$target]);
    }

    public function saveScoreParameter(array $input, array $user): void
    {
        if (!$this->canManage($user)) { $this->json(403,'Tidak memiliki hak mengubah parameter skor'); return; }
        $id=(int)($input['id']??0); if(!$id){$this->json(422,'ID parameter wajib diisi');return;}
        $st=$this->pdo->prepare('UPDATE kpi_parameter_skor_jabatan SET min_indeks=:min,max_indeks=:max,predikat=:predikat,aktif=:aktif WHERE id=:id');
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
