<?php

require_once __DIR__ . '/../helpers/response.php';

class FlowParController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }



    // ✅ READ Recovery Hapus Buku
    public function getFlowPar($input = []) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        $kc           = $input['kode_kantor']  ?? null;

        if ($kc === '000' || $kc === '') $kc = null;

        // LOGIC GROUPING OTOMATIS
        if ($kc) {
            $masterTable  = "kankas k";
            $colKey       = "kode_group1"; 
            $selectName   = "k.deskripsi_group1 AS nama_kantor";
            $filterMaster = "WHERE k.kode_kantor = :kc_master";
            $joinKey      = "k.kode_group1";
            $filterHarian = "AND kode_cabang = :kc1";
            $filterClosing = "AND kode_cabang = :kc2";
            $kc_val       = str_pad((string)$kc, 3, '0', STR_PAD_LEFT);
        } else {
            $masterTable  = "kode_kantor k";
            $colKey       = "kode_cabang";
            $selectName   = "k.nama_kantor AS nama_kantor";
            $filterMaster = "WHERE k.kode_kantor <> '000'";
            $joinKey      = "k.kode_kantor";
            $filterHarian = "";
            $filterClosing = "";
            $kc_val       = null;
        }

        $sql = "
            WITH closing AS (
                SELECT no_rekening
                FROM nominatif
                WHERE created = :closing_date $filterClosing
                AND kolektibilitas IN ('L', 'DP')
            ),
            harian AS (
                SELECT no_rekening, $colKey as kode_key, baki_debet
                FROM nominatif
                WHERE created = :harian_date $filterHarian
                AND kolektibilitas IN ('KL', 'D', 'M')
            ),
            flow_par AS (
                SELECT h.kode_key, h.no_rekening, h.baki_debet
                FROM harian h
                JOIN closing c ON h.no_rekening = c.no_rekening
            ),
            rekap_cabang AS (
                -- RAHASIANYA DI SINI: Base tabelnya adalah Master, lalu di LEFT JOIN
                SELECT 
                    $joinKey AS kode_cabang,
                    $selectName,
                    COUNT(f.no_rekening) AS noa_flow,
                    COALESCE(SUM(f.baki_debet), 0) AS baki_debet_flow
                FROM $masterTable
                LEFT JOIN flow_par f ON f.kode_key = $joinKey
                $filterMaster
                GROUP BY $joinKey, nama_kantor
            )
            SELECT kode_cabang, nama_kantor, noa_flow, baki_debet_flow
            FROM rekap_cabang
            UNION ALL
            SELECT '', 'TOTAL KONSOLIDASI', SUM(noa_flow), SUM(baki_debet_flow)
            FROM rekap_cabang
            ORDER BY CASE WHEN nama_kantor = 'TOTAL KONSOLIDASI' THEN 1 ELSE 0 END, kode_cabang ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            
            if ($kc_val) {
                // Bind untuk filter master
                $stmt->bindValue(':kc_master', $kc_val);
                // Bind untuk CTE
                $stmt->bindValue(':kc1', $kc_val);
                $stmt->bindValue(':kc2', $kc_val);
            }
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pisahkan Grand Total agar API rapi
            $grandTotal = array_pop($data);
            if (!$grandTotal) {
                $grandTotal = ['kode_cabang'=>'','nama_kantor'=>'TOTAL KONSOLIDASI','noa_flow'=>0,'baki_debet_flow'=>0];
            }

            sendResponse(200, "Berhasil ambil data Flow PAR", ['data' => $data, 'grand_total' => $grandTotal]);
        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function getDebiturFlowPar($input) {
        $kode_kantor  = str_pad($input['kode_kantor'] ?? '', 3, '0', STR_PAD_LEFT);
        $kode_kankas  = $input['kode_kankas'] ?? '';
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');

        $filterCabang = "";
        $filterKankas = "";
        
        if ($kode_kantor !== '000' && $kode_kantor !== '') {
            $filterCabang = " AND kode_cabang = :kc1 ";
        }
        if ($kode_kankas !== '') {
            $filterKankas = " AND kode_group1 = :kankas1 ";
        }

        $filterHarian = $filterCabang . $filterKankas;

        $sql = "
            WITH closing AS (
                SELECT no_rekening, kolektibilitas AS kolek_closing, tunggakan_pokok, tunggakan_bunga
                FROM nominatif
                WHERE created = :closing_date
                  AND kolektibilitas IN ('L','DP')
            ),
            harian AS (
                SELECT 
                    no_rekening, kode_cabang, kolektibilitas AS kolek_harian, baki_debet,
                    tunggakan_pokok, tunggakan_bunga, nama_nasabah, tgl_realisasi,
                    tgl_jatuh_tempo, hari_menunggak, hari_menunggak_pokok, hari_menunggak_bunga,
                    norek_tabungan, kode_group1
                FROM nominatif
                WHERE created = :harian_date
                  AND kolektibilitas IN ('KL','D','M')
                  $filterHarian
            ),
            flow_par AS (
                SELECT h.*, c.kolek_closing
                FROM harian h
                JOIN closing c ON h.no_rekening = c.no_rekening
            ),
            trx AS (
                SELECT no_rekening, MAX(tgl_trans) AS tgl_trans,
                       SUM(COALESCE(angsuran_pokok,0)) AS angsuran_pokok,
                       SUM(COALESCE(angsuran_bunga,0)) AS angsuran_bunga,
                       SUM(COALESCE(angsuran_denda,0)) AS angsuran_denda
                FROM transaksi_kredit
                WHERE tgl_trans > :closing_date_trx AND tgl_trans <= :harian_date_trx
                GROUP BY no_rekening
            ),
            km_last AS (
                /* TAMBAHAN: Masukkan k.nominal di sini */
                SELECT k.no_rekening, k.komitmen, k.tgl_pembayaran, k.nominal, k.alasan
                FROM komitmen_flowpar k
                JOIN (
                    SELECT no_rekening, MAX(COALESCE(updated, created)) AS last_ts
                    FROM komitmen_flowpar
                    WHERE DATE_FORMAT(COALESCE(updated, created), '%Y-%m') = DATE_FORMAT(:harian_date_km, '%Y-%m')
                    GROUP BY no_rekening
                ) s ON s.no_rekening = k.no_rekening AND COALESCE(k.updated, k.created) = s.last_ts
            )
            SELECT
                f.kode_cabang,
                kk.nama_kantor,
                kas.deskripsi_group1 AS nama_kankas,
                f.no_rekening,
                f.nama_nasabah,
                f.kolek_closing,
                f.kolek_harian,
                f.baki_debet,
                f.tunggakan_pokok,
                f.tunggakan_bunga,
                (COALESCE(f.tunggakan_pokok, 0) + COALESCE(f.tunggakan_bunga, 0)) AS total_tunggakan,
                f.hari_menunggak,
                f.hari_menunggak_pokok,
                f.hari_menunggak_bunga,
                tb.saldo_akhir,
                f.tgl_realisasi,
                f.tgl_jatuh_tempo,
                trx.angsuran_pokok,
                trx.angsuran_bunga,
                trx.tgl_trans,
                km_last.komitmen,
                /* TAMBAHAN: Keluarkan data tgl, nominal, dan alasan */
                km_last.tgl_pembayaran,
                km_last.nominal,
                km_last.alasan
            FROM flow_par f
            LEFT JOIN trx ON f.no_rekening = trx.no_rekening
            LEFT JOIN kode_kantor kk ON f.kode_cabang = kk.kode_kantor
            LEFT JOIN kankas kas ON f.kode_group1 = kas.kode_group1
            LEFT JOIN km_last ON km_last.no_rekening = f.no_rekening
            LEFT JOIN tabungan tb ON tb.no_rekening = f.norek_tabungan
            ORDER BY f.baki_debet DESC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            $stmt->bindValue(':closing_date_trx', $closing_date);
            $stmt->bindValue(':harian_date_trx', $harian_date);
            $stmt->bindValue(':harian_date_km', $harian_date);

            if ($kode_kantor !== '000' && $kode_kantor !== '') {
                $stmt->bindValue(':kc1', $kode_kantor);
            }
            if ($kode_kankas !== '') {
                $stmt->bindValue(':kankas1', $kode_kankas);
            }

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Detail debitur flow PAR", $data);
        } catch (Exception $e) {
            sendResponse(500, "Error: " . $e->getMessage());
        }
    }

    public function searchDebiturKredit($input) {
        // --- 1. AMBIL PARAMETER ---
        $user_login_kode = $input['user_kode'] ?? '000'; 
        $kc_input = $input['kode_kantor'] ?? '';

        if ($user_login_kode !== '000' && $user_login_kode !== '') {
            $kode_kantor = str_pad($user_login_kode, 3, '0', STR_PAD_LEFT);
        } else {
            $kode_kantor = ($kc_input !== '') ? str_pad($kc_input, 3, '0', STR_PAD_LEFT) : '';
        }

        $kolek       = $input['kolek'] ?? 'Semua';
        $search      = trim($input['search'] ?? '');
        $kode_group1 = $input['kode_group1'] ?? $input['kode_kankas'] ?? '';
        $kode_group2 = $input['kode_group2'] ?? '';
        $kelurahan   = $input['kelurahan'] ?? '';
        $kecamatan   = $input['kecamatan'] ?? '';
        $status_jt   = $input['status_jt'] ?? ''; 
        $totung      = (isset($input['totung']) && $input['totung'] !== '') ? (float)$input['totung'] : null;
        
        $h_input = $input['harian_date'] ?? '';
        $c_input = $input['closing_date'] ?? '';

        $harian_date  = ($h_input !== '') ? date('Y-m-d', strtotime($h_input)) : date('Y-m-d', strtotime('-1 day'));
        $closing_date = ($c_input !== '') ? date('Y-m-d', strtotime($c_input)) : date('Y-m-d', strtotime('last day of previous month', strtotime($harian_date)));

        $page   = isset($input['page']) ? (int)$input['page'] : 1;
        $limit  = isset($input['limit']) ? (int)$input['limit'] : 50;
        $offset = ($page - 1) * $limit;

        $subWhere = "";
        if ($kode_kantor !== '' && $kode_kantor !== '000') {
            $subWhere .= " AND kode_cabang = '$kode_kantor' ";
        }

        $where  = " WHERE 1=1 ";
        $params = []; 

        // --- 2. LOGIC FILTERING ---
        // 🔥 UBAHAN BARU: FILTER KOLEK BERDASARKAN KOLEK LALU (Tabel c)
        if ($kolek !== 'Semua' && $kolek !== '') {
            $kolekArray = array_map('trim', explode(',', $kolek));
            $kolekPlaceholders = [];
            foreach ($kolekArray as $index => $val) {
                $key = ":kolek_$index";
                $kolekPlaceholders[] = $key;
                $params[$key] = $val;
            }
            // Pakai c.kolektibilitas (Data Closing/Bulan Lalu)
            $where .= " AND COALESCE(c.kolektibilitas, 'Lunas') IN (" . implode(',', $kolekPlaceholders) . ") ";
        }

        if ($search !== '') {
            $where .= " AND (act.no_rekening LIKE :search OR COALESCE(n.nama_nasabah, c.nama_nasabah) LIKE :search_nama OR COALESCE(n.alamat, c.alamat) LIKE :search_alamat) ";
            $params[':search'] = "%$search%";
            $params[':search_nama'] = "%$search%";
            $params[':search_alamat'] = "%$search%";
        }

        if ($totung !== null) {
            $where .= " AND (COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)) > 0 ";
            $where .= " AND (COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)) <= :totung ";
            $params[':totung'] = $totung;
        }

        if ($kode_group1 !== '') {
            $where .= " AND COALESCE(n.kode_group1, c.kode_group1) = :kode_group1 ";
            $params[':kode_group1'] = $kode_group1;
        }
        if ($kode_group2 !== '') {
            $where .= " AND COALESCE(n.kode_group2, c.kode_group2) = :kode_group2 ";
            $params[':kode_group2'] = $kode_group2;
        }
        if ($kelurahan !== '') {
            $where .= " AND COALESCE(n.deskripsi_kode_kelurahan, c.deskripsi_kode_kelurahan) = :kelurahan ";
            $params[':kelurahan'] = $kelurahan;
        }
        if ($kecamatan !== '') {
            $where .= " AND COALESCE(n.deskripsi_kode_kecamatan, c.deskripsi_kode_kecamatan) = :kecamatan ";
            $params[':kecamatan'] = $kecamatan;
        }

        // --- 3. FROM & JOIN STATEMENT ---
        $from_join = "
            FROM (
                SELECT no_rekening FROM nominatif WHERE created = '$harian_date' $subWhere
                UNION
                SELECT no_rekening FROM nominatif WHERE created = '$closing_date' $subWhere
            ) act
            LEFT JOIN nominatif n ON act.no_rekening = n.no_rekening AND n.created = '$harian_date'
            LEFT JOIN nominatif c ON act.no_rekening = c.no_rekening AND c.created = '$closing_date'
            LEFT JOIN tabungan tb ON COALESCE(n.norek_tabungan, c.norek_tabungan) = tb.no_rekening
            LEFT JOIN kankas kas ON COALESCE(n.kode_group1, c.kode_group1) = kas.kode_group1
            LEFT JOIN ao_kredit ao ON COALESCE(n.kode_group2, c.kode_group2) = ao.kode_group2
            LEFT JOIN (
                SELECT 
                    no_rekening,
                    MAX(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT('$harian_date', '%Y-%m') THEN tgl_trans END) as tgl_trans_sekarang,
                    SUM(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT('$harian_date', '%Y-%m') THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) as total_bayar_sekarang,
                    
                    MAX(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT(DATE_SUB('$harian_date', INTERVAL 1 MONTH), '%Y-%m') THEN tgl_trans END) as tgl_trans_lalu,
                    SUM(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT(DATE_SUB('$harian_date', INTERVAL 1 MONTH), '%Y-%m') THEN COALESCE(angsuran_pokok, 0) ELSE 0 END) as pokok_lalu,
                    SUM(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT(DATE_SUB('$harian_date', INTERVAL 1 MONTH), '%Y-%m') THEN (COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) as bunga_lalu
                FROM transaksi_kredit
                WHERE tgl_trans >= DATE_FORMAT(DATE_SUB('$harian_date', INTERVAL 1 MONTH), '%Y-%m-01')
                GROUP BY no_rekening
            ) trx ON act.no_rekening = trx.no_rekening
        ";

        // --- FILTER STATUS ---
        if ($status_jt === 'otp_sesuai') {
            $where .= " AND n.no_rekening IS NOT NULL AND n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND trx.total_bayar_sekarang > 0 AND DAY(trx.tgl_trans_sekarang) <= DAY(n.tgl_jatuh_tempo) ";
        } else if ($status_jt === 'otp_tidak_sesuai') {
            $where .= " AND n.no_rekening IS NOT NULL AND n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND trx.total_bayar_sekarang > 0 AND DAY(trx.tgl_trans_sekarang) > DAY(n.tgl_jatuh_tempo) ";
        } else if ($status_jt === 'lunas') {
            $where .= " AND n.no_rekening IS NULL AND c.no_rekening IS NOT NULL ";
        } else if ($status_jt === 'jt_potensi_npl') {
            $where .= " AND COALESCE(n.tgl_jatuh_tempo, c.tgl_jatuh_tempo) >= DATE_ADD(LAST_DAY(DATE_SUB('$harian_date', INTERVAL 1 MONTH)), INTERVAL -15 DAY) AND COALESCE(n.tgl_jatuh_tempo, c.tgl_jatuh_tempo) <= LAST_DAY('$harian_date') AND c.kolektibilitas IN ('L', 'DP') AND n.baki_debet > 0 ";
        } else if ($status_jt === 'recovery_npl') {
            $where .= " AND c.kolektibilitas IN ('KL', 'D', 'M') AND (n.no_rekening IS NULL OR n.kolektibilitas IN ('L', 'DP') OR n.baki_debet < c.baki_debet) ";
        } else if ($status_jt === 'flow_par') {
            $where .= " AND c.kolektibilitas IN ('L', 'DP') AND n.kolektibilitas IN ('KL', 'D', 'M') ";
        } else if ($status_jt === 'realisasi_baru') {
            $where .= " AND c.no_rekening IS NULL AND n.no_rekening IS NOT NULL ";
        } else if ($status_jt === 'restruktur') {
            $where .= " AND n.baki_debet > c.baki_debet AND c.no_rekening IS NOT NULL ";
        } else if ($status_jt === 'byr_tunggakan') {
            $where .= " AND n.no_rekening IS NOT NULL AND trx.total_bayar_sekarang > 0 AND (COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)) > 0 ";
        } else if ($status_jt === 'belum_bayar_belum_jt') {
            $where .= " AND n.no_rekening IS NOT NULL AND n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND (trx.total_bayar_sekarang IS NULL OR trx.total_bayar_sekarang <= 0) AND DAY('$harian_date') <= DAY(n.tgl_jatuh_tempo) ";
        } else if ($status_jt === 'belum_bayar_lewat_jt') {
            $where .= " AND n.no_rekening IS NOT NULL AND (trx.total_bayar_sekarang IS NULL OR trx.total_bayar_sekarang <= 0) AND DAY('$harian_date') > DAY(n.tgl_jatuh_tempo) ";
        }

        try {
            // --- 4. COUNT & SUMMARY ---
            $sqlSum = "SELECT COUNT(act.no_rekening) as total_data, SUM(COALESCE(n.baki_debet, 0)) AS sum_bd $from_join $where";
            $stmtSum = $this->pdo->prepare($sqlSum);
            foreach ($params as $key => $val) { $stmtSum->bindValue($key, $val); }
            $stmtSum->execute();
            $summary = $stmtSum->fetch(PDO::FETCH_ASSOC);
            
            $totalData  = (int)($summary['total_data'] ?? 0);
            $totalPages = ceil($totalData / $limit);

            // --- 5. QUERY DATA UTAMA ---
            $sqlData = "
                SELECT 
                    COALESCE(n.kode_cabang, c.kode_cabang) AS kode_cabang,
                    COALESCE(n.nama_nasabah, c.nama_nasabah) AS nama_nasabah,
                    act.no_rekening,
                    COALESCE(n.norek_tabungan, c.norek_tabungan) AS norek_tabungan,
                    COALESCE(n.kode_produk, c.kode_produk) AS kode_produk,
                    COALESCE(n.alamat, c.alamat) AS alamat,
                    COALESCE(n.kolektibilitas, 'Lunas') AS kolek, 
                    COALESCE(n.hari_menunggak, 0) AS dpd,
                    COALESCE(n.hari_menunggak_pokok, 0) AS hmp, 
                    COALESCE(n.hari_menunggak_bunga, 0) AS hmb,
                    COALESCE(n.tgl_jatuh_tempo, c.tgl_jatuh_tempo) AS tgl_jatuh_tempo, 
                    COALESCE(n.baki_debet, 0) AS baki_debet,
                    COALESCE(n.tunggakan_pokok, 0) AS tunggakan_pokok, 
                    COALESCE(n.tunggakan_bunga, 0) AS tunggakan_bunga,
                    (COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)) AS totung,
                    COALESCE(tb.saldo_akhir, 0) AS saldo_tabungan,
                    COALESCE(n.kode_group1, c.kode_group1) AS kode_group1, 
                    kas.deskripsi_group1 AS nama_kankas,
                    COALESCE(n.kode_group2, c.kode_group2) AS kode_group2, 
                    ao.nama_ao,
                    COALESCE(n.deskripsi_kode_kelurahan, c.deskripsi_kode_kelurahan) AS deskripsi_kode_kelurahan, 
                    COALESCE(n.deskripsi_kode_kecamatan, c.deskripsi_kode_kecamatan) AS deskripsi_kode_kecamatan,
                    
                    COALESCE(n.jml_pinjaman, c.jml_pinjaman) AS plafon,
                    COALESCE(n.tgl_realisasi, c.tgl_realisasi) AS tgl_realisasi,
                    COALESCE(n.nilai_ckpn, c.nilai_ckpn) AS nilai_ckpn,
                    
                    trx.tgl_trans_sekarang, 
                    trx.total_bayar_sekarang,
                    trx.tgl_trans_lalu, 
                    trx.pokok_lalu, 
                    trx.bunga_lalu,
                    c.kolektibilitas AS kolek_lalu,
                    
                    CASE
                        WHEN n.no_rekening IS NULL AND c.no_rekening IS NOT NULL THEN 'Lunas'
                        WHEN c.no_rekening IS NULL THEN 'Realisasi Baru'
                        WHEN n.baki_debet > c.baki_debet AND c.no_rekening IS NOT NULL THEN 'Restruktur'
                        WHEN c.kolektibilitas IN ('L', 'DP') AND n.kolektibilitas IN ('KL', 'D', 'M') THEN 'Flow Par'
                        WHEN c.kolektibilitas IN ('KL', 'D', 'M') AND (n.no_rekening IS NULL OR n.kolektibilitas IN ('L', 'DP') OR n.baki_debet < c.baki_debet) THEN 'Recovery NPL'
                        WHEN COALESCE(n.tgl_jatuh_tempo, c.tgl_jatuh_tempo) >= DATE_ADD(LAST_DAY(DATE_SUB('$harian_date', INTERVAL 1 MONTH)), INTERVAL -15 DAY) AND COALESCE(n.tgl_jatuh_tempo, c.tgl_jatuh_tempo) <= LAST_DAY('$harian_date') AND c.kolektibilitas IN ('L', 'DP') AND n.baki_debet > 0 THEN 'JT Potensi NPL'
                        WHEN n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND trx.total_bayar_sekarang > 0 AND DAY(trx.tgl_trans_sekarang) <= DAY(n.tgl_jatuh_tempo) THEN 'OTP (Sesuai JT)'
                        WHEN n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND trx.total_bayar_sekarang > 0 AND DAY(trx.tgl_trans_sekarang) > DAY(n.tgl_jatuh_tempo) THEN 'OTP (Tidak Sesuai JT)'
                        WHEN trx.total_bayar_sekarang > 0 AND (COALESCE(n.tunggakan_pokok, 0) + COALESCE(n.tunggakan_bunga, 0)) > 0 THEN 'Byr, Ada Tunggakan'
                        WHEN n.hari_menunggak = 0 AND n.kolektibilitas = 'L' AND (trx.total_bayar_sekarang IS NULL OR trx.total_bayar_sekarang <= 0) AND DAY('$harian_date') <= DAY(n.tgl_jatuh_tempo) THEN 'Blm Byr, Belum JT'
                        WHEN (trx.total_bayar_sekarang IS NULL OR trx.total_bayar_sekarang <= 0) AND DAY('$harian_date') > DAY(n.tgl_jatuh_tempo) THEN 'Blm Byr, Lewat JT'
                        ELSE '-'
                    END AS status_bayar_berjalan
                    
                $from_join
                $where
                ORDER BY n.baki_debet DESC
                LIMIT $limit OFFSET $offset
            ";

            $stmtData = $this->pdo->prepare($sqlData);
            foreach ($params as $key => $val) { $stmtData->bindValue($key, $val); }
            $stmtData->execute();
            $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Sukses", [
                'summary' => ['noa' => $totalData, 'bd_act' => $summary['sum_bd'] ?? 0],
                'pagination' => [
                    'total_data'   => $totalData,
                    'total_page'   => $totalPages,
                    'current_page' => $page,
                    'limit'        => $limit
                ],
                'data' => $data
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Error BE: " . $e->getMessage());
        }
    }





    
    public function getLastCreatedDate() {
        $sql = "SELECT MAX(created) AS last_created FROM nominatif";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $lastCreated = $result['last_created'];
        $closingDate = null;
        $awalBulan   = null;

        if ($lastCreated) {
            $closingDateObj = new DateTime($lastCreated);

            // Hitung closing date: akhir bulan sebelum tanggal lastCreated
            $closingDateObj->modify('last day of previous month');
            $closingDate = $closingDateObj->format('Y-m-d');

            // Hitung awal bulan dari lastCreated
            $awalBulanObj = new DateTime($lastCreated);
            $awalBulanObj->modify('first day of this month');
            $awalBulan = $awalBulanObj->format('Y-m-d');
        }

        sendResponse(200, "Tanggal terakhir data nominatif", [
            'awal_bulan'   => $awalBulan,
            'last_created' => $lastCreated,
            'last_closing' => $closingDate
        ]);
    }

    public function getTop50FlowPar($input) {
        $closing_date  = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $awal_date     = $input['awal_date'] ?? date('Y-m-01');
        $harian_date   = $input['harian_date'] ?? date('Y-m-d');

        $sql = "
            WITH closing AS (
                SELECT 
                    no_rekening,
                    kolektibilitas AS kolek_closing
                FROM nominatif
                WHERE created = :closing_date
                AND kolektibilitas IN ('L', 'DP')
            ),

            harian AS (
                SELECT 
                    no_rekening,
                    kode_cabang,
                    kolektibilitas AS kolek_harian,
                    baki_debet,
                    alamat,
                    tunggakan_pokok,
                    tunggakan_bunga,
                    nama_nasabah,
                    tgl_realisasi
                FROM nominatif
                WHERE created = :harian_date
                AND kolektibilitas IN ('KL', 'D', 'M')
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
                GROUP BY no_rekening
            )

            SELECT
                h.kode_cabang,
                k.nama_kantor,
                h.no_rekening,
                h.nama_nasabah,
                c.kolek_closing,
                h.alamat,
                h.kolek_harian,
                h.baki_debet,
                h.tunggakan_pokok,
                h.tunggakan_bunga,
                h.tgl_realisasi,
                trx.tgl_trans,
                trx.angsuran_pokok,
                trx.angsuran_bunga,
                trx.angsuran_denda
            FROM harian h
            JOIN closing c ON h.no_rekening = c.no_rekening
            LEFT JOIN trx ON h.no_rekening = trx.no_rekening
            LEFT JOIN kode_kantor k ON h.kode_cabang = k.kode_kantor
            ORDER BY h.baki_debet DESC
            LIMIT 50
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':closing_date', $closing_date);
        $stmt->bindValue(':harian_date', $harian_date);
        $stmt->bindValue(':harian_date_trx', $harian_date);
        $stmt->bindValue(':awal_date', $awal_date);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, "Top 50 debitur flow PAR konsolidasi", $data);
    }

    public function updateKomitmenKlBaru($input) {
        $rekening        = $input['rekening'] ?? null;
        $komitmen        = $input['komitmen'] ?? null;
        $alasan          = $input['alasan'] ?? null;
        $tgl_pembayaran  = $input['tgl_pembayaran'] ?? date('Y-m-d');
        // Tangkap nilai nominal, pastikan hanya angka yang masuk (hapus karakter selain digit jika perlu, atau cast to int)
        $nominal         = isset($input['nominal']) ? (int) $input['nominal'] : 0; 
        
        $tanggal = date('Y-m-d');

        if (!$rekening || !$komitmen) {
            sendResponse(400, "Request tidak valid");
            return;
        }

        // Cek apakah data dengan rekening dan bulan yang sama sudah ada
        $sql_check = "
            SELECT id, created FROM komitmen_flowpar 
            WHERE no_rekening = :rekening 
            AND DATE_FORMAT(created, '%Y-%m') = DATE_FORMAT(:created, '%Y-%m')
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql_check);
        $stmt->execute([
            ':rekening' => $rekening,
            ':created' => $tanggal
        ]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Data sudah ada → lakukan UPDATE, pertahankan created, UPDATE nominal
            $sql_update = "
                UPDATE komitmen_flowpar 
                SET 
                    tgl_pembayaran = :tgl_pembayaran,
                    komitmen = :komitmen,
                    alasan = :alasan,
                    nominal = :nominal,
                    updated = NOW()
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql_update);
            $stmt->execute([
                ':tgl_pembayaran' => $tgl_pembayaran,
                ':komitmen' => $komitmen,
                ':alasan' => $alasan,
                ':nominal' => $nominal, // Bind parameter nominal
                ':id' => $existing['id']
            ]);

            sendResponse(200, "Data komitmen berhasil diupdate");
        } else {
            // Data belum ada → lakukan INSERT baru termasuk nominal
            $sql_insert = "
                INSERT INTO komitmen_flowpar 
                    (no_rekening, komitmen, alasan, tgl_pembayaran, nominal, created, updated)
                VALUES 
                    (:rekening, :komitmen, :alasan, :tgl_pembayaran, :nominal, NOW(), NOW())
            ";
            $stmt = $this->pdo->prepare($sql_insert);
            $stmt->execute([
                ':rekening' => $rekening,
                ':komitmen' => $komitmen,
                ':alasan' => $alasan,
                ':tgl_pembayaran' => $tgl_pembayaran,
                ':nominal' => $nominal // Bind parameter nominal
            ]);
            
            sendResponse(200, "Data komitmen berhasil disimpan");
        }
    }




    // ======================= Helper Buckets & Utilities =======================
private function loadBuckets(): array {
    $rows = $this->pdo->query("
      SELECT dpd_code, dpd_name, min_day, max_day, status_tag
      FROM ref_dpd_bucket ORDER BY min_day
    ")->fetchAll(PDO::FETCH_ASSOC);

    $def = []; $name=[]; $tag=[];
    foreach ($rows as $r){
      $def[] = [
        'code'=>$r['dpd_code'],
        'name'=>$r['dpd_name'],
        'min'=>(int)$r['min_day'],
        'max'=>is_null($r['max_day'])?null:(int)$r['max_day'],
        'tag'=>$r['status_tag'] ?? null
      ];
      $name[$r['dpd_code']] = $r['dpd_name'];
      $tag[$r['dpd_code']]  = $r['status_tag'] ?? null;
    }
    return [$def,$name,$tag];
}

private function dpdToCode(int $dpd, array $defs): ?string {
    foreach ($defs as $b) {
      if ($dpd >= $b['min'] && ($b['max']===null || $dpd <= $b['max'])) return $b['code'];
    }
    return null;
}

private function dayRange(string $d): array {
    return [$d." 00:00:00", date('Y-m-d', strtotime("$d +1 day"))." 00:00:00"];
}

// ======================= Endpoint: Detail Debitur Flow PAR =======================
public function getDebiturFlowParXX($input) {
    // --- Parse & normalisasi input
    $kode_kantor  = isset($input['kode_kantor']) ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
    if (!$kode_kantor) {
        sendResponse(400, "Parameter 'kode_kantor' wajib diisi", []);
        return;
    }
    $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
    $awal_date    = $input['awal_date']    ?? date('Y-m-01');
    $harian_date  = $input['harian_date']  ?? date('Y-m-d');

    $sql = "
        WITH closing AS (
            SELECT 
                no_rekening,
                kode_cabang,
                kolektibilitas AS kolek_closing,
                tunggakan_pokok,
                tunggakan_bunga
            FROM nominatif
            WHERE created = :closing_date
              AND kolektibilitas IN ('L', 'DP')
              AND kode_cabang = :kode_kantor_closing
        ),
        harian AS (
            SELECT 
                no_rekening,
                kode_cabang,
                kolektibilitas AS kolek_harian,
                baki_debet,
                tunggakan_pokok,
                tunggakan_bunga,
                nama_nasabah,
                tgl_realisasi,
                tgl_jatuh_tempo,
                hari_menunggak,
                norek_tabungan
            FROM nominatif
            WHERE created = :harian_date
              AND kolektibilitas IN ('KL', 'D', 'M')
              AND kode_cabang = :kode_kantor_harian
        ),
        trx AS (
            SELECT 
                no_rekening,
                MAX(tgl_trans)      AS tgl_trans,
                SUM(angsuran_pokok) AS angsuran_pokok,
                SUM(angsuran_bunga) AS angsuran_bunga,
                SUM(angsuran_denda) AS angsuran_denda
            FROM transaksi_kredit
            WHERE tgl_trans BETWEEN :awal_date AND :harian_date_trx
              AND kode_kantor = :kode_kantor_trx
            GROUP BY no_rekening
        )
        SELECT
            h.kode_cabang,
            k.nama_kantor,
            h.no_rekening,
            h.nama_nasabah,
            c.kolek_closing,
            h.kolek_harian,
            h.baki_debet,
            h.tunggakan_pokok,
            h.tunggakan_bunga,
            h.hari_menunggak,
            tb.saldo_akhir,
            tb.saldo_blokir,
            h.tgl_realisasi,
            h.tgl_jatuh_tempo,
            h.norek_tabungan,
            trx.angsuran_pokok,
            trx.angsuran_bunga,
            trx.angsuran_denda,
            trx.tgl_trans,
            km.komitmen,
            km.tgl_pembayaran,
            km.alasan
        FROM harian h
        JOIN closing c 
          ON h.no_rekening = c.no_rekening
        LEFT JOIN trx 
          ON h.no_rekening = trx.no_rekening
        LEFT JOIN kode_kantor k 
          ON h.kode_cabang = k.kode_kantor
        LEFT JOIN komitmen_flowpar km 
          ON h.no_rekening = km.no_rekening
         AND DATE_FORMAT(COALESCE(km.updated, km.created), '%Y-%m') = DATE_FORMAT(:harian_date_km, '%Y-%m')
        LEFT JOIN tabungan tb
          ON tb.no_rekening = h.norek_tabungan
        ORDER BY h.baki_debet DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':closing_date',        $closing_date);
    $stmt->bindValue(':harian_date',         $harian_date);
    $stmt->bindValue(':harian_date_trx',     $harian_date);
    $stmt->bindValue(':awal_date',           $awal_date);
    $stmt->bindValue(':kode_kantor_closing', $kode_kantor);
    $stmt->bindValue(':kode_kantor_harian',  $kode_kantor);
    $stmt->bindValue(':kode_kantor_trx',     $kode_kantor);
    $stmt->bindValue(':harian_date_km',      $harian_date);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ---------- Tambahkan kolom DPD berdasar hari_menunggak ----------
    list($defs, $nameMap, $tagMap) = $this->loadBuckets(); // defs: [code,min,max,....], nameMap: code=>name, tagMap: code=>tag
    foreach ($data as &$row) {
        $hm = isset($row['hari_menunggak']) && $row['hari_menunggak'] !== '' ? (int)$row['hari_menunggak'] : null;
        if ($hm !== null) {
            $code = $this->dpdToCode($hm, $defs); // contoh hasil: "E_DPD"
            $row['dpd_code']   = $code;                             // opsional
            $row['dpd_name']   = $code ? ($nameMap[$code] ?? null) : null; // contoh: "91-120"
            $row['status_tag'] = $code ? ($tagMap[$code] ?? null) : null;   // opsional
            $row['DPD']        = ($code && isset($nameMap[$code]))
                                  ? ($code . ' ' . $nameMap[$code])          // "E_DPD 91-120"
                                  : null;
        } else {
            $row['dpd_code'] = $row['dpd_name'] = $row['status_tag'] = $row['DPD'] = null;
        }
    }
    unset($row);

    sendResponse(200, "Detail debitur flow PAR untuk cabang $kode_kantor", $data);
}


public function getDetailDebitur($input) {
    // ===== Validasi input dasar =====
    $no_rekening  = trim($input['no_rekening']  ?? '');
    $kode_cabangI = trim($input['kode_cabang']  ?? '');
    if ($no_rekening === '' || $kode_cabangI === '') {
        return sendResponse(400, "no_rekening dan kode_cabang wajib diisi", []);
    }

    // Normalisasi kode cabang (3 digit)
    $kode_cabang  = str_pad($kode_cabangI, 3, '0', STR_PAD_LEFT);

    // Tanggal default
    $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
    $harian_date  = $input['harian_date']  ?? date('Y-m-d');

    // Awal periode transaksi = awal bulan dari harian_date (bisa diubah jika perlu)
    $awal_date    = date('Y-m-01', strtotime($harian_date));

    $sql = "
        WITH closing AS (
            SELECT 
                no_rekening,
                kode_cabang,
                kolektibilitas AS kolek_closing,
                tunggakan_pokok,
                tunggakan_bunga
            FROM nominatif
            WHERE created = :closing_date
              AND kolektibilitas IN ('L','DP')
              AND kode_cabang = :kode_cabang_closing
              AND no_rekening = :no_rekening_closing
        ),
        harian AS (
            SELECT 
                no_rekening,
                kode_cabang,
                kolektibilitas AS kolek_harian,
                baki_debet,
                tunggakan_pokok,
                tunggakan_bunga,
                nama_nasabah,
                tgl_realisasi,
                tgl_jatuh_tempo,
                hari_menunggak,
                norek_tabungan
            FROM nominatif
            WHERE created = :harian_date
              AND kolektibilitas IN ('KL','D','M')
              AND kode_cabang = :kode_cabang_harian
              AND no_rekening = :no_rekening_harian
        ),
        trx AS (
            SELECT 
                no_rekening,
                MAX(tgl_trans)      AS tgl_trans,
                SUM(angsuran_pokok) AS angsuran_pokok,
                SUM(angsuran_bunga) AS angsuran_bunga,
                SUM(angsuran_denda) AS angsuran_denda
            FROM transaksi_kredit
            WHERE tgl_trans BETWEEN :awal_date AND :harian_date_trx
              AND kode_kantor = :kode_kantor_trx
              AND no_rekening = :no_rekening_trx
            GROUP BY no_rekening
        )
        SELECT
            h.kode_cabang,
            k.nama_kantor,
            h.no_rekening,
            h.nama_nasabah,
            c.kolek_closing,
            h.kolek_harian,
            h.baki_debet,
            h.tunggakan_pokok,
            h.tunggakan_bunga,
            h.hari_menunggak,
            tb.saldo_akhir,
            tb.saldo_blokir,
            h.tgl_realisasi,
            h.tgl_jatuh_tempo,
            h.norek_tabungan,
            trx.angsuran_pokok,
            trx.angsuran_bunga,
            trx.angsuran_denda,
            trx.tgl_trans,
            km.komitmen,
            km.tgl_pembayaran,
            km.alasan
        FROM harian h
        JOIN closing c 
          ON h.no_rekening = c.no_rekening
        LEFT JOIN trx 
          ON h.no_rekening = trx.no_rekening
        LEFT JOIN kode_kantor k 
          ON h.kode_cabang = k.kode_kantor
        LEFT JOIN komitmen_flowpar km 
          ON h.no_rekening = km.no_rekening
         AND DATE_FORMAT(COALESCE(km.updated, km.created), '%Y-%m') = DATE_FORMAT(:harian_date_km, '%Y-%m')
        LEFT JOIN tabungan tb
          ON tb.no_rekening = h.norek_tabungan
        ORDER BY h.baki_debet DESC
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    // Bind tanggal
    $stmt->bindValue(':closing_date',      $closing_date);
    $stmt->bindValue(':harian_date',       $harian_date);     // untuk CTE harian
    $stmt->bindValue(':awal_date',         $awal_date);
    $stmt->bindValue(':harian_date_trx',   $harian_date);     // untuk trx
    $stmt->bindValue(':harian_date_km',    $harian_date);     // join komitmen

    // Bind kode cabang / kantor
    $stmt->bindValue(':kode_cabang_closing', $kode_cabang);
    $stmt->bindValue(':kode_cabang_harian',  $kode_cabang);
    $stmt->bindValue(':kode_kantor_trx',     $kode_cabang);

    // Bind no rekening (semua tempat)
    $stmt->bindValue(':no_rekening_closing', $no_rekening);
    $stmt->bindValue(':no_rekening_harian',  $no_rekening);
    $stmt->bindValue(':no_rekening_trx',     $no_rekening);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return sendResponse(404, "Detail debitur tidak ditemukan untuk $no_rekening di cabang $kode_cabang", []);
    }

    return sendResponse(200, "Detail debitur flow PAR ($no_rekening) – cabang $kode_cabang", $row);
}











    












}
