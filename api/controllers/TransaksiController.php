<?php

require_once __DIR__ . '/../helpers/response.php';


class TransaksiController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

 
    /**
     * 8. REKAP TRANSAKSI DIGITAL (VA, Branchless, QRIS)
     */
    public function getRekapTransaksiChannel($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        
        $channel = !empty($b['channel']) ? strtoupper($b['channel']) : 'ALL';

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        // 🔥 FIX: Bisa Custom Closing Date dari Input. Jika kosong, auto-hitung akhir bulan kemarin.
        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $ts_harian = strtotime($harian);
            $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
        }

        // --- 1. BUILD FILTER QUERY ---
        $sqlFilter = "";
        $params = [
            ':harian'  => $harian,
            ':closing' => $closing_date
        ];

        // Filter Cabang / Korwil
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
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
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        // Filter Kankas
        if ($kankas) {
            $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // Filter Transaksi per Channel
        $chanFilter = "";
        if ($channel === 'VA') {
            $chanFilter = " AND TRIM(t.kode_transaksi) = '320' ";
        } elseif ($channel === 'BRANCHLESS') {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('150', '152') ";
        } elseif ($channel === 'QRIS') {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('140', '16', '162') ";
        } else {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('320', '150', '152', '140', '16', '162') ";
        }

        // --- 2. MAIN QUERY ---
        $sql = "SELECT 
                    CASE 
                        WHEN TRIM(t.kode_transaksi) = '320' THEN 'Virtual Account (VA)'
                        WHEN TRIM(t.kode_transaksi) IN ('150', '152') THEN 'Branchless Banking'
                        WHEN TRIM(t.kode_transaksi) IN ('140', '16', '162') THEN 'QRIS'
                    END as kategori_trx,
                    CASE 
                        WHEN TRIM(t.kode_transaksi) = '320' THEN 1
                        WHEN TRIM(t.kode_transaksi) IN ('150', '152') THEN 2
                        WHEN TRIM(t.kode_transaksi) IN ('140', '16', '162') THEN 3
                    END as sort_order,
                    COUNT(1) as total_frekuensi,
                    SUM(t.jumlah) as total_nominal,
                    SUM(COALESCE(t.adm, 0)) as total_adm
                FROM va t 
                WHERE t.tgl_transaksi > :closing AND t.tgl_transaksi <= :harian
                $sqlFilter $chanFilter
                GROUP BY kategori_trx, sort_order
                ORDER BY sort_order ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Dinamis Filter Nama
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

            $grandTotal = ['total_frekuensi' => 0, 'total_nominal' => 0, 'total_adm' => 0];

            foreach ($rows as &$r) {
                $r['total_frekuensi'] = (int)$r['total_frekuensi'];
                $r['total_nominal']   = (float)$r['total_nominal'];
                $r['total_adm']       = (float)$r['total_adm'];

                $grandTotal['total_frekuensi'] += $r['total_frekuensi'];
                $grandTotal['total_nominal']   += $r['total_nominal'];
                $grandTotal['total_adm']       += $r['total_adm'];
            }

            return sendResponse(200, "Berhasil", [
                'meta' => [
                    'filter_aktif' => $nama_kantor_filter, 
                    'harian_date'  => $harian,
                    'closing_date' => $closing_date,
                    'channel_aktif'=> $channel
                ],
                'grand_total' => $grandTotal,
                'data' => $rows
            ]);
        } catch (PDOException $e) { 
            error_log("Error Rekap Transaksi: " . $e->getMessage());
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null); 
        }
    }

    /**
     * 9. TREN NOMINAL & TRX (Chart Line)
     * Filter Dinamis Berdasarkan Channel (VA, Branchless, QRIS)
     * Menampilkan 1 Garis Total Gabungan (Bebas Bug Bulan PHP)
     */
    public function getTrenNominalVa($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian_date = $b['harian_date'] ?? date('Y-m-d');
        $periode     = $b['periode'] ?? 'bulanan'; // 7_hari, 30_hari, bulanan, tahunan
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil      = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas      = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        
        $channel     = !empty($b['channel']) ? strtoupper($b['channel']) : 'VA';

        if (!$harian_date) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        $ts_harian = strtotime($harian_date);

        // --- 1. GENERATE PERIODE & TANGGAL (X-AXIS CHART) YANG FLEKSIBEL ---
        $keys = []; $labels = []; $startDate = ""; $endDate = ""; $sqlGroup = "";

        if ($periode === '7_hari') {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i day", $ts_harian));
                $keys[] = $d; $labels[] = date('d M', strtotime($d));
            }
            $startDate = $keys[0] . " 00:00:00"; 
            $endDate   = $harian_date . " 23:59:59"; 
            $sqlGroup  = "DATE(t.tgl_transaksi)";

        } elseif ($periode === '30_hari') {
            for ($i = 29; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i day", $ts_harian));
                $keys[] = $d; $labels[] = date('d M', strtotime($d));
            }
            $startDate = $keys[0] . " 00:00:00"; 
            $endDate   = $harian_date . " 23:59:59"; 
            $sqlGroup  = "DATE(t.tgl_transaksi)";

        } elseif ($periode === 'tahunan') {
            $startYear = 2020; 
            $currentYear = (int)date('Y', $ts_harian);
            for ($year = $startYear; $year <= $currentYear; $year++) {
                $keys[] = (string)$year; $labels[] = (string)$year;
            }
            $startDate = "2020-01-01 00:00:00"; 
            $endDate   = date('Y-12-31 23:59:59', $ts_harian); 
            $sqlGroup  = "YEAR(t.tgl_transaksi)";

        } else {
            // Default: bulanan (6 Bulan Terakhir dari harian_date)
            // 🔥 FIX BUG: Loop bulan mundur menggunakan matematika murni (anti-bug tgl 31 PHP)
            $y = (int)date('Y', $ts_harian);
            $m = (int)date('m', $ts_harian);

            for ($i = 5; $i >= 0; $i--) {
                $target_m = $m - $i;
                $target_y = $y;
                
                // Jika bulan <= 0, mundur tahunnya
                while ($target_m <= 0) {
                    $target_m += 12;
                    $target_y--;
                }
                
                $d = sprintf("%04d-%02d", $target_y, $target_m);
                $keys[] = $d; 
                $labels[] = date('M Y', strtotime($d . '-01'));
            }
            
            $startDate = $keys[0] . "-01 00:00:00"; 
            $endDate   = $harian_date . " 23:59:59"; 
            $sqlGroup  = "DATE_FORMAT(t.tgl_transaksi, '%Y-%m')";
        }

        // --- 2. BUILD FILTER QUERY ---
        $sqlFilter = "";
        $params = [
            ':start_date' => $startDate,
            ':end_date'   => $endDate
        ];

        // Filter Cabang / Korwil / Kankas
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
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
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        if ($kankas) {
            $sqlFilter .= " AND COALESCE(NULLIF(TRIM(t.kankas), ''), CONCAT(t.kantor, '000')) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // Filter Channel
        $chanFilter = "";
        if ($channel === 'BRANCHLESS') { 
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('150', '152') "; 
        } elseif ($channel === 'QRIS') { 
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('140', '16', '162') "; 
        } else { 
            $chanFilter = " AND TRIM(t.kode_transaksi) = '320' "; // VA default
        }

        // --- 3. MAIN QUERY ---
        $sql = "SELECT 
                    $sqlGroup as periode_key,
                    SUM(t.jumlah) as total_nominal,
                    COUNT(1) as total_trx,
                    COUNT(DISTINCT t.no_rekening) as total_noa
                FROM va t 
                WHERE t.tgl_transaksi >= :start_date AND t.tgl_transaksi <= :end_date
                $chanFilter
                $sqlFilter
                GROUP BY periode_key
                ORDER BY periode_key ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- 4. FORMAT DATA UNTUK CHART ---
            $dataNominal = array_fill(0, count($keys), 0);
            $dataTrx     = array_fill(0, count($keys), 0);
            $dataNoa     = array_fill(0, count($keys), 0);

            $keyIndex = array_flip($keys);

            $grandTotalNominal = 0;
            $grandTotalNoa = 0;

            foreach ($rows as $r) {
                $pkey = (string)$r['periode_key'];
                if (!isset($keyIndex[$pkey])) continue;
                
                $idx = $keyIndex[$pkey];
                $nominal = (float)$r['total_nominal'];
                $trx = (int)$r['total_trx'];
                $noa = (int)$r['total_noa'];

                $grandTotalNominal += $nominal;
                $grandTotalNoa += $noa;
                
                $dataNominal[$idx] = $nominal;
                $dataTrx[$idx]     = $trx;
                $dataNoa[$idx]     = $noa;
            }

            $seriesName = ($channel === 'VA') ? 'Virtual Account' : ($channel === 'BRANCHLESS' ? 'Branchless' : 'QRIS');
            if ($channel === 'ALL') $seriesName = 'Total Digital';

            $seriesNominal = [
                ['name' => $seriesName, 'data' => $dataNominal, 'trx' => $dataTrx] 
            ];
            
            $seriesNoa = [
                ['name' => $seriesName, 'data' => $dataNoa]
            ];

            return sendResponse(200, "Berhasil ambil tren", [
                'meta' => [
                    'periode_aktif'           => $periode,
                    'channel_aktif'           => $channel,
                    'total_akumulasi_nominal' => $grandTotalNominal,
                    'total_akumulasi_noa'     => $grandTotalNoa
                ],
                'chart_nominal' => [
                    'labels' => $labels,
                    'series' => $seriesNominal
                ],
                'chart_noa' => [
                    'labels' => $labels,
                    'series' => $seriesNoa
                ]
            ]);

        } catch (PDOException $e) { 
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null); 
        }
    }

    /**
     * 10. DISTRIBUSI & TOP 5 NOMINAL
     * Hierarki Dinamis & Filter Murni Berdasarkan Channel
     */
    public function getDistribusiVa($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        
        // 🔥 Ganti default jadi 'VA' (All Channel dihapus sesuai request)
        $channel = !empty($b['channel']) ? strtoupper($b['channel']) : 'VA';

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $ts_harian = strtotime($harian);
            $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
        }

        // --- 1. BUILD FILTER QUERY ---
        $sqlFilter = ""; $params = [':harian' => $harian, ':closing' => $closing_date];
        $mode_hirarki = 'KONSOLIDASI';

        // Filter Area
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor "; $params[':kode_kantor'] = $kode_kantor;
            $mode_hirarki = 'CABANG';
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start; $params[':kw_end'] = $kw_end;
            }
            $mode_hirarki = 'KORWIL';
        }
        if ($kankas) {
            $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas "; $params[':kode_kankas'] = $kankas;
            $mode_hirarki = 'KANKAS_SPECIFIC';
        }

        // 🔥 Filter Channel (Murni tanpa embel-embel Bank)
        $chanFilter = "";
        if ($channel === 'BRANCHLESS') { 
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('150', '152') "; 
        } elseif ($channel === 'QRIS') { 
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('140', '16', '162') "; 
        } else { 
            $chanFilter = " AND TRIM(t.kode_transaksi) = '320' "; // Default selalu ke VA
        }

        // --- 2. MAIN QUERY EFFICIENT ---
        $sql = "SELECT 
                    t.kantor, kk.nama_kantor, TRIM(t.kankas) as kankas, kn.deskripsi_group1 as nama_kankas,
                    SUM(t.jumlah) as total_nominal, COUNT(1) as total_trx, COUNT(DISTINCT t.no_rekening) as total_noa
                FROM va t
                LEFT JOIN kode_kantor kk ON t.kantor = kk.kode_kantor
                LEFT JOIN kankas kn ON TRIM(t.kankas) = TRIM(kn.kode_group1)
                WHERE t.tgl_transaksi > :closing AND t.tgl_transaksi <= :harian
                $chanFilter
                $sqlFilter
                GROUP BY t.kantor, kk.nama_kantor, kankas, kn.deskripsi_group1";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- 3. MAPPING DINAMIS DATA ---
            $aggCabang = []; $aggKankas = []; $aggKorwil = [];

            $getKorwil = function($cabang) {
                $c = (int)$cabang;
                if ($c >= 1 && $c <= 7) return 'KORWIL SEMARANG';
                if ($c >= 8 && $c <= 14) return 'KORWIL SOLO';
                if ($c >= 15 && $c <= 21) return 'KORWIL BANYUMAS';
                if ($c >= 22 && $c <= 28) return 'KORWIL PEKALONGAN';
                return 'PUSAT / LAINNYA';
            };

            foreach ($rows as $r) {
                $cab = $r['kantor'] ?? '000';
                $nama_cab = $r['nama_kantor'] ?: "Cabang " . $cab;
                $kan = $r['kankas'];
                $nama_kan = $r['nama_kankas'] ?: ($kan ? "Kankas " . $kan : "Pusat / Operasional");
                
                $nom = (float)$r['total_nominal'];
                $trx = (int)$r['total_trx'];
                $kw = $getKorwil($cab);

                if (!isset($aggCabang[$cab])) $aggCabang[$cab] = ['label' => $nama_cab, 'nominal' => 0, 'trx' => 0];
                $aggCabang[$cab]['nominal'] += $nom; $aggCabang[$cab]['trx'] += $trx;

                $kanKey = $cab . '_' . $kan;
                if (!isset($aggKankas[$kanKey])) $aggKankas[$kanKey] = ['label' => $nama_kan, 'nominal' => 0, 'trx' => 0];
                $aggKankas[$kanKey]['nominal'] += $nom; $aggKankas[$kanKey]['trx'] += $trx;

                if (!isset($aggKorwil[$kw])) $aggKorwil[$kw] = ['label' => $kw, 'nominal' => 0, 'trx' => 0];
                $aggKorwil[$kw]['nominal'] += $nom; $aggKorwil[$kw]['trx'] += $trx;
            }

            // --- 4. TENTUKAN SUMBER DATA SESUAI HIERARKI ---
            $sourceTop5 = []; $sourceDonut = [];
            if ($mode_hirarki === 'KONSOLIDASI') {
                $sourceTop5  = array_values($aggCabang);
                $sourceDonut = array_values($aggKorwil);
            } elseif ($mode_hirarki === 'KORWIL') {
                $sourceTop5  = array_values($aggCabang);
                $sourceDonut = array_values($aggCabang); 
            } else { 
                $sourceTop5  = array_values($aggKankas);
                $sourceDonut = array_values($aggKankas);
            }

            usort($sourceTop5, function($a, $b) { return $b['nominal'] <=> $a['nominal']; });
            $finalTop5 = array_slice($sourceTop5, 0, 5);
            usort($sourceDonut, function($a, $b) { return $b['nominal'] <=> $a['nominal']; });
            
            $donutLabels = []; $donutSeries = []; $donutTrx = [];
            foreach ($sourceDonut as $d) {
                if ($d['nominal'] > 0) { 
                    $donutLabels[] = $d['label'];
                    $donutSeries[] = $d['nominal'];
                    $donutTrx[]    = $d['trx']; 
                }
            }

            return sendResponse(200, "Berhasil ambil distribusi", [
                'meta' => [
                    'hierarki_aktif' => $mode_hirarki,
                    'channel_aktif'  => $channel,
                    'harian_date'    => $harian,
                    'closing_date'   => $closing_date
                ],
                'top_5' => $finalTop5,
                'donut_chart' => ['labels' => $donutLabels, 'series' => $donutSeries, 'trx' => $donutTrx]
            ]);

        } catch (PDOException $e) { 
            error_log("Error Distribusi: " . $e->getMessage());
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null); 
        }
    }

    /**
         * 11. SUMMARY CARDS DASHBOARD TRANSAKSI
         * Menampilkan semua data (Keseluruhan, VA, Branchless, QRIS, Mandiri, Permata).
         * Bebas Bug HY093 & Fix Bug Tanggal PHP strtotime('-1 month') pada tanggal 31.
         */
    public function getSummaryCardsTransaksi($input = null) {
            // 🔥 VAKSIN: Matikan print warning/notice HTML yang bisa bikin JSON rusak (Unexpected token '<')
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
            set_time_limit(300); ini_set('memory_limit', '1024M');

            $b = is_array($input) ? $input : [];
            $harian  = $b['harian_date'] ?? date('Y-m-d');
            $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
            $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;

            if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

            $ts_harian = strtotime($harian);
            $y = (int)date('Y', $ts_harian);
            $m = (int)date('m', $ts_harian);
            $d = (int)date('d', $ts_harian);

            // 🔥 LOGIKA TANGGAL BULLETPROOF MENGGUNAKAN mktime()
            $prev_month_ts = mktime(0, 0, 0, $m - 1, 1, $y);
            $prev_y = date('Y', $prev_month_ts);
            $prev_m = date('m', $prev_month_ts);
            $max_days = date('t', $prev_month_ts);
            
            $prev_d = min($d, $max_days);
            $prev_harian = sprintf("%04d-%02d-%02d", $prev_y, $prev_m, $prev_d);

            if (!empty($b['closing_date'])) {
                $closing_date = $b['closing_date'];
            } else {
                $closing_date = date('Y-m-t', strtotime(sprintf("%04d-%02d-01", $y, $m) . ' -1 day'));
            }
            
            $prev_closing = date('Y-m-t', strtotime(date('Y-m-01', strtotime($closing_date)) . ' -1 month'));

            $label_periode = date('M Y', $ts_harian);
            $prev_label_periode = date('M Y', strtotime($prev_harian));

            $sqlFilter = "";
            $params = [
                ':s_closing'  => $closing_date, ':s_harian'   => $harian,
                ':s_pclosing' => $prev_closing, ':s_pharian'  => $prev_harian,
                ':w_closing'  => $closing_date, ':w_harian'   => $harian,
                ':w_pclosing' => $prev_closing, ':w_pharian'  => $prev_harian
            ];

            if ($kode_kantor && $kode_kantor !== '000') {
                $sqlFilter .= " AND t.kantor = :kode_kantor "; $params[':kode_kantor'] = $kode_kantor;
            } elseif ($korwil) {
                $kw_start = null; $kw_end = null;
                switch ($korwil) {
                    case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                    case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                    case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                    case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
                }
                if ($kw_start && $kw_end) {
                    $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                    $params[':kw_start'] = $kw_start; $params[':kw_end'] = $kw_end;
                }
            }

            $sql = "
                SELECT 
                    SUM(CASE WHEN is_curr=1 THEN jumlah ELSE 0 END) as curr_nom_all,
                    SUM(CASE WHEN is_curr=1 THEN 1 ELSE 0 END) as curr_trx_all,
                    
                    SUM(CASE WHEN is_curr=1 AND is_va=1 THEN jumlah ELSE 0 END) as curr_nom_va,
                    SUM(CASE WHEN is_curr=1 AND is_va=1 THEN 1 ELSE 0 END) as curr_trx_va,
                    
                    SUM(CASE WHEN is_curr=1 AND is_mandiri=1 THEN jumlah ELSE 0 END) as curr_nom_mandiri,
                    SUM(CASE WHEN is_curr=1 AND is_mandiri=1 THEN 1 ELSE 0 END) as curr_trx_mandiri,
                    
                    SUM(CASE WHEN is_curr=1 AND is_permata=1 THEN jumlah ELSE 0 END) as curr_nom_permata,
                    SUM(CASE WHEN is_curr=1 AND is_permata=1 THEN 1 ELSE 0 END) as curr_trx_permata,
                    
                    SUM(CASE WHEN is_curr=1 AND is_br=1 THEN jumlah ELSE 0 END) as curr_nom_br,
                    SUM(CASE WHEN is_curr=1 AND is_br=1 THEN 1 ELSE 0 END) as curr_trx_br,

                    SUM(CASE WHEN is_curr=1 AND is_qris=1 THEN jumlah ELSE 0 END) as curr_nom_qris,
                    SUM(CASE WHEN is_curr=1 AND is_qris=1 THEN 1 ELSE 0 END) as curr_trx_qris,

                    SUM(CASE WHEN is_prev=1 THEN jumlah ELSE 0 END) as prev_nom_all,
                    SUM(CASE WHEN is_prev=1 THEN 1 ELSE 0 END) as prev_trx_all,

                    SUM(CASE WHEN is_prev=1 AND is_va=1 THEN jumlah ELSE 0 END) as prev_nom_va,
                    SUM(CASE WHEN is_prev=1 AND is_va=1 THEN 1 ELSE 0 END) as prev_trx_va,
                    
                    SUM(CASE WHEN is_prev=1 AND is_mandiri=1 THEN jumlah ELSE 0 END) as prev_nom_mandiri,
                    SUM(CASE WHEN is_prev=1 AND is_mandiri=1 THEN 1 ELSE 0 END) as prev_trx_mandiri, 
                    
                    SUM(CASE WHEN is_prev=1 AND is_permata=1 THEN jumlah ELSE 0 END) as prev_nom_permata,
                    SUM(CASE WHEN is_prev=1 AND is_permata=1 THEN 1 ELSE 0 END) as prev_trx_permata, 
                    
                    SUM(CASE WHEN is_prev=1 AND is_br=1 THEN jumlah ELSE 0 END) as prev_nom_br,
                    SUM(CASE WHEN is_prev=1 AND is_br=1 THEN 1 ELSE 0 END) as prev_trx_br,

                    SUM(CASE WHEN is_prev=1 AND is_qris=1 THEN jumlah ELSE 0 END) as prev_nom_qris,
                    SUM(CASE WHEN is_prev=1 AND is_qris=1 THEN 1 ELSE 0 END) as prev_trx_qris
                FROM (
                    SELECT 
                        t.jumlah,
                        CASE WHEN t.tgl_transaksi > :s_closing AND t.tgl_transaksi <= :s_harian THEN 1 ELSE 0 END as is_curr,
                        CASE WHEN t.tgl_transaksi > :s_pclosing AND t.tgl_transaksi <= :s_pharian THEN 1 ELSE 0 END as is_prev,
                        CASE WHEN TRIM(t.kode_transaksi) = '320' THEN 1 ELSE 0 END as is_va,
                        CASE WHEN TRIM(t.kode_transaksi) = '320' AND t.norek_aba LIKE '%0001000001' THEN 1 ELSE 0 END as is_mandiri,
                        CASE WHEN TRIM(t.kode_transaksi) = '320' AND t.norek_aba LIKE '%0001000004' THEN 1 ELSE 0 END as is_permata,
                        CASE WHEN TRIM(t.kode_transaksi) IN ('150', '152') THEN 1 ELSE 0 END as is_br,
                        CASE WHEN TRIM(t.kode_transaksi) IN ('140', '16', '162') THEN 1 ELSE 0 END as is_qris
                    FROM va t
                    WHERE ((t.tgl_transaksi > :w_closing AND t.tgl_transaksi <= :w_harian) 
                    OR (t.tgl_transaksi > :w_pclosing AND t.tgl_transaksi <= :w_pharian))
                    AND TRIM(t.kode_transaksi) IN ('320', '150', '152', '140', '16', '162')
                    $sqlFilter
                ) as mapped_data
            ";

            try {
                $stmt = $this->pdo->prepare($sql);
                foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
                $stmt->execute();
                $d = $stmt->fetch(PDO::FETCH_ASSOC);

                $calcGrowth = function($curr, $prev) {
                    if ($prev > 0) return round((($curr - $prev) / $prev) * 100, 1);
                    return $curr > 0 ? 100 : 0;
                };

                $fmtNominal = function($num) {
                    if ($num >= 1000000000) return 'Rp ' . round($num / 1000000000, 2) . ' M';
                    if ($num >= 1000000) return 'Rp ' . round($num / 1000000, 1) . ' jt';
                    return 'Rp ' . number_format($num, 0, ',', '.');
                };

                $cards = [
                    [
                        'title' => 'TOTAL DIGITAL (Semua Channel)',
                        'value' => $fmtNominal($d['curr_nom_all'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_all'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_all'] ?? 0, $d['prev_nom_all'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_all'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_all'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ],
                    [
                        'title' => 'TOTAL VIRTUAL ACCOUNT (VA)',
                        'value' => $fmtNominal($d['curr_nom_va'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_va'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_va'] ?? 0, $d['prev_nom_va'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_va'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_va'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ],
                    [
                        'title' => 'BANK MANDIRI (VA)',
                        'value' => $fmtNominal($d['curr_nom_mandiri'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_mandiri'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_mandiri'] ?? 0, $d['prev_nom_mandiri'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_mandiri'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_mandiri'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ],
                    [
                        'title' => 'BANK PERMATA (VA)',
                        'value' => $fmtNominal($d['curr_nom_permata'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_permata'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_permata'] ?? 0, $d['prev_nom_permata'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_permata'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_permata'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ],
                    [
                        'title' => 'TOTAL BRANCHLESS',
                        'value' => $fmtNominal($d['curr_nom_br'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_br'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_br'] ?? 0, $d['prev_nom_br'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_br'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_br'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ],
                    [
                        'title' => 'TOTAL QRIS',
                        'value' => $fmtNominal($d['curr_nom_qris'] ?? 0),
                        'subtitle' => number_format($d['curr_trx_qris'] ?? 0, 0, ',', '.') . ' transaksi (' . $label_periode . ')',
                        'growth' => $calcGrowth($d['curr_nom_qris'] ?? 0, $d['prev_nom_qris'] ?? 0),
                        'prev_nominal' => $fmtNominal($d['prev_nom_qris'] ?? 0),
                        'prev_trx' => number_format($d['prev_trx_qris'] ?? 0, 0, ',', '.'),
                        'prev_label' => $prev_label_periode
                    ]
                ];

                return sendResponse(200, "Berhasil ambil Summary Cards", [
                    'meta' => ['harian_date' => $harian, 'closing_date' => $closing_date],
                    'cards' => $cards
                ]);

            } catch (PDOException $e) { 
                return sendResponse(500, "PDO Error: " . $e->getMessage(), null); 
            }
        }

        /**
         * 12. DETAIL BREAKDOWN TRANSAKSI (Hierarki)
         * Membandingkan Current vs Previous Month.
         * Bebas Bug HY093 (Duplicate Named Parameters).
         */
        public function getDetailBreakdownTransaksi($input = null) {
            set_time_limit(300); ini_set('memory_limit', '1024M');

            $b = is_array($input) ? $input : [];
            $harian  = $b['harian_date'] ?? date('Y-m-d');
            $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
            $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
            $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
            $channel = !empty($b['channel']) ? strtoupper($b['channel']) : 'ALL'; 

            if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

            // --- 1. LOGIC PERIODE (Bulan Ini vs Bulan Lalu) ---
            $ts_harian = strtotime($harian);
            $prev_harian = date('Y-m-d', strtotime('-1 month', $ts_harian));

            if (!empty($b['closing_date'])) {
                $closing_date = $b['closing_date'];
                $prev_closing = date('Y-m-d', strtotime('-1 month', strtotime($closing_date)));
            } else {
                $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
                $prev_closing = date('Y-m-t', strtotime(date('Y-m-01', strtotime($prev_harian)) . ' -1 day'));
            }

            // --- 2. BUILD FILTER QUERY & AMAN DARI HY093 ---
            $sqlFilter = "";
            $params = [
                ':s_closing'  => $closing_date, ':s_harian'   => $harian,
                ':s_pclosing' => $prev_closing, ':s_pharian'  => $prev_harian,
                ':w_closing'  => $closing_date, ':w_harian'   => $harian,
                ':w_pclosing' => $prev_closing, ':w_pharian'  => $prev_harian
            ];

            $mode_hirarki = 'KONSOLIDASI';

            if ($kode_kantor && $kode_kantor !== '000') {
                $sqlFilter .= " AND t.kantor = :kode_kantor ";
                $params[':kode_kantor'] = $kode_kantor;
                $mode_hirarki = 'CABANG';
            } elseif ($korwil) {
                $kw_start = null; $kw_end = null;
                switch ($korwil) {
                    case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                    case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                    case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                    case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
                }
                if ($kw_start && $kw_end) {
                    $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                    $params[':kw_start'] = $kw_start;
                    $params[':kw_end'] = $kw_end;
                }
                $mode_hirarki = 'KORWIL';
            }

            if ($kankas) {
                $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas ";
                $params[':kode_kankas'] = $kankas;
                $mode_hirarki = 'KANKAS';
            }

            // Filter Channel
            $chanFilter = "";
            if ($channel === 'VA') {
                $chanFilter = " AND TRIM(t.kode_transaksi) = '320' ";
            } elseif ($channel === 'BRANCHLESS') {
                $chanFilter = " AND TRIM(t.kode_transaksi) IN ('150', '152') ";
            } elseif ($channel === 'QRIS') {
                $chanFilter = " AND TRIM(t.kode_transaksi) IN ('140', '16', '162') ";
            } else {
                $chanFilter = " AND TRIM(t.kode_transaksi) IN ('320', '150', '152', '140', '16', '162') ";
            }

            // --- 3. MAIN QUERY (SUBQUERY MAPPING ANTI HY093) ---
            $sql = "
                SELECT 
                    kantor,
                    nama_kantor,
                    kankas,
                    nama_kankas,
                    SUM(CASE WHEN is_curr = 1 THEN jumlah ELSE 0 END) as curr_nom,
                    SUM(CASE WHEN is_curr = 1 THEN 1 ELSE 0 END) as curr_trx,
                    SUM(CASE WHEN is_prev = 1 THEN jumlah ELSE 0 END) as prev_nom,
                    SUM(CASE WHEN is_prev = 1 THEN 1 ELSE 0 END) as prev_trx
                FROM (
                    SELECT 
                        t.kantor,
                        kk.nama_kantor,
                        TRIM(t.kankas) as kankas,
                        kn.deskripsi_group1 as nama_kankas,
                        t.jumlah,
                        CASE WHEN t.tgl_transaksi > :s_closing AND t.tgl_transaksi <= :s_harian THEN 1 ELSE 0 END as is_curr,
                        CASE WHEN t.tgl_transaksi > :s_pclosing AND t.tgl_transaksi <= :s_pharian THEN 1 ELSE 0 END as is_prev
                    FROM va t
                    LEFT JOIN kode_kantor kk ON t.kantor = kk.kode_kantor
                    LEFT JOIN kankas kn ON TRIM(t.kankas) = TRIM(kn.kode_group1)
                    WHERE ((t.tgl_transaksi > :w_closing AND t.tgl_transaksi <= :w_harian) 
                    OR (t.tgl_transaksi > :w_pclosing AND t.tgl_transaksi <= :w_pharian))
                    $chanFilter
                    $sqlFilter
                ) as mapped_data
                GROUP BY kantor, nama_kantor, kankas, nama_kankas
            ";

            try {
                $stmt = $this->pdo->prepare($sql);
                foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // --- 4. PROCESSING & HIERARCHY MAPPING ---
                $getKorwil = function($cabang) {
                    $c = (int)$cabang;
                    if ($c >= 1 && $c <= 7) return 'KORWIL SEMARANG';
                    if ($c >= 8 && $c <= 14) return 'KORWIL SOLO';
                    if ($c >= 15 && $c <= 21) return 'KORWIL BANYUMAS';
                    if ($c >= 22 && $c <= 28) return 'KORWIL PEKALONGAN';
                    return 'PUSAT / LAINNYA';
                };

                $calcGrowth = function($curr, $prev) {
                    if ($prev > 0) return round((($curr - $prev) / $prev) * 100, 2);
                    return $curr > 0 ? 100 : 0;
                };

                $grandTotal = ['curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0];
                $resultData = [];

                // JIKA LOGIN / FILTER CABANG -> Tampilkan Breakdown Kankas
                if ($mode_hirarki === 'CABANG' || $mode_hirarki === 'KANKAS') {
                    $kankasMap = [];
                    foreach ($rows as $r) {
                        $kKey = $r['kankas'] ?: '000000';
                        if (!isset($kankasMap[$kKey])) {
                            $kankasMap[$kKey] = [
                                'kode' => $kKey,
                                'nama' => $r['nama_kankas'] ?: 'Pusat / Operasional',
                                'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0
                            ];
                        }
                        $kankasMap[$kKey]['curr_nom'] += (float)$r['curr_nom'];
                        $kankasMap[$kKey]['curr_trx'] += (int)$r['curr_trx'];
                        $kankasMap[$kKey]['prev_nom'] += (float)$r['prev_nom'];
                        $kankasMap[$kKey]['prev_trx'] += (int)$r['prev_trx'];

                        $grandTotal['curr_nom'] += (float)$r['curr_nom'];
                        $grandTotal['curr_trx'] += (int)$r['curr_trx'];
                        $grandTotal['prev_nom'] += (float)$r['prev_nom'];
                        $grandTotal['prev_trx'] += (int)$r['prev_trx'];
                    }

                    // Hitung Growth Kankas
                    foreach ($kankasMap as &$k) {
                        $k['growth_nom'] = $calcGrowth($k['curr_nom'], $k['prev_nom']);
                        $k['growth_trx'] = $calcGrowth($k['curr_trx'], $k['prev_trx']);
                    }

                    $resultData = array_values($kankasMap);
                    usort($resultData, function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });

                } 
                // JIKA KONSOLIDASI / KORWIL -> Tampilkan Breakdown Korwil > Cabang
                else {
                    $korwilMap = [];
                    foreach ($rows as $r) {
                        $cab = $r['kantor'] ?: '000';
                        $kw = $getKorwil($cab);
                        
                        if (!isset($korwilMap[$kw])) {
                            $korwilMap[$kw] = [
                                'korwil' => $kw,
                                'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0,
                                'cabang' => []
                            ];
                        }
                        
                        if (!isset($korwilMap[$kw]['cabang'][$cab])) {
                            $korwilMap[$kw]['cabang'][$cab] = [
                                'kode' => $cab,
                                'nama' => $r['nama_kantor'] ?: "Cabang $cab",
                                'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0
                            ];
                        }
                        
                        // Inject Cabang
                        $korwilMap[$kw]['cabang'][$cab]['curr_nom'] += (float)$r['curr_nom'];
                        $korwilMap[$kw]['cabang'][$cab]['curr_trx'] += (int)$r['curr_trx'];
                        $korwilMap[$kw]['cabang'][$cab]['prev_nom'] += (float)$r['prev_nom'];
                        $korwilMap[$kw]['cabang'][$cab]['prev_trx'] += (int)$r['prev_trx'];
                        
                        // Inject Korwil
                        $korwilMap[$kw]['curr_nom'] += (float)$r['curr_nom'];
                        $korwilMap[$kw]['curr_trx'] += (int)$r['curr_trx'];
                        $korwilMap[$kw]['prev_nom'] += (float)$r['prev_nom'];
                        $korwilMap[$kw]['prev_trx'] += (int)$r['prev_trx'];

                        // Inject Grand Total
                        $grandTotal['curr_nom'] += (float)$r['curr_nom'];
                        $grandTotal['curr_trx'] += (int)$r['curr_trx'];
                        $grandTotal['prev_nom'] += (float)$r['prev_nom'];
                        $grandTotal['prev_trx'] += (int)$r['prev_trx'];
                    }

                    // Kalkulasi Growth & Re-index
                    foreach ($korwilMap as &$kwData) {
                        $kwData['growth_nom'] = $calcGrowth($kwData['curr_nom'], $kwData['prev_nom']);
                        $kwData['growth_trx'] = $calcGrowth($kwData['curr_trx'], $kwData['prev_trx']);
                        
                        $kwData['cabang'] = array_values($kwData['cabang']);
                        foreach ($kwData['cabang'] as &$cb) {
                            $cb['growth_nom'] = $calcGrowth($cb['curr_nom'], $cb['prev_nom']);
                            $cb['growth_trx'] = $calcGrowth($cb['curr_trx'], $cb['prev_trx']);
                        }
                        usort($kwData['cabang'], function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });
                    }

                    $resultData = array_values($korwilMap);
                    usort($resultData, function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });
                }

                // Hitung Growth Grand Total
                $grandTotal['growth_nom'] = $calcGrowth($grandTotal['curr_nom'], $grandTotal['prev_nom']);
                $grandTotal['growth_trx'] = $calcGrowth($grandTotal['curr_trx'], $grandTotal['prev_trx']);

                return sendResponse(200, "Berhasil ambil Breakdown Transaksi", [
                    'meta' => [
                        'hierarki_aktif' => $mode_hirarki,
                        'channel_aktif'  => $channel,
                        'periode_curr'   => ['start' => $closing_date, 'end' => $harian],
                        'periode_prev'   => ['start' => $prev_closing, 'end' => $prev_harian]
                    ],
                    'grand_total' => $grandTotal,
                    'data' => $resultData
                ]);

            } catch (PDOException $e) { 
                error_log("Error Breakdown Transaksi: " . $e->getMessage());
                return sendResponse(500, "PDO Error: " . $e->getMessage(), null); 
            }
        }

    /**
     * 12B. YEAR-OVER-YEAR (YOY) COMPARISON
     * Membandingkan Current Period vs Same Period Last Year.
     * Bebas Bug HY093 (Duplicate Named Parameters).
     */
    public function getYoyTransaksi($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        $channel = !empty($b['channel']) ? strtoupper($b['channel']) : 'ALL';

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        // --- 1. LOGIC PERIODE (Tahun Ini vs Tahun Lalu - Same Period) ---
        $ts_harian = strtotime($harian);

        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
        }

        // Previous Year: subtract 1 year from both dates (DateTime handles leap year correctly)
        $dtPrevHarian = new DateTime($harian);
        $dtPrevHarian->modify('-1 year');
        $prev_harian = $dtPrevHarian->format('Y-m-d');

        $dtPrevClosing = new DateTime($closing_date);
        $dtPrevClosing->modify('-1 year');
        $prev_closing = $dtPrevClosing->format('Y-m-d');

        // --- 2. BUILD FILTER QUERY & AMAN DARI HY093 ---
        $sqlFilter = "";
        $params = [
            ':s_closing'  => $closing_date, ':s_harian'   => $harian,
            ':s_pclosing' => $prev_closing, ':s_pharian'  => $prev_harian,
            ':w_closing'  => $closing_date, ':w_harian'   => $harian,
            ':w_pclosing' => $prev_closing, ':w_pharian'  => $prev_harian
        ];

        $mode_hirarki = 'KONSOLIDASI';

        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
            $mode_hirarki = 'CABANG';
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
            $mode_hirarki = 'KORWIL';
        }

        if ($kankas) {
            $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
            $mode_hirarki = 'KANKAS';
        }

        // Filter Channel
        $chanFilter = "";
        if ($channel === 'VA') {
            $chanFilter = " AND TRIM(t.kode_transaksi) = '320' ";
        } elseif ($channel === 'BRANCHLESS') {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('150', '152') ";
        } elseif ($channel === 'QRIS') {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('140', '16', '162') ";
        } else {
            $chanFilter = " AND TRIM(t.kode_transaksi) IN ('320', '150', '152', '140', '16', '162') ";
        }

        // --- 3. MAIN QUERY (SUBQUERY MAPPING ANTI HY093) ---
        $sql = "
            SELECT 
                kantor,
                nama_kantor,
                kankas,
                nama_kankas,
                SUM(CASE WHEN is_curr = 1 THEN jumlah ELSE 0 END) as curr_nom,
                SUM(CASE WHEN is_curr = 1 THEN 1 ELSE 0 END) as curr_trx,
                SUM(CASE WHEN is_prev = 1 THEN jumlah ELSE 0 END) as prev_nom,
                SUM(CASE WHEN is_prev = 1 THEN 1 ELSE 0 END) as prev_trx
            FROM (
                SELECT 
                    t.kantor,
                    kk.nama_kantor,
                    TRIM(t.kankas) as kankas,
                    kn.deskripsi_group1 as nama_kankas,
                    t.jumlah,
                    CASE WHEN t.tgl_transaksi > :s_closing AND t.tgl_transaksi <= :s_harian THEN 1 ELSE 0 END as is_curr,
                    CASE WHEN t.tgl_transaksi > :s_pclosing AND t.tgl_transaksi <= :s_pharian THEN 1 ELSE 0 END as is_prev
                FROM va t
                LEFT JOIN kode_kantor kk ON t.kantor = kk.kode_kantor
                LEFT JOIN kankas kn ON TRIM(t.kankas) = TRIM(kn.kode_group1)
                WHERE ((t.tgl_transaksi > :w_closing AND t.tgl_transaksi <= :w_harian) 
                OR (t.tgl_transaksi > :w_pclosing AND t.tgl_transaksi <= :w_pharian))
                $chanFilter
                $sqlFilter
            ) as mapped_data
            GROUP BY kantor, nama_kantor, kankas, nama_kankas
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- 4. PROCESSING & HIERARCHY MAPPING ---
            $getKorwil = function($cabang) {
                $c = (int)$cabang;
                if ($c >= 1 && $c <= 7) return 'KORWIL SEMARANG';
                if ($c >= 8 && $c <= 14) return 'KORWIL SOLO';
                if ($c >= 15 && $c <= 21) return 'KORWIL BANYUMAS';
                if ($c >= 22 && $c <= 28) return 'KORWIL PEKALONGAN';
                return 'PUSAT / LAINNYA';
            };

            $calcGrowth = function($curr, $prev) {
                if ($prev > 0) return round((($curr - $prev) / $prev) * 100, 2);
                return $curr > 0 ? 100 : 0;
            };

            $grandTotal = ['curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0];
            $resultData = [];
            $chartLabels = [];
            $chartCurrData = [];
            $chartPrevData = [];

            // JIKA LOGIN / FILTER CABANG -> Tampilkan Breakdown Kankas
            if ($mode_hirarki === 'CABANG' || $mode_hirarki === 'KANKAS') {
                $kankasMap = [];
                foreach ($rows as $r) {
                    $kKey = $r['kankas'] ?: '000000';
                    if (!isset($kankasMap[$kKey])) {
                        $kankasMap[$kKey] = [
                            'kode' => $kKey,
                            'nama' => $r['nama_kankas'] ?: 'Pusat / Operasional',
                            'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0
                        ];
                    }
                    $kankasMap[$kKey]['curr_nom'] += (float)$r['curr_nom'];
                    $kankasMap[$kKey]['curr_trx'] += (int)$r['curr_trx'];
                    $kankasMap[$kKey]['prev_nom'] += (float)$r['prev_nom'];
                    $kankasMap[$kKey]['prev_trx'] += (int)$r['prev_trx'];

                    $grandTotal['curr_nom'] += (float)$r['curr_nom'];
                    $grandTotal['curr_trx'] += (int)$r['curr_trx'];
                    $grandTotal['prev_nom'] += (float)$r['prev_nom'];
                    $grandTotal['prev_trx'] += (int)$r['prev_trx'];
                }

                foreach ($kankasMap as &$k) {
                    $k['yoy_growth_nom'] = $calcGrowth($k['curr_nom'], $k['prev_nom']);
                    $k['yoy_growth_trx'] = $calcGrowth($k['curr_trx'], $k['prev_trx']);
                }

                $resultData = array_values($kankasMap);
                usort($resultData, function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });

                foreach ($resultData as $item) {
                    $chartLabels[] = $item['nama'];
                    $chartCurrData[] = $item['curr_nom'];
                    $chartPrevData[] = $item['prev_nom'];
                }

            } else {
                // KONSOLIDASI / KORWIL -> Tampilkan Breakdown Korwil > Cabang
                $korwilMap = [];
                foreach ($rows as $r) {
                    $cab = $r['kantor'] ?: '000';
                    $kw = $getKorwil($cab);

                    if (!isset($korwilMap[$kw])) {
                        $korwilMap[$kw] = [
                            'korwil' => $kw,
                            'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0,
                            'cabang' => []
                        ];
                    }

                    if (!isset($korwilMap[$kw]['cabang'][$cab])) {
                        $korwilMap[$kw]['cabang'][$cab] = [
                            'kode' => $cab,
                            'nama' => $r['nama_kantor'] ?: "Cabang $cab",
                            'curr_nom' => 0, 'curr_trx' => 0, 'prev_nom' => 0, 'prev_trx' => 0
                        ];
                    }

                    $korwilMap[$kw]['cabang'][$cab]['curr_nom'] += (float)$r['curr_nom'];
                    $korwilMap[$kw]['cabang'][$cab]['curr_trx'] += (int)$r['curr_trx'];
                    $korwilMap[$kw]['cabang'][$cab]['prev_nom'] += (float)$r['prev_nom'];
                    $korwilMap[$kw]['cabang'][$cab]['prev_trx'] += (int)$r['prev_trx'];

                    $korwilMap[$kw]['curr_nom'] += (float)$r['curr_nom'];
                    $korwilMap[$kw]['curr_trx'] += (int)$r['curr_trx'];
                    $korwilMap[$kw]['prev_nom'] += (float)$r['prev_nom'];
                    $korwilMap[$kw]['prev_trx'] += (int)$r['prev_trx'];

                    $grandTotal['curr_nom'] += (float)$r['curr_nom'];
                    $grandTotal['curr_trx'] += (int)$r['curr_trx'];
                    $grandTotal['prev_nom'] += (float)$r['prev_nom'];
                    $grandTotal['prev_trx'] += (int)$r['prev_trx'];
                }

                foreach ($korwilMap as &$kwData) {
                    $kwData['yoy_growth_nom'] = $calcGrowth($kwData['curr_nom'], $kwData['prev_nom']);
                    $kwData['yoy_growth_trx'] = $calcGrowth($kwData['curr_trx'], $kwData['prev_trx']);

                    $kwData['cabang'] = array_values($kwData['cabang']);
                    foreach ($kwData['cabang'] as &$cb) {
                        $cb['yoy_growth_nom'] = $calcGrowth($cb['curr_nom'], $cb['prev_nom']);
                        $cb['yoy_growth_trx'] = $calcGrowth($cb['curr_trx'], $cb['prev_trx']);
                    }
                    usort($kwData['cabang'], function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });
                }

                $resultData = array_values($korwilMap);
                usort($resultData, function($a, $b) { return $b['curr_nom'] <=> $a['curr_nom']; });

                // Chart data: at korwil level for KONSOLIDASI, at cabang level for KORWIL
                if ($mode_hirarki === 'KONSOLIDASI') {
                    foreach ($resultData as $kw) {
                        $chartLabels[] = $kw['korwil'];
                        $chartCurrData[] = $kw['curr_nom'];
                        $chartPrevData[] = $kw['prev_nom'];
                    }
                } else {
                    foreach ($resultData as $kw) {
                        foreach ($kw['cabang'] as $cb) {
                            $chartLabels[] = $cb['nama'];
                            $chartCurrData[] = $cb['curr_nom'];
                            $chartPrevData[] = $cb['prev_nom'];
                        }
                    }
                }
            }

            // Hitung Growth Grand Total
            $grandTotal['yoy_growth_nom'] = $calcGrowth($grandTotal['curr_nom'], $grandTotal['prev_nom']);
            $grandTotal['yoy_growth_trx'] = $calcGrowth($grandTotal['curr_trx'], $grandTotal['prev_trx']);

            return sendResponse(200, "Berhasil ambil YOY Transaksi", [
                'meta' => [
                    'hierarki_aktif' => $mode_hirarki,
                    'channel_aktif'  => $channel,
                    'periode_curr'   => ['start' => $closing_date, 'end' => $harian],
                    'periode_prev'   => ['start' => $prev_closing, 'end' => $prev_harian]
                ],
                'grand_total' => $grandTotal,
                'data' => $resultData,
                'chart' => [
                    'labels' => $chartLabels,
                    'series' => [
                        ['name' => 'Tahun Ini', 'data' => $chartCurrData],
                        ['name' => 'Tahun Lalu', 'data' => $chartPrevData]
                    ]
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Error YOY Transaksi: " . $e->getMessage());
            return sendResponse(500, "Terjadi kesalahan server.", null);
        }
    }

    /**
     * 13. REKAP TRANSAKSI BRANCHLESS BERDASARKAN DEVICE
     * Syarat: kode_transaksi IN ('150', '152')
     */
    public function getRekapDeviceBranchless($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        // Logic Tanggal Range
        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $ts_harian = strtotime($harian);
            $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
        }

        $sqlFilter = "";
        $params = [':closing' => $closing_date, ':harian' => $harian];

        // Filter Area
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
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
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        if ($kankas) {
            $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // 🔥 FIX: Tambah kolom kantor dan nama_kantor ke dalam rekap
        $sql = "
            SELECT 
                t.kantor as kode_kantor,
                kk.nama_kantor,
                COALESCE(NULLIF(TRIM(t.device), ''), 'TIDAK TERDETEKSI') as device_id,
                COUNT(1) as total_trx,
                COUNT(DISTINCT t.no_rekening) as total_noa,
                SUM(t.jumlah) as total_nominal,
                SUM(COALESCE(t.adm, 0)) as total_adm
            FROM va t
            LEFT JOIN kode_kantor kk ON t.kantor = kk.kode_kantor
            WHERE t.tgl_transaksi > :closing AND t.tgl_transaksi <= :harian
            AND TRIM(t.kode_transaksi) IN ('150', '152')
            $sqlFilter
            GROUP BY t.kantor, kk.nama_kantor, device_id
            ORDER BY total_nominal DESC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grandTotal = ['total_trx' => 0, 'total_noa' => 0, 'total_nominal' => 0, 'total_adm' => 0];

            foreach ($rows as &$r) {
                $r['total_trx']     = (int)$r['total_trx'];
                $r['total_noa']     = (int)$r['total_noa'];
                $r['total_nominal'] = (float)$r['total_nominal'];
                $r['total_adm']     = (float)$r['total_adm'];

                // Handle jika nama_kantor kosong
                if (empty($r['nama_kantor'])) {
                    $r['nama_kantor'] = 'Cabang ' . $r['kode_kantor'];
                }

                $grandTotal['total_trx']     += $r['total_trx'];
                $grandTotal['total_noa']     += $r['total_noa'];
                $grandTotal['total_nominal'] += $r['total_nominal'];
                $grandTotal['total_adm']     += $r['total_adm'];
            }

            return sendResponse(200, "Berhasil ambil rekap device branchless", [
                'meta' => ['harian_date' => $harian, 'closing_date' => $closing_date],
                'grand_total' => $grandTotal,
                'data' => $rows
            ]);
        } catch (PDOException $e) { return sendResponse(500, "PDO Error: " . $e->getMessage(), null); }
    }

    /**
     * 14. DETAIL RIWAYAT TRANSAKSI DEVICE (Pagination)
     * Ditampilkan ketika salah satu Device ID di-klik.
     */
    public function getDetailDeviceBranchless($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
        
        $device  = $b['device_id'] ?? ''; // ID Device yang diklik
        $page    = $b['page'] ?? 1;
        $limit   = $b['limit'] ?? 20;
        $offset  = ($page - 1) * $limit;

        if (!$harian || !$device) return sendResponse(400, "Tanggal Harian & Device ID wajib diisi.", null);

        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $ts_harian = strtotime($harian);
            $closing_date = date('Y-m-t', strtotime(date('Y-m-01', $ts_harian) . ' -1 day'));
        }

        $sqlFilter = "";
        $params = [':closing' => $closing_date, ':harian' => $harian];

        // Filter Area
        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
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
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        if ($kankas) {
            $sqlFilter .= " AND TRIM(t.kankas) = :kode_kankas ";
            $params[':kode_kankas'] = $kankas;
        }

        // Logic Tangkap Device (Termasuk yang kosong / NULL)
        if ($device === 'TIDAK TERDETEKSI') {
            $sqlFilter .= " AND (TRIM(t.device) IS NULL OR TRIM(t.device) = '') ";
        } else {
            $sqlFilter .= " AND TRIM(t.device) = :device_id ";
            $params[':device_id'] = $device;
        }

        $baseQuery = "
            FROM va t
            WHERE t.tgl_transaksi > :closing AND t.tgl_transaksi <= :harian
            AND TRIM(t.kode_transaksi) IN ('150', '152')
            $sqlFilter
        ";

        try {
            $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) $baseQuery");
            foreach ($params as $key => $val) { $stmtCnt->bindValue($key, $val); }
            $stmtCnt->execute();
            $totalRecords = $stmtCnt->fetchColumn();

            $sqlData = "
                SELECT 
                    t.tgl_transaksi, 
                    t.jam_transaksi, 
                    t.no_rekening, 
                    t.kode_transaksi, 
                    t.jumlah, 
                    COALESCE(t.adm, 0) as adm, 
                    t.no_bukti, 
                    t.keterangan, 
                    t.user_id,
                    t.kantor,
                    TRIM(t.kankas) as kankas
                $baseQuery 
                ORDER BY t.tgl_transaksi DESC, t.jam_transaksi DESC 
                LIMIT :lim OFFSET :off
            ";

            $stmt = $this->pdo->prepare($sqlData);
            foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format tipe data
            foreach ($rows as &$r) {
                $r['jumlah'] = (float)$r['jumlah'];
                $r['adm']    = (float)$r['adm'];
            }

            return sendResponse(200, "Berhasil ambil detail riwayat device", [
                'pagination' => [
                    'current_page'  => (int)$page, 
                    'total_records' => (int)$totalRecords, 
                    'total_pages'   => ceil($totalRecords / $limit)
                ],
                'data' => $rows
            ]);
        } catch (PDOException $e) { return sendResponse(500, "PDO Error: " . $e->getMessage(), null); }
    }


    /**
     * Dashboard Layanan Digital - Top 5 & Bottom 5 cabang per channel
     */
    public function getDashboardLayananDigital($input = null) {
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $harian  = $b['harian_date'] ?? date('Y-m-d');
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;

        if (!$harian) return sendResponse(400, "Tanggal Actual (Harian) wajib diisi.", null);

        $ts_harian = strtotime($harian);
        $y = (int)date('Y', $ts_harian);
        $m = (int)date('m', $ts_harian);
        $d = (int)date('d', $ts_harian);

        // Logika tanggal bulletproof menggunakan mktime()
        $prev_month_ts = mktime(0, 0, 0, $m - 1, 1, $y);
        $prev_y = date('Y', $prev_month_ts);
        $prev_m = date('m', $prev_month_ts);
        $max_days = date('t', $prev_month_ts);

        $prev_d = min($d, $max_days);
        $prev_harian = sprintf("%04d-%02d-%02d", $prev_y, $prev_m, $prev_d);

        if (!empty($b['closing_date'])) {
            $closing_date = $b['closing_date'];
        } else {
            $closing_date = date('Y-m-t', strtotime(sprintf("%04d-%02d-01", $y, $m) . ' -1 day'));
        }

        $prev_closing = date('Y-m-t', strtotime(date('Y-m-01', strtotime($closing_date)) . ' -1 month'));

        // Channel definitions
        $channels = [
            'VA' => "TRIM(t.kode_transaksi) = '320'",
            'BRANCHLESS' => "TRIM(t.kode_transaksi) IN ('150', '152')",
            'QRIS' => "TRIM(t.kode_transaksi) IN ('140', '16', '162')"
        ];

        // Area filter
        $sqlFilter = "";
        $filterParams = [];
        $filterLabel = "KONSOLIDASI";

        if ($kode_kantor && $kode_kantor !== '000') {
            $filterLabel = "CABANG_" . $kode_kantor;
        } elseif ($korwil) {
            $kw_start = null; $kw_end = null;
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
            if ($kw_start && $kw_end) {
                $sqlFilter = " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $filterParams[':kw_start'] = $kw_start;
                $filterParams[':kw_end'] = $kw_end;
            }
            $filterLabel = "KORWIL_" . $korwil;
        }

        $result = [];

        try {
            foreach ($channels as $chName => $chCondition) {

                if ($kode_kantor && $kode_kantor !== '000') {
                    // Specific branch: show kankas breakdown
                    $sql = "
                        SELECT 
                            TRIM(t.kankas) as kode,
                            TRIM(t.kankas) as nama,
                            SUM(CASE WHEN t.tgl_transaksi > :c_closing AND t.tgl_transaksi <= :c_harian THEN t.jumlah ELSE 0 END) as curr_nom,
                            SUM(CASE WHEN t.tgl_transaksi > :p_closing AND t.tgl_transaksi <= :p_harian THEN t.jumlah ELSE 0 END) as prev_nom
                        FROM va t
                        WHERE ((t.tgl_transaksi > :w_closing AND t.tgl_transaksi <= :w_harian)
                            OR (t.tgl_transaksi > :wp_closing AND t.tgl_transaksi <= :wp_harian))
                            AND $chCondition
                            AND t.kantor = :kode_kantor
                        GROUP BY TRIM(t.kankas)
                        ORDER BY curr_nom DESC
                    ";

                    $params = [
                        ':c_closing' => $closing_date, ':c_harian' => $harian,
                        ':p_closing' => $prev_closing, ':p_harian' => $prev_harian,
                        ':w_closing' => $closing_date, ':w_harian' => $harian,
                        ':wp_closing' => $prev_closing, ':wp_harian' => $prev_harian,
                        ':kode_kantor' => $kode_kantor
                    ];

                    $stmt = $this->pdo->prepare($sql);
                    foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $totalCurr = 0; $totalPrev = 0;
                    $items = [];
                    foreach ($rows as $r) {
                        $cNom = (float)($r['curr_nom'] ?? 0);
                        $pNom = (float)($r['prev_nom'] ?? 0);
                        $growth = $pNom > 0 ? round(($cNom - $pNom) / $pNom * 100, 1) : ($cNom > 0 ? 100 : 0);
                        $totalCurr += $cNom;
                        $totalPrev += $pNom;
                        $items[] = [
                            'nama' => $r['nama'] ?: ('Kankas ' . $r['kode']),
                            'kode' => $r['kode'],
                            'curr_nom' => $cNom,
                            'prev_nom' => $pNom,
                            'growth' => $growth
                        ];
                    }

                    $top5 = array_slice($items, 0, 5);
                    $bottom5 = count($items) > 5 ? array_slice($items, -5) : [];
                    $totalGrowth = $totalPrev > 0 ? round(($totalCurr - $totalPrev) / $totalPrev * 100, 1) : ($totalCurr > 0 ? 100 : 0);

                    $result[$chName] = [
                        'top5' => $top5,
                        'bottom5' => $bottom5,
                        'total_curr' => $totalCurr,
                        'total_prev' => $totalPrev,
                        'growth' => $totalGrowth
                    ];

                } else {
                    // Konsolidasi or Korwil: show cabang breakdown
                    $sql = "
                        SELECT 
                            t.kantor as kode,
                            COALESCE(k.nama_kantor, CONCAT('Cabang ', t.kantor)) as nama,
                            SUM(CASE WHEN t.tgl_transaksi > :c_closing AND t.tgl_transaksi <= :c_harian THEN t.jumlah ELSE 0 END) as curr_nom,
                            SUM(CASE WHEN t.tgl_transaksi > :p_closing AND t.tgl_transaksi <= :p_harian THEN t.jumlah ELSE 0 END) as prev_nom
                        FROM va t
                        LEFT JOIN kode_kantor k ON t.kantor = k.kode_kantor
                        WHERE ((t.tgl_transaksi > :w_closing AND t.tgl_transaksi <= :w_harian)
                            OR (t.tgl_transaksi > :wp_closing AND t.tgl_transaksi <= :wp_harian))
                            AND $chCondition
                            $sqlFilter
                        GROUP BY t.kantor, k.nama_kantor
                        ORDER BY curr_nom DESC
                    ";

                    $params = [
                        ':c_closing' => $closing_date, ':c_harian' => $harian,
                        ':p_closing' => $prev_closing, ':p_harian' => $prev_harian,
                        ':w_closing' => $closing_date, ':w_harian' => $harian,
                        ':wp_closing' => $prev_closing, ':wp_harian' => $prev_harian
                    ];
                    foreach ($filterParams as $fk => $fv) { $params[$fk] = $fv; }

                    $stmt = $this->pdo->prepare($sql);
                    foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $totalCurr = 0; $totalPrev = 0;
                    $items = [];
                    foreach ($rows as $r) {
                        $cNom = (float)($r['curr_nom'] ?? 0);
                        $pNom = (float)($r['prev_nom'] ?? 0);
                        $growth = $pNom > 0 ? round(($cNom - $pNom) / $pNom * 100, 1) : ($cNom > 0 ? 100 : 0);
                        $totalCurr += $cNom;
                        $totalPrev += $pNom;
                        $items[] = [
                            'nama' => $r['nama'],
                            'kode' => $r['kode'],
                            'curr_nom' => $cNom,
                            'prev_nom' => $pNom,
                            'growth' => $growth
                        ];
                    }

                    $top5 = array_slice($items, 0, 5);
                    // Bottom 5: take from bottom, but only items with curr_nom > 0
                    $nonZero = array_filter($items, fn($it) => $it['curr_nom'] > 0);
                    $nonZero = array_values($nonZero);
                    $bottom5 = count($nonZero) > 5 ? array_slice($nonZero, -5) : [];

                    $totalGrowth = $totalPrev > 0 ? round(($totalCurr - $totalPrev) / $totalPrev * 100, 1) : ($totalCurr > 0 ? 100 : 0);

                    $result[$chName] = [
                        'top5' => $top5,
                        'bottom5' => $bottom5,
                        'total_curr' => $totalCurr,
                        'total_prev' => $totalPrev,
                        'growth' => $totalGrowth
                    ];
                }
            }

            return sendResponse(200, "Berhasil", [
                'meta' => [
                    'harian_date' => $harian,
                    'closing_date' => $closing_date,
                    'filter' => $filterLabel
                ],
                'channels' => $result
            ]);

        } catch (PDOException $e) {
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null);
        }
    }


    /**
     * VA Detail Mandiri vs Permata - Monthly Breakdown
     */
    public function getVaDetailMandiriPermata($input = null) {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $b = is_array($input) ? $input : [];
        $harian = $b['harian_date'] ?? date('Y-m-d');
        $closing_date = !empty($b['closing_date']) ? $b['closing_date'] : null;
        $kode_kantor = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;

        // Determine year from harian_date
        $year = date('Y', strtotime($harian));

        // Build filter
        $sqlFilter = "";
        $params = [':year' => $year];

        if ($kode_kantor && $kode_kantor !== '000') {
            $sqlFilter .= " AND t.kantor = :kode_kantor ";
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
                $sqlFilter .= " AND t.kantor BETWEEN :kw_start AND :kw_end ";
                $params[':kw_start'] = $kw_start;
                $params[':kw_end'] = $kw_end;
            }
        }

        try {
            $sql = "
                SELECT 
                    MONTH(t.tgl_transaksi) as bulan,
                    SUM(CASE WHEN t.norek_aba LIKE '%0001000001' THEN t.jumlah ELSE 0 END) as mandiri_nom,
                    SUM(CASE WHEN t.norek_aba LIKE '%0001000001' THEN 1 ELSE 0 END) as mandiri_trx,
                    SUM(CASE WHEN t.norek_aba LIKE '%0001000004' THEN t.jumlah ELSE 0 END) as permata_nom,
                    SUM(CASE WHEN t.norek_aba LIKE '%0001000004' THEN 1 ELSE 0 END) as permata_trx
                FROM va t
                WHERE TRIM(t.kode_transaksi) = '320'
                    AND YEAR(t.tgl_transaksi) = :year
                    $sqlFilter
                GROUP BY MONTH(t.tgl_transaksi)
                ORDER BY bulan ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

            $monthly = [];
            $totalMandiriNom = 0;
            $totalMandiriTrx = 0;
            $totalPermataNom = 0;
            $totalPermataTrx = 0;

            // Map fetched data by month
            $dataMap = [];
            foreach ($rows as $r) {
                $dataMap[(int)$r['bulan']] = $r;
            }

            for ($m = 1; $m <= 12; $m++) {
                $mNom = isset($dataMap[$m]) ? (float)$dataMap[$m]['mandiri_nom'] : 0;
                $mTrx = isset($dataMap[$m]) ? (int)$dataMap[$m]['mandiri_trx'] : 0;
                $pNom = isset($dataMap[$m]) ? (float)$dataMap[$m]['permata_nom'] : 0;
                $pTrx = isset($dataMap[$m]) ? (int)$dataMap[$m]['permata_trx'] : 0;

                $totalMandiriNom += $mNom;
                $totalMandiriTrx += $mTrx;
                $totalPermataNom += $pNom;
                $totalPermataTrx += $pTrx;

                $monthly[] = [
                    'bulan' => $bulanNames[$m - 1],
                    'mandiri_nom' => $mNom,
                    'mandiri_trx' => $mTrx,
                    'permata_nom' => $pNom,
                    'permata_trx' => $pTrx
                ];
            }

            $filterLabel = 'Konsolidasi';
            if ($kode_kantor && $kode_kantor !== '000') {
                $filterLabel = 'Cabang ' . $kode_kantor;
            } elseif ($korwil) {
                $filterLabel = 'Korwil ' . ucfirst(strtolower($korwil));
            }

            return sendResponse(200, "Berhasil", [
                'meta' => [
                    'tahun' => $year,
                    'harian_date' => $harian,
                    'filter' => $filterLabel
                ],
                'monthly' => $monthly,
                'total' => [
                    'mandiri_nom' => $totalMandiriNom,
                    'mandiri_trx' => $totalMandiriTrx,
                    'permata_nom' => $totalPermataNom,
                    'permata_trx' => $totalPermataTrx
                ]
            ]);

        } catch (PDOException $e) {
            return sendResponse(500, "PDO Error: " . $e->getMessage(), null);
        }
    }


}