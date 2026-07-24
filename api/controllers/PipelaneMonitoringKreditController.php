<?php

require_once __DIR__ . '/../helpers/response.php';

class PipelaneMonitoringKreditController
{
    private $pdoDpk;
    private $pdoProspek;

    private $statusOptions = [
        'Prospek',
        'Analisa',
        'Komite',
        'Akad Terjadwal',
        'Realisasi',
        'Tertunda'
    ];

    private $actionOptions = [
        'Follow-up dokumen legalitas',
        'Kunjungan usaha & verifikasi omzet',
        'Lengkapi appraisal agunan',
        'Input hasil SLIK dan analisa',
        'Ajukan ke komite kredit',
        'Konfirmasi jadwal akad nasabah',
        'Revisi proyeksi arus kas',
        'Monitoring penggunaan kredit'
    ];

    public function __construct($pdoDpk, $pdoProspek)
    {
        $this->pdoDpk = $pdoDpk;
        $this->pdoProspek = $pdoProspek;
    }

    public function ensureTables()
    {
        try {
            $sqlMonitoring = "
                CREATE TABLE IF NOT EXISTS pipeline_monitoring_kredit (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    prospect_id BIGINT UNSIGNED NOT NULL,
                    pipeline_id BIGINT UNSIGNED NULL,
                    kode_kantor VARCHAR(10) NOT NULL,
                    tahun SMALLINT UNSIGNED NOT NULL,
                    bulan TINYINT UNSIGNED NOT NULL,
                    minggu_ke TINYINT UNSIGNED NOT NULL,
                    status_minggu_lalu VARCHAR(50) NULL,
                    action_minggu_ini VARCHAR(150) NULL,
                    status_terkini VARCHAR(50) NULL,
                    catatan TEXT NULL,
                    created_by VARCHAR(100) NULL,
                    updated_by VARCHAR(100) NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    UNIQUE KEY uniq_pipeline_week (prospect_id, tahun, bulan, minggu_ke),
                    KEY idx_kantor_periode (kode_kantor, tahun, bulan),
                    KEY idx_status (status_terkini)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ";

            $sqlHistory = "
                CREATE TABLE IF NOT EXISTS pipeline_monitoring_kredit_history (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    monitoring_id BIGINT UNSIGNED NULL,
                    prospect_id BIGINT UNSIGNED NOT NULL,
                    pipeline_id BIGINT UNSIGNED NULL,
                    kode_kantor VARCHAR(10) NOT NULL,
                    tahun SMALLINT UNSIGNED NOT NULL,
                    bulan TINYINT UNSIGNED NOT NULL,
                    minggu_ke TINYINT UNSIGNED NOT NULL,
                    action_label VARCHAR(150) NULL,
                    status_lama VARCHAR(50) NULL,
                    status_baru VARCHAR(50) NULL,
                    catatan TEXT NULL,
                    created_by VARCHAR(100) NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY idx_prospect_time (prospect_id, created_at),
                    KEY idx_kantor_periode (kode_kantor, tahun, bulan)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ";

            $this->pdoDpk->exec($sqlMonitoring);
            $this->pdoDpk->exec($sqlHistory);

            sendResponse(200, 'Tabel monitoring pipeline kredit siap.', [
                'tables' => [
                    'pipeline_monitoring_kredit',
                    'pipeline_monitoring_kredit_history'
                ]
            ]);
        } catch (PDOException $e) {
            sendResponse(500, 'Gagal membuat tabel monitoring: ' . $e->getMessage());
        }
    }

    public function getOptions()
    {
        sendResponse(200, 'Berhasil memuat opsi monitoring pipeline kredit.', [
            'status' => $this->statusOptions,
            'actions' => $this->actionOptions
        ]);
    }

    public function getDashboard($input = null)
    {
        $b = is_array($input) ? $input : [];
        $tahun = $this->normalYear($b['tahun'] ?? date('Y'));
        $bulan = $this->normalMonth($b['bulan'] ?? date('n'));
        $kodeKantor = $this->normalKodeKantor($b['kode_kantor'] ?? '000');
        $tanggalAkhir = $this->normalDate($b['harian_date'] ?? date('Y-m-d'));

        $periodeAwal = sprintf('%04d-%02d-01', $tahun, $bulan);
        $periodeAkhir = date('Y-m-t', strtotime($periodeAwal));
        if (substr($tanggalAkhir, 0, 7) === substr($periodeAwal, 0, 7) && $tanggalAkhir < $periodeAkhir) {
            $periodeAkhir = $tanggalAkhir;
        }

        try {
            $summary = $this->loadDpkSummary($periodeAwal, $periodeAkhir, $kodeKantor);
            $targetProduksi = $this->loadTargetProduksiRbb($periodeAwal, $kodeKantor);
            $summary['target_produksi_rbb'] = $targetProduksi;
            $summary['target_run_off_rbb'] = null;
            $summary['persen_produksi_rbb'] = $targetProduksi > 0
                ? round(((float)$summary['realisasi_bulan_ini'] / $targetProduksi) * 100, 2)
                : 0;
            $pipeline = $this->loadPipelineList($tahun, $bulan, $kodeKantor, '', 1, 7);
            $monitoring = $this->loadMonitoringRows($tahun, $bulan, $kodeKantor, '', 1, 7);

            sendResponse(200, 'Berhasil memuat dashboard monitoring pipeline kredit.', [
                'periode' => [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'tanggal_awal' => $periodeAwal,
                    'tanggal_akhir' => $periodeAkhir
                ],
                'summary' => $summary,
                'pipeline' => $pipeline,
                'monitoring' => $monitoring
            ]);
        } catch (PDOException $e) {
            sendResponse(500, 'Gagal memuat dashboard monitoring: ' . $e->getMessage());
        }
    }

    public function getDaftarPipeline($input = null)
    {
        $b = is_array($input) ? $input : [];
        $tahun = $this->normalYear($b['tahun'] ?? date('Y'));
        $bulan = $this->normalMonth($b['bulan'] ?? date('n'));
        $kodeKantor = $this->normalKodeKantor($b['kode_kantor'] ?? '000');
        $search = trim((string)($b['search'] ?? ''));
        $page = max(1, (int)($b['page'] ?? 1));
        $limit = min(100, max(1, (int)($b['limit'] ?? 25)));

        try {
            sendResponse(200, 'Berhasil memuat daftar pipeline kredit.', $this->loadPipelineList($tahun, $bulan, $kodeKantor, $search, $page, $limit));
        } catch (PDOException $e) {
            sendResponse(500, 'Gagal memuat daftar pipeline: ' . $e->getMessage());
        }
    }

    public function getMonitoringMingguan($input = null)
    {
        $b = is_array($input) ? $input : [];
        $tahun = $this->normalYear($b['tahun'] ?? date('Y'));
        $bulan = $this->normalMonth($b['bulan'] ?? date('n'));
        $kodeKantor = $this->normalKodeKantor($b['kode_kantor'] ?? '000');
        $search = trim((string)($b['search'] ?? ''));
        $page = max(1, (int)($b['page'] ?? 1));
        $limit = min(100, max(1, (int)($b['limit'] ?? 25)));

        try {
            sendResponse(200, 'Berhasil memuat monitoring mingguan.', $this->loadMonitoringRows($tahun, $bulan, $kodeKantor, $search, $page, $limit));
        } catch (PDOException $e) {
            sendResponse(500, 'Gagal memuat monitoring mingguan: ' . $e->getMessage());
        }
    }

    public function saveMonitoring($input = null)
    {
        $b = is_array($input) ? $input : [];
        $prospectId = (int)($b['prospect_id'] ?? 0);
        $pipelineId = isset($b['pipeline_id']) && $b['pipeline_id'] !== '' ? (int)$b['pipeline_id'] : null;
        $kodeKantor = $this->normalKodeKantor($b['kode_kantor'] ?? '');
        $tahun = $this->normalYear($b['tahun'] ?? date('Y'));
        $bulan = $this->normalMonth($b['bulan'] ?? date('n'));
        $mingguKe = max(1, min(6, (int)($b['minggu_ke'] ?? 1)));
        $statusMingguLalu = $this->cleanText($b['status_minggu_lalu'] ?? '');
        $actionMingguIni = $this->cleanText($b['action_minggu_ini'] ?? '');
        $statusTerkini = $this->cleanText($b['status_terkini'] ?? '');
        $catatan = $this->cleanText($b['catatan'] ?? '');
        $user = $this->cleanText($b['user'] ?? ($b['updated_by'] ?? 'system'));

        if ($prospectId <= 0 || $kodeKantor === '') {
            sendResponse(400, 'prospect_id dan kode_kantor wajib diisi.');
        }

        try {
            $this->pdoDpk->beginTransaction();

            $sqlFind = "SELECT id, status_terkini FROM pipeline_monitoring_kredit
                        WHERE prospect_id = :prospect_id AND tahun = :tahun AND bulan = :bulan AND minggu_ke = :minggu_ke
                        LIMIT 1";
            $stmtFind = $this->pdoDpk->prepare($sqlFind);
            $stmtFind->execute([
                ':prospect_id' => $prospectId,
                ':tahun' => $tahun,
                ':bulan' => $bulan,
                ':minggu_ke' => $mingguKe
            ]);
            $existing = $stmtFind->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $sql = "UPDATE pipeline_monitoring_kredit
                        SET pipeline_id = :pipeline_id,
                            kode_kantor = :kode_kantor,
                            status_minggu_lalu = :status_minggu_lalu,
                            action_minggu_ini = :action_minggu_ini,
                            status_terkini = :status_terkini,
                            catatan = :catatan,
                            updated_by = :updated_by
                        WHERE id = :id";
                $stmt = $this->pdoDpk->prepare($sql);
                $stmt->execute([
                    ':pipeline_id' => $pipelineId,
                    ':kode_kantor' => $kodeKantor,
                    ':status_minggu_lalu' => $statusMingguLalu,
                    ':action_minggu_ini' => $actionMingguIni,
                    ':status_terkini' => $statusTerkini,
                    ':catatan' => $catatan,
                    ':updated_by' => $user,
                    ':id' => (int)$existing['id']
                ]);
                $monitoringId = (int)$existing['id'];
                $statusLama = $existing['status_terkini'];
            } else {
                $sql = "INSERT INTO pipeline_monitoring_kredit
                        (prospect_id, pipeline_id, kode_kantor, tahun, bulan, minggu_ke, status_minggu_lalu, action_minggu_ini, status_terkini, catatan, created_by, updated_by)
                        VALUES
                        (:prospect_id, :pipeline_id, :kode_kantor, :tahun, :bulan, :minggu_ke, :status_minggu_lalu, :action_minggu_ini, :status_terkini, :catatan, :created_by, :updated_by)";
                $stmt = $this->pdoDpk->prepare($sql);
                $stmt->execute([
                    ':prospect_id' => $prospectId,
                    ':pipeline_id' => $pipelineId,
                    ':kode_kantor' => $kodeKantor,
                    ':tahun' => $tahun,
                    ':bulan' => $bulan,
                    ':minggu_ke' => $mingguKe,
                    ':status_minggu_lalu' => $statusMingguLalu,
                    ':action_minggu_ini' => $actionMingguIni,
                    ':status_terkini' => $statusTerkini,
                    ':catatan' => $catatan,
                    ':created_by' => $user,
                    ':updated_by' => $user
                ]);
                $monitoringId = (int)$this->pdoDpk->lastInsertId();
                $statusLama = null;
            }

            $sqlHistory = "INSERT INTO pipeline_monitoring_kredit_history
                           (monitoring_id, prospect_id, pipeline_id, kode_kantor, tahun, bulan, minggu_ke, action_label, status_lama, status_baru, catatan, created_by)
                           VALUES
                           (:monitoring_id, :prospect_id, :pipeline_id, :kode_kantor, :tahun, :bulan, :minggu_ke, :action_label, :status_lama, :status_baru, :catatan, :created_by)";
            $stmtHistory = $this->pdoDpk->prepare($sqlHistory);
            $stmtHistory->execute([
                ':monitoring_id' => $monitoringId,
                ':prospect_id' => $prospectId,
                ':pipeline_id' => $pipelineId,
                ':kode_kantor' => $kodeKantor,
                ':tahun' => $tahun,
                ':bulan' => $bulan,
                ':minggu_ke' => $mingguKe,
                ':action_label' => $actionMingguIni,
                ':status_lama' => $statusLama,
                ':status_baru' => $statusTerkini,
                ':catatan' => $catatan,
                ':created_by' => $user
            ]);

            $this->pdoDpk->commit();
            sendResponse(200, 'Monitoring pipeline berhasil disimpan.', ['id' => $monitoringId]);
        } catch (PDOException $e) {
            if ($this->pdoDpk->inTransaction()) {
                $this->pdoDpk->rollBack();
            }
            sendResponse(500, 'Gagal menyimpan monitoring: ' . $e->getMessage());
        }
    }

    public function getHistory($input = null)
    {
        $b = is_array($input) ? $input : [];
        $prospectId = (int)($b['prospect_id'] ?? 0);
        if ($prospectId <= 0) {
            sendResponse(400, 'prospect_id wajib diisi.');
        }

        try {
            $profile = $this->loadProspectProfile($prospectId);
            $localHistory = $this->loadLocalHistory($prospectId);
            $prospekHistory = $this->loadProspekHistory($prospectId);

            sendResponse(200, 'Berhasil memuat history pipeline.', [
                'profile' => $profile,
                'monitoring_history' => $localHistory,
                'prospek_history' => $prospekHistory
            ]);
        } catch (PDOException $e) {
            sendResponse(500, 'Gagal memuat history pipeline: ' . $e->getMessage());
        }
    }

    private function loadDpkSummary($periodeAwal, $periodeAkhir, $kodeKantor)
    {
        $filterSql = '';
        $params = [
            ':start_date' => $periodeAwal,
            ':end_date' => $periodeAkhir
        ];

        if ($kodeKantor !== '000') {
            $filterSql = ' AND s.kode_kantor = :kode_kantor';
            $params[':kode_kantor'] = $kodeKantor;
        }

        $sql = "
            SELECT
                SUM(COALESCE(s.realisasi, 0)) AS realisasi_bulan_ini,
                SUM(COALESCE(s.restrukturisasi, 0)) AS restrukturisasi_bulan_ini,
                SUM(COALESCE(s.angsuran, 0)) AS run_off_bulan_ini,
                SUM(COALESCE(s.pelunasan, 0)) AS pelunasan_bulan_ini,
                SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS angsuran_bulan_ini,
                SUM(COALESCE(s.realisasi, 0) + COALESCE(s.restrukturisasi, 0) - COALESCE(s.angsuran, 0)) AS growth_bulan_ini
            FROM summary_kredit_harian_update s
            WHERE s.created >= :start_date
              AND s.created <= :end_date
              {$filterSql}
        ";
        $stmt = $this->pdoDpk->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($summary as $key => $value) {
            $summary[$key] = (float)($value ?? 0);
        }

        return $summary;
    }

    private function loadTargetProduksiRbb($periodeAwal, $kodeKantor)
    {
        $branchColumns = ['001','002','003','004','005','006','007','008','009','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028'];

        if ($kodeKantor === '000') {
            $sumColumns = [];
            foreach ($branchColumns as $code) {
                $sumColumns[] = "IFNULL(r.`{$code}`, 0)";
            }
            $targetExpr = '(' . implode(' + ', $sumColumns) . ')';
        } elseif (in_array($kodeKantor, $branchColumns, true)) {
            $targetExpr = "IFNULL(r.`{$kodeKantor}`, 0)";
        } else {
            return 0;
        }

        $sql = "
            SELECT SUM({$targetExpr}) AS target_produksi
            FROM rbb r
            INNER JOIN ref_rbb ref ON ref.kode_monbis = r.kode_monbis
            WHERE r.periode = :periode
              AND ref.kode_perkiraan = 'produksi.total'
        ";

        $stmt = $this->pdoDpk->prepare($sql);
        $stmt->bindValue(':periode', $periodeAwal, PDO::PARAM_STR);
        $stmt->execute();
        return (float)($stmt->fetchColumn() ?: 0);
    }

    private function loadPipelineList($tahun, $bulan, $kodeKantor, $search, $page, $limit)
    {
        $meta = $this->detectProspekColumns();
        $offset = ($page - 1) * $limit;
        $params = [
            ':start_date' => sprintf('%04d-%02d-01', $tahun, $bulan),
            ':end_date' => date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $tahun, $bulan)))
        ];
        $where = ["{$meta['date']} >= :start_date", "{$meta['date']} <= :end_date"];

        if ($meta['deleted']) {
            $where[] = "{$meta['deleted']} IS NULL";
        }
        if ($kodeKantor !== '000' && $meta['kode_kantor']) {
            $where[] = "{$meta['kode_kantor']} = :kode_kantor";
            $params[':kode_kantor'] = $kodeKantor;
        }
        if ($search !== '') {
            $where[] = "({$meta['nama']} LIKE :search OR {$meta['ao']} LIKE :search OR {$meta['kantor']} LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $whereSql = implode(' AND ', $where);

        $sqlCount = "SELECT COUNT(1) FROM {$meta['from']} WHERE {$whereSql}";
        $stmtCount = $this->pdoProspek->prepare($sqlCount);
        foreach ($params as $key => $value) {
            $stmtCount->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetchColumn();

        $sql = "SELECT
                    {$meta['id']} AS prospect_id,
                    {$meta['pipeline_id']} AS pipeline_id,
                    {$meta['nama']} AS nama_debitur,
                    {$meta['plafon']} AS rencana_plafon,
                    {$meta['date']} AS tanggal_akuisisi,
                    {$meta['target']} AS target_realisasi,
                    {$meta['ao']} AS nama_ao,
                    {$meta['kantor']} AS nama_kantor,
                    {$meta['kode_kantor_select']} AS kode_kantor,
                    {$meta['status']} AS status_terkini
                FROM {$meta['from']}
                WHERE {$whereSql}
                ORDER BY {$meta['target']} ASC, {$meta['id']} DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdoProspek->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'pagination' => [
                'current_page' => $page,
                'total_records' => $total,
                'total_pages' => $limit > 0 ? (int)ceil($total / $limit) : 0
            ],
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    private function loadMonitoringRows($tahun, $bulan, $kodeKantor, $search, $page, $limit)
    {
        $pipeline = $this->loadPipelineList($tahun, $bulan, $kodeKantor, $search, $page, $limit);
        $rows = $pipeline['data'];
        if (!$rows) {
            return $pipeline;
        }

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int)$row['prospect_id'];
        }

        $placeholders = [];
        $params = [
            ':tahun' => $tahun,
            ':bulan' => $bulan
        ];
        foreach ($ids as $i => $id) {
            $key = ':id_' . $i;
            $placeholders[] = $key;
            $params[$key] = $id;
        }

        $sql = "SELECT *
                FROM pipeline_monitoring_kredit
                WHERE tahun = :tahun
                  AND bulan = :bulan
                  AND prospect_id IN (" . implode(',', $placeholders) . ")
                ORDER BY prospect_id ASC, minggu_ke DESC";

        $stmt = $this->pdoDpk->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $monitoringRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $byProspect = [];
        foreach ($monitoringRows as $m) {
            $pid = (int)$m['prospect_id'];
            if (!isset($byProspect[$pid])) {
                $byProspect[$pid] = [];
            }
            $byProspect[$pid][] = $m;
        }

        foreach ($rows as &$row) {
            $pid = (int)$row['prospect_id'];
            $row['monitoring'] = isset($byProspect[$pid]) ? $byProspect[$pid] : [];
            $latest = isset($byProspect[$pid][0]) ? $byProspect[$pid][0] : null;
            if ($latest) {
                $row['status_minggu_lalu'] = $latest['status_minggu_lalu'];
                $row['action_minggu_ini'] = $latest['action_minggu_ini'];
                $row['status_terkini'] = $latest['status_terkini'];
                $row['minggu_ke'] = (int)$latest['minggu_ke'];
            }
        }

        $pipeline['data'] = $rows;
        return $pipeline;
    }

    private function loadProspectProfile($prospectId)
    {
        $meta = $this->detectProspekColumns();
        $sql = "SELECT
                    {$meta['id']} AS prospect_id,
                    {$meta['pipeline_id']} AS pipeline_id,
                    {$meta['nama']} AS nama_debitur,
                    {$meta['plafon']} AS rencana_plafon,
                    {$meta['target']} AS target_realisasi,
                    {$meta['ao']} AS nama_ao,
                    {$meta['kantor']} AS nama_kantor,
                    {$meta['kode_kantor_select']} AS kode_kantor,
                    {$meta['status']} AS status_terkini
                FROM {$meta['from']}
                WHERE {$meta['id']} = :id
                LIMIT 1";
        $stmt = $this->pdoProspek->prepare($sql);
        $stmt->bindValue(':id', $prospectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function loadLocalHistory($prospectId)
    {
        $sql = "SELECT *
                FROM pipeline_monitoring_kredit_history
                WHERE prospect_id = :prospect_id
                ORDER BY created_at DESC, id DESC";
        $stmt = $this->pdoDpk->prepare($sql);
        $stmt->bindValue(':prospect_id', $prospectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function loadProspekHistory($prospectId)
    {
        if (!$this->tableExists($this->pdoProspek, 'prospect_histories')) {
            return [];
        }

        $columns = $this->tableColumns($this->pdoProspek, 'prospect_histories');
        $prospectCol = $this->pickColumn($columns, ['prospect_id', 'prospek_id']);
        if (!$prospectCol) {
            return [];
        }

        $createdCol = $this->pickColumn($columns, ['created_at', 'tanggal', 'updated_at']);
        $sql = "SELECT * FROM prospect_histories WHERE {$prospectCol} = :prospect_id";
        if ($createdCol) {
            $sql .= " ORDER BY {$createdCol} DESC";
        }

        $stmt = $this->pdoProspek->prepare($sql);
        $stmt->bindValue(':prospect_id', $prospectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function detectProspekColumns()
    {
        if (!$this->tableExists($this->pdoProspek, 'prospects')) {
            sendResponse(500, 'Tabel prospects tidak ditemukan di database Prospek.');
        }

        $prospectColumns = $this->tableColumns($this->pdoProspek, 'prospects');
        $pipelineColumns = $this->tableExists($this->pdoProspek, 'prospect_credit_pipelines')
            ? $this->tableColumns($this->pdoProspek, 'prospect_credit_pipelines')
            : [];
        $cabangColumns = $this->tableExists($this->pdoProspek, 'cabangs')
            ? $this->tableColumns($this->pdoProspek, 'cabangs')
            : [];

        $id = $this->pickColumn($prospectColumns, ['id']);
        $nama = $this->pickColumn($prospectColumns, ['nama_debitur', 'nama_nasabah', 'nama', 'name']);
        if (!$id || !$nama) {
            sendResponse(500, 'Struktur tabel prospek belum dikenali untuk monitoring pipeline.');
        }

        $pipelineId = $this->pickColumn($pipelineColumns, ['id']);
        $pipelineProspectId = $this->pickColumn($pipelineColumns, ['prospect_id', 'prospek_id']);
        $joinPipeline = ($pipelineId && $pipelineProspectId)
            ? ' LEFT JOIN prospect_credit_pipelines cp ON cp.' . $pipelineProspectId . ' = p.' . $id
            : '';

        $cabangId = $this->pickColumn($prospectColumns, ['cabang_id']);
        $cabangKode = $this->pickColumn($cabangColumns, ['kode_cabang', 'kode_kantor']);
        $cabangNama = $this->pickColumn($cabangColumns, ['nama_cabang', 'nama_kantor', 'nama']);
        $joinCabang = ($cabangId && $cabangKode)
            ? ' LEFT JOIN cabangs c ON c.id = p.' . $cabangId
            : '';

        $plafonP = $this->pickColumn($prospectColumns, ['rencana_plafon', 'plafon', 'plafond', 'nominal_plafon', 'nominal_plafond', 'nominal_pengajuan', 'jumlah_pinjaman', 'nominal']);
        $plafonCp = $this->pickColumn($pipelineColumns, ['rencana_plafon', 'plafon', 'plafond', 'nominal_plafon', 'nominal_plafond', 'nominal_pengajuan', 'jumlah_pinjaman', 'nominal']);

        $tanggalP = $this->pickColumn($prospectColumns, ['tanggal_akuisisi', 'tanggal_prospek', 'created_at']);
        $tanggalCp = $this->pickColumn($pipelineColumns, ['tanggal_akuisisi', 'tanggal_prospek', 'created_at']);

        $targetP = $this->pickColumn($prospectColumns, ['target_realisasi', 'target_date', 'target_tanggal_realisasi', 'tanggal_target_realisasi', 'tanggal_realisasi', 'created_at']);
        $targetCp = $this->pickColumn($pipelineColumns, ['target_realisasi', 'target_date', 'target_tanggal_realisasi', 'tanggal_target_realisasi', 'tanggal_realisasi', 'created_at']);

        $aoP = $this->pickColumn($prospectColumns, ['nama_ao', 'ao_name', 'diambil_oleh', 'ao']);
        $aoCp = $this->pickColumn($pipelineColumns, ['nama_ao', 'ao_name', 'ao']);

        $kodeKantorP = $this->pickColumn($prospectColumns, ['kode_kantor', 'kode_cabang']);
        $statusP = $this->pickColumn($prospectColumns, ['status_terkini', 'status_pipeline', 'status']);
        $statusCp = $this->pickColumn($pipelineColumns, ['status_terkini', 'status_pipeline', 'status']);
        $deleted = $this->pickColumn($prospectColumns, ['deleted_at']);

        $plafonExpr = $this->coalesceColumns([
            $plafonCp ? 'cp.' . $plafonCp : null,
            $plafonP ? 'p.' . $plafonP : null,
            '0'
        ]);
        $dateExpr = $this->coalesceColumns([
            $tanggalCp ? 'cp.' . $tanggalCp : null,
            $tanggalP ? 'p.' . $tanggalP : null,
            'p.created_at'
        ]);
        $targetExpr = $this->coalesceColumns([
            $targetCp ? 'cp.' . $targetCp : null,
            $targetP ? 'p.' . $targetP : null,
            $dateExpr
        ]);
        $aoExpr = $this->coalesceColumns([
            $aoCp ? 'cp.' . $aoCp : null,
            $aoP ? 'p.' . $aoP : null,
            "'-'"
        ]);
        $kantorExpr = $this->coalesceColumns([
            $cabangNama ? 'c.' . $cabangNama : null,
            "'-'"
        ]);
        $kodeKantorExpr = $this->coalesceColumns([
            $cabangKode ? 'c.' . $cabangKode : null,
            $kodeKantorP ? 'p.' . $kodeKantorP : null,
            $cabangId ? 'p.' . $cabangId : null,
            "'-'"
        ]);
        $kodeKantorFilter = $cabangKode ? 'c.' . $cabangKode : ($kodeKantorP ? 'p.' . $kodeKantorP : ($cabangId ? 'p.' . $cabangId : null));
        $statusExpr = $this->coalesceColumns([
            $statusCp ? 'cp.' . $statusCp : null,
            $statusP ? 'p.' . $statusP : null,
            "'Prospek'"
        ]);

        return [
            'from' => 'prospects p' . $joinPipeline . $joinCabang,
            'id' => 'p.' . $id,
            'pipeline_id' => $pipelineId ? 'cp.' . $pipelineId : 'NULL',
            'nama' => 'p.' . $nama,
            'plafon' => $plafonExpr,
            'date' => $dateExpr,
            'target' => $targetExpr,
            'ao' => $aoExpr,
            'kantor' => $kantorExpr,
            'kode_kantor' => $kodeKantorFilter,
            'kode_kantor_select' => $kodeKantorExpr,
            'status' => $statusExpr,
            'deleted' => $deleted ? 'p.' . $deleted : null
        ];
    }

    private function coalesceColumns($columns)
    {
        $valid = [];
        foreach ($columns as $column) {
            if ($column !== null && $column !== '') {
                $valid[] = $column;
            }
        }
        if (count($valid) === 1) {
            return $valid[0];
        }
        return 'COALESCE(' . implode(', ', $valid) . ')';
    }

    private function tableExists($pdo, $table)
    {
        $stmt = $pdo->prepare("SELECT COUNT(1)
                               FROM information_schema.TABLES
                               WHERE TABLE_SCHEMA = DATABASE()
                                 AND TABLE_NAME = :table_name");
        $stmt->bindValue(':table_name', $table, PDO::PARAM_STR);
        $stmt->execute();
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function tableColumns($pdo, $table)
    {
        $stmt = $pdo->query("SHOW COLUMNS FROM {$table}");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columns = [];
        foreach ($rows as $row) {
            $columns[] = $row['Field'];
        }
        return $columns;
    }

    private function pickColumn($columns, $candidates)
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }
        return null;
    }

    private function normalDate($date)
    {
        $ts = strtotime((string)$date);
        return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
    }

    private function normalYear($year)
    {
        $year = (int)$year;
        if ($year < 2000 || $year > 2100) {
            return (int)date('Y');
        }
        return $year;
    }

    private function normalMonth($month)
    {
        $month = (int)$month;
        if ($month < 1 || $month > 12) {
            return (int)date('n');
        }
        return $month;
    }

    private function normalKodeKantor($kode)
    {
        $kode = trim((string)$kode);
        if ($kode === '' || $kode === '000') {
            return '000';
        }
        if (ctype_digit($kode)) {
            return str_pad($kode, 3, '0', STR_PAD_LEFT);
        }
        return $kode;
    }

    private function cleanText($text)
    {
        return trim((string)$text);
    }
}
?>
