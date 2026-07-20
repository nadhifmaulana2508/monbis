<?php

require_once __DIR__ . '/../helpers/response.php';
// require_once __DIR__ . '/../helpers/MobHelper.php'; // Aktifkan jika butuh helper lain

class DashboardController{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }



    /**
     * =================================================================
     * FUNGSI MANDOR (EXECUTIVE DASHBOARD ULTIMATE)
     * =================================================================
     */
    public function getExecutiveDashboard($input = []) {
        try {
            // Tentukan base date standar (kalau tidak ada dari FE, pakai hari ini)
            $baseDate = $input['harian_date'] ?? date('Y-m-d');

            // 🔥 TRIK SAKTI 1: Clone input khusus untuk H-1 (Metrik DPK)
            $inputH1 = $input;
            // Set tanggal mundur 1 hari dari base date
            $inputH1['harian_date'] = date('Y-m-d', strtotime($baseDate . ' -1 day'));

            // 🔥 TRIK SAKTI 2: Clone input khusus untuk blok Realisasi/Runoff yang butuh Realtime
            $inputRealtime = $input;
            
            // Cek apakah FE ngirim 'harian_date_realisasi'
            if (isset($input['harian_date_realisasi'])) {
                // Timpa harian_date dengan tanggal Realtime dari FE
                $inputRealtime['harian_date'] = $input['harian_date_realisasi']; 
            }

            // =========================================================================
            // 🔥 LOGIKA PINTAR REPORT CLOSING (SOLUSI GANTI BULAN)
            // =========================================================================
            // Jika bulan pada harian_date berbeda dengan bulan pada realtime date (hari ini),
            // berarti user sedang melihat data closing akhir bulan (contoh: 31 Maret, dibuka 1 April).
            // Maka kita paksa data realtimenya MUNDUR mengikuti harian_date agar tidak kosong!
            
            $month_harian   = date('Y-m', strtotime($baseDate));
            $month_realtime = date('Y-m', strtotime($inputRealtime['harian_date']));
            
            if ($month_harian !== $month_realtime) {
                $inputRealtime['harian_date'] = $baseDate;
            }
            // =========================================================================

            // Kita kumpulkan semua puzzle-nya di sini!
            $data = [
                // 1. Metrik NPL & Kolektibilitas (Pakai $input standar)
                'tren_npl'                => $this->getTrenNPL($input),
                'top_bottom_npl'          => $this->getTopBottomNPL($input),
                'kenaikan_penurunan_npl'  => $this->getTopKenaikanPenurunanNPL($input),
                'flow_vs_recovery_npl'    => $this->getFlowVsRecoveryNPL($input),
                
                // 2. Metrik Kredit & Realisasi
                // top_bottom & repayment pakai $input standar
                'top_bottom_realisasi'    => $this->getTopBottomRealisasiNominatif($input),
                'repayment_rate'          => $this->getRepaymentRateCabang($input),
                
                // 🔥 Ini yang pakai $input (Bisa Realtime Hari Ini, bisa ikut Closing)
                'tren_runoff_realisasi'   => $this->getTrenRunOffRealisasi($input),
                'realisasi_by_produk'     => $this->getRealisasiRealtimeByProduk($input),
                'runoff_vs_realisasi'     => $this->getRunOffVsRealisasiKorwil($input),
                'saldo_bank'              => $this->getSaldoBank($input),
                
                // 3. Metrik DPK (Dana Pihak Ketiga) (🔥 Pakai $inputH1 -> Pasti H-1)
                'perkembangan_deposito'   => $this->getPerkembanganDeposito($input),
                'perkembangan_tabungan'   => $this->getPerkembanganTabungan($input),
                'tren_portofolio_kredit'  => $this->getTrenPortofolioKredit($input)
            ];

            // Kirim responsenya ke Front-End dengan penuh gaya
            sendResponse(200, "Berhasil memuat Executive Dashboard Ultimate", $data);

        } catch (Exception $e) {
            // Kalau ada error level dewa, tangkap di sini
            error_log("Error Executive Dashboard: " . $e->getMessage());
            sendResponse(500, "Gagal memuat dashboard: " . $e->getMessage(), null);
        }
    }

    /**
     * =================================================================
     * HELPER FILTER KORWIL & CABANG
     * =================================================================
     * Biar tidak perlu nulis if-else korwil & cabang berulang-ulang
     */
    private function buildFilterQuery($input, $alias = 't') {
        $kode_kantor  = !empty($input['kode_kantor']) ? str_pad($input['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil_input = !empty($input['korwil']) ? strtoupper($input['korwil']) : null;
        
        $sqlFilter = "";
        $params = [];
        $prefix = $alias ? "{$alias}." : "";

        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter = " AND {$prefix}kode_cabang = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
        } elseif ($korwil_input) {
            $kw_start = null; $kw_end = null;
            switch ($korwil_input) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter = " AND {$prefix}kode_cabang BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        return ['sql' => $sqlFilter, 'params' => $params];
    }

    /**
     * =================================================================
     * FUNGSI-FUNGSI MODULAR (PECAHAN)
     * =================================================================
     */

    public function getRunOffVsRealisasi($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        $filter = $this->buildFilterQuery($input, 't');

        // Contoh kerangka query (tinggal sesuaikan dengan logic runoff dari sepuh)
        $sql = "
            SELECT 
                COALESCE(SUM(t.baki_debet), 0) as total_run_off,
                (SELECT SUM(plafond) FROM nominatif t2 WHERE t2.created = :harian_date AND t2.tgl_realisasi > :closing_date {$filter['sql']}) as total_realisasi
            FROM nominatif t
            WHERE t.created = :closing_date
            {$filter['sql']}
            /* Tambahkan logic runoff_calc di sini seperti di KreditController */
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            
            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_run_off' => 0, 'total_realisasi' => 0];
        } catch (Exception $e) {
            return []; // Kembalikan array kosong jika error agar dashboard tidak mati total
        }
    }

    /**
     * =================================================================
     * FUNGSI SALDO BANK (UNTUK KPI BOX)
     * =================================================================
     * Mengambil total Saldo Bank untuk hari ini (Actual) 
     * dan dibandingkan dengan bulan lalu (Closing) dari tabel nominatif
     */
    public function getSaldoBank($input) {
        $harian_date  = $input['harian_date'] ?? date('Y-m-d');
        // Default closing: hari terakhir bulan sebelumnya
        $closing_date = $input['closing_date'] ?? date('Y-m-t', strtotime($harian_date . ' -1 month')); 
        
        $filter = $this->buildFilterQuery($input, 't');

        // Tarik data Actual dan Closing sekaligus pakai CASE WHEN dari tabel nominatif
        $sql = "
            SELECT 
                -- Data Current (Harian / Actual)
                SUM(CASE WHEN t.created = :harian_date_1 THEN COALESCE(t.saldo_bank, 0) ELSE 0 END) AS saldo_bank_curr,
                
                -- Data Previous (Closing)
                SUM(CASE WHEN t.created = :closing_date_1 THEN COALESCE(t.saldo_bank, 0) ELSE 0 END) AS saldo_bank_prev
                
            FROM nominatif t
            WHERE t.created IN (:harian_date_2, :closing_date_2)
            {$filter['sql']}
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter berulang
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            
            // Bind parameter filter (Korwil / Cabang)
            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $saldo_bank_curr = (float) ($result['saldo_bank_curr'] ?? 0);
            $saldo_bank_prev = (float) ($result['saldo_bank_prev'] ?? 0);

            return [
                'actual'  => $saldo_bank_curr,
                'closing' => $saldo_bank_prev,
                'delta'   => $saldo_bank_curr - $saldo_bank_prev
            ];

        } catch (PDOException $e) {
            error_log("Error getSaldoBank: " . $e->getMessage());
            return [
                'actual'  => 0, 
                'closing' => 0, 
                'delta'   => 0
            ];
        }
    }

    public function getTrenNPL($input) {
        $harian_date = $input['harian_date'] ?? date('Y-m-d');
        $periode = $input['periode'] ?? 'bulanan'; 
        
        $dates = [$harian_date]; // Selalu masukkan tanggal hari ini (ACTUAL)
        
        // 1. Generate Tanggal Secara Dinamis
        if ($periode === 'mingguan') {
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i week", strtotime($harian_date)));
            }
        } elseif ($periode === '7_hari') {
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === '14_hari') {
            for ($i = 1; $i <= 13; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === '30_hari') {
            for ($i = 1; $i <= 29; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === 'tahunan') {
            /**
             * LOGIKA TAHUNAN:
             * Ambil closing 31 Des mulai dari 2020 sampai tahun kemarin.
             */
            $startYear = 2020;
            $currentYear = (int)date('Y', strtotime($harian_date));
            
            for ($year = $startYear; $year < $currentYear; $year++) {
                $dates[] = "$year-12-31";
            }
        } else {
            // Default: Bulanan (Mundur 6 bulan ke belakang)
            $patokan_mundur = strtotime(date('Y-m-01', strtotime($harian_date))); 
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("last day of -$i month", $patokan_mundur));
            }
        }
        
        // -- Urutkan tanggal ASC agar chart mengalir dari lama ke baru --
        sort($dates);
        $dates = array_unique($dates); 

        // 2. Siapkan Binding Parameter untuk Klausa IN (...)
        $inParams = [];
        $inQueryParts = [];
        foreach ($dates as $i => $date) {
            $paramName = ":date_$i";
            $inParams[$paramName] = $date;
            $inQueryParts[] = $paramName;
        }
        $inString = implode(', ', $inQueryParts); 

        // 3. Ambil Filter Cabang/Korwil (Method internal Anda)
        $filter = $this->buildFilterQuery($input, 't');

        // 4. Susun Query SQL
        $sql = "
            SELECT 
                t.created AS tanggal,
                SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_amt,
                SUM(t.baki_debet) AS total_kredit,
                ROUND((SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) / NULLIF(SUM(t.baki_debet), 0) * 100), 2) AS npl_persen
            FROM nominatif t
            WHERE t.created IN ($inString)
            {$filter['sql']}
            GROUP BY t.created
            ORDER BY t.created ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($inParams as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Indexing hasil DB untuk handle data yang mungkin kosong (Gapping)
            $dbDataIndexed = [];
            foreach ($rows as $row) {
                $dbDataIndexed[$row['tanggal']] = $row;
            }

            $formattedData = [];
            $lastValidNplAmt = 0;
            $lastValidTotalKredit = 0;
            $lastValidNplPersen = 0;

            foreach ($dates as $expectedDate) {
                // Penentuan Label agar FE tetap konsisten
                if ($periode === 'tahunan') {
                    if ($expectedDate === $harian_date) {
                        $label = date('d M Y', strtotime($expectedDate)) . ' (Act)';
                    } else {
                        // Format: 31 Des 2020
                        $label = date('d M Y', strtotime($expectedDate)); 
                    }
                } elseif ($periode === 'bulanan') {
                    $label = date('M Y', strtotime($expectedDate));
                } elseif ($periode === 'mingguan') {
                    $label = date('d M Y', strtotime($expectedDate));
                } else {
                    $label = date('d M', strtotime($expectedDate));
                }

                if (isset($dbDataIndexed[$expectedDate])) {
                    $dbItem = $dbDataIndexed[$expectedDate];
                    
                    $lastValidNplAmt = (float) $dbItem['npl_amt'];
                    $lastValidTotalKredit = (float) $dbItem['total_kredit'];
                    $lastValidNplPersen = (float) $dbItem['npl_persen'];

                    $formattedData[] = [
                        'tanggal'      => $expectedDate,
                        'label'        => $label,
                        'npl_amt'      => $lastValidNplAmt,
                        'total_kredit' => $lastValidTotalKredit,
                        'npl_persen'   => $lastValidNplPersen
                    ];
                } else {
                    // Carry Forward: Pakai data terakhir jika tanggal tsb tidak ada di DB
                    $formattedData[] = [
                        'tanggal'      => $expectedDate,
                        'label'        => $label,
                        'npl_amt'      => $lastValidNplAmt,
                        'total_kredit' => $lastValidTotalKredit,
                        'npl_persen'   => $lastValidNplPersen
                    ];
                }
            }

            return $formattedData;

        } catch (PDOException $e) {
            error_log("Error getTrenNPL: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =================================================================
     * FUNGSI TREN PORTOFOLIO KREDIT (OSC TOTAL, NPL, RR)
     * =================================================================
     * Mengambil tren pergerakan Outstanding (OSC) Total, OSC NPL, 
     * dan Repayment Rate (RR) langsung dari tabel nominatif beserta GAP-nya.
     */
    public function getTrenPortofolioKredit($input) {
        $harian_date = $input['harian_date'] ?? date('Y-m-d');
        $periode = $input['periode'] ?? 'bulanan'; 
        
        $dates = [$harian_date]; // Selalu masukkan tanggal hari ini (ACTUAL)
        
        // 1. Generate Tanggal Secara Dinamis
        if ($periode === 'mingguan') {
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i week", strtotime($harian_date)));
            }
        } elseif ($periode === '7_hari') {
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === '14_hari') {
            for ($i = 1; $i <= 13; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === '30_hari') {
            for ($i = 1; $i <= 29; $i++) {
                $dates[] = date('Y-m-d', strtotime("-$i day", strtotime($harian_date)));
            }
        } elseif ($periode === 'tahunan') {
            /**
             * LOGIKA TAHUNAN (FIXED):
             * Ambil closing 31 Des mulai dari 2020 sampai tahun kemarin.
             */
            $startYear = 2020;
            $currentYear = (int)date('Y', strtotime($harian_date));
            
            for ($year = $startYear; $year < $currentYear; $year++) {
                $dates[] = "$year-12-31";
            }
        } else {
            // Default: Bulanan
            $patokan_mundur = strtotime(date('Y-m-01', strtotime($harian_date))); 
            for ($i = 1; $i <= 6; $i++) {
                $dates[] = date('Y-m-d', strtotime("last day of -$i month", $patokan_mundur));
            }
        }
        
        // Urutkan tanggal ASC dan buang duplikat (jika ada)
        sort($dates); 
        $dates = array_unique($dates);

        // 2. Siapkan Binding Parameter untuk Klausa IN (...)
        $inParams = [];
        $inQueryParts = [];
        foreach ($dates as $i => $date) {
            $paramName = ":date_$i";
            $inParams[$paramName] = $date;
            $inQueryParts[] = $paramName;
        }
        $inString = implode(', ', $inQueryParts); 

        // 3. Ambil Filter Wilayah/Cabang
        $filter = $this->buildFilterQuery($input, 't'); 

        // 4. Query Super Ngebut (1 Tabel untuk semua metrik)
        // 🔥 FIX: Hitung RR berdasarkan hari_menunggak = 0 DAN kolektibilitas = 'L' 🔥
        $sql = "
            SELECT 
                t.created AS tanggal,
                SUM(t.baki_debet) AS osc_total,
                SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS osc_npl,
                SUM(CASE WHEN t.hari_menunggak = 0 AND t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) AS osc_rr
            FROM nominatif t
            WHERE t.created IN ($inString)
            {$filter['sql']}
            GROUP BY t.created
            ORDER BY t.created ASC
        ";

        try {
            // Eksekusi Query
            $stmt = $this->pdo->prepare($sql);
            foreach ($inParams as $key => $val) { $stmt->bindValue($key, $val); }
            foreach ($filter['params'] as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Mapping Data agar cepat dilookup
            $dbIndexed = []; 
            foreach ($rows as $r) { 
                $dbIndexed[$r['tanggal']] = $r; 
            }

            // 6. Format Output, Carry Forward, dan Kalkulasi GAP
            $formattedData = [];
            
            // Variabel penyimpan nilai saat ini
            $last_osc_total = 0;
            $last_osc_npl = 0;
            $last_osc_rr = 0; 
            
            // Variabel penyimpan nilai sebelumnya (untuk hitung gap)
            $prev_osc_total = null;
            $prev_osc_npl = null;
            $prev_osc_rr = null;
            $prev_npl_persen = null;
            $prev_rr_persen = null;

            foreach ($dates as $expectedDate) {
                // Formatting label tanggal (FIXED TAHUNAN)
                $label = '';
                if ($periode === 'tahunan') {
                    $label = date('d M Y', strtotime($expectedDate)); // Format: 31 Dec 2020
                } elseif ($periode === 'bulanan') {
                    $label = date('M Y', strtotime($expectedDate));
                } elseif ($periode === 'mingguan') {
                    $label = date('d M Y', strtotime($expectedDate));
                } else {
                    $label = date('d M', strtotime($expectedDate));
                }

                // Tambahan khusus (Act) jika itu tanggal hari ini
                if ($expectedDate === $harian_date) {
                    // Cek jika tahunan dan itu Act, format jadi: 26 Apr 2026 (Act)
                    if ($periode === 'tahunan' && strpos($label, date('Y')) !== false) {
                        $label = date('d M Y', strtotime($expectedDate)) . ' (Act)';
                    } else {
                        $label .= ' (Act)';
                    }
                }

                // Ambil data jika ada, jika tidak pakai data hari sebelumnya (Carry Forward)
                if (isset($dbIndexed[$expectedDate])) {
                    $last_osc_total = (float) $dbIndexed[$expectedDate]['osc_total'];
                    $last_osc_npl   = (float) $dbIndexed[$expectedDate]['osc_npl'];
                    $last_osc_rr    = (float) $dbIndexed[$expectedDate]['osc_rr'];
                }

                // Kalkulasi Persentase (Otomatis RR = osc_rr / osc_total)
                $npl_persen = $last_osc_total > 0 ? round(($last_osc_npl / $last_osc_total) * 100, 2) : 0;
                $rr_persen  = $last_osc_total > 0 ? round(($last_osc_rr / $last_osc_total) * 100, 2) : 0;

                // ==========================================
                // 🔥 KALKULASI GAP (SELISIH)
                // ==========================================
                // Jika prev_* masih null (data pertama), gap = 0
                $gap_osc_total  = $prev_osc_total !== null ? ($last_osc_total - $prev_osc_total) : 0;
                $gap_osc_npl    = $prev_osc_npl !== null ? ($last_osc_npl - $prev_osc_npl) : 0;
                $gap_osc_rr     = $prev_osc_rr !== null ? ($last_osc_rr - $prev_osc_rr) : 0;
                $gap_npl_persen = $prev_npl_persen !== null ? round($npl_persen - $prev_npl_persen, 2) : 0;
                $gap_rr_persen  = $prev_rr_persen !== null ? round($rr_persen - $prev_rr_persen, 2) : 0;

                // Push ke array final
                $formattedData[] = [
                    'tanggal'        => $expectedDate,
                    'label'          => $label,
                    
                    'osc_total'      => $last_osc_total,
                    'gap_osc_total'  => $gap_osc_total, // Output Gap Nominal OS
                    
                    'osc_npl'        => $last_osc_npl,
                    'gap_osc_npl'    => $gap_osc_npl, // Output Gap Nominal NPL
                    
                    'npl_persen'     => $npl_persen,
                    'gap_npl_persen' => $gap_npl_persen, // Output Gap % NPL
                    
                    'osc_rr'         => $last_osc_rr, 
                    'gap_osc_rr'     => $gap_osc_rr, // Output Gap Nominal RR
                    
                    'rr_persen'      => $rr_persen,
                    'gap_rr_persen'  => $gap_rr_persen // Output Gap % RR
                ];

                // Set nilai prev untuk iterasi berikutnya
                $prev_osc_total  = $last_osc_total;
                $prev_osc_npl    = $last_osc_npl;
                $prev_osc_rr     = $last_osc_rr;
                $prev_npl_persen = $npl_persen;
                $prev_rr_persen  = $rr_persen;
            }

            return $formattedData;

        } catch (PDOException $e) {
            error_log("Error getTrenPortofolioKredit: " . $e->getMessage());
            return [];
        }
    }

    public function getRepaymentRateCabang($input) {
        // --- SUNTIK TENAGA SERVER (BEBAS TIME OUT) ---
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        $harian_date  = $input['harian_date'] ?? date('Y-m-d');
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $kode_kantor  = $input['kode_kantor'] ?? '000';
        
        $filter = $this->buildFilterQuery($input, 't');

        $displayMode = 'PUSAT';
        $filterSql_cabang = "";

        // Deteksi Mode: Jika bukan 000, berarti mode Cabang (Breakdown ke Kankas)
        if ($kode_kantor !== '000') {
            $displayMode = 'CABANG';
            $filterSql_cabang = " AND t.kode_cabang = :kode_kantor_master"; 
        }

        // =========================================================
        // SUSUN BASE QUERY SQL BERDASARKAN MODE (PUSAT VS CABANG)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // MODE CABANG: Breakdown per Kankas menggunakan kode_group1
            $sql = "
                SELECT 
                    COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')) AS kode_cabang,
                    COALESCE(k.deskripsi_group1, CONCAT('KAS CABANG ', t.kode_cabang)) AS nama_cabang,
                    
                    -- Data Current (Harian)
                    SUM(CASE WHEN t.created = :harian_date_1 AND t.hari_menunggak = 0 AND t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) AS baki_lancar_curr,
                    SUM(CASE WHEN t.created = :harian_date_2 THEN t.baki_debet ELSE 0 END) AS baki_total_curr,
                    
                    -- Data Previous (Closing Bulan Lalu)
                    SUM(CASE WHEN t.created = :closing_date_1 AND t.hari_menunggak = 0 AND t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) AS baki_lancar_prev,
                    SUM(CASE WHEN t.created = :closing_date_2 THEN t.baki_debet ELSE 0 END) AS baki_total_prev
                    
                FROM nominatif t
                LEFT JOIN kankas k ON k.kode_group1 = COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000'))
                WHERE t.created IN (:harian_date_3, :closing_date_3)
                {$filterSql_cabang}
                {$filter['sql']}
                GROUP BY kode_cabang, k.deskripsi_group1
            ";
        } else {
            // MODE PUSAT: Konsolidasi per Cabang
            $sql = "
                SELECT 
                    t.kode_cabang,
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_cabang)) AS nama_cabang,
                    
                    -- Data Current (Harian)
                    SUM(CASE WHEN t.created = :harian_date_1 AND t.hari_menunggak = 0 AND t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) AS baki_lancar_curr,
                    SUM(CASE WHEN t.created = :harian_date_2 THEN t.baki_debet ELSE 0 END) AS baki_total_curr,
                    
                    -- Data Previous (Closing Bulan Lalu)
                    SUM(CASE WHEN t.created = :closing_date_1 AND t.hari_menunggak = 0 AND t.kolektibilitas = 'L' THEN t.baki_debet ELSE 0 END) AS baki_lancar_prev,
                    SUM(CASE WHEN t.created = :closing_date_2 THEN t.baki_debet ELSE 0 END) AS baki_total_prev
                    
                FROM nominatif t
                LEFT JOIN kode_kantor k ON t.kode_cabang = k.kode_kantor
                WHERE t.created IN (:harian_date_3, :closing_date_3)
                {$filter['sql']}
                GROUP BY t.kode_cabang, k.nama_kantor
            ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            $stmt->bindValue(':harian_date_3', $harian_date);
            
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            $stmt->bindValue(':closing_date_3', $closing_date);
            
            if ($displayMode === 'CABANG') {
                $stmt->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $semua_cabang = [];
            $kenaikan_rr  = [];
            $penurunan_rr = [];

            // Variabel untuk Grand Total (OS ALL)
            $grand_baki_total_curr  = 0;
            $grand_baki_lancar_curr = 0;
            $grand_baki_total_prev  = 0;
            $grand_baki_lancar_prev = 0;

            foreach ($rows as $r) {
                $baki_total_curr  = (float) $r['baki_total_curr'];
                $baki_lancar_curr = (float) $r['baki_lancar_curr'];
                $baki_total_prev  = (float) $r['baki_total_prev'];
                $baki_lancar_prev = (float) $r['baki_lancar_prev'];

                if ($baki_total_curr <= 0) continue;

                // Akumulasi ke Grand Total
                $grand_baki_total_curr  += $baki_total_curr;
                $grand_baki_lancar_curr += $baki_lancar_curr;
                $grand_baki_total_prev  += $baki_total_prev;
                $grand_baki_lancar_prev += $baki_lancar_prev;

                $rr_curr = ($baki_lancar_curr / $baki_total_curr) * 100;
                $rr_prev = $baki_total_prev > 0 ? ($baki_lancar_prev / $baki_total_prev) * 100 : 0;
                $delta = $rr_curr - $rr_prev;

                $dataCabang = [
                    'kode_cabang'    => $r['kode_cabang'],
                    'nama_cabang'    => str_replace('Kc. ', '', $r['nama_cabang'] ?? ''),
                    'os_total'       => $baki_total_curr,
                    'os_lancar'      => $baki_lancar_curr,
                    'rr_persen_prev' => round($rr_prev, 2),
                    'rr_persen_curr' => round($rr_curr, 2),
                    'delta_rr'       => round($delta, 2)
                ];

                $semua_cabang[] = $dataCabang;

                if ($delta > 0) {
                    $kenaikan_rr[] = $dataCabang;
                } elseif ($delta < 0) {
                    $penurunan_rr[] = $dataCabang;
                }
            }

            // Hitung RR untuk Grand Total Konsolidasi
            $grand_rr_curr = $grand_baki_total_curr > 0 ? ($grand_baki_lancar_curr / $grand_baki_total_curr) * 100 : 0;
            $grand_rr_prev = $grand_baki_total_prev > 0 ? ($grand_baki_lancar_prev / $grand_baki_total_prev) * 100 : 0;
            $grand_delta   = $grand_rr_curr - $grand_rr_prev;

            $grand_total = [
                'nama_cabang'    => 'TOTAL KESELURUHAN',
                'os_total'       => $grand_baki_total_curr,
                'os_lancar'      => $grand_baki_lancar_curr,
                'rr_persen_prev' => round($grand_rr_prev, 2),
                'rr_persen_curr' => round($grand_rr_curr, 2),
                'delta_rr'       => round($grand_delta, 2)
            ];

            // =========================================================
            // SORTING: TOP 5 & SISA MASUK BOTTOM (MODE CABANG)
            // =========================================================
            
            // 1. REPAYMENT RATE TERBAIK & TERBURUK
            usort($semua_cabang, function($a, $b) { return $b['rr_persen_curr'] <=> $a['rr_persen_curr']; }); // Descending (Terbaik)
            $top_rr = array_slice($semua_cabang, 0, 5);

            if ($displayMode === 'CABANG') {
                $bottom_rr = array_slice($semua_cabang, 5); // Tampilkan semua sisa kankas
                usort($bottom_rr, function($a, $b) { return $a['rr_persen_curr'] <=> $b['rr_persen_curr']; }); // Ascending (Paling buruk di atas)
            } else {
                $temp_rr = $semua_cabang;
                usort($temp_rr, function($a, $b) { return $a['rr_persen_curr'] <=> $b['rr_persen_curr']; }); // Ascending
                $bottom_rr = array_slice($temp_rr, 0, 5);
            }

            // 2. OS TOTAL TERBESAR
            usort($semua_cabang, function($a, $b) { return $b['os_total'] <=> $a['os_total']; }); // Descending
            $top_os_terbesar = array_slice($semua_cabang, 0, 5);

            // 3. KENAIKAN & PENURUNAN
            usort($kenaikan_rr, function($a, $b) { return $b['delta_rr'] <=> $a['delta_rr']; }); // Descending
            $top_kenaikan = array_slice($kenaikan_rr, 0, 5);

            if ($displayMode === 'CABANG') {
                $top_penurunan = array_slice($penurunan_rr, 0); // Ambil semua penurunan jika cabang
                usort($top_penurunan, function($a, $b) { return $a['delta_rr'] <=> $b['delta_rr']; }); // Ascending (Paling minus di bawah)
            } else {
                usort($penurunan_rr, function($a, $b) { return $a['delta_rr'] <=> $b['delta_rr']; }); // Ascending
                $top_penurunan = array_slice($penurunan_rr, 0, 5);
            }

            return [
                'grand_total'     => $grand_total,
                'top_os_terbesar' => $top_os_terbesar, 
                'top_rr'          => $top_rr,
                'bottom_rr'       => $bottom_rr,
                'top_kenaikan'    => $top_kenaikan,
                'top_penurunan'   => $top_penurunan
            ];

        } catch (\Exception $e) { // <-- Diubah jadi Exception Global
            return [
                'ERROR_DETEKSI' => 'Terjadi masalah di sistem Repayment Rate!',
                'pesan_error'   => $e->getMessage(),
                'file_error'    => $e->getFile(),
                'baris_error'   => $e->getLine()
            ];
        }
    }

    public function getTrenRunOffRealisasi($input) {
        $harian_date = $input['harian_date'] ?? date('Y-m-d');
        $periode = $input['periode'] ?? '7_hari'; 
        
        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');
        
        // =========================================================
        // 1. Tentukan batas tanggal awal dan format grouping
        // =========================================================
        $start_date = $harian_date;
        $format_group = 'daily'; 
        
        if ($periode === '7_hari') {
            $start_date = date('Y-m-d', strtotime('-6 days', strtotime($harian_date)));
        } elseif ($periode === '14_hari') {
            $start_date = date('Y-m-d', strtotime('-13 days', strtotime($harian_date)));
        } elseif ($periode === '30_hari') {
            $start_date = date('Y-m-d', strtotime('-29 days', strtotime($harian_date)));
        } elseif ($periode === 'mingguan') {
            $start_date = date('Y-m-d', strtotime('-7 weeks', strtotime($harian_date))); 
            $format_group = 'weekly';
        } elseif ($periode === 'bulanan') {
            $start_date = date('Y-m-01', strtotime('-5 months', strtotime($harian_date))); 
            $format_group = 'monthly';
        } elseif ($periode === 'tahunan') {
            // FIX: Ambil dari awal tahun 2020
            $start_date = '2020-01-01'; 
            $format_group = 'yearly';
        }

        // =========================================================
        // 2. FILTER CABANG & KORWIL EKSKLUSIF
        // =========================================================
        $filterSql = "";
        $filterParams = [];

        if ($kode_kantor !== '000' && empty($korwil)) {
            $filterSql .= " AND s.kode_kantor = :kode_kantor";
            $filterParams[':kode_kantor'] = $kode_kantor;
        } elseif (!empty($korwil)) {
            if ($korwil === 'SEMARANG') {
                $filterSql .= " AND s.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql .= " AND s.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql .= " AND s.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql .= " AND s.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 3. QUERY AMBIL SEMUA DATA (Dengan Filter)
        // =========================================================
        $sql = "
            SELECT 
                s.created AS tanggal,
                SUM(COALESCE(s.realisasi, 0)) AS total_realisasi,
                SUM(COALESCE(s.noa_realisasi, 0)) AS noa_realisasi,
                
                SUM(COALESCE(s.pelunasan, 0)) AS total_lunas,
                SUM(COALESCE(s.noa_pelunasan, 0)) AS noa_lunas,
                
                SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS total_angsuran,
                SUM(COALESCE(s.noa_angsuran, 0) - COALESCE(s.noa_pelunasan, 0)) AS noa_angsuran,
                
                SUM(COALESCE(s.angsuran, 0)) AS total_runoff
            FROM summary_kredit_harian s
            WHERE s.created >= :start_date AND s.created <= :end_date
            {$filterSql}
            GROUP BY s.created
            ORDER BY s.created ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':start_date', $start_date);
            $stmt->bindValue(':end_date', $harian_date);
            
            // Bind Filter Params (Jika ada)
            foreach ($filterParams as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 4. BUAT KERANJANG (BUCKET) UNTUK GROUPING
            // =========================================================
            $groupedData = [];

            if ($format_group === 'daily') {
                $curr = strtotime($start_date);
                $end = strtotime($harian_date);
                while ($curr <= $end) {
                    $lbl = date('Y-m-d', $curr);
                    $groupedData[$lbl] = [
                        'label' => date('d M', $curr), 
                        'realisasi' => 0, 'noa_realisasi' => 0, 
                        'lunas' => 0, 'noa_lunas' => 0, 
                        'angsuran' => 0, 'noa_angsuran' => 0, 
                        'runoff' => 0
                    ];
                    $curr = strtotime('+1 day', $curr);
                }
            } elseif ($format_group === 'weekly') {
                $curr = strtotime($start_date);
                $end = strtotime($harian_date);
                while ($curr <= $end) {
                    $lbl = date('o-\WW', $curr); 
                    if (!isset($groupedData[$lbl])) {
                        $groupedData[$lbl] = [
                            'label' => "Mg " . date('W', $curr) . " '" . date('y', $curr), 
                            'realisasi' => 0, 'noa_realisasi' => 0, 
                            'lunas' => 0, 'noa_lunas' => 0, 
                            'angsuran' => 0, 'noa_angsuran' => 0, 
                            'runoff' => 0
                        ];
                    }
                    $curr = strtotime('+1 day', $curr);
                }
            } elseif ($format_group === 'monthly') {
                $curr = strtotime($start_date);
                $end = strtotime($harian_date);
                while ($curr <= $end) {
                    $lbl = date('Y-m', $curr); 
                    $groupedData[$lbl] = [
                        'label' => date('M Y', $curr), 
                        'realisasi' => 0, 'noa_realisasi' => 0, 
                        'lunas' => 0, 'noa_lunas' => 0, 
                        'angsuran' => 0, 'noa_angsuran' => 0, 
                        'runoff' => 0
                    ];
                    $curr = strtotime('+1 month', $curr);
                }
            } elseif ($format_group === 'yearly') {
                // FIX: Loop dari 2020 sampai tahun hari ini
                $startYear = 2020;
                $currentYear = (int)date('Y', strtotime($harian_date));
                
                for ($y = $startYear; $y <= $currentYear; $y++) {
                    $lbl = (string)$y;
                    $groupedData[$lbl] = [
                        'label' => $lbl, // Output misal: "2020", "2021"
                        'realisasi' => 0, 'noa_realisasi' => 0, 
                        'lunas' => 0, 'noa_lunas' => 0, 
                        'angsuran' => 0, 'noa_angsuran' => 0, 
                        'runoff' => 0
                    ];
                }
            }

            // =========================================================
            // 5. MASUKKAN DATA DB KE KERANJANG (DI-SUM)
            // =========================================================
            foreach ($rows as $row) {
                $tgl = $row['tanggal'];
                $lblKey = $tgl; 
                
                if ($format_group === 'weekly') $lblKey = date('o-\WW', strtotime($tgl));
                elseif ($format_group === 'monthly') $lblKey = date('Y-m', strtotime($tgl));
                elseif ($format_group === 'yearly') $lblKey = date('Y', strtotime($tgl)); // FIX: Map ke tahun

                if (isset($groupedData[$lblKey])) {
                    $groupedData[$lblKey]['realisasi']     += (float) $row['total_realisasi'];
                    $groupedData[$lblKey]['noa_realisasi'] += (int) $row['noa_realisasi'];
                    $groupedData[$lblKey]['lunas']         += (float) $row['total_lunas'];
                    $groupedData[$lblKey]['noa_lunas']     += (int) $row['noa_lunas'];
                    $groupedData[$lblKey]['angsuran']      += (float) $row['total_angsuran'];
                    $groupedData[$lblKey]['noa_angsuran']  += (int) $row['noa_angsuran'];
                    $groupedData[$lblKey]['runoff']        += (float) $row['total_runoff'];
                }
            }

            // =========================================================
            // 6. FORMAT OUTPUT AKHIR
            // =========================================================
            $formattedData = [];
            foreach ($groupedData as $key => $val) {
                $growth = $val['realisasi'] - $val['runoff'];

                $formattedData[] = [
                    'tanggal'         => $key,
                    'label'           => $val['label'],
                    'total_realisasi' => $val['realisasi'],
                    'noa_realisasi'   => $val['noa_realisasi'],
                    'total_lunas'     => $val['lunas'],            
                    'noa_lunas'       => $val['noa_lunas'],        
                    'total_angsuran'  => $val['angsuran'],         
                    'noa_angsuran'    => $val['noa_angsuran'],     
                    'total_runoff'    => $val['runoff'],
                    'growth'          => $growth
                ];
            }
            
            // Tandai elemen terakhir sebagai "Act" (Aktual)
            if (count($formattedData) > 0) {
                $formattedData[count($formattedData) - 1]['label'] .= ' (Act)';
            }

            return $formattedData;

        } catch (PDOException $e) {
            error_log("Error getTrenRunOffRealisasi: " . $e->getMessage());
            return [];
        }
    }

    public function getRealisasiRealtimeByProduk($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // 1. FILTER CABANG & KORWIL
        // =========================================================
        $filterSql = "";
        $filterParams = [];

        if ($kode_kantor !== '000' && empty($korwil)) {
            $filterSql .= " AND t.kode_kantor = :kode_kantor";
            $filterParams[':kode_kantor'] = $kode_kantor;
        } elseif (!empty($korwil)) {
            if ($korwil === 'SEMARANG') {
                $filterSql .= " AND t.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql .= " AND t.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql .= " AND t.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql .= " AND t.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 2. SUSUN QUERY JOIN DENGAN PRODUK KREDIT
        // =========================================================
        // 🔥 FIX: Mengubah t.tgl_realisasi menjadi t.tanggal_realisasi sesuai DB
        $sql = "
            SELECT
                x.kode_produk_group AS kode_produk,
                COALESCE(p_group.nama_produk, x.nama_produk_group, CONCAT('PRODUK ', x.kode_produk_group)) AS nama_produk,
                SUM(x.realisasi_pokok) AS total_realisasi,
                COUNT(DISTINCT x.no_rekening) AS noa_realisasi
            FROM (
                SELECT
                    t.no_rekening,
                    t.realisasi_pokok,
                    t.tanggal_realisasi,
                    CASE
                        WHEN t.tanggal_realisasi >= '2026-06-01' THEN COALESCE(p_old.kode_baru, t.kode_produk)
                        ELSE t.kode_produk
                    END AS kode_produk_group,
                    CASE
                        WHEN t.tanggal_realisasi >= '2026-06-01' AND p_new.nama_produk IS NOT NULL THEN p_new.nama_produk
                        ELSE p_old.nama_produk
                    END AS nama_produk_group
                FROM update_realisasi_kredit t
                LEFT JOIN produk_kredit p_old ON t.kode_produk = p_old.kode_produk
                LEFT JOIN produk_kredit p_new ON p_old.kode_baru = p_new.kode_produk
                WHERE t.tanggal_realisasi > :closing_date
                  AND t.tanggal_realisasi <= :harian_date
                {$filterSql}
            ) x
            LEFT JOIN produk_kredit p_group ON x.kode_produk_group = p_group.kode_produk
            GROUP BY x.kode_produk_group, COALESCE(p_group.nama_produk, x.nama_produk_group, CONCAT('PRODUK ', x.kode_produk_group))
            ORDER BY total_realisasi DESC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter tanggal
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            
            // Bind parameter filter wilayah
            foreach ($filterParams as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 3. FORMAT OUTPUT & HITUNG GRAND TOTAL
            // =========================================================
            $formattedData = [];
            $grand_total_realisasi = 0;
            $grand_total_noa = 0;

            foreach ($rows as $r) {
                $realisasi = (float) $r['total_realisasi'];
                $noa       = (int) $r['noa_realisasi'];

                $formattedData[] = [
                    'kode_produk'     => $r['kode_produk'],
                    'nama_produk'     => $r['nama_produk'],
                    'total_realisasi' => $realisasi,
                    'noa_realisasi'   => $noa
                ];

                $grand_total_realisasi += $realisasi;
                $grand_total_noa       += $noa;
            }

            return [
                'detail_produk' => $formattedData,
                'grand_total'   => [
                    'total_realisasi' => $grand_total_realisasi,
                    'noa_realisasi'   => $grand_total_noa
                ]
            ];

        } catch (PDOException $e) {
            // Kalau masih error, nanti akan tercatat di log error PHP
            error_log("Error getRealisasiRealtimeByProduk: " . $e->getMessage());
            return ['detail_produk' => [], 'grand_total' => []];
        }
    }

    public function getTopBottomNPL($input) {
        // Suntik tenaga biar aman saat hitung konsolidasi
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        $harian_date = $input['harian_date'] ?? date('Y-m-d');
        $kode_kantor = $input['kode_kantor'] ?? '000';
        
        // Ambil filter 
        $filter = $this->buildFilterQuery($input, 't');

        $displayMode = 'PUSAT';
        $filterSql_cabang = "";

        // Deteksi Mode: Jika bukan 000, berarti mode Cabang (Breakdown ke Kankas)
        if ($kode_kantor !== '000') {
            $displayMode = 'CABANG';
            $filterSql_cabang = " AND t.kode_cabang = :kode_kantor_master"; 
        }

        // =========================================================
        // SUSUN BASE QUERY SQL (Tanpa Limit, Ambil Semua Data)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // MODE CABANG: Breakdown per Kankas
            $sqlBase = "
                SELECT 
                    COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')) AS kode_target,
                    COALESCE(k.deskripsi_group1, CONCAT('KAS ', COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')))) AS nama_target,
                    SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_amt,
                    SUM(t.baki_debet) AS total_kredit,
                    ROUND((SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) / NULLIF(SUM(t.baki_debet), 0) * 100), 2) AS npl_persen
                FROM nominatif t
                LEFT JOIN kankas k ON k.kode_group1 = COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000'))
                WHERE t.created = :harian_date
                {$filterSql_cabang}
                {$filter['sql']}
                GROUP BY kode_target, k.deskripsi_group1
                HAVING SUM(t.baki_debet) > 0 
            ";
        } else {
            // MODE PUSAT: Konsolidasi per Cabang
            $sqlBase = "
                SELECT 
                    t.kode_cabang AS kode_target,
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_cabang)) AS nama_target,
                    SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_amt,
                    SUM(t.baki_debet) AS total_kredit,
                    ROUND((SUM(CASE WHEN t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) / NULLIF(SUM(t.baki_debet), 0) * 100), 2) AS npl_persen
                FROM nominatif t
                LEFT JOIN kode_kantor k ON t.kode_cabang = k.kode_kantor
                WHERE t.created = :harian_date
                {$filter['sql']}
                GROUP BY t.kode_cabang, k.nama_kantor
                HAVING SUM(t.baki_debet) > 0 
            ";
        }

        try {
            // Eksekusi SQL HANYA 1 KALI (Beban Database Jauh Lebih Ringan)
            $stmt = $this->pdo->prepare($sqlBase);
            $stmt->bindValue(':harian_date', $harian_date);
            
            if ($displayMode === 'CABANG') {
                $stmt->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // OLAH DATA & SORTING DI PHP (Pisah Top & Bottom)
            // =========================================================
            $allData = [];
            foreach ($rows as $r) {
                // Penangkal teks "NULL" biar aman untuk FE
                $raw_nama = trim($r['nama_target'] ?? '');
                if ($raw_nama === '' || strtoupper($raw_nama) === 'NULL') {
                    $final_nama = 'KAS CABANG ' . $r['kode_target'];
                } else {
                    $final_nama = str_replace('Kc. ', '', $raw_nama);
                }

                $allData[] = [
                    'kode_cabang'  => $r['kode_target'],
                    'nama_cabang'  => $final_nama,
                    'npl_amt'      => (float) $r['npl_amt'],
                    'total_kredit' => (float) $r['total_kredit'],
                    'npl_persen'   => (float) $r['npl_persen']
                ];
            }

            if ($displayMode === 'CABANG') {
                // =========================================================
                // LOGIKA CABANG: THRESHOLD 20% (TAMPIL SEMUA TANPA LIMIT)
                // =========================================================
                
                // 1. TOP (NPL Terburuk / Merah di FE) = NPL >= 20%
                $topData = array_filter($allData, function($r) { 
                    return $r['npl_persen'] >= 20; 
                });
                // Urutkan NPL terkecil ke terbesar (Terbesar/Paling Hancur di paling bawah)
                usort($topData, function($a, $b) { return $a['npl_persen'] <=> $b['npl_persen']; });
                
                // 2. BOTTOM (NPL Terbaik / Hijau di FE) = NPL < 20%
                $bottomData = array_filter($allData, function($r) { 
                    return $r['npl_persen'] < 20; 
                });
                // Urutkan NPL terkecil ke terbesar
                usort($bottomData, function($a, $b) { return $a['npl_persen'] <=> $b['npl_persen']; });

                // Reset Index Array agar format JSON valid (menjadi array [] bukan object {})
                $topData    = array_values($topData);
                $bottomData = array_values($bottomData);

            } else {
                // =========================================================
                // LOGIKA PUSAT: LIMIT 5 TERBURUK & 5 TERBAIK
                // =========================================================
                
                // 1. Urutkan seluruh data NPL dari yang TERBURUK (Tertinggi) ke Terendah
                usort($allData, function($a, $b) { return $b['npl_persen'] <=> $a['npl_persen']; });
                
                // 2. Ambil 5 Teratas untuk masuk ke TOP (NPL Terburuk / Merah)
                $topData = array_slice($allData, 0, 5);

                // 3. Ambil SISANYA untuk disiapkan ke Bottom
                $sisaData = array_slice($allData, 5);
                
                // 4. Urutkan sisa data dari NPL TERBAIK (Terkecil) ke Terbesar
                usort($sisaData, function($a, $b) { return $a['npl_persen'] <=> $b['npl_persen']; });
                
                // 5. Ambil maksimal 5 NPL terbaik dari sisa Cabang
                $bottomData = array_slice($sisaData, 0, 5);
            }

            return [
                'top'    => $topData,
                'bottom' => $bottomData
            ];

        } catch (\Exception $e) { 
            return [
                'ERROR_DETEKSI' => 'Terjadi masalah di sistem NPL!',
                'pesan_error'   => $e->getMessage(),
                'file_error'    => $e->getFile(),
                'baris_error'   => $e->getLine(),
                'top'           => [],
                'bottom'        => []
            ];
        }
    }

    public function getTopKenaikanPenurunanNPL($input) {
        // --- SUNTIK TENAGA SERVER (BEBAS TIME OUT) ---
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        $harian_date  = $input['harian_date'] ?? date('Y-m-d');
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $kode_kantor  = $input['kode_kantor'] ?? '000';
        
        $filter = $this->buildFilterQuery($input, 't');

        $displayMode = 'PUSAT';
        $filterSql_cabang = "";

        // Deteksi Mode: Jika bukan 000, berarti mode Cabang (Breakdown ke Kankas)
        if ($kode_kantor !== '000') {
            $displayMode = 'CABANG';
            $filterSql_cabang = " AND t.kode_cabang = :kode_kantor_master"; 
        }

        // =========================================================
        // SUSUN BASE QUERY SQL BERDASARKAN MODE (PUSAT VS CABANG)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // MODE CABANG: Breakdown per Kankas
            $sql = "
                SELECT 
                    COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000')) AS kode_cabang,
                    COALESCE(k.deskripsi_group1, CONCAT('KAS CABANG ', t.kode_cabang)) AS nama_cabang,
                    
                    -- Data Current (Harian)
                    SUM(CASE WHEN t.created = :harian_date_1 AND t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_curr,
                    SUM(CASE WHEN t.created = :harian_date_2 THEN t.baki_debet ELSE 0 END) AS baki_curr,
                    
                    -- Data Previous (Closing Bulan Lalu)
                    SUM(CASE WHEN t.created = :closing_date_1 AND t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_prev,
                    SUM(CASE WHEN t.created = :closing_date_2 THEN t.baki_debet ELSE 0 END) AS baki_prev
                    
                FROM nominatif t
                LEFT JOIN kankas k ON k.kode_group1 = COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000'))
                WHERE t.created IN (:harian_date_3, :closing_date_3)
                {$filterSql_cabang}
                {$filter['sql']}
                GROUP BY kode_cabang, k.deskripsi_group1
            ";
        } else {
            // MODE PUSAT: Konsolidasi per Cabang
            $sql = "
                SELECT 
                    t.kode_cabang,
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_cabang)) AS nama_cabang,
                    
                    -- Data Current (Harian)
                    SUM(CASE WHEN t.created = :harian_date_1 AND t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_curr,
                    SUM(CASE WHEN t.created = :harian_date_2 THEN t.baki_debet ELSE 0 END) AS baki_curr,
                    
                    -- Data Previous (Closing Bulan Lalu)
                    SUM(CASE WHEN t.created = :closing_date_1 AND t.kolektibilitas IN ('KL','D','M') THEN t.baki_debet ELSE 0 END) AS npl_prev,
                    SUM(CASE WHEN t.created = :closing_date_2 THEN t.baki_debet ELSE 0 END) AS baki_prev
                    
                FROM nominatif t
                LEFT JOIN kode_kantor k ON t.kode_cabang = k.kode_kantor
                WHERE t.created IN (:harian_date_3, :closing_date_3)
                {$filter['sql']}
                GROUP BY t.kode_cabang, k.nama_kantor
            ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter tanggal berulang untuk amannya di semua versi PDO
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            $stmt->bindValue(':harian_date_3', $harian_date);
            
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            $stmt->bindValue(':closing_date_3', $closing_date);
            
            if ($displayMode === 'CABANG') {
                $stmt->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $kenaikan  = [];
            $penurunan = [];

            // Proses perhitungannya di PHP agar lebih ringan
            foreach ($rows as $r) {
                $npl_curr = (float)$r['npl_curr'];
                $baki_curr = (float)$r['baki_curr'];
                $npl_prev = (float)$r['npl_prev'];
                $baki_prev = (float)$r['baki_prev'];

                // Hitung persentase
                $persen_curr = $baki_curr > 0 ? ($npl_curr / $baki_curr) * 100 : 0;
                $persen_prev = $baki_prev > 0 ? ($npl_prev / $baki_prev) * 100 : 0;

                // Hitung Delta (Selisih)
                $delta = $persen_curr - $persen_prev;

                // Penangkal "NULL" untuk amannya Frontend
                $raw_nama = trim($r['nama_cabang'] ?? '');
                if ($raw_nama === '' || strtoupper($raw_nama) === 'NULL') {
                    $final_nama = 'KAS CABANG ' . $r['kode_cabang'];
                } else {
                    $final_nama = str_replace('Kc. ', '', $raw_nama);
                }

                $dataCabang = [
                    'kode_cabang'     => $r['kode_cabang'],
                    'nama_cabang'     => $final_nama,
                    'npl_persen_prev' => round($persen_prev, 2),
                    'npl_persen_curr' => round($persen_curr, 2),
                    'delta_npl'       => round($delta, 2)
                ];

                // Pisahkan mana yang naik (Memburuk), mana yang turun (Membaik)
                if ($delta > 0) {
                    $kenaikan[] = $dataCabang;
                } elseif ($delta < 0) {
                    $penurunan[] = $dataCabang;
                }
            }

            // =========================================================
            // SORTING: TOP 5 & SISA MASUK BOTTOM (MODE CABANG)
            // =========================================================

            // --- 1. KENAIKAN NPL (Memburuk / Merah) ---
            // Urutkan Kenaikan dari yang terburuk (Delta terbesar ke terkecil)
            usort($kenaikan, function($a, $b) { return $b['delta_npl'] <=> $a['delta_npl']; });
            
            $top_kenaikan = array_slice($kenaikan, 0, 5);
            $bottom_kenaikan = [];
            
            if ($displayMode === 'CABANG' && count($kenaikan) > 5) {
                // Sisa Kankas masuk ke bottom
                $bottom_kenaikan = array_slice($kenaikan, 5);
                usort($bottom_kenaikan, function($a, $b) { return $a['delta_npl'] <=> $b['delta_npl']; }); // ASC
            }

            // --- 2. PENURUNAN NPL (Membaik / Hijau) ---
            // Urutkan Penurunan dari yang terbaik (Delta paling minus ke kurang minus)
            usort($penurunan, function($a, $b) { return $a['delta_npl'] <=> $b['delta_npl']; });
            
            $top_penurunan = array_slice($penurunan, 0, 5);
            $bottom_penurunan = [];
            
            if ($displayMode === 'CABANG' && count($penurunan) > 5) {
                 // Sisa Kankas masuk ke bottom
                $bottom_penurunan = array_slice($penurunan, 5);
                usort($bottom_penurunan, function($a, $b) { return $b['delta_npl'] <=> $a['delta_npl']; }); // DESC
            }

            // Kembalikan Datanya
            return [
                'top_kenaikan'     => $top_kenaikan,
                'bottom_kenaikan'  => $bottom_kenaikan,
                'top_penurunan'    => $top_penurunan,
                'bottom_penurunan' => $bottom_penurunan
            ];

        } catch (\Exception $e) { // <-- Diubah jadi Exception Global
            return [
                'ERROR_DETEKSI'    => 'Terjadi masalah di sistem Delta NPL!',
                'pesan_error'      => $e->getMessage(),
                'file_error'       => $e->getFile(),
                'baris_error'      => $e->getLine(),
                'top_kenaikan'     => [],
                'bottom_kenaikan'  => [],
                'top_penurunan'    => [],
                'bottom_penurunan' => []
            ];
        }
    }

    public function getRunOffVsRealisasiKorwil($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        // Kita mulai narik data dari H+1 closing (Tanggal 1 bulan berjalan biasanya)
        $start_date = date('Y-m-d', strtotime('+1 day', strtotime($closing_date)));

        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // 1. TENTUKAN MODE TAMPILAN & FILTER SQL
        // =========================================================
        $displayMode = 'KORWIL'; // Default nampilin 4 Korwil
        $filterSql = "";
        $filterParams = [];

        if ($kode_kantor !== '000' && empty($korwil)) {
            // MODE CABANG: Tampilkan 1 Cabang aja
            $displayMode = 'CABANG';
            $filterSql .= " AND s.kode_kantor = :kode_kantor";
            $filterParams[':kode_kantor'] = $kode_kantor;
        } elseif (!empty($korwil)) {
            // MODE KORWIL FILTERED: Tampilkan semua cabang di Korwil tsb
            $displayMode = 'CABANG_BY_KORWIL';
            if ($korwil === 'SEMARANG') {
                $filterSql .= " AND s.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql .= " AND s.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql .= " AND s.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql .= " AND s.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 2. SUSUN QUERY BERDASARKAN MODE
        // =========================================================
        
        if ($displayMode === 'KORWIL') {
            // Tampilkan 4 Korwil Utama
            $sql = "
                WITH 
                master_korwil AS (
                    SELECT 'SEMARANG' AS nama_korwil, 1 as sort_order UNION ALL
                    SELECT 'SOLO', 2 UNION ALL
                    SELECT 'BANYUMAS', 3 UNION ALL
                    SELECT 'PEKALONGAN', 4
                ),
                summary_data AS (
                    SELECT 
                        CASE 
                            WHEN s.kode_kantor BETWEEN '001' AND '007' THEN 'SEMARANG'
                            WHEN s.kode_kantor BETWEEN '008' AND '014' THEN 'SOLO'
                            WHEN s.kode_kantor BETWEEN '015' AND '021' THEN 'BANYUMAS'
                            WHEN s.kode_kantor BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                            ELSE 'LAINNYA' 
                        END AS nama_korwil,
                        SUM(COALESCE(s.realisasi, 0)) AS total_realisasi,
                        SUM(COALESCE(s.pelunasan, 0)) AS total_lunas,
                        SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS total_angsuran,
                        SUM(COALESCE(s.angsuran, 0)) AS total_runoff
                    FROM summary_kredit_harian s
                    WHERE s.created >= :start_date AND s.created <= :end_date
                    GROUP BY 
                        CASE 
                            WHEN s.kode_kantor BETWEEN '001' AND '007' THEN 'SEMARANG'
                            WHEN s.kode_kantor BETWEEN '008' AND '014' THEN 'SOLO'
                            WHEN s.kode_kantor BETWEEN '015' AND '021' THEN 'BANYUMAS'
                            WHEN s.kode_kantor BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                            ELSE 'LAINNYA' 
                        END
                )
                SELECT 
                    mk.nama_korwil,
                    COALESCE(sd.total_realisasi, 0) AS realisasi,
                    COALESCE(sd.total_lunas, 0) AS lunas,
                    COALESCE(sd.total_angsuran, 0) AS angsuran,
                    COALESCE(sd.total_runoff, 0) AS total_runoff,
                    (COALESCE(sd.total_realisasi, 0) - COALESCE(sd.total_runoff, 0)) AS growth
                FROM master_korwil mk
                LEFT JOIN summary_data sd ON mk.nama_korwil = sd.nama_korwil
                ORDER BY mk.sort_order;
            ";
        } else {
            // MODE CABANG atau CABANG DALAM KORWIL (Tampilkan per Cabang)
            $sql = "
                SELECT 
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', s.kode_kantor)) AS nama_korwil,
                    SUM(COALESCE(s.realisasi, 0)) AS realisasi,
                    SUM(COALESCE(s.pelunasan, 0)) AS lunas,
                    SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS angsuran,
                    SUM(COALESCE(s.angsuran, 0)) AS total_runoff,
                    SUM(COALESCE(s.realisasi, 0)) - SUM(COALESCE(s.angsuran, 0)) AS growth
                FROM summary_kredit_harian s
                LEFT JOIN kode_kantor k ON s.kode_kantor = k.kode_kantor
                WHERE s.created >= :start_date AND s.created <= :end_date
                {$filterSql}
                GROUP BY s.kode_kantor, k.nama_kantor
                ORDER BY realisasi DESC
            ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter tanggal
            $stmt->bindValue(':start_date', $start_date);
            $stmt->bindValue(':end_date', $harian_date);
            
            // Bind parameter filter (Hanya jika mode cabang)
            if ($displayMode !== 'KORWIL') {
                foreach ($filterParams as $key => $val) {
                    $stmt->bindValue($key, $val);
                }
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 3. HITUNG GRAND TOTAL & FORMAT OUTPUT
            // =========================================================
            $grand_total = [
                'nama_korwil'  => 'TOTAL KONSOLIDASI',
                'realisasi'    => 0,
                'lunas'        => 0,
                'angsuran'     => 0,
                'total_runoff' => 0,
                'growth'       => 0
            ];

            $formattedData = [];
            foreach ($rows as $r) {
                $realisasi = (float) $r['realisasi'];
                $lunas     = (float) $r['lunas'];
                $angsuran  = (float) $r['angsuran'];
                $runoff    = (float) $r['total_runoff'];
                $growth    = (float) $r['growth'];

                $formattedData[] = [
                    'nama_korwil'  => str_replace('Kc. ', '', $r['nama_korwil']), // Bersihkan nama cabang
                    'realisasi'    => $realisasi,
                    'lunas'        => $lunas,
                    'angsuran'     => $angsuran,
                    'total_runoff' => $runoff,
                    'growth'       => $growth
                ];

                $grand_total['realisasi']    += $realisasi;
                $grand_total['lunas']        += $lunas;
                $grand_total['angsuran']     += $angsuran;
                $grand_total['total_runoff'] += $runoff;
                $grand_total['growth']       += $growth;
            }

            return [
                'detail_korwil' => $formattedData,
                'grand_total'   => $grand_total
            ];

        } catch (PDOException $e) {
            error_log("Error getRunOffVsRealisasiKorwil: " . $e->getMessage());
            return ['detail_korwil' => [], 'grand_total' => []];
        }
    }

    public function getRunOffRealisasi($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        // Mulai narik data dari H+1 closing
        $start_date = date('Y-m-d', strtotime('+1 day', strtotime($closing_date)));

        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // 1. TENTUKAN MODE TAMPILAN & FILTER SQL
        // =========================================================
        $displayMode = 'KORWIL'; // Default Konsolidasi
        $filterSql_master = ""; 
        $filterParams = [];

        if ($kode_kantor !== '000' && empty($korwil)) {
            // MODE CABANG: Breakdown ke Kas
            $displayMode = 'CABANG';
            $filterSql_master .= " AND g.kode_kantor = :kode_kantor_master";
            $filterParams[':kode_kantor_master'] = $kode_kantor;
        } elseif (!empty($korwil)) {
            // MODE KORWIL FILTERED: Tampilkan semua cabang di Korwil tsb
            $displayMode = 'CABANG_BY_KORWIL';
            if ($korwil === 'SEMARANG') {
                $filterSql_master .= " AND k.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql_master .= " AND k.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql_master .= " AND k.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql_master .= " AND k.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 2. SUSUN QUERY BERDASARKAN MODE
        // =========================================================
        
        if ($displayMode === 'KORWIL') {
            // --- MODE KONSOLIDASI: Tampilkan 4 Korwil Utama ---
            $sql = "
                WITH 
                master_korwil AS (
                    SELECT 'SEMARANG' AS nama_korwil, 1 as sort_order UNION ALL
                    SELECT 'SOLO', 2 UNION ALL
                    SELECT 'BANYUMAS', 3 UNION ALL
                    SELECT 'PEKALONGAN', 4
                ),
                summary_data AS (
                    SELECT 
                        CASE 
                            WHEN s.kode_kantor BETWEEN '001' AND '007' THEN 'SEMARANG'
                            WHEN s.kode_kantor BETWEEN '008' AND '014' THEN 'SOLO'
                            WHEN s.kode_kantor BETWEEN '015' AND '021' THEN 'BANYUMAS'
                            WHEN s.kode_kantor BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                            ELSE 'LAINNYA' 
                        END AS nama_korwil,
                        -- 🔥 FIX: realisasi ditambah restrukturisasi
                        SUM(COALESCE(s.realisasi, 0) + COALESCE(s.restrukturisasi, 0)) AS total_realisasi,
                        SUM(COALESCE(s.pelunasan, 0)) AS total_lunas,
                        SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS total_angsuran,
                        SUM(COALESCE(s.angsuran, 0)) AS total_runoff
                    FROM summary_kredit_harian_update s
                    WHERE s.created >= :start_date AND s.created <= :end_date
                    GROUP BY 
                        CASE 
                            WHEN s.kode_kantor BETWEEN '001' AND '007' THEN 'SEMARANG'
                            WHEN s.kode_kantor BETWEEN '008' AND '014' THEN 'SOLO'
                            WHEN s.kode_kantor BETWEEN '015' AND '021' THEN 'BANYUMAS'
                            WHEN s.kode_kantor BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                            ELSE 'LAINNYA' 
                        END
                )
                SELECT 
                    mk.nama_korwil,
                    COALESCE(sd.total_realisasi, 0) AS realisasi,
                    COALESCE(sd.total_lunas, 0) AS lunas,
                    COALESCE(sd.total_angsuran, 0) AS angsuran,
                    COALESCE(sd.total_runoff, 0) AS total_runoff,
                    (COALESCE(sd.total_realisasi, 0) - COALESCE(sd.total_runoff, 0)) AS growth
                FROM master_korwil mk
                LEFT JOIN summary_data sd ON mk.nama_korwil = sd.nama_korwil
                ORDER BY mk.sort_order;
            ";
        } elseif ($displayMode === 'CABANG_BY_KORWIL') {
            // --- MODE KORWIL: Breakdown ke Cabang ---
            $sql = "
                WITH summary_data AS (
                    SELECT 
                        s.kode_kantor,
                        -- 🔥 FIX: realisasi ditambah restrukturisasi
                        SUM(COALESCE(s.realisasi, 0) + COALESCE(s.restrukturisasi, 0)) AS total_realisasi,
                        SUM(COALESCE(s.pelunasan, 0)) AS total_lunas,
                        SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS total_angsuran,
                        SUM(COALESCE(s.angsuran, 0)) AS total_runoff
                    FROM summary_kredit_harian_update s
                    WHERE s.created >= :start_date AND s.created <= :end_date
                    GROUP BY s.kode_kantor
                )
                SELECT 
                    k.nama_kantor AS nama_korwil,
                    COALESCE(sd.total_realisasi, 0) AS realisasi,
                    COALESCE(sd.total_lunas, 0) AS lunas,
                    COALESCE(sd.total_angsuran, 0) AS angsuran,
                    COALESCE(sd.total_runoff, 0) AS total_runoff,
                    (COALESCE(sd.total_realisasi, 0) - COALESCE(sd.total_runoff, 0)) AS growth
                FROM kode_kantor k
                LEFT JOIN summary_data sd ON k.kode_kantor = sd.kode_kantor
                WHERE 1=1 {$filterSql_master}
                ORDER BY k.kode_kantor ASC;
            ";
        } elseif ($displayMode === 'CABANG') {
            // --- MODE CABANG SPESIFIK: Breakdown ke Kantor Kas ---
            $sql = "
                WITH summary_data AS (
                    SELECT 
                        s.kode_group_1,
                        -- 🔥 FIX: realisasi ditambah restrukturisasi
                        SUM(COALESCE(s.realisasi, 0) + COALESCE(s.restrukturisasi, 0)) AS total_realisasi,
                        SUM(COALESCE(s.pelunasan, 0)) AS total_lunas,
                        SUM(COALESCE(s.angsuran, 0) - COALESCE(s.pelunasan, 0)) AS total_angsuran,
                        SUM(COALESCE(s.angsuran, 0)) AS total_runoff
                    FROM summary_kredit_harian_update s
                    WHERE s.created >= :start_date AND s.created <= :end_date
                    GROUP BY s.kode_group_1
                )
                SELECT 
                    g.deskripsi_group1 AS nama_korwil,
                    COALESCE(sd.total_realisasi, 0) AS realisasi,
                    COALESCE(sd.total_lunas, 0) AS lunas,
                    COALESCE(sd.total_angsuran, 0) AS angsuran,
                    COALESCE(sd.total_runoff, 0) AS total_runoff,
                    (COALESCE(sd.total_realisasi, 0) - COALESCE(sd.total_runoff, 0)) AS growth
                FROM kankas g
                LEFT JOIN summary_data sd ON g.kode_group1 = sd.kode_group_1
                WHERE 1=1 {$filterSql_master}
                ORDER BY g.kode_group1 ASC;
            ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter tanggal
            $stmt->bindValue(':start_date', $start_date);
            $stmt->bindValue(':end_date', $harian_date);
            
            // Bind parameter filter
            if (!empty($filterParams)) {
                foreach ($filterParams as $key => $val) {
                    $stmt->bindValue($key, $val);
                }
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 3. HITUNG GRAND TOTAL & FORMAT OUTPUT
            // =========================================================
            $grand_total = [
                'nama_korwil'  => 'TOTAL KONSOLIDASI',
                'realisasi'    => 0,
                'lunas'        => 0,
                'angsuran'     => 0,
                'total_runoff' => 0,
                'growth'       => 0
            ];

            $formattedData = [];
            foreach ($rows as $r) {
                $realisasi = (float) $r['realisasi'];
                $lunas     = (float) $r['lunas'];
                $angsuran  = (float) $r['angsuran'];
                $runoff    = (float) $r['total_runoff'];
                $growth    = (float) $r['growth'];

                $formattedData[] = [
                    'nama_korwil'  => str_replace('Kc. ', '', $r['nama_korwil'] ?? 'KAS TANPA NAMA'), 
                    'realisasi'    => $realisasi,
                    'lunas'        => $lunas,
                    'angsuran'     => $angsuran,
                    'total_runoff' => $runoff,
                    'growth'       => $growth
                ];

                $grand_total['realisasi']    += $realisasi;
                $grand_total['lunas']        += $lunas;
                $grand_total['angsuran']     += $angsuran;
                $grand_total['total_runoff'] += $runoff;
                $grand_total['growth']       += $growth;
            }

            return [
                'detail_korwil' => $formattedData,
                'grand_total'   => $grand_total
            ];

        } catch (PDOException $e) {
            error_log("Error getRunOffVsRealisasi: " . $e->getMessage());
            return ['detail_korwil' => [], 'grand_total' => []];
        }
    }

    public function getFlowVsRecoveryNPL($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');
        
        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // 1. FILTER PINTAR & MODE TAMPILAN
        // =========================================================
        $displayMode = 'KORWIL'; // Default Konsolidasi
        $filterSql_cls = "";
        $filterSql_hrn = "";
        $filterSql_master = ""; // Filter khusus untuk tabel master (kantor / kankas)
        $filterParams = [];

        // Jika mode Cabang Spesifik (001, 002, dst) -> BREAKDOWN KE KAS
        if ($kode_kantor !== '000' && empty($korwil)) {
            $displayMode = 'CABANG';
            // Dibedakan nama parameternya agar PDO tidak error (HY093)
            $filterSql_cls .= " AND kode_cabang = :kode_kantor_cls";
            $filterSql_hrn .= " AND kode_cabang = :kode_kantor_hrn";
            $filterSql_master .= " AND g.kode_kantor = :kode_kantor_master";
            $filterParams[':kode_kantor_cls'] = $kode_kantor;
            $filterParams[':kode_kantor_hrn'] = $kode_kantor;
            $filterParams[':kode_kantor_master'] = $kode_kantor;
        } 
        // Jika mode Filter Dropdown Korwil -> BREAKDOWN KE CABANG
        elseif (!empty($korwil)) {
            $displayMode = 'CABANG_BY_KORWIL';
            if ($korwil === 'SEMARANG') {
                $filterSql_cls .= " AND kode_cabang BETWEEN '001' AND '007'";
                $filterSql_hrn .= " AND kode_cabang BETWEEN '001' AND '007'";
                $filterSql_master .= " AND kantor.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql_cls .= " AND kode_cabang BETWEEN '008' AND '014'";
                $filterSql_hrn .= " AND kode_cabang BETWEEN '008' AND '014'";
                $filterSql_master .= " AND kantor.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql_cls .= " AND kode_cabang BETWEEN '015' AND '021'";
                $filterSql_hrn .= " AND kode_cabang BETWEEN '015' AND '021'";
                $filterSql_master .= " AND kantor.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql_cls .= " AND kode_cabang BETWEEN '022' AND '028'";
                $filterSql_hrn .= " AND kode_cabang BETWEEN '022' AND '028'";
                $filterSql_master .= " AND kantor.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 2. CTE MASTER (Menggunakan kolom: kode_cabang & kode_group1)
        // =========================================================
        $cte_base = "
            WITH 
            closing AS (
                SELECT no_rekening, kode_cabang, kode_group1, kolektibilitas AS kolek_prev, baki_debet AS baki_prev
                FROM nominatif
                WHERE created = :closing_date
                AND kolektibilitas IN ('L','DP','KL','D','M')
                {$filterSql_cls}
            ),
            harian AS (
                SELECT no_rekening, kode_cabang, kode_group1, kolektibilitas AS kolek_curr, baki_debet AS baki_curr
                FROM nominatif
                WHERE created = :harian_date
                AND kolektibilitas IN ('L','DP','KL','D','M')
                {$filterSql_hrn}
            ),
            gabung AS (
                SELECT 
                    c.no_rekening,
                    c.kode_cabang,
                    c.kode_group1,
                    c.kolek_prev,
                    c.baki_prev,
                    h.kolek_curr,
                    COALESCE(h.baki_curr, 0) AS baki_curr,
                    CASE WHEN h.no_rekening IS NULL THEN 1 ELSE 0 END AS is_lunas
                FROM closing c
                LEFT JOIN harian h ON c.no_rekening = h.no_rekening
            )
        ";

        // =========================================================
        // 3. SUSUN QUERY BERDASARKAN MODE TAMPILAN
        // =========================================================
        if ($displayMode === 'KORWIL') {
            // --- MODE KONSOLIDASI: Tampilkan 4 Korwil Utama ---
            $sql = $cte_base . ",
            kalkulasi AS (
                SELECT 
                    CASE 
                        WHEN kode_cabang BETWEEN '001' AND '007' THEN 'SEMARANG'
                        WHEN kode_cabang BETWEEN '008' AND '014' THEN 'SOLO'
                        WHEN kode_cabang BETWEEN '015' AND '021' THEN 'BANYUMAS'
                        WHEN kode_cabang BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                        ELSE 'LAINNYA' 
                    END AS nama_korwil,
                    SUM(CASE WHEN kolek_prev IN ('L','DP') AND kolek_curr IN ('KL','D','M') THEN baki_curr ELSE 0 END) AS flow_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND kolek_curr IN ('L','DP') THEN baki_curr ELSE 0 END) AS backflow,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 1 THEN baki_prev ELSE 0 END) AS lunas_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 0 THEN (baki_prev - baki_curr) ELSE 0 END) AS angsuran_npl
                FROM gabung
                GROUP BY 
                    CASE 
                        WHEN kode_cabang BETWEEN '001' AND '007' THEN 'SEMARANG'
                        WHEN kode_cabang BETWEEN '008' AND '014' THEN 'SOLO'
                        WHEN kode_cabang BETWEEN '015' AND '021' THEN 'BANYUMAS'
                        WHEN kode_cabang BETWEEN '022' AND '028' THEN 'PEKALONGAN'
                        ELSE 'LAINNYA' 
                    END
            ),
            master_korwil AS (
                SELECT 'SEMARANG' AS nama_korwil, 1 as sort_order UNION ALL
                SELECT 'SOLO', 2 UNION ALL
                SELECT 'BANYUMAS', 3 UNION ALL
                SELECT 'PEKALONGAN', 4
            )
            SELECT 
                mk.nama_korwil,
                COALESCE(k.flow_npl, 0) AS flow_npl,
                COALESCE(k.backflow, 0) AS backflow,
                COALESCE(k.lunas_npl, 0) AS lunas_npl,
                COALESCE(k.angsuran_npl, 0) AS angsuran_npl,
                (COALESCE(k.backflow, 0) + COALESCE(k.lunas_npl, 0) + COALESCE(k.angsuran_npl, 0)) AS total_recovery
            FROM master_korwil mk
            LEFT JOIN kalkulasi k ON mk.nama_korwil = k.nama_korwil
            ORDER BY mk.sort_order;
            ";
        } elseif ($displayMode === 'CABANG_BY_KORWIL') {
            // --- MODE KORWIL: Breakdown ke Cabang ---
            $sql = $cte_base . ",
            kalkulasi AS (
                SELECT 
                    kode_cabang,
                    SUM(CASE WHEN kolek_prev IN ('L','DP') AND kolek_curr IN ('KL','D','M') THEN baki_curr ELSE 0 END) AS flow_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND kolek_curr IN ('L','DP') THEN baki_curr ELSE 0 END) AS backflow,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 1 THEN baki_prev ELSE 0 END) AS lunas_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 0 THEN (baki_prev - baki_curr) ELSE 0 END) AS angsuran_npl
                FROM gabung
                GROUP BY kode_cabang
            )
            SELECT 
                kantor.nama_kantor AS nama_korwil,
                COALESCE(k.flow_npl, 0) AS flow_npl,
                COALESCE(k.backflow, 0) AS backflow,
                COALESCE(k.lunas_npl, 0) AS lunas_npl,
                COALESCE(k.angsuran_npl, 0) AS angsuran_npl,
                (COALESCE(k.backflow, 0) + COALESCE(k.lunas_npl, 0) + COALESCE(k.angsuran_npl, 0)) AS total_recovery
            FROM kode_kantor kantor
            LEFT JOIN kalkulasi k ON kantor.kode_kantor = k.kode_cabang
            WHERE 1=1 {$filterSql_master}
            ORDER BY kantor.kode_kantor ASC;
            ";
        } elseif ($displayMode === 'CABANG') {
            // --- MODE CABANG SPESIFIK: Breakdown ke Kantor Kas (Base kankas agar semua Kas muncul) ---
            $sql = $cte_base . ",
            kalkulasi AS (
                SELECT 
                    kode_group1,
                    SUM(CASE WHEN kolek_prev IN ('L','DP') AND kolek_curr IN ('KL','D','M') THEN baki_curr ELSE 0 END) AS flow_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND kolek_curr IN ('L','DP') THEN baki_curr ELSE 0 END) AS backflow,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 1 THEN baki_prev ELSE 0 END) AS lunas_npl,
                    SUM(CASE WHEN kolek_prev IN ('KL','D','M') AND is_lunas = 0 THEN (baki_prev - baki_curr) ELSE 0 END) AS angsuran_npl
                FROM gabung
                GROUP BY kode_group1
            )
            SELECT 
                g.deskripsi_group1 AS nama_korwil,
                COALESCE(k.flow_npl, 0) AS flow_npl,
                COALESCE(k.backflow, 0) AS backflow,
                COALESCE(k.lunas_npl, 0) AS lunas_npl,
                COALESCE(k.angsuran_npl, 0) AS angsuran_npl,
                (COALESCE(k.backflow, 0) + COALESCE(k.lunas_npl, 0) + COALESCE(k.angsuran_npl, 0)) AS total_recovery
            FROM kankas g
            LEFT JOIN kalkulasi k ON g.kode_group1 = k.kode_group1
            WHERE 1=1 {$filterSql_master}
            ORDER BY g.kode_group1 ASC;
            ";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind parameter tanggal
            $stmt->bindValue(':closing_date', $closing_date);
            $stmt->bindValue(':harian_date', $harian_date);
            
            // Bind parameter filter master & kankas jika ada
            if (!empty($filterParams)) {
                foreach ($filterParams as $key => $val) {
                    $stmt->bindValue($key, $val);
                }
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 4. HITUNG GRAND TOTAL & FORMAT OUTPUT
            // =========================================================
            $grand_total = [
                'nama_korwil'    => 'TOTAL KONSOLIDASI',
                'flow_npl'       => 0,
                'backflow'       => 0,
                'lunas_npl'      => 0,
                'angsuran_npl'   => 0,
                'total_recovery' => 0
            ];

            $formattedData = [];
            foreach ($rows as $r) {
                $flow_npl     = (float) $r['flow_npl'];
                $backflow     = (float) $r['backflow'];
                $lunas_npl    = (float) $r['lunas_npl'];
                $angsuran_npl = (float) $r['angsuran_npl'];
                $recovery     = (float) $r['total_recovery'];

                $formattedData[] = [
                    'nama_korwil'    => str_replace('Kc. ', '', $r['nama_korwil'] ?? 'KAS TANPA NAMA'), 
                    'flow_npl'       => $flow_npl,
                    'backflow'       => $backflow,
                    'lunas_npl'      => $lunas_npl,
                    'angsuran_npl'   => $angsuran_npl,
                    'total_recovery' => $recovery
                ];

                $grand_total['flow_npl']       += $flow_npl;
                $grand_total['backflow']       += $backflow;
                $grand_total['lunas_npl']      += $lunas_npl;
                $grand_total['angsuran_npl']   += $angsuran_npl;
                $grand_total['total_recovery'] += $recovery;
            }

            return [
                'detail_korwil' => $formattedData,
                'grand_total'   => $grand_total
            ];

        } catch (PDOException $e) {
            error_log("Error getFlowVsRecoveryNPL: " . $e->getMessage());
            return ['detail_korwil' => [], 'grand_total' => []];
        }
    }

    public function getTopBottomRealisasi($input) {
        $harian_date  = $input['harian_date'] ?? date('Y-m-d');
        $closing_date = $input['closing_date'] ?? date('Y-m-t', strtotime($harian_date . ' -1 month')); 
        
        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // FILTER CABANG & KORWIL (Berlaku untuk Master Kantor)
        // =========================================================
        $filterSql = "";
        $filterParams = [];

        if ($kode_kantor !== '000' && empty($korwil)) {
            $filterSql .= " AND k.kode_kantor = :kode_kantor";
            $filterParams[':kode_kantor'] = $kode_kantor;
        } elseif (!empty($korwil)) {
            if ($korwil === 'SEMARANG') {
                $filterSql .= " AND k.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql .= " AND k.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql .= " AND k.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql .= " AND k.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // ==========================================
        // 1. QUERY REALISASI CABANG (Base: Master Kantor)
        // ==========================================
        // 🔥 FIX: Pakai LEFT JOIN agar realisasi 0 tetap muncul
        $sqlCabang = "
            SELECT 
                k.kode_kantor AS kode_cabang,
                k.nama_kantor AS nama_cabang,
                COALESCE(SUM(t.realisasi_pokok), 0) AS total_realisasi,
                COUNT(DISTINCT t.no_rekening) AS noa_realisasi
            FROM kode_kantor k
            LEFT JOIN update_realisasi_kredit t ON k.kode_kantor = t.kode_kantor
                AND t.tanggal_realisasi > :closing_date
                AND t.tanggal_realisasi <= :harian_date
            WHERE k.kode_kantor <> '000'
            {$filterSql}
            GROUP BY k.kode_kantor, k.nama_kantor
        ";

        // ==========================================
        // 2. QUERY REALISASI AO (Top 5 Saja)
        // ==========================================
        $sqlAO = "
            SELECT 
                t.kode_group2,
                COALESCE(ao.nama_ao, CONCAT('AO ', t.kode_group2)) AS nama_ao,
                t.kode_kantor AS kode_cabang,
                COALESCE(k.nama_kantor, CONCAT('CABANG ', t.kode_kantor)) AS nama_cabang,
                SUM(COALESCE(t.realisasi_pokok, 0)) AS total_realisasi,
                COUNT(DISTINCT t.no_rekening) AS noa_realisasi
            FROM update_realisasi_kredit t
            LEFT JOIN ao_kredit ao ON t.kode_group2 = ao.kode_group2
            LEFT JOIN kode_kantor k ON t.kode_kantor = k.kode_kantor
            WHERE t.tanggal_realisasi > :closing_date
              AND t.tanggal_realisasi <= :harian_date
              AND t.kode_kantor <> '000'
            {$filterSql}
            GROUP BY t.kode_group2, ao.nama_ao, t.kode_kantor, k.nama_kantor
            HAVING SUM(COALESCE(t.realisasi_pokok, 0)) > 0
            ORDER BY total_realisasi DESC
            LIMIT 5
        ";

        try {
            // --- Eksekusi Cabang ---
            $stmtCabang = $this->pdo->prepare($sqlCabang);
            $stmtCabang->bindValue(':harian_date', $harian_date);
            $stmtCabang->bindValue(':closing_date', $closing_date);
            foreach ($filterParams as $key => $val) { $stmtCabang->bindValue($key, $val); }
            $stmtCabang->execute();
            $rowsCabang = $stmtCabang->fetchAll(PDO::FETCH_ASSOC);

            $cabangData = array_map(function($r) {
                return [
                    'kode_cabang'     => $r['kode_cabang'],
                    'nama_cabang'     => $r['nama_cabang'],
                    'total_realisasi' => (float) $r['total_realisasi'],
                    'noa_realisasi'   => (int) $r['noa_realisasi']
                ];
            }, $rowsCabang);

            // Sort Descending (Top 5 Tertinggi)
            usort($cabangData, function($a, $b) {
                return $b['total_realisasi'] <=> $a['total_realisasi'];
            });
            $topCabang = array_slice($cabangData, 0, 5);

            // Sort Ascending (Bottom 5 Terendah - Sekarang Cabang 0 akan ikut)
            usort($cabangData, function($a, $b) {
                // Jika realisasi sama, sort by kode cabang
                if ($a['total_realisasi'] == $b['total_realisasi']) {
                    return $a['kode_cabang'] <=> $b['kode_cabang'];
                }
                return $a['total_realisasi'] <=> $b['total_realisasi'];
            });
            $bottomCabang = array_slice($cabangData, 0, 5);

            // --- Eksekusi AO ---
            $stmtAO = $this->pdo->prepare($sqlAO);
            $stmtAO->bindValue(':harian_date', $harian_date);
            $stmtAO->bindValue(':closing_date', $closing_date);
            foreach ($filterParams as $key => $val) { $stmtAO->bindValue($key, $val); }
            $stmtAO->execute();
            $rowsAO = $stmtAO->fetchAll(PDO::FETCH_ASSOC);

            $topAO = array_map(function($r) {
                return [
                    'kode_ao'         => $r['kode_group2'],
                    'nama_ao'         => $r['nama_ao'],
                    'kode_cabang'     => $r['kode_cabang'],
                    'nama_cabang'     => $r['nama_cabang'],
                    'total_realisasi' => (float) $r['total_realisasi'],
                    'noa_realisasi'   => (int) $r['noa_realisasi']
                ];
            }, $rowsAO);

            // --- GRAND TOTAL ---
            $grand_total_realisasi = 0;
            $grand_total_noa = 0;
            foreach($cabangData as $cd) {
                $grand_total_realisasi += $cd['total_realisasi'];
                $grand_total_noa += $cd['noa_realisasi'];
            }

            return [
                'top_cabang'    => $topCabang,
                'bottom_cabang' => $bottomCabang,
                'top_ao'        => $topAO,
                'grand_total'   => [
                    'total_realisasi' => $grand_total_realisasi,
                    'noa_realisasi'   => $grand_total_noa
                ]
            ];

        } catch (PDOException $e) {
            error_log("Error getTopBottomRealisasi: " . $e->getMessage());
            return [
                'top_cabang'    => [], 
                'bottom_cabang' => [], 
                'top_ao'        => [],
                'grand_total'   => ['total_realisasi' => 0, 'noa_realisasi' => 0]
            ];
        }
    }

    public function getTopBottomRealisasiNominatif($input) {
        // --- SUNTIK TENAGA SERVER (BEBAS TIME OUT) ---
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        // Tetap pakai Actual (Hari Ini)
        $harian_date  = $input['harian_date'] ?? date('Y-m-d');
        $closing_date = $input['closing_date'] ?? date('Y-m-t', strtotime($harian_date . ' -1 month')); 
        
        $kode_kantor = $input['kode_kantor'] ?? '000';

        // Panggil helper filter
        $filter = $this->buildFilterQuery($input, 't');

        $displayMode = 'PUSAT';
        $filterSql_ao_where = "";

        if ($kode_kantor !== '000') {
            $displayMode = 'CABANG';
            // Filter tambahan untuk WHERE di tabel AO
            $filterSql_ao_where = " AND ao.kode_kantor = :kode_kantor_master "; 
        }

        // =========================================================
        // 1. QUERY CABANG DENGAN DYNAMIC BREAKDOWN (Cabang vs Kas)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // View Cabang: Breakdown per Kantor Kas
            $sqlCabang = "
                SELECT 
                    k.kode_group1 AS kode_cabang,
                    COALESCE(k.deskripsi_group1, CONCAT('KAS ', k.kode_group1)) AS nama_cabang,
                    COALESCE(SUM(t.jml_pinjaman), 0) AS total_realisasi,
                    COUNT(t.no_rekening) AS noa_realisasi
                FROM kankas k
                LEFT JOIN nominatif t 
                    ON k.kode_group1 = COALESCE(NULLIF(TRIM(t.kode_group1), ''), CONCAT(t.kode_cabang, '000'))
                    AND t.created = :harian_date_1
                    AND t.tgl_realisasi > :closing_date_1
                    AND t.tgl_realisasi <= :harian_date_2
                    {$filter['sql']}
                WHERE k.kode_kantor = :kode_kantor_master
                GROUP BY k.kode_group1, k.deskripsi_group1
            ";
        } else {
            // View ALL: Breakdown per Cabang
            $sqlCabang = "
                SELECT 
                    k.kode_kantor AS kode_cabang,
                    COALESCE(k.nama_kantor, CONCAT('CABANG ', k.kode_kantor)) AS nama_cabang,
                    COALESCE(SUM(t.jml_pinjaman), 0) AS total_realisasi,
                    COUNT(t.no_rekening) AS noa_realisasi
                FROM kode_kantor k
                LEFT JOIN nominatif t 
                    ON k.kode_kantor = t.kode_cabang
                    AND t.created = :harian_date_1
                    AND t.tgl_realisasi > :closing_date_1
                    AND t.tgl_realisasi <= :harian_date_2
                    {$filter['sql']}
                WHERE k.kode_kantor <> '000'
                GROUP BY k.kode_kantor, k.nama_kantor
            ";
        }

        // =========================================================
        // 2. QUERY AO (Menampilkan Semua AO + Join Nama Cabang Induk)
        // =========================================================
        $sqlAO = "
            SELECT 
                ao.kode_group2 AS kode_ao,
                COALESCE(NULLIF(TRIM(ao.nama_ao), ''), CONCAT('AO ', ao.kode_group2)) AS nama_ao,
                COALESCE(k.nama_kantor, '') AS nama_cabang_induk,
                COALESCE(SUM(t.jml_pinjaman), 0) AS total_realisasi,
                COUNT(t.no_rekening) AS noa_realisasi
            FROM ao_kredit ao
            LEFT JOIN kode_kantor k ON ao.kode_kantor = k.kode_kantor
            LEFT JOIN nominatif t 
                ON ao.kode_group2 = t.kode_group2
                AND t.created = :harian_date_1
                AND t.tgl_realisasi > :closing_date_1
                AND t.tgl_realisasi <= :harian_date_2
                {$filter['sql']}
            WHERE 1=1 {$filterSql_ao_where}
            GROUP BY ao.kode_group2, ao.nama_ao, k.nama_kantor
        ";

        try {
            // --- Eksekusi Cabang ---
            $stmtCabang = $this->pdo->prepare($sqlCabang);
            $stmtCabang->bindValue(':harian_date_1', $harian_date);
            $stmtCabang->bindValue(':harian_date_2', $harian_date);
            $stmtCabang->bindValue(':closing_date_1', $closing_date);
            
            if ($displayMode === 'CABANG') {
                $stmtCabang->bindValue(':kode_kantor_master', $kode_kantor);
            }
            
            foreach ($filter['params'] as $key => $val) {
                $stmtCabang->bindValue($key, $val);
            }
            $stmtCabang->execute();
            $rowsCabang = $stmtCabang->fetchAll(PDO::FETCH_ASSOC);

            // --- Eksekusi AO ---
            $stmtAO = $this->pdo->prepare($sqlAO);
            $stmtAO->bindValue(':harian_date_1', $harian_date);
            $stmtAO->bindValue(':harian_date_2', $harian_date);
            $stmtAO->bindValue(':closing_date_1', $closing_date);
            
            if ($displayMode === 'CABANG') {
                $stmtAO->bindValue(':kode_kantor_master', $kode_kantor);
            }
            
            foreach ($filter['params'] as $key => $val) {
                $stmtAO->bindValue($key, $val);
            }
            $stmtAO->execute();
            $rowsAO = $stmtAO->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 3. PARSING DATA & GRAND TOTAL
            // =========================================================
            $grand_total_realisasi = 0;
            $grand_total_noa = 0;
            
            $cabangData = [];
            foreach ($rowsCabang as $r) {
                $raw_nama = trim($r['nama_cabang'] ?? '');
                if ($raw_nama === '' || strtoupper($raw_nama) === 'NULL') {
                    $final_nama = 'KAS CABANG ' . $r['kode_cabang'];
                } else {
                    $final_nama = str_replace('Kc. ', '', $raw_nama);
                }

                $realisasi = (float) $r['total_realisasi'];
                $noa       = (int) $r['noa_realisasi'];

                $grand_total_realisasi += $realisasi;
                $grand_total_noa += $noa;

                $cabangData[] = [
                    'kode_cabang'     => $r['kode_cabang'],
                    'nama_cabang'     => $final_nama,
                    'total_realisasi' => $realisasi,
                    'noa_realisasi'   => $noa
                ];
            }

            $aoData = [];
            foreach ($rowsAO as $r) {
                $cabang_short = trim(str_replace(['Kc. ', 'Cab. '], '', $r['nama_cabang_induk'] ?? ''));
                $nama_ao_raw  = trim($r['nama_ao']);
                
                if (!empty($cabang_short) && strtolower($cabang_short) !== 'unknown') {
                    $nama_ao_combined = "[{$cabang_short}] {$nama_ao_raw}";
                } else {
                    $nama_ao_combined = $nama_ao_raw;
                }

                if (strlen($nama_ao_combined) > 25) {
                    $nama_ao_combined = mb_strimwidth($nama_ao_combined, 0, 22, '...');
                }

                $aoData[] = [
                    'kode_ao'         => $r['kode_ao'],
                    'nama_ao'         => $nama_ao_combined, 
                    'total_realisasi' => (float) $r['total_realisasi'],
                    'noa_realisasi'   => (int) $r['noa_realisasi']
                ];
            }

            // =========================================================
            // 4. SORTING: ANTI REALISASI NOL MASUK TOP
            // =========================================================
            
            // --- A. PISAHKAN CABANG/KANKAS ---
            $cabang_ada_realisasi = array_filter($cabangData, function($c) { return $c['total_realisasi'] > 0; });
            $cabang_nol_realisasi = array_filter($cabangData, function($c) { return $c['total_realisasi'] <= 0; });

            usort($cabang_ada_realisasi, function($a, $b) { return $b['total_realisasi'] <=> $a['total_realisasi']; });
            
            // Cuma yang > 0 yang boleh masuk TOP (Maks 5)
            $topCabang = array_slice($cabang_ada_realisasi, 0, 5);
            
            // Sisanya digabung dengan yang Nol, lemparkan ke Bottom
            $sisaCabang = array_slice($cabang_ada_realisasi, 5);
            $poolBottomCabang = array_merge($sisaCabang, $cabang_nol_realisasi);
            usort($poolBottomCabang, function($a, $b) { return $a['total_realisasi'] <=> $b['total_realisasi']; }); // ASC (Nol paling atas)

            if ($displayMode === 'CABANG') {
                $bottomCabang = $poolBottomCabang; // Tampil semua
            } else {
                $bottomCabang = array_slice($poolBottomCabang, 0, 5);
            }

            // --- B. PISAHKAN AO KREDIT ---
            $ao_ada_realisasi = array_filter($aoData, function($c) { return $c['total_realisasi'] > 0; });
            $ao_nol_realisasi = array_filter($aoData, function($c) { return $c['total_realisasi'] <= 0; });

            usort($ao_ada_realisasi, function($a, $b) { return $b['total_realisasi'] <=> $a['total_realisasi']; });
            
            // Cuma AO yang > 0 yang boleh masuk TOP
            $topAO = array_slice($ao_ada_realisasi, 0, 5);

            // 5. KEMBALIKAN PAYLOAD
            return [
                'top_cabang'    => $topCabang,
                'bottom_cabang' => $bottomCabang,
                'top_ao'        => $topAO,
                'grand_total'   => [
                    'total_realisasi' => $grand_total_realisasi,
                    'noa_realisasi'   => $grand_total_noa
                ]
            ];

        } catch (\Exception $e) { 
            return [
                'ERROR_DETEKSI' => 'Terjadi masalah di sistem Realisasi Nominatif!',
                'pesan_error'   => $e->getMessage(),
                'file_error'    => $e->getFile(),
                'baris_error'   => $e->getLine(),
                'top_cabang'    => [], 
                'bottom_cabang' => [], 
                'top_ao'        => [],
                'grand_total'   => ['total_realisasi' => 0, 'noa_realisasi' => 0]
            ];
        }
    }


    public function getPerkembanganDeposito($input) {
        // --- 1. SUNTIK TENAGA SERVER (BEBAS TIME OUT) ---
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');

        $kode_kantor = $input['kode_kantor'] ?? '000';
        $korwil      = strtoupper($input['korwil'] ?? '');

        // =========================================================
        // 2. FILTER PINTAR & MODE TAMPILAN
        // =========================================================
        $displayMode = 'KORWIL'; 
        $filterSql_cabang = ""; 
        
        // Panggil helper filter (beri alias 'nd' untuk nominatif_deposito)
        $filter = $this->buildFilterQuery($input, 'nd');
        
        // Trik sakti bawaan: Ganti 'kode_cabang' jadi 'kode_kantor' khusus untuk tabel ini
        $filter['sql'] = str_replace('nd.kode_cabang', 'nd.kode_kantor', $filter['sql']);

        // Tentukan Mode Tampilan & Amankan query breakdown-nya
        if ($kode_kantor !== '000' && empty($korwil)) {
            $displayMode = 'CABANG';
            $filterSql_cabang .= " AND nd.kode_kantor = :kode_kantor_master";
        } elseif (!empty($korwil)) {
            $displayMode = 'CABANG_BY_KORWIL';
            // Filter ringan diletakkan di CTE agar proses hitung lebih cepat
            if ($korwil === 'SEMARANG') {
                $filterSql_cabang .= " AND nd.kode_kantor BETWEEN '001' AND '007'";
            } elseif ($korwil === 'SOLO') {
                $filterSql_cabang .= " AND nd.kode_kantor BETWEEN '008' AND '014'";
            } elseif ($korwil === 'BANYUMAS') {
                $filterSql_cabang .= " AND nd.kode_kantor BETWEEN '015' AND '021'";
            } elseif ($korwil === 'PEKALONGAN') {
                $filterSql_cabang .= " AND nd.kode_kantor BETWEEN '022' AND '028'";
            }
        }

        // =========================================================
        // 3. KONDISIONAL QUERY UTAMA (BREAKDOWN KANKAS VS PUSAT)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // MODE CABANG: Breakdown ke Kantor Kas (Sudah Bebas NULL & Super Cepat)
            $sql_main = "
                WITH rekap_rek AS (
                    SELECT 
                        no_rekening,
                        -- Jika kode_group1 kosong/null, gabungkan kode_kantor + '000' (Misal: 004000)
                        MAX(COALESCE(NULLIF(TRIM(kode_group1), ''), CONCAT(kode_kantor, '000'))) AS kode_target, 
                        SUM(CASE WHEN created = :closing_date_1 THEN 1 ELSE 0 END) AS is_prev,
                        SUM(CASE WHEN created = :harian_date_1 THEN 1 ELSE 0 END) AS is_curr,
                        SUM(CASE WHEN created = :closing_date_2 THEN saldo_akhir ELSE 0 END) AS saldo_prev,
                        SUM(CASE WHEN created = :harian_date_2 THEN saldo_akhir ELSE 0 END) AS saldo_curr
                    FROM nominatif_deposito nd
                    WHERE created IN (:closing_date_3, :harian_date_3)
                    {$filter['sql']} {$filterSql_cabang}
                    GROUP BY no_rekening
                )
                SELECT 
                    r.kode_target AS kode_kantor,
                    COALESCE(g.deskripsi_group1, CONCAT('KAS CABANG ', r.kode_target)) AS nama_cabang,
                    SUM(CASE WHEN r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_curr, 
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_tambah,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN 1 ELSE 0 END) AS noa_kurang,
                    SUM(r.saldo_prev) AS saldo_prev,
                    SUM(r.saldo_curr) AS saldo_curr,
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN r.saldo_curr ELSE 0 END) AS saldo_baru,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN r.saldo_prev ELSE 0 END) AS saldo_cair
                FROM rekap_rek r
                LEFT JOIN kankas g ON TRIM(g.kode_group1) = r.kode_target
                GROUP BY r.kode_target, g.deskripsi_group1
            ";
        } else {
            // MODE KONSOLIDASI & KORWIL: Breakdown ke Cabang Utama (Lebih Ringan)
            $sql_main = "
                WITH rekap_rek AS (
                    SELECT 
                        no_rekening,
                        MAX(kode_kantor) AS kode_target, 
                        SUM(CASE WHEN created = :closing_date_1 THEN 1 ELSE 0 END) AS is_prev,
                        SUM(CASE WHEN created = :harian_date_1 THEN 1 ELSE 0 END) AS is_curr,
                        SUM(CASE WHEN created = :closing_date_2 THEN saldo_akhir ELSE 0 END) AS saldo_prev,
                        SUM(CASE WHEN created = :harian_date_2 THEN saldo_akhir ELSE 0 END) AS saldo_curr
                    FROM nominatif_deposito nd
                    WHERE created IN (:closing_date_3, :harian_date_3)
                    {$filter['sql']} {$filterSql_cabang}
                    GROUP BY no_rekening
                )
                SELECT 
                    r.kode_target AS kode_kantor,
                    COALESCE((SELECT nama_kantor FROM kode_kantor WHERE kode_kantor = r.kode_target LIMIT 1), CONCAT('CABANG ', r.kode_target)) AS nama_cabang,
                    SUM(CASE WHEN r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_curr, 
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_tambah,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN 1 ELSE 0 END) AS noa_kurang,
                    SUM(r.saldo_prev) AS saldo_prev,
                    SUM(r.saldo_curr) AS saldo_curr,
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN r.saldo_curr ELSE 0 END) AS saldo_baru,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN r.saldo_prev ELSE 0 END) AS saldo_cair
                FROM rekap_rek r
                GROUP BY r.kode_target
            ";
        }

        // =========================================================
        // 4. QUERY KHUSUS UNTUK KINERJA AO DANA (Tanpa CTE, Super Ngebut)
        // =========================================================
        $sql_ao = "
            SELECT 
                nd.kode_group2,
                MAX(ao.deskripsi_group2) AS nama_ao,
                SUM(CASE WHEN nd.created = :closing_date_2 THEN nd.saldo_akhir ELSE 0 END) AS saldo_prev,
                SUM(CASE WHEN nd.created = :harian_date_2 THEN nd.saldo_akhir ELSE 0 END) AS saldo_curr
            FROM nominatif_deposito nd
            LEFT JOIN kode_ao_dep ao ON nd.kode_group2 = ao.kode_group2
            WHERE nd.created IN (:closing_date_3, :harian_date_3)
            AND nd.kode_group2 IS NOT NULL AND TRIM(nd.kode_group2) != ''
            {$filter['sql']} {$filterSql_cabang}
            GROUP BY nd.kode_group2
        ";

        try {
            // --- EKSEKUSI QUERY UTAMA ---
            $stmt = $this->pdo->prepare($sql_main);
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            $stmt->bindValue(':closing_date_3', $closing_date);
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            $stmt->bindValue(':harian_date_3', $harian_date);
            
            if ($displayMode === 'CABANG') {
                $stmt->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- EKSEKUSI QUERY AO DANA ---
            $stmt_ao = $this->pdo->prepare($sql_ao);
            $stmt_ao->bindValue(':closing_date_2', $closing_date);
            $stmt_ao->bindValue(':closing_date_3', $closing_date);
            $stmt_ao->bindValue(':harian_date_2', $harian_date);
            $stmt_ao->bindValue(':harian_date_3', $harian_date);
            
            if ($displayMode === 'CABANG') {
                $stmt_ao->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt_ao->bindValue($key, $val);
            }
            $stmt_ao->execute();
            $ao_rows = $stmt_ao->fetchAll(PDO::FETCH_ASSOC);

            // =========================================================
            // 5. WADAH UNTUK GENERATE KORWIL & GRAND TOTAL
            // =========================================================
            $korwil_data = [];
            $korwil_list = ['SEMARANG', 'SOLO', 'BANYUMAS', 'PEKALONGAN'];
            foreach ($korwil_list as $kw) {
                $korwil_data[$kw] = [
                    'nama_korwil' => $kw, 'noa_curr' => 0, 'noa_tambah' => 0, 'noa_kurang' => 0, 
                    'saldo_prev' => 0, 'saldo_curr' => 0, 'delta_saldo' => 0, 'saldo_baru' => 0, 'saldo_cair' => 0
                ];
            }

            $grand_total = [
                'nama_korwil' => 'TOTAL KONSOLIDASI', 'noa_curr' => 0, 'noa_tambah' => 0, 'noa_kurang' => 0, 
                'saldo_prev' => 0, 'saldo_curr' => 0, 'delta_saldo' => 0, 'saldo_baru' => 0, 'saldo_cair' => 0
            ];
            $cabang_array = [];

            // =========================================================
            // 6. OLAH DATA CABANG/KANKAS & FILTER NULL
            // =========================================================
            foreach ($rows as $r) {
                $kd = str_pad($r['kode_kantor'] ?? '', 3, '0', STR_PAD_LEFT);
                
                // Rapikan nama & tangkal "NULL"
                $raw_nama = trim($r['nama_cabang'] ?? '');
                if ($raw_nama === '' || strtoupper($raw_nama) === 'NULL') {
                    $final_nama_cabang = 'KAS CABANG ' . $kd;
                } else {
                    $final_nama_cabang = str_replace('Kc. ', 'Cab. ', $raw_nama);
                }
                
                $saldo_prev = (float) $r['saldo_prev'];
                $saldo_curr = (float) $r['saldo_curr'];
                $delta      = $saldo_curr - $saldo_prev;
                
                $saldo_baru = (float) $r['saldo_baru'];
                $saldo_cair = (float) $r['saldo_cair'];

                $noa_curr   = (int) $r['noa_curr'];
                $noa_tambah = (int) $r['noa_tambah'];
                $noa_kurang = (int) $r['noa_kurang'];

                // Atur Korwil berdasarkan kode cabang asalnya jika tidak kosong
                $korwil = '';
                if ($displayMode === 'CABANG') {
                    $kd_cab = str_pad($kode_kantor, 3, '0', STR_PAD_LEFT);
                } else {
                    $kd_cab = substr($kd, 0, 3);
                }

                if ($kd_cab >= '001' && $kd_cab <= '007') $korwil = 'SEMARANG';
                elseif ($kd_cab >= '008' && $kd_cab <= '014') $korwil = 'SOLO';
                elseif ($kd_cab >= '015' && $kd_cab <= '021') $korwil = 'BANYUMAS';
                elseif ($kd_cab >= '022' && $kd_cab <= '028') $korwil = 'PEKALONGAN';

                // Akumulasikan ke Korwil
                if ($korwil !== '') {
                    $korwil_data[$korwil]['noa_curr']    += $noa_curr;
                    $korwil_data[$korwil]['noa_tambah']  += $noa_tambah;
                    $korwil_data[$korwil]['noa_kurang']  += $noa_kurang;
                    $korwil_data[$korwil]['saldo_prev']  += $saldo_prev;
                    $korwil_data[$korwil]['saldo_curr']  += $saldo_curr;
                    $korwil_data[$korwil]['delta_saldo'] += $delta;
                    $korwil_data[$korwil]['saldo_baru']  += $saldo_baru;
                    $korwil_data[$korwil]['saldo_cair']  += $saldo_cair;
                }

                // Akumulasikan ke Grand Total
                $grand_total['noa_curr']    += $noa_curr;
                $grand_total['noa_tambah']  += $noa_tambah;
                $grand_total['noa_kurang']  += $noa_kurang;
                $grand_total['saldo_prev']  += $saldo_prev;
                $grand_total['saldo_curr']  += $saldo_curr;
                $grand_total['delta_saldo'] += $delta;
                $grand_total['saldo_baru']  += $saldo_baru;
                $grand_total['saldo_cair']  += $saldo_cair;

                $cabang_array[] = [
                    'kode_cabang' => $kd,
                    'nama_cabang' => $final_nama_cabang,
                    'noa_curr'    => $noa_curr,
                    'noa_tambah'  => $noa_tambah,
                    'noa_kurang'  => $noa_kurang,
                    'saldo_prev'  => $saldo_prev,
                    'saldo_curr'  => $saldo_curr,
                    'delta_saldo' => $delta,
                    'saldo_baru'  => $saldo_baru,
                    'saldo_cair'  => $saldo_cair
                ];
            }

            // =========================================================
            // 7. SORTING CABANG/KANKAS (TAMPIL SEMUA JIKA MODE CABANG)
            // =========================================================
            
            // -- A. KELOLAAN (Saldo Curr) --
            usort($cabang_array, function($a, $b) { return $b['saldo_curr'] <=> $a['saldo_curr']; });
            $top_saldo = array_slice($cabang_array, 0, 5);
            
            if ($displayMode === 'CABANG') {
                $bottom_saldo = array_slice($cabang_array, 5); // Tampilkan sisanya di bottom
                usort($bottom_saldo, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; }); // ASC (Terkecil di atas)
            } else {
                $temp_saldo = $cabang_array;
                usort($temp_saldo, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
                $bottom_saldo = array_slice($temp_saldo, 0, 5); // Ambil 5 terkecil
            }

            // -- B. GROWTH (Kenaikan & Penurunan) --
            usort($cabang_array, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_kenaikan = array_slice($cabang_array, 0, 5); // 5 Tertinggi

            if ($displayMode === 'CABANG') {
                $top_penurunan = array_slice($cabang_array, 5); // Sisa Kankas masuk ke Penurunan
                usort($top_penurunan, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; }); // ASC (Paling minus di bawah)
            } else {
                $temp_growth = $cabang_array;
                usort($temp_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; });
                $top_penurunan = array_slice($temp_growth, 0, 5); // Ambil 5 terendah
            }

            // -- C. DEPOSITO BARU & PENCAIRAN --
            $baru = array_filter($cabang_array, function($c) { return $c['saldo_baru'] > 0; });
            usort($baru, function($a, $b) { return $b['saldo_baru'] <=> $a['saldo_baru']; });
            $top_baru = array_slice($baru, 0, 5);

            $cair = array_filter($cabang_array, function($c) { return $c['saldo_cair'] > 0; });
            usort($cair, function($a, $b) { return $b['saldo_cair'] <=> $a['saldo_cair']; });
            $top_cair = array_slice($cair, 0, 5);


            // =========================================================
            // 8. OLAH DATA KINERJA AO DANA (TOP & BOTTOM)
            // =========================================================
            foreach ($ao_rows as &$ao) {
                if (empty(trim($ao['nama_ao'] ?? ''))) $ao['nama_ao'] = 'AO ' . $ao['kode_group2'];
                $ao['saldo_prev']  = (float) ($ao['saldo_prev'] ?? 0);
                $ao['saldo_curr']  = (float) ($ao['saldo_curr'] ?? 0);
                $ao['delta_saldo'] = $ao['saldo_curr'] - $ao['saldo_prev'];
            }

            // -- GROWTH AO --
            usort($ao_rows, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_ao_growth = array_slice($ao_rows, 0, 5);
            
            if ($displayMode === 'CABANG') {
                $bottom_ao_growth = array_slice($ao_rows, 5); // Tampilkan semua sisa AO
                usort($bottom_ao_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; }); // ASC
            } else {
                $sisa_ao_growth = array_slice($ao_rows, 5);
                usort($sisa_ao_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; });
                $bottom_ao_growth = array_slice($sisa_ao_growth, 0, 5);
            }

            // -- KELOLAAN AO --
            usort($ao_rows, function($a, $b) { return $b['saldo_curr'] <=> $a['saldo_curr']; });
            $top_ao_kelolaan = array_slice($ao_rows, 0, 5);

            if ($displayMode === 'CABANG') {
                $bottom_ao_kelolaan = array_slice($ao_rows, 5); // Tampilkan semua sisa AO
                usort($bottom_ao_kelolaan, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; }); // ASC
            } else {
                $sisa_ao_kelolaan = array_slice($ao_rows, 5);
                usort($sisa_ao_kelolaan, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
                $bottom_ao_kelolaan = array_slice($sisa_ao_kelolaan, 0, 5);
            }


            // 9. KEMBALIKAN PAYLOAD
            return [
                'per_korwil'         => array_values($korwil_data),
                'grand_total'        => $grand_total,
                
                // Kinerja Cabang / Kankas
                'top_saldo'          => $top_saldo,
                'bottom_saldo'       => $bottom_saldo,
                'top_kenaikan'       => $top_kenaikan,
                'top_penurunan'      => $top_penurunan,
                'top_baru'           => $top_baru,
                'top_pencairan'      => $top_cair,
                'detail_cabang'      => $cabang_array, // Array mentah untuk FE
                
                // Kinerja AO Dana
                'top_ao_growth'      => $top_ao_growth,
                'bottom_ao_growth'   => $bottom_ao_growth,
                'top_ao_kelolaan'    => $top_ao_kelolaan,
                'bottom_ao_kelolaan' => $bottom_ao_kelolaan
            ];

        } catch (\Exception $e) { // <-- Berubah jadi Global Exception
            return [
                'ERROR_DETEKSI' => 'Terjadi masalah di sistem Deposito!',
                'pesan_error'   => $e->getMessage(),
                'file_error'    => $e->getFile(),
                'baris_error'   => $e->getLine()
            ];
        }
    }

    public function getPerkembanganTabunganlalu($input) {
        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');

        // 1. Panggil helper filter (beri alias 'nt' untuk nominatif_tabungan)
        $filter = $this->buildFilterQuery($input, 'nt');
        
        // 2. Trik sakti: Ganti 'kode_cabang' jadi 'kode_kantor' khusus untuk tabel ini
        $filter['sql'] = str_replace('nt.kode_cabang', 'nt.kode_kantor', $filter['sql']);

        // Query CTE Ultimate: Menghitung mutasi, saldo baru, saldo cair untuk TABUNGAN
        $sql = "
            WITH rekap_rek AS (
                SELECT 
                    no_rekening,
                    MAX(kode_kantor) AS kode_kantor, 
                    SUM(CASE WHEN created = :closing_date_1 THEN 1 ELSE 0 END) AS is_prev,
                    SUM(CASE WHEN created = :harian_date_1 THEN 1 ELSE 0 END) AS is_curr,
                    -- Perhatikan: Pakai kolom 'saldo' sesuai screenshot, bukan 'saldo_akhir'
                    SUM(CASE WHEN created = :closing_date_2 THEN saldo ELSE 0 END) AS saldo_prev,
                    SUM(CASE WHEN created = :harian_date_2 THEN saldo ELSE 0 END) AS saldo_curr
                FROM nominatif_tabungan nt
                WHERE created IN (:closing_date_3, :harian_date_3)
                {$filter['sql']}
                GROUP BY no_rekening
            )
            SELECT 
                r.kode_kantor,
                COALESCE(k.nama_kantor, CONCAT('CABANG ', r.kode_kantor)) AS nama_cabang,
                
                SUM(CASE WHEN r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_curr, 
                SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_tambah,
                SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN 1 ELSE 0 END) AS noa_kurang,
                
                SUM(r.saldo_prev) AS saldo_prev,
                SUM(r.saldo_curr) AS saldo_curr,
                
                -- Hitung Saldo Uang Segar (Rekening Baru) dan Saldo Kabur (Tutup Rekening)
                SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN r.saldo_curr ELSE 0 END) AS saldo_baru,
                SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN r.saldo_prev ELSE 0 END) AS saldo_cair
                
            FROM rekap_rek r
            LEFT JOIN kode_kantor k ON r.kode_kantor = k.kode_kantor
            GROUP BY r.kode_kantor, k.nama_kantor
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            $stmt->bindValue(':closing_date_3', $closing_date);
            
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            $stmt->bindValue(':harian_date_3', $harian_date);
            
            // 3. Bind parameter filternya (jika ada)
            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Siapkan Wadah untuk 4 Korwil Saja
            $korwil_data = [];
            $korwil_list = ['SEMARANG', 'SOLO', 'BANYUMAS', 'PEKALONGAN'];
            foreach ($korwil_list as $kw) {
                $korwil_data[$kw] = [
                    'nama_korwil' => $kw, 'noa_curr' => 0, 'noa_tambah' => 0, 'noa_kurang' => 0, 
                    'saldo_prev' => 0, 'saldo_curr' => 0, 'delta_saldo' => 0, 'saldo_baru' => 0, 'saldo_cair' => 0
                ];
            }

            $grand_total = [
                'nama_korwil' => 'TOTAL KONSOLIDASI', 'noa_curr' => 0, 'noa_tambah' => 0, 'noa_kurang' => 0, 
                'saldo_prev' => 0, 'saldo_curr' => 0, 'delta_saldo' => 0, 'saldo_baru' => 0, 'saldo_cair' => 0
            ];
            $cabang_array = [];

            // 5. Olah Data dari Database
            foreach ($rows as $r) {
                $kd = str_pad($r['kode_kantor'], 3, '0', STR_PAD_LEFT);
                $saldo_prev = (float) $r['saldo_prev'];
                $saldo_curr = (float) $r['saldo_curr'];
                $delta      = $saldo_curr - $saldo_prev;
                
                $saldo_baru = (float) $r['saldo_baru'];
                $saldo_cair = (float) $r['saldo_cair'];

                $noa_curr   = (int) $r['noa_curr'];
                $noa_tambah = (int) $r['noa_tambah'];
                $noa_kurang = (int) $r['noa_kurang'];

                // Mapping Korwil
                $korwil = '';
                if ($kd >= '001' && $kd <= '007') $korwil = 'SEMARANG';
                elseif ($kd >= '008' && $kd <= '014') $korwil = 'SOLO';
                elseif ($kd >= '015' && $kd <= '021') $korwil = 'BANYUMAS';
                elseif ($kd >= '022' && $kd <= '028') $korwil = 'PEKALONGAN';

                // Tambah ke Korwil (Hanya kalau masuk 4 korwil utama)
                if ($korwil !== '') {
                    $korwil_data[$korwil]['noa_curr']    += $noa_curr;
                    $korwil_data[$korwil]['noa_tambah']  += $noa_tambah;
                    $korwil_data[$korwil]['noa_kurang']  += $noa_kurang;
                    $korwil_data[$korwil]['saldo_prev']  += $saldo_prev;
                    $korwil_data[$korwil]['saldo_curr']  += $saldo_curr;
                    $korwil_data[$korwil]['delta_saldo'] += $delta;
                    $korwil_data[$korwil]['saldo_baru']  += $saldo_baru;
                    $korwil_data[$korwil]['saldo_cair']  += $saldo_cair;
                }

                // Tambah ke Grand Total
                $grand_total['noa_curr']    += $noa_curr;
                $grand_total['noa_tambah']  += $noa_tambah;
                $grand_total['noa_kurang']  += $noa_kurang;
                $grand_total['saldo_prev']  += $saldo_prev;
                $grand_total['saldo_curr']  += $saldo_curr;
                $grand_total['delta_saldo'] += $delta;
                $grand_total['saldo_baru']  += $saldo_baru;
                $grand_total['saldo_cair']  += $saldo_cair;

                // 🔥 FIX: Tambahkan 'noa_curr' ke array Cabang biar muncul di Front-End
                $cabang_array[] = [
                    'kode_cabang' => $kd,
                    'nama_cabang' => $r['nama_cabang'],
                    'noa_curr'    => $noa_curr,      // <--- INI BIANG KEROKNYA
                    'noa_tambah'  => $noa_tambah,
                    'noa_kurang'  => $noa_kurang,
                    'saldo_prev'  => $saldo_prev,
                    'saldo_curr'  => $saldo_curr,
                    'delta_saldo' => $delta,
                    'saldo_baru'  => $saldo_baru,
                    'saldo_cair'  => $saldo_cair
                ];
            }

            // 6. Eksekusi Kategori Sortir

            $kenaikan = array_filter($cabang_array, function($c) { return $c['delta_saldo'] > 0; });
            usort($kenaikan, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_kenaikan = array_slice($kenaikan, 0, 5);

            $penurunan = array_filter($cabang_array, function($c) { return $c['delta_saldo'] < 0; });
            usort($penurunan, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; }); 
            $top_penurunan = array_slice($penurunan, 0, 5);

            $baru = array_filter($cabang_array, function($c) { return $c['saldo_baru'] > 0; });
            usort($baru, function($a, $b) { return $b['saldo_baru'] <=> $a['saldo_baru']; });
            $top_baru = array_slice($baru, 0, 5);

            $cair = array_filter($cabang_array, function($c) { return $c['saldo_cair'] > 0; });
            usort($cair, function($a, $b) { return $b['saldo_cair'] <=> $a['saldo_cair']; });
            $top_cair = array_slice($cair, 0, 5);

            $saldo_aktif = array_filter($cabang_array, function($c) { return $c['saldo_curr'] > 0; });
            
            usort($saldo_aktif, function($a, $b) { return $b['saldo_curr'] <=> $a['saldo_curr']; });
            $top_saldo = array_slice($saldo_aktif, 0, 5);

            usort($saldo_aktif, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
            $bottom_saldo = array_slice($saldo_aktif, 0, 5);

            return [
                'per_korwil'    => array_values($korwil_data),
                'grand_total'   => $grand_total,
                'top_saldo'     => $top_saldo,
                'bottom_saldo'  => $bottom_saldo,
                'top_kenaikan'  => $top_kenaikan,
                'top_penurunan' => $top_penurunan,
                'top_baru'      => $top_baru,
                'top_pencairan' => $top_cair
            ];

        } catch (PDOException $e) {
            error_log("Error getPerkembanganTabungan: " . $e->getMessage());
            return [];
        }
    }

    public function getPerkembanganTabungan($input) {
        // --- 1. SUNTIK TENAGA SERVER (BEBAS TIME OUT) ---
        set_time_limit(0); 
        ini_set('memory_limit', '2048M'); 

        $closing_date = $input['closing_date'] ?? date('Y-m-d', strtotime('last day of previous month'));
        $harian_date  = $input['harian_date']  ?? date('Y-m-d');

        $kode_kantor = $input['kode_kantor'] ?? '000';

        // 2. Panggil helper filter (beri alias 'nt' untuk nominatif_tabungan)
        $filter = $this->buildFilterQuery($input, 'nt');
        
        // 3. Ganti 'kode_cabang' jadi 'kode_kantor' khusus untuk tabel ini
        $filter['sql'] = str_replace('nt.kode_cabang', 'nt.kode_kantor', $filter['sql']);

        $displayMode = 'PUSAT';
        $filterSql_cabang = "";

        if ($kode_kantor !== '000') {
            $displayMode = 'CABANG';
            $filterSql_cabang = " AND nt.kode_kantor = :kode_kantor_master"; 
        }

        // =========================================================
        // 4. KONDISIONAL QUERY UTAMA (BREAKDOWN KANKAS VS PUSAT)
        // =========================================================
        if ($displayMode === 'CABANG') {
            // MODE CABANG: Breakdown ke Kankas (Sudah kebal teks "NULL")
            $sql_main = "
                WITH rekap_rek AS (
                    SELECT 
                        no_rekening,
                        MAX(kode_kantor) AS kode_kantor,
                        MAX(nama_kankas) AS nama_kankas, 
                        SUM(CASE WHEN created = :closing_date_1 THEN 1 ELSE 0 END) AS is_prev,
                        SUM(CASE WHEN created = :harian_date_1 THEN 1 ELSE 0 END) AS is_curr,
                        SUM(CASE WHEN created = :closing_date_2 THEN saldo ELSE 0 END) AS saldo_prev,
                        SUM(CASE WHEN created = :harian_date_2 THEN saldo ELSE 0 END) AS saldo_curr
                    FROM nominatif_tabungan nt
                    WHERE created IN (:closing_date_3, :harian_date_3)
                    {$filter['sql']} {$filterSql_cabang}
                    GROUP BY no_rekening
                )
                SELECT 
                    -- Jika kosong/NULL, fallback ke kode_kantor + '000' (Misal: 004000)
                    COALESCE(k.kode_group1, CONCAT(r.kode_kantor, '000')) AS kode_cabang,
                    
                   
                    COALESCE(
                        NULLIF(NULLIF(TRIM(r.nama_kankas), ''), 'NULL'), 
                        k.deskripsi_group1, 
                        (SELECT nama_kantor FROM kode_kantor WHERE kode_kantor = r.kode_kantor LIMIT 1),
                        CONCAT('CABANG ', r.kode_kantor)
                    ) AS nama_cabang, 
                    
                    SUM(CASE WHEN r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_curr, 
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_tambah,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN 1 ELSE 0 END) AS noa_kurang,
                    SUM(r.saldo_prev) AS saldo_prev,
                    SUM(r.saldo_curr) AS saldo_curr,
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN r.saldo_curr ELSE 0 END) AS saldo_baru,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN r.saldo_prev ELSE 0 END) AS saldo_cair
                FROM rekap_rek r
                LEFT JOIN kankas k 
                    ON r.nama_kankas IS NOT NULL 
                    AND TRIM(r.nama_kankas) != '' 
                    AND TRIM(r.nama_kankas) != 'NULL'
                    AND TRIM(r.nama_kankas) = TRIM(k.deskripsi_group1)
                    AND k.kode_kantor = r.kode_kantor
                GROUP BY kode_cabang, nama_cabang
            ";
        } else {
            // MODE PUSAT: Konsolidasi per Cabang
            $sql_main = "
                WITH rekap_rek AS (
                    SELECT 
                        no_rekening,
                        MAX(kode_kantor) AS kode_target,
                        SUM(CASE WHEN created = :closing_date_1 THEN 1 ELSE 0 END) AS is_prev,
                        SUM(CASE WHEN created = :harian_date_1 THEN 1 ELSE 0 END) AS is_curr,
                        SUM(CASE WHEN created = :closing_date_2 THEN saldo ELSE 0 END) AS saldo_prev,
                        SUM(CASE WHEN created = :harian_date_2 THEN saldo ELSE 0 END) AS saldo_curr
                    FROM nominatif_tabungan nt
                    WHERE created IN (:closing_date_3, :harian_date_3)
                    {$filter['sql']} {$filterSql_cabang}
                    GROUP BY no_rekening
                )
                SELECT 
                    r.kode_target AS kode_cabang,
                    COALESCE((SELECT nama_kantor FROM nominatif_tabungan WHERE kode_kantor = r.kode_target ORDER BY created DESC LIMIT 1), CONCAT('CABANG ', r.kode_target)) AS nama_cabang,
                    SUM(CASE WHEN r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_curr, 
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN 1 ELSE 0 END) AS noa_tambah,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN 1 ELSE 0 END) AS noa_kurang,
                    SUM(r.saldo_prev) AS saldo_prev,
                    SUM(r.saldo_curr) AS saldo_curr,
                    SUM(CASE WHEN r.is_prev = 0 AND r.is_curr > 0 THEN r.saldo_curr ELSE 0 END) AS saldo_baru,
                    SUM(CASE WHEN r.is_prev > 0 AND r.is_curr = 0 THEN r.saldo_prev ELSE 0 END) AS saldo_cair
                FROM rekap_rek r
                GROUP BY r.kode_target
            ";
        }

        // =========================================================
        // 5. QUERY KHUSUS UNTUK KINERJA AO
        // =========================================================
        $sql_ao = "
            SELECT 
                nama_ao,
                SUM(CASE WHEN created = :closing_date_2 THEN saldo ELSE 0 END) AS saldo_prev,
                SUM(CASE WHEN created = :harian_date_2 THEN saldo ELSE 0 END) AS saldo_curr
            FROM nominatif_tabungan nt
            WHERE created IN (:closing_date_3, :harian_date_3)
            AND nama_ao IS NOT NULL AND TRIM(nama_ao) != ''
            {$filter['sql']} {$filterSql_cabang}
            GROUP BY nama_ao
        ";

        try {
            // --- EKSEKUSI QUERY UTAMA ---
            $stmt = $this->pdo->prepare($sql_main);
            $stmt->bindValue(':closing_date_1', $closing_date);
            $stmt->bindValue(':closing_date_2', $closing_date);
            $stmt->bindValue(':closing_date_3', $closing_date);
            $stmt->bindValue(':harian_date_1', $harian_date);
            $stmt->bindValue(':harian_date_2', $harian_date);
            $stmt->bindValue(':harian_date_3', $harian_date);
            
            if ($displayMode === 'CABANG') {
                $stmt->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- EKSEKUSI QUERY AO ---
            $stmt_ao = $this->pdo->prepare($sql_ao);
            $stmt_ao->bindValue(':closing_date_2', $closing_date);
            $stmt_ao->bindValue(':closing_date_3', $closing_date);
            $stmt_ao->bindValue(':harian_date_2', $harian_date);
            $stmt_ao->bindValue(':harian_date_3', $harian_date);
            
            if ($displayMode === 'CABANG') {
                $stmt_ao->bindValue(':kode_kantor_master', $kode_kantor);
            }

            foreach ($filter['params'] as $key => $val) {
                $stmt_ao->bindValue($key, $val);
            }
            $stmt_ao->execute();
            $ao_rows = $stmt_ao->fetchAll(PDO::FETCH_ASSOC);

            $cabang_array = [];
            
            // --- WADAH GRAND TOTAL ---
            $grand_total = [
                'nama_cabang' => 'TOTAL KESELURUHAN',
                'noa_curr'    => 0,
                'noa_tambah'  => 0,
                'noa_kurang'  => 0,
                'delta_noa'   => 0,
                'saldo_prev'  => 0,
                'saldo_curr'  => 0,
                'delta_saldo' => 0,
                'saldo_baru'  => 0,
                'saldo_cair'  => 0
            ];

            // 6. Olah Data & Akumulasi Total
            foreach ($rows as $r) {
                $kd = str_pad($r['kode_cabang'] ?? '000', 3, '0', STR_PAD_LEFT);
                
                // Rapikan nama (Ubah "Kc." jadi "Cab." biar elegan)
                $raw_nama = trim($r['nama_cabang'] ?? '');
                $final_nama_cabang = str_replace('Kc. ', 'Cab. ', $raw_nama);

                $saldo_prev = (float) $r['saldo_prev'];
                $saldo_curr = (float) $r['saldo_curr'];
                $delta      = $saldo_curr - $saldo_prev;
                
                $saldo_baru = (float) $r['saldo_baru'];
                $saldo_cair = (float) $r['saldo_cair'];

                $noa_curr   = (int) $r['noa_curr'];
                $noa_tambah = (int) $r['noa_tambah'];
                $noa_kurang = (int) $r['noa_kurang'];
                $delta_noa  = $noa_tambah - $noa_kurang; // Net Penambahan NOA

                // Masukkan ke Grand Total
                $grand_total['noa_curr']    += $noa_curr;
                $grand_total['noa_tambah']  += $noa_tambah;
                $grand_total['noa_kurang']  += $noa_kurang;
                $grand_total['delta_noa']   += $delta_noa;
                $grand_total['saldo_prev']  += $saldo_prev;
                $grand_total['saldo_curr']  += $saldo_curr;
                $grand_total['delta_saldo'] += $delta;
                $grand_total['saldo_baru']  += $saldo_baru;
                $grand_total['saldo_cair']  += $saldo_cair;

                $cabang_array[] = [
                    'kode_cabang' => $kd,
                    'nama_cabang' => $final_nama_cabang, 
                    'noa_curr'    => $noa_curr,
                    'noa_tambah'  => $noa_tambah,
                    'noa_kurang'  => $noa_kurang,
                    'delta_noa'   => $delta_noa,
                    'saldo_prev'  => $saldo_prev,
                    'saldo_curr'  => $saldo_curr,
                    'delta_saldo' => $delta,
                    'saldo_baru'  => $saldo_baru,
                    'saldo_cair'  => $saldo_cair
                ];
            }

            // =========================================================
            // 7. SORTING KHUSUS 
            // =========================================================
            
            // --- A. Kelolaan (Saldo Curr) ---
            usort($cabang_array, function($a, $b) { return $b['saldo_curr'] <=> $a['saldo_curr']; });
            $top_kelolaan = array_slice($cabang_array, 0, 5);

            if ($displayMode === 'CABANG') {
                $bottom_saldo = array_slice($cabang_array, 5);
                usort($bottom_saldo, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; }); // ASC
            } else {
                $temp_saldo = $cabang_array;
                usort($temp_saldo, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
                $bottom_saldo = array_slice($temp_saldo, 0, 5);
            }

            // --- B. Growth (Delta Saldo) ---
            usort($cabang_array, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_growth = array_slice($cabang_array, 0, 5);

            if ($displayMode === 'CABANG') {
                $top_penurunan = array_slice($cabang_array, 5);
                usort($top_penurunan, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; }); // ASC
            } else {
                $temp_growth = $cabang_array;
                usort($temp_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; });
                $top_penurunan = array_slice($temp_growth, 0, 5);
            }

            // --- C. Penambahan NOA ---
            usort($cabang_array, function($a, $b) { return $b['delta_noa'] <=> $a['delta_noa']; });
            $top_penambahan_noa = array_slice($cabang_array, 0, 5);
            
            // --- D. Tabungan Baru Masuk (DIHIDUPKAN KEMBALI) ---
            $baru = array_filter($cabang_array, function($c) { return $c['saldo_baru'] > 0; });
            usort($baru, function($a, $b) { return $b['saldo_baru'] <=> $a['saldo_baru']; });
            $top_baru = array_slice($baru, 0, 5);

            // --- E. Sortir Bawaan Lainnya ---
            $kenaikan = array_filter($cabang_array, function($c) { return $c['delta_saldo'] > 0; });
            usort($kenaikan, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_kenaikan = array_slice($kenaikan, 0, 5);

            $cair = array_filter($cabang_array, function($c) { return $c['saldo_cair'] > 0; });
            usort($cair, function($a, $b) { return $b['saldo_cair'] <=> $a['saldo_cair']; });
            $top_cair = array_slice($cair, 0, 5);


            // =========================================================
            // 8. OLAH DATA KINERJA AO (TOP & BOTTOM)
            // =========================================================
            foreach ($ao_rows as &$ao) {
                $ao['saldo_prev']  = (float) ($ao['saldo_prev'] ?? 0);
                $ao['saldo_curr']  = (float) ($ao['saldo_curr'] ?? 0);
                $ao['delta_saldo'] = $ao['saldo_curr'] - $ao['saldo_prev'];
            }

            usort($ao_rows, function($a, $b) { return $b['delta_saldo'] <=> $a['delta_saldo']; });
            $top_ao_growth = array_slice($ao_rows, 0, 5);
            
            if ($displayMode === 'CABANG') {
                $bottom_ao_growth = array_slice($ao_rows, 5);
                usort($bottom_ao_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; });
            } else {
                $sisa_ao_growth = array_slice($ao_rows, 5);
                usort($sisa_ao_growth, function($a, $b) { return $a['delta_saldo'] <=> $b['delta_saldo']; });
                $bottom_ao_growth = array_slice($sisa_ao_growth, 0, 5);
            }

            usort($ao_rows, function($a, $b) { return $b['saldo_curr'] <=> $a['saldo_curr']; });
            $top_ao_kelolaan = array_slice($ao_rows, 0, 5);

            if ($displayMode === 'CABANG') {
                $bottom_ao_kelolaan = array_slice($ao_rows, 5);
                usort($bottom_ao_kelolaan, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
            } else {
                $sisa_ao_kelolaan = array_slice($ao_rows, 5);
                usort($sisa_ao_kelolaan, function($a, $b) { return $a['saldo_curr'] <=> $b['saldo_curr']; });
                $bottom_ao_kelolaan = array_slice($sisa_ao_kelolaan, 0, 5);
            }

            // 9. Kembalikan data dengan payload bersih (Standar FE Bapak)
            return [
                'per_korwil'         => [], // Kosongan agar FE tidak undefined error
                'grand_total'        => $grand_total, 
                'detail_cabang'      => $cabang_array,
                
                // Sorting Dashboard Tabungan
                'top_saldo'          => $top_kelolaan,
                'bottom_saldo'       => $bottom_saldo,
                'top_kenaikan'       => $top_kenaikan,
                'top_penurunan'      => $top_penurunan,
                'top_baru'           => $top_baru,       // <-- AKHIRNYA MUNCUL!
                'top_pencairan'      => $top_cair,
                
                // Variabel extra jaga-jaga
                'top_kelolaan'       => $top_kelolaan,
                'top_growth'         => $top_growth,
                'top_penambahan_noa' => $top_penambahan_noa,
                
                // Kinerja AO
                'top_ao_growth'      => $top_ao_growth,
                'bottom_ao_growth'   => $bottom_ao_growth,
                'top_ao_kelolaan'    => $top_ao_kelolaan,
                'bottom_ao_kelolaan' => $bottom_ao_kelolaan
            ];

        } catch (\Exception $e) { 
            return [
                'ERROR_DETEKSI' => 'Terjadi masalah di sistem!',
                'pesan_error'   => $e->getMessage(),
                'file_error'    => $e->getFile(),
                'baris_error'   => $e->getLine()
            ];
        }
    }






}
