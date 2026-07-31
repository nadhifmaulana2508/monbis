<?php

require_once __DIR__ . '/../helpers/response.php';

class NplController {
    private $pdo;
    private $korwilRanges = [
        'SEMARANG' => ['001', '007'],
        'SOLO' => ['008', '014'],
        'BANYUMAS' => ['015', '021'],
        'PEKALONGAN' => ['022', '028'],
    ];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function normalizeKodeKantor($kode) {
        $kode = trim((string)($kode ?? ''));
        if ($kode === '' || strtoupper($kode) === 'ALL' || $kode === '000') return null;
        return str_pad($kode, 3, '0', STR_PAD_LEFT);
    }

    private function korwilRange($korwil) {
        $key = strtoupper(trim((string)($korwil ?? '')));
        return $this->korwilRanges[$key] ?? null;
    }


    public function getNpl($input) {
    $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
    $harian_date  = $input['harian_date']  ?? date('Y-m-d');
    $kc           = $input['kode_kantor']  ?? null;
    
    // Tambahkan logika pemilihan kolom (baki_debet atau saldo_bank)
    // Default tetap baki_debet jika tidak dikirim
    $modeHitung = $input['hitung_berdasarkan'] ?? 'baki_debet';
    $colValue   = ($modeHitung === 'saldo_bank') ? 'saldo_bank' : 'baki_debet';

    if ($kc === '000') $kc = null;

    // Logic Parameter Binding
    if ($kc) {
        // Mode KANKAS
        $colKey       = "kode_group1";
        $selectName   = "COALESCE(k.deskripsi_group1, CONCAT('KAS ', h.kode_key))";
        $joinTable    = "LEFT JOIN kankas k ON h.kode_key = k.kode_group1";
        $filterClauseHarian = "AND kode_cabang = :kc1";
        $filterClauseClosing = "AND kode_cabang = :kc2";
        $kc_val       = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
    } else {
        // Mode KONSOLIDASI
        $colKey       = "kode_cabang";
        $selectName   = "k.nama_kantor";
        $joinTable    = "LEFT JOIN kode_kantor k ON h.kode_key = k.kode_kantor";
        $filterClauseHarian = "";
        $filterClauseClosing = "";
        $kc_val       = null;
    }

    $sql = "
        WITH 
        harian AS (
            /* Menggunakan dinamis kolom $colValue */
            SELECT $colKey as kode_key, kolektibilitas, $colValue as nilai_nominal
            FROM nominatif WHERE created = :harian_date $filterClauseHarian
        ),
        closing AS (
            SELECT $colKey as kode_key, kolektibilitas, $colValue as nilai_nominal
            FROM nominatif WHERE created = :closing_date $filterClauseClosing
        ),
        rekap_harian AS (
            SELECT h.kode_key, $selectName as nama_unit,
                SUM(CASE WHEN h.kolektibilitas IN ('KL', 'D', 'M') THEN h.nilai_nominal ELSE 0 END) AS npl_harian,
                SUM(h.nilai_nominal) AS total_harian
            FROM harian h $joinTable GROUP BY h.kode_key, $selectName
        ),
        rekap_closing AS (
            SELECT c.kode_key,
                SUM(CASE WHEN c.kolektibilitas IN ('KL', 'D', 'M') THEN c.nilai_nominal ELSE 0 END) AS npl_closing,
                SUM(c.nilai_nominal) AS total_closing
            FROM closing c GROUP BY c.kode_key
        ),
        gabung AS (
            SELECT rh.kode_key, rh.nama_unit, COALESCE(rc.npl_closing, 0) AS npl_closing, rh.npl_harian,
                (rh.npl_harian - COALESCE(rc.npl_closing, 0)) AS selisih_npl,
                COALESCE(rc.total_closing, 0) AS total_closing, rh.total_harian
            FROM rekap_harian rh LEFT JOIN rekap_closing rc ON rh.kode_key = rc.kode_key
        )
        SELECT kode_key as kode_unit, nama_unit, npl_closing, npl_harian, selisih_npl,
            ROUND(CASE WHEN total_closing = 0 THEN 0 ELSE (npl_closing * 100.0) / total_closing END, 2) AS npl_closing_persen,
            ROUND(CASE WHEN total_harian = 0 THEN 0 ELSE (npl_harian * 100.0) / total_harian END, 2) AS npl_harian_persen,
            ROUND((CASE WHEN total_harian = 0 THEN 0 ELSE (npl_harian * 100.0) / total_harian END) - 
                (CASE WHEN total_closing = 0 THEN 0 ELSE (npl_closing * 100.0) / total_closing END), 2) AS selisih_npl_persen
        FROM gabung
        UNION ALL
        SELECT '', 'TOTAL KONSOLIDASI', SUM(npl_closing), SUM(npl_harian), SUM(selisih_npl),
            ROUND(CASE WHEN SUM(total_closing) = 0 THEN 0 ELSE (SUM(npl_closing) * 100.0) / SUM(total_closing) END, 2),
            ROUND(CASE WHEN SUM(total_harian) = 0 THEN 0 ELSE (SUM(npl_harian) * 100.0) / SUM(total_harian) END, 2),
            ROUND((CASE WHEN SUM(total_harian) = 0 THEN 0 ELSE (SUM(npl_harian) * 100.0) / SUM(total_harian) END) - 
                (CASE WHEN SUM(total_closing) = 0 THEN 0 ELSE (SUM(npl_closing) * 100.0) / SUM(total_closing) END), 2)
        FROM gabung
        ORDER BY CASE WHEN nama_unit = 'TOTAL KONSOLIDASI' THEN 1 ELSE 0 END, kode_unit ASC
    ";

    try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':closing_date', $closing_date);
        $stmt->bindValue(':harian_date', $harian_date);
        if ($kc_val) {
            $stmt->bindValue(':kc1', $kc_val);
            $stmt->bindValue(':kc2', $kc_val);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $grandTotal = array_pop($data);
        if(!$grandTotal) {
            $grandTotal = ['kode_unit'=>'','nama_unit'=>'TOTAL KONSOLIDASI','npl_closing'=>0,'npl_harian'=>0,'selisih_npl'=>0,'npl_closing_persen'=>0,'npl_harian_persen'=>0,'selisih_npl_persen'=>0];
        }

        sendResponse(200, "Sukses menghitung berdasarkan $colValue", ['data' => $data, 'grand_total' => $grandTotal]);
    } catch (Exception $e) {
        sendResponse(500, "Error: " . $e->getMessage());
    }
}

    public function getRecoveryNPL($input = []) {
        $closing_date = isset($input['closing_date']) ? $input['closing_date'] : date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = isset($input['harian_date'])  ? $input['harian_date']  : date('Y-m-d');
        $kc           = $this->normalizeKodeKantor($input['kode_kantor'] ?? null);
        $korwilRange  = $kc ? null : $this->korwilRange($input['korwil'] ?? null);

        if ($kc) {
            $colKey = "kode_group1";
            $selectName = "COALESCE(k.deskripsi_group1, CONCAT('Kankas ', r.kode_key)) AS nama_kantor";
            $joinName = "LEFT JOIN kankas k ON r.kode_key = k.kode_group1";
            $filterClosing = "AND kode_cabang = :kc_closing";
            $filterClosingAll = "AND kode_cabang = :kc_closing_all_filter";
            $filterHarian = "AND kode_cabang = :kc_harian";
            $filterMaster = "WHERE k.kode_kantor = :kc_master";
            $masterSql = "SELECT k.kode_group1 AS kode_key, COALESCE(k.deskripsi_group1, CONCAT('Kankas ', k.kode_group1)) AS nama_kantor FROM kankas k $filterMaster";
        } else {
            $colKey = "kode_cabang";
            $selectName = "COALESCE(k.nama_kantor, r.kode_key) AS nama_kantor";
            $joinName = "LEFT JOIN kode_kantor k ON r.kode_key = k.kode_kantor";
            $filterClosing = "";
            $filterClosingAll = "";
            $filterHarian = "";
            $filterMaster = "WHERE kode_kantor <> '000'";
            if ($korwilRange) {
                $filterMaster .= " AND kode_kantor BETWEEN :kw_master_start AND :kw_master_end";
                $filterClosing = "AND kode_cabang BETWEEN :kw_closing_start AND :kw_closing_end";
                $filterClosingAll = "AND kode_cabang BETWEEN :kw_closing_all_start AND :kw_closing_all_end";
                $filterHarian = "AND kode_cabang BETWEEN :kw_harian_start AND :kw_harian_end";
            }
            $masterSql = "SELECT kode_kantor AS kode_key, nama_kantor FROM kode_kantor $filterMaster";
        }

        $sql = "
            WITH closing AS (
                SELECT 
                    no_rekening,
                    kode_cabang,
                    kode_group1,
                    nama_nasabah,
                    kolektibilitas,
                    baki_debet
                FROM nominatif
                WHERE created = :closing1
                AND kolektibilitas IN ('KL', 'D', 'M')
                $filterClosing
            ),
            closing_all AS (
                SELECT
                    no_rekening,
                    kode_cabang,
                    kode_group1,
                    kolektibilitas,
                    baki_debet
                FROM nominatif
                WHERE created = :closing_all
                $filterClosingAll
            ),
            harian AS (
                SELECT 
                    no_rekening,
                    kode_cabang,
                    kode_group1,
                    kolektibilitas,
                    baki_debet,
                    tgl_jatuh_tempo
                FROM nominatif
                WHERE created = :harian1
                $filterHarian
            ),
            master_area AS (
                $masterSql
            ),
            event_recovery AS (
                SELECT 
                    c.$colKey AS kode_key,
                    c.baki_debet AS npl_closing_nom,
                    CASE WHEN h.no_rekening IS NOT NULL AND h.kolektibilitas IN ('KL','D','M') THEN h.baki_debet ELSE 0 END AS npl_harian_nom,
                    CASE WHEN h.no_rekening IS NULL THEN 1 ELSE 0 END AS noa_lunas,
                    CASE WHEN h.no_rekening IS NULL THEN c.baki_debet ELSE 0 END AS baki_debet_lunas,
                    CASE WHEN h.no_rekening IS NOT NULL AND h.kolektibilitas IN ('L','DP') THEN 1 ELSE 0 END AS noa_backflow,
                    CASE WHEN h.no_rekening IS NOT NULL AND h.kolektibilitas IN ('L','DP') THEN h.baki_debet ELSE 0 END AS baki_debet_backflow,
                    CASE WHEN h.no_rekening IS NOT NULL AND c.baki_debet > h.baki_debet THEN 1 ELSE 0 END AS noa_angsuran_npl,
                    CASE WHEN h.no_rekening IS NOT NULL AND c.baki_debet > h.baki_debet THEN c.baki_debet - h.baki_debet ELSE 0 END AS baki_debet_angsuran_npl,
                    CASE WHEN h.no_rekening IS NOT NULL AND h.kolektibilitas IN ('KL','D','M') AND h.baki_debet > c.baki_debet THEN h.baki_debet - c.baki_debet ELSE 0 END AS baki_debet_existing_naik
                FROM closing c
                LEFT JOIN harian h ON h.no_rekening = c.no_rekening
            ),
            event_flow AS (
                SELECT
                    h.$colKey AS kode_key,
                    COUNT(*) AS noa_flow_npl,
                    SUM(h.baki_debet) AS baki_debet_flow_npl
                FROM harian h
                LEFT JOIN closing_all ca ON ca.no_rekening = h.no_rekening
                WHERE h.kolektibilitas IN ('KL','D','M')
                AND (ca.no_rekening IS NULL OR ca.kolektibilitas NOT IN ('KL','D','M'))
                GROUP BY h.$colKey
            ),
            rekap_raw AS (
                SELECT
                    kode_key,
                    SUM(npl_closing_nom) AS npl_closing,
                    SUM(npl_harian_nom) AS npl_existing_harian,
                    SUM(noa_lunas) AS noa_lunas,
                    SUM(baki_debet_lunas) AS baki_debet_lunas,
                    SUM(noa_backflow) AS noa_backflow,
                    SUM(baki_debet_backflow) AS baki_debet_backflow,
                    SUM(noa_angsuran_npl) AS noa_angsuran_npl,
                    SUM(baki_debet_angsuran_npl) AS baki_debet_angsuran_npl,
                    SUM(baki_debet_existing_naik) AS baki_debet_existing_naik
                FROM event_recovery
                GROUP BY kode_key
            ),
            rekap_cabang AS (
                SELECT
                    m.kode_key AS kode_cabang,
                    m.nama_kantor,
                    COALESCE(r.npl_closing, 0) AS npl_closing,
                    COALESCE(r.npl_existing_harian, 0) + COALESCE(f.baki_debet_flow_npl, 0) AS npl_harian,
                    COALESCE(f.noa_flow_npl, 0) AS noa_flow_npl,
                    COALESCE(f.baki_debet_flow_npl, 0) AS baki_debet_flow_npl,
                    COALESCE(r.noa_lunas, 0) AS noa_lunas,
                    COALESCE(r.baki_debet_lunas, 0) AS baki_debet_lunas,
                    COALESCE(r.noa_backflow, 0) AS noa_backflow,
                    COALESCE(r.baki_debet_backflow, 0) AS baki_debet_backflow,
                    COALESCE(r.noa_angsuran_npl, 0) AS noa_angsuran_npl,
                    COALESCE(r.baki_debet_angsuran_npl, 0) AS baki_debet_angsuran_npl,
                    COALESCE(r.baki_debet_existing_naik, 0) AS baki_debet_existing_naik
                FROM master_area m
                LEFT JOIN rekap_raw r ON m.kode_key = r.kode_key
                LEFT JOIN event_flow f ON m.kode_key = f.kode_key
            )
            SELECT 
                kode_cabang,
                nama_kantor,
                npl_closing,
                npl_harian,
                npl_harian - npl_closing AS selisih_os_npl,
                noa_flow_npl,
                baki_debet_flow_npl,
                noa_lunas,
                baki_debet_lunas,
                noa_backflow,
                baki_debet_backflow,
                noa_angsuran_npl,
                baki_debet_angsuran_npl,
                baki_debet_existing_naik,
                (noa_lunas + noa_backflow + noa_angsuran_npl) AS total_noa_recovery,
                (baki_debet_lunas + baki_debet_backflow + baki_debet_angsuran_npl) AS total_recovery,
                (baki_debet_flow_npl + baki_debet_existing_naik - (baki_debet_lunas + baki_debet_backflow + baki_debet_angsuran_npl)) AS net_flow_recovery
            FROM rekap_cabang

            UNION ALL

            SELECT 
                'TOTAL',
                'TOTAL KONSOLIDASI',
                SUM(npl_closing),
                SUM(npl_harian),
                SUM(npl_harian - npl_closing),
                SUM(noa_flow_npl),
                SUM(baki_debet_flow_npl),
                SUM(noa_lunas),
                SUM(baki_debet_lunas),
                SUM(noa_backflow),
                SUM(baki_debet_backflow),
                SUM(noa_angsuran_npl),
                SUM(baki_debet_angsuran_npl),
                SUM(baki_debet_existing_naik),
                SUM(noa_lunas + noa_backflow + noa_angsuran_npl),
                SUM(baki_debet_lunas + baki_debet_backflow + baki_debet_angsuran_npl),
                SUM(baki_debet_flow_npl + baki_debet_existing_naik - (baki_debet_lunas + baki_debet_backflow + baki_debet_angsuran_npl))
            FROM rekap_cabang

            ORDER BY 
                CASE WHEN kode_cabang = 'TOTAL' THEN 1 ELSE 0 END,
                kode_cabang;
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':closing1', $closing_date);
        $stmt->bindValue(':closing_all', $closing_date);
        $stmt->bindValue(':harian1', $harian_date);
        if ($kc) {
            $stmt->bindValue(':kc_closing', $kc);
            $stmt->bindValue(':kc_closing_all_filter', $kc);
            $stmt->bindValue(':kc_harian', $kc);
            $stmt->bindValue(':kc_master', $kc);
        } elseif ($korwilRange) {
            $stmt->bindValue(':kw_master_start', $korwilRange[0]);
            $stmt->bindValue(':kw_master_end', $korwilRange[1]);
            $stmt->bindValue(':kw_closing_start', $korwilRange[0]);
            $stmt->bindValue(':kw_closing_end', $korwilRange[1]);
            $stmt->bindValue(':kw_closing_all_start', $korwilRange[0]);
            $stmt->bindValue(':kw_closing_all_end', $korwilRange[1]);
            $stmt->bindValue(':kw_harian_start', $korwilRange[0]);
            $stmt->bindValue(':kw_harian_end', $korwilRange[1]);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, "Berhasil ambil data Recovery NPL", $data);
    }


    // public function getTop25NplPerCabang($input) {
    //     $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
    //     $kode_cabang  = $input['kode_cabang'] ?? null;

    //     // SQL dasar
    //     $sql = "
    //         SELECT 
    //             n.no_rekening,
    //             n.nama_nasabah,
    //             n.kode_cabang,
    //             k.nama_kantor,
    //             n.kolektibilitas,
    //             n.baki_debet,
    //             n.tunggakan_pokok,
    //             n.tunggakan_bunga,
    //             n.tgl_realisasi
    //         FROM nominatif n
    //         LEFT JOIN kode_kantor k ON n.kode_cabang = k.kode_kantor
    //         WHERE n.created = :closing_date
    //         AND n.kolektibilitas IN ('KL', 'D', 'M')
    //     ";

    //     // Filter cabang jika ada
    //     if (!empty($kode_cabang)) {
    //         $sql .= " AND n.kode_cabang = :kode_cabang";
    //     }

    //     $sql .= " ORDER BY n.baki_debet DESC LIMIT 25";

    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->bindValue(':closing_date', $closing_date);
    //     if (!empty($kode_cabang)) {
    //         $stmt->bindValue(':kode_cabang', $kode_cabang);
    //     }
    //     $stmt->execute();

    //     $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $msg = !empty($kode_cabang)
    //         ? "Top 25 NPL cabang $kode_cabang"
    //         : "Top 25 NPL konsolidasi";

    //     sendResponse(200, $msg, $data);
    // }

public function getTop25NplPerCabang($input) {
    $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
    $harian_date  = $input['harian_date'] ?? date('Y-m-d');
    $kode_cabang  = $input['kode_cabang'] ?? null;
    $start_month  = date('Y-m-01', strtotime($harian_date));

    try {
        // 1. Buat filter dinamis untuk CTE dan Query Utama
        $branchFilter = "";
        if (!empty($kode_cabang) && $kode_cabang !== '000') {
            $branchFilter = " AND kode_cabang = :kc_filter ";
        }

        // 2. Query dengan hitungan Persen Kontribusi
        $sql = "
            WITH total_npl AS (
                SELECT SUM(baki_debet) as grand_total_bd_npl
                FROM nominatif
                WHERE created = :closing_date
                AND kolektibilitas IN ('KL', 'D', 'M')
                $branchFilter
            ),
            top25 AS (
                SELECT 
                    n.no_rekening, n.nama_nasabah, n.kode_cabang, k.nama_kantor,
                    n.jml_pinjaman, n.kolektibilitas AS kolek_closing, n.baki_debet
                FROM nominatif n
                LEFT JOIN kode_kantor k ON n.kode_cabang = k.kode_kantor
                WHERE n.created = :closing_date_2
                AND n.kolektibilitas IN ('KL', 'D', 'M')
                " . str_replace(':kc_filter', ':kc_filter_2', $branchFilter) . "
                ORDER BY n.baki_debet DESC
                LIMIT 25
            )
            SELECT 
                t25.*,
                -- Hitung % kontribusi debitur terhadap Total NPL Cabang/Pusat
                CASE 
                    WHEN tn.grand_total_bd_npl > 0 
                    THEN ROUND((t25.baki_debet / tn.grand_total_bd_npl) * 100, 2)
                    ELSE 0 
                END AS persen_npl,

                nh.kolektibilitas AS kolek_harian,
                nh.baki_debet AS baki_debet_harian,
                nh.tunggakan_pokok, nh.tunggakan_bunga,
                COALESCE(SUM(tk.angsuran_pokok), 0) AS total_pokok,
                COALESCE(SUM(tk.angsuran_bunga), 0) AS total_bunga,
                MAX(tk.tgl_trans) AS tgl_trans

            FROM top25 t25
            CROSS JOIN total_npl tn
            LEFT JOIN nominatif nh ON t25.no_rekening = nh.no_rekening AND nh.created = :harian_date
            LEFT JOIN transaksi_kredit tk ON t25.no_rekening = tk.no_rekening AND tk.tgl_trans BETWEEN :start_month AND :end_date
            GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12
            ORDER BY t25.baki_debet DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        
        // BINDING PARAMETER (Harus Unik karena PDO tidak suka nama parameter sama di query yang sama)
        $stmt->bindValue(':closing_date', $closing_date);
        $stmt->bindValue(':closing_date_2', $closing_date);
        $stmt->bindValue(':harian_date', $harian_date);
        $stmt->bindValue(':start_month', $start_month);
        $stmt->bindValue(':end_date', $harian_date);

        if (!empty($kode_cabang) && $kode_cabang !== '000') {
            $val_kc = str_pad((string)$kode_cabang, 3, '0', STR_PAD_LEFT);
            $stmt->bindValue(':kc_filter', $val_kc);
            $stmt->bindValue(':kc_filter_2', $val_kc);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, "Top 25 NPL Sukses", $data);

    } catch (Exception $e) {
        sendResponse(500, "Error DB: " . $e->getMessage());
    }
}

    
    public function getDetailRecoveryNpl($input = []) {
        $kode_kantor  = $this->normalizeKodeKantor($input['kode_kantor'] ?? null);
        $kode_kankas  = trim((string)($input['kode_kankas'] ?? ''));
        $korwilRange  = $kode_kantor ? null : $this->korwilRange($input['korwil'] ?? null);
        $closing_date = isset($input['closing_date']) ? $input['closing_date'] : date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = isset($input['harian_date']) ? $input['harian_date'] : date('Y-m-d');
        $type         = isset($input['type']) ? strtolower($input['type']) : null; // lunas / backflow / angsuran / total_recovery
        $jtStatus     = strtolower(trim((string)($input['jt_status'] ?? 'all')));

        if (!$type || !in_array($type, ['lunas', 'backflow', 'angsuran', 'total_recovery'], true)) {
            sendResponse(400, "Tipe harus 'lunas', 'backflow', 'angsuran', atau 'total_recovery'.");
            return;
        }

        $scopeClosing = "";
        $scopeHarian = "";
        if ($kode_kankas !== '') {
            $scopeClosing = " AND c.kode_group1 = :kode_kankas_c ";
            $scopeHarian = " AND h.kode_group1 = :kode_kankas_h ";
        } elseif ($kode_kantor) {
            $scopeClosing = " AND c.kode_cabang = :kode_kantor_c ";
            $scopeHarian = " AND h.kode_cabang = :kode_kantor_h ";
        } elseif ($korwilRange) {
            $scopeClosing = " AND c.kode_cabang BETWEEN :kw_start_c AND :kw_end_c ";
            $scopeHarian = " AND h.kode_cabang BETWEEN :kw_start_h AND :kw_end_h ";
        }

        $jtFilter = "";
        if ($type === 'backflow' && $jtStatus === 'sudah') {
            $jtFilter = " AND h.tgl_jatuh_tempo IS NOT NULL AND DAY(h.tgl_jatuh_tempo) <= DAY(:harian_jt) ";
        } elseif ($type === 'backflow' && $jtStatus === 'belum') {
            $jtFilter = " AND (h.tgl_jatuh_tempo IS NULL OR DAY(h.tgl_jatuh_tempo) > DAY(:harian_jt)) ";
        }

        $selectCommon = "
            d.jenis_recovery,
            d.no_rekening,
            d.nama_nasabah,
            d.baki_debet_closing,
            d.baki_debet,
            d.recovery_nominal,
            d.kolek,
            d.kolek_update,
            d.kode_cabang,
            d.kode_group1,
            d.tgl_jatuh_tempo,
            CASE
                WHEN d.tgl_jatuh_tempo IS NULL THEN 'Belum Angsuran'
                WHEN DAY(d.tgl_jatuh_tempo) <= DAY(:harian_date_label) THEN 'Sudah Angsuran'
                ELSE 'Potensi Flow'
            END AS jt_status,
            trx.tgl_trans,
            trx.angsuran_pokok,
            trx.angsuran_bunga,
            trx.angsuran_denda
        ";

        $awal_date = $closing_date;
        $trxKodeKantor = $kode_kantor ?: ($kode_kankas !== '' ? substr($kode_kankas, 0, 3) : null);
        $trxScope = $trxKodeKantor ? " AND kode_kantor = :kode_kantor_trx " : "";

        $baseCte = "
            WITH closing AS (
                SELECT c.no_rekening, c.nama_nasabah, c.baki_debet, c.kolektibilitas, c.kode_cabang, c.kode_group1
                FROM nominatif c
                WHERE c.created = :closing_date
                AND c.kolektibilitas IN ('KL', 'D', 'M')
                $scopeClosing
            ),
            harian AS (
                SELECT h.no_rekening, h.nama_nasabah, h.baki_debet, h.kolektibilitas, h.kode_cabang, h.kode_group1, h.tgl_jatuh_tempo
                FROM nominatif h
                WHERE h.created = :harian_date
                $scopeHarian
            ),
            trx AS (
                SELECT 
                    no_rekening,
                    MAX(tgl_trans) AS tgl_trans,
                    SUM(angsuran_pokok) AS angsuran_pokok,
                    SUM(angsuran_bunga) AS angsuran_bunga,
                    SUM(angsuran_denda) AS angsuran_denda
                FROM transaksi_kredit
                WHERE tgl_trans BETWEEN :awal_date AND :harian_date_trx
                $trxScope
                GROUP BY no_rekening
            )
        ";

        if ($type === 'backflow') {
            $sql = $baseCte . "
                SELECT $selectCommon
                FROM (
                    SELECT
                        h.no_rekening,
                        h.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        h.baki_debet,
                        h.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        h.kolektibilitas AS kolek_update,
                        h.kode_cabang,
                        h.kode_group1,
                        h.tgl_jatuh_tempo,
                        'Backflow' AS jenis_recovery
                    FROM harian h
                    JOIN closing c ON h.no_rekening = c.no_rekening
                    WHERE h.kolektibilitas IN ('L', 'DP')
                    $jtFilter
                ) d
                LEFT JOIN trx ON d.no_rekening = trx.no_rekening
                ORDER BY d.recovery_nominal DESC
            ";
        } elseif ($type === 'angsuran') {
            $sql = $baseCte . "
                SELECT $selectCommon
                FROM (
                    SELECT
                        h.no_rekening,
                        h.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        h.baki_debet,
                        c.baki_debet - h.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        h.kolektibilitas AS kolek_update,
                        h.kode_cabang,
                        h.kode_group1,
                        h.tgl_jatuh_tempo,
                        'Angsuran NPL' AS jenis_recovery
                    FROM harian h
                    JOIN closing c ON h.no_rekening = c.no_rekening
                    WHERE c.baki_debet > h.baki_debet
                ) d
                LEFT JOIN trx ON d.no_rekening = trx.no_rekening
                ORDER BY d.recovery_nominal DESC
            ";
        } elseif ($type === 'lunas') {
            $sql = $baseCte . "
                SELECT $selectCommon
                FROM (
                    SELECT
                        c.no_rekening,
                        c.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        c.baki_debet AS baki_debet,
                        c.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        'Lunas' AS kolek_update,
                        c.kode_cabang,
                        c.kode_group1,
                        NULL AS tgl_jatuh_tempo,
                        'Lunas NPL' AS jenis_recovery
                    FROM closing c
                    LEFT JOIN harian h ON h.no_rekening = c.no_rekening
                    WHERE h.no_rekening IS NULL
                ) d
                LEFT JOIN trx ON d.no_rekening = trx.no_rekening
                ORDER BY d.recovery_nominal DESC
            ";
        } elseif ($type === 'total_recovery') {
            $sql = $baseCte . "
                SELECT $selectCommon
                FROM (
                    SELECT
                        c.no_rekening,
                        c.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        c.baki_debet AS baki_debet,
                        c.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        'Lunas' AS kolek_update,
                        c.kode_cabang,
                        c.kode_group1,
                        NULL AS tgl_jatuh_tempo,
                        'Lunas NPL' AS jenis_recovery
                    FROM closing c
                    LEFT JOIN harian h ON h.no_rekening = c.no_rekening
                    WHERE h.no_rekening IS NULL

                    UNION ALL

                    SELECT
                        h.no_rekening,
                        h.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        h.baki_debet,
                        h.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        h.kolektibilitas AS kolek_update,
                        h.kode_cabang,
                        h.kode_group1,
                        h.tgl_jatuh_tempo,
                        'Backflow' AS jenis_recovery
                    FROM harian h
                    JOIN closing c ON h.no_rekening = c.no_rekening
                    WHERE h.kolektibilitas IN ('L', 'DP')

                    UNION ALL

                    SELECT
                        h.no_rekening,
                        h.nama_nasabah,
                        c.baki_debet AS baki_debet_closing,
                        h.baki_debet,
                        c.baki_debet - h.baki_debet AS recovery_nominal,
                        c.kolektibilitas AS kolek,
                        h.kolektibilitas AS kolek_update,
                        h.kode_cabang,
                        h.kode_group1,
                        h.tgl_jatuh_tempo,
                        'Angsuran NPL' AS jenis_recovery
                    FROM harian h
                    JOIN closing c ON h.no_rekening = c.no_rekening
                    WHERE c.baki_debet > h.baki_debet
                ) d
                LEFT JOIN trx ON d.no_rekening = trx.no_rekening
                ORDER BY d.recovery_nominal DESC
            ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':closing_date', $closing_date);
        $stmt->bindValue(':harian_date', $harian_date);
        $stmt->bindValue(':harian_date_label', $harian_date);
        $stmt->bindValue(':awal_date', $awal_date);
        $stmt->bindValue(':harian_date_trx', $harian_date);

        if ($kode_kankas !== '') {
            $stmt->bindValue(':kode_kankas_c', $kode_kankas);
            $stmt->bindValue(':kode_kankas_h', $kode_kankas);
        } elseif ($kode_kantor) {
            $stmt->bindValue(':kode_kantor_c', $kode_kantor);
            $stmt->bindValue(':kode_kantor_h', $kode_kantor);
        } elseif ($korwilRange) {
            $stmt->bindValue(':kw_start_c', $korwilRange[0]);
            $stmt->bindValue(':kw_end_c', $korwilRange[1]);
            $stmt->bindValue(':kw_start_h', $korwilRange[0]);
            $stmt->bindValue(':kw_end_h', $korwilRange[1]);
        }
        if ($trxKodeKantor) {
            $stmt->bindValue(':kode_kantor_trx', $trxKodeKantor);
        }
        if ($type === 'backflow' && in_array($jtStatus, ['sudah', 'belum'], true)) {
            $stmt->bindValue(':harian_jt', $harian_date);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse(200, "Berhasil ambil detail debitur $type", $data);
    }

    public function getPotensiNplRekap($input = [])
    {
        $harian_date = !empty($input['harian_date']) 
            ? date('Y-m-d', strtotime($input['harian_date'])) 
            : date('Y-m-d');

        $closing_date = !empty($input['closing_date']) 
            ? date('Y-m-d', strtotime($input['closing_date'])) 
            : date('Y-m-t', strtotime($harian_date . ' -1 month'));

        $awalBulan  = date('Y-m-01', strtotime($harian_date));
        $akhirBulan = date('Y-m-t', strtotime($harian_date));

        // --- LOGIC HARI DINAMIS ---
        $hari_bulan = (int) date('t', strtotime($harian_date)); // Total hari di bulan ini (misal 30 atau 31)
        $tgl_harian = (int) date('d', strtotime($harian_date)); // Tanggal hari ini
        $sisa_hari  = $hari_bulan - $tgl_harian;                // Sisa hari menuju akhir bulan
        if ($sisa_hari < 0) $sisa_hari = 0;

        $jt_start = date('Y-m-15', strtotime($harian_date . ' -1 month'));
        $jt_end   = $akhirBulan;

        $kc = $input['kode_kantor'] ?? null;
        if ($kc === '000' || $kc === '') $kc = null;

        // --- LOGIC MASTER TABLE (Agar Kantor 0 tetap tampil) ---
        if ($kc) {
            $masterTable  = "kankas k";
            $colKey       = "kode_group1"; 
            $selectName   = "k.deskripsi_group1 AS nama_cabang";
            $filterMaster = "WHERE k.kode_kantor = :kc_master";
            $joinKey      = "k.kode_group1";
            $filterHarian = "AND h.kode_cabang = :kc1";
            $filterClosing = "AND n.kode_cabang = :kc2";
            $kc_val       = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        } else {
            $masterTable  = "kode_kantor k";
            $colKey       = "kode_cabang";
            $selectName   = "k.nama_kantor AS nama_cabang";
            $filterMaster = "WHERE k.kode_kantor <> '000'";
            $joinKey      = "k.kode_kantor";
            $filterHarian = "";
            $filterClosing = "";
            $kc_val       = null;
        }

        $sql = "
            SELECT
                m.kode_unit AS kode_cabang,
                m.nama_cabang,
                COALESCE(r.total_noa, 0) AS total_noa,
                COALESCE(r.total_baki, 0) AS total_baki,
                COALESCE(r.noa_aman, 0) AS noa_aman,
                COALESCE(r.baki_aman, 0) AS baki_aman,
                COALESCE(r.noa_jt, 0) AS noa_jt,
                COALESCE(r.baki_jt, 0) AS baki_jt,
                COALESCE(r.noa_flow, 0) AS noa_flow,
                COALESCE(r.baki_flow, 0) AS baki_flow,
                COALESCE(r.noa_potensi, 0) AS noa_potensi,
                COALESCE(r.baki_potensi, 0) AS baki_potensi
            FROM (
                SELECT $joinKey AS kode_unit, $selectName
                FROM $masterTable
                $filterMaster
            ) m
            LEFT JOIN (
                SELECT
                    h.$colKey AS kode_join,
                    COUNT(h.no_rekening) AS total_noa,
                    SUM(COALESCE(h.baki_debet, 0)) AS total_baki,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND (h.tgl_jatuh_tempo < :ab1 OR h.tgl_jatuh_tempo > :ak1 OR h.tgl_jatuh_tempo IS NULL) AND (COALESCE(h.hari_menunggak,0) + :sh1) < 90 AND (COALESCE(h.hari_menunggak_pokok,0) + :sh2) < 90 AND (COALESCE(h.hari_menunggak_bunga,0) + :sh3) < 90 THEN 1 ELSE 0 END) AS noa_aman,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND (h.tgl_jatuh_tempo < :ab2 OR h.tgl_jatuh_tempo > :ak2 OR h.tgl_jatuh_tempo IS NULL) AND (COALESCE(h.hari_menunggak,0) + :sh4) < 90 AND (COALESCE(h.hari_menunggak_pokok,0) + :sh5) < 90 AND (COALESCE(h.hari_menunggak_bunga,0) + :sh6) < 90 THEN COALESCE(h.baki_debet,0) ELSE 0 END) AS baki_aman,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND h.tgl_jatuh_tempo BETWEEN :ab3 AND :ak3 THEN 1 ELSE 0 END) AS noa_jt,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND h.tgl_jatuh_tempo BETWEEN :ab4 AND :ak4 THEN COALESCE(h.baki_debet,0) ELSE 0 END) AS baki_jt,
                    SUM(CASE WHEN h.kolektibilitas IN ('KL','D','M') THEN 1 ELSE 0 END) AS noa_flow,
                    SUM(CASE WHEN h.kolektibilitas IN ('KL','D','M') THEN COALESCE(h.baki_debet,0) ELSE 0 END) AS baki_flow,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND (h.tgl_jatuh_tempo < :ab5 OR h.tgl_jatuh_tempo > :ak5 OR h.tgl_jatuh_tempo IS NULL) AND ((COALESCE(h.hari_menunggak,0) + :sh7) >= 90 OR (COALESCE(h.hari_menunggak_pokok,0) + :sh8) >= 90 OR (COALESCE(h.hari_menunggak_bunga,0) + :sh9) >= 90) THEN 1 ELSE 0 END) AS noa_potensi,
                    SUM(CASE WHEN h.kolektibilitas NOT IN ('KL','D','M') AND (h.tgl_jatuh_tempo < :ab6 OR h.tgl_jatuh_tempo > :ak6 OR h.tgl_jatuh_tempo IS NULL) AND ((COALESCE(h.hari_menunggak,0) + :sh10) >= 90 OR (COALESCE(h.hari_menunggak_pokok,0) + :sh11) >= 90 OR (COALESCE(h.hari_menunggak_bunga,0) + :sh12) >= 90) THEN COALESCE(h.baki_debet,0) ELSE 0 END) AS baki_potensi
                FROM nominatif n
                JOIN nominatif h
                    ON h.created = :harian_date
                    AND h.no_rekening = n.no_rekening
                    $filterHarian
                WHERE n.created = :closing
                    AND n.kolektibilitas IN ('L', 'DP')
                    $filterClosing
                    AND (
                           (COALESCE(n.hari_menunggak,0) + :hb1) >= 90
                        OR (COALESCE(n.hari_menunggak_pokok,0) + :hb2) >= 90
                        OR (COALESCE(n.hari_menunggak_bunga,0) + :hb3) >= 90
                        OR n.tgl_jatuh_tempo BETWEEN :jt_start AND :jt_end
                    )
                GROUP BY h.$colKey
            ) r ON r.kode_join = m.kode_unit
            ORDER BY m.kode_unit ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing',     $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            $stmt->bindValue(':hb1', $hari_bulan, PDO::PARAM_INT);
            $stmt->bindValue(':hb2', $hari_bulan, PDO::PARAM_INT);
            $stmt->bindValue(':hb3', $hari_bulan, PDO::PARAM_INT);
            $stmt->bindValue(':jt_start', $jt_start);
            $stmt->bindValue(':jt_end',   $jt_end);

            // Looping Parameter Binding untuk Case (Mencegah error HY093)
            for ($i=1; $i<=6; $i++) {
                $stmt->bindValue(":ab$i", $awalBulan);
                $stmt->bindValue(":ak$i", $akhirBulan);
            }
            for ($i=1; $i<=12; $i++) {
                $stmt->bindValue(":sh$i", $sisa_hari, PDO::PARAM_INT);
            }
            
            if ($kc_val) {
                $stmt->bindValue(':kc_master', $kc_val);
                $stmt->bindValue(':kc1', $kc_val);
                $stmt->bindValue(':kc2', $kc_val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grandTotal = [
                'kode_cabang' => '',
                'nama_cabang' => $kc_val ? 'TOTAL' : 'TOTAL KONSOLIDASI',
                'total_noa' => 0,
                'total_baki' => 0,
                'noa_aman' => 0,
                'baki_aman' => 0,
                'noa_jt' => 0,
                'baki_jt' => 0,
                'noa_flow' => 0,
                'baki_flow' => 0,
                'noa_potensi' => 0,
                'baki_potensi' => 0,
            ];
            foreach ($rows as $row) {
                foreach ($grandTotal as $key => $value) {
                    if ($key === 'kode_cabang' || $key === 'nama_cabang') continue;
                    $grandTotal[$key] += (float)($row[$key] ?? 0);
                }
            }

            sendResponse(200, "Berhasil ambil Rekap Potensi NPL", [
                'data' => $rows, 
                'grand_total' => $grandTotal
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function getDetailPotensiNpl($input = [])
    {
        $kode_kantor = isset($input['kode_kantor']) && $input['kode_kantor'] !== ''
            ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT)
            : null;
            
        $kode_kankas = $input['kode_kankas'] ?? '';
        $kode_ao = $input['kode_ao'] ?? '';

        $harian_date = !empty($input['harian_date'])
            ? date('Y-m-d', strtotime($input['harian_date']))
            : date('Y-m-d');
            
        $closing_date = !empty($input['closing_date'])
            ? date('Y-m-d', strtotime($input['closing_date']))
            : (new DateTime($harian_date))->modify('first day of this month')->modify('-1 day')->format('Y-m-d');

        $awal_date = !empty($input['awal_date'])
            ? date('Y-m-d', strtotime($input['awal_date']))
            : date('Y-m-01', strtotime($harian_date));

        $bulan_awal      = date('Y-m-01', strtotime($harian_date));
        $bulan_akhir     = date('Y-m-t',  strtotime($harian_date));
        
        $jml_hari_bulan = (int) date('t', strtotime($harian_date));
        $tgl_harian     = (int) date('d', strtotime($harian_date));
        $sisa_hari      = $jml_hari_bulan - $tgl_harian;
        if ($sisa_hari < 0) $sisa_hari = 0;

        $filterKantorClosing = $kode_kantor && $kode_kantor !== '000' ? " AND n.kode_cabang = :kode_kantor_c " : "";
        $filterKankasClosing = $kode_kankas !== '' ? " AND n.kode_group1 = :kode_kankas " : "";
        $filterAoClosing     = $kode_ao !== '' ? " AND n.kode_group2 = :kode_ao " : "";
        
        $filterKantorTrx     = $kode_kantor && $kode_kantor !== '000' ? " AND t.kode_kantor = :kode_kantor_trx " : "";

        $sql = "
            WITH kandidat AS (
                SELECT
                    n.no_rekening,
                    n.kode_cabang,
                    n.kode_group1,
                    n.kode_group2,
                    n.nama_nasabah,
                    n.alamat,
                    n.kolektibilitas AS kolek_closing,
                    n.baki_debet     AS baki_debet_closing,
                    COALESCE(n.hari_menunggak,0)        AS hm_closing,
                    COALESCE(n.hari_menunggak_pokok,0)  AS hmp_closing,
                    COALESCE(n.hari_menunggak_bunga,0)  AS hmb_closing,
                    n.tgl_jatuh_tempo AS jt_closing,
                    n.tgl_realisasi
                FROM nominatif n
                WHERE n.created = :closing_date
                AND n.kolektibilitas IN ('L','DP')
                {$filterKantorClosing}
                {$filterKankasClosing}
                {$filterAoClosing}
                AND (
                        (COALESCE(n.hari_menunggak,0)       + :jml_hari1) >= 90
                    OR (COALESCE(n.hari_menunggak_pokok,0) + :jml_hari2) >= 90
                    OR (COALESCE(n.hari_menunggak_bunga,0) + :jml_hari3) >= 90
                    OR (n.tgl_jatuh_tempo BETWEEN :bulan_awal1 AND :bulan_akhir1)
                )
            ),
            harian AS (
                SELECT
                    h.no_rekening,
                    h.kolektibilitas AS kolek_harian,
                    h.baki_debet     AS baki_debet_harian,
                    COALESCE(h.tunggakan_pokok,0)       AS tunggakan_pokok,
                    COALESCE(h.tunggakan_bunga,0)       AS tunggakan_bunga,
                    COALESCE(h.hari_menunggak,0)        AS hm_harian,
                    COALESCE(h.hari_menunggak_pokok,0)  AS hmp_harian,
                    COALESCE(h.hari_menunggak_bunga,0)  AS hmb_harian,
                    h.tgl_jatuh_tempo AS jt_harian,
                    h.norek_tabungan
                FROM nominatif h
                WHERE h.created = :harian_date
            ),
            trx AS (
                SELECT
                    t.no_rekening,
                    MAX(t.tgl_trans)        AS tgl_trans_terakhir,
                    SUM(t.angsuran_pokok)   AS angsuran_pokok,
                    SUM(t.angsuran_bunga)   AS angsuran_bunga,
                    SUM(t.angsuran_denda)   AS angsuran_denda
                FROM transaksi_kredit t
                WHERE t.tgl_trans BETWEEN :awal_date AND :harian_date_trx
                {$filterKantorTrx}
                GROUP BY t.no_rekening
            )
            SELECT
                kd.kode_cabang,
                kk.nama_kantor,
                kas.deskripsi_group1 AS nama_kankas,
                kd.no_rekening,
                kd.nama_nasabah,
                kd.alamat,
                a.nama_ao, -- 🔥 Ambil Nama AO dari tabel ao_kredit
                kd.kolek_closing,
                kd.baki_debet_closing,
                COALESCE(h.kolek_harian, 'Lunas') AS kolek_harian,
                COALESCE(h.baki_debet_harian, 0) AS baki_debet_harian,
                h.tunggakan_pokok,
                h.tunggakan_bunga,
                (COALESCE(h.tunggakan_pokok, 0) + COALESCE(h.tunggakan_bunga, 0)) AS total_tunggakan,
                h.hm_harian,
                h.hmp_harian,
                h.hmb_harian,
                h.jt_harian,
                tb.saldo_akhir,
                CASE 
                    WHEN h.no_rekening IS NULL OR h.baki_debet_harian = 0 THEN 'LUNAS / AMAN'
                    WHEN h.kolek_harian IN ('KL','D','M') THEN 'FLOW KOLEK'
                    WHEN h.jt_harian BETWEEN :bulan_awal2 AND :bulan_akhir2 THEN 'JATUH TEMPO'
                    WHEN (h.hm_harian + :sisa_hari1) < 90 AND (h.hmp_harian + :sisa_hari2) < 90 AND (h.hmb_harian + :sisa_hari3) < 90 THEN 'AMAN'
                    ELSE 'MASIH POTENSI'
                END AS status_potensi,
                tr.tgl_trans_terakhir,
                tr.angsuran_pokok,
                tr.angsuran_bunga,
                tr.angsuran_denda,
                km.komitmen,
                km.tgl_pembayaran,
                km.nominal,
                km.alasan
            FROM kandidat kd
            LEFT JOIN harian h ON kd.no_rekening = h.no_rekening
            LEFT JOIN trx    tr ON kd.no_rekening = tr.no_rekening
            LEFT JOIN komitmen_flowpar km
                ON km.id = (
                    SELECT k2.id
                    FROM komitmen_flowpar k2
                    WHERE k2.no_rekening = kd.no_rekening
                      AND COALESCE(k2.updated, k2.created) >= :month_start_km
                      AND COALESCE(k2.updated, k2.created) < :next_month_start_km
                    ORDER BY COALESCE(k2.updated, k2.created) DESC, k2.id DESC
                    LIMIT 1
                )
            LEFT JOIN kode_kantor kk ON kd.kode_cabang = kk.kode_kantor
            LEFT JOIN kankas kas ON kd.kode_group1 = kas.kode_group1
            LEFT JOIN tabungan tb ON tb.no_rekening = h.norek_tabungan
            LEFT JOIN ao_kredit a ON kd.kode_group2 = a.kode_group2 AND kd.kode_cabang = a.kode_kantor -- 🔥 JOIN ke tabel ao_kredit
            WHERE kk.kode_kantor <> '000'
            ORDER BY kd.baki_debet_closing DESC, kd.no_rekening
        ";

        try {
            $st = $this->pdo->prepare($sql);
            $st->bindValue(':closing_date',    $closing_date);
            $st->bindValue(':harian_date',     $harian_date);
            $st->bindValue(':awal_date',       $awal_date);
            $st->bindValue(':harian_date_trx', $harian_date);
            $st->bindValue(':month_start_km', $bulan_awal);
            $st->bindValue(':next_month_start_km', date('Y-m-01', strtotime('+1 month', strtotime($harian_date))));
            
            $st->bindValue(':jml_hari1', $jml_hari_bulan, PDO::PARAM_INT);
            $st->bindValue(':jml_hari2', $jml_hari_bulan, PDO::PARAM_INT);
            $st->bindValue(':jml_hari3', $jml_hari_bulan, PDO::PARAM_INT);

            $st->bindValue(':sisa_hari1', $sisa_hari, PDO::PARAM_INT);
            $st->bindValue(':sisa_hari2', $sisa_hari, PDO::PARAM_INT);
            $st->bindValue(':sisa_hari3', $sisa_hari, PDO::PARAM_INT);

            $st->bindValue(':bulan_awal1',  $bulan_awal);
            $st->bindValue(':bulan_akhir1', $bulan_akhir);
            $st->bindValue(':bulan_awal2',  $bulan_awal);
            $st->bindValue(':bulan_akhir2', $bulan_akhir);

            if ($kode_kantor && $kode_kantor !== '000') {
                $st->bindValue(':kode_kantor_c',   $kode_kantor);
                $st->bindValue(':kode_kantor_trx', $kode_kantor);
            }
            if ($kode_kankas !== '') {
                $st->bindValue(':kode_kankas', $kode_kankas);
            }
            if ($kode_ao !== '') {
                $st->bindValue(':kode_ao', $kode_ao);
            }

            $st->execute();
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            
            $numericFields = [
                'baki_debet_closing', 'baki_debet_harian', 'tunggakan_pokok', 'tunggakan_bunga', 
                'total_tunggakan', 'hm_harian', 'hmp_harian', 'hmb_harian', 'angsuran_pokok', 'angsuran_bunga', 'saldo_akhir', 'nominal'
            ];
            foreach ($rows as &$r) {
                foreach ($numericFields as $f) {
                    if (isset($r[$f])) $r[$f] = 0 + $r[$f];
                }
            }
            unset($r);

            sendResponse(200, 'Detail potensi NPL', $rows);
        } catch (Throwable $e) {
            sendResponse(500, 'Gagal ambil detail potensi NPL: '.$e->getMessage());
        }
    }


 


    public function getBucket($input = [])
    {
        // snapshot nominatif
        $closing_date = !empty($input['closing_date'])
            ? date('Y-m-d', strtotime($input['closing_date']))
            : date('Y-m-d', strtotime('last day of previous month'));

        // optional: filter 1 cabang (biarkan null untuk konsolidasi)
        $kode_kantor = !empty($input['kode_kantor'])
            ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT)
            : null;

        $filterCabang = $kode_kantor ? " AND k.kode_kantor = :kode_kantor " : "";

        $sql = "
            WITH base AS (
                SELECT
                    n.kode_cabang,
                    COALESCE(n.hari_menunggak,0) AS hm,
                    COALESCE(n.baki_debet,0)     AS baki
                FROM nominatif n
                WHERE n.created = :closing_date
                -- TIDAK filter kolektibilitas, ambil semua
            ),
            agg AS (
                SELECT
                    k.kode_kantor AS kode_cabang,
                    k.nama_kantor AS nama_cabang,

                    -- 0–30
                    SUM(CASE WHEN b.hm BETWEEN 0   AND 30  THEN 1     ELSE 0 END) AS noa_0_30,
                    SUM(CASE WHEN b.hm BETWEEN 0   AND 30  THEN b.baki ELSE 0 END) AS baki_0_30,

                    -- 31–90
                    SUM(CASE WHEN b.hm BETWEEN 31  AND 90  THEN 1     ELSE 0 END) AS noa_31_90,
                    SUM(CASE WHEN b.hm BETWEEN 31  AND 90  THEN b.baki ELSE 0 END) AS baki_31_90,

                    -- 91–180
                    SUM(CASE WHEN b.hm BETWEEN 91  AND 180 THEN 1     ELSE 0 END) AS noa_91_180,
                    SUM(CASE WHEN b.hm BETWEEN 91  AND 180 THEN b.baki ELSE 0 END) AS baki_91_180,

                    -- 181–360
                    SUM(CASE WHEN b.hm BETWEEN 181 AND 360 THEN 1     ELSE 0 END) AS noa_181_360,
                    SUM(CASE WHEN b.hm BETWEEN 181 AND 360 THEN b.baki ELSE 0 END) AS baki_181_360,

                    -- >360
                    SUM(CASE WHEN b.hm > 360                  THEN 1   ELSE 0 END) AS noa_gt_360,
                    SUM(CASE WHEN b.hm > 360                  THEN b.baki ELSE 0 END) AS baki_gt_360

                FROM base b
                JOIN kode_kantor k ON k.kode_kantor = b.kode_cabang
                WHERE k.kode_kantor <> '000' $filterCabang
                GROUP BY k.kode_kantor, k.nama_kantor
            ),
            final AS (
                SELECT
                    a.*,
                    -- total
                    (noa_0_30 + noa_31_90 + noa_91_180 + noa_181_360 + noa_gt_360)   AS noa_total,
                    (baki_0_30 + baki_31_90 + baki_91_180 + baki_181_360 + baki_gt_360) AS baki_total
                FROM agg a
            )

            -- per cabang
            SELECT * FROM final

            UNION ALL

            -- total konsolidasi
            SELECT
                NULL AS kode_cabang,
                'TOTAL' AS nama_cabang,
                SUM(noa_0_30),  SUM(baki_0_30),
                SUM(noa_31_90), SUM(baki_31_90),
                SUM(noa_91_180),SUM(baki_91_180),
                SUM(noa_181_360),SUM(baki_181_360),
                SUM(noa_gt_360), SUM(baki_gt_360),
                SUM(noa_total),  SUM(baki_total)
            FROM final

            ORDER BY
                CASE WHEN nama_cabang='TOTAL' THEN 1 ELSE 0 END,
                kode_cabang
        ";

        try {
            $st = $this->pdo->prepare($sql);
            $st->bindValue(':closing_date', $closing_date);
            if ($kode_kantor) {
                $st->bindValue(':kode_kantor', $kode_kantor);
            }
            $st->execute();
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'OK - Rekap baket hari_menunggak', $rows);
        } catch (Throwable $e) {
            sendResponse(500, 'Gagal ambil rekap: '.$e->getMessage(), null);
        }
    }

    
















    













    












}
