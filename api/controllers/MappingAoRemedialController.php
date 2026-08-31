<?php
require_once __DIR__ . '/../helpers/response.php';

class MappingAoRemedialController
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    private function currentUser(array $token): array
    {
        $employeeId = trim((string)($token['employee_id'] ?? ''));
        if ($employeeId === '') sendResponse(401, 'Identitas pengguna tidak ditemukan.');
        $stmt = $this->pdo->prepare('SELECT kode, employee_id, full_name, job_position, unit_kerja, level, role FROM users WHERE employee_id=? LIMIT 1');
        $stmt->execute([$employeeId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) sendResponse(401, 'Pengguna tidak ditemukan.');
        $user['kode_kantor'] = str_pad((string)$user['kode'], 3, '0', STR_PAD_LEFT);
        $job = strtolower((string)$user['job_position']);
        $unit = strtolower((string)$user['unit_kerja']);
        $branchLeader = preg_match('/kepala cabang|kepala bidang pemasaran/', $job) === 1;
        $headOffice = $user['kode_kantor'] === '000' && (
            strpos($unit, 'divisi operasional') !== false || strpos($unit, 'divisi penyelesaian kredit') !== false
        );
        $user['can_map'] = $user['kode_kantor'] === '000' ? $headOffice : $branchLeader;
        $user['can_view_all'] = $user['kode_kantor'] === '000';
        return $user;
    }

    private function closingDate(?string $requested = null): string
    {
        if ($requested && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requested)) {
            $stmt = $this->pdo->prepare('SELECT MAX(created) FROM nominatif WHERE created=? AND created=LAST_DAY(created)');
            $stmt->execute([$requested]);
            if ($date = $stmt->fetchColumn()) return (string)$date;
            sendResponse(422, 'Data closing akhir bulan yang dipilih tidak tersedia.');
        }
        $date = $this->pdo->query('SELECT MAX(created) FROM nominatif WHERE created=LAST_DAY(created)')->fetchColumn();
        if (!$date) sendResponse(404, 'Data closing nominatif belum tersedia.');
        return (string)$date;
    }

    private function previousClosing(string $closing): ?string
    {
        $stmt = $this->pdo->prepare('SELECT MAX(created) FROM nominatif WHERE created<? AND created=LAST_DAY(created)');
        $stmt->execute([$closing]);
        return $stmt->fetchColumn() ?: null;
    }

    private function actualDate(?string $requested = null, ?string $closing = null): string
    {
        if ($requested && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requested)) {
            if ($closing && $requested < $closing) sendResponse(422, 'Tanggal actual tidak boleh sebelum tanggal closing.');
            $stmt = $this->pdo->prepare('SELECT MAX(created) FROM nominatif WHERE created=?');
            $stmt->execute([$requested]);
            if ($date = $stmt->fetchColumn()) return (string)$date;
            sendResponse(422, 'Data nominatif pada tanggal actual yang dipilih tidak tersedia.');
        }
        if($closing){
            $stmt=$this->pdo->prepare('SELECT MAX(created) FROM nominatif WHERE created>=?');
            $stmt->execute([$closing]); $date=$stmt->fetchColumn();
        }else $date = $this->pdo->query('SELECT MAX(created) FROM nominatif')->fetchColumn();
        if (!$date) sendResponse(404, 'Data actual nominatif belum tersedia.');
        return (string)$date;
    }

    private function scope(array $input, array $user): string
    {
        if (!$user['can_view_all']) return $user['kode_kantor'];
        $raw = trim((string)($input['kode_kantor'] ?? '000'));
        return preg_match('/^\d{3}$/', $raw) ? $raw : '000';
    }

    private function korwilRange(?string $value): ?array
    {
        switch (strtoupper(trim((string)$value))) {
            case 'SEMARANG': return ['001','007'];
            case 'SOLO': return ['008','014'];
            case 'BANYUMAS': return ['015','021'];
            case 'PEKALONGAN': return ['022','028'];
            default: return null;
        }
    }

    private function context(array $input, array $user): array
    {
        $closing = $this->closingDate($input['closing_date'] ?? null);
        return [$closing, $this->previousClosing($closing), $this->scope($input, $user)];
    }

    public function bootstrap(array $input, array $token): void
    {
        $user = $this->currentUser($token);
        [$closing, $previous, $scope] = $this->context($input, $user);
        $actual = $this->actualDate($input['actual_date'] ?? null,$closing);
        $offices = $user['can_view_all']
            ? $this->pdo->query("SELECT kode_kantor,nama_kantor FROM kode_kantor WHERE kode_kantor<>'000' ORDER BY kode_kantor")->fetchAll(PDO::FETCH_ASSOC)
            : [];
        sendResponse(200, 'Konfigurasi mapping AO remedial.', [
            'user'=>['kode_kantor'=>$user['kode_kantor'],'full_name'=>$user['full_name'],'job_position'=>$user['job_position'],'can_map'=>(bool)$user['can_map'],'can_view_all'=>(bool)$user['can_view_all']],
            'closing_date'=>$closing,'actual_date'=>$actual,'max_actual_date'=>$actual,'previous_closing_date'=>$previous,'scope'=>$scope,'offices'=>$offices,
        ]);
    }

    private function listParts(array $input, array $user): array
    {
        [$closing, $previous, $scope] = $this->context($input, $user);
        $bucket = strtoupper(trim((string)($input['bucket'] ?? 'ALL')));
        $status = strtoupper(trim((string)($input['mapping_status'] ?? 'ALL')));
        $search = trim((string)($input['search'] ?? ''));
        $where = ['n.created=:closing','n.baki_debet>0','n.hari_menunggak>=31'];
        $params = [':closing'=>$closing];
        if ($scope !== '000') { $where[] = 'n.kode_cabang=:kode'; $params[':kode'] = $scope; }
        elseif (($range = $this->korwilRange(str_replace('KOR-', '', (string)($input['kode_kantor'] ?? ''))))) {
            $where[] = 'n.kode_cabang BETWEEN :korwil_start AND :korwil_end';
            $params[':korwil_start'] = $range[0]; $params[':korwil_end'] = $range[1];
        }
        if ($bucket === 'FE') $where[] = 'n.hari_menunggak BETWEEN 31 AND 180';
        elseif ($bucket === 'BE') $where[] = 'n.hari_menunggak>180';
        if ($status === 'MAPPED') $where[] = "NULLIF(TRIM(m.ao_fe_id_peg),'') IS NOT NULL AND NULLIF(TRIM(m.ao_be_id_peg),'') IS NOT NULL";
        elseif ($status === 'UNMAPPED') $where[] = "(NULLIF(TRIM(m.ao_fe_id_peg),'') IS NULL OR NULLIF(TRIM(m.ao_be_id_peg),'') IS NULL)";
        if ($search !== '') {
            $where[] = '(n.no_rekening LIKE :search_rek OR n.nama_nasabah LIKE :search_nama OR n.kode_cabang LIKE :search_kode OR m.ao_fe_nama LIKE :search_fe OR m.ao_be_nama LIKE :search_be OR n.deskripsi_kode_kelurahan LIKE :search_kel OR n.deskripsi_kode_kecamatan LIKE :search_kec)';
            foreach([':search_rek',':search_nama',':search_kode',':search_fe',':search_be',':search_kel',':search_kec'] as $key)$params[$key]='%'.$search.'%';
        }
        return [$closing,$previous,$scope,implode(' AND ',$where),$params];
    }

    public function list(array $input, array $token): void
    {
        $user = $this->currentUser($token);
        [$closing,$previous,$scope,$where,$params] = $this->listParts($input,$user);
        $all=!empty($input['all']);
        $page = max(1,(int)($input['page'] ?? 1));
        $limit = min(100,max(10,(int)($input['limit'] ?? 25)));
        $offset = ($page-1)*$limit;
        $count = $this->pdo->prepare("SELECT COUNT(*) FROM nominatif n LEFT JOIN mapping_ao_remedial m ON m.no_rekening=n.no_rekening WHERE {$where}");
        $count->execute($params); $total = (int)$count->fetchColumn();
        $limitSql=$all?'':"LIMIT {$limit} OFFSET {$offset}";
        $sql = "SELECT base.*
                FROM (
                    SELECT n.kode_cabang kode_kantor,n.no_rekening,n.nama_nasabah,n.alamat,n.baki_debet,
                           n.hari_menunggak,n.hari_menunggak_pokok,n.hari_menunggak_bunga,
                           n.tunggakan_pokok,n.tunggakan_bunga,
                           GREATEST(COALESCE(n.tunggakan_pokok,0),0)+GREATEST(COALESCE(n.tunggakan_bunga,0),0) total_tunggakan,
                           n.kolektibilitas,n.kode_group1,n.deskripsi_kode_kelurahan,n.deskripsi_kode_kecamatan,
                           n.tgl_jatuh_tempo,COALESCE(n.nilai_ckpn,0) nilai_ckpn,
                           CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN 'FE' ELSE 'BE' END bucket,
                           m.id mapping_id,m.ao_fe_id_peg,m.ao_fe_nama,m.ao_be_id_peg,m.ao_be_nama,
                           CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN NULLIF(TRIM(m.ao_fe_id_peg),'') ELSE NULLIF(TRIM(m.ao_be_id_peg),'') END ao_aktif_id_peg,
                           CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN m.ao_fe_nama ELSE m.ao_be_nama END ao_aktif_nama,
                           (NULLIF(TRIM(m.ao_fe_id_peg),'') IS NOT NULL AND NULLIF(TRIM(m.ao_be_id_peg),'') IS NOT NULL) mapping_lengkap,
                           m.assigned_at,m.assigned_by_name
                    FROM nominatif n
                    LEFT JOIN mapping_ao_remedial m ON m.no_rekening=n.no_rekening
                    WHERE {$where}
                    ORDER BY (NULLIF(TRIM(m.ao_fe_id_peg),'') IS NULL OR NULLIF(TRIM(m.ao_be_id_peg),'') IS NULL) DESC,
                             n.deskripsi_kode_kelurahan,n.kode_cabang,n.hari_menunggak DESC,n.baki_debet DESC
                    {$limitSql}
                ) base
                ORDER BY base.deskripsi_kode_kelurahan,base.kode_kantor,base.mapping_lengkap,base.hari_menunggak DESC,base.baki_debet DESC";
        $stmt=$this->pdo->prepare($sql); $stmt->execute($params); $listRows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $officeMap=[];
        foreach($this->pdo->query('SELECT kode_kantor,nama_kantor FROM kode_kantor')->fetchAll(PDO::FETCH_ASSOC) as $office)$officeMap[(string)$office['kode_kantor']]=$office['nama_kantor'];
        $kankasMap=[];
        foreach($this->pdo->query('SELECT kode_kantor,kode_group1,deskripsi_group1 FROM kankas')->fetchAll(PDO::FETCH_ASSOC) as $unit)$kankasMap[$unit['kode_kantor'].'|'.$unit['kode_group1']]=$unit['deskripsi_group1'];
        foreach($listRows as &$row){
            $row['nama_kantor']=$officeMap[(string)$row['kode_kantor']]??$row['kode_kantor'];
            $row['kantor_kas']=$kankasMap[$row['kode_kantor'].'|'.$row['kode_group1']]??($row['kode_group1']?:'-');
        }
        unset($row);

        $summaryWhere=['n.created=:closing','n.baki_debet>0','n.hari_menunggak>=31'];
        $summaryParams=[':closing'=>$closing];
        if($scope!=='000'){ $summaryWhere[]='n.kode_cabang=:kode'; $summaryParams[':kode']=$scope; }
$sum=$this->pdo->prepare("SELECT COUNT(*) total,SUM(n.hari_menunggak BETWEEN 31 AND 180) fe,SUM(n.hari_menunggak>180) be,
                SUM(NULLIF(TRIM(m.ao_fe_id_peg),'') IS NOT NULL AND NULLIF(TRIM(m.ao_be_id_peg),'') IS NOT NULL) mapped,
                SUM(NULLIF(TRIM(m.ao_fe_id_peg),'') IS NULL OR NULLIF(TRIM(m.ao_be_id_peg),'') IS NULL) unmapped,
                SUM(CASE WHEN NULLIF(TRIM(m.ao_fe_id_peg),'') IS NULL OR NULLIF(TRIM(m.ao_be_id_peg),'') IS NULL THEN n.baki_debet ELSE 0 END) unmapped_bd
            FROM nominatif n LEFT JOIN mapping_ao_remedial m ON m.no_rekening=n.no_rekening WHERE ".implode(' AND ',$summaryWhere));
        $sum->execute($summaryParams);
        sendResponse(200,'Data mapping AO remedial.',['rows'=>$listRows,'summary'=>$sum->fetch(PDO::FETCH_ASSOC),'pagination'=>['page'=>$all?1:$page,'limit'=>$all?$total:$limit,'total'=>$total,'pages'=>$all?1:max(1,(int)ceil($total/$limit))],'closing_date'=>$closing,'previous_closing_date'=>$previous,'can_map'=>(bool)$user['can_map'],'scope'=>$scope]);
    }

    public function aoOptions(array $input, array $token): void
    {
        $user=$this->currentUser($token); $scope=$this->scope($input,$user);
        if($scope==='000') sendResponse(422,'Pilih satu cabang untuk melihat AO remedial.');
        $closing=$this->closingDate($input['closing_date']??null);
        $stmt=$this->pdo->prepare("SELECT a.id_peg,a.nama,a.kode_kantor,UPPER(a.remedial) remedial,
                    COALESCE(fe.total_mapped,0) total_mapped_fe,COALESCE(be.total_mapped,0) total_mapped_be
                FROM ao_remedial a
                LEFT JOIN (SELECT m.ao_fe_id_peg,COUNT(DISTINCT m.no_rekening) total_mapped FROM mapping_ao_remedial m INNER JOIN nominatif n ON n.no_rekening=m.no_rekening AND n.created=? WHERE n.kode_cabang=? GROUP BY m.ao_fe_id_peg) fe ON fe.ao_fe_id_peg=a.id_peg
                LEFT JOIN (SELECT m.ao_be_id_peg,COUNT(DISTINCT m.no_rekening) total_mapped FROM mapping_ao_remedial m INNER JOIN nominatif n ON n.no_rekening=m.no_rekening AND n.created=? WHERE n.kode_cabang=? GROUP BY m.ao_be_id_peg) be ON be.ao_be_id_peg=a.id_peg
                WHERE a.kode_kantor=? AND (a.status IS NULL OR UPPER(a.status) NOT IN ('NONAKTIF','INACTIVE')) ORDER BY a.remedial,a.nama");
        $stmt->execute([$closing,$scope,$closing,$scope,$scope]); sendResponse(200,'Daftar AO remedial.',$stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function recap(array $input, array $token): void
    {
        $user=$this->currentUser($token); [$closing,,$scope]=$this->context($input,$user);
        $actual=$this->actualDate($input['actual_date']??null,$closing);
        $bucket=strtoupper(trim((string)($input['bucket']??'ALL')));
        $status=strtoupper(trim((string)($input['mapping_status']??'ALL')));
        $search=trim((string)($input['search']??''));
        $activeId="CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN NULLIF(TRIM(m.ao_fe_id_peg),'') ELSE NULLIF(TRIM(m.ao_be_id_peg),'') END";
        $rawActiveName="CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN m.ao_fe_nama ELSE m.ao_be_nama END";
        $activeName="CASE WHEN {$activeId} IS NULL THEN NULL ELSE {$rawActiveName} END";
        $isLunas="(a.no_rekening IS NULL OR COALESCE(a.baki_debet,0)<=0)";
        $isBackflow="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND n.kolektibilitas IN ('KL','D','M') AND a.kolektibilitas IN ('L','DP'))";
        $isFlow="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND n.kolektibilitas IN ('L','DP') AND a.kolektibilitas IN ('KL','D','M'))";
        $isPerbaikan="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)<FLOOR((GREATEST(n.hari_menunggak,1)-1)/30))";
        $isPemburukan="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)>FLOOR((GREATEST(n.hari_menunggak,1)-1)/30))";
        $isStay="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)=FLOOR((GREATEST(n.hari_menunggak,1)-1)/30))";
        $where=['n.created=:closing','n.baki_debet>0','n.hari_menunggak>=31'];
        $params=[':closing'=>$closing];
        if($scope!=='000'){ $where[]='n.kode_cabang=:kode'; $params[':kode']=$scope; }
        elseif (($range = $this->korwilRange(str_replace('KOR-', '', (string)($input['kode_kantor'] ?? ''))))) {
            $where[]='n.kode_cabang BETWEEN :korwil_start AND :korwil_end';
            $params[':korwil_start']=$range[0]; $params[':korwil_end']=$range[1];
        }
        if($bucket==='FE')$where[]='n.hari_menunggak BETWEEN 31 AND 180';
        elseif($bucket==='BE')$where[]='n.hari_menunggak>180';
        if($status==='MAPPED')$where[]="{$activeId} IS NOT NULL";
        elseif($status==='UNMAPPED')$where[]="{$activeId} IS NULL";
        if($search!==''){
            $where[]="(n.kode_cabang LIKE :search_kode OR k.nama_kantor LIKE :search_kantor OR n.no_rekening LIKE :search_rek OR n.nama_nasabah LIKE :search_nama OR {$activeName} LIKE :search_ao)";
            foreach([':search_kode',':search_kantor',':search_rek',':search_nama',':search_ao'] as $key)$params[$key]='%'.$search.'%';
        }
        $sql="SELECT n.kode_cabang kode_kantor,COALESCE(k.nama_kantor,n.kode_cabang) nama_kantor,
                    CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN 'FE' ELSE 'BE' END bucket,
                    COALESCE({$activeId},'') ao_id_peg,COALESCE({$activeName},'BELUM TERMAPPING') ao_nama,
                    COUNT(*) total_noa,SUM(n.baki_debet) total_baki,
                    SUM({$activeId} IS NOT NULL) mapped_noa,SUM({$activeId} IS NULL) unmapped_noa,
                    SUM({$isLunas}) lunas_noa,SUM({$isBackflow}) backflow_noa,SUM({$isPerbaikan}) perbaikan_noa,SUM({$isStay}) stay_noa,SUM({$isPemburukan}) pemburukan_noa,SUM({$isFlow}) flow_noa,
                    SUM(CASE WHEN {$isLunas} THEN COALESCE(n.baki_debet,0) ELSE 0 END) lunas_bd,
                    SUM(CASE WHEN {$isBackflow} THEN COALESCE(a.baki_debet,0) ELSE 0 END) backflow_bd,
                    SUM(CASE WHEN {$isPerbaikan} THEN COALESCE(a.baki_debet,0) ELSE 0 END) perbaikan_bd,
                    SUM(CASE WHEN {$isStay} THEN COALESCE(a.baki_debet,0) ELSE 0 END) stay_bd,
                    SUM(CASE WHEN {$isPemburukan} THEN COALESCE(a.baki_debet,0) ELSE 0 END) pemburukan_bd,
                    SUM(CASE WHEN {$isFlow} THEN COALESCE(a.baki_debet,0) ELSE 0 END) flow_bd
              FROM nominatif n
              LEFT JOIN mapping_ao_remedial m ON m.no_rekening=n.no_rekening
              LEFT JOIN kode_kantor k ON k.kode_kantor=n.kode_cabang
              LEFT JOIN nominatif a ON a.no_rekening=n.no_rekening AND a.created=:actual
              WHERE ".implode(' AND ',$where)."
              GROUP BY n.kode_cabang,k.nama_kantor,
                       CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN 'FE' ELSE 'BE' END,
                       {$activeId},{$activeName}
              ORDER BY n.kode_cabang,bucket,({$activeId} IS NULL) DESC,ao_nama";
        $recapParams=$params; $recapParams[':actual']=$actual;
        $stmt=$this->pdo->prepare($sql); $stmt->execute($recapParams); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $accountSql="SELECT n.no_rekening,n.kode_cabang kode_kantor,
                    CASE WHEN n.hari_menunggak BETWEEN 31 AND 180 THEN 'FE' ELSE 'BE' END bucket,
                    COALESCE({$activeId},'') ao_id_peg
                FROM nominatif n
                LEFT JOIN mapping_ao_remedial m ON m.no_rekening=n.no_rekening
                LEFT JOIN kode_kantor k ON k.kode_kantor=n.kode_cabang
                WHERE ".implode(' AND ',$where);
        $accountStmt=$this->pdo->prepare($accountSql); $accountStmt->execute($params);
        $accounts=$accountStmt->fetchAll(PDO::FETCH_ASSOC);
        $accountGroup=[];
        foreach($accounts as $account)$accountGroup[(string)$account['no_rekening']]=$account['kode_kantor'].'|'.$account['bucket'].'|'.$account['ao_id_peg'];
        $groupAmount=[];
        foreach(array_chunk(array_keys($accountGroup),10000) as $chunk){
            if(!$chunk)continue;
            $marks=implode(',',array_fill(0,count($chunk),'?'));
            $pay=$this->pdo->prepare("SELECT no_rekening,
                    SUM(COALESCE(angsuran_pokok,0)+COALESCE(angsuran_bunga,0)+COALESCE(angsuran_denda,0)-COALESCE(diskon_bunga,0)) amount_call
                FROM transaksi_kredit WHERE tgl_trans>? AND tgl_trans<=? AND no_rekening IN ({$marks}) GROUP BY no_rekening");
            $pay->execute(array_merge([$closing,$actual],$chunk));
            foreach($pay->fetchAll(PDO::FETCH_ASSOC) as $payment){
                $key=$accountGroup[(string)$payment['no_rekening']]??'';
                if($key!=='')$groupAmount[$key]=($groupAmount[$key]??0)+(float)$payment['amount_call'];
            }
        }
        foreach($rows as &$row){
            $key=$row['kode_kantor'].'|'.$row['bucket'].'|'.$row['ao_id_peg'];
            $row['amount_call']=$groupAmount[$key]??0;
        }
        unset($row);
        $totalRow=['kode_kantor'=>'ALL','nama_kantor'=>'GRAND TOTAL','bucket'=>'ALL','ao_id_peg'=>'','ao_nama'=>'GRAND TOTAL','total_noa'=>0,'total_baki'=>0,'mapped_noa'=>0,'unmapped_noa'=>0,'fe_noa'=>0,'be_noa'=>0,'lunas_noa'=>0,'backflow_noa'=>0,'perbaikan_noa'=>0,'stay_noa'=>0,'pemburukan_noa'=>0,'flow_noa'=>0,'lunas_bd'=>0,'backflow_bd'=>0,'perbaikan_bd'=>0,'stay_bd'=>0,'pemburukan_bd'=>0,'flow_bd'=>0,'amount_call'=>0];
        foreach($rows as $row)foreach(['total_noa','total_baki','mapped_noa','unmapped_noa','lunas_noa','backflow_noa','perbaikan_noa','stay_noa','pemburukan_noa','flow_noa','lunas_bd','backflow_bd','perbaikan_bd','stay_bd','pemburukan_bd','flow_bd','amount_call'] as $key)$totalRow[$key]+=(float)($row[$key]??0);
        foreach($rows as $row) $totalRow[$row['bucket']==='FE'?'fe_noa':'be_noa']+=(float)($row['total_noa']??0);
        sendResponse(200,'Rekap mapping AO sesuai bucket closing dan posisi actual.',['rows'=>$rows,'total'=>$totalRow,'closing_date'=>$closing,'actual_date'=>$actual]);
    }

    public function detail(array $input, array $token): void
    {
        $user=$this->currentUser($token);
        [$closing,,$scope]=$this->context($input,$user);
        $actual=$this->actualDate($input['actual_date']??null,$closing);
        $office=str_pad(trim((string)($input['kode_kantor']??'')),3,'0',STR_PAD_LEFT);
        $bucket=strtoupper(trim((string)($input['bucket']??'')));
        $movement=strtoupper(trim((string)($input['movement_status']??'ALL')));
        $aoId=trim((string)($input['ao_id_peg']??''));
        $search=trim((string)($input['search']??''));
        $page=max(1,(int)($input['page']??1));
        $limit=min(50,max(10,(int)($input['limit']??20)));
        $offset=($page-1)*$limit;
        if(!preg_match('/^\d{3}$/',$office)||$office==='000')sendResponse(422,'Pilih rekap pada satu kantor.');
        if(!$user['can_view_all']&&$office!==$scope)sendResponse(403,'Anda hanya dapat melihat detail kantor sendiri.');
        if(!in_array($bucket,['FE','BE'],true))sendResponse(422,'Bucket detail harus FE atau BE.');
        if(!in_array($movement,['ALL','LUNAS','BACKFLOW','PERBAIKAN','STAY','PEMBURUKAN','FLOW'],true))sendResponse(422,'Status pergerakan tidak dikenali.');

        $activeId=$bucket==='FE'?"NULLIF(TRIM(m.ao_fe_id_peg),'')":"NULLIF(TRIM(m.ao_be_id_peg),'')";
        $where=['c.created=:closing','c.kode_cabang=:office','c.baki_debet>0'];
        $where[]=$bucket==='FE'?'c.hari_menunggak BETWEEN 31 AND 180':'c.hari_menunggak>180';
        $params=[':closing'=>$closing,':office'=>$office];
        if($aoId==='')$where[]="{$activeId} IS NULL";
        else{ $where[]="{$activeId}=:ao"; $params[':ao']=$aoId; }
        if($search!==''){
            $where[]='(c.no_rekening LIKE :search_rek OR c.nama_nasabah LIKE :search_nama OR c.alamat LIKE :search_alamat)';
            foreach([':search_rek',':search_nama',':search_alamat'] as $key)$params[$key]='%'.$search.'%';
        }
        $isLunas="(a.no_rekening IS NULL OR COALESCE(a.baki_debet,0)<=0)";
        $isBackflow="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND c.kolektibilitas IN ('KL','D','M') AND a.kolektibilitas IN ('L','DP'))";
        $isFlow="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND c.kolektibilitas IN ('L','DP') AND a.kolektibilitas IN ('KL','D','M'))";
        $isPerbaikan="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)<FLOOR((GREATEST(c.hari_menunggak,1)-1)/30))";
        $isPemburukan="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)>FLOOR((GREATEST(c.hari_menunggak,1)-1)/30))";
        $isStay="(a.no_rekening IS NOT NULL AND COALESCE(a.baki_debet,0)>0 AND NOT {$isBackflow} AND NOT {$isFlow} AND FLOOR((GREATEST(COALESCE(a.hari_menunggak,0),1)-1)/30)=FLOOR((GREATEST(c.hari_menunggak,1)-1)/30))";
        if($movement==='LUNAS')$where[]=$isLunas;
        elseif($movement==='BACKFLOW')$where[]=$isBackflow;
        elseif($movement==='PERBAIKAN')$where[]=$isPerbaikan;
        elseif($movement==='PEMBURUKAN')$where[]=$isPemburukan;
        elseif($movement==='STAY')$where[]=$isStay;
        elseif($movement==='FLOW')$where[]=$isFlow;
        $whereSql=implode(' AND ',$where);
        $count=$this->pdo->prepare("SELECT COUNT(*) total,SUM(c.baki_debet) baki_closing,SUM(COALESCE(a.baki_debet,0)) baki_actual
            FROM nominatif c LEFT JOIN mapping_ao_remedial m ON m.no_rekening=c.no_rekening
            LEFT JOIN nominatif a ON a.no_rekening=c.no_rekening AND a.created=:actual
            WHERE {$whereSql}");
        $countParams=$params; $countParams[':actual']=$actual;
        $count->execute($countParams); $summary=$count->fetch(PDO::FETCH_ASSOC)?:[];
        $total=(int)($summary['total']??0);

        $sql="SELECT c.no_rekening,c.kode_cabang kode_kantor,c.nama_nasabah,c.alamat,c.kode_group1,
                    COALESCE(kk.deskripsi_group1,c.kode_group1,'-') kantor_kas,c.deskripsi_kode_kelurahan,c.deskripsi_kode_kecamatan,
                    c.kolektibilitas kolektibilitas_closing,c.hari_menunggak dpd_closing,
                    c.hari_menunggak_pokok dpd_pokok_closing,c.hari_menunggak_bunga dpd_bunga_closing,
                    c.tunggakan_pokok tunggakan_pokok_closing,c.tunggakan_bunga tunggakan_bunga_closing,c.baki_debet baki_debet_closing,
                    {$activeId} ao_id_peg,".($bucket==='FE'?'m.ao_fe_nama':'m.ao_be_nama')." ao_nama
              FROM nominatif c
              LEFT JOIN mapping_ao_remedial m ON m.no_rekening=c.no_rekening
              LEFT JOIN kankas kk ON kk.kode_group1=c.kode_group1 AND kk.kode_kantor=c.kode_cabang
              LEFT JOIN nominatif a ON a.no_rekening=c.no_rekening AND a.created=:actual_source
              WHERE {$whereSql}
              ORDER BY c.hari_menunggak DESC,c.baki_debet DESC,c.no_rekening LIMIT {$limit} OFFSET {$offset}";
        $sourceParams=$params; $sourceParams[':actual_source']=$actual;
        $stmt=$this->pdo->prepare($sql); $stmt->execute($sourceParams); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

        if($rows){
            $rekening=array_column($rows,'no_rekening');
            $marks=implode(',',array_fill(0,count($rekening),'?'));
            $actualStmt=$this->pdo->prepare("SELECT no_rekening,nama_nasabah,alamat,kolektibilitas kolektibilitas_actual,
                    hari_menunggak dpd_actual,hari_menunggak_pokok dpd_pokok_actual,hari_menunggak_bunga dpd_bunga_actual,
                    tunggakan_pokok,tunggakan_bunga,
                    COALESCE(tunggakan_pokok,0)+COALESCE(tunggakan_bunga,0) total_tunggakan,baki_debet baki_debet_actual
                FROM nominatif WHERE created=? AND no_rekening IN ({$marks})");
            $actualStmt->execute(array_merge([$actual],$rekening));
            $actualByRek=array_column($actualStmt->fetchAll(PDO::FETCH_ASSOC),null,'no_rekening');
            $payStmt=$this->pdo->prepare("SELECT no_rekening,MAX(tgl_trans) tanggal_bayar_terakhir,
                    SUM(COALESCE(angsuran_pokok,0)) pembayaran_pokok,SUM(COALESCE(angsuran_bunga,0)) pembayaran_bunga,
                    SUM(COALESCE(angsuran_denda,0)) pembayaran_denda,SUM(COALESCE(diskon_bunga,0)) diskon_bunga,
                    SUM(COALESCE(angsuran_pokok,0)+COALESCE(angsuran_bunga,0)+COALESCE(angsuran_denda,0)-COALESCE(diskon_bunga,0)) amount_call
                FROM transaksi_kredit WHERE tgl_trans>? AND tgl_trans<=? AND no_rekening IN ({$marks}) GROUP BY no_rekening");
            $payStmt->execute(array_merge([$closing,$actual],$rekening));
            $payByRek=array_column($payStmt->fetchAll(PDO::FETCH_ASSOC),null,'no_rekening');
            foreach($rows as &$row){
                $rek=(string)$row['no_rekening'];
                $actualRow=$actualByRek[$rek]??[]; $pay=$payByRek[$rek]??[];
                if($actualRow){
                    $row['nama_nasabah']=$actualRow['nama_nasabah']?:$row['nama_nasabah'];
                    $row['alamat']=$actualRow['alamat']?:$row['alamat'];
                }
                $row['kolektibilitas_actual']=$actualRow['kolektibilitas_actual']??'LUNAS';
                foreach(['dpd_actual','dpd_pokok_actual','dpd_bunga_actual','tunggakan_pokok','tunggakan_bunga','total_tunggakan','baki_debet_actual'] as $key)$row[$key]=$actualRow[$key]??0;
                foreach(['pembayaran_pokok','pembayaran_bunga','pembayaran_denda','diskon_bunga','amount_call'] as $key)$row[$key]=$pay[$key]??0;
                $row['tanggal_bayar_terakhir']=$pay['tanggal_bayar_terakhir']??null;
                $dpdActual=(int)($actualRow['dpd_actual']??0); $bdActual=(float)($actualRow['baki_debet_actual']??0);
                $kolekClosing=strtoupper(trim((string)($row['kolektibilitas_closing']??'')));
                $kolekActual=strtoupper(trim((string)($row['kolektibilitas_actual']??'')));
                $closingBucket=(int)floor((max((int)($row['dpd_closing']??0),1)-1)/30);
                $actualBucket=(int)floor((max($dpdActual,1)-1)/30);
                $row['bucket_actual']=$actualRow ? ($dpdActual<=30?'SC':($dpdActual<=180?'FE':'BE')) : '-';
                if(!$actualRow||$bdActual<=0)$row['movement_status']='LUNAS';
                elseif(in_array($kolekClosing,['KL','D','M'],true)&&in_array($kolekActual,['L','DP'],true))$row['movement_status']='BACKFLOW';
                elseif(in_array($kolekClosing,['L','DP'],true)&&in_array($kolekActual,['KL','D','M'],true))$row['movement_status']='FLOW';
                elseif($actualBucket<$closingBucket)$row['movement_status']='PERBAIKAN';
                elseif($actualBucket>$closingBucket)$row['movement_status']='PEMBURUKAN';
                else $row['movement_status']='STAY';
            }
            unset($row);
        }
        sendResponse(200,'Detail kelolaan AO sesuai posisi closing dan actual.',[
            'rows'=>$rows,'summary'=>$summary,
            'pagination'=>['page'=>$page,'limit'=>$limit,'total'=>$total,'pages'=>max(1,(int)ceil($total/$limit))],
            'closing_date'=>$closing,'actual_date'=>$actual,'bucket'=>$bucket,'ao_id_peg'=>$aoId,'movement_status'=>$movement,
        ]);
    }

    public function assign(array $input, array $token): void
    {
        $user=$this->currentUser($token);
        if(!$user['can_map'])sendResponse(403,'Akun Anda hanya memiliki akses melihat mapping.');
        $rekening=array_values(array_unique(array_filter(array_map('strval',$input['no_rekening']??[]))));
        $aoFeId=trim((string)($input['ao_fe_id_peg']??''));
        $aoBeId=trim((string)($input['ao_be_id_peg']??''));
        $closing=$this->closingDate($input['closing_date']??null);
        if(!$rekening||count($rekening)>200)sendResponse(422,'Pilih 1 sampai 200 rekening.');
        if($aoFeId===''||$aoBeId==='')sendResponse(422,'AO kelolaan FE dan AO kelolaan BE wajib dipilih.');
        $marks=implode(',',array_fill(0,count($rekening),'?'));
        $stmt=$this->pdo->prepare("SELECT DISTINCT kode_cabang FROM nominatif WHERE created=? AND no_rekening IN ({$marks}) AND hari_menunggak>=31 AND baki_debet>0");
        $stmt->execute(array_merge([$closing],$rekening));
        $branches=$stmt->fetchAll(PDO::FETCH_COLUMN);
        if(count($branches)!==1)sendResponse(422,'Rekening yang dipilih harus berasal dari satu cabang.');
        $branch=str_pad((string)$branches[0],3,'0',STR_PAD_LEFT);
        if(!$user['can_view_all']&&$branch!==$user['kode_kantor'])sendResponse(403,'Anda hanya dapat mapping rekening kantor sendiri.');
        $aoStmt=$this->pdo->prepare('SELECT id_peg,nama,kode_kantor,UPPER(remedial) remedial FROM ao_remedial WHERE id_peg=? AND kode_kantor=? LIMIT 1');
        $aoStmt->execute([$aoFeId,$branch]); $aoFe=$aoStmt->fetch(PDO::FETCH_ASSOC);
        $aoStmt->execute([$aoBeId,$branch]); $aoBe=$aoStmt->fetch(PDO::FETCH_ASSOC);
        if(!$aoFe||!$aoBe)sendResponse(422,'AO FE atau AO BE tidak sesuai dengan cabang rekening.');
        $rowsStmt=$this->pdo->prepare("SELECT no_rekening,hari_menunggak FROM nominatif WHERE created=? AND no_rekening IN ({$marks})");
        $rowsStmt->execute(array_merge([$closing],$rekening));
        $byRek=array_column($rowsStmt->fetchAll(PDO::FETCH_ASSOC),null,'no_rekening');
        $this->pdo->beginTransaction();
        try{
            $upsert=$this->pdo->prepare('INSERT INTO mapping_ao_remedial
                (no_rekening,kode_kantor,ao_fe_id_peg,ao_fe_nama,ao_be_id_peg,ao_be_nama,bucket_awal,dpd_awal,closing_date_awal,assigned_by,assigned_by_name)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE ao_fe_id_peg=VALUES(ao_fe_id_peg),ao_fe_nama=VALUES(ao_fe_nama),ao_be_id_peg=VALUES(ao_be_id_peg),ao_be_nama=VALUES(ao_be_nama),assigned_by=VALUES(assigned_by),assigned_by_name=VALUES(assigned_by_name),assigned_at=NOW()');
            $find=$this->pdo->prepare('SELECT id FROM mapping_ao_remedial WHERE no_rekening=?');
            $history=$this->pdo->prepare('INSERT INTO mapping_ao_remedial_history (mapping_id,no_rekening,kode_kantor,ao_id_peg,ao_nama,action_type,action_by,action_by_name) VALUES (?,?,?,?,?,?,?,?)');
            foreach($rekening as $rek){
                if(!isset($byRek[$rek]))continue;
                $dpd=(int)$byRek[$rek]['hari_menunggak'];
                $upsert->execute([$rek,$branch,$aoFe['id_peg'],$aoFe['nama'],$aoBe['id_peg'],$aoBe['nama'],$dpd<=180?'FE':'BE',$dpd,$closing,$user['employee_id'],$user['full_name']]);
                $find->execute([$rek]); $mappingId=(int)$find->fetchColumn();
                $history->execute([$mappingId,$rek,$branch,$aoFe['id_peg'],$aoFe['nama'],'ASSIGN_FE',$user['employee_id'],$user['full_name']]);
                $history->execute([$mappingId,$rek,$branch,$aoBe['id_peg'],$aoBe['nama'],'ASSIGN_BE',$user['employee_id'],$user['full_name']]);
            }
            $this->pdo->commit();
        }catch(Throwable $e){$this->pdo->rollBack();sendResponse(500,'Mapping gagal disimpan.');}
        sendResponse(200,count($rekening).' rekening berhasil memiliki mapping FE dan BE.',['count'=>count($rekening),'ao_fe'=>$aoFe['nama'],'ao_be'=>$aoBe['nama']]);
    }
}
