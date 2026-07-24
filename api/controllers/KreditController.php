<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/MobHelper.php';
require_once __DIR__ . '/../helpers/filterHelpers.php';

class KreditController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRealisasiKredit($input = []) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        $kc           = $input['kode_kantor']  ?? null;
        
        if ($kc === '000' || empty($kc)) $kc = null;

        // 1. Setup Grouping & Join Table
        if ($kc) {
            $colKey     = "kode_group1"; 
            $selectName = "COALESCE(k.deskripsi_group1, CONCAT('KAS ', base.unit_key)) as nama_unit";
            $joinTable  = "LEFT JOIN kankas k ON base.unit_key = k.kode_group1";
            $filter     = "AND t.kode_cabang = '" . str_pad((string)$kc, 3, '0', STR_PAD_LEFT) . "'";
        } else {
            $colKey     = "kode_cabang";
            $selectName = "COALESCE(k.nama_kantor, CONCAT('CABANG ', base.unit_key)) as nama_unit";
            $joinTable  = "LEFT JOIN kode_kantor k ON base.unit_key = k.kode_kantor";
            $filter     = "";
        }

        // 2. Query Utama dengan Logic Simpel
        $sql = "
            SELECT 
                base.unit_key as kode_unit,
                $selectName,
                COALESCE(realiz.noa, 0) as noa_realisasi,
                COALESCE(realiz.total_plafond, 0) as total_realisasi,
                -- Formula: Run Off = Realisasi - (Saldo Akhir - Saldo Awal)
                (COALESCE(realiz.total_plafond, 0) - (COALESCE(base.akhir, 0) - COALESCE(base.awal, 0))) as total_run_off,
                (COALESCE(base.akhir, 0) - COALESCE(base.awal, 0)) as growth
            FROM (
                SELECT 
                    unit_key,
                    SUM(awal) as awal,
                    SUM(akhir) as akhir
                FROM (
                    -- Ambil Saldo Awal (Closing)
                    SELECT $colKey as unit_key, SUM(baki_debet) as awal, 0 as akhir 
                    FROM nominatif t WHERE created = :closing $filter GROUP BY 1
                    UNION ALL
                    -- Ambil Saldo Akhir (Harian)
                    SELECT $colKey as unit_key, 0 as awal, SUM(baki_debet) as akhir 
                    FROM nominatif t WHERE created = :harian $filter GROUP BY 1
                ) as tmp
                GROUP BY unit_key
            ) as base
            -- Join Data Realisasi Baru
            LEFT JOIN (
                SELECT $colKey as unit_key, COUNT(no_rekening) as noa, SUM(plafond) as total_plafond
                FROM nominatif t
                WHERE created = :harian_ref 
                AND tgl_realisasi > :closing_ref 
                AND tgl_realisasi <= :harian_ref2
                $filter
                GROUP BY 1
            ) as realiz ON base.unit_key = realiz.unit_key
            $joinTable
            ORDER BY base.unit_key ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing', $closing_date);
            $stmt->bindValue(':harian', $harian_date);
            $stmt->bindValue(':harian_ref', $harian_date);
            $stmt->bindValue(':closing_ref', $closing_date);
            $stmt->bindValue(':harian_ref2', $harian_date);

            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Hitung Grand Total
            $gt = ['noa_realisasi' => 0, 'total_realisasi' => 0, 'total_run_off' => 0, 'growth' => 0];
            foreach ($rows as $row) {
                $gt['noa_realisasi']   += (int)$row['noa_realisasi'];
                $gt['total_realisasi'] += (float)$row['total_realisasi'];
                $gt['total_run_off']   += (float)$row['total_run_off'];
                $gt['growth']          += (float)$row['growth'];
            }

            sendResponse(200, "Sukses", ['data' => $rows, 'grand_total' => $gt]);

        } catch (Exception $e) {
            sendResponse(500, "Error Database: " . $e->getMessage());
        }
    }

    /**
     * DETAIL REALISASI
     */
    public function getDetailRealisasiKredit($input = []) {
        $harian_date = $input['harian_date'] ?? date('Y-m-d');
        $awal_date   = date('Y-m-01', strtotime($harian_date));
        $kc          = $input['kode_kantor'] ?? null;
        $kankas      = $input['kode_kankas'] ?? null; 

        $where = "WHERE t1.created = :harian 
                  AND t1.tgl_realisasi BETWEEN :awal AND :akhir";
        
        $params = [
            ':harian' => $harian_date,
            ':awal'   => $awal_date,
            ':akhir'  => $harian_date
        ];

        if ($kc && $kc !== '000') {
            $where .= " AND t1.kode_cabang = :kc";
            $params[':kc'] = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        }

        if ($kankas) {
            $where .= " AND t1.kode_group1 = :kankas";
            $params[':kankas'] = $kankas;
        }

        $sql = "SELECT 
                    t1.no_rekening,
                    t1.nama_nasabah,
                    t1.plafond,
                    t1.baki_debet,
                    t1.tgl_realisasi,
                    t1.tgl_jatuh_tempo,
                    t1.kode_cabang,
                    COALESCE(k.deskripsi_group1, t1.kode_group1) as nama_kankas,
                    COALESCE(ao.nama_ao, t1.kode_group2) as nama_ao,
                    t1.alamat
                FROM nominatif t1
                LEFT JOIN kankas k ON t1.kode_group1 = k.kode_group1
                LEFT JOIN ao_kredit ao ON t1.kode_group2 = ao.kode_group2
                $where
                ORDER BY t1.tgl_realisasi DESC, t1.no_rekening ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Detail Realisasi", $data);
        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function getRealisasiSum($input = []) {
        // 1. Setup Input & Parsing Parameter
        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);
        
        $closing_date = $b['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $b['harian_date']  ?? date('Y-m-d');
        $kode_kantor  = $b['kode_kantor']  ?? null;

        // 2. Panggil Helper Filter (Gunakan prefix 't')
        $filterData = buildBankFilters($b, 't');
        $filterSql  = $filterData['sql'];
        
        // Penyesuaian nama kolom khusus tabel summary_kredit_harian_update
        $filterSql = str_replace('t.kode_cabang', 't.kode_kantor', $filterSql);
        $filterSql = str_replace('t.kode_group1', 't.kode_group_1', $filterSql);
        
        $paramsBind = $filterData['params'];

        // Masukkan tanggal ke binding parameter
        $paramsBind[':closing_date'] = $closing_date;
        $paramsBind[':harian_date']  = $harian_date;

        // 3. Tentukan Mode Tampilan (Konsolidasi vs Breakdown Kankas)
        $displayMode = 'KONSOLIDASI';
        if (!empty($kode_kantor) && $kode_kantor !== '000' && $kode_kantor !== 'ALL') {
            $displayMode = 'BREAKDOWN_KANKAS';
        }

        // 4. Bangun Query Utama Secara Dinamis
        if ($displayMode === 'BREAKDOWN_KANKAS') {
            // --- MODE CABANG: Breakdown Data per Kankas ---
            $sql = "
                SELECT 
                    COALESCE(NULLIF(t.kode_group_1, ''), CONCAT(t.kode_kantor, '000')) AS kode_kantor,
                    COALESCE(k.deskripsi_group1, CONCAT('KAS ', COALESCE(NULLIF(t.kode_group_1, ''), CONCAT(t.kode_kantor, '000')))) AS nama_kantor,
                    
                    SUM(COALESCE(t.noa_realisasi, 0)) AS noa_realisasi,
                    SUM(COALESCE(t.realisasi, 0)) AS total_realisasi,
                    
                    SUM(COALESCE(t.noa_restrukturisasi, 0)) AS noa_restruck,
                    SUM(COALESCE(t.restrukturisasi, 0)) AS total_restruck,
                    
                    SUM(COALESCE(t.pelunasan, 0)) AS total_pelunasan,
                    SUM(COALESCE(t.angsuran, 0) - COALESCE(t.pelunasan, 0)) AS angsuran_murni,
                    SUM(COALESCE(t.angsuran, 0)) AS total_run_off,
                    
                    (SUM(COALESCE(t.realisasi, 0)) + SUM(COALESCE(t.restrukturisasi, 0)) - SUM(COALESCE(t.angsuran, 0))) AS growth,
                    COALESCE(pc.portofolio_closing, 0) AS portofolio_closing,
                    COALESCE(ph.portofolio_harian, 0) AS portofolio_harian
                FROM summary_kredit_harian_update t
                LEFT JOIN kankas k ON k.kode_group1 = COALESCE(NULLIF(t.kode_group_1, ''), CONCAT(t.kode_kantor, '000'))
                LEFT JOIN (
                    SELECT kode_group1, SUM(COALESCE(baki_debet, 0)) AS portofolio_closing
                    FROM nominatif
                    WHERE created = :closing_date_porto
                      AND kode_cabang = :kode_kantor_porto_c
                    GROUP BY kode_group1
                ) pc ON pc.kode_group1 = COALESCE(NULLIF(t.kode_group_1, ''), CONCAT(t.kode_kantor, '000'))
                LEFT JOIN (
                    SELECT kode_group1, SUM(COALESCE(baki_debet, 0)) AS portofolio_harian
                    FROM nominatif
                    WHERE created = :harian_date_porto
                      AND kode_cabang = :kode_kantor_porto_h
                    GROUP BY kode_group1
                ) ph ON ph.kode_group1 = COALESCE(NULLIF(t.kode_group_1, ''), CONCAT(t.kode_kantor, '000'))
                WHERE t.created > :closing_date 
                  AND t.created <= :harian_date
                  {$filterSql}
                GROUP BY 1, 2, pc.portofolio_closing, ph.portofolio_harian
                ORDER BY 1 ASC
            ";
            $paramsBind[':closing_date_porto'] = $closing_date;
            $paramsBind[':harian_date_porto'] = $harian_date;
            $paramsBind[':kode_kantor_porto_c'] = str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT);
            $paramsBind[':kode_kantor_porto_h'] = str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT);
        } else {
            // --- MODE KONSOLIDASI: Breakdown Data per Cabang ---
            $sql = "
                SELECT 
                    t.kode_kantor,
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_kantor)) AS nama_kantor,
                    
                    SUM(COALESCE(t.noa_realisasi, 0)) AS noa_realisasi,
                    SUM(COALESCE(t.realisasi, 0)) AS total_realisasi,
                    
                    SUM(COALESCE(t.noa_restrukturisasi, 0)) AS noa_restruck,
                    SUM(COALESCE(t.restrukturisasi, 0)) AS total_restruck,
                    
                    SUM(COALESCE(t.pelunasan, 0)) AS total_pelunasan,
                    SUM(COALESCE(t.angsuran, 0) - COALESCE(t.pelunasan, 0)) AS angsuran_murni,
                    SUM(COALESCE(t.angsuran, 0)) AS total_run_off,
                    
                    (SUM(COALESCE(t.realisasi, 0)) + SUM(COALESCE(t.restrukturisasi, 0)) - SUM(COALESCE(t.angsuran, 0))) AS growth,
                    COALESCE(pc.portofolio_closing, 0) AS portofolio_closing,
                    COALESCE(ph.portofolio_harian, 0) AS portofolio_harian
                FROM summary_kredit_harian_update t
                LEFT JOIN kode_kantor k ON t.kode_kantor = k.kode_kantor
                LEFT JOIN (
                    SELECT kode_cabang, SUM(COALESCE(baki_debet, 0)) AS portofolio_closing
                    FROM nominatif
                    WHERE created = :closing_date_porto
                    GROUP BY kode_cabang
                ) pc ON pc.kode_cabang = t.kode_kantor
                LEFT JOIN (
                    SELECT kode_cabang, SUM(COALESCE(baki_debet, 0)) AS portofolio_harian
                    FROM nominatif
                    WHERE created = :harian_date_porto
                    GROUP BY kode_cabang
                ) ph ON ph.kode_cabang = t.kode_kantor
                WHERE t.created > :closing_date 
                  AND t.created <= :harian_date
                  {$filterSql}
                GROUP BY t.kode_kantor, k.nama_kantor, pc.portofolio_closing, ph.portofolio_harian
                ORDER BY t.kode_kantor ASC
            ";
            $paramsBind[':closing_date_porto'] = $closing_date;
            $paramsBind[':harian_date_porto'] = $harian_date;
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Eksekusi Bind Parameter Dinamis
            foreach ($paramsBind as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Setup & Kalkulasi Grand Total
            $grandTotal = [
                'kode_kantor'     => '', 
                'nama_kantor'     => 'TOTAL KONSOLIDASI',
                'noa_realisasi'   => 0, 
                'total_realisasi' => 0,
                'noa_restruck'    => 0,
                'total_restruck'  => 0,
                'pelunasan'       => 0,
                'angsuran_murni'  => 0,
                'total_run_off'   => 0, 
                'growth'          => 0,
                'portofolio_closing' => 0,
                'portofolio_harian'  => 0
            ];

            $cleanedRows = [];

            foreach ($rows as $row) {
                $noa_real       = (int) $row['noa_realisasi'];
                $tot_real       = (float) $row['total_realisasi'];
                $noa_res        = (int) $row['noa_restruck'];
                $tot_res        = (float) $row['total_restruck'];
                $pelunasan      = (float) $row['total_pelunasan']; 
                $angsuran_murni = (float) $row['angsuran_murni']; 
                $tot_run        = (float) $row['total_run_off'];
                $growth         = (float) $row['growth'];
                $portoClosing   = (float) $row['portofolio_closing'];
                $portoHarian    = (float) $row['portofolio_harian'];

                $cleanedRows[] = [
                    'kode_kantor'     => $row['kode_kantor'],
                    'nama_kantor'     => str_replace('Kc. ', '', $row['nama_kantor']),
                    'noa_realisasi'   => $noa_real,
                    'total_realisasi' => $tot_real,
                    'noa_restruck'    => $noa_res,
                    'total_restruck'  => $tot_res,
                    'pelunasan'       => $pelunasan,
                    'angsuran_murni'  => $angsuran_murni,
                    'total_run_off'   => $tot_run,
                    'growth'          => $growth,
                    'portofolio_closing' => $portoClosing,
                    'portofolio_harian'  => $portoHarian
                ];

                $grandTotal['noa_realisasi']   += $noa_real;
                $grandTotal['total_realisasi'] += $tot_real;
                $grandTotal['noa_restruck']    += $noa_res;
                $grandTotal['total_restruck']  += $tot_res;
                $grandTotal['pelunasan']       += $pelunasan;
                $grandTotal['angsuran_murni']  += $angsuran_murni;
                $grandTotal['total_run_off']   += $tot_run;
                $grandTotal['growth']          += $growth;
                $grandTotal['portofolio_closing'] += $portoClosing;
                $grandTotal['portofolio_harian']  += $portoHarian;
            }

            sendResponse(200, "Sukses Realisasi & Growth ($displayMode)", [
                'meta' => [
                    'mode'    => $displayMode,
                    'closing' => $closing_date,
                    'harian'  => $harian_date
                ],
                'data'        => $cleanedRows,
                'grand_total' => $grandTotal
            ]);

        } catch (Exception $e) {
            error_log("Error getRealisasiSum: " . $e->getMessage());
            sendResponse(500, "Error DB: " . $e->getMessage());
        }
    }


    public function getDetailRealisasiUpdate($input = []) {
        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);

        // 1. Ambil Parameter Dasar
        $closing_date = $b['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $b['harian_date']  ?? date('Y-m-d');
        $kode_trans   = $b['kode_trans']   ?? null; // 110 = Baru, 109 = Restruck

        $page   = isset($b['page']) ? (int)$b['page'] : 1;
        $limit  = isset($b['limit']) ? (int)$b['limit'] : 20; 
        $offset = ($page - 1) * $limit;

        // 2. Panggil Helper Filter (Prefix 't1')
        $filterData = buildBankFilters($b, 't1');
        $filterSql  = $filterData['sql'];
        
        // Penyesuaian nama kolom khusus tabel update_realisasi_kredit
        $filterSql  = str_replace('t1.kode_cabang', 't1.kode_kantor', $filterSql);
        $paramsBind = $filterData['params'];

        // 3. Base Where Clause untuk range tanggal
        $where = "WHERE t1.tanggal_realisasi > :closing AND t1.tanggal_realisasi <= :harian";
        $paramsBind[':closing'] = $closing_date;
        $paramsBind[':harian']  = $harian_date;

        // Filter Transaksi (Baru / Restruck)
        if ($kode_trans) {
            $where .= " AND t1.kode_trans = :kode_trans";
            $paramsBind[':kode_trans'] = $kode_trans;
        }

        try {
            // 4. Hitung Total Records untuk Pagination
            $sqlCount = "SELECT COUNT(*) FROM update_realisasi_kredit t1 $where {$filterSql}";
            $stmtCount = $this->pdo->prepare($sqlCount);
            foreach ($paramsBind as $k => $v) { $stmtCount->bindValue($k, $v); }
            $stmtCount->execute();
            $total_records = $stmtCount->fetchColumn();

            // 5. Query Utama Detail (Dengan handling Kankas Kosong)
            $sql = "
                SELECT 
                    t1.no_rekening,
                    t1.nama_nasabah,
                    t1.realisasi_pokok AS plafond,
                    t1.realisasi_pokok AS baki_debet,
                    t1.tanggal_realisasi AS tgl_realisasi,
                    NULL AS tgl_jatuh_tempo, 
                    t1.kode_kantor AS kode_cabang,
                    t1.kode_trans,
                    
                    COALESCE(k.deskripsi_group1, COALESCE(NULLIF(t1.kode_group1, ''), CONCAT(t1.kode_kantor, '000'))) AS nama_kankas,
                    COALESCE(ao.nama_ao, t1.kode_group2) AS nama_ao,
                    t1.alamat
                FROM update_realisasi_kredit t1
                LEFT JOIN kankas k ON k.kode_group1 = COALESCE(NULLIF(t1.kode_group1, ''), CONCAT(t1.kode_kantor, '000'))
                LEFT JOIN ao_kredit ao ON t1.kode_group2 = ao.kode_group2 AND t1.kode_kantor = ao.kode_kantor
                $where
                {$filterSql}
                ORDER BY t1.tanggal_realisasi DESC, t1.no_rekening ASC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($sql);
            
            // Bind Semua Parameter
            foreach ($paramsBind as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tipe data casting nominal
            foreach ($data as &$row) {
                $row['plafond']    = (float) $row['plafond'];
                $row['baki_debet'] = (float) $row['baki_debet'];
            }
            unset($row);

            sendResponse(200, "Detail Realisasi Update Sukses", [
                'total_records' => $total_records,
                'total_pages'   => ceil($total_records / $limit),
                'current_page'  => $page,
                'data'          => $data
            ]);

        } catch (Exception $e) {
            error_log("Error getDetailRealisasiUpdate: " . $e->getMessage());
            sendResponse(500, "Error DB: " . $e->getMessage());
        }
    }


    // ===================================================================
    // 1. FUNGSI UNTUK MENDAPATKAN LIST PROMO (Untuk Dropdown FE)
    // ===================================================================
    public function getListPromo() {
        // Ambil data promo unik yang pernah terpakai di tabel realisasi
        // (Bisa juga di-JOIN ke tabel master promo jika ada)
        $sql = "SELECT DISTINCT kode_promo 
                FROM update_realisasi_kredit 
                WHERE kode_promo IS NOT NULL AND kode_promo != ''
                ORDER BY kode_promo ASC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, "List Promo", $data);
        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    // ===================================================================
    // 2. FUNGSI REKAP PROMO (Dengan Filter Tanggal)
    // ===================================================================
    public function getRekapPromo($input = []) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        $kode_promo   = $input['kode_promo'] ?? '';
        $kc           = $input['kode_kantor'] ?? null;
        
        if ($kc === '000' || $kc === '') $kc = null;

        // Wajib filter tanggal dan pastikan kode_promo tidak kosong
        $filterClause = "WHERE t.kode_promo IS NOT NULL AND t.kode_promo != '' 
                         AND t.tanggal_realisasi > :closing_date 
                         AND t.tanggal_realisasi <= :harian_date";
        
        $params = [
            ':closing_date' => $closing_date,
            ':harian_date'  => $harian_date
        ];

        if (!empty($kode_promo)) {
            $filterClause .= " AND t.kode_promo = :promo";
            $params[':promo'] = $kode_promo;
        }

        if ($kc) {
            $filterClause .= " AND t.kode_kantor = :kc";
            $params[':kc'] = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        }

        $sql = "
            SELECT 
                t.kode_kantor,
                COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_kantor)) AS nama_kantor,
                COUNT(DISTINCT t.no_rekening) AS noa,
                SUM(COALESCE(t.realisasi_pokok, 0)) AS nominal
            FROM update_realisasi_kredit t
            LEFT JOIN kode_kantor k ON t.kode_kantor = k.kode_kantor
            $filterClause
            GROUP BY t.kode_kantor, k.nama_kantor
            ORDER BY t.kode_kantor ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grandTotal = [
                'kode_kantor' => '', 
                'nama_kantor' => 'TOTAL KONSOLIDASI',
                'noa'         => 0, 
                'nominal'     => 0
            ];

            $cleanedRows = [];
            foreach ($rows as $row) {
                $noa = (int) $row['noa'];
                $nom = (float) $row['nominal'];

                $cleanedRows[] = [
                    'kode_kantor' => $row['kode_kantor'],
                    'nama_kantor' => $row['nama_kantor'],
                    'noa'         => $noa,
                    'nominal'     => $nom
                ];

                $grandTotal['noa']     += $noa;
                $grandTotal['nominal'] += $nom;
            }

            sendResponse(200, "Sukses Rekap Promo", [
                'meta' => [
                    'promo_dipilih' => $kode_promo ?: 'SEMUA PROMO',
                    'closing'       => $closing_date,
                    'harian'        => $harian_date
                ],
                'data' => $cleanedRows,
                'grand_total' => $grandTotal
            ]);

        } catch (Exception $e) {
            error_log("Error getRekapPromo: " . $e->getMessage());
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    // ===================================================================
    // 3. FUNGSI DETAIL PROMO (Dengan Filter Tanggal, Kankas, dan AO)
    // ===================================================================
    public function getDetailPromo($input = []) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        $kode_promo   = $input['kode_promo'] ?? '';
        
        $kc           = $input['kode_kantor'] ?? null;
        $kankas       = $input['kode_kankas'] ?? null; 
        $kode_ao      = $input['kode_ao'] ?? null; // 🔥 TAMBAHAN: Tangkap Filter AO

        if ($kc === '000' || $kc === '') $kc = null;

        $filterClause = "WHERE t.kode_promo IS NOT NULL AND t.kode_promo != '' 
                         AND t.tanggal_realisasi > :closing_date 
                         AND t.tanggal_realisasi <= :harian_date";
                         
        $params = [
            ':closing_date' => $closing_date,
            ':harian_date'  => $harian_date
        ];

        if (!empty($kode_promo)) {
            $filterClause .= " AND t.kode_promo = :promo";
            $params[':promo'] = $kode_promo;
        }

        if ($kc) {
            $filterClause .= " AND t.kode_kantor = :kc";
            $params[':kc'] = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        }

        // Filter Kankas
        if ($kankas) {
            $filterClause .= " AND t.kode_group1 = :kankas";
            $params[':kankas'] = $kankas;
        }

        // 🔥 TAMBAHAN: Filter AO (Biar dropdown AO di FE jalan)
        if ($kode_ao) {
            $filterClause .= " AND t.kode_group2 = :kode_ao";
            $params[':kode_ao'] = $kode_ao;
        }

        $sql = "SELECT 
                    t.no_rekening,
                    t.nama_nasabah,
                    t.realisasi_pokok AS plafond,
                    t.tanggal_realisasi,
                    t.kode_promo,
                    t.kode_kantor AS kode_cabang,
                    COALESCE(k.deskripsi_group1, t.kode_group1) AS nama_kankas,
                    COALESCE(ao.nama_ao, t.kode_group2) AS nama_ao,
                    t.alamat
                FROM update_realisasi_kredit t
                LEFT JOIN kankas k ON t.kode_group1 = k.kode_group1
                LEFT JOIN ao_kredit ao ON t.kode_group2 = ao.kode_group2
                $filterClause
                ORDER BY t.tanggal_realisasi DESC, t.no_rekening ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Detail Promo Sukses", $data);
        } catch (Exception $e) {
            error_log("Error getDetailPromo: " . $e->getMessage());
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }


    public function getChartPromo($input = []) {
        // 🔥 FIX 1: Closing Date BEBAS diubah, tapi default-nya 2026-02-23
        $closing_date = $input['closing_date'] ?? '2026-02-23'; 
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        // Hardcode kode promo sesuai permintaan
        $promo_code   = '000011'; 

        // 1. Panggil Helper Filter Korwil & Cabang
        $filterData    = $this->buildFilterQuery($input, 't');
        $sqlWilayah    = $filterData['sql'];
        $paramsWilayah = $filterData['params'];

        // 2. Gabungkan klausa WHERE
        $filterClause = "WHERE t.tanggal_realisasi > :closing_date 
                           AND t.tanggal_realisasi <= :harian_date 
                           {$sqlWilayah}";
                           
        // 3. Setup Parameter Bawaan
        $params = [
            ':closing_date' => $closing_date,
            ':harian_date'  => $harian_date,
            ':promo1'       => $promo_code,
            ':promo2'       => $promo_code,
            ':promo3'       => $promo_code,
            ':promo4'       => $promo_code
        ];

        // 4. Merge parameter
        $params = array_merge($params, $paramsWilayah);

        // 🔥 FIX 2: GROUP BY YEARWEEK (Jadikan mingguan) 
        // Mengambil MIN() dan MAX() tanggal realisasi di minggu tersebut untuk label chart
        $sql = "
            SELECT 
                MIN(t.tanggal_realisasi) as start_date,
                MAX(t.tanggal_realisasi) as end_date,
                SUM(CASE WHEN t.kode_promo = :promo1 THEN COALESCE(t.realisasi_pokok, 0) ELSE 0 END) as promo_nominal,
                SUM(CASE WHEN t.kode_promo != :promo2 OR t.kode_promo IS NULL THEN COALESCE(t.realisasi_pokok, 0) ELSE 0 END) as non_promo_nominal,
                COUNT(DISTINCT CASE WHEN t.kode_promo = :promo3 THEN t.no_rekening END) as promo_noa,
                COUNT(DISTINCT CASE WHEN t.kode_promo != :promo4 OR t.kode_promo IS NULL THEN t.no_rekening END) as non_promo_noa
            FROM update_realisasi_kredit t
            $filterClause
            GROUP BY YEARWEEK(t.tanggal_realisasi, 1)
            ORDER BY MIN(t.tanggal_realisasi) ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
            $stmt->execute();
            $weeklyRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Variabel penyimpan Total keseluruhan
            $totals = [
                'promo_nominal' => 0, 'promo_noa' => 0,
                'non_promo_nominal' => 0, 'non_promo_noa' => 0
            ];

            $chartData = [];
            foreach ($weeklyRows as $r) {
                $p_nom  = (float)$r['promo_nominal'];
                $np_nom = (float)$r['non_promo_nominal'];
                $p_noa  = (int)$r['promo_noa'];
                $np_noa = (int)$r['non_promo_noa'];

                // 🔥 FORMAT LABEL TANGGAL: "02 Mar - 08 Mar"
                $start = date('d M', strtotime($r['start_date']));
                $end   = date('d M', strtotime($r['end_date']));
                $label = ($start === $end) ? $start : "$start - $end";

                $chartData[] = [
                    'tanggal'           => $label, // Label yang akan muncul di Chart FE
                    'promo_nominal'     => $p_nom,
                    'non_promo_nominal' => $np_nom,
                    'promo_noa'         => $p_noa,
                    'non_promo_noa'     => $np_noa
                ];

                // Akumulasi Total
                $totals['promo_nominal']     += $p_nom;
                $totals['promo_noa']         += $p_noa;
                $totals['non_promo_nominal'] += $np_nom;
                $totals['non_promo_noa']     += $np_noa;
            }

            // Kembalikan Response ke FE
            sendResponse(200, "Sukses Chart Promo", [
                'meta' => [
                    'closing' => $closing_date,
                    'harian'  => $harian_date,
                    'filter'  => $input['korwil'] ?? $input['kode_kantor'] ?? 'KONSOLIDASI'
                ],
                'totals' => $totals,
                'trend'  => $chartData
            ]);

        } catch (Exception $e) {
            error_log("Error getChartPromo: " . $e->getMessage());
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }
    
    public function getMigrasiKolek($input) {
        $closing_date = !empty($input['closing_date']) ? $input['closing_date'] : date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = !empty($input['harian_date'])  ? $input['harian_date']  : date('Y-m-d');
        
        // 1. Ambil Input Kode Kantor (Prioritas Utama)
        $kode_kantor  = !empty($input['kode_kantor'])  ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        
        // 2. Ambil Input Korwil (Prioritas Kedua)
        $korwil_input = !empty($input['korwil']) ? strtoupper($input['korwil']) : null;
        $kw_start = null;
        $kw_end   = null;

        // Mapping Kode Korwil
        if (!$kode_kantor && $korwil_input) {
            switch ($korwil_input) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
        }

        // 3. Siapkan String Filter SQL (Pilih salah satu: Cabang atau Korwil)
        $filter_cabang_closing = "";
        $filter_cabang_harian  = "";

        if ($kode_kantor) {
            // Filter Cabang Spesifik
            $filter_cabang_closing = " AND c.kode_cabang = :kode_kantor_c ";
            $filter_cabang_harian  = " AND h.kode_cabang = :kode_kantor_h ";
        } elseif ($kw_start && $kw_end) {
            // Filter Range Korwil
            $filter_cabang_closing = " AND c.kode_cabang BETWEEN :kw_start_c AND :kw_end_c ";
            $filter_cabang_harian  = " AND h.kode_cabang BETWEEN :kw_start_h AND :kw_end_h ";
        }

        $sql = "
            WITH 
            closing AS (
                SELECT c.no_rekening, c.kode_cabang, c.kolektibilitas AS kolek_closed, c.baki_debet AS baki_closed
                FROM nominatif c
                WHERE c.created = :closing_date_m
                AND c.kolektibilitas IN ('L','DP','KL','D','M')
                $filter_cabang_closing
            ),
            harian AS (
                SELECT h.no_rekening, h.kode_cabang, h.kolektibilitas AS kolek_update, h.baki_debet AS baki_harian
                FROM nominatif h
                WHERE h.created = :harian_date_m
                AND h.kolektibilitas IN ('L','DP','KL','D','M')
                $filter_cabang_harian
            ),
            gabung AS (
                SELECT 
                    c.no_rekening,
                    c.kolek_closed,
                    h.kolek_update,
                    c.baki_closed,
                    COALESCE(h.baki_harian, 0) AS baki_harian,
                    (c.baki_closed - COALESCE(h.baki_harian, 0)) AS pembayaran,
                    CASE WHEN h.no_rekening IS NULL THEN 1 ELSE 0 END AS is_lunas
                FROM closing c
                LEFT JOIN harian h ON h.no_rekening = c.no_rekening
            )
            /* ===== Per-kolek: + run_off_asli (lunas_osc - pembayaran) ===== */
            SELECT 
                g.kolek_closed,
                SUM(g.baki_closed) AS saldo_closed,
                SUM(CASE WHEN g.kolek_update = 'L'  THEN g.baki_harian ELSE 0 END) AS migrasi_L,
                SUM(CASE WHEN g.kolek_update = 'DP' THEN g.baki_harian ELSE 0 END) AS migrasi_DP,
                SUM(CASE WHEN g.kolek_update = 'KL' THEN g.baki_harian ELSE 0 END) AS migrasi_KL,
                SUM(CASE WHEN g.kolek_update = 'D'  THEN g.baki_harian ELSE 0 END) AS migrasi_D,
                SUM(CASE WHEN g.kolek_update = 'M'  THEN g.baki_harian ELSE 0 END) AS migrasi_M,
                SUM(g.pembayaran) AS pembayaran,
                SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) AS lunas_osc,
                (SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) - SUM(g.pembayaran)) AS run_off_asli
            FROM gabung g
            GROUP BY g.kolek_closed

            UNION ALL

            /* ===== Baris TOTAL ===== */
            SELECT 
                'TOTAL' AS kolek_closed,
                SUM(g.baki_closed) AS saldo_closed,
                SUM(CASE WHEN g.kolek_update = 'L'  THEN g.baki_harian ELSE 0 END) AS migrasi_L,
                SUM(CASE WHEN g.kolek_update = 'DP' THEN g.baki_harian ELSE 0 END) AS migrasi_DP,
                SUM(CASE WHEN g.kolek_update = 'KL' THEN g.baki_harian ELSE 0 END) AS migrasi_KL,
                SUM(CASE WHEN g.kolek_update = 'D'  THEN g.baki_harian ELSE 0 END) AS migrasi_D,
                SUM(CASE WHEN g.kolek_update = 'M'  THEN g.baki_harian ELSE 0 END) AS migrasi_M,
                SUM(g.pembayaran) AS pembayaran,
                SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) AS lunas_osc,
                (SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) - SUM(g.pembayaran)) AS run_off_asli
            FROM gabung g

            ORDER BY 
                CASE 
                    WHEN kolek_closed = 'L'     THEN 1
                    WHEN kolek_closed = 'DP'    THEN 2
                    WHEN kolek_closed = 'KL'    THEN 3
                    WHEN kolek_closed = 'D'     THEN 4
                    WHEN kolek_closed = 'M'     THEN 5
                    WHEN kolek_closed = 'TOTAL' THEN 99
                    ELSE 98
                END
        ";

        try {
            // 1) Eksekusi migrasi
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing_date_m', $closing_date);
            $stmt->bindValue(':harian_date_m',  $harian_date);
            
            if ($kode_kantor) {
                $stmt->bindValue(':kode_kantor_c', $kode_kantor);
                $stmt->bindValue(':kode_kantor_h', $kode_kantor);
            } elseif ($kw_start && $kw_end) {
                // Bind range korwil
                $stmt->bindValue(':kw_start_c', $kw_start);
                $stmt->bindValue(':kw_end_c',   $kw_end);
                $stmt->bindValue(':kw_start_h', $kw_start);
                $stmt->bindValue(':kw_end_h',   $kw_end);
            }

            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // cari indeks TOTAL + pembayaran_total
            $pembayaran_total = 0.0; $totalIdx = null;
            foreach ($rows as $i => $r) {
                if (($r['kolek_closed'] ?? '') === 'TOTAL') {
                    $totalIdx = $i;
                    $pembayaran_total = (float)($r['pembayaran'] ?? 0);
                    break;
                }
            }

            // ================== HITUNG METRIK TOTAL SAJA ==================
            $backflow_total = 0.0; 
            $lunas_npl      = 0.0; 
            $angsuran_npl   = 0.0; 
            $flow_par       = 0.0; 

            foreach ($rows as $r) {
                $kc = $r['kolek_closed'] ?? '';
                if ($kc === 'TOTAL') continue;

                $migrasiL   = (float)($r['migrasi_L']  ?? 0);
                $migrasiDP  = (float)($r['migrasi_DP'] ?? 0);
                $migrasiKL  = (float)($r['migrasi_KL'] ?? 0);
                $migrasiD   = (float)($r['migrasi_D']  ?? 0);
                $migrasiM   = (float)($r['migrasi_M']  ?? 0);
                $bayar      = (float)($r['pembayaran'] ?? 0);
                $lunas      = (float)($r['lunas_osc']  ?? 0);

                if ($kc === 'KL' || $kc === 'D' || $kc === 'M') {
                    $backflow_total += ($migrasiL + $migrasiDP);
                    $lunas_npl     += $lunas;
                    $angsuran_npl  += max(0.0, $bayar - $lunas);
                }

                if ($kc === 'L' || $kc === 'DP') {
                    $flow_par += ($migrasiKL + $migrasiD + $migrasiM);
                }
            }

            // ================== META: %NPL, REALISASI, GROWTH (Filter Korwil Applied) ==================
            $awal_bulan = date('Y-m-01', strtotime($harian_date));
            
            // Siapkan filter untuk query META
            $filter_np = ""; $filter_nn = ""; $filter_tp = ""; $filter_tn = ""; $filter_rl = "";
            
            if ($kode_kantor) {
                $filter_np = " AND kode_cabang = :kode_np ";
                $filter_nn = " AND kode_cabang = :kode_nn ";
                $filter_tp = " AND kode_cabang = :kode_tp ";
                $filter_tn = " AND kode_cabang = :kode_tn ";
                $filter_rl = " AND kode_cabang = :kode_rl ";
            } elseif ($kw_start && $kw_end) {
                $filter_np = " AND kode_cabang BETWEEN :kw_start_np AND :kw_end_np ";
                $filter_nn = " AND kode_cabang BETWEEN :kw_start_nn AND :kw_end_nn ";
                $filter_tp = " AND kode_cabang BETWEEN :kw_start_tp AND :kw_end_tp ";
                $filter_tn = " AND kode_cabang BETWEEN :kw_start_tn AND :kw_end_tn ";
                $filter_rl = " AND kode_cabang BETWEEN :kw_start_rl AND :kw_end_rl ";
            }

            $sqlMeta = "
                SELECT
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :closing_date_np
                    AND kolektibilitas IN ('KL','D','M') $filter_np) AS npl_prev,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_nn
                    AND kolektibilitas IN ('KL','D','M') $filter_nn) AS npl_now,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :closing_date_tp
                    AND kolektibilitas IN ('L','DP','KL','D','M') $filter_tp) AS total_prev,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_tn
                    AND kolektibilitas IN ('L','DP','KL','D','M') $filter_tn) AS total_now,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_rl1
                    AND tgl_realisasi >= :awal_bulan
                    AND tgl_realisasi <= :harian_date_rl2 $filter_rl) AS realisasi_bulan_ini
            ";

            $st = $this->pdo->prepare($sqlMeta);
            // Bind tanggal
            $st->bindValue(':closing_date_np', $closing_date);
            $st->bindValue(':harian_date_nn',  $harian_date);
            $st->bindValue(':closing_date_tp', $closing_date);
            $st->bindValue(':harian_date_tn',  $harian_date);
            $st->bindValue(':harian_date_rl1', $harian_date);
            $st->bindValue(':harian_date_rl2', $harian_date);
            $st->bindValue(':awal_bulan',      $awal_bulan);

            // Bind Cabang atau Korwil
            if ($kode_kantor) {
                $st->bindValue(':kode_np', $kode_kantor);
                $st->bindValue(':kode_nn', $kode_kantor);
                $st->bindValue(':kode_tp', $kode_kantor);
                $st->bindValue(':kode_tn', $kode_kantor);
                $st->bindValue(':kode_rl', $kode_kantor);
            } elseif ($kw_start && $kw_end) {
                // Kita bind berulang untuk tiap subquery agar aman
                $st->bindValue(':kw_start_np', $kw_start); $st->bindValue(':kw_end_np', $kw_end);
                $st->bindValue(':kw_start_nn', $kw_start); $st->bindValue(':kw_end_nn', $kw_end);
                $st->bindValue(':kw_start_tp', $kw_start); $st->bindValue(':kw_end_tp', $kw_end);
                $st->bindValue(':kw_start_tn', $kw_start); $st->bindValue(':kw_end_tn', $kw_end);
                $st->bindValue(':kw_start_rl', $kw_start); $st->bindValue(':kw_end_rl', $kw_end);
            }

            $st->execute();
            $meta = $st->fetch(PDO::FETCH_ASSOC) ?: [
                'npl_prev'=>0,'npl_now'=>0,'total_prev'=>0,'total_now'=>0,'realisasi_bulan_ini'=>0
            ];

            $npl_prev = (float)$meta['npl_prev'];
            $npl_now  = (float)$meta['npl_now'];
            $tot_prev = (float)$meta['total_prev'];
            $tot_now  = (float)$meta['total_now'];
            $realisasi= (float)$meta['realisasi_bulan_ini'];

            $npl_prev_pct = $tot_prev > 0 ? round($npl_prev * 100.0 / $tot_prev, 2) : 0.0;
            $npl_now_pct  = $tot_now  > 0 ? round($npl_now  * 100.0 / $tot_now , 2) : 0.0;
            $npl_delta_pct= round($npl_now_pct - $npl_prev_pct, 2);
            $growth       = $realisasi - $pembayaran_total;

            if ($totalIdx !== null) {
                $rows[$totalIdx]['realisasi_bulan_ini'] = $realisasi;
                $rows[$totalIdx]['npl_prev']            = $npl_prev;
                $rows[$totalIdx]['npl_now']             = $npl_now;
                $rows[$totalIdx]['total_prev']          = $tot_prev;
                $rows[$totalIdx]['total_now']           = $tot_now;
                $rows[$totalIdx]['npl_prev_pct']        = $npl_prev_pct;
                $rows[$totalIdx]['npl_now_pct']         = $npl_now_pct;
                $rows[$totalIdx]['npl_delta_pct']       = $npl_delta_pct;
                $rows[$totalIdx]['growth']              = $growth;
                $rows[$totalIdx]['pembayaran_total']    = $pembayaran_total;

                $rows[$totalIdx]['backflow_total']      = $backflow_total;
                $rows[$totalIdx]['lunas_npl']           = $lunas_npl;
                $rows[$totalIdx]['angsuran_npl']        = $angsuran_npl;
                $rows[$totalIdx]['flow_par']            = $flow_par;
            }

            sendResponse(200, "Berhasil ambil data migrasi kolektibilitas", $rows);

        } catch (PDOException $e) {
            sendResponse(500, "PDO Error: " . $e->getMessage(), null);
        }
    }

    public function getKolek($input) {
        // 1. Ambil Parameter
        $harian_date = isset($input['harian_date']) ? $input['harian_date'] : date('Y-m-d');
        $kc          = isset($input['kode_kantor']) ? $input['kode_kantor'] : null;
        $korwil      = strtoupper(trim($input['korwil'] ?? ''));
        
        // --- LOGIK TAMBAHAN: Pilih Kolom Nominal ---
        $modeHitung = $input['hitung_berdasarkan'] ?? 'baki_debet';
        $colValue   = ($modeHitung === 'saldo_bank') ? 'saldo_bank' : 'baki_debet';

        // Normalisasi: '000' dianggap null (Pusat)
        if ($kc === '000' || $kc === '') $kc = null;

        // 2. Logic Switch Query (Pusat vs Cabang)
        if ($kc) {
            // === MODE DETAIL PER KANKAS (User Cabang) ===
            $colKey       = "kode_group1"; 
            $selectName   = "COALESCE(k.deskripsi_group1, CONCAT('KAS ', d.kode_key))";
            $joinTable    = "LEFT JOIN kankas k ON d.kode_key = k.kode_group1";
            
            $filterClause = "AND kode_cabang = :kc"; 
            $kc_val       = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        } else {
            // === MODE KONSOLIDASI (User Pusat) ===
            $colKey       = "kode_cabang";
            $selectName   = "k.nama_kantor";
            $joinTable    = "LEFT JOIN kode_kantor k ON d.kode_key = k.kode_kantor";
            
            $filterClause = ""; 
            if ($korwil === 'SEMARANG') {
                $filterClause = "AND kode_cabang BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterClause = "AND kode_cabang BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterClause = "AND kode_cabang BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterClause = "AND kode_cabang BETWEEN '022' AND '028'";
            }
            $kc_val       = null;
        }

        $sql = "
            WITH data_harian AS (
                SELECT 
                    $colKey as kode_key,
                    kolektibilitas,
                    $colValue as nilai_nominal
                FROM nominatif
                WHERE created = :harian_date
                $filterClause
            ),
            
            agregat AS (
                SELECT
                    d.kode_key,
                    $selectName as nama_unit,

                    -- LANCAR (L)
                    COUNT(CASE WHEN d.kolektibilitas = 'L' THEN 1 END) AS noa_L,
                    SUM(CASE WHEN d.kolektibilitas = 'L' THEN d.nilai_nominal ELSE 0 END) AS bd_L,

                    -- DALAM PERHATIAN KHUSUS (DP)
                    COUNT(CASE WHEN d.kolektibilitas = 'DP' THEN 1 END) AS noa_DP,
                    SUM(CASE WHEN d.kolektibilitas = 'DP' THEN d.nilai_nominal ELSE 0 END) AS bd_DP,

                    -- KURANG LANCAR (KL)
                    COUNT(CASE WHEN d.kolektibilitas = 'KL' THEN 1 END) AS noa_KL,
                    SUM(CASE WHEN d.kolektibilitas = 'KL' THEN d.nilai_nominal ELSE 0 END) AS bd_KL,

                    -- DIRAGUKAN (D)
                    COUNT(CASE WHEN d.kolektibilitas = 'D' THEN 1 END) AS noa_D,
                    SUM(CASE WHEN d.kolektibilitas = 'D' THEN d.nilai_nominal ELSE 0 END) AS bd_D,

                    -- MACET (M)
                    COUNT(CASE WHEN d.kolektibilitas = 'M' THEN 1 END) AS noa_M,
                    SUM(CASE WHEN d.kolektibilitas = 'M' THEN d.nilai_nominal ELSE 0 END) AS bd_M,

                    -- TOTAL PORTOFOLIO
                    COUNT(*) AS total_noa,
                    SUM(d.nilai_nominal) AS total_bd,

                    -- TOTAL NPL (KL + D + M)
                    SUM(CASE WHEN d.kolektibilitas IN ('KL', 'D', 'M') THEN 1 ELSE 0 END) AS noa_npl,
                    SUM(CASE WHEN d.kolektibilitas IN ('KL', 'D', 'M') THEN d.nilai_nominal ELSE 0 END) AS bd_npl

                FROM data_harian d
                $joinTable
                GROUP BY d.kode_key, $selectName
            )

            SELECT
                kode_key as kode_unit,
                nama_unit,
                noa_L, bd_L,
                noa_DP, bd_DP,
                noa_KL, bd_KL,
                noa_D, bd_D,
                noa_M, bd_M,
                total_noa,
                total_bd,
                noa_npl,
                bd_npl,
                ROUND(CASE WHEN total_bd = 0 THEN 0 ELSE (bd_npl * 100.0) / total_bd END, 2) AS persentase_npl
            FROM agregat

            UNION ALL

            SELECT
                '' as kode_unit,
                'TOTAL KONSOLIDASI' as nama_unit,
                SUM(noa_L), SUM(bd_L),
                SUM(noa_DP), SUM(bd_DP),
                SUM(noa_KL), SUM(bd_KL),
                SUM(noa_D), SUM(bd_D),
                SUM(noa_M), SUM(bd_M),
                SUM(total_noa),
                SUM(total_bd),
                SUM(noa_npl),
                SUM(bd_npl),
                ROUND(CASE WHEN SUM(total_bd) = 0 THEN 0 ELSE (SUM(bd_npl) * 100.0) / SUM(total_bd) END, 2)
            FROM agregat

            ORDER BY 
                CASE WHEN nama_unit = 'TOTAL KONSOLIDASI' THEN 1 ELSE 0 END,
                kode_unit ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':harian_date', $harian_date);
            
            if ($kc_val) {
                $stmt->bindValue(':kc', $kc_val);
            }

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grandTotal = array_pop($data);
            if (!$grandTotal) {
                $grandTotal = [
                    'kode_unit' => '', 'nama_unit' => 'TOTAL KONSOLIDASI',
                    'noa_L'=>0, 'bd_L'=>0, 'noa_DP'=>0, 'bd_DP'=>0, 
                    'noa_KL'=>0, 'bd_KL'=>0, 'noa_D'=>0, 'bd_D'=>0, 'noa_M'=>0, 'bd_M'=>0,
                    'total_noa'=>0, 'total_bd'=>0, 'noa_npl'=>0, 'bd_npl'=>0, 'persentase_npl'=>0
                ];
            }

            sendResponse(200, "Berhasil menghitung berdasarkan $colValue", [
                'data' => $data,
                'grand_total' => $grandTotal
            ]);

        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function getTopRealisasi($input = [])
    {
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);

        $harian_date = $b['harian_date'] ?? date('Y-m-d');

        // Default closing_date = last day of previous month dari harian_date
        $closing_date = $b['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month', strtotime($harian_date)));

        $page  = isset($b['page']) ? max(1, (int)$b['page']) : 1;
        $limit = isset($b['limit']) ? max(1, (int)$b['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $kode_kantor = $b['kode_kantor'] ?? null;
        $kode_ao     = $b['kode_ao'] ?? null;
        $korwil      = strtoupper(trim($b['korwil'] ?? ''));

        $kode_kantor_norm = $kode_kantor ? str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT) : null;
        $is_konsolidasi = empty($kode_kantor) || $kode_kantor === '000' || strtolower((string)$kode_kantor) === 'konsolidasi';
        $korwilRanges = [
            'SEMARANG' => ['001', '007'],
            'SOLO' => ['008', '014'],
            'BANYUMAS' => ['015', '021'],
            'PEKALONGAN' => ['022', '028'],
        ];
        $korwilRange = $korwilRanges[$korwil] ?? null;

        /*
        * Bulan berjalan:
        * - Kalau harian_date masih bulan berjalan, AO yang tampil hanya status = 1.
        * - Kalau tarik history / bulan lampau, semua AO boleh tampil.
        */
        $is_bulan_berjalan = date('Y-m', strtotime($harian_date)) === date('Y-m');

        try {
            /*
            * =========================
            * FILTER MASTER AO
            * =========================
            * Basis query dari ao_kredit supaya AO yang belum realisasi tetap muncul.
            */
            $aoWhere = [];
            $aoParams = [];

            if (!$is_konsolidasi) {
                $aoWhere[] = "LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = :ao_kode_kantor";
                $aoParams[':ao_kode_kantor'] = $kode_kantor_norm;
            } elseif ($korwilRange) {
                $aoWhere[] = "LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') BETWEEN :ao_korwil_awal AND :ao_korwil_akhir";
                $aoParams[':ao_korwil_awal'] = $korwilRange[0];
                $aoParams[':ao_korwil_akhir'] = $korwilRange[1];
            }

            if (!empty($kode_ao) && strtoupper((string)$kode_ao) !== 'ALL') {
                $aoWhere[] = "ao.kode_group2 = :ao_kode_ao";
                $aoParams[':ao_kode_ao'] = $kode_ao;
            }

            if ($is_bulan_berjalan) {
                $aoWhere[] = "ao.status = 1";
            }

            $aoWhereSql = '';
            if (!empty($aoWhere)) {
                $aoWhereSql = " WHERE " . implode(" AND ", $aoWhere);
            }

            /*
            * =========================
            * FILTER TRANSAKSI REALISASI
            * =========================
            * Hanya kode_trans = 110.
            */
            $trxWhere = [];
            $trxParams = [];

            $trxWhere[] = "t1.tanggal_realisasi > :closing_date";
            $trxWhere[] = "t1.tanggal_realisasi <= :harian_date";
            $trxWhere[] = "t1.kode_trans = 110";

            if (!$is_konsolidasi) {
                $trxWhere[] = "LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') = :trx_kode_kantor";
                $trxParams[':trx_kode_kantor'] = $kode_kantor_norm;
            } elseif ($korwilRange) {
                $trxWhere[] = "LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') BETWEEN :trx_korwil_awal AND :trx_korwil_akhir";
                $trxParams[':trx_korwil_awal'] = $korwilRange[0];
                $trxParams[':trx_korwil_akhir'] = $korwilRange[1];
            }

            if (!empty($kode_ao) && strtoupper((string)$kode_ao) !== 'ALL') {
                $trxWhere[] = "COALESCE(n.kode_group2, t1.kode_group2) = :trx_kode_ao";
                $trxParams[':trx_kode_ao'] = $kode_ao;
            }

            $trxWhereSql = implode(" AND ", $trxWhere);

            /*
            * =========================
            * COUNT TOTAL AO
            * =========================
            */
            $sqlCount = "
                SELECT COUNT(*) AS total
                FROM ao_kredit ao
                LEFT JOIN kode_kantor kk
                    ON LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = kk.kode_kantor
                {$aoWhereSql}
            ";

            $stmtCount = $this->pdo->prepare($sqlCount);

            foreach ($aoParams as $key => $val) {
                $stmtCount->bindValue($key, $val);
            }

            $stmtCount->execute();
            $totalData = (int)($stmtCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            /*
            * =========================
            * QUERY UTAMA REKAP AO
            * =========================
            * Catatan:
            * - Alias subquery pakai rls, bukan real, supaya aman di MariaDB.
            * - AO belum realisasi tetap muncul karena FROM utama dari ao_kredit.
            */
            $sql = "
                SELECT
                    LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') AS kode_kantor,
                    COALESCE(kk.nama_kantor, LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0')) AS nama_kantor,

                    ao.kode_group2 AS kode_ao,
                    COALESCE(ao.nama_ao, ao.kode_group2) AS nama_ao,

                    COALESCE(rls.total_noa, 0) AS total_noa,
                    COALESCE(rls.total_realisasi, 0) AS total_realisasi
                FROM ao_kredit ao
                LEFT JOIN kode_kantor kk
                    ON LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = kk.kode_kantor
                LEFT JOIN (
                    SELECT
                        LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') AS kode_kantor,
                        COALESCE(n.kode_group2, t1.kode_group2) AS kode_ao,
                        COUNT(DISTINCT t1.no_rekening) AS total_noa,
                        COALESCE(SUM(t1.realisasi_pokok), 0) AS total_realisasi
                    FROM update_realisasi_kredit t1
                    LEFT JOIN nominatif n
                        ON t1.no_rekening = n.no_rekening
                        AND DATE(n.created) = :posisi_date
                    WHERE {$trxWhereSql}
                    GROUP BY
                        LPAD(CAST(n.kode_cabang AS CHAR), 3, '0'),
                        COALESCE(n.kode_group2, t1.kode_group2)
                ) rls
                    ON rls.kode_ao = ao.kode_group2
                    AND rls.kode_kantor = LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0')
                {$aoWhereSql}
                ORDER BY
                    COALESCE(rls.total_realisasi, 0) DESC,
                    ao.nama_ao ASC
            ";

            if ($is_konsolidasi && !$korwilRange) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':posisi_date', $harian_date);
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);

            foreach ($trxParams as $key => $val) {
                $stmt->bindValue($key, $val);
            }

            foreach ($aoParams as $key => $val) {
                $stmt->bindValue($key, $val);
            }

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $summary = [
                'total_ao'        => count($data),
                'total_noa'       => 0,
                'total_realisasi' => 0,
            ];

            foreach ($data as $row) {
                $summary['total_noa'] += (int)($row['total_noa'] ?? 0);
                $summary['total_realisasi'] += (float)($row['total_realisasi'] ?? 0);
            }

            $dropdownData = [];
            if (function_exists('getDropdownKankasAo')) {
                $dropdownData = getDropdownKankasAo($this->pdo, $b);
            }

            return sendResponse(200, "Sukses load top realisasi AO", [
                'posisi_data' => $harian_date,
                'periode' => [
                    'closing_date' => $closing_date,
                    'harian_date'  => $harian_date,
                ],
                'mode' => [
                    'bulan_berjalan'  => $is_bulan_berjalan,
                    'filter_status_ao' => $is_bulan_berjalan ? 1 : 'ALL',
                ],
                'filter_aktif' => [
                    'kode_kantor' => $kode_kantor ?? 'ALL',
                    'korwil'      => $korwil ?: 'ALL',
                    'kode_ao'     => $kode_ao ?? 'ALL',
                ],
                'dropdown_lists' => $dropdownData,
                'summary' => $summary,
                'data' => $data,
                'pagination' => [
                    'total_data'     => $totalData,
                    'total_page'     => ($is_konsolidasi && !$korwilRange) ? (int)ceil($totalData / $limit) : 1,
                    'current_page'   => $page,
                    'limit'          => $limit,
                    'is_konsolidasi' => $is_konsolidasi && !$korwilRange,
                ],
            ]);
        } catch (Exception $e) {
            return sendResponse(500, "Error: " . $e->getMessage());
        }
    }


    public function getDetailRealisasiAO($input = [])
    {
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);

        $harian_date = $b['harian_date'] ?? date('Y-m-d');

        // Default closing_date = last day of previous month dari harian_date
        $closing_date = $b['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month', strtotime($harian_date)));

        $kode_ao = $b['kode_ao'] ?? null;

        $kode_kantor = $b['kode_kantor'] ?? null;
        $korwil = strtoupper(trim($b['korwil'] ?? ''));
        $kode_kantor_norm = $kode_kantor ? str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT) : null;
        $is_konsolidasi = empty($kode_kantor) || $kode_kantor === '000' || strtolower((string)$kode_kantor) === 'konsolidasi';
        $korwilRanges = [
            'SEMARANG' => ['001', '007'],
            'SOLO' => ['008', '014'],
            'BANYUMAS' => ['015', '021'],
            'PEKALONGAN' => ['022', '028'],
        ];
        $korwilRange = $korwilRanges[$korwil] ?? null;

        if (!$harian_date || !$closing_date) {
            return sendResponse(400, "Parameter harian_date / closing_date tidak lengkap");
        }

        if (!$kode_ao || strtoupper((string)$kode_ao) === 'ALL') {
            return sendResponse(400, "Parameter kode_ao wajib diisi untuk detail realisasi AO");
        }

        try {
            /*
            * =========================
            * MASTER AO
            * =========================
            * Supaya modal tetap punya data AO walaupun realisasi kosong.
            */
            $sqlAo = "
                SELECT
                    LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') AS kode_kantor,
                    COALESCE(kk.nama_kantor, LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0')) AS nama_kantor,
                    ao.kode_group2 AS kode_ao,
                    COALESCE(ao.nama_ao, ao.kode_group2) AS nama_ao,
                    ao.status
                FROM ao_kredit ao
                LEFT JOIN kode_kantor kk
                    ON LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = kk.kode_kantor
                WHERE ao.kode_group2 = :master_kode_ao
            ";

            if (!$is_konsolidasi) {
                $sqlAo .= " AND LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = :master_kode_kantor";
            } elseif ($korwilRange) {
                $sqlAo .= " AND LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') BETWEEN :master_korwil_awal AND :master_korwil_akhir";
            }

            $sqlAo .= " LIMIT 1";

            $stmtAo = $this->pdo->prepare($sqlAo);
            $stmtAo->bindValue(':master_kode_ao', $kode_ao);

            if (!$is_konsolidasi) {
                $stmtAo->bindValue(':master_kode_kantor', $kode_kantor_norm);
            } elseif ($korwilRange) {
                $stmtAo->bindValue(':master_korwil_awal', $korwilRange[0]);
                $stmtAo->bindValue(':master_korwil_akhir', $korwilRange[1]);
            }

            $stmtAo->execute();
            $masterAo = $stmtAo->fetch(PDO::FETCH_ASSOC) ?: null;

            /*
            * =========================
            * DETAIL TRANSAKSI REALISASI
            * =========================
            * Hanya kode_trans = 110.
            */
            $where = [];
            $params = [];

            $where[] = "t1.tanggal_realisasi > :closing_date";
            $where[] = "t1.tanggal_realisasi <= :harian_date";
            $where[] = "t1.kode_trans = 110";
            $where[] = "COALESCE(n.kode_group2, t1.kode_group2) = :kode_ao";

            $params[':closing_date'] = $closing_date;
            $params[':harian_date']  = $harian_date;
            $params[':kode_ao']      = $kode_ao;

            if (!$is_konsolidasi) {
                $where[] = "LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') = :kode_kantor";
                $params[':kode_kantor'] = $kode_kantor_norm;
            } elseif ($korwilRange) {
                $where[] = "LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') BETWEEN :korwil_awal AND :korwil_akhir";
                $params[':korwil_awal'] = $korwilRange[0];
                $params[':korwil_akhir'] = $korwilRange[1];
            }

            $whereSql = implode(" AND ", $where);

            $sql = "
                SELECT
                    t1.no_rekening,
                    COALESCE(t1.nama_nasabah, n.nama_nasabah, '-') AS nama_nasabah,
                    t1.realisasi_pokok AS plafond,
                    t1.tanggal_realisasi,

                    LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') AS kode_kantor,
                    COALESCE(k.nama_kantor, LPAD(CAST(n.kode_cabang AS CHAR), 3, '0')) AS nama_kantor,

                    n.kode_group1 AS kode_kankas,
                    COALESCE(kks.deskripsi_group1, n.kode_group1) AS nama_kankas,

                    COALESCE(n.kode_group2, t1.kode_group2) AS kode_ao,
                    COALESCE(ao.nama_ao, :kode_ao_nama_fallback) AS nama_ao,

                    n.kode_produk,
                 
                    n.baki_debet,
                    n.hari_menunggak
                FROM update_realisasi_kredit t1
                LEFT JOIN nominatif n
                    ON t1.no_rekening = n.no_rekening
                    AND DATE(n.created) = :posisi_date
                LEFT JOIN kode_kantor k
                    ON LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') = k.kode_kantor
                LEFT JOIN ao_kredit ao
                    ON COALESCE(n.kode_group2, t1.kode_group2) = ao.kode_group2
                    AND LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') = LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0')
                LEFT JOIN kankas kks
                    ON n.kode_group1 = kks.kode_group1
                    AND LPAD(CAST(n.kode_cabang AS CHAR), 3, '0') = LPAD(CAST(kks.kode_kantor AS CHAR), 3, '0')
                WHERE {$whereSql}
                ORDER BY
                    t1.tanggal_realisasi DESC,
                    t1.realisasi_pokok DESC,
                    t1.no_rekening ASC
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':posisi_date', $harian_date);
            $stmt->bindValue(':kode_ao_nama_fallback', $masterAo['nama_ao'] ?? $kode_ao);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $summary = [
                'total_noa'       => 0,
                'total_realisasi' => 0,
            ];

            foreach ($data as $row) {
                $summary['total_noa']++;
                $summary['total_realisasi'] += (float)($row['plafond'] ?? 0);
            }

            return sendResponse(200, "Sukses load detail realisasi AO", [
                'posisi_data' => $harian_date,
                'periode' => [
                    'closing_date' => $closing_date,
                    'harian_date'  => $harian_date,
                ],
                'filter_aktif' => [
                    'kode_kantor' => $kode_kantor ?? 'ALL',
                    'korwil'      => $korwil ?: 'ALL',
                    'kode_ao'     => $kode_ao,
                ],
                'ao' => $masterAo,
                'summary' => $summary,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function getRekapMob6Bulan($input = null) {
        set_time_limit(300); 
        ini_set('memory_limit', '512M');
        
        // --- 1. SETUP & REQUEST BODY ---
        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);
        $harian_date = $b['harian_date'] ?? date('Y-m-d'); 
        
        // Fitur dinamis grouping: 'bulan', 'ao', atau 'kankas'
        $rekap_by = $b['rekap_by'] ?? 'bulan'; 
        
        // Panggil Helper Filter (PENTING: pakai prefix 'n' karena kita mau JOIN)
        $filterData = buildBankFilters($b, 'n');
        $filterSql  = $filterData['sql'];
        $paramsBind = $filterData['params'];

        // --- 2. TENTUKAN RANGE REALISASI ---
        $tgl_data_obj = new DateTime($harian_date);
        
        $end_obj = clone $tgl_data_obj;
        $end_obj->modify('last day of previous month');
        $end_date_realisasi = $end_obj->format('Y-m-d');

        $start_obj = clone $end_obj;
        $start_obj->modify('-5 months'); 
        $start_obj->modify('first day of this month');
        $start_date_realisasi = $start_obj->format('Y-m-d');

        // --- 3. QUERY UTAMA NOMINATIF (Dengan JOIN) ---
        $sql = "SELECT 
                    n.kode_cabang, 
                    n.kode_group1,
                    n.kode_group2,
                    ak.nama_ao,
                    kk.deskripsi_group1 as nama_kankas,
                    n.tgl_realisasi,
                    n.jml_pinjaman as plafond, 
                    n.baki_debet as os,            
                    n.hari_menunggak
                FROM nominatif n
                LEFT JOIN ao_kredit ak ON n.kode_group2 = ak.kode_group2 AND n.kode_cabang = ak.kode_kantor
                LEFT JOIN kankas kk ON n.kode_group1 = kk.kode_group1 AND n.kode_cabang = kk.kode_kantor
                WHERE DATE(n.created) = :harian_date
                AND n.tgl_realisasi BETWEEN :start_date AND :end_date
                {$filterSql}"; 

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':harian_date', $harian_date);
            $stmt->bindValue(':start_date', $start_date_realisasi);
            $stmt->bindValue(':end_date', $end_date_realisasi);
            
            // Eksekusi Parameter Dinamis
            foreach ($paramsBind as $key => $val) {
                $stmt->bindValue($key, $val);
            }

            $stmt->execute();
            
            // --- 4. DATA PROCESSING (Grouping Dinamis) ---
            $grouped = [];
            $report_year  = (int)$tgl_data_obj->format('Y');
            $report_month = (int)$tgl_data_obj->format('n');

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $real_time   = strtotime($row['tgl_realisasi']);
                $real_month  = (int)date('n', $real_time);
                $real_year   = (int)date('Y', $real_time);
                $label_bulan = date('Y-m', $real_time);

                // Tetap pastikan hanya data MOB 1-6 yang diambil
                $mob = (($report_year - $real_year) * 12) + ($report_month - $real_month);
                if ($mob < 1 || $mob > 6) continue; 

                // --- TENTUKAN KEY GROUPING ---
                if ($rekap_by === 'ao') {
                    $group_key  = $row['kode_group2'] ?: 'UNKNOWN_AO';
                    $group_name = $row['nama_ao'] ? $row['nama_ao'] : ($row['kode_group2'] ?: 'TANPA AO');
                } elseif ($rekap_by === 'kankas') {
                    $group_key  = $row['kode_group1'] ?: 'UNKNOWN_KANKAS';
                    $group_name = $row['nama_kankas'] ? $row['nama_kankas'] : ($row['kode_group1'] ?: 'TANPA KANKAS');
                } else {
                    // Default Rekap By Bulan Realisasi
                    $group_key  = $label_bulan; 
                    $group_name = $label_bulan;
                }

                if (!isset($grouped[$group_key])) {
                    $grouped[$group_key] = [
                        'group_id'        => $group_key,
                        'group_name'      => $group_name,
                        'total_plafond'   => 0,
                        'buckets'         => []
                    ];
                    
                    // Kalau mode bulan, kita selipkan atribut "mob" ke response
                    if ($rekap_by === 'bulan') {
                        $grouped[$group_key]['mob'] = $mob;
                    }

                    $bk = ['0', '1 - 7', '8 - 14', '15 - 21', '22 - 30', '31 - 60', '61 - 90', '> 90'];
                    foreach($bk as $k) $grouped[$group_key]['buckets'][$k] = ['os'=>0, 'noa'=>0];
                }

                $grouped[$group_key]['total_plafond'] += (float)$row['plafond'];
                
                $dpd = (int)$row['hari_menunggak'];
                $bucketKey = '0';
                if ($dpd > 0 && $dpd <= 7) $bucketKey = '1 - 7';
                elseif ($dpd > 7 && $dpd <= 14) $bucketKey = '8 - 14';
                elseif ($dpd > 14 && $dpd <= 21) $bucketKey = '15 - 21';
                elseif ($dpd > 21 && $dpd <= 30) $bucketKey = '22 - 30';
                elseif ($dpd > 30 && $dpd <= 60) $bucketKey = '31 - 60';
                elseif ($dpd > 60 && $dpd <= 90) $bucketKey = '61 - 90';
                elseif ($dpd > 90) $bucketKey = '> 90';

                $grouped[$group_key]['buckets'][$bucketKey]['os'] += (float)$row['os'];
                $grouped[$group_key]['buckets'][$bucketKey]['noa']++;
            }

            $final_data = array_values($grouped);
            
            // --- 5. SORTING DATA BIAR RAPI ---
            usort($final_data, function($a, $b) {
                return $a['group_name'] <=> $b['group_name']; // Sort by nama / bulan (Ascending)
            });

            // Ambil Dropdown Data dari Helper
            $dropdownData = getDropdownKankasAo($this->pdo, $b);

            return sendResponse(200, "Rekap MOB (Group by: $rekap_by)", [
                'posisi_data'     => $harian_date,
                'rekap_by'        => $rekap_by,
                'filter_aktif'    => [
                    'korwil'      => $b['korwil'] ?? null,
                    'kode_kantor' => $b['kode_kantor'] ?? 'ALL',
                    'kode_kankas' => $b['kode_kankas'] ?? 'ALL',
                    'kode_ao'     => $b['kode_ao'] ?? 'ALL'
                ],
                'dropdown_lists'  => $dropdownData, 
                'buckets_order'   => ['0', '1 - 7', '8 - 14', '15 - 21', '22 - 30', '31 - 60', '61 - 90', '> 90'],
                'data'            => $final_data
            ]);

        } catch (PDOException $e) {
            return sendResponse(500, "DB Error: " . $e->getMessage());
        }
    }

    public function getDetailMobDebitur($input = null) {
        $b = is_array($input) ? $input : (json_decode(file_get_contents('php://input'), true) ?: []);

        // --- REQUEST BODY ---
        // harian_date, bulan_realisasi, bucket_label, page
        // korwil, kode_kantor, kode_kankas, kode_ao

        $harian_date   = $b['harian_date'] ?? date('Y-m-d');
        $bln_realisasi = $b['bulan_realisasi'] ?? null; 
        $bucket_label  = isset($b['bucket_label']) ? (string)$b['bucket_label'] : null;
        $isAllExport   = !empty($b['export_all']) || strtoupper((string)$bln_realisasi) === 'ALL' || strtoupper((string)$bucket_label) === 'ALL';
        
        $page   = isset($b['page']) ? (int)$b['page'] : 1;
        $limit  = isset($b['limit']) ? max(1, min((int)$b['limit'], 50000)) : 10; 
        $offset = ($page - 1) * $limit;

        if (!$isAllExport && (!$bln_realisasi || $bucket_label === null || $bucket_label === '')) {
            return sendResponse(400, "Parameter 'bulan_realisasi' dan 'bucket_label' wajib diisi.");
        }

        // 1. Panggil Helper Filter (Gunakan prefix 'n' karena query JOIN)
        $filterData = buildBankFilters($b, 'n');
        $filterSql  = $filterData['sql'];
        $paramsBind = $filterData['params'];

        // 2. Mapping Bucket DPD
        $dpd_min = 0; $dpd_max = 99999;
        if ($isAllExport)                    { $dpd_min = null; $dpd_max = null; }
        elseif ($bucket_label === '0')       { $dpd_min = 0;  $dpd_max = 0; }
        elseif ($bucket_label === '1 - 7')   { $dpd_min = 1;  $dpd_max = 7; }
        elseif ($bucket_label === '8 - 14')  { $dpd_min = 8;  $dpd_max = 14; }
        elseif ($bucket_label === '15 - 21') { $dpd_min = 15; $dpd_max = 21; }
        elseif ($bucket_label === '22 - 30') { $dpd_min = 22; $dpd_max = 30; }
        elseif ($bucket_label === '31 - 60') { $dpd_min = 31; $dpd_max = 60; }
        elseif ($bucket_label === '61 - 90') { $dpd_min = 61; $dpd_max = 90; }
        elseif ($bucket_label === '> 90')    { $dpd_min = 91; $dpd_max = 99999; }
        else {
            return sendResponse(400, "Label Bucket tidak valid: " . $bucket_label);
        }

        if ($isAllExport) {
            $tgl_data_obj = new DateTime($harian_date);
            $end_obj = clone $tgl_data_obj;
            $end_obj->modify('last day of previous month');
            $tgl_akhir_bulan = $end_obj->format('Y-m-d');

            $start_obj = clone $end_obj;
            $start_obj->modify('-5 months');
            $start_obj->modify('first day of this month');
            $tgl_awal_bulan = $start_obj->format('Y-m-d');
        } else {
            $tgl_awal_bulan  = $bln_realisasi . '-01';
            $tgl_akhir_bulan = date('Y-m-t', strtotime($tgl_awal_bulan));
        }

        $dpdSql = $isAllExport ? "" : "AND n.hari_menunggak BETWEEN :dpd_min AND :dpd_max";

        try {
            // 3. Hitung Total Data (Untuk Pagination)
            $sqlCount = "
                SELECT COUNT(*) 
                FROM nominatif n
                WHERE DATE(n.created) = :harian_date
                AND n.tgl_realisasi BETWEEN :start AND :end
                {$dpdSql}
                {$filterSql}
            ";

            $stmtCount = $this->pdo->prepare($sqlCount);
            $stmtCount->bindValue(':harian_date', $harian_date);
            $stmtCount->bindValue(':start', $tgl_awal_bulan);
            $stmtCount->bindValue(':end', $tgl_akhir_bulan);
            if (!$isAllExport) {
                $stmtCount->bindValue(':dpd_min', $dpd_min);
                $stmtCount->bindValue(':dpd_max', $dpd_max);
            }
            
            // Bind parameter filter helper
            foreach ($paramsBind as $key => $val) {
                $stmtCount->bindValue($key, $val);
            }
            
            $stmtCount->execute();
            $total_records = $stmtCount->fetchColumn();

            // 4. Query Utama Detail
            $sql = "
                SELECT 
                    n.no_rekening, 
                    n.nama_nasabah, 
                    n.alamat,
                    n.hp as no_hp,
                    
                    -- Tampilkan nama master, fallback ke kode jika tidak ditemukan di master
                    COALESCE(kk.deskripsi_group1, n.kode_group1) as nama_kankas,
                    COALESCE(ak.nama_ao, n.kode_group2) as nama_ao,
                    
                    COALESCE(tb.saldo_akhir, 0) as tabungan,
                    n.tgl_realisasi, 
                    n.jml_pinjaman as plafond, 
                    n.baki_debet as os, 
                    n.hari_menunggak,
                    COALESCE(n.hari_menunggak_pokok, 0) as hari_menunggak_pokok,
                    COALESCE(n.hari_menunggak_bunga, 0) as hari_menunggak_bunga,
                    GREATEST((COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)), 0) as totung,
                    n.kolektibilitas,
                    n.kode_cabang,
                    DATE_FORMAT(n.tgl_realisasi, '%Y-%m') as bulan_realisasi,
                    t.tgl_trans,
                    COALESCE(t.total_bayar, 0) as transaksi
                FROM nominatif n
                
                -- JOIN ke tabel master
                LEFT JOIN kankas kk ON n.kode_group1 = kk.kode_group1 AND n.kode_cabang = kk.kode_kantor
                LEFT JOIN ao_kredit ak ON n.kode_group2 = ak.kode_group2 AND n.kode_cabang = ak.kode_kantor
                
                LEFT JOIN (
                    SELECT 
                        no_rekening,
                        MAX(tgl_trans) as tgl_trans,
                        SUM(COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0)) as total_bayar
                    FROM transaksi_kredit 
                    WHERE MONTH(tgl_trans) = MONTH(:trans_date_1) 
                      AND YEAR(tgl_trans) = YEAR(:trans_date_2)
                    GROUP BY no_rekening
                ) t ON n.no_rekening = t.no_rekening
                
                LEFT JOIN tabungan tb ON n.norek_tabungan = tb.no_rekening
                
                WHERE DATE(n.created) = :harian_date
                AND n.tgl_realisasi BETWEEN :start AND :end
                {$dpdSql}
                {$filterSql}
                ORDER BY n.baki_debet DESC LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':harian_date', $harian_date);
            $stmt->bindValue(':trans_date_1', $harian_date); 
            $stmt->bindValue(':trans_date_2', $harian_date); 
            $stmt->bindValue(':start', $tgl_awal_bulan);
            $stmt->bindValue(':end', $tgl_akhir_bulan);
            if (!$isAllExport) {
                $stmt->bindValue(':dpd_min', $dpd_min);
                $stmt->bindValue(':dpd_max', $dpd_max);
            }
            
            // Bind parameter filter helper lagi untuk query utama
            foreach ($paramsBind as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Data Type Casting
            foreach ($data as &$row) {
                $row['transaksi'] = (float)$row['transaksi']; 
                $row['plafond']   = (float)$row['plafond'];
                $row['os']        = (float)$row['os'];
                $row['totung']    = (float)$row['totung'];
                $row['tabungan']  = (float)$row['tabungan'];
                
                $row['hari_menunggak']       = (int)$row['hari_menunggak'];
                $row['hari_menunggak_pokok'] = (int)$row['hari_menunggak_pokok'];
                $row['hari_menunggak_bunga'] = (int)$row['hari_menunggak_bunga'];
                
                if ($row['tabungan'] >= (1.5 * $row['totung'])) {
                    $row['status_tabungan'] = 'Aman';
                } else {
                    $row['status_tabungan'] = 'Belum Aman';
                }

                $dpd = $row['hari_menunggak'];
                if ($dpd <= 0) $row['bucket_label'] = '0';
                elseif ($dpd <= 7) $row['bucket_label'] = '1 - 7';
                elseif ($dpd <= 14) $row['bucket_label'] = '8 - 14';
                elseif ($dpd <= 21) $row['bucket_label'] = '15 - 21';
                elseif ($dpd <= 30) $row['bucket_label'] = '22 - 30';
                elseif ($dpd <= 60) $row['bucket_label'] = '31 - 60';
                elseif ($dpd <= 90) $row['bucket_label'] = '61 - 90';
                else $row['bucket_label'] = '> 90';
            }
            unset($row); 

            return sendResponse(200, "Detail Debitur Sukses (Bucket: $bucket_label)", [
                'total_records' => $total_records,
                'total_pages'   => ceil($total_records / $limit),
                'current_page'  => $page,
                'data'          => $data
            ]);

        } catch (PDOException $e) {
            return sendResponse(500, "Database Error: " . $e->getMessage());
        }
    }


    public function getMigrasiKolek1($input) {
        $closing_date = !empty($input['closing_date']) ? $input['closing_date'] : date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = !empty($input['harian_date'])  ? $input['harian_date']  : date('Y-m-d');
        $kode_kantor  = !empty($input['kode_kantor'])  ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;

        // filter cabang (pakai placeholder BERBEDA)
        $filter_cabang_closing = $kode_kantor ? " AND c.kode_cabang = :kode_kantor_c " : "";
        $filter_cabang_harian  = $kode_kantor ? " AND h.kode_cabang = :kode_kantor_h " : "";

        $sql = "
            WITH 
            closing AS (
                SELECT c.no_rekening, c.kode_cabang, c.kolektibilitas AS kolek_closed, c.baki_debet AS baki_closed
                FROM nominatif c
                WHERE c.created = :closing_date_m
                AND c.kolektibilitas IN ('L','DP','KL','D','M')
                $filter_cabang_closing
            ),
            harian AS (
                SELECT h.no_rekening, h.kode_cabang, h.kolektibilitas AS kolek_update, h.baki_debet AS baki_harian
                FROM nominatif h
                WHERE h.created = :harian_date_m
                AND h.kolektibilitas IN ('L','DP','KL','D','M')
                $filter_cabang_harian
            ),
            gabung AS (
                SELECT 
                    c.kolek_closed,
                    h.kolek_update,
                    c.baki_closed,
                    COALESCE(h.baki_harian, 0) AS baki_harian,
                    (c.baki_closed - COALESCE(h.baki_harian, 0)) AS pembayaran,
                    CASE WHEN h.no_rekening IS NULL THEN 1 ELSE 0 END AS is_lunas
                FROM closing c
                LEFT JOIN harian h ON h.no_rekening = c.no_rekening
            )
            SELECT 
                g.kolek_closed,
                SUM(g.baki_closed) AS saldo_closed,
                SUM(CASE WHEN g.kolek_update = 'L'  THEN g.baki_harian ELSE 0 END) AS migrasi_L,
                SUM(CASE WHEN g.kolek_update = 'DP' THEN g.baki_harian ELSE 0 END) AS migrasi_DP,
                SUM(CASE WHEN g.kolek_update = 'KL' THEN g.baki_harian ELSE 0 END) AS migrasi_KL,
                SUM(CASE WHEN g.kolek_update = 'D'  THEN g.baki_harian ELSE 0 END) AS migrasi_D,
                SUM(CASE WHEN g.kolek_update = 'M'  THEN g.baki_harian ELSE 0 END) AS migrasi_M,
                SUM(g.pembayaran) AS pembayaran,
                SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) AS lunas_osc
            FROM gabung g
            GROUP BY g.kolek_closed

            UNION ALL

            SELECT 
                'TOTAL' AS kolek_closed,
                SUM(g.baki_closed) AS saldo_closed,
                SUM(CASE WHEN g.kolek_update = 'L'  THEN g.baki_harian ELSE 0 END) AS migrasi_L,
                SUM(CASE WHEN g.kolek_update = 'DP' THEN g.baki_harian ELSE 0 END) AS migrasi_DP,
                SUM(CASE WHEN g.kolek_update = 'KL' THEN g.baki_harian ELSE 0 END) AS migrasi_KL,
                SUM(CASE WHEN g.kolek_update = 'D'  THEN g.baki_harian ELSE 0 END) AS migrasi_D,
                SUM(CASE WHEN g.kolek_update = 'M'  THEN g.baki_harian ELSE 0 END) AS migrasi_M,
                SUM(g.pembayaran) AS pembayaran,
                SUM(CASE WHEN g.is_lunas = 1 THEN g.baki_closed ELSE 0 END) AS lunas_osc
            FROM gabung g

            ORDER BY 
                CASE 
                    WHEN kolek_closed = 'L'     THEN 1
                    WHEN kolek_closed = 'DP'    THEN 2
                    WHEN kolek_closed = 'KL'    THEN 3
                    WHEN kolek_closed = 'D'     THEN 4
                    WHEN kolek_closed = 'M'     THEN 5
                    WHEN kolek_closed = 'TOTAL' THEN 99
                    ELSE 98
                END
        ";

        try {
            // 1) Eksekusi migrasi
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing_date_m', $closing_date);
            $stmt->bindValue(':harian_date_m',  $harian_date);
            if ($kode_kantor) {
                $stmt->bindValue(':kode_kantor_c', $kode_kantor);
                $stmt->bindValue(':kode_kantor_h', $kode_kantor);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ambil pembayaran TOTAL
            $pembayaran_total = 0.0; $totalIdx = null;
            foreach ($rows as $i => $r) {
                if (($r['kolek_closed'] ?? '') === 'TOTAL') {
                    $totalIdx = $i;
                    $pembayaran_total = (float)($r['pembayaran'] ?? 0);
                    break;
                }
            }

            // ================== META: %NPL, REALISASI, GROWTH ==================
            $awal_bulan = date('Y-m-01', strtotime($harian_date));
            $filter_np = $kode_kantor ? " AND kode_cabang = :kode_np " : "";
            $filter_nn = $kode_kantor ? " AND kode_cabang = :kode_nn " : "";
            $filter_tp = $kode_kantor ? " AND kode_cabang = :kode_tp " : "";
            $filter_tn = $kode_kantor ? " AND kode_cabang = :kode_tn " : "";
            $filter_rl = $kode_kantor ? " AND kode_cabang = :kode_rl " : "";

            $sqlMeta = "
                SELECT
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :closing_date_np
                    AND kolektibilitas IN ('KL','D','M') $filter_np) AS npl_prev,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_nn
                    AND kolektibilitas IN ('KL','D','M') $filter_nn) AS npl_now,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :closing_date_tp
                    AND kolektibilitas IN ('L','DP','KL','D','M') $filter_tp) AS total_prev,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_tn
                    AND kolektibilitas IN ('L','DP','KL','D','M') $filter_tn) AS total_now,
                (SELECT COALESCE(SUM(baki_debet),0) FROM nominatif
                    WHERE created = :harian_date_rl1
                    AND tgl_realisasi >= :awal_bulan
                    AND tgl_realisasi <= :harian_date_rl2 $filter_rl) AS realisasi_bulan_ini
            ";

            $st = $this->pdo->prepare($sqlMeta);
            // bind tanggal – semua UNIK
            $st->bindValue(':closing_date_np', $closing_date);
            $st->bindValue(':harian_date_nn',  $harian_date);
            $st->bindValue(':closing_date_tp', $closing_date);
            $st->bindValue(':harian_date_tn',  $harian_date);
            $st->bindValue(':harian_date_rl1', $harian_date);
            $st->bindValue(':harian_date_rl2', $harian_date);
            $st->bindValue(':awal_bulan',      $awal_bulan);
            // bind cabang opsional
            if ($kode_kantor) {
                $st->bindValue(':kode_np', $kode_kantor);
                $st->bindValue(':kode_nn', $kode_kantor);
                $st->bindValue(':kode_tp', $kode_kantor);
                $st->bindValue(':kode_tn', $kode_kantor);
                $st->bindValue(':kode_rl', $kode_kantor);
            }
            $st->execute();
            $meta = $st->fetch(PDO::FETCH_ASSOC) ?: [
                'npl_prev'=>0,'npl_now'=>0,'total_prev'=>0,'total_now'=>0,'realisasi_bulan_ini'=>0
            ];

            $npl_prev = (float)$meta['npl_prev'];
            $npl_now  = (float)$meta['npl_now'];
            $tot_prev = (float)$meta['total_prev'];
            $tot_now  = (float)$meta['total_now'];
            $realisasi= (float)$meta['realisasi_bulan_ini'];

            $npl_prev_pct = $tot_prev > 0 ? round($npl_prev * 100.0 / $tot_prev, 2) : 0.0;
            $npl_now_pct  = $tot_now  > 0 ? round($npl_now  * 100.0 / $tot_now , 2) : 0.0;
            $npl_delta_pct= round($npl_now_pct - $npl_prev_pct, 2);
            $growth       = $realisasi - $pembayaran_total;

            if ($totalIdx !== null) {
                $rows[$totalIdx]['realisasi_bulan_ini'] = $realisasi;
                $rows[$totalIdx]['npl_prev']            = $npl_prev;
                $rows[$totalIdx]['npl_now']             = $npl_now;
                $rows[$totalIdx]['total_prev']          = $tot_prev;
                $rows[$totalIdx]['total_now']           = $tot_now;
                $rows[$totalIdx]['npl_prev_pct']        = $npl_prev_pct;
                $rows[$totalIdx]['npl_now_pct']         = $npl_now_pct;
                $rows[$totalIdx]['npl_delta_pct']       = $npl_delta_pct;
                $rows[$totalIdx]['growth']              = $growth;
                $rows[$totalIdx]['pembayaran_total']    = $pembayaran_total;
            }

            sendResponse(200, "Berhasil ambil data migrasi kolektibilitas (dengan lunas_osc)", $rows);

        } catch (PDOException $e) {
            sendResponse(500, "PDO Error: " . $e->getMessage(), null);
        }
    }

    /**
     * 5. REKAP USIA KREDIT (Berdasarkan Range Bulan)
     * Filter: Korwil, Cabang, Kankas.
     * Note: Kolom created bertipe DATE
     */
    public function getRekapUsiaKredit($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;

        if (!$harian) {
            return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);
        }

        // --- 1. BUILD FILTER QUERY ---
        $sqlFilter = "";
        $params = [
            ':harian' => $harian 
        ];

        // Filter Cabang / Korwil
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kode_cabang = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter .= " AND t.kode_cabang BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        // Filter Kankas
        if ($kankas) {
            $sqlFilter .= " AND COALESCE(NULLIF(t.kode_group1, ''), CONCAT(t.kode_cabang, '000')) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // --- 2. MAIN QUERY ---
        // 🔥 FIX: Kategori umur ditambahkan sampai > 4 Tahun (Tahun ke-5 dst)
        $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) < 6 THEN '< 6 Bulan'
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 6 AND 12 THEN '6 - 12 Bulan'
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 13 AND 24 THEN '1 - 2 Tahun'
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 25 AND 48 THEN '> 2 - 4 Tahun'
                        ELSE '> 4 Tahun'
                    END as kategori,
                    CASE 
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) < 6 THEN 1
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 6 AND 12 THEN 2
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 13 AND 24 THEN 3
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) BETWEEN 25 AND 48 THEN 4
                        ELSE 5
                    END as sort_order,
                    COUNT(t.no_rekening) as total_noa,
                    SUM(t.baki_debet) as total_os,
                    
                    -- Pemilahan berdasarkan Kolektibilitas
                    SUM(CASE WHEN t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) as os_lancar,
                    SUM(CASE WHEN t.kolektibilitas IN ('DP', 'DPK') THEN t.baki_debet ELSE 0 END) as os_dpk,
                    SUM(CASE WHEN t.kolektibilitas IN ('KL', 'D', 'M') THEN t.baki_debet ELSE 0 END) as os_npl,
                    
                    SUM(CASE WHEN t.tgl_jatuh_tempo < p.harian THEN t.baki_debet ELSE 0 END) as os_lewat_jt
                FROM nominatif t
                CROSS JOIN (SELECT :harian AS harian) p
                WHERE t.created = p.harian 
                AND t.baki_debet > 0
                $sqlFilter
                GROUP BY kategori, sort_order
                ORDER BY sort_order ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- 3. DYNAMIC META DATA (NAMA KANTOR) ---
            $nama_kantor_filter = "SEMUA CABANG (KONSOLIDASI)";
            if ($kankas) {
                $stmtK = $this->pdo->prepare("SELECT deskripsi_group1 FROM kankas WHERE kode_group1 = ?");
                $stmtK->execute([$kankas]);
                $nama = $stmtK->fetchColumn();
                $nama_kantor_filter = $nama ?: "KANKAS " . $kankas;
            } elseif ($kode_kantor && $kode_kantor !== '000') {
                $stmtK = $this->pdo->prepare("SELECT nama_kantor FROM kode_kantor WHERE kode_kantor = ?");
                $stmtK->execute([$kode_kantor]);
                $nama = $stmtK->fetchColumn();
                $nama_kantor_filter = $nama ?: "CABANG " . $kode_kantor;
            } elseif ($korwil) {
                $nama_kantor_filter = "KORWIL " . $korwil;
            }

            // --- 4. DATA PROCESSING ---
            $grandTotal = [
                'total_noa' => 0, 
                'total_os' => 0, 
                'os_lancar' => 0, 
                'os_dpk' => 0, 
                'os_npl' => 0, 
                'os_lewat_jt' => 0, 
                'persen_npl' => 0
            ];

            foreach ($rows as &$r) {
                $r['total_noa']   = (int)$r['total_noa'];
                $r['total_os']    = (float)$r['total_os'];
                $r['os_lancar']   = (float)$r['os_lancar'];
                $r['os_dpk']      = (float)$r['os_dpk'];
                $r['os_npl']      = (float)$r['os_npl'];
                $r['os_lewat_jt'] = (float)$r['os_lewat_jt'];
                $r['persen_npl']  = $r['total_os'] > 0 ? round(($r['os_npl'] / $r['total_os']) * 100, 2) : 0;

                $grandTotal['total_noa']   += $r['total_noa'];
                $grandTotal['total_os']    += $r['total_os'];
                $grandTotal['os_lancar']   += $r['os_lancar'];
                $grandTotal['os_dpk']      += $r['os_dpk'];
                $grandTotal['os_npl']      += $r['os_npl'];
                $grandTotal['os_lewat_jt'] += $r['os_lewat_jt'];
            }

            $grandTotal['persen_npl'] = $grandTotal['total_os'] > 0 ? round(($grandTotal['os_npl'] / $grandTotal['total_os']) * 100, 2) : 0;

            $responseData = [
                'meta' => [
                    'filter_aktif' => $nama_kantor_filter,
                    'tanggal'      => $harian
                ],
                'grand_total' => $grandTotal,
                'data' => $rows
            ];

            return sendResponse(200, "Berhasil ambil rekap usia kredit", $responseData);

        } catch (PDOException $e) {
            error_log("Error getRekapUsiaKredit: " . $e->getMessage());
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null);
        }
    }


    /**
     * 6. REKAP PROGRESS KREDIT (%)
     */
    public function getRekapProgressKredit($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        $sqlFilter = "";
        $params = [':harian' => $harian];

        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kode_cabang = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter .= " AND t.kode_cabang BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        // 🔥 FIX KANKAS: Pakai TRIM untuk jaga-jaga spasi kosong di database
        if ($kankas) {
            $sqlFilter .= " AND COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        $sql = "SELECT 
                    CASE 
                        WHEN t.tgl_jatuh_tempo < p.harian THEN '> 100% (Lewat JT)'
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) <= 0 THEN 'Data Error'
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 25 THEN '0% - 25% (Awal)'
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 50 THEN '26% - 50%'
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 75 THEN '51% - 75%'
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 95 THEN '76% - 95%'
                        ELSE '96% - 100% (Pantauan)'
                    END as kategori,
                    CASE 
                        WHEN t.tgl_jatuh_tempo < p.harian THEN 6
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) <= 0 THEN 7
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 25 THEN 1
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 50 THEN 2
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 75 THEN 3
                        WHEN (TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100 <= 95 THEN 4
                        ELSE 5
                    END as sort_order,
                    COUNT(t.no_rekening) as total_noa,
                    SUM(t.baki_debet) as total_os,
                    
                    SUM(CASE WHEN t.kolektibilitas IN ('L', 'DP', 'DPK') THEN 1 ELSE 0 END) as noa_performing,
                    SUM(CASE WHEN t.kolektibilitas IN ('L', 'DP', 'DPK') THEN t.baki_debet ELSE 0 END) as os_performing,
                    
                    SUM(CASE WHEN t.kolektibilitas IN ('KL', 'D', 'M') THEN 1 ELSE 0 END) as noa_npl,
                    SUM(CASE WHEN t.kolektibilitas IN ('KL', 'D', 'M') THEN t.baki_debet ELSE 0 END) as os_npl
                FROM nominatif t
                CROSS JOIN (SELECT :harian AS harian) p
                WHERE t.created = p.harian 
                AND t.baki_debet > 0
                $sqlFilter
                GROUP BY kategori, sort_order
                ORDER BY sort_order ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $nama_kantor_filter = "SEMUA CABANG (KONSOLIDASI)";
            if ($kankas) {
                $stmtK = $this->pdo->prepare("SELECT deskripsi_group1 FROM kankas WHERE kode_group1 = ?");
                $stmtK->execute([$kankas]);
                $nama_kantor_filter = $stmtK->fetchColumn() ?: "KANKAS " . $kankas;
            } elseif ($kode_kantor && $kode_kantor !== '000') {
                $stmtK = $this->pdo->prepare("SELECT nama_kantor FROM kode_kantor WHERE kode_kantor = ?");
                $stmtK->execute([$kode_kantor]);
                $nama_kantor_filter = $stmtK->fetchColumn() ?: "CABANG " . $kode_kantor;
            } elseif ($korwil) {
                $nama_kantor_filter = "KORWIL " . $korwil;
            }

            $grandTotal = ['total_noa'=>0, 'total_os'=>0, 'noa_performing'=>0, 'os_performing'=>0, 'noa_npl'=>0, 'os_npl'=>0, 'persen_npl'=>0];

            foreach ($rows as $r) {
                $grandTotal['total_noa']      += (int)$r['total_noa'];
                $grandTotal['total_os']       += (float)$r['total_os'];
                $grandTotal['noa_performing'] += (int)$r['noa_performing'];
                $grandTotal['os_performing']  += (float)$r['os_performing'];
                $grandTotal['noa_npl']        += (int)$r['noa_npl'];
                $grandTotal['os_npl']         += (float)$r['os_npl'];
            }
            $grandTotal['persen_npl'] = $grandTotal['total_os'] > 0 ? round(($grandTotal['os_npl'] / $grandTotal['total_os']) * 100, 2) : 0;

            foreach ($rows as &$r) {
                $r['total_noa']      = (int)$r['total_noa'];
                $r['total_os']       = (float)$r['total_os'];
                $r['noa_performing'] = (int)$r['noa_performing'];
                $r['os_performing']  = (float)$r['os_performing'];
                $r['noa_npl']        = (int)$r['noa_npl'];
                $r['os_npl']         = (float)$r['os_npl'];
                $r['persen_npl']     = $grandTotal['total_os'] > 0 ? round(($r['os_npl'] / $grandTotal['total_os']) * 100, 2) : 0;
            }

            return sendResponse(200, "Berhasil", [
                'meta' => ['filter_aktif' => $nama_kantor_filter, 'tanggal' => $harian],
                'grand_total' => $grandTotal,
                'data' => $rows
            ]);
        } catch (PDOException $e) { return sendResponse(500, "PDO Error: " . $e->getMessage(), null); }
    }

    /**
     * 7. DETAIL PROGRESS KREDIT (Pagination)
     */
    public function getDetailProgressKredit($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        
        // 🔥 PASTIKAN 3 BARIS INI ADA AGAR FILTER NPL & AO JALAN!
        $kategori= $b['kategori'] ?? 'ALL'; 
        $status  = $b['status'] ?? 'ALL'; 
        $ao      = $b['kode_ao'] ?? null; 
        
        $page    = $b['page'] ?? 1;
        $limit   = $b['limit'] ?? 20;
        $offset  = ($page - 1) * $limit;

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        $sqlFilter = "";
        $params = [':harian' => $harian];

        // 1. Filter Area
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kode_cabang = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter .= " AND t.kode_cabang BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        if ($kankas) {
            $sqlFilter .= " AND COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // 🔥 2. Filter AO (Dari Dropdown Modal)
        if ($ao) {
            $sqlFilter .= " AND t.kode_group2 = :kode_ao ";
            $params[':kode_ao'] = $ao;
        }

        // 3. Filter Range Kategori Persentase
        $pct_calc = "(TIMESTAMPDIFF(MONTH, t.tgl_realisasi, p.harian) / TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo)) * 100";
        $catFilter = "";

        if ($kategori === 'Lewat JT') {
            $catFilter = " AND t.tgl_jatuh_tempo < p.harian ";
        } elseif ($kategori === 'Error') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) <= 0 ";
        } elseif ($kategori === '0-25') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) > 0 AND $pct_calc <= 25 ";
        } elseif ($kategori === '26-50') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) > 0 AND $pct_calc > 25 AND $pct_calc <= 50 ";
        } elseif ($kategori === '51-75') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) > 0 AND $pct_calc > 50 AND $pct_calc <= 75 ";
        } elseif ($kategori === '76-95') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) > 0 AND $pct_calc > 75 AND $pct_calc <= 95 ";
        } elseif ($kategori === '96-100') {
            $catFilter = " AND t.tgl_jatuh_tempo >= p.harian AND TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) > 0 AND $pct_calc > 95 AND $pct_calc <= 100 ";
        }

        // 🔥 4. Filter Status Murni (Lancar vs NPL)
        if ($status === 'PERFORMING') {
            $catFilter .= " AND t.kolektibilitas IN ('L', 'DP', 'DPK') ";
        } elseif ($status === 'NPL') {
            $catFilter .= " AND t.kolektibilitas IN ('KL', 'D', 'M') ";
        }

        $baseQuery = "FROM nominatif t
                      CROSS JOIN (SELECT :harian AS harian) p
                      LEFT JOIN kankas kn ON t.kode_group1 = kn.kode_group1
                      LEFT JOIN ao_kredit ao ON t.kode_group2 = ao.kode_group2
                      WHERE t.created = p.harian AND t.baki_debet > 0 
                      $sqlFilter $catFilter";

        try {
            $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) $baseQuery");
            foreach ($params as $key => $val) { $stmtCnt->bindValue($key, $val); }
            $stmtCnt->execute();
            $totalRecords = $stmtCnt->fetchColumn();

            $cols = "t.no_rekening, t.nama_nasabah, t.alamat, t.hp as no_hp, 
                     COALESCE(kn.deskripsi_group1, t.kode_group1) as kankas,
                     COALESCE(ao.nama_ao, t.kode_group2) as nama_ao,
                     t.tgl_realisasi, t.tgl_jatuh_tempo, t.jml_pinjaman, t.baki_debet, t.kolektibilitas,
                     CASE 
                        WHEN t.tgl_jatuh_tempo < p.harian THEN 100 
                        WHEN TIMESTAMPDIFF(MONTH, t.tgl_realisasi, t.tgl_jatuh_tempo) <= 0 THEN 0
                        ELSE ROUND($pct_calc, 2)
                     END as persen_jalan";

            $sqlData = "SELECT $cols $baseQuery ORDER BY t.baki_debet DESC LIMIT :lim OFFSET :off";
            
            $stmt = $this->pdo->prepare($sqlData);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$r) {
                $r['baki_debet'] = (float)$r['baki_debet'];
                $r['jml_pinjaman'] = (float)$r['jml_pinjaman'];
                $r['persen_jalan'] = (float)$r['persen_jalan'];
                
                if (in_array($r['kolektibilitas'], ['L', 'DP', 'DPK'])) {
                    $r['status_ket'] = 'PERFORMING';
                } else {
                    $r['status_ket'] = 'NPL';
                }
            }

            return sendResponse(200, "Berhasil", [
                'pagination' => ['current_page' => (int)$page, 'total_records' => (int)$totalRecords, 'total_pages' => ceil($totalRecords / $limit)],
                'data' => $rows
            ]);
        } catch (PDOException $e) { return sendResponse(500, "PDO Error: " . $e->getMessage(), null); }
    }



}
