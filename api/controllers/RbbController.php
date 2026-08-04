<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/filterHelpers.php';

class RbbController
{
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    /**
     * LAPORAN ASSET VS RBB (PERIODE JUNI 2026)
     * UPDATE FORMULA: Khusus Saldo Bank (id_ref 63) dihitung dari 10601 + 10606
     */
    public function getAsetRealisasi($input = null) 
    {
        set_time_limit(120); 
        ini_set('memory_limit', '512M');

        $b = is_array($input) ? $input : [];
        
        $tanggal_input = !empty($b['tanggal']) ? $b['tanggal'] : '2026-06-30';
        $tanggal_clean = str_replace('-', '', $tanggal_input); 

        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : '000';

        $tahun_berjalan = date('Y', strtotime($tanggal_input));
        $periode_rbb = $tahun_berjalan . '-01-01'; 

        $sql = "
            WITH filter_parameter AS (
                SELECT :kode_kantor AS p_kode_kantor
            ),

            saldo_raw AS (
                -- 1. Mengambil data saldo riil dari acc_history (Cabang 000 s/d 028)
                SELECT 
                    h.kode_perk,
                    SUM(h.saldo_akhir) AS total_saldo
                FROM acc_history h
                INNER JOIN filter_parameter p 
                    ON (p.p_kode_kantor = '000' AND h.kode_kantor BETWEEN '000' AND '028')
                    OR (p.p_kode_kantor != '000' AND h.kode_kantor = p.p_kode_kantor)
                WHERE h.tanggal = :tanggal_clean
                GROUP BY h.kode_perk
            ),

            target_rbb AS (
                -- 2. Mengambil Angka Target RBB secara horizontal dari kolom cabang 000-028
                SELECT 
                    kode_monbis,
                    SUM(CASE 
                        WHEN p.p_kode_kantor = '000' THEN COALESCE(`000`, 0)
                        WHEN p.p_kode_kantor = '001' THEN COALESCE(`001`, 0)
                        WHEN p.p_kode_kantor = '002' THEN COALESCE(`002`, 0)
                        WHEN p.p_kode_kantor = '003' THEN COALESCE(`003`, 0)
                        WHEN p.p_kode_kantor = '004' THEN COALESCE(`004`, 0)
                        WHEN p.p_kode_kantor = '005' THEN COALESCE(`005`, 0)
                        WHEN p.p_kode_kantor = '006' THEN COALESCE(`006`, 0)
                        WHEN p.p_kode_kantor = '007' THEN COALESCE(`007`, 0)
                        WHEN p.p_kode_kantor = '008' THEN COALESCE(`008`, 0)
                        WHEN p.p_kode_kantor = '009' THEN COALESCE(`009`, 0)
                        WHEN p.p_kode_kantor = '010' THEN COALESCE(`010`, 0)
                        WHEN p.p_kode_kantor = '011' THEN COALESCE(`011`, 0)
                        WHEN p.p_kode_kantor = '012' THEN COALESCE(`012`, 0)
                        WHEN p.p_kode_kantor = '013' THEN COALESCE(`013`, 0)
                        WHEN p.p_kode_kantor = '014' THEN COALESCE(`014`, 0)
                        WHEN p.p_kode_kantor = '015' THEN COALESCE(`015`, 0)
                        WHEN p.p_kode_kantor = '016' THEN COALESCE(`016`, 0)
                        WHEN p.p_kode_kantor = '017' THEN COALESCE(`017`, 0)
                        WHEN p.p_kode_kantor = '018' THEN COALESCE(`018`, 0)
                        WHEN p.p_kode_kantor = '019' THEN COALESCE(`019`, 0)
                        WHEN p.p_kode_kantor = '020' THEN COALESCE(`020`, 0)
                        WHEN p.p_kode_kantor = '021' THEN COALESCE(`021`, 0)
                        WHEN p.p_kode_kantor = '022' THEN COALESCE(`022`, 0)
                        WHEN p.p_kode_kantor = '023' THEN COALESCE(`023`, 0)
                        WHEN p.p_kode_kantor = '024' THEN COALESCE(`024`, 0)
                        WHEN p.p_kode_kantor = '025' THEN COALESCE(`025`, 0)
                        WHEN p.p_kode_kantor = '026' THEN COALESCE(`026`, 0)
                        WHEN p.p_kode_kantor = '027' THEN COALESCE(`027`, 0)
                        WHEN p.p_kode_kantor = '028' THEN COALESCE(`028`, 0)
                        ELSE 0 
                    END) AS target_rbb_juni
                FROM rbb
                CROSS JOIN filter_parameter p
                WHERE periode = :periode_rbb
                GROUP BY kode_monbis
            ),

            data_level_akun AS (
                -- 3. Mapping struktur master ref_rbb dengan data realisasi 
                SELECT 
                    ref.id_ref,
                    ref.kode_monbis,
                    ref.kode_perkiraan,
                    ref.sandi_lbbpr,
                    ref.keterangan,
                    
                    -- Formula Baru: Jika baris Saldo Bank (id_ref 63), hitung otomatis dari nominal 10601 + 10606
                    CASE 
                        WHEN ref.id_ref = 63 OR ref.keterangan LIKE '%Kredit Yang Diberikan (Saldo Bank)%' THEN COALESCE(s_bank.total_saldo_bank, 0)
                        ELSE COALESCE(hist.total_saldo, 0)
                    END AS realisasi,

                    COALESCE(r.target_rbb_juni, 0) AS target_rbb,
                    COALESCE(l210.total_saldo, 0) AS l210_real_210
                FROM ref_rbb ref
                LEFT JOIN saldo_raw hist ON ref.kode_perkiraan = hist.kode_perk
                LEFT JOIN target_rbb r ON ref.kode_monbis = r.kode_monbis
                LEFT JOIN saldo_raw l210 ON l210.kode_perk = '210'
                CROSS JOIN (
                    -- Mengalkulasi sub-total internal khusus untuk kebutuhan akun 10601 + 10606
                    SELECT SUM(total_saldo) AS total_saldo_bank 
                    FROM saldo_raw 
                    WHERE kode_perk IN ('10601', '10606')
                ) s_bank
                WHERE ref.kategori = 'ASET'
            ),

            total_aset_calc AS (
                -- 4. Perhitungan ringkas total grand sum aset
                SELECT
                    SUM(CASE WHEN kode_perkiraan IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') OR id_ref = 63 THEN realisasi ELSE 0 END) AS tot_realisasi,
                    SUM(CASE WHEN kode_perkiraan IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') OR id_ref = 63 THEN target_rbb ELSE 0 END) AS tot_rbb,
                    MAX(l210_real_210) AS max_l210
                FROM data_level_akun
            ),

            laporan_base AS (
                -- 5. Suntik data total akumulasi murni ke baris bawaan master id_ref = 95
                SELECT 
                    d.id_ref, 
                    d.kode_monbis, 
                    d.kode_perkiraan, 
                    d.sandi_lbbpr, 
                    d.keterangan, 
                    CASE WHEN d.id_ref = 95 THEN t.tot_realisasi ELSE d.realisasi END AS realisasi,
                    CASE WHEN d.id_ref = 95 THEN t.tot_rbb ELSE d.target_rbb END AS target_rbb
                FROM data_level_akun d
                CROSS JOIN total_aset_calc t

                UNION ALL

                -- 6. Baris terbawah pelengkap: TOTAL ASET GABUNGAN
                SELECT 
                    999999 AS id_ref,
                    NULL AS kode_monbis,
                    NULL AS kode_perkiraan,
                    '2000000000' AS sandi_lbbpr,
                    'TOTAL ASET GABUNGAN (TOTAL ASET - 210)' AS keterangan,
                    t.tot_realisasi - t.max_l210 AS realisasi,
                    t.tot_rbb AS target_rbb
                FROM total_aset_calc t
            )

            -- 7. Tampilan akhir komparasi data finansial
            SELECT 
                kode_monbis,
                kode_perkiraan,
                sandi_lbbpr,
                keterangan,
                realisasi,
                target_rbb,
                (realisasi - target_rbb) AS selisih,
                CASE 
                    WHEN target_rbb = 0 THEN 0 
                    ELSE ROUND((realisasi / target_rbb) * 100, 2)
                END AS pencapaian
            FROM laporan_base
            ORDER BY id_ref ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':kode_kantor', $kode_kantor, PDO::PARAM_STR);
            $stmt->bindValue(':tanggal_clean', $tanggal_clean, PDO::PARAM_STR);
            $stmt->bindValue(':periode_rbb', $periode_rbb, PDO::PARAM_STR);
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$r) {
                $r['realisasi']  = (float)$r['realisasi'];
                $r['target_rbb'] = (float)$r['target_rbb'];
                $r['selisih']    = (float)$r['selisih'];
                $r['pencapaian'] = (float)$r['pencapaian'];
            }

            $nama_filter = ($kode_kantor === '000') ? "KONSOLIDASI (SEMUA CABANG)" : "CABANG KODE " . $kode_kantor;

            return sendResponse(200, "Berhasil meload Laporan Realisasi vs RBB Aset", [
                'meta' => [
                    'filter_kantor' => $nama_filter,
                    'tanggal_posisi'=> $tanggal_input,
                    'periode_rbb'   => $periode_rbb
                ],
                'data' => $rows
            ]);

        } catch (PDOException $e) {
            error_log("PDO Error Laporan RBB Aset: " . $e->getMessage());
            return sendResponse(500, "Database Query Error: " . $e->getMessage(), null);
        }
    }

    /**
     * LAPORAN ASSET REALISASI MoM & YoY (Dinamis Berdasarkan Tanggal Input)
     * UPDATE FORMULA: 
     * 1. Saldo Bank (id_ref 63) dihitung dari 10601 + 10606 murni dari acc_history (Bebas Nominatif)
     * 2. TOTAL ASET disatukan langsung ke baris master id_ref = 95 (Monbis 95) agar tidak double/kosong
     */
    public function getAsetMoMYoY($input = null) 
    {
        set_time_limit(120); 
        ini_set('memory_limit', '512M');

        $b = is_array($input) ? $input : [];
        
        // 1. Ambil Parameter Tanggal Utama (Contoh: '2026-06-30')
        $tanggal_input = !empty($b['tanggal']) ? $b['tanggal'] : '2026-06-30';
        $time_utama    = strtotime($tanggal_input);

        // 2. Hitung Tanggal Mei 2026 (MoM / Akhir Bulan Sebelumnya)
        $tanggal_mom   = date('Y-m-t', strtotime('-1 month', $time_utama));

        // 3. Hitung Tanggal Juni 2025 (YoY / Bulan yang Sama di Tahun Lalu)
        $tanggal_yoy   = date('Y-m-d', strtotime('-1 year', $time_utama));

        // Format bersih untuk query akuntansi (YYYYMMDD)
        $tgl_utama_clean = str_replace('-', '', $tanggal_input);
        $tgl_mom_clean   = str_replace('-', '', $tanggal_mom);
        $tgl_yoy_clean   = str_replace('-', '', $tanggal_yoy);

        // Filter Kantor Cabang
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : '000';

        $sql = "
            WITH filter_parameter AS (
                SELECT :kode_kantor AS p_kode_kantor
            ),

            saldo_acc_history AS (
                -- 1. Mengambil data saldo dari acc_history berdasarkan 3 parameter tanggal dinamis
                SELECT 
                    h.kode_perk,
                    SUM(CASE WHEN h.tanggal = :tgl_utama THEN h.saldo_akhir ELSE 0 END) AS total_utama,
                    SUM(CASE WHEN h.tanggal = :tgl_mom THEN h.saldo_akhir ELSE 0 END) AS total_mom,
                    SUM(CASE WHEN h.tanggal = :tgl_yoy THEN h.saldo_akhir ELSE 0 END) AS total_yoy
                FROM acc_history h
                INNER JOIN filter_parameter p 
                    ON (p.p_kode_kantor = '000' AND h.kode_kantor BETWEEN '000' AND '028')
                    OR (p.p_kode_kantor != '000' AND h.kode_kantor = p.p_kode_kantor)
                WHERE h.tanggal IN (:tgl_utama, :tgl_mom, :tgl_yoy)
                GROUP BY h.kode_perk
            ),

            data_level_akun AS (
                -- 2. Mapping data saldo ke struktur master ref_rbb + Hitung formula khusus Saldo Bank (10601 + 10606)
                SELECT 
                    ref.id_ref,
                    ref.kode_monbis,
                    ref.kode_perkiraan,
                    ref.sandi_lbbpr,
                    ref.keterangan,
                    
                    -- Nilai Utama (Juni 2026)
                    CASE 
                        WHEN ref.id_ref = 63 OR ref.keterangan LIKE '%Kredit Yang Diberikan (Saldo Bank)%' THEN COALESCE(s_bank.utama_bank, 0)
                        ELSE COALESCE(hist.total_utama, 0)
                    END AS utama,

                    -- Nilai MoM (Mei 2026)
                    CASE 
                        WHEN ref.id_ref = 63 OR ref.keterangan LIKE '%Kredit Yang Diberikan (Saldo Bank)%' THEN COALESCE(s_bank.mom_bank, 0)
                        ELSE COALESCE(hist.total_mom, 0)
                    END AS mom,

                    -- Nilai YoY (Juni 2025)
                    CASE 
                        WHEN ref.id_ref = 63 OR ref.keterangan LIKE '%Kredit Yang Diberikan (Saldo Bank)%' THEN COALESCE(s_bank.yoy_bank, 0)
                        ELSE COALESCE(hist.total_yoy, 0)
                    END AS yoy,
                    
                    -- Kolom penarik nilai akun liabilitas 210
                    COALESCE(l210.total_utama, 0) AS l210_utama,
                    COALESCE(l210.total_mom, 0) AS l210_mom,
                    COALESCE(l210.total_yoy, 0) AS l210_yoy
                FROM ref_rbb ref
                LEFT JOIN saldo_acc_history hist ON ref.kode_perkiraan = hist.kode_perk
                LEFT JOIN saldo_acc_history l210 ON l210.kode_perk = '210'
                CROSS JOIN (
                    -- Sub-kalkulasi internal khusus Saldo Bank (10601 + 10606) dari tabel acc_history
                    SELECT 
                        SUM(total_utama) AS utama_bank,
                        SUM(total_mom) AS mom_bank,
                        SUM(total_yoy) AS yoy_bank
                    FROM saldo_acc_history 
                    WHERE kode_perk IN ('10601', '10606')
                ) s_bank
                WHERE ref.kategori = 'ASET'
            ),

            total_aset_calc AS (
                -- 3. Mengalkulasi Grand Total Akumulasi Komponen Aset
                SELECT
                    SUM(CASE WHEN kode_perkiraan IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') OR id_ref = 63 THEN utama ELSE 0 END) AS tot_utama,
                    SUM(CASE WHEN kode_perkiraan IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') OR id_ref = 63 THEN mom ELSE 0 END) AS tot_mom,
                    SUM(CASE WHEN kode_perkiraan IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') OR id_ref = 63 THEN yoy ELSE 0 END) AS tot_yoy,
                    MAX(l210_utama) AS max_l210_utama,
                    MAX(l210_mom) AS max_l210_mom,
                    MAX(l210_yoy) AS max_l210_yoy
                FROM data_level_akun
            ),

            laporan_base AS (
                -- 4. Tampilkan semua komponen detail aset, kecualikan id_ref 95 bawaan agar nanti bisa ditimpa
                SELECT 
                    d.id_ref, d.kode_monbis, d.kode_perkiraan, d.sandi_lbbpr, d.keterangan, 
                    d.utama, d.mom, d.yoy
                FROM data_level_akun d
                WHERE d.id_ref != 95

                UNION ALL

                -- 5. SUNTIK LANGSUNG TOTAL ASET KE BARIS MASTER MONBIS 95 (Mencegah baris kosong / double)
                SELECT 
                    95 AS id_ref,
                    '95' AS kode_monbis,
                    NULL AS kode_perkiraan,
                    '1000000000' AS sandi_lbbpr,
                    'TOTAL ASET' AS keterangan,
                    t.tot_utama AS utama,
                    t.tot_mom AS mom,
                    t.tot_yoy AS yoy
                FROM total_aset_calc t

                UNION ALL

                -- 6. Baris Tambahan Pelengkap Khusus: TOTAL ASET GABUNGAN (TOTAL ASET - 210)
                SELECT 
                    999999 AS id_ref,
                    NULL AS kode_monbis,
                    NULL AS kode_perkiraan,
                    '2000000000' AS sandi_lbbpr,
                    'TOTAL ASET GABUNGAN (TOTAL ASET - 210)' AS keterangan,
                    t.tot_utama - t.max_l210_utama AS utama,
                    t.tot_mom - t.max_l210_mom AS mom,
                    t.tot_yoy - t.max_l210_yoy AS yoy
                FROM total_aset_calc t
            )

            -- 7. Hasil Akhir Komparasi Finansial Lengkap Rasio MoM & YoY %
            SELECT 
                kode_monbis,
                kode_perkiraan,
                sandi_lbbpr,
                keterangan,
                utama AS nominal_utama,
                mom AS nominal_mom,
                (utama - mom) AS mom_nominal_diff,
                CASE 
                    WHEN mom = 0 THEN 0 
                    ELSE ROUND(((utama - mom) / mom) * 100, 2)
                END AS mom_persen,
                yoy AS nominal_yoy,
                (utama - yoy) AS yoy_nominal_diff,
                CASE 
                    WHEN yoy = 0 THEN 0 
                    ELSE ROUND(((utama - yoy) / yoy) * 100, 2)
                END AS yoy_persen
            FROM laporan_base
            ORDER BY id_ref ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Binding Parameter Tanggal & Cabang
            $stmt->bindValue(':kode_kantor', $kode_kantor, PDO::PARAM_STR);
            $stmt->bindValue(':tgl_utama', $tgl_utama_clean, PDO::PARAM_STR);
            $stmt->bindValue(':tgl_mom', $tgl_mom_clean, PDO::PARAM_STR);
            $stmt->bindValue(':tgl_yoy', $tgl_yoy_clean, PDO::PARAM_STR);
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cast type data ke Float untuk Response Clean API
            foreach ($rows as &$r) {
                $r['nominal_utama']     = (float)$r['nominal_utama'];
                $r['nominal_mom']       = (float)$r['nominal_mom'];
                $r['mom_nominal_diff']  = (float)$r['mom_nominal_diff'];
                $r['mom_persen']        = (float)$r['mom_persen'];
                $r['nominal_yoy']       = (float)$r['nominal_yoy'];
                $r['yoy_nominal_diff']  = (float)$r['yoy_nominal_diff'];
                $r['yoy_persen']        = (float)$r['yoy_persen'];
            }

            $nama_filter = ($kode_kantor === '000') ? "KONSOLIDASI (SEMUA CABANG)" : "CABANG KODE " . $kode_kantor;

            return sendResponse(200, "Berhasil meload Laporan Realisasi Aset MoM dan YoY", [
                'meta' => [
                    'filter_kantor'  => $nama_filter,
                    'tanggal_utama'  => $tanggal_input,
                    'tanggal_mom'    => $tanggal_mom,
                    'tanggal_yoy'    => $tanggal_yoy
                ],
                'data' => $rows
            ]);

        } catch (PDOException $e) {
            error_log("PDO Error Laporan MoM YoY Aset: " . $e->getMessage());
            return sendResponse(500, "Database Query Error: " . $e->getMessage(), null);
        }
    }

    public function getRealisasiRbbBulanBerjalan($input = null)
    {
        set_time_limit(120);
        ini_set('memory_limit', '512M');

        $b = is_array($input) ? $input : [];

        $harianDate = !empty($b['harian_date']) ? $b['harian_date'] : (!empty($b['tanggal']) ? $b['tanggal'] : date('Y-m-d'));
        $time = strtotime($harianDate);
        if (!$time) {
            return sendResponse(400, "Format harian_date tidak valid.");
        }

        $monthStart = date('Y-m-01', $time);
        $yearStart = date('Y-01-01', $time);
        $harianNext = date('Y-m-d', strtotime('+1 day', $time));
        $sqlMonthStart = $this->pdo->quote($monthStart);
        $sqlYearStart = $this->pdo->quote($yearStart);
        $sqlHarianNext = $this->pdo->quote($harianNext);
        $sqlActualDate = $this->pdo->quote($harianDate);
        $reportType = strtolower(trim((string)($b['report_type'] ?? '')));
        if ($reportType === 'laba_rugi') {
            $kodePerkiraanList = ['4', '5'];
        } elseif ($reportType === 'neraca') {
            $kodePerkiraanList = ['1'];
        } else {
            $kodePerkiraanList = [!empty($b['kode_perkiraan']) ? (string)$b['kode_perkiraan'] : 'produksi.total'];
            $reportType = 'produksi';
        }
        $kodePerkiraan = implode(',', $kodePerkiraanList);
        $compareMode = strtolower((string)($b['compare_mode'] ?? 'auto'));
        if (!in_array($compareMode, ['auto', 'rbb', 'history'], true)) {
            $compareMode = 'auto';
        }

        $kodeKantor = '';
        if (!empty($b['kode_kantor']) && $b['kode_kantor'] !== '000' && strtolower((string)$b['kode_kantor']) !== 'konsolidasi') {
            $kodeKantor = str_pad((string)$b['kode_kantor'], 3, '0', STR_PAD_LEFT);
        }

        $korwil = strtoupper((string)($b['korwil'] ?? ''));
        $kodePlaceholders = [];
        $params = [];
        foreach ($kodePerkiraanList as $index => $kode) {
            $placeholder = ':kode_perkiraan_' . $index;
            $kodePlaceholders[] = $placeholder;
            $params[$placeholder] = $kode;
        }
        $whereFinal = "WHERE ref.kode_perkiraan IN (" . implode(',', $kodePlaceholders) . ")";

        if ($kodeKantor !== '') {
            $whereFinal .= " AND data_gabungan.kode_kantor = :kode_kantor";
            $params[':kode_kantor'] = $kodeKantor;
        } elseif ($korwil === 'SEMARANG') {
            $whereFinal .= " AND data_gabungan.kode_kantor BETWEEN '001' AND '007'";
        } elseif ($korwil === 'SOLO') {
            $whereFinal .= " AND data_gabungan.kode_kantor BETWEEN '008' AND '014'";
        } elseif ($korwil === 'BANYUMAS') {
            $whereFinal .= " AND data_gabungan.kode_kantor BETWEEN '015' AND '021'";
        } elseif ($korwil === 'PEKALONGAN') {
            $whereFinal .= " AND data_gabungan.kode_kantor BETWEEN '022' AND '028'";
        } else {
            $whereFinal .= " AND data_gabungan.kode_kantor BETWEEN '001' AND '028'";
        }

        if ($compareMode === 'history') {
            return $this->getRealisasiHistoryComparison($b, false);
        }

        $branchColumns = ['001','002','003','004','005','006','007','008','009','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028'];
        $sumColumns = [];
        $monthUnion = [];
        $laluUnion = [];

        foreach ($branchColumns as $code) {
            $sumColumns[] = "IFNULL(`{$code}`,0)";
            $monthUnion[] = "SELECT kode_monbis, periode, '{$code}' AS kode_kantor, IFNULL(`{$code}`,0) AS rbb_target FROM rbb WHERE periode = {$sqlMonthStart}";
            $laluUnion[] = "SELECT kode_monbis, '{$code}' AS kode_kantor, SUM(IFNULL(`{$code}`,0)) AS target_s_d_lalu FROM rbb WHERE periode >= {$sqlYearStart} AND periode < {$sqlMonthStart} GROUP BY kode_monbis";
        }

        $sumExpression = implode('+', $sumColumns);
        array_unshift($monthUnion, "SELECT kode_monbis, periode, '000' AS kode_kantor, ({$sumExpression}) AS rbb_target FROM rbb WHERE periode = {$sqlMonthStart}");
        array_unshift($laluUnion, "SELECT kode_monbis, '000' AS kode_kantor, SUM({$sumExpression}) AS target_s_d_lalu FROM rbb WHERE periode >= {$sqlYearStart} AND periode < {$sqlMonthStart} GROUP BY kode_monbis");

        $isFinancialReport = in_array($reportType, ['neraca', 'laba_rugi'], true);
        if ($isFinancialReport) {
            $actualCategory = $reportType === 'neraca'
                ? "'1' AS kode_perkiraan, SUM(CASE WHEN h.kode_perk IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113') THEN h.saldo_akhir ELSE 0 END) AS total_realisasi"
                : "CASE WHEN h.kode_perk LIKE '4%' THEN '4' ELSE '5' END AS kode_perkiraan, SUM(h.saldo_akhir) AS total_realisasi";
            $actualWhere = $reportType === 'neraca'
                ? "h.kode_perk IN ('101','102','103','116','104','105','10602','10604','10605','107','117','118','108','119','109','110','11102','112','120','121','113')"
                : "h.kode_perk LIKE '4%' OR h.kode_perk LIKE '5%'";
            $realBlnIniSql = "
                SELECT h.kode_kantor, {$actualCategory}
                FROM acc_history h
                WHERE h.tanggal = {$sqlActualDate} AND ({$actualWhere})
                GROUP BY h.kode_kantor";
            $realBlnIniSql .= " UNION ALL SELECT '000' AS kode_kantor, {$actualCategory}
                FROM acc_history h
                WHERE h.tanggal = {$sqlActualDate} AND ({$actualWhere})";
            $realLaluSql = "SELECT NULL AS kode_kantor, '1' AS kode_perkiraan, 0 AS realisasi_s_d_lalu WHERE 1=0";
            $realJoin = "data_gabungan.kode_kantor = real_bln_ini.kode_kantor AND ref.kode_perkiraan = real_bln_ini.kode_perkiraan";
            $realLaluJoin = "1=0";
        } else {
            $realBlnIniSql = "
                SELECT kode_kantor, NULL AS kode_perkiraan, SUM(realisasi_pokok) AS total_realisasi
                FROM update_realisasi_kredit
                WHERE kode_trans = '110'
                  AND tanggal_realisasi >= {$sqlMonthStart}
                  AND tanggal_realisasi < {$sqlHarianNext}
                GROUP BY kode_kantor
                UNION ALL
                SELECT '000' AS kode_kantor, NULL AS kode_perkiraan, SUM(realisasi_pokok) AS total_realisasi
                FROM update_realisasi_kredit
                WHERE kode_trans = '110'
                  AND tanggal_realisasi >= {$sqlMonthStart}
                  AND tanggal_realisasi < {$sqlHarianNext}";
            $realLaluSql = "
                SELECT kode_kantor, SUM(realisasi_pokok) AS realisasi_s_d_lalu
                FROM update_realisasi_kredit
                WHERE kode_trans = '110'
                  AND tanggal_realisasi >= {$sqlYearStart}
                  AND tanggal_realisasi < {$sqlMonthStart}
                GROUP BY kode_kantor
                UNION ALL
                SELECT '000' AS kode_kantor, SUM(realisasi_pokok) AS realisasi_s_d_lalu
                FROM update_realisasi_kredit
                WHERE kode_trans = '110'
                  AND tanggal_realisasi >= {$sqlYearStart}
                  AND tanggal_realisasi < {$sqlMonthStart}";
            $realJoin = "data_gabungan.kode_kantor = real_bln_ini.kode_kantor";
            $realLaluJoin = "data_gabungan.kode_kantor = real_lalu.kode_kantor";
        }

        $sql = "
            WITH data_gabungan AS (
                " . implode("\nUNION ALL\n", $monthUnion) . "
            ),
            rbb_lalu AS (
                " . implode("\nUNION ALL\n", $laluUnion) . "
            ),
            real_bln_ini AS (
                {$realBlnIniSql}
            ),
            real_lalu AS (
                {$realLaluSql}
            )
            SELECT
                data_gabungan.kode_monbis,
                ref.keterangan,
                data_gabungan.kode_kantor,
                IFNULL(k.nama_kantor, 'Konsolidasi') AS nama_kantor,
                data_gabungan.periode,
                IFNULL(data_gabungan.rbb_target, 0) AS nilai_rbb,
                IFNULL(real_bln_ini.total_realisasi, 0) AS realisasi_bulan_ini,
                ROUND(IFNULL(real_bln_ini.total_realisasi, 0) / NULLIF(data_gabungan.rbb_target, 0) * 100, 2) AS persentase_rbb_bulan_ini,
                GREATEST(IFNULL(rbb_lalu.target_s_d_lalu, 0) - IFNULL(real_lalu.realisasi_s_d_lalu, 0), 0) AS kekurangan_sd_bulan_lalu,
                (
                    IFNULL(data_gabungan.rbb_target, 0) +
                    GREATEST(IFNULL(rbb_lalu.target_s_d_lalu, 0) - IFNULL(real_lalu.realisasi_s_d_lalu, 0), 0)
                ) AS total_beban_target,
                ROUND(
                    IFNULL(real_bln_ini.total_realisasi, 0) /
                    NULLIF(
                        IFNULL(data_gabungan.rbb_target, 0) +
                        GREATEST(IFNULL(rbb_lalu.target_s_d_lalu, 0) - IFNULL(real_lalu.realisasi_s_d_lalu, 0), 0),
                        0
                    ) * 100,
                    2
                ) AS persentase_rbb_plus_kekurangan
            FROM data_gabungan
            LEFT JOIN rbb_lalu ON data_gabungan.kode_kantor = rbb_lalu.kode_kantor AND data_gabungan.kode_monbis = rbb_lalu.kode_monbis
            INNER JOIN ref_rbb ref ON data_gabungan.kode_monbis = ref.kode_monbis
            LEFT JOIN real_bln_ini ON {$realJoin}
            LEFT JOIN real_lalu ON {$realLaluJoin}
            LEFT JOIN kode_kantor k ON data_gabungan.kode_kantor = k.kode_kantor
            {$whereFinal}
            ORDER BY data_gabungan.kode_kantor ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grand = [
                'nilai_rbb' => 0,
                'realisasi_bulan_ini' => 0,
                'kekurangan_sd_bulan_lalu' => 0,
                'total_beban_target' => 0
            ];

            foreach ($rows as &$r) {
                $numericKeys = ['nilai_rbb', 'realisasi_bulan_ini', 'persentase_rbb_bulan_ini', 'kekurangan_sd_bulan_lalu', 'total_beban_target', 'persentase_rbb_plus_kekurangan'];
                foreach ($numericKeys as $key) {
                    $r[$key] = (float)($r[$key] ?? 0);
                }

                $grand['nilai_rbb'] += $r['nilai_rbb'];
                $grand['realisasi_bulan_ini'] += $r['realisasi_bulan_ini'];
                $grand['kekurangan_sd_bulan_lalu'] += $r['kekurangan_sd_bulan_lalu'];
                $grand['total_beban_target'] += $r['total_beban_target'];
            }
            unset($r);

            $grand['persentase_rbb_bulan_ini'] = $grand['nilai_rbb'] == 0 ? 0 : round(($grand['realisasi_bulan_ini'] / $grand['nilai_rbb']) * 100, 2);
            $grand['persentase_rbb_plus_kekurangan'] = $grand['total_beban_target'] == 0 ? 0 : round(($grand['realisasi_bulan_ini'] / $grand['total_beban_target']) * 100, 2);

            if ($compareMode === 'auto' && $grand['nilai_rbb'] == 0) {
                return $this->getRealisasiHistoryComparison($b, true);
            }

            $monthlyBreakdown = [];
            if ($kodeKantor !== '' && !$isFinancialReport) {
                $branchCol = "`{$kodeKantor}`";
                $sqlBreakdown = "
                    SELECT
                        r.kode_monbis,
                        ref.keterangan,
                        :breakdown_kode_kantor AS kode_kantor,
                        IFNULL(k.nama_kantor, CONCAT('Cabang ', :breakdown_kode_kantor_name)) AS nama_kantor,
                        r.periode,
                        IFNULL(r.{$branchCol}, 0) AS nilai_rbb,
                        IFNULL(real_month.total_realisasi, 0) AS realisasi_bulan_ini,
                        ROUND(IFNULL(real_month.total_realisasi, 0) / NULLIF(IFNULL(r.{$branchCol}, 0), 0) * 100, 2) AS persentase_rbb_bulan_ini,
                        (IFNULL(real_month.total_realisasi, 0) - IFNULL(r.{$branchCol}, 0)) AS selisih,
                        GREATEST(IFNULL(r.{$branchCol}, 0) - IFNULL(real_month.total_realisasi, 0), 0) AS kekurangan
                    FROM rbb r
                    INNER JOIN ref_rbb ref ON r.kode_monbis = ref.kode_monbis
                    LEFT JOIN kode_kantor k ON k.kode_kantor = :breakdown_kode_kantor_join
                    LEFT JOIN (
                        SELECT
                            DATE_FORMAT(tanggal_realisasi, '%Y-%m-01') AS periode,
                            SUM(realisasi_pokok) AS total_realisasi
                        FROM update_realisasi_kredit
                        WHERE kode_trans = '110'
                          AND kode_kantor = :breakdown_kode_kantor_real
                          AND tanggal_realisasi >= {$sqlYearStart}
                          AND tanggal_realisasi < {$sqlHarianNext}
                        GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                    ) real_month ON real_month.periode = r.periode
                    WHERE ref.kode_perkiraan IN (" . implode(',', array_map(static function ($index) {
                        return ':breakdown_kode_perkiraan_' . $index;
                    }, array_keys($kodePerkiraanList))) . ")
                      AND r.periode >= {$sqlYearStart}
                      AND r.periode <= {$sqlMonthStart}
                    ORDER BY r.periode DESC
                ";

                $stmtBreakdown = $this->pdo->prepare($sqlBreakdown);
                $stmtBreakdown->bindValue(':breakdown_kode_kantor', $kodeKantor, PDO::PARAM_STR);
                $stmtBreakdown->bindValue(':breakdown_kode_kantor_name', $kodeKantor, PDO::PARAM_STR);
                $stmtBreakdown->bindValue(':breakdown_kode_kantor_join', $kodeKantor, PDO::PARAM_STR);
                $stmtBreakdown->bindValue(':breakdown_kode_kantor_real', $kodeKantor, PDO::PARAM_STR);
                foreach ($kodePerkiraanList as $index => $kode) {
                    $stmtBreakdown->bindValue(':breakdown_kode_perkiraan_' . $index, $kode, PDO::PARAM_STR);
                }
                $stmtBreakdown->execute();
                $monthlyBreakdown = $stmtBreakdown->fetchAll(PDO::FETCH_ASSOC);

                foreach ($monthlyBreakdown as &$monthRow) {
                    foreach (['nilai_rbb', 'realisasi_bulan_ini', 'persentase_rbb_bulan_ini', 'selisih', 'kekurangan'] as $key) {
                        $monthRow[$key] = (float)($monthRow[$key] ?? 0);
                    }
                }
                unset($monthRow);
            }

            return sendResponse(200, "Berhasil meload Realisasi vs RBB bulan berjalan", [
                'meta' => [
                    'harian_date' => $harianDate,
                    'periode_bulan' => $monthStart,
                    'tahun_start' => $yearStart,
                    'kode_perkiraan' => $kodePerkiraanList,
                    'report_type' => $reportType,
                    'kode_kantor' => $kodeKantor !== '' ? $kodeKantor : '000',
                    'korwil' => $korwil,
                    'compare_mode' => 'rbb',
                    'fallback_history' => false
                ],
                'grand_total' => $grand,
                'monthly_breakdown' => $monthlyBreakdown,
                'data' => $rows
            ]);
        } catch (PDOException $e) {
            error_log("PDO Error Realisasi vs RBB: " . $e->getMessage());
            return sendResponse(500, "Database Query Error: " . $e->getMessage(), null);
        }
    }

    private function getRealisasiHistoryComparison($input = null, $fallbackHistory = false)
    {
        $b = is_array($input) ? $input : [];
        $harianDate = !empty($b['harian_date']) ? $b['harian_date'] : (!empty($b['tanggal']) ? $b['tanggal'] : date('Y-m-d'));
        $time = strtotime($harianDate);
        if (!$time) {
            return sendResponse(400, "Format harian_date tidak valid.");
        }

        $monthStart = date('Y-m-01', $time);
        $yearStart = date('Y-01-01', $time);
        $harianNext = date('Y-m-d', strtotime('+1 day', $time));
        $prevYearStart = date('Y-01-01', strtotime('-1 year', $time));
        $prevHarianNext = date('Y-m-d', strtotime('-1 year +1 day', $time));
        $year = date('Y', $time);
        $prevYear = date('Y', strtotime('-1 year', $time));

        $sqlMonthStart = $this->pdo->quote($monthStart);
        $sqlYearStart = $this->pdo->quote($yearStart);
        $sqlHarianNext = $this->pdo->quote($harianNext);
        $sqlPrevYearStart = $this->pdo->quote($prevYearStart);
        $sqlPrevHarianNext = $this->pdo->quote($prevHarianNext);

        $kodeKantor = '';
        if (!empty($b['kode_kantor']) && $b['kode_kantor'] !== '000' && strtolower((string)$b['kode_kantor']) !== 'konsolidasi') {
            $kodeKantor = str_pad((string)$b['kode_kantor'], 3, '0', STR_PAD_LEFT);
        }

        $korwil = strtoupper((string)($b['korwil'] ?? ''));
        $whereKantor = "k.kode_kantor BETWEEN '001' AND '028'";
        $params = [];

        if ($kodeKantor !== '') {
            $whereKantor = "k.kode_kantor = :kode_kantor";
            $params[':kode_kantor'] = $kodeKantor;
        } elseif ($korwil === 'SEMARANG') {
            $whereKantor = "k.kode_kantor BETWEEN '001' AND '007'";
        } elseif ($korwil === 'SOLO') {
            $whereKantor = "k.kode_kantor BETWEEN '008' AND '014'";
        } elseif ($korwil === 'BANYUMAS') {
            $whereKantor = "k.kode_kantor BETWEEN '015' AND '021'";
        } elseif ($korwil === 'PEKALONGAN') {
            $whereKantor = "k.kode_kantor BETWEEN '022' AND '028'";
        }

        try {
            $sql = "
                SELECT
                    k.kode_kantor,
                    k.nama_kantor,
                    IFNULL(cur.total_realisasi, 0) AS realisasi_bulan_ini,
                    IFNULL(prev.total_realisasi, 0) AS realisasi_tahun_lalu,
                    IFNULL(runoff.angsuran, 0) AS angsuran,
                    IFNULL(runoff.pelunasan, 0) AS pelunasan,
                    IFNULL(runoff.run_off, 0) AS run_off,
                    IFNULL(runoff.growth, 0) AS growth,
                    ROUND(IFNULL(runoff.growth, 0) / NULLIF(IFNULL(runoff.run_off, 0), 0) * 100, 2) AS growth_persen,
                    (IFNULL(cur.total_realisasi, 0) - IFNULL(prev.total_realisasi, 0)) AS selisih,
                    ROUND(
                        (IFNULL(cur.total_realisasi, 0) - IFNULL(prev.total_realisasi, 0)) /
                        NULLIF(IFNULL(prev.total_realisasi, 0), 0) * 100,
                        2
                    ) AS yoy_persen
                FROM kode_kantor k
                LEFT JOIN (
                    SELECT kode_kantor, SUM(realisasi_pokok) AS total_realisasi
                    FROM update_realisasi_kredit
                    WHERE kode_trans = '110'
                      AND tanggal_realisasi >= {$sqlYearStart}
                      AND tanggal_realisasi < {$sqlHarianNext}
                    GROUP BY kode_kantor
                ) cur ON cur.kode_kantor = k.kode_kantor
                LEFT JOIN (
                    SELECT kode_kantor, SUM(realisasi_pokok) AS total_realisasi
                    FROM update_realisasi_kredit
                    WHERE kode_trans = '110'
                      AND tanggal_realisasi >= {$sqlPrevYearStart}
                      AND tanggal_realisasi < {$sqlPrevHarianNext}
                    GROUP BY kode_kantor
                ) prev ON prev.kode_kantor = k.kode_kantor
                LEFT JOIN (
                    SELECT
                        kode_kantor,
                        SUM(COALESCE(angsuran, 0)) - SUM(COALESCE(pelunasan, 0)) AS angsuran,
                        SUM(COALESCE(pelunasan, 0)) AS pelunasan,
                        SUM(COALESCE(angsuran, 0)) AS run_off,
                        SUM(COALESCE(realisasi, 0)) + SUM(COALESCE(restrukturisasi, 0)) - SUM(COALESCE(angsuran, 0)) AS growth
                    FROM summary_kredit_harian_update
                    WHERE created >= {$sqlYearStart}
                      AND created < {$sqlHarianNext}
                    GROUP BY kode_kantor
                ) runoff ON runoff.kode_kantor = k.kode_kantor
                WHERE {$whereKantor}
                ORDER BY selisih ASC, realisasi_bulan_ini DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grand = [
                'realisasi_bulan_ini' => 0,
                'realisasi_tahun_lalu' => 0,
                'angsuran' => 0,
                'pelunasan' => 0,
                'run_off' => 0,
                'growth' => 0,
                'selisih' => 0,
                'yoy_persen' => 0,
                'growth_persen' => 0
            ];

            foreach ($rows as &$row) {
                foreach (['realisasi_bulan_ini', 'realisasi_tahun_lalu', 'angsuran', 'pelunasan', 'run_off', 'growth', 'selisih', 'yoy_persen', 'growth_persen'] as $key) {
                    $row[$key] = (float)($row[$key] ?? 0);
                }
                $grand['realisasi_bulan_ini'] += $row['realisasi_bulan_ini'];
                $grand['realisasi_tahun_lalu'] += $row['realisasi_tahun_lalu'];
                $grand['angsuran'] += $row['angsuran'];
                $grand['pelunasan'] += $row['pelunasan'];
                $grand['run_off'] += $row['run_off'];
                $grand['growth'] += $row['growth'];
                $grand['selisih'] += $row['selisih'];
            }
            unset($row);

            $grand['yoy_persen'] = $grand['realisasi_tahun_lalu'] == 0 ? 0 : round(($grand['selisih'] / $grand['realisasi_tahun_lalu']) * 100, 2);
            $grand['growth_persen'] = $grand['run_off'] == 0 ? 0 : round(($grand['growth'] / $grand['run_off']) * 100, 2);

            $monthlyBreakdown = [];
            if ($kodeKantor !== '') {
                $sqlMonthly = "
                    SELECT
                        months.periode,
                        :breakdown_kode_kantor AS kode_kantor,
                        IFNULL(k.nama_kantor, CONCAT('Cabang ', :breakdown_kode_kantor_name)) AS nama_kantor,
                        IFNULL(cur.total_realisasi, 0) AS realisasi_bulan_ini,
                        IFNULL(prev.total_realisasi, 0) AS realisasi_tahun_lalu,
                        IFNULL(runoff.angsuran, 0) AS angsuran,
                        IFNULL(runoff.pelunasan, 0) AS pelunasan,
                        IFNULL(runoff.run_off, 0) AS run_off,
                        IFNULL(runoff.growth, 0) AS growth,
                        ROUND(IFNULL(runoff.growth, 0) / NULLIF(IFNULL(runoff.run_off, 0), 0) * 100, 2) AS growth_persen,
                        (IFNULL(cur.total_realisasi, 0) - IFNULL(prev.total_realisasi, 0)) AS selisih,
                        ROUND(
                            (IFNULL(cur.total_realisasi, 0) - IFNULL(prev.total_realisasi, 0)) /
                            NULLIF(IFNULL(prev.total_realisasi, 0), 0) * 100,
                            2
                        ) AS yoy_persen
                    FROM (
                        SELECT DATE_FORMAT(tanggal_realisasi, '%Y-%m-01') AS periode
                        FROM update_realisasi_kredit
                        WHERE kode_trans = '110'
                          AND kode_kantor = :breakdown_kode_kantor_cur_month
                          AND tanggal_realisasi >= {$sqlYearStart}
                          AND tanggal_realisasi < {$sqlHarianNext}
                        GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                        UNION
                        SELECT DATE_ADD(DATE_FORMAT(tanggal_realisasi, '%Y-%m-01'), INTERVAL 1 YEAR) AS periode
                        FROM update_realisasi_kredit
                        WHERE kode_trans = '110'
                          AND kode_kantor = :breakdown_kode_kantor_prev_month
                          AND tanggal_realisasi >= {$sqlPrevYearStart}
                          AND tanggal_realisasi < {$sqlPrevHarianNext}
                        GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                    ) months
                    LEFT JOIN kode_kantor k ON k.kode_kantor = :breakdown_kode_kantor_join
                    LEFT JOIN (
                        SELECT DATE_FORMAT(tanggal_realisasi, '%Y-%m-01') AS periode, SUM(realisasi_pokok) AS total_realisasi
                        FROM update_realisasi_kredit
                        WHERE kode_trans = '110'
                          AND kode_kantor = :breakdown_kode_kantor_cur
                          AND tanggal_realisasi >= {$sqlYearStart}
                          AND tanggal_realisasi < {$sqlHarianNext}
                        GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                    ) cur ON cur.periode = months.periode
                    LEFT JOIN (
                        SELECT DATE_ADD(DATE_FORMAT(tanggal_realisasi, '%Y-%m-01'), INTERVAL 1 YEAR) AS periode, SUM(realisasi_pokok) AS total_realisasi
                        FROM update_realisasi_kredit
                        WHERE kode_trans = '110'
                          AND kode_kantor = :breakdown_kode_kantor_prev
                          AND tanggal_realisasi >= {$sqlPrevYearStart}
                          AND tanggal_realisasi < {$sqlPrevHarianNext}
                        GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                    ) prev ON prev.periode = months.periode
                    LEFT JOIN (
                        SELECT
                            months_runoff.periode,
                            SUM(COALESCE(s.angsuran, 0)) - SUM(COALESCE(s.pelunasan, 0)) AS angsuran,
                            SUM(COALESCE(s.pelunasan, 0)) AS pelunasan,
                            SUM(COALESCE(s.angsuran, 0)) AS run_off,
                            SUM(COALESCE(s.realisasi, 0)) + SUM(COALESCE(s.restrukturisasi, 0)) - SUM(COALESCE(s.angsuran, 0)) AS growth
                        FROM (
                            SELECT periode FROM (
                                SELECT DATE_FORMAT(tanggal_realisasi, '%Y-%m-01') AS periode
                                FROM update_realisasi_kredit
                                WHERE kode_trans = '110'
                                  AND kode_kantor = :breakdown_kode_kantor_cur_runoff_month
                                  AND tanggal_realisasi >= {$sqlYearStart}
                                  AND tanggal_realisasi < {$sqlHarianNext}
                                GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                                UNION
                                SELECT DATE_ADD(DATE_FORMAT(tanggal_realisasi, '%Y-%m-01'), INTERVAL 1 YEAR) AS periode
                                FROM update_realisasi_kredit
                                WHERE kode_trans = '110'
                                  AND kode_kantor = :breakdown_kode_kantor_prev_runoff_month
                                  AND tanggal_realisasi >= {$sqlPrevYearStart}
                                  AND tanggal_realisasi < {$sqlPrevHarianNext}
                                GROUP BY DATE_FORMAT(tanggal_realisasi, '%Y-%m-01')
                            ) month_source
                        ) months_runoff
                        LEFT JOIN summary_kredit_harian_update s
                          ON s.kode_kantor = :breakdown_kode_kantor_runoff
                         /*
                          * PENTING: range dimulai dari awal bulan masing-masing.
                          * Sebelumnya menggunakan {$sqlYearStart}, sehingga Run Off dan
                          * Growth Februari berisi Januari + Februari, Maret berisi
                          * Januari + Februari + Maret, dan seterusnya.
                          */
                         AND s.created >= months_runoff.periode
                         AND s.created < LEAST(
                             DATE_ADD(months_runoff.periode, INTERVAL 1 MONTH),
                             {$sqlHarianNext}
                         )
                        GROUP BY months_runoff.periode
                    ) runoff ON runoff.periode = months.periode
                    ORDER BY months.periode DESC
                ";

                $stmtMonthly = $this->pdo->prepare($sqlMonthly);
                foreach ([
                    ':breakdown_kode_kantor' => $kodeKantor,
                    ':breakdown_kode_kantor_name' => $kodeKantor,
                    ':breakdown_kode_kantor_cur_month' => $kodeKantor,
                    ':breakdown_kode_kantor_prev_month' => $kodeKantor,
                    ':breakdown_kode_kantor_join' => $kodeKantor,
                    ':breakdown_kode_kantor_cur' => $kodeKantor,
                    ':breakdown_kode_kantor_prev' => $kodeKantor,
                    ':breakdown_kode_kantor_cur_runoff_month' => $kodeKantor,
                    ':breakdown_kode_kantor_prev_runoff_month' => $kodeKantor,
                    ':breakdown_kode_kantor_runoff' => $kodeKantor
                ] as $key => $value) {
                    $stmtMonthly->bindValue($key, $value, PDO::PARAM_STR);
                }
                $stmtMonthly->execute();
                $monthlyBreakdown = $stmtMonthly->fetchAll(PDO::FETCH_ASSOC);

                foreach ($monthlyBreakdown as &$monthRow) {
                    foreach (['realisasi_bulan_ini', 'realisasi_tahun_lalu', 'angsuran', 'pelunasan', 'run_off', 'growth', 'selisih', 'yoy_persen', 'growth_persen'] as $key) {
                        $monthRow[$key] = (float)($monthRow[$key] ?? 0);
                    }
                }
                unset($monthRow);
            }

            return sendResponse(200, "Berhasil meload History Realisasi YoY", [
                'meta' => [
                    'harian_date' => $harianDate,
                    'periode_bulan' => $monthStart,
                    'tahun_start' => $yearStart,
                    'tahun_pembanding_start' => $prevYearStart,
                    'tahun' => $year,
                    'tahun_pembanding' => $prevYear,
                    'kode_kantor' => $kodeKantor !== '' ? $kodeKantor : '000',
                    'korwil' => $korwil,
                    'compare_mode' => 'history',
                    'fallback_history' => $fallbackHistory
                ],
                'grand_total' => $grand,
                'monthly_breakdown' => $monthlyBreakdown,
                'data' => $rows
            ]);
        } catch (PDOException $e) {
            error_log("PDO Error History Realisasi RBB: " . $e->getMessage());
            return sendResponse(500, "Database Query Error: " . $e->getMessage(), null);
        }
    }

    public function getLapkeuRbbVsRealisasi($input = null)
    {
        set_time_limit(120);
        ini_set('memory_limit', '512M');

        $b = is_array($input) ? $input : [];
        $jenis = strtolower((string)($b['jenis_laporan'] ?? 'neraca'));
        if (!in_array($jenis, ['neraca', 'laba_rugi'], true)) {
            return sendResponse(400, "Jenis laporan tidak valid.");
        }

        $harianDate = !empty($b['harian_date']) ? (string)$b['harian_date'] : date('Y-m-d');
        $time = strtotime($harianDate);
        if (!$time) {
            return sendResponse(400, "Format harian_date tidak valid.");
        }

        $useYearEnd = filter_var($b['use_year_end'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $periodeRbb = $useYearEnd ? date('Y-12-01', $time) : date('Y-m-01', $time);
        $periodeRbbYearEnd = date('Y-12-01', $time);
        if (!empty($b['periode_rbb'])) {
            $periodeTime = strtotime((string)$b['periode_rbb']);
            if ($periodeTime) {
                $periodeRbb = date('Y-m-01', $periodeTime);
            }
        }

        $scope = $this->buildLapkeuRbbScope($b);
        $targetExpression = $scope['target_expression'];
        $actualWhere = $scope['actual_where'];
        $scopeLabel = $scope['label'];

        $sql = $jenis === 'laba_rugi'
            ? $this->buildLapkeuRbbLabaRugiSql($targetExpression, $actualWhere)
            : $this->buildLapkeuRbbNeracaSql($targetExpression, $actualWhere);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':periode_rbb', $periodeRbb, PDO::PARAM_STR);
            $stmt->bindValue(':periode_rbb_year_end', $periodeRbbYearEnd, PDO::PARAM_STR);
            $stmt->bindValue(':periode_rbb_meta', $periodeRbb, PDO::PARAM_STR);
            $stmt->bindValue(':actual_date', $harianDate, PDO::PARAM_STR);
            $stmt->bindValue(':actual_date_meta', $harianDate, PDO::PARAM_STR);
            if ($jenis === 'neraca') {
                $stmt->bindValue(':actual_date_210', $harianDate, PDO::PARAM_STR);
                $stmt->bindValue(':actual_date_formula', $harianDate, PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $summary = [
                'target_rbb' => 0.0,
                'realisasi_actual' => 0.0,
                'selisih' => 0.0,
                'jumlah_pos' => 0,
                'pencapaian_persen' => 0.0
            ];

            foreach ($rows as &$row) {
                foreach (['target_rbb', 'realisasi_actual', 'selisih', 'pencapaian_persen'] as $key) {
                    $row[$key] = (float)($row[$key] ?? 0);
                }
                $summary['target_rbb'] += $row['target_rbb'];
                $summary['realisasi_actual'] += $row['realisasi_actual'];
                $summary['selisih'] += $row['selisih'];
                $summary['jumlah_pos']++;
            }
            unset($row);

            $summary['pencapaian_persen'] = $summary['target_rbb'] == 0.0
                ? 0.0
                : round(($summary['realisasi_actual'] / $summary['target_rbb']) * 100, 2);
            $categorySummary = $this->buildLapkeuRbbCategorySummary($rows, $jenis);

            return sendResponse(200, "Berhasil memuat RBB vs Realisasi", [
                'meta' => [
                    'jenis_laporan' => $jenis,
                    'harian_date' => $harianDate,
                    'periode_rbb' => $periodeRbb,
                    'periode_rbb_year_end' => $periodeRbbYearEnd,
                    'use_year_end' => $useYearEnd,
                    'scope' => $scopeLabel,
                    'kode_kantor' => $scope['kode_kantor'],
                    'korwil' => $scope['korwil']
                ],
                'summary' => $summary,
                'category_summary' => $categorySummary,
                'data' => $rows
            ]);
        } catch (PDOException $e) {
            error_log("PDO Error Lapkeu RBB vs Realisasi: " . $e->getMessage());
            return sendResponse(500, "Database Query Error: " . $e->getMessage(), null);
        }
    }

    private function buildLapkeuRbbCategorySummary(array $rows, string $jenis): array
    {
        $byCode = [];
        $byPerk = [];
        foreach ($rows as $row) {
            $code = (string)($row['kode_monbis'] ?? '');
            $perk = (string)($row['kode_perkiraan'] ?? '');
            if ($code !== '') {
                $byCode[$code] = $row;
            }
            if ($perk !== '') {
                $byPerk[$perk] = $row;
            }
        }

        $make = function (string $key, string $label, ?array $row, int $count, string $tone, string $note): array {
            $target = (float)($row['target_rbb'] ?? 0);
            $actual = (float)($row['realisasi_actual'] ?? 0);
            $selisih = $actual - $target;
            return [
                'key' => $key,
                'label' => $label,
                'target_rbb' => $target,
                'realisasi_actual' => $actual,
                'selisih' => $selisih,
                'pencapaian_persen' => $target == 0.0 ? 0.0 : round(($actual / $target) * 100, 2),
                'jumlah_pos' => $count,
                'tone' => $tone,
                'note' => $note
            ];
        };

        if ($jenis === 'laba_rugi') {
            $pendapatan = $this->sumLapkeuRbbRows($rows, function ($row) {
                return in_array((string)($row['kode_perkiraan'] ?? ''), ['401', '402'], true);
            });
            $beban = $this->sumLapkeuRbbRows($rows, function ($row) {
                return in_array((string)($row['kode_perkiraan'] ?? ''), ['501', '502'], true);
            });
            return [
                $make('pendapatan', 'Pendapatan', $pendapatan, $this->countLapkeuRbbRows($rows, 'PENDAPATAN'), 'green', 'Total pendapatan operasional dan non operasional'),
                $make('biaya', 'Biaya', $beban, $this->countLapkeuRbbRows($rows, 'BEBAN'), 'orange', 'Total beban operasional dan non operasional'),
                $make('laba', 'Laba Rugi Berjalan', $byCode['261'] ?? $byPerk['kode monbis 259 + 260'] ?? null, 1, 'blue', 'Surplus berjalan sebelum pajak')
            ];
        }

        return [
            $make('aset', 'Aset Gabungan', $byPerk['1'] ?? $byCode['95'] ?? null, $this->countLapkeuRbbRows($rows, 'ASET'), 'blue', 'Total aset bersih'),
            $make('kewajiban', 'Kewajiban', $byPerk['2'] ?? null, $this->countLapkeuRbbRows($rows, 'LIABILITAS'), 'purple', 'Total liabilitas/kewajiban'),
            $make('ekuitas', 'Ekuitas', $byPerk['3'] ?? null, $this->countLapkeuRbbRows($rows, 'EKUITAS'), 'orange', 'Modal dan saldo laba')
        ];
    }

    private function sumLapkeuRbbRows(array $rows, callable $matcher): array
    {
        $sum = ['target_rbb' => 0.0, 'realisasi_actual' => 0.0];
        foreach ($rows as $row) {
            if (!$matcher($row)) {
                continue;
            }
            $sum['target_rbb'] += (float)($row['target_rbb'] ?? 0);
            $sum['realisasi_actual'] += (float)($row['realisasi_actual'] ?? 0);
        }
        return $sum;
    }

    private function countLapkeuRbbRows(array $rows, string $kategori): int
    {
        $count = 0;
        foreach ($rows as $row) {
            if (strtoupper((string)($row['kategori'] ?? '')) === $kategori) {
                $count++;
            }
        }
        return $count;
    }

    private function buildLapkeuRbbScope(array $input): array
    {
        $branchColumns = ['001','002','003','004','005','006','007','008','009','010','011','012','013','014','015','016','017','018','019','020','021','022','023','024','025','026','027','028'];
        $sumAll = [];
        foreach ($branchColumns as $code) {
            $sumAll[] = "COALESCE(r.`{$code}`,0)";
        }

        $kodeKantor = '';
        if (!empty($input['kode_kantor']) && (string)$input['kode_kantor'] !== '000' && strtolower((string)$input['kode_kantor']) !== 'konsolidasi') {
            $kodeKantor = str_pad((string)$input['kode_kantor'], 3, '0', STR_PAD_LEFT);
            if (!in_array($kodeKantor, $branchColumns, true)) {
                $kodeKantor = '';
            }
        }

        $korwil = strtoupper((string)($input['korwil'] ?? ''));
        $rangeStart = '';
        $rangeEnd = '';
        if ($korwil === 'SEMARANG') {
            $rangeStart = '001'; $rangeEnd = '007';
        } elseif ($korwil === 'SOLO') {
            $rangeStart = '008'; $rangeEnd = '014';
        } elseif ($korwil === 'BANYUMAS') {
            $rangeStart = '015'; $rangeEnd = '021';
        } elseif ($korwil === 'PEKALONGAN') {
            $rangeStart = '022'; $rangeEnd = '028';
        }

        if ($kodeKantor !== '') {
            return [
                'kode_kantor' => $kodeKantor,
                'korwil' => '',
                'label' => 'CABANG ' . $kodeKantor,
                'target_expression' => "COALESCE(r.`{$kodeKantor}`,0)",
                'actual_where' => "ah.kode_kantor = '{$kodeKantor}'"
            ];
        }

        if ($rangeStart !== '' && $rangeEnd !== '') {
            $parts = [];
            foreach ($branchColumns as $code) {
                if ($code >= $rangeStart && $code <= $rangeEnd) {
                    $parts[] = "COALESCE(r.`{$code}`,0)";
                }
            }
            return [
                'kode_kantor' => '000',
                'korwil' => $korwil,
                'label' => 'KORWIL ' . $korwil,
                'target_expression' => implode(' + ', $parts),
                'actual_where' => "ah.kode_kantor BETWEEN '{$rangeStart}' AND '{$rangeEnd}'"
            ];
        }

        return [
            'kode_kantor' => '000',
            'korwil' => '',
            'label' => 'KONSOLIDASI',
            'target_expression' => "COALESCE(r.`000`, (" . implode(' + ', $sumAll) . "))",
            'actual_where' => "ah.kode_kantor BETWEEN '000' AND '028'"
        ];
    }

    private function buildLapkeuRbbNeracaSql(string $targetExpression, string $actualWhere): string
    {
        return "
            WITH ref_data AS (
                SELECT
                    ref.id_ref,
                    ref.kode_monbis,
                    ref.kode_perkiraan,
                    ref.sandi_lbbpr,
                    ref.kategori,
                    ref.keterangan,
                    CASE
                        WHEN UPPER(TRIM(ref.kategori)) IN ('PASIFA','PASIVA')
                          OR ref.kode_perkiraan = '2+3'
                        THEN 1 ELSE 0
                    END AS is_pasifa,
                    CASE
                        WHEN UPPER(TRIM(ref.kategori)) = 'ASET'
                         AND ref.kode_perkiraan = '1'
                        THEN 1 ELSE 0
                    END AS is_total_aset,
                    CASE
                        WHEN UPPER(TRIM(ref.kategori)) = 'ASET' THEN 1
                        WHEN UPPER(TRIM(ref.kategori)) = 'LIABILITAS' THEN 2
                        WHEN UPPER(TRIM(ref.kategori)) = 'EKUITAS' THEN 3
                        WHEN UPPER(TRIM(ref.kategori)) IN ('PASIFA','PASIVA') THEN 4
                        ELSE 5
                    END AS urutan_kelompok
                FROM ref_rbb ref
                WHERE ref.is_active = 1
                  AND (
                    UPPER(TRIM(ref.kategori)) IN ('ASET','LIABILITAS','EKUITAS','PASIFA','PASIVA')
                    OR ref.kode_perkiraan = '2+3'
                  )
            ),
            target_rbb AS (
                SELECT r.kode_monbis, MAX({$targetExpression}) AS target_rbb
                FROM rbb r
                WHERE r.periode = :periode_rbb
                GROUP BY r.kode_monbis
            ),
            target_rbb_year_end AS (
                SELECT r.kode_monbis, MAX({$targetExpression}) AS target_rbb_year_end
                FROM rbb r
                WHERE r.periode = :periode_rbb_year_end
                GROUP BY r.kode_monbis
            ),
            realisasi_per_akun AS (
                SELECT ah.kode_perk AS kode_perkiraan,
                       SUM(COALESCE(ah.saldo_akhir,0)) AS realisasi_raw,
                       COUNT(*) AS jumlah_data
                FROM acc_history ah
                WHERE ah.tanggal = :actual_date
                  AND {$actualWhere}
                GROUP BY ah.kode_perk
            ),
            saldo_akun_210 AS (
                SELECT SUM(COALESCE(ah.saldo_akhir,0)) AS saldo_210
                FROM acc_history ah
                WHERE ah.tanggal = :actual_date_210
                  AND ah.kode_kantor BETWEEN '000' AND '028'
                  AND ah.kode_perk = '210'
            ),
            realisasi_pasifa AS (
                SELECT
                    SUM(CASE WHEN ah.kode_perk = '2' THEN COALESCE(ah.saldo_akhir,0) ELSE 0 END) AS total_liabilitas,
                    SUM(CASE WHEN ah.kode_perk = '3' THEN COALESCE(ah.saldo_akhir,0) ELSE 0 END) AS total_ekuitas,
                    SUM(CASE WHEN ah.kode_perk IN ('2','3') THEN COALESCE(ah.saldo_akhir,0) ELSE 0 END) AS realisasi_pasifa,
                    MAX(CASE WHEN ah.kode_perk = '2' THEN 1 ELSE 0 END) AS akun_2_ada,
                    MAX(CASE WHEN ah.kode_perk = '3' THEN 1 ELSE 0 END) AS akun_3_ada
                FROM acc_history ah
                WHERE ah.tanggal = :actual_date_formula
                  AND {$actualWhere}
            ),
            hasil AS (
                SELECT
                    ref.id_ref, ref.kode_monbis, ref.kode_perkiraan, ref.sandi_lbbpr, ref.kategori, ref.keterangan,
                    ref.urutan_kelompok,
                    COALESCE(trg.target_rbb,0) AS target_rbb,
                    COALESCE(trg_ye.target_rbb_year_end,0) AS target_rbb_year_end,
                    CASE
                        WHEN ref.is_pasifa = 1 THEN COALESCE(pas.realisasi_pasifa,0)
                        WHEN ref.is_total_aset = 1 THEN COALESCE(act.realisasi_raw,0) - COALESCE(elm.saldo_210,0)
                        ELSE COALESCE(act.realisasi_raw,0)
                    END AS realisasi_actual,
                    CASE
                        WHEN ref.is_pasifa = 1 THEN
                            CASE
                                WHEN COALESCE(pas.akun_2_ada,0) = 0 AND COALESCE(pas.akun_3_ada,0) = 0 THEN 'AKUN 2 DAN 3 BELUM DITEMUKAN'
                                WHEN COALESCE(pas.akun_2_ada,0) = 0 THEN 'AKUN 2 / LIABILITAS BELUM DITEMUKAN'
                                WHEN COALESCE(pas.akun_3_ada,0) = 0 THEN 'AKUN 3 / EKUITAS BELUM DITEMUKAN'
                                ELSE 'OK'
                            END
                        WHEN ref.kode_perkiraan IS NULL OR ref.kode_perkiraan = '' THEN 'KODE PERKIRAAN BELUM DIISI'
                        WHEN LOWER(ref.kode_perkiraan) = 'xxx' THEN 'KODE PERKIRAAN MASIH XXX'
                        WHEN act.kode_perkiraan IS NULL AND COALESCE(trg.target_rbb,0) <> 0 THEN 'RBB ADA - REALISASI BELUM DITEMUKAN'
                        WHEN act.kode_perkiraan IS NULL AND COALESCE(trg.target_rbb,0) = 0 THEN 'OK'
                        ELSE 'OK'
                    END AS status_crosscheck
                FROM ref_data ref
                LEFT JOIN target_rbb trg ON trg.kode_monbis = ref.kode_monbis
                LEFT JOIN target_rbb_year_end trg_ye ON trg_ye.kode_monbis = ref.kode_monbis
                LEFT JOIN realisasi_per_akun act ON act.kode_perkiraan = ref.kode_perkiraan
                CROSS JOIN saldo_akun_210 elm
                CROSS JOIN realisasi_pasifa pas
            )
            SELECT
                id_ref, kode_monbis, kode_perkiraan, sandi_lbbpr, kategori, keterangan,
                :periode_rbb_meta AS periode_rbb,
                :actual_date_meta AS tanggal_actual,
                target_rbb,
                realisasi_actual,
                realisasi_actual - target_rbb AS selisih,
                ROUND(CASE WHEN COALESCE(target_rbb,0) = 0 THEN 0 ELSE realisasi_actual / target_rbb * 100 END, 2) AS pencapaian_persen,
                COALESCE(target_rbb_year_end, 0) AS target_rbb_year_end,
                realisasi_actual - COALESCE(target_rbb_year_end, 0) AS selisih_year_end,
                ROUND(CASE WHEN COALESCE(target_rbb_year_end,0) = 0 THEN 0 ELSE realisasi_actual / target_rbb_year_end * 100 END, 2) AS pencapaian_year_end_persen,
                status_crosscheck
            FROM hasil
            WHERE status_crosscheck = 'OK'
            ORDER BY urutan_kelompok ASC, id_ref ASC
        ";
    }

    private function buildLapkeuRbbLabaRugiSql(string $targetExpression, string $actualWhere): string
    {
        return "
            WITH ref_data AS (
                SELECT
                    ref.id_ref,
                    ref.kode_monbis,
                    ref.kode_perkiraan,
                    ref.sandi_lbbpr,
                    ref.kategori,
                    ref.keterangan,
                    CASE
                        WHEN UPPER(TRIM(ref.kategori)) = 'LAINNYA'
                         AND ref.kode_monbis IN ('259','260','261')
                        THEN 1 ELSE 0
                    END AS is_formula
                FROM ref_rbb ref
                WHERE ref.is_active = 1
                  AND (
                    UPPER(TRIM(ref.kategori)) IN ('PENDAPATAN','BEBAN')
                    OR (
                        UPPER(TRIM(ref.kategori)) = 'LAINNYA'
                        AND ref.kode_monbis IN ('259','260','261')
                    )
                  )
            ),
            target_rbb AS (
                SELECT r.kode_monbis, MAX({$targetExpression}) AS target_rbb
                FROM rbb r
                WHERE r.periode = :periode_rbb
                GROUP BY r.kode_monbis
            ),
            target_rbb_year_end AS (
                SELECT r.kode_monbis, MAX({$targetExpression}) AS target_rbb_year_end
                FROM rbb r
                WHERE r.periode = :periode_rbb_year_end
                GROUP BY r.kode_monbis
            ),
            realisasi_per_akun AS (
                SELECT ah.kode_perk AS kode_perkiraan,
                       SUM(COALESCE(ah.saldo_akhir,0)) AS realisasi_actual,
                       COUNT(*) AS jumlah_data
                FROM acc_history ah
                WHERE ah.tanggal = :actual_date
                  AND {$actualWhere}
                GROUP BY ah.kode_perk
            ),
            sumber_formula AS (
                SELECT
                    COALESCE(SUM(CASE WHEN kode_perkiraan = '401' THEN realisasi_actual ELSE 0 END),0) AS pendapatan_operasional,
                    COALESCE(SUM(CASE WHEN kode_perkiraan = '501' THEN realisasi_actual ELSE 0 END),0) AS beban_operasional,
                    COALESCE(SUM(CASE WHEN kode_perkiraan = '402' THEN realisasi_actual ELSE 0 END),0) AS pendapatan_non_operasional,
                    COALESCE(SUM(CASE WHEN kode_perkiraan = '502' THEN realisasi_actual ELSE 0 END),0) AS beban_non_operasional,
                    MAX(CASE WHEN kode_perkiraan = '401' AND jumlah_data > 0 THEN 1 ELSE 0 END) AS akun_401_ada,
                    MAX(CASE WHEN kode_perkiraan = '501' AND jumlah_data > 0 THEN 1 ELSE 0 END) AS akun_501_ada,
                    MAX(CASE WHEN kode_perkiraan = '402' AND jumlah_data > 0 THEN 1 ELSE 0 END) AS akun_402_ada,
                    MAX(CASE WHEN kode_perkiraan = '502' AND jumlah_data > 0 THEN 1 ELSE 0 END) AS akun_502_ada
                FROM realisasi_per_akun
            ),
            formula_laba_rugi AS (
                SELECT '259' AS kode_monbis, (sf.pendapatan_operasional - sf.beban_operasional) AS realisasi_actual,
                    CASE WHEN sf.akun_401_ada = 1 AND sf.akun_501_ada = 1 THEN 'OK' ELSE 'SUMBER FORMULA BELUM LENGKAP' END AS status_formula
                FROM sumber_formula sf
                UNION ALL
                SELECT '260' AS kode_monbis, (sf.pendapatan_non_operasional - sf.beban_non_operasional) AS realisasi_actual,
                    CASE WHEN sf.akun_402_ada = 1 AND sf.akun_502_ada = 1 THEN 'OK' ELSE 'SUMBER FORMULA BELUM LENGKAP' END AS status_formula
                FROM sumber_formula sf
                UNION ALL
                SELECT '261' AS kode_monbis,
                    (sf.pendapatan_operasional - sf.beban_operasional) + (sf.pendapatan_non_operasional - sf.beban_non_operasional) AS realisasi_actual,
                    CASE WHEN sf.akun_401_ada = 1 AND sf.akun_501_ada = 1 AND sf.akun_402_ada = 1 AND sf.akun_502_ada = 1 THEN 'OK' ELSE 'SUMBER FORMULA BELUM LENGKAP' END AS status_formula
                FROM sumber_formula sf
            ),
            hasil AS (
                SELECT
                    ref.id_ref, ref.kode_monbis, ref.kode_perkiraan, ref.sandi_lbbpr, ref.kategori, ref.keterangan,
                    COALESCE(trg.target_rbb,0) AS target_rbb,
                    COALESCE(trg_ye.target_rbb_year_end,0) AS target_rbb_year_end,
                    CASE WHEN ref.is_formula = 1 THEN COALESCE(frm.realisasi_actual,0) ELSE COALESCE(act.realisasi_actual,0) END AS realisasi_actual,
                    CASE
                        WHEN ref.is_formula = 1 THEN COALESCE(frm.status_formula, 'SUMBER FORMULA BELUM LENGKAP')
                        WHEN ref.kode_perkiraan IS NULL OR ref.kode_perkiraan = '' THEN 'KODE PERKIRAAN BELUM DIISI'
                        WHEN LOWER(ref.kode_perkiraan) = 'xxx' THEN 'KODE PERKIRAAN MASIH XXX'
                        WHEN act.kode_perkiraan IS NULL AND COALESCE(trg.target_rbb,0) <> 0 THEN 'RBB ADA - REALISASI BELUM DITEMUKAN'
                        WHEN act.kode_perkiraan IS NULL AND COALESCE(trg.target_rbb,0) = 0 THEN 'OK'
                        ELSE 'OK'
                    END AS status_crosscheck
                FROM ref_data ref
                LEFT JOIN target_rbb trg ON trg.kode_monbis = ref.kode_monbis
                LEFT JOIN target_rbb_year_end trg_ye ON trg_ye.kode_monbis = ref.kode_monbis
                LEFT JOIN realisasi_per_akun act ON act.kode_perkiraan = ref.kode_perkiraan
                LEFT JOIN formula_laba_rugi frm ON frm.kode_monbis = ref.kode_monbis
            )
            SELECT
                id_ref, kode_monbis, kode_perkiraan, sandi_lbbpr, kategori, keterangan,
                :periode_rbb_meta AS periode_rbb,
                :actual_date_meta AS tanggal_actual,
                target_rbb,
                realisasi_actual,
                realisasi_actual - target_rbb AS selisih,
                ROUND(CASE WHEN COALESCE(target_rbb,0) = 0 THEN 0 ELSE realisasi_actual / target_rbb * 100 END, 2) AS pencapaian_persen,
                COALESCE(target_rbb_year_end, 0) AS target_rbb_year_end,
                realisasi_actual - COALESCE(target_rbb_year_end, 0) AS selisih_year_end,
                ROUND(CASE WHEN COALESCE(target_rbb_year_end,0) = 0 THEN 0 ELSE realisasi_actual / target_rbb_year_end * 100 END, 2) AS pencapaian_year_end_persen,
                status_crosscheck
            FROM hasil
            WHERE status_crosscheck = 'OK'
            ORDER BY
                CASE
                    WHEN UPPER(TRIM(kategori)) = 'PENDAPATAN' THEN 1
                    WHEN UPPER(TRIM(kategori)) = 'BEBAN' THEN 2
                    WHEN kode_monbis = '259' THEN 3
                    WHEN kode_monbis = '260' THEN 4
                    WHEN kode_monbis = '261' THEN 5
                    ELSE 6
                END,
                id_ref ASC
        ";
    }
}
