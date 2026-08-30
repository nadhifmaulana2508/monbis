<?php

class RepaymentRateController {

    private $pdo;
    private $reportCacheTtl = 300;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function reportCachePath(string $key): string {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'report_dpk_rr_cache';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        return $dir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
    }

    private function getReportCache(string $key, bool $refresh = false): ?array {
        $path = $this->reportCachePath($key);
        if ($refresh || !is_file($path)) return null;
        $age = time() - (int)filemtime($path);
        if ($age > $this->reportCacheTtl) return null;
        $data = json_decode((string)@file_get_contents($path), true);
        if (!is_array($data)) return null;
        $data['meta'] = is_array($data['meta'] ?? null) ? $data['meta'] : [];
        $data['meta']['cache_hit'] = true;
        $data['meta']['cache_age_seconds'] = max(0, $age);
        return $data;
    }

    private function putReportCache(string $key, array $data): void {
        $data['meta'] = is_array($data['meta'] ?? null) ? $data['meta'] : [];
        $data['meta']['cache_hit'] = false;
        @file_put_contents($this->reportCachePath($key), json_encode($data), LOCK_EX);
    }

    private function send($status, $msg, $data = []) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode(['status' => $status, 'message' => $msg, 'data' => $data]);
        exit;
    }

    private function getDayRange($date) {
        return [$date . ' 00:00:00', $date . ' 23:59:59'];
    }

    private function getMappedDay($originalDay, $month, $year) {
        $lastDayOfMonth = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
        $effectiveDay = min($originalDay, $lastDayOfMonth);
        
        if ($effectiveDay == $lastDayOfMonth) {
            $dateString = "$year-$month-$effectiveDay";
            $dayOfWeek  = date('w', strtotime($dateString)); 
            if ($dayOfWeek == 0) { 
                $effectiveDay = $effectiveDay - 1; 
            }
        }
        return $effectiveDay;
    }

    private function getDpdWhere(string $column, string $bucket): string {
        if ($bucket === 'dpd1-30') {
            return "AND {$column} BETWEEN 1 AND 30";
        }
        if ($bucket === 'all' || $bucket === 'dpd0-30') {
            return "AND {$column} BETWEEN 0 AND 30";
        }
        return "AND {$column} = 0";
    }

    private function getKppExclusionWhere(string $alias = ''): string {
        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
        return "AND NOT ({$prefix}kode_produk = '127' OR ({$prefix}kode_produk = '139' AND COALESCE({$prefix}no_alternatif_1, '') = '1'))";
    }

    private function getSaldoColumn(array $input): string {
        $mode = strtolower(trim((string)($input['hitung_berdasarkan'] ?? $input['tipe_saldo'] ?? 'baki_debet')));
        return $mode === 'saldo_bank' ? 'saldo_bank' : 'baki_debet';
    }

    private function getDueDaysForRequest(string $harian, $tglTagih = 'ALL'): array {
        $curTime = strtotime($harian);
        $month = (int)date('n', $curTime);
        $year = (int)date('Y', $curTime);
        $cutoffDay = min((int)date('j', $curTime), (int)date('t', $curTime));

        $days = [];
        if ($tglTagih !== 'ALL') {
            $tglMap = (int)$tglTagih;
            for ($d = 1; $d <= 31; $d++) {
                if ($this->getMappedDay($d, $month, $year) === $tglMap) {
                    $days[] = $d;
                }
            }
            return $days ?: [$tglMap];
        }

        for ($d = 1; $d <= 31; $d++) {
            if ($this->getMappedDay($d, $month, $year) <= $cutoffDay) {
                $days[] = $d;
            }
        }

        return $days ?: [1];
    }

    private function getMappedDueDateForHarian(string $harian, ?string $dueDate): ?string {
        if (!$dueDate) return null;
        $dueDay = (int)date('j', strtotime(substr($dueDate, 0, 10)));
        if ($dueDay < 1) return null;

        $harianTs = strtotime(substr($harian, 0, 10));
        if (!$harianTs) return null;

        $month = (int)date('n', $harianTs);
        $year = (int)date('Y', $harianTs);
        $mappedDay = $this->getMappedDay($dueDay, $month, $year);
        return sprintf('%04d-%02d-%02d', $year, $month, $mappedDay);
    }

    private function buildPaymentStatus(string $harian, ?string $dueDate, ?string $paidDate, float $paidAmount, int $fallbackDpd = 0): array {
        $mappedDueDate = $this->getMappedDueDateForHarian($harian, $dueDate);
        $dueTs = $mappedDueDate ? strtotime($mappedDueDate) : false;
        $harianTs = strtotime(substr($harian, 0, 10));
        $paidTs = ($paidAmount > 0 && $paidDate) ? strtotime(substr($paidDate, 0, 10)) : false;

        if ($paidAmount > 0 && $paidTs && $dueTs) {
            $lateDays = max(0, (int)floor(($paidTs - $dueTs) / 86400));
            if ($lateDays === 0) {
                return ['status_code' => 'OTP', 'label' => 'OTP', 'hari_telat' => 0, 'hari_menunggak' => 0];
            }
            return ['status_code' => 'TELAT', 'label' => 'Telat', 'hari_telat' => $lateDays, 'hari_menunggak' => 0];
        }

        if ($dueTs && $harianTs <= $dueTs) {
            return ['status_code' => 'BELUM_JATUH_TEMPO', 'label' => 'Belum Jatuh Tempo', 'hari_telat' => 0, 'hari_menunggak' => 0];
        }

        $lateDays = ($dueTs && $harianTs) ? max(0, (int)floor(($harianTs - $dueTs) / 86400)) : max(0, $fallbackDpd);
        return ['status_code' => 'BELUM_BAYAR', 'label' => 'Belum Bayar', 'hari_telat' => 0, 'hari_menunggak' => $lateDays];
    }

    /**
     * 1. REKAP UTAMA (Summary Per Tanggal) (OTP RR)
     */
    public function getRepaymentRate($input = null) {
            set_time_limit(300); ini_set('memory_limit', '1024M');

            $b = is_array($input) ? $input : [];
            $closing = $b['closing_date'] ?? null;
            $harian  = $b['harian_date'] ?? null;
            $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
            $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
            $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
            $ao      = !empty($b['kode_ao']) ? $b['kode_ao'] : null;
            $dpdBucket = $b['dpd_bucket'] ?? 'dpd0'; // dpd0, dpd1-30, all
            
            // 🔥 Tangkap parameter include_127 dari FE
            $include127 = filter_var($b['include_127'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $whereProduk = !$include127 ? $this->getKppExclusionWhere() : "";

            if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

            $reportCacheKey = 'rekap_rr|' . json_encode([
                'closing' => $closing, 'harian' => $harian, 'kantor' => $kc,
                'korwil' => $korwil, 'kankas' => $kankas, 'ao' => $ao,
                'dpd' => $dpdBucket, 'include_127' => $include127,
            ]);
            $cachedReport = $this->getReportCache($reportCacheKey, filter_var($b['refresh'] ?? false, FILTER_VALIDATE_BOOLEAN));
            if ($cachedReport !== null) $this->send(200, "Sukses Rekap RR (cache)", $cachedReport);

            [$s1, $e1] = $this->getDayRange($closing);
            [$s2, $e2] = $this->getDayRange($harian);

            $curTime  = strtotime($harian);
            $curMonth = date('n', $curTime);
            $curYear  = date('Y', $curTime);
            
            // 🔥 Tampilkan full kalender 1 bulan penuh (misal: 30 atau 31 hari)
            $cutoffDay = (int)date('t', $curTime);
            // 🔥 Ambil tanggal aktual harian untuk membatasi hitungan akumulasi persentase TOTAL
            $actualDay = (int)date('j', $curTime); 

            // 🔥 Filter DPD Bucket di M-1
            $whereDpd = $this->getDpdWhere('hari_menunggak', $dpdBucket);

            // 🔥 Korwil mapping
            $kw_start = null; $kw_end = null;
            if ($korwil) {
                switch ($korwil) {
                    case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                    case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                    case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                    case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
                }
            }

            // 🔥 Terapkan $whereProduk + $whereDpd
            $txStart = date('Y-m-01 00:00:00', strtotime($harian));
            $txEnd   = date('Y-m-d 23:59:59', strtotime($harian));

            $dayMapParts = [];
            for ($day = 1; $day <= 31; $day++) {
                $dayMapParts[] = "WHEN {$day} THEN " . $this->getMappedDay($day, (int)$curMonth, (int)$curYear);
            }
            $dayMapCase = "CASE DAY(t1.tgl_jatuh_tempo) " . implode(' ', $dayMapParts) . " ELSE DAY(t1.tgl_jatuh_tempo) END";
            $whereDpdFast = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);
            $whereProdukFast = !$include127 ? $this->getKppExclusionWhere('t1') : "";

            try {
                $paidDiff = "(t1.baki_debet - COALESCE(t2.baki_debet, 0))";
                $paidCondition = "t2.no_rekening IS NOT NULL AND COALESCE(t2.baki_debet, 0) > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 AND t1.baki_debet > COALESCE(t2.baki_debet, 0)";
                $sqlFast = "SELECT
                                {$dayMapCase} AS tgl,
                                COUNT(1) AS target_noa,
                                COALESCE(SUM(t1.baki_debet), 0) AS target_os,
                                SUM(CASE WHEN t2.no_rekening IS NOT NULL AND COALESCE(t2.baki_debet, 0) > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 THEN 1 ELSE 0 END) AS lancar_noa,
                                COALESCE(SUM(CASE WHEN t2.no_rekening IS NOT NULL AND COALESCE(t2.baki_debet, 0) > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 THEN t2.baki_debet ELSE 0 END), 0) AS lancar_os,
                                SUM(CASE WHEN t2.no_rekening IS NOT NULL AND COALESCE(t2.baki_debet, 0) > 0 AND COALESCE(t2.hari_menunggak, 0) > 0 THEN 1 ELSE 0 END) AS macet_noa,
                                COALESCE(SUM(CASE WHEN t2.no_rekening IS NOT NULL AND COALESCE(t2.baki_debet, 0) > 0 AND COALESCE(t2.hari_menunggak, 0) > 0 THEN t2.baki_debet ELSE 0 END), 0) AS macet_os,
                                SUM(CASE WHEN t2.no_rekening IS NULL OR COALESCE(t2.baki_debet, 0) <= 0 THEN 1 ELSE 0 END) AS lunas_noa,
                                COALESCE(SUM(CASE WHEN t2.no_rekening IS NULL OR COALESCE(t2.baki_debet, 0) <= 0 THEN t1.baki_debet ELSE 0 END), 0) AS lunas_os,
                                COALESCE(SUM(CASE WHEN {$paidCondition} THEN {$paidDiff} ELSE 0 END), 0) AS angsuran,
                                0 AS angsuran_sesuai,
                                0 AS angsuran_sesuai_noa,
                                0 AS angsuran_sesuai_baki_debet,
                                0 AS angsuran_lewat,
                                0 AS angsuran_lewat_noa,
                                0 AS angsuran_lewat_baki_debet,
                                0 AS angsuran_tanpa_tanggal,
                                0 AS angsuran_tanpa_tanggal_noa,
                                0 AS angsuran_tanpa_tanggal_baki_debet
                            FROM nominatif t1
                            LEFT JOIN nominatif t2 ON t1.no_rekening = t2.no_rekening
                                AND t2.created BETWEEN :s2_fast AND :e2_fast
                            WHERE t1.created BETWEEN :s1_fast AND :e1_fast
                              AND t1.kolektibilitas = 'L'
                              AND t1.baki_debet > 0
                              {$whereDpdFast}
                              {$whereProdukFast}";

                if ($kc && $kc !== '000') $sqlFast .= " AND t1.kode_cabang = :kc_fast";
                elseif ($korwil && $kw_start && $kw_end) $sqlFast .= " AND t1.kode_cabang BETWEEN :kw_start_fast AND :kw_end_fast";
                if ($kankas) $sqlFast .= " AND t1.kode_group1 = :kankas_fast";
                if ($ao) $sqlFast .= " AND t1.kode_group2 = :ao_fast";
                $sqlFast .= " GROUP BY {$dayMapCase} ORDER BY tgl ASC";

                $fastMainStarted = microtime(true);
                $stmtFast = $this->pdo->prepare($sqlFast);
                $stmtFast->bindValue(':s1_fast', $s1);
                $stmtFast->bindValue(':e1_fast', $e1);
                $stmtFast->bindValue(':s2_fast', $s2);
                $stmtFast->bindValue(':e2_fast', $e2);
                if ($kc && $kc !== '000') $stmtFast->bindValue(':kc_fast', $kc);
                elseif ($korwil && $kw_start && $kw_end) {
                    $stmtFast->bindValue(':kw_start_fast', $kw_start);
                    $stmtFast->bindValue(':kw_end_fast', $kw_end);
                }
                if ($kankas) $stmtFast->bindValue(':kankas_fast', $kankas);
                if ($ao) $stmtFast->bindValue(':ao_fast', $ao);
                $stmtFast->execute();
                $fastMainMs = round((microtime(true) - $fastMainStarted) * 1000);

                $report = [];
                for ($i = 1; $i <= $cutoffDay; $i++) {
                    $report[$i] = [
                        'tgl' => $i,
                        'target_noa' => 0, 'target_os' => 0,
                        'lancar_noa' => 0, 'lancar_os' => 0,
                        'macet_noa' => 0, 'macet_os' => 0,
                        'lunas_noa' => 0, 'lunas_os' => 0,
                        'angsuran' => 0, 'angsuran_sesuai' => 0, 'angsuran_sesuai_noa' => 0,
                        'angsuran_lewat' => 0, 'angsuran_lewat_noa' => 0,
                        'angsuran_tanpa_tanggal' => 0, 'angsuran_tanpa_tanggal_noa' => 0,
                        'angsuran_sesuai_baki_debet' => 0, 'angsuran_lewat_baki_debet' => 0,
                        'angsuran_tanpa_tanggal_baki_debet' => 0,
                        'angsuran_sesuai_persen' => 0, 'angsuran_lewat_persen' => 0,
                        'total_bayar' => 0,
                        'persen' => null
                    ];
                }
                $grandTotal = [
                    'target_noa'=>0, 'target_os'=>0, 'lancar_noa'=>0, 'lancar_os'=>0,
                    'macet_noa'=>0, 'macet_os'=>0, 'lunas_noa'=>0, 'lunas_os'=>0,
                    'angsuran'=>0, 'angsuran_sesuai'=>0, 'angsuran_sesuai_noa'=>0,
                    'angsuran_lewat'=>0, 'angsuran_lewat_noa'=>0,
                    'angsuran_tanpa_tanggal'=>0, 'angsuran_tanpa_tanggal_noa'=>0,
                    'angsuran_sesuai_baki_debet'=>0, 'angsuran_lewat_baki_debet'=>0,
                    'angsuran_tanpa_tanggal_baki_debet'=>0,
                    'angsuran_sesuai_persen'=>0, 'angsuran_lewat_persen'=>0,
                    'total_bayar'=>0, 'persen'=>0
                ];
                $dueSummary = [
                    'sesuai' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                    'lewat' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                    'tanpa_tanggal' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                    'total' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 100]
                ];

                foreach ($stmtFast->fetchAll(PDO::FETCH_ASSOC) as $rowFast) {
                    $tglMap = (int)$rowFast['tgl'];
                    if ($tglMap < 1 || $tglMap > $cutoffDay || !isset($report[$tglMap])) continue;

                    foreach (['target_noa','lancar_noa','macet_noa','lunas_noa','angsuran_sesuai_noa','angsuran_lewat_noa','angsuran_tanpa_tanggal_noa'] as $key) {
                        $report[$tglMap][$key] = (int)($rowFast[$key] ?? 0);
                        $grandTotal[$key] += $report[$tglMap][$key];
                    }
                    foreach (['target_os','lancar_os','macet_os','lunas_os','angsuran','angsuran_sesuai','angsuran_lewat','angsuran_tanpa_tanggal','angsuran_sesuai_baki_debet','angsuran_lewat_baki_debet','angsuran_tanpa_tanggal_baki_debet'] as $key) {
                        $report[$tglMap][$key] = (float)($rowFast[$key] ?? 0);
                        $grandTotal[$key] += $report[$tglMap][$key];
                    }

                    $dueSummary['sesuai']['noa'] += (int)($rowFast['angsuran_sesuai_noa'] ?? 0);
                    $dueSummary['sesuai']['angsuran'] += (float)($rowFast['angsuran_sesuai'] ?? 0);
                    $dueSummary['sesuai']['baki_debet'] += (float)($rowFast['angsuran_sesuai_baki_debet'] ?? 0);
                    $dueSummary['lewat']['noa'] += (int)($rowFast['angsuran_lewat_noa'] ?? 0);
                    $dueSummary['lewat']['angsuran'] += (float)($rowFast['angsuran_lewat'] ?? 0);
                    $dueSummary['lewat']['baki_debet'] += (float)($rowFast['angsuran_lewat_baki_debet'] ?? 0);
                    $dueSummary['tanpa_tanggal']['noa'] += (int)($rowFast['angsuran_tanpa_tanggal_noa'] ?? 0);
                    $dueSummary['tanpa_tanggal']['angsuran'] += (float)($rowFast['angsuran_tanpa_tanggal'] ?? 0);
                    $dueSummary['tanpa_tanggal']['baki_debet'] += (float)($rowFast['angsuran_tanpa_tanggal_baki_debet'] ?? 0);
                }

                $sqlPaid = "SELECT
                                paid.tgl,
                                CASE
                                    WHEN paid.tgl_bayar IS NULL THEN 'tanpa_tanggal'
                                    WHEN DAY(paid.tgl_bayar) <= paid.tgl THEN 'sesuai'
                                    ELSE 'lewat'
                                END AS bucket,
                                COUNT(1) AS noa,
                                COALESCE(SUM(paid.angsuran), 0) AS angsuran,
                                COALESCE(SUM(paid.target_os), 0) AS baki_debet
                            FROM (
                                SELECT
                                    {$dayMapCase} AS tgl,
                                    {$paidDiff} AS angsuran,
                                    t2.baki_debet AS target_os,
                                    tx.tgl_bayar
                                FROM nominatif t1
                                INNER JOIN nominatif t2 ON t1.no_rekening = t2.no_rekening
                                    AND t2.created BETWEEN :s2_paid AND :e2_paid
                                LEFT JOIN (
                                    SELECT no_rekening, MAX(tgl_trans) AS tgl_bayar
                                    FROM transaksi_kredit
                                    WHERE tgl_trans BETWEEN :tx_start_paid AND :tx_end_paid
                                    GROUP BY no_rekening
                                ) tx ON tx.no_rekening = t1.no_rekening
                                WHERE t1.created BETWEEN :s1_paid AND :e1_paid
                                  AND t1.kolektibilitas = 'L'
                                  AND t1.baki_debet > 0
                                  AND {$paidCondition}
                                  {$whereDpdFast}
                                  {$whereProdukFast}";
                if ($kc && $kc !== '000') $sqlPaid .= " AND t1.kode_cabang = :kc_paid";
                elseif ($korwil && $kw_start && $kw_end) $sqlPaid .= " AND t1.kode_cabang BETWEEN :kw_start_paid AND :kw_end_paid";
                if ($kankas) $sqlPaid .= " AND t1.kode_group1 = :kankas_paid";
                if ($ao) $sqlPaid .= " AND t1.kode_group2 = :ao_paid";
                $sqlPaid .= " GROUP BY t1.no_rekening, {$dayMapCase}, t1.baki_debet, t2.baki_debet, tx.tgl_bayar
                            ) paid
                            GROUP BY paid.tgl, bucket";

                $fastPaidStarted = microtime(true);
                $stmtPaid = $this->pdo->prepare($sqlPaid);
                $stmtPaid->bindValue(':s1_paid', $s1);
                $stmtPaid->bindValue(':e1_paid', $e1);
                $stmtPaid->bindValue(':s2_paid', $s2);
                $stmtPaid->bindValue(':e2_paid', $e2);
                $stmtPaid->bindValue(':tx_start_paid', $txStart);
                $stmtPaid->bindValue(':tx_end_paid', $txEnd);
                if ($kc && $kc !== '000') $stmtPaid->bindValue(':kc_paid', $kc);
                elseif ($korwil && $kw_start && $kw_end) {
                    $stmtPaid->bindValue(':kw_start_paid', $kw_start);
                    $stmtPaid->bindValue(':kw_end_paid', $kw_end);
                }
                if ($kankas) $stmtPaid->bindValue(':kankas_paid', $kankas);
                if ($ao) $stmtPaid->bindValue(':ao_paid', $ao);
                $stmtPaid->execute();
                $fastPaidMs = round((microtime(true) - $fastPaidStarted) * 1000);
                foreach ($stmtPaid->fetchAll(PDO::FETCH_ASSOC) as $paidRow) {
                    $tglPaid = (int)$paidRow['tgl'];
                    $bucket = $paidRow['bucket'] ?? 'tanpa_tanggal';
                    if (!isset($report[$tglPaid], $dueSummary[$bucket])) continue;

                    $noaPaid = (int)($paidRow['noa'] ?? 0);
                    $angsuranPaid = (float)($paidRow['angsuran'] ?? 0);
                    $bakiDebetPaid = (float)($paidRow['baki_debet'] ?? 0);
                    $fieldPrefix = $bucket === 'sesuai' ? 'angsuran_sesuai' : ($bucket === 'lewat' ? 'angsuran_lewat' : 'angsuran_tanpa_tanggal');

                    $report[$tglPaid][$fieldPrefix] += $angsuranPaid;
                    $report[$tglPaid][$fieldPrefix . '_noa'] += $noaPaid;
                    $report[$tglPaid][$fieldPrefix . '_baki_debet'] += $bakiDebetPaid;
                    $grandTotal[$fieldPrefix] += $angsuranPaid;
                    $grandTotal[$fieldPrefix . '_noa'] += $noaPaid;
                    $grandTotal[$fieldPrefix . '_baki_debet'] += $bakiDebetPaid;
                    $dueSummary[$bucket]['noa'] += $noaPaid;
                    $dueSummary[$bucket]['angsuran'] += $angsuranPaid;
                    $dueSummary[$bucket]['baki_debet'] += $bakiDebetPaid;
                }

                $dueSummary['total']['noa'] = $dueSummary['sesuai']['noa'] + $dueSummary['lewat']['noa'] + $dueSummary['tanpa_tanggal']['noa'];
                $dueSummary['total']['angsuran'] = $dueSummary['sesuai']['angsuran'] + $dueSummary['lewat']['angsuran'] + $dueSummary['tanpa_tanggal']['angsuran'];
                $dueSummary['total']['baki_debet'] = $dueSummary['sesuai']['baki_debet'] + $dueSummary['lewat']['baki_debet'] + $dueSummary['tanpa_tanggal']['baki_debet'];

                $cumTargetOs = 0;
                $cumLancarOs = 0;
                $cumLunasOs = 0;
                $cumAngsuran = 0;

                foreach ($report as &$fastReportRow) {
                    $fastReportRow['total_bayar'] = $fastReportRow['lunas_os'] + $fastReportRow['angsuran'];
                    $otpDenom = (float)$fastReportRow['lancar_os'];
                    $fastReportRow['angsuran_sesuai_persen'] = $otpDenom > 0 ? round(($fastReportRow['angsuran_sesuai_baki_debet'] / $otpDenom) * 100, 2) : 0;
                    $fastReportRow['angsuran_lewat_persen'] = $otpDenom > 0 ? round(($fastReportRow['angsuran_lewat_baki_debet'] / $otpDenom) * 100, 2) : 0;

                    if ($fastReportRow['tgl'] <= $actualDay) {
                        $cumTargetOs += $fastReportRow['target_os'];
                        $cumLancarOs += $fastReportRow['lancar_os'];
                        $cumLunasOs += $fastReportRow['lunas_os'];
                        $cumAngsuran += $fastReportRow['angsuran'];
                        $dailyPerformance = $fastReportRow['lancar_os'] + $fastReportRow['lunas_os'] + $fastReportRow['angsuran'];
                        $fastReportRow['persen'] = $fastReportRow['target_os'] > 0 ? round(($dailyPerformance / $fastReportRow['target_os']) * 100, 2) : 0;
                    } else {
                        $fastReportRow['persen'] = null;
                    }
                }
                unset($fastReportRow);

                $grandTotal['total_bayar'] = $grandTotal['lunas_os'] + $grandTotal['angsuran'];
                $grandOtpDenom = (float)$grandTotal['lancar_os'];
                $grandTotal['angsuran_sesuai_persen'] = $grandOtpDenom > 0 ? round(($grandTotal['angsuran_sesuai_baki_debet'] / $grandOtpDenom) * 100, 2) : 0;
                $grandTotal['angsuran_lewat_persen'] = $grandOtpDenom > 0 ? round(($grandTotal['angsuran_lewat_baki_debet'] / $grandOtpDenom) * 100, 2) : 0;
                $gtPerformanceCum = $cumLancarOs + $cumLunasOs + $cumAngsuran;
                $grandTotal['persen'] = $cumTargetOs > 0 ? round(($gtPerformanceCum / $cumTargetOs) * 100, 2) : 0;

                foreach (['sesuai', 'lewat', 'tanpa_tanggal'] as $summaryKey) {
                    $dueSummary[$summaryKey]['persen'] = $dueSummary['total']['angsuran'] > 0
                        ? round(($dueSummary[$summaryKey]['angsuran'] / $dueSummary['total']['angsuran']) * 100, 2)
                        : 0;
                }

                $reportRows = array_values(array_filter($report, function($r) {
                    return (float)$r['target_os'] > 0;
                }));

                $responseData = [
                    'meta' => ['m1' => $closing, 'cur' => $harian, 'include_127' => $include127, 'dpd_bucket' => $dpdBucket, 'cutoff_day' => $cutoffDay, 'actual_day' => $actualDay, 'mode' => 'fast', 'main_query_ms' => $fastMainMs, 'paid_query_ms' => $fastPaidMs],
                    'grand_total' => $grandTotal,
                    'due_summary' => $dueSummary,
                    'data' => $reportRows
                ];
                $this->putReportCache($reportCacheKey, $responseData);
                $this->send(200, "Sukses Rekap RR", $responseData);
            } catch (Exception $fastError) {
                // Fallback ke jalur lama jika SQL agregasi tidak cocok di server.
            }
            $whereDpdTarget = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);
            $whereProdukTarget = !$include127 ? $this->getKppExclusionWhere('t1') : "";
            $sqlRows = "SELECT
                            t1.no_rekening,
                            t1.baki_debet AS target_os,
                            DAY(t1.tgl_jatuh_tempo) AS tgl_ori,
                            t2.no_rekening AS rekening_harian,
                            COALESCE(t2.baki_debet, 0) AS os_actual,
                            COALESCE(t2.hari_menunggak, 0) AS dpd_actual,
                            0 AS nominal_bayar,
                            NULL AS tgl_bayar
                        FROM nominatif t1
                        LEFT JOIN nominatif t2 ON t1.no_rekening = t2.no_rekening
                            AND t2.created BETWEEN :s2 AND :e2
                        WHERE t1.created BETWEEN :s1 AND :e1
                          AND t1.kolektibilitas = 'L'
                          AND t1.baki_debet > 0
                          $whereDpdTarget
                          $whereProdukTarget";

            if ($kc && $kc !== '000') $sqlRows .= " AND t1.kode_cabang = :kc";
            elseif ($korwil && $kw_start && $kw_end) $sqlRows .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
            if ($kankas) $sqlRows .= " AND t1.kode_group1 = :kankas";
            if ($ao) $sqlRows .= " AND t1.kode_group2 = :ao";

            $stmtRows = $this->pdo->prepare($sqlRows);
            $stmtRows->bindValue(':s1', $s1); $stmtRows->bindValue(':e1', $e1);
            $stmtRows->bindValue(':s2', $s2); $stmtRows->bindValue(':e2', $e2);
            if ($kc && $kc !== '000') $stmtRows->bindValue(':kc', $kc);
            elseif ($korwil && $kw_start && $kw_end) { $stmtRows->bindValue(':kw_start', $kw_start); $stmtRows->bindValue(':kw_end', $kw_end); }
            if ($kankas) $stmtRows->bindValue(':kankas', $kankas);
            if ($ao) $stmtRows->bindValue(':ao', $ao);
            $stmtRows->execute();
            $otpRows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

            $trxMap = [];
            $sqlTrx = "SELECT
                            no_rekening,
                            COALESCE(SUM(COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)), 0) AS nominal_bayar,
                            MAX(tgl_trans) AS tgl_bayar
                        FROM transaksi_kredit
                        WHERE tgl_trans BETWEEN :tx_start AND :tx_end
                        GROUP BY no_rekening";
            $stmtTrx = $this->pdo->prepare($sqlTrx);
            $stmtTrx->bindValue(':tx_start', $txStart);
            $stmtTrx->bindValue(':tx_end', $txEnd);
            $stmtTrx->execute();
            foreach ($stmtTrx->fetchAll(PDO::FETCH_ASSOC) as $trxRow) {
                $trxMap[$trxRow['no_rekening']] = $trxRow;
            }

            $report = [];
            for ($i = 1; $i <= $cutoffDay; $i++) {
                $report[$i] = [
                    'tgl' => $i,
                    'target_noa' => 0, 'target_os' => 0,
                    'lancar_noa' => 0, 'lancar_os' => 0, 
                    'macet_noa'  => 0, 'macet_os'  => 0,
                    'lunas_noa'  => 0, 'lunas_os'  => 0,
                    'angsuran'   => 0, 'angsuran_sesuai' => 0, 'angsuran_sesuai_noa' => 0,
                    'angsuran_lewat' => 0, 'angsuran_lewat_noa' => 0,
                    'angsuran_tanpa_tanggal' => 0, 'angsuran_tanpa_tanggal_noa' => 0,
                    'angsuran_sesuai_baki_debet' => 0, 'angsuran_lewat_baki_debet' => 0,
                    'angsuran_tanpa_tanggal_baki_debet' => 0,
                    'angsuran_sesuai_persen' => 0, 'angsuran_lewat_persen' => 0,
                    'total_bayar'=> 0, 
                    'persen' => null 
                ];
            }
            $grandTotal = [
                'target_noa'=>0, 'target_os'=>0, 'lancar_noa'=>0, 'lancar_os'=>0, 
                'macet_noa'=>0,  'macet_os'=>0,  'lunas_noa'=>0,  'lunas_os'=>0, 
                'angsuran'=>0, 'angsuran_sesuai'=>0, 'angsuran_sesuai_noa'=>0,
                'angsuran_lewat'=>0, 'angsuran_lewat_noa'=>0,
                'angsuran_tanpa_tanggal'=>0, 'angsuran_tanpa_tanggal_noa'=>0,
                'angsuran_sesuai_baki_debet'=>0, 'angsuran_lewat_baki_debet'=>0,
                'angsuran_tanpa_tanggal_baki_debet'=>0,
                'angsuran_sesuai_persen'=>0, 'angsuran_lewat_persen'=>0,
                'total_bayar'=>0, 'persen'=>0
            ];
            $dueSummary = [
                'sesuai' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                'lewat'  => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                'tanpa_tanggal' => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 0],
                'total'  => ['noa' => 0, 'angsuran' => 0, 'baki_debet' => 0, 'persen' => 100]
            ];

            foreach ($otpRows as $row) {
                $tglOri = (int)$row['tgl_ori']; 
                if ($tglOri < 1 || $tglOri > 31) continue;

                $tglMap = $this->getMappedDay($tglOri, $curMonth, $curYear);
                if ($tglMap > $cutoffDay || !isset($report[$tglMap])) continue;
                $osTarget = (float)$row['target_os'];

                $report[$tglMap]['target_noa']++; $report[$tglMap]['target_os'] += $osTarget;
                $grandTotal['target_noa']++; $grandTotal['target_os'] += $osTarget;

                if (!empty($row['rekening_harian'])) {
                    $osActual  = (float)$row['os_actual'];
                    $dpdActual = (int)$row['dpd_actual'];

                    if ($osActual <= 0) {
                        $report[$tglMap]['lunas_noa']++; $report[$tglMap]['lunas_os'] += $osTarget;
                        $grandTotal['lunas_noa']++; $grandTotal['lunas_os'] += $osTarget;
                    } else {
                        if ($dpdActual == 0) {
                            $report[$tglMap]['lancar_noa']++; $report[$tglMap]['lancar_os'] += $osActual;
                            $grandTotal['lancar_noa']++; $grandTotal['lancar_os'] += $osActual;
                        } else {
                            $report[$tglMap]['macet_noa']++; $report[$tglMap]['macet_os'] += $osActual;
                            $grandTotal['macet_noa']++; $grandTotal['macet_os'] += $osActual;
                        }
                        if ($dpdActual == 0 && $osTarget > $osActual) {
                            $bayar = $osTarget - $osActual;
                            $report[$tglMap]['angsuran'] += $bayar;
                            $grandTotal['angsuran'] += $bayar;
                            $dueSummary['total']['noa']++;
                            $dueSummary['total']['angsuran'] += $bayar;
                            $dueSummary['total']['baki_debet'] += $osTarget;

                            if (!empty($trxMap[$row['no_rekening']]['tgl_bayar'])) {
                                $hariBayar = (int)date('j', strtotime($trxMap[$row['no_rekening']]['tgl_bayar']));
                                $bucketKey = $hariBayar <= $tglMap ? 'sesuai' : 'lewat';
                            } else {
                                $bucketKey = 'tanpa_tanggal';
                            }
                            if ($bucketKey === 'sesuai') {
                                $report[$tglMap]['angsuran_sesuai'] += $bayar;
                                $report[$tglMap]['angsuran_sesuai_noa']++;
                                $report[$tglMap]['angsuran_sesuai_baki_debet'] += $osActual;
                                $grandTotal['angsuran_sesuai'] += $bayar;
                                $grandTotal['angsuran_sesuai_noa']++;
                                $grandTotal['angsuran_sesuai_baki_debet'] += $osActual;
                            } elseif ($bucketKey === 'lewat') {
                                $report[$tglMap]['angsuran_lewat'] += $bayar;
                                $report[$tglMap]['angsuran_lewat_noa']++;
                                $report[$tglMap]['angsuran_lewat_baki_debet'] += $osActual;
                                $grandTotal['angsuran_lewat'] += $bayar;
                                $grandTotal['angsuran_lewat_noa']++;
                                $grandTotal['angsuran_lewat_baki_debet'] += $osActual;
                            } else {
                                $report[$tglMap]['angsuran_tanpa_tanggal'] += $bayar;
                                $report[$tglMap]['angsuran_tanpa_tanggal_noa']++;
                                $report[$tglMap]['angsuran_tanpa_tanggal_baki_debet'] += $osActual;
                                $grandTotal['angsuran_tanpa_tanggal'] += $bayar;
                                $grandTotal['angsuran_tanpa_tanggal_noa']++;
                                $grandTotal['angsuran_tanpa_tanggal_baki_debet'] += $osActual;
                            }
                            $dueSummary[$bucketKey]['noa']++;
                            $dueSummary[$bucketKey]['angsuran'] += $bayar;
                            $dueSummary[$bucketKey]['baki_debet'] += $osActual;
                        }
                    }
                } else {
                    $report[$tglMap]['lunas_noa']++; $report[$tglMap]['lunas_os'] += $osTarget;
                    $grandTotal['lunas_noa']++; $grandTotal['lunas_os'] += $osTarget;
                }
            }

            // Variabel penampung akumulasi khusus untuk menghitung Grand Total Persen Real
            $cumTargetOs = 0;
            $cumLancarOs = 0;
            $cumLunasOs  = 0;
            $cumAngsuran = 0;

            foreach ($report as &$r) {
                $r['total_bayar'] = $r['lunas_os'] + $r['angsuran'];
                $otpDenom = (float)$r['lancar_os'];
                $r['angsuran_sesuai_persen'] = $otpDenom > 0 ? round(($r['angsuran_sesuai_baki_debet'] / $otpDenom) * 100, 2) : 0;
                $r['angsuran_lewat_persen'] = $otpDenom > 0 ? round(($r['angsuran_lewat_baki_debet'] / $otpDenom) * 100, 2) : 0;
                $dailyPerformance = $r['lancar_os'] + $r['lunas_os'] + $r['angsuran'];
                
                if ($r['tgl'] <= $actualDay) {
                    // Masukkan ke perhitungan akumulasi Grand Total Persen Real (Sesuai Rumus Gambar Excel)
                    $cumTargetOs += $r['target_os'];
                    $cumLancarOs += $r['lancar_os'];
                    $cumLunasOs  += $r['lunas_os'];
                    $cumAngsuran += $r['angsuran'];

                    if ($r['target_os'] > 0) {
                        $r['persen'] = round(($dailyPerformance / $r['target_os']) * 100, 2);
                    } else {
                        $r['persen'] = 0.00;
                    }
                } else {
                    $r['persen'] = null; 
                }
            }
            unset($r);

            // 🔥 PERBAIKAN UTAMA: Rumus Grand Total % Akumulatif s.d Hari Aktual (Bukan Full Sebulan)
            $grandTotal['total_bayar'] = $grandTotal['lunas_os'] + $grandTotal['angsuran'];
            $grandOtpDenom = (float)$grandTotal['lancar_os'];
            $grandTotal['angsuran_sesuai_persen'] = $grandOtpDenom > 0 ? round(($grandTotal['angsuran_sesuai_baki_debet'] / $grandOtpDenom) * 100, 2) : 0;
            $grandTotal['angsuran_lewat_persen'] = $grandOtpDenom > 0 ? round(($grandTotal['angsuran_lewat_baki_debet'] / $grandOtpDenom) * 100, 2) : 0;
            
            $gtPerformanceCum = $cumLancarOs + $cumLunasOs + $cumAngsuran;
            if ($cumTargetOs > 0) {
                $grandTotal['persen'] = round(($gtPerformanceCum / $cumTargetOs) * 100, 2);
            } else {
                $grandTotal['persen'] = 0.00;
            }

            // 🔥 Saring data: Buang baris tanggal yang tidak memiliki target jatuh tempo (target_os == 0)
            foreach (['sesuai', 'lewat', 'tanpa_tanggal'] as $summaryKey) {
                $dueSummary[$summaryKey]['persen'] = $dueSummary['total']['angsuran'] > 0
                    ? round(($dueSummary[$summaryKey]['angsuran'] / $dueSummary['total']['angsuran']) * 100, 2)
                    : 0;
            }

            $reportRows = array_values(array_filter($report, function($r) {
                return (float)$r['target_os'] > 0;
            }));

            $responseData = [
                'meta' => ['m1' => $closing, 'cur' => $harian, 'include_127' => $include127, 'dpd_bucket' => $dpdBucket, 'cutoff_day' => $cutoffDay, 'actual_day' => $actualDay],
                'grand_total' => $grandTotal,
                'due_summary' => $dueSummary,
                'data' => $reportRows
            ];
            $this->putReportCache($reportCacheKey, $responseData);
            $this->send(200, "Sukses Rekap RR", $responseData);
    }

    /**
     * 2. DETAIL DATA
     * Menampilkan data Lancar & Menunggak (OTP  RR)
     */
    public function getRepaymentRateArea($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        try {
            $b = is_array($input) ? $input : [];
            $closing = $b['closing_date'] ?? null;
            $harian  = $b['harian_date'] ?? null;
            $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
            $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
            $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
            $ao      = !empty($b['kode_ao']) ? $b['kode_ao'] : null;
            $dpdBucket = $b['dpd_bucket'] ?? 'dpd0';
            $include127 = filter_var($b['include_127'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $whereProduk = !$include127 ? $this->getKppExclusionWhere('t1') : "";

            if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

            [$s1, $e1] = $this->getDayRange($closing);
            $whereDpd = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);

            $kw_start = null; $kw_end = null;
            if ($korwil) {
                switch ($korwil) {
                    case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                    case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                    case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                    case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
                }
            }

            $groupCode = ($kc && $kc !== '000') ? 't1.kode_group1' : 't1.kode_cabang';
            $groupName = ($kc && $kc !== '000') ? "COALESCE(kn.deskripsi_group1, t1.kode_group1)" : "COALESCE(kk.nama_kantor, t1.kode_cabang)";

            $sql = "SELECT
                        $groupCode AS kode_area,
                        $groupName AS nama_area,
                        COUNT(1) AS target_noa,
                        SUM(t1.baki_debet) AS target_os,
                        SUM(CASE WHEN t2.baki_debet > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 THEN 1 ELSE 0 END) AS lancar_noa,
                        SUM(CASE WHEN t2.baki_debet > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 THEN t2.baki_debet ELSE 0 END) AS lancar_os,
                        SUM(CASE WHEN t2.baki_debet > 0 AND COALESCE(t2.hari_menunggak, 0) > 0 THEN 1 ELSE 0 END) AS macet_noa,
                        SUM(CASE WHEN t2.baki_debet > 0 AND COALESCE(t2.hari_menunggak, 0) > 0 THEN t2.baki_debet ELSE 0 END) AS macet_os,
                        SUM(CASE WHEN t2.no_rekening IS NULL OR t2.baki_debet <= 0 THEN 1 ELSE 0 END) AS lunas_noa,
                        SUM(CASE WHEN t2.no_rekening IS NULL OR t2.baki_debet <= 0 THEN t1.baki_debet ELSE 0 END) AS lunas_os,
                        SUM(CASE WHEN t2.baki_debet > 0 AND t2.baki_debet < t1.baki_debet THEN (t1.baki_debet - t2.baki_debet) ELSE 0 END) AS angsuran
                    FROM nominatif t1
                    LEFT JOIN nominatif t2 ON t1.no_rekening = t2.no_rekening AND t2.created BETWEEN :s2 AND :e2
                    LEFT JOIN kode_kantor kk ON t1.kode_cabang = kk.kode_kantor
                    LEFT JOIN kankas kn ON t1.kode_group1 = kn.kode_group1
                    WHERE t1.created BETWEEN :s1 AND :e1
                      AND t1.kolektibilitas = 'L'
                      AND t1.baki_debet > 0
                      $whereDpd
                      $whereProduk";

            if ($kc && $kc !== '000') $sql .= " AND t1.kode_cabang = :kc";
            elseif ($korwil && $kw_start && $kw_end) $sql .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
            if ($kankas) $sql .= " AND t1.kode_group1 = :kankas";
            if ($ao) $sql .= " AND t1.kode_group2 = :ao";
            $sql .= " GROUP BY kode_area, nama_area ORDER BY kode_area ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':s1', $s1); $stmt->bindValue(':e1', $e1);
            $stmt->bindValue(':s2', $s2); $stmt->bindValue(':e2', $e2);
            if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
            elseif ($korwil && $kw_start && $kw_end) { $stmt->bindValue(':kw_start', $kw_start); $stmt->bindValue(':kw_end', $kw_end); }
            if ($kankas) $stmt->bindValue(':kankas', $kankas);
            if ($ao) $stmt->bindValue(':ao', $ao);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total = ['kode_area'=>'TOTAL','nama_area'=>'TOTAL','target_noa'=>0,'target_os'=>0,'lancar_noa'=>0,'lancar_os'=>0,'macet_noa'=>0,'macet_os'=>0,'lunas_noa'=>0,'lunas_os'=>0,'angsuran'=>0,'total_bayar'=>0,'persen'=>0];
            foreach ($rows as &$r) {
                foreach (['target_noa','lancar_noa','macet_noa','lunas_noa'] as $k) $r[$k] = (int)($r[$k] ?? 0);
                foreach (['target_os','lancar_os','macet_os','lunas_os','angsuran'] as $k) $r[$k] = (float)($r[$k] ?? 0);
                $r['total_bayar'] = $r['lunas_os'] + $r['angsuran'];
                $r['persen'] = $r['target_os'] > 0 ? round(($r['total_bayar'] / $r['target_os']) * 100, 2) : 0;
                foreach ($total as $k => $v) if (is_numeric($v) && isset($r[$k])) $total[$k] += $r[$k];
            }
            unset($r);
            $total['total_bayar'] = $total['lunas_os'] + $total['angsuran'];
            $total['persen'] = $total['target_os'] > 0 ? round(($total['total_bayar'] / $total['target_os']) * 100, 2) : 0;

            $this->send(200, "Rekap Area RR", ['summary' => $total, 'data' => $rows]);
        } catch (Exception $e) {
            $this->send(500, "Error Rekap Area RR: " . $e->getMessage());
        }
    }

    public function getRepaymentRateCollectionArea($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        try {
            $b = is_array($input) ? $input : [];
            $closing = $b['closing_date'] ?? null;
            $harian  = $b['harian_date'] ?? null;
            $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
            $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
            $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null;
            $ao      = !empty($b['kode_ao']) ? $b['kode_ao'] : null;
            $dpdBucket = $b['dpd_bucket'] ?? 'dpd0';
            $include127 = filter_var($b['include_127'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $whereProduk = !$include127 ? $this->getKppExclusionWhere('t1') : "";

            if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

            [$s1, $e1] = $this->getDayRange($closing);
            [$s2, $e2] = $this->getDayRange($harian);
            $txStart = date('Y-m-01 00:00:00', strtotime($harian));
            $txEnd = date('Y-m-d 23:59:59', strtotime($harian));
            $komitStart = date('Y-m-01 00:00:00', strtotime($harian));
            $komitEnd = date('Y-m-d 23:59:59', strtotime($harian));
            $whereDpd = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);

            $kw_start = null; $kw_end = null;
            if ($korwil) {
                switch ($korwil) {
                    case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                    case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                    case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                    case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
                }
            }

            $groupCode = ($kc && $kc !== '000') ? 't1.kode_group1' : 't1.kode_cabang';
            $groupName = ($kc && $kc !== '000') ? 't1.kode_group1' : 't1.kode_cabang';

            $sqlTarget = "SELECT
                              $groupCode AS kode_area,
                              $groupName AS nama_area,
                              t1.no_rekening,
                              t1.baki_debet AS os_m1
                          FROM nominatif t1
                          WHERE t1.created BETWEEN :s1 AND :e1
                            AND t1.kolektibilitas = 'L'
                            AND t1.baki_debet > 0
                            $whereDpd
                            $whereProduk";
            if ($kc && $kc !== '000') $sqlTarget .= " AND t1.kode_cabang = :kc";
            elseif ($korwil && $kw_start && $kw_end) $sqlTarget .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
            if ($kankas) $sqlTarget .= " AND t1.kode_group1 = :kankas";
            if ($ao) $sqlTarget .= " AND t1.kode_group2 = :ao";
            $sqlTarget .= " ORDER BY kode_area ASC";
            $timingStart = microtime(true);
            $timing = [];

            $stmtTarget = $this->pdo->prepare($sqlTarget);
            $stmtTarget->bindValue(':s1', $s1);
            $stmtTarget->bindValue(':e1', $e1);
            if ($kc && $kc !== '000') $stmtTarget->bindValue(':kc', $kc);
            elseif ($korwil && $kw_start && $kw_end) {
                $stmtTarget->bindValue(':kw_start', $kw_start);
                $stmtTarget->bindValue(':kw_end', $kw_end);
            }
            if ($kankas) $stmtTarget->bindValue(':kankas', $kankas);
            if ($ao) $stmtTarget->bindValue(':ao', $ao);
            $stmtTarget->execute();
            $targetRows = $stmtTarget->fetchAll(PDO::FETCH_ASSOC);
            $areaNameMap = [];
            if ($kc && $kc !== '000') {
                $stmtAreaName = $this->pdo->query("SELECT kode_group1 AS kode_area, deskripsi_group1 AS nama_area FROM kankas");
            } else {
                $stmtAreaName = $this->pdo->query("SELECT kode_kantor AS kode_area, nama_kantor AS nama_area FROM kode_kantor");
            }
            foreach ($stmtAreaName->fetchAll(PDO::FETCH_ASSOC) as $areaNameRow) {
                $areaNameMap[$areaNameRow['kode_area']] = $areaNameRow['nama_area'];
            }
            foreach ($targetRows as &$targetNameRow) {
                $targetCode = $targetNameRow['kode_area'];
                $targetNameRow['nama_area'] = $areaNameMap[$targetCode] ?? $targetCode;
            }
            unset($targetNameRow);
            $timing['target_ms'] = round((microtime(true) - $timingStart) * 1000);

            $rekeningList = [];
            foreach ($targetRows as $targetRow) {
                if (!empty($targetRow['no_rekening'])) $rekeningList[] = $targetRow['no_rekening'];
            }
            $rekeningList = array_values(array_unique($rekeningList));

            $trxMap = [];
            $actualMap = [];
            $komitMap = [];
            $komitFreqMap = [];
            $chunkSize = 1000;

            $stmtActualAll = $this->pdo->prepare("SELECT no_rekening, baki_debet
                FROM nominatif
                WHERE created BETWEEN :s2_actual AND :e2_actual");
            $stmtActualAll->bindValue(':s2_actual', $s2);
            $stmtActualAll->bindValue(':e2_actual', $e2);
            $stmtActualAll->execute();
            foreach ($stmtActualAll->fetchAll(PDO::FETCH_ASSOC) as $actualRow) {
                $actualMap[$actualRow['no_rekening']] = (float)($actualRow['baki_debet'] ?? 0);
            }

            $stmtFreqGlobal = $this->pdo->prepare("SELECT rekening, COUNT(1) AS frekuensi_monitoring
                FROM report_komitmen
                WHERE tanggal BETWEEN :ks AND :ke
                GROUP BY rekening");
            $stmtFreqGlobal->bindValue(':ks', $komitStart);
            $stmtFreqGlobal->bindValue(':ke', $komitEnd);
            $stmtFreqGlobal->execute();
            foreach ($stmtFreqGlobal->fetchAll(PDO::FETCH_ASSOC) as $freqRow) {
                $komitFreqMap[$freqRow['rekening']] = (int)($freqRow['frekuensi_monitoring'] ?? 0);
            }
            $timing['komit_freq_ms'] = round((microtime(true) - $timingStart) * 1000) - array_sum($timing);

            $latestStart = microtime(true);
            $stmtKomitGlobal = $this->pdo->prepare("SELECT rk.rekening,
                       SUM(COALESCE(rk.nom_pok, 0) + COALESCE(rk.nom_bung, 0)) AS nominal_janji
                FROM report_komitmen rk
                JOIN (
                    SELECT rekening, MAX(tanggal) AS latest_created
                    FROM report_komitmen
                    WHERE tanggal BETWEEN :ks_latest AND :ke_latest
                    GROUP BY rekening
                ) latest ON latest.rekening = rk.rekening AND latest.latest_created = rk.tanggal
                GROUP BY rk.rekening");
            $stmtKomitGlobal->bindValue(':ks_latest', $komitStart);
            $stmtKomitGlobal->bindValue(':ke_latest', $komitEnd);
            $stmtKomitGlobal->execute();
            foreach ($stmtKomitGlobal->fetchAll(PDO::FETCH_ASSOC) as $komitRow) {
                $komitMap[$komitRow['rekening']] = (float)($komitRow['nominal_janji'] ?? 0);
            }
            $timing['komit_latest_ms'] = round((microtime(true) - $latestStart) * 1000);

            $rowsByArea = [];
            foreach ($targetRows as $targetRow) {
                $rek = $targetRow['no_rekening'];
                $areaCode = $targetRow['kode_area'] ?: '-';
                $areaName = $targetRow['nama_area'] ?: $areaCode;
                if (!isset($rowsByArea[$areaCode])) {
                    $rowsByArea[$areaCode] = [
                        'kode_area' => $areaCode, 'nama_area' => $areaName,
                        'target_noa' => 0, 'call_noa' => 0, 'call_frekuensi' => 0,
                        'nc_noa' => 0, 'nc_os' => 0, 'nc_bayar' => 0,
                        'ptp_noa' => 0, 'ptp_os' => 0, 'ptp_bayar' => 0,
                        'po_noa' => 0, 'po_os' => 0, 'po_bayar' => 0,
                        'call_percent' => 0
                    ];
                }

                $osM1 = (float)($targetRow['os_m1'] ?? 0);
                if (!array_key_exists($rek, $actualMap) || $actualMap[$rek] <= 0) {
                    $pembayaran = $osM1;
                } elseif ($osM1 > $actualMap[$rek]) {
                    $pembayaran = $osM1 - $actualMap[$rek];
                } else {
                    $pembayaran = 0;
                }
                $nominalJanji = (float)($komitMap[$rek] ?? 0);
                $frekuensi = (int)($komitFreqMap[$rek] ?? 0);

                if ($nominalJanji <= 0 && $pembayaran <= 0) $status = 'NC';
                elseif ($nominalJanji <= 0 && $pembayaran > 0) $status = 'PO';
                elseif ($nominalJanji > 0 && $pembayaran <= 0) $status = 'PTP';
                elseif ($pembayaran >= ($nominalJanji * 0.8)) $status = 'PO';
                else $status = 'PTP';

                $row =& $rowsByArea[$areaCode];
                $row['target_noa']++;
                if ($frekuensi > 0) $row['call_noa']++;
                $row['call_frekuensi'] += $frekuensi;
                $prefix = strtolower($status);
                $row[$prefix . '_noa']++;
                $row[$prefix . '_os'] += $osM1;
                $row[$prefix . '_bayar'] += $pembayaran;
                unset($row);
            }

            $rows = array_values($rowsByArea);
            $total = ['kode_area'=>'TOTAL','nama_area'=>'TOTAL','target_noa'=>0,'call_noa'=>0,'call_frekuensi'=>0,'nc_noa'=>0,'nc_os'=>0,'nc_bayar'=>0,'ptp_noa'=>0,'ptp_os'=>0,'ptp_bayar'=>0,'po_noa'=>0,'po_os'=>0,'po_bayar'=>0,'call_percent'=>0];
            foreach ($rows as &$r) {
                foreach (['target_noa','call_noa','call_frekuensi','nc_noa','ptp_noa','po_noa'] as $k) $r[$k] = (int)($r[$k] ?? 0);
                foreach (['nc_os','nc_bayar','ptp_os','ptp_bayar','po_os','po_bayar'] as $k) $r[$k] = (float)($r[$k] ?? 0);
                $r['call_percent'] = $r['target_noa'] > 0 ? round(($r['call_noa'] / $r['target_noa']) * 100, 2) : 0;
                foreach ($total as $k => $v) if (isset($r[$k]) && is_numeric($v)) $total[$k] += $r[$k];
            }
            unset($r);
            $total['call_percent'] = $total['target_noa'] > 0 ? round(($total['call_noa'] / $total['target_noa']) * 100, 2) : 0;
            $timing['total_ms'] = round((microtime(true) - $timingStart) * 1000);

            $this->send(200, "Rekap Collection RR", ['summary' => $total, 'data' => $rows, 'meta' => ['timing' => $timing]]);
        } catch (Exception $e) {
            $this->send(500, "Error Rekap Collection RR: " . $e->getMessage());
        }
    }

    public function getDetailRepaymentRate($input = null) {
        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = $b['kode_kankas'] ?? null; 
        $ao      = $b['kode_ao'] ?? null;     
        $search  = trim($b['search'] ?? '');
        $dpdBucket = $b['dpd_bucket'] ?? 'dpd0'; // dpd0, dpd1-30, all
        $saldoCol = $this->getSaldoColumn($b);
        $t1Saldo = "COALESCE(t1.{$saldoCol}, 0)";
        $t2Saldo = "COALESCE(t2.{$saldoCol}, 0)";
        $statusBayar = strtolower(trim((string)($b['status_bayar'] ?? 'all')));
        $statusTunggakan = strtolower(trim((string)($b['status_tunggakan'] ?? 'all')));
        $statusPembayaran = strtoupper(trim((string)($b['status_pembayaran'] ?? 'ALL')));
        if (!in_array($statusBayar, ['all', 'sudah_bayar', 'belum_bayar'], true)) $statusBayar = 'all';
        if (!in_array($statusTunggakan, ['all', 'nol', 'lebih'], true)) $statusTunggakan = 'all';
        if (!in_array($statusPembayaran, ['ALL', 'OTP', 'TELAT', 'BELUM_JATUH_TEMPO', 'BELUM_BAYAR'], true)) $statusPembayaran = 'ALL';
        
        $tglTagih = $b['tgl_tagih'] ?? 'ALL'; 
        $status  = strtoupper($b['status'] ?? 'ALL');
        $collectionStatus = strtoupper($b['collection_status'] ?? 'ALL');
        if (!in_array($collectionStatus, ['ALL', 'CALL', 'NC', 'PTP', 'PO'], true)) $collectionStatus = 'ALL';
        $page    = $b['page'] ?? 1;
        $limit   = $b['limit'] ?? 10;
        $offset  = ($page - 1) * $limit;

        // 🔥 Tangkap parameter include_127 dari FE
        $include127 = filter_var($b['include_127'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $whereProduk = !$include127 ? $this->getKppExclusionWhere('t1') : "";

        if (!$closing || !$harian) return $this->send(400, "Data kurang lengkap.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);
        $txCurStart = date('Y-m-01 00:00:00', strtotime($harian));
        $txCurEnd = date('Y-m-d 23:59:59', strtotime($harian));
        $txPrevStart = date('Y-m-01 00:00:00', strtotime($harian . ' -1 month'));
        $txPrevEnd = date('Y-m-t 23:59:59', strtotime($harian . ' -1 month'));
        $komitStart = date('Y-m-01 00:00:00', strtotime($harian));
        $komitEnd = date('Y-m-d 23:59:59', strtotime($harian));
        $kw_start = null; $kw_end = null;
        $bindTxParams = function($stmt, $withKomit = true) use ($txCurStart, $txCurEnd, $txPrevStart, $txPrevEnd, $komitStart, $komitEnd) {
            $stmt->bindValue(':tx_cur_start_sum', $txCurStart);
            $stmt->bindValue(':tx_cur_end_sum', $txCurEnd);
            $stmt->bindValue(':tx_cur_start_max', $txCurStart);
            $stmt->bindValue(':tx_cur_end_max', $txCurEnd);
            $stmt->bindValue(':tx_prev_start_sum', $txPrevStart);
            $stmt->bindValue(':tx_prev_end_sum', $txPrevEnd);
            $stmt->bindValue(':tx_prev_start_max', $txPrevStart);
            $stmt->bindValue(':tx_prev_end_max', $txPrevEnd);
            $stmt->bindValue(':tx_range_start', $txPrevStart);
            $stmt->bindValue(':tx_range_end', $txCurEnd);
            if ($withKomit) {
                $stmt->bindValue(':komit_start', $komitStart);
                $stmt->bindValue(':komit_end', $komitEnd);
            }
        };

        // 🔥 Filter DPD Bucket di M-1
        $whereDpd = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);

        // 🔥 Korwil mapping
        if ($korwil) {
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
        }

        $detailDueDays = strtoupper((string)$tglTagih) === 'ALL'
            ? range(1, 31)
            : $this->getDueDaysForRequest($harian, $tglTagih);
        $daysStr = implode(',', $detailDueDays);
        $detailMonth = (int)date('n', strtotime($harian));
        $detailYear = (int)date('Y', strtotime($harian));
        $detailDayMapParts = [];
        for ($day = 1; $day <= 31; $day++) {
            $detailDayMapParts[] = "WHEN {$day} THEN " . $this->getMappedDay($day, $detailMonth, $detailYear);
        }
        $detailDayMapCase = "CASE DAY(t1.tgl_jatuh_tempo) " . implode(' ', $detailDayMapParts) . " ELSE DAY(t1.tgl_jatuh_tempo) END";

        if ($status === 'ALL' && strtoupper((string)$tglTagih) === 'ALL') {
            $sqlFastTarget = "SELECT
                                  t1.no_rekening,
                                  t1.nama_nasabah,
                                  t1.kode_produk,
                                  t1.alamat,
                                  t1.hp AS no_hp,
                                  t1.kode_group1 AS kode_kankas,
                                  t1.kode_group2 AS kode_ao,
                                  t1.norek_tabungan,
                                  t1.tgl_jatuh_tempo,
                                  t1.jml_pinjaman,
                                  t1.{$saldoCol} AS os_m1
                              FROM nominatif t1
                              WHERE t1.created BETWEEN :s1_fast_detail AND :e1_fast_detail
                                AND t1.kolektibilitas = 'L'
                                AND {$t1Saldo} > 0
                                $whereDpd
                                AND DAY(t1.tgl_jatuh_tempo) IN ($daysStr)
                                $whereProduk";
            if ($kc && $kc !== '000') $sqlFastTarget .= " AND t1.kode_cabang = :kc_fast_detail";
            elseif ($korwil && $kw_start && $kw_end) $sqlFastTarget .= " AND t1.kode_cabang BETWEEN :kw_start_fast_detail AND :kw_end_fast_detail";
            if ($kankas) $sqlFastTarget .= " AND t1.kode_group1 = :kankas_fast_detail";
            if ($ao) $sqlFastTarget .= " AND t1.kode_group2 = :ao_fast_detail";
            if ($search !== '') $sqlFastTarget .= " AND (t1.no_rekening LIKE :search_fast_detail OR t1.nama_nasabah LIKE :search2_fast_detail OR t1.hp LIKE :search3_fast_detail)";
            $sqlFastTarget .= " ORDER BY {$t1Saldo} DESC";

            $needsPostFilterFast = $statusBayar !== 'all' || $statusTunggakan !== 'all' || $statusPembayaran !== 'ALL';
            $isPrePagedFast = $collectionStatus === 'ALL' && !$needsPostFilterFast;
            $totalFastPre = null;
            $bindFastTarget = function($stmt) use ($s1, $e1, $kc, $korwil, $kw_start, $kw_end, $kankas, $ao, $search) {
                $stmt->bindValue(':s1_fast_detail', $s1);
                $stmt->bindValue(':e1_fast_detail', $e1);
                if ($kc && $kc !== '000') $stmt->bindValue(':kc_fast_detail', $kc);
                elseif ($korwil && $kw_start && $kw_end) {
                    $stmt->bindValue(':kw_start_fast_detail', $kw_start);
                    $stmt->bindValue(':kw_end_fast_detail', $kw_end);
                }
                if ($kankas) $stmt->bindValue(':kankas_fast_detail', $kankas);
                if ($ao) $stmt->bindValue(':ao_fast_detail', $ao);
                if ($search !== '') {
                    $stmt->bindValue(':search_fast_detail', "%$search%");
                    $stmt->bindValue(':search2_fast_detail', "%$search%");
                    $stmt->bindValue(':search3_fast_detail', "%$search%");
                }
            };

            if ($isPrePagedFast) {
                $sqlFastCount = preg_replace('/SELECT\s+.*?\s+FROM nominatif t1/s', 'SELECT COUNT(1) FROM nominatif t1', $sqlFastTarget, 1);
                $sqlFastCount = preg_replace('/\s+ORDER BY.*$/s', '', $sqlFastCount);
                $stmtFastCount = $this->pdo->prepare($sqlFastCount);
                $bindFastTarget($stmtFastCount);
                $stmtFastCount->execute();
                $totalFastPre = (int)$stmtFastCount->fetchColumn();
                $sqlFastTarget .= " LIMIT :limit_fast_detail OFFSET :offset_fast_detail";
            }

            $stmtFastTarget = $this->pdo->prepare($sqlFastTarget);
            $bindFastTarget($stmtFastTarget);
            if ($isPrePagedFast) {
                $stmtFastTarget->bindValue(':limit_fast_detail', $limit, PDO::PARAM_INT);
                $stmtFastTarget->bindValue(':offset_fast_detail', $offset, PDO::PARAM_INT);
            }
            $stmtFastTarget->execute();
            $targetRowsFast = $stmtFastTarget->fetchAll(PDO::FETCH_ASSOC);

            $actualMap = [];
            if ($isPrePagedFast) {
                $pageLookupReks = array_values(array_filter(array_column($targetRowsFast, 'no_rekening')));
                if (!empty($pageLookupReks)) {
                    $actualPh = [];
                    $actualParams = [':s2_fast_detail' => $s2, ':e2_fast_detail' => $e2];
                    foreach ($pageLookupReks as $idx => $rek) {
                        $key = ':actual_rek_fast_detail_' . $idx;
                        $actualPh[] = $key;
                        $actualParams[$key] = $rek;
                    }
                    $stmtActualFast = $this->pdo->prepare("SELECT no_rekening, {$saldoCol} AS saldo_terpilih, hari_menunggak, tunggakan_pokok, tunggakan_bunga
                        FROM nominatif
                        WHERE no_rekening IN (" . implode(',', $actualPh) . ")
                          AND created BETWEEN :s2_fast_detail AND :e2_fast_detail");
                    foreach ($actualParams as $key => $value) $stmtActualFast->bindValue($key, $value);
                    $stmtActualFast->execute();
                    foreach ($stmtActualFast->fetchAll(PDO::FETCH_ASSOC) as $actualRow) {
                        $actualMap[$actualRow['no_rekening']] = $actualRow;
                    }
                }
            } else {
                $stmtActualFast = $this->pdo->prepare("SELECT no_rekening, {$saldoCol} AS saldo_terpilih, hari_menunggak, tunggakan_pokok, tunggakan_bunga
                    FROM nominatif
                    WHERE created BETWEEN :s2_fast_detail AND :e2_fast_detail");
                $stmtActualFast->bindValue(':s2_fast_detail', $s2);
                $stmtActualFast->bindValue(':e2_fast_detail', $e2);
                $stmtActualFast->execute();
                foreach ($stmtActualFast->fetchAll(PDO::FETCH_ASSOC) as $actualRow) {
                    $actualMap[$actualRow['no_rekening']] = $actualRow;
                }
            }

            $komitMap = [];
            $stmtKomitFast = $this->pdo->prepare("SELECT rk.rekening,
                       SUM(COALESCE(rk.nom_pok, 0)) AS nominal_pokok,
                       SUM(COALESCE(rk.nom_bung, 0)) AS nominal_bunga,
                       SUM(COALESCE(rk.nom_pok, 0) + COALESCE(rk.nom_bung, 0)) AS nominal_janji,
                       COUNT(1) AS frekuensi_monitoring,
                       MAX(rk.tgl_komit) AS tgl_komitmen,
                       MAX(rk.tanggal) AS created,
                       MAX(rk.kendala) AS keterangan
                FROM report_komitmen rk
                JOIN (
                    SELECT rekening, MAX(tanggal) AS latest_created
                    FROM report_komitmen
                    WHERE tanggal BETWEEN :komit_start_fast_detail AND :komit_end_fast_detail
                    GROUP BY rekening
                ) latest ON latest.rekening = rk.rekening AND latest.latest_created = rk.tanggal
                GROUP BY rk.rekening");
            $stmtKomitFast->bindValue(':komit_start_fast_detail', $komitStart);
            $stmtKomitFast->bindValue(':komit_end_fast_detail', $komitEnd);
            $stmtKomitFast->execute();
            foreach ($stmtKomitFast->fetchAll(PDO::FETCH_ASSOC) as $komitRow) {
                $komitMap[$komitRow['rekening']] = $komitRow;
            }

            $filteredFast = [];
            foreach ($targetRowsFast as $rowFast) {
                $rek = $rowFast['no_rekening'];
                $osM1 = (float)($rowFast['os_m1'] ?? 0);
                $actual = $actualMap[$rek] ?? null;
                $osCur = $actual ? (float)($actual['saldo_terpilih'] ?? 0) : 0;
                $pembayaran = (!$actual || $osCur <= 0) ? $osM1 : max(0, $osM1 - $osCur);
                $komit = $komitMap[$rek] ?? [];
                $nominalJanji = (float)($komit['nominal_janji'] ?? 0);

                if ($nominalJanji <= 0 && $pembayaran <= 0) $rowCollectionStatus = 'NC';
                elseif ($nominalJanji <= 0 && $pembayaran > 0) $rowCollectionStatus = 'PO';
                elseif ($nominalJanji > 0 && $pembayaran <= 0) $rowCollectionStatus = 'PTP';
                elseif ($pembayaran >= ($nominalJanji * 0.8)) $rowCollectionStatus = 'PO';
                else $rowCollectionStatus = 'PTP';

                if ($collectionStatus === 'CALL' && empty($komitMap[$rek])) continue;
                if (!in_array($collectionStatus, ['ALL', 'CALL'], true) && $rowCollectionStatus !== $collectionStatus) continue;

                $rowFast['_collection_status'] = $rowCollectionStatus;
                $filteredFast[] = $rowFast;
            }

            $totalFast = $isPrePagedFast ? (int)$totalFastPre : count($filteredFast);
            $pageRows = $isPrePagedFast ? $filteredFast : array_slice($filteredFast, $offset, $limit);
            $pageReks = array_values(array_filter(array_column($pageRows, 'no_rekening')));
            $trxMap = [];
            $tabunganMap = [];
            $aoMap = [];
            $kankasMap = [];

            foreach ($this->pdo->query("SELECT kode_group2, nama_ao FROM ao_kredit")->fetchAll(PDO::FETCH_ASSOC) as $aoRow) {
                $aoMap[$aoRow['kode_group2']] = $aoRow['nama_ao'];
            }
            foreach ($this->pdo->query("SELECT kode_group1, deskripsi_group1 FROM kankas")->fetchAll(PDO::FETCH_ASSOC) as $kankasRow) {
                $kankasMap[$kankasRow['kode_group1']] = $kankasRow['deskripsi_group1'];
            }

            if (!empty($pageReks)) {
                $ph = [];
                $params = [
                    ':tx_prev_start_fast_detail' => $txPrevStart,
                    ':tx_cur_end_fast_detail' => $txCurEnd,
                    ':tx_cur_start_sum_fast_detail' => $txCurStart,
                    ':tx_cur_end_sum_fast_detail' => $txCurEnd,
                    ':tx_cur_start_max_fast_detail' => $txCurStart,
                    ':tx_cur_end_max_fast_detail' => $txCurEnd,
                    ':tx_prev_start_sum_fast_detail' => $txPrevStart,
                    ':tx_prev_end_sum_fast_detail' => $txPrevEnd,
                    ':tx_prev_start_max_fast_detail' => $txPrevStart,
                    ':tx_prev_end_max_fast_detail' => $txPrevEnd
                ];
                foreach ($pageReks as $idx => $rek) {
                    $key = ':rek_fast_detail_' . $idx;
                    $ph[] = $key;
                    $params[$key] = $rek;
                }
                $stmtTrxFast = $this->pdo->prepare("SELECT no_rekening,
                           SUM(CASE WHEN tgl_trans BETWEEN :tx_cur_start_sum_fast_detail AND :tx_cur_end_sum_fast_detail THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) AS trx_bulan_ini,
                           MAX(CASE WHEN tgl_trans BETWEEN :tx_cur_start_max_fast_detail AND :tx_cur_end_max_fast_detail THEN tgl_trans END) AS tgl_bayar_ini,
                           SUM(CASE WHEN tgl_trans BETWEEN :tx_prev_start_sum_fast_detail AND :tx_prev_end_sum_fast_detail THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) AS trx_bulan_lalu,
                           MAX(CASE WHEN tgl_trans BETWEEN :tx_prev_start_max_fast_detail AND :tx_prev_end_max_fast_detail THEN tgl_trans END) AS tgl_bayar_lalu
                    FROM transaksi_kredit
                    WHERE no_rekening IN (" . implode(',', $ph) . ")
                      AND tgl_trans BETWEEN :tx_prev_start_fast_detail AND :tx_cur_end_fast_detail
                    GROUP BY no_rekening");
                foreach ($params as $key => $value) $stmtTrxFast->bindValue($key, $value);
                $stmtTrxFast->execute();
                foreach ($stmtTrxFast->fetchAll(PDO::FETCH_ASSOC) as $trxRow) {
                    $trxMap[$trxRow['no_rekening']] = $trxRow;
                }

                $tabunganReks = array_values(array_unique(array_filter(array_column($pageRows, 'norek_tabungan'))));
                if (!empty($tabunganReks)) {
                    $tabPh = [];
                    $tabParams = [];
                    foreach ($tabunganReks as $idx => $tabRek) {
                        $key = ':tab_fast_detail_' . $idx;
                        $tabPh[] = $key;
                        $tabParams[$key] = $tabRek;
                    }
                    $stmtTabFast = $this->pdo->prepare("SELECT no_rekening, saldo_akhir FROM tabungan WHERE no_rekening IN (" . implode(',', $tabPh) . ")");
                    foreach ($tabParams as $key => $value) $stmtTabFast->bindValue($key, $value);
                    $stmtTabFast->execute();
                    foreach ($stmtTabFast->fetchAll(PDO::FETCH_ASSOC) as $tabRow) {
                        $tabunganMap[$tabRow['no_rekening']] = (float)($tabRow['saldo_akhir'] ?? 0);
                    }
                }
            }

            $rows = [];
            foreach ($pageRows as $rowFast) {
                $rek = $rowFast['no_rekening'];
                $actual = $actualMap[$rek] ?? [];
                $komit = $komitMap[$rek] ?? [];
                $trx = $trxMap[$rek] ?? [];
                $osM1 = (float)($rowFast['os_m1'] ?? 0);
                $osCur = (float)($actual['saldo_terpilih'] ?? 0);
                $dpd = (int)($actual['hari_menunggak'] ?? 0);
                $totung = max(0, (float)($actual['tunggakan_pokok'] ?? 0) + (float)($actual['tunggakan_bunga'] ?? 0));
                $tglBayarIni = $trx['tgl_bayar_ini'] ?? null;
                $trxBulanIni = (float)($trx['trx_bulan_ini'] ?? 0);
                $dueDate = $rowFast['tgl_jatuh_tempo'] ?? null;
                $statusInfo = $this->buildPaymentStatus($harian, $dueDate, $tglBayarIni, $trxBulanIni, $dpd);

                if ($statusBayar === 'sudah_bayar' && $trxBulanIni <= 0) continue;
                if ($statusBayar === 'belum_bayar' && $trxBulanIni > 0) continue;
                if ($statusTunggakan === 'nol' && $totung > 0) continue;
                if ($statusTunggakan === 'lebih' && $totung <= 0) continue;
                if ($statusPembayaran !== 'ALL' && $statusInfo['status_code'] !== $statusPembayaran) continue;
                $tabungan = (float)($tabunganMap[$rowFast['norek_tabungan'] ?? ''] ?? 0);

                $rowFast['kankas'] = $kankasMap[$rowFast['kode_kankas']] ?? $rowFast['kode_kankas'];
                $rowFast['nama_ao'] = $aoMap[$rowFast['kode_ao']] ?? $rowFast['kode_ao'];
                $rowFast['tabungan'] = $tabungan;
                $rowFast['os_m1'] = $osM1;
                $rowFast['os_curr'] = $osCur;
                $rowFast['dpd_curr'] = $dpd;
                $rowFast['totung'] = $totung;
                $rowFast['trx_bulan_lalu'] = (float)($trx['trx_bulan_lalu'] ?? 0);
                $rowFast['tgl_bayar_lalu'] = $trx['tgl_bayar_lalu'] ?? null;
                $rowFast['trx_bulan_ini'] = $trxBulanIni;
                $rowFast['tgl_bayar_ini'] = $tglBayarIni;
                $rowFast['status_pembayaran'] = $statusInfo['label'];
                $rowFast['status_pembayaran_code'] = $statusInfo['status_code'];
                $rowFast['hari_telat'] = $statusInfo['hari_telat'];
                $rowFast['hari_menunggak_jt'] = $statusInfo['hari_menunggak'];
                $rowFast['janji_pokok'] = (float)($komit['nominal_pokok'] ?? 0);
                $rowFast['janji_bunga'] = (float)($komit['nominal_bunga'] ?? 0);
                $rowFast['nominal_janji'] = (float)($komit['nominal_janji'] ?? 0);
                $rowFast['frekuensi_monitoring'] = (int)($komit['frekuensi_monitoring'] ?? 0);
                $rowFast['tgl_komitmen'] = $komit['tgl_komitmen'] ?? null;
                $rowFast['komit_created'] = $komit['created'] ?? null;
                $rowFast['komit_keterangan'] = $komit['keterangan'] ?? null;
                $rowFast['collection_status'] = $rowFast['_collection_status'];
                unset($rowFast['_collection_status']);
                $rowFast['status_ket'] = $osCur <= 0 ? 'LUNAS' : ($dpd === 0 ? 'LANCAR' : 'MENUNGGAK');
                $rowFast['bayar_pokok'] = ($osM1 > $osCur) ? ($osM1 - $osCur) : 0;
                $rowFast['status_tabungan'] = (($tabungan * 0.015) > $totung) ? 'Aman' : 'Belum Aman';
                $rows[] = $rowFast;
            }

            $this->send(200, "Detail Data RR", [
                'pagination' => ['current_page' => $page, 'total_records' => (int)$totalFast, 'total_pages' => (int)ceil($totalFast / max(1, $limit))],
                'collection_summary' => null,
                'data' => $rows,
                'meta' => ['mode' => 'fast_detail']
            ]);
        }

        $joinType = "LEFT JOIN";
        $whereStatus = "";
        if ($status === 'LUNAS') {
            $whereStatus = "AND (t2.no_rekening IS NULL OR {$t2Saldo} <= 0)";
        } elseif ($status === 'LANCAR') {
            $joinType = "JOIN";
            $whereStatus = "AND {$t2Saldo} > 0 AND t2.hari_menunggak = 0";
        } elseif ($status === 'MENUNGGAK') {
            $joinType = "JOIN";
            $whereStatus = "AND {$t2Saldo} > 0 AND t2.hari_menunggak > 0";
        } elseif ($status === 'ANGSURAN') {
            $joinType = "JOIN";
            $whereStatus = "AND {$t2Saldo} > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 AND {$t2Saldo} < {$t1Saldo}";
        } elseif ($status === 'SESUAI_TAGIH') {
            $joinType = "JOIN";
            $whereStatus = "AND {$t2Saldo} > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 AND {$t2Saldo} < {$t1Saldo} AND trx.trx_bulan_ini > 0 AND DAY(trx.tgl_bayar_ini) <= ({$detailDayMapCase})";
        } elseif ($status === 'LEWAT_TAGIH') {
            $joinType = "JOIN";
            $whereStatus = "AND {$t2Saldo} > 0 AND COALESCE(t2.hari_menunggak, 0) = 0 AND {$t2Saldo} < {$t1Saldo} AND trx.trx_bulan_ini > 0 AND DAY(trx.tgl_bayar_ini) > ({$detailDayMapCase})";
        } elseif ($status === 'TOTAL_BAYAR') {
            $whereStatus = "AND (t2.no_rekening IS NULL OR {$t2Saldo} <= 0 OR {$t2Saldo} < {$t1Saldo})";
        }

        $collectionCase = "CASE
                            WHEN COALESCE(komit.nominal_janji, 0) <= 0 AND COALESCE(trx.trx_bulan_ini, 0) <= 0 THEN 'NC'
                            WHEN COALESCE(komit.nominal_janji, 0) <= 0 AND COALESCE(trx.trx_bulan_ini, 0) > 0 THEN 'PO'
                            WHEN COALESCE(komit.nominal_janji, 0) > 0 AND COALESCE(trx.trx_bulan_ini, 0) <= 0 THEN 'PTP'
                            WHEN COALESCE(trx.trx_bulan_ini, 0) >= (COALESCE(komit.nominal_janji, 0) * 0.8) THEN 'PO'
                            ELSE 'PTP'
                          END";

        // 🔥 Terapkan $whereProduk ke Base Query
        $baseQuery = "FROM nominatif t1 
                      $joinType nominatif t2 ON t1.no_rekening = t2.no_rekening 
                          AND (t2.created BETWEEN :s2 AND :e2)
                      LEFT JOIN ao_kredit ao ON t1.kode_group2 = ao.kode_group2
                      LEFT JOIN tabungan tb ON t1.norek_tabungan = tb.no_rekening
                      LEFT JOIN kankas kn ON t1.kode_group1 = kn.kode_group1
                      LEFT JOIN (
                          SELECT
                              tk.no_rekening,
                              SUM(CASE WHEN tgl_trans BETWEEN :tx_cur_start_sum AND :tx_cur_end_sum THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) AS trx_bulan_ini,
                              MAX(CASE WHEN tgl_trans BETWEEN :tx_cur_start_max AND :tx_cur_end_max THEN tgl_trans END) AS tgl_bayar_ini,
                              SUM(CASE WHEN tgl_trans BETWEEN :tx_prev_start_sum AND :tx_prev_end_sum THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) AS trx_bulan_lalu,
                              MAX(CASE WHEN tgl_trans BETWEEN :tx_prev_start_max AND :tx_prev_end_max THEN tgl_trans END) AS tgl_bayar_lalu
                          FROM transaksi_kredit tk
                          WHERE tk.tgl_trans BETWEEN :tx_range_start AND :tx_range_end
                          GROUP BY tk.no_rekening
                      ) trx ON t1.no_rekening = trx.no_rekening
                      LEFT JOIN (
                          SELECT
                              rk.rekening,
                              MAX(rk.kode_kantor) AS kode_kantor,
                              SUM(COALESCE(rk.nom_pok, 0)) AS nominal_pokok,
                              SUM(COALESCE(rk.nom_bung, 0)) AS nominal_bunga,
                              SUM(COALESCE(rk.nom_pok, 0) + COALESCE(rk.nom_bung, 0)) AS nominal_janji,
                              COUNT(1) AS frekuensi_monitoring,
                              MAX(rk.tgl_komit) AS tgl_komitmen,
                              MAX(rk.tanggal) AS created,
                              MAX(rk.kendala) AS keterangan
                          FROM report_komitmen rk
                          JOIN (
                              SELECT rekening, MAX(tanggal) AS latest_created
                              FROM report_komitmen
                              WHERE tanggal BETWEEN :komit_start AND :komit_end
                              GROUP BY rekening
                          ) latest ON latest.rekening = rk.rekening AND latest.latest_created = rk.tanggal
                          GROUP BY rk.rekening
                      ) komit ON t1.no_rekening = komit.rekening
                      WHERE (t1.created BETWEEN :s1 AND :e1)
                      AND t1.kolektibilitas = 'L' 
                      AND {$t1Saldo} > 0
                      $whereDpd 
                      AND DAY(t1.tgl_jatuh_tempo) IN ($daysStr)
                      $whereStatus
                      $whereProduk";
        
        if ($kc && $kc !== '000') $baseQuery .= " AND t1.kode_cabang = :kc";
        elseif ($korwil && $kw_start && $kw_end) $baseQuery .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
        if ($kankas) $baseQuery .= " AND t1.kode_group1 = :kankas"; 
        if ($ao) $baseQuery .= " AND t1.kode_group2 = :ao";
        if ($search !== '') $baseQuery .= " AND (t1.no_rekening LIKE :search OR t1.nama_nasabah LIKE :search2 OR t1.hp LIKE :search3)";
        if ($collectionStatus === 'CALL') $baseQuery .= " AND komit.rekening IS NOT NULL";
        elseif ($collectionStatus !== 'ALL') $baseQuery .= " AND ($collectionCase) = :collection_status";
        if ($statusBayar === 'sudah_bayar') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0";
        elseif ($statusBayar === 'belum_bayar') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0";
        if ($statusTunggakan === 'nol') $baseQuery .= " AND (COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) = 0";
        elseif ($statusTunggakan === 'lebih') $baseQuery .= " AND (COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) > 0";
        if ($statusPembayaran === 'OTP') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0 AND DAY(trx.tgl_bayar_ini) <= ({$detailDayMapCase})";
        elseif ($statusPembayaran === 'TELAT') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0 AND DAY(trx.tgl_bayar_ini) > ({$detailDayMapCase})";
        elseif ($statusPembayaran === 'BELUM_JATUH_TEMPO') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0 AND DAY(:harian_status) <= ({$detailDayMapCase})";
        elseif ($statusPembayaran === 'BELUM_BAYAR') $baseQuery .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0 AND DAY(:harian_status) > ({$detailDayMapCase})";

        $baseQueryNoKomitmen = preg_replace('/\s+LEFT JOIN\s+\(\s+SELECT\s+rk\.rekening,.*?\)\s+komit\s+ON\s+t1\.no_rekening\s+=\s+komit\.rekening/s', '', $baseQuery);
        if (!$baseQueryNoKomitmen) $baseQueryNoKomitmen = $baseQuery;

        $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) " . ($collectionStatus === 'ALL' ? $baseQueryNoKomitmen : $baseQuery));
        $bindTxParams($stmtCnt, $collectionStatus !== 'ALL');
        $stmtCnt->bindValue(':s1', $s1); $stmtCnt->bindValue(':e1', $e1);
        $stmtCnt->bindValue(':s2', $s2); $stmtCnt->bindValue(':e2', $e2);
        if ($kc && $kc !== '000') $stmtCnt->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmtCnt->bindValue(':kw_start', $kw_start); $stmtCnt->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmtCnt->bindValue(':kankas', $kankas); 
        if ($ao) $stmtCnt->bindValue(':ao', $ao); 
        if ($search !== '') {
            $stmtCnt->bindValue(':search', "%$search%");
            $stmtCnt->bindValue(':search2', "%$search%");
            $stmtCnt->bindValue(':search3', "%$search%");
        }
        if (!in_array($collectionStatus, ['ALL', 'CALL'], true)) $stmtCnt->bindValue(':collection_status', $collectionStatus);
        if (in_array($statusPembayaran, ['BELUM_JATUH_TEMPO', 'BELUM_BAYAR'], true)) $stmtCnt->bindValue(':harian_status', $harian);
        $stmtCnt->execute();
        $total = $stmtCnt->fetchColumn();

        $collectionSummary = null;

        $cols = "t1.no_rekening, t1.nama_nasabah, t1.kode_produk,
                 t1.alamat, t1.hp as no_hp, 
                 COALESCE(kn.deskripsi_group1, t1.kode_group1) as kankas,
                 t1.kode_group1 as kode_kankas,
                 COALESCE(tb.saldo_akhir, 0) as tabungan,
                 COALESCE(ao.nama_ao, t1.kode_group2) as nama_ao,
                 t1.kode_group2 as kode_ao,
                 t1.tgl_jatuh_tempo, t1.jml_pinjaman,
                 t1.{$saldoCol} as os_m1, 
                 COALESCE(t2.{$saldoCol}, 0) as os_curr, 
                 COALESCE(t2.hari_menunggak, 0) as dpd_curr,
                 GREATEST(0, COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) as totung,
                 COALESCE(trx.trx_bulan_lalu, 0) as trx_bulan_lalu,
                 trx.tgl_bayar_lalu,
                 COALESCE(trx.trx_bulan_ini, 0) as trx_bulan_ini,
                 trx.tgl_bayar_ini,
                 COALESCE(komit.nominal_pokok, 0) AS janji_pokok,
                 COALESCE(komit.nominal_bunga, 0) AS janji_bunga,
                 COALESCE(komit.nominal_janji, 0) AS nominal_janji,
                 COALESCE(komit.frekuensi_monitoring, 0) AS frekuensi_monitoring,
                 komit.tgl_komitmen,
                 komit.created AS komit_created,
                 komit.keterangan AS komit_keterangan,
                 $collectionCase AS collection_status";
        
        $sqlData = "SELECT $cols $baseQuery ORDER BY
                    CASE
                      WHEN COALESCE(trx.trx_bulan_ini, 0) <= 0 AND DAY(:harian_order) > ({$detailDayMapCase}) THEN 0
                      WHEN COALESCE(trx.trx_bulan_ini, 0) <= 0 THEN 1
                      ELSE 2
                    END ASC,
                    CASE WHEN DAY(:harian_order2) > ({$detailDayMapCase}) THEN DAY(:harian_order3) - ({$detailDayMapCase}) ELSE 0 END DESC,
                    {$t1Saldo} DESC LIMIT :lim OFFSET :off";
        $stmt = $this->pdo->prepare($sqlData);
        $bindTxParams($stmt);
        $stmt->bindValue(':s1', $s1); $stmt->bindValue(':e1', $e1);
        $stmt->bindValue(':s2', $s2); $stmt->bindValue(':e2', $e2);
        if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmt->bindValue(':kw_start', $kw_start); $stmt->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmt->bindValue(':kankas', $kankas); 
        if ($ao) $stmt->bindValue(':ao', $ao); 
        if ($search !== '') {
            $stmt->bindValue(':search', "%$search%");
            $stmt->bindValue(':search2', "%$search%");
            $stmt->bindValue(':search3', "%$search%");
        }
        if (!in_array($collectionStatus, ['ALL', 'CALL'], true)) $stmt->bindValue(':collection_status', $collectionStatus);
        if (in_array($statusPembayaran, ['BELUM_JATUH_TEMPO', 'BELUM_BAYAR'], true)) $stmt->bindValue(':harian_status', $harian);
        $stmt->bindValue(':harian_order', $harian);
        $stmt->bindValue(':harian_order2', $harian);
        $stmt->bindValue(':harian_order3', $harian);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $osM1 = (float)$r['os_m1']; $osCur = (float)$r['os_curr']; $dpd = (int)$r['dpd_curr'];
            $totung = (float)$r['totung']; $tabungan = (float)$r['tabungan'];
            
            if ($osCur <= 0) { $r['status_ket'] = 'LUNAS'; }
            elseif ($dpd == 0) { $r['status_ket'] = 'LANCAR'; }
            else { $r['status_ket'] = 'MENUNGGAK'; }

            $r['os_m1']=$osM1; $r['os_curr']=$osCur; 
            $r['trx_bulan_lalu'] = (float)($r['trx_bulan_lalu'] ?? 0);
            $r['trx_bulan_ini'] = (float)($r['trx_bulan_ini'] ?? 0);
            $r['tgl_bayar_lalu'] = $r['tgl_bayar_lalu'] ?? null;
            $r['tgl_bayar_ini'] = $r['tgl_bayar_ini'] ?? null;
            $statusInfo = $this->buildPaymentStatus($harian, $r['tgl_jatuh_tempo'] ?? null, $r['tgl_bayar_ini'], $r['trx_bulan_ini'], $dpd);
            $r['status_pembayaran'] = $statusInfo['label'];
            $r['status_pembayaran_code'] = $statusInfo['status_code'];
            $r['hari_telat'] = $statusInfo['hari_telat'];
            $r['hari_menunggak_jt'] = $statusInfo['hari_menunggak'];
            $r['janji_pokok'] = (float)($r['janji_pokok'] ?? 0);
            $r['janji_bunga'] = (float)($r['janji_bunga'] ?? 0);
            $r['nominal_janji'] = (float)($r['nominal_janji'] ?? 0);
            $r['frekuensi_monitoring'] = (int)($r['frekuensi_monitoring'] ?? 0);
            $r['collection_status'] = $r['collection_status'] ?? 'NC';
            $r['bayar_pokok'] = ($osM1 > $osCur) ? ($osM1 - $osCur) : 0;

            if (($tabungan * 0.015) > $totung) {
                $r['status_tabungan'] = 'Aman';
            } else {
                $r['status_tabungan'] = 'Belum Aman';
            }
        }

        $this->send(200, "Detail Data RR", [
            'pagination' => ['current_page' => $page, 'total_records' => (int)$total, 'total_pages' => ceil($total / $limit)],
            'collection_summary' => $collectionSummary,
            'data' => $rows
        ]);
    }

    public function getDetailRekapRr($input = null) {
        set_time_limit(180); ini_set('memory_limit', '768M');

        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $kankas  = $b['kode_kankas'] ?? null;
        $ao      = $b['kode_ao'] ?? null;
        $search  = trim($b['search'] ?? '');
        $status  = strtoupper(trim((string)($b['status'] ?? 'ALL')));
        $statusBayar = strtolower(trim((string)($b['status_bayar'] ?? 'all')));
        $statusTunggakan = strtolower(trim((string)($b['status_tunggakan'] ?? 'all')));
        $statusPembayaran = strtoupper(trim((string)($b['status_pembayaran'] ?? 'ALL')));
        $page    = max(1, (int)($b['page'] ?? 1));
        $limit   = max(1, min(500, (int)($b['limit'] ?? 20)));
        $offset  = ($page - 1) * $limit;

        if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);
        $txStart = date('Y-m-01 00:00:00', strtotime($harian));
        $txEnd = date('Y-m-d 23:59:59', strtotime($harian));

        $saldoCol = $this->getSaldoColumn($b);
        $t1Saldo = "COALESCE(t1.{$saldoCol}, 0)";
        $t2Saldo = "COALESCE(t2.{$saldoCol}, 0)";
        $mode = $status === 'TOTAL_BAYAR' || $status === 'DELTA' ? 'DELTA' : 'ACTUAL';

        $lancarM1 = "({$t1Saldo} > 0 AND COALESCE(t1.kolektibilitas, '') = 'L' AND COALESCE(t1.hari_menunggak, 0) = 0)";
        $lancarCur = "({$t2Saldo} > 0 AND COALESCE(t2.kolektibilitas, '') = 'L' AND COALESCE(t2.hari_menunggak, 0) = 0)";
        $migrasiRr = "({$lancarM1} AND {$t2Saldo} > 0 AND COALESCE(t2.kolektibilitas, '') = 'L' AND COALESCE(t2.hari_menunggak, 0) > 0)";
        $trxJoin = "LEFT JOIN (
                        SELECT no_rekening,
                               SUM(COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) AS trx_bulan_ini,
                               MAX(tgl_trans) AS tgl_bayar_ini
                        FROM transaksi_kredit
                        WHERE tgl_trans BETWEEN :tx_start AND :tx_end
                        GROUP BY no_rekening
                    ) trx ON trx.no_rekening = t2.no_rekening";
        $trxJoinOuter = "LEFT JOIN (
                        SELECT no_rekening,
                               SUM(COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) AS trx_bulan_ini,
                               MAX(tgl_trans) AS tgl_bayar_ini
                        FROM transaksi_kredit
                        WHERE tgl_trans BETWEEN :tx_start_outer AND :tx_end_outer
                        GROUP BY no_rekening
                    ) trx ON trx.no_rekening = t2.no_rekening";

        $detailExtraWhere = "";
        $needsTrxFilter = $statusBayar !== 'all' || $statusPembayaran !== 'ALL';
        if ($statusBayar === 'sudah_bayar') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0";
        if ($statusBayar === 'belum_bayar') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0";
        if ($statusTunggakan === 'nol') $detailExtraWhere .= " AND (COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) = 0";
        if ($statusTunggakan === 'lebih') $detailExtraWhere .= " AND (COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) > 0";
        $dueDaySql = "LEAST(DAY(t2.tgl_jatuh_tempo), DAY(LAST_DAY(:harian_status_rr_due)))";
        if ($statusPembayaran === 'OTP') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0 AND DAY(trx.tgl_bayar_ini) <= {$dueDaySql}";
        if ($statusPembayaran === 'TELAT') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) > 0 AND DAY(trx.tgl_bayar_ini) > {$dueDaySql}";
        if ($statusPembayaran === 'BELUM_JATUH_TEMPO') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0 AND DAY(:harian_status_rr) <= {$dueDaySql}";
        if ($statusPembayaran === 'BELUM_BAYAR') $detailExtraWhere .= " AND COALESCE(trx.trx_bulan_ini, 0) <= 0 AND DAY(:harian_status_rr) > {$dueDaySql}";

        $bind = function($stmt) use ($s1, $e1, $s2, $e2, $txStart, $txEnd, $harian, $kc, $kankas, $ao, $search) {
            if (preg_match('/\\:s1(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':s1', $s1);
            if (preg_match('/\\:e1(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':e1', $e1);
            if (preg_match('/\\:s2(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':s2', $s2);
            if (preg_match('/\\:e2(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':e2', $e2);
            if (preg_match('/\\:tx_start(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':tx_start', $txStart);
            if (preg_match('/\\:tx_end(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':tx_end', $txEnd);
            if (preg_match('/\\:tx_start_outer(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':tx_start_outer', $txStart);
            if (preg_match('/\\:tx_end_outer(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':tx_end_outer', $txEnd);
            if (preg_match('/\\:harian_status_rr(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':harian_status_rr', $harian);
            if (preg_match('/\\:harian_status_rr_due(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':harian_status_rr_due', $harian);
            if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
            if ($kankas) $stmt->bindValue(':kankas', $kankas);
            if ($ao) $stmt->bindValue(':ao', $ao);
            if ($search !== '') {
                $stmt->bindValue(':search', "%$search%");
                $stmt->bindValue(':search2', "%$search%");
                $stmt->bindValue(':search3', "%$search%");
            }
        };
        $bindActualOnly = function($stmt) use ($s2, $e2, $txStart, $txEnd, $harian, $kc, $kankas, $ao, $search) {
            $stmt->bindValue(':s2', $s2);
            $stmt->bindValue(':e2', $e2);
            if (strpos($stmt->queryString, ':tx_start') !== false) $stmt->bindValue(':tx_start', $txStart);
            if (strpos($stmt->queryString, ':tx_end') !== false) $stmt->bindValue(':tx_end', $txEnd);
            if (preg_match('/\\:harian_status_rr(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':harian_status_rr', $harian);
            if (preg_match('/\\:harian_status_rr_due(?![A-Za-z0-9_])/', $stmt->queryString)) $stmt->bindValue(':harian_status_rr_due', $harian);
            if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
            if ($kankas) $stmt->bindValue(':kankas', $kankas);
            if ($ao) $stmt->bindValue(':ao', $ao);
            if ($search !== '') {
                $stmt->bindValue(':search', "%$search%");
                $stmt->bindValue(':search2', "%$search%");
                $stmt->bindValue(':search3', "%$search%");
            }
        };

        $selectColumns = "COALESCE(t2.no_rekening, t1.no_rekening) AS no_rekening,
                    COALESCE(t2.nama_nasabah, t1.nama_nasabah) AS nama_nasabah,
                    COALESCE(t2.alamat, t1.alamat) AS alamat,
                    COALESCE(t2.hp, t1.hp) AS no_hp,
                    COALESCE(kn.deskripsi_group1, COALESCE(t2.kode_group1, t1.kode_group1)) AS kankas,
                    COALESCE(t2.kode_group1, t1.kode_group1) AS kode_kankas,
                    COALESCE(ao.nama_ao, COALESCE(t2.kode_group2, t1.kode_group2)) AS nama_ao,
                    COALESCE(t2.kode_group2, t1.kode_group2) AS kode_ao,
                    COALESCE(t2.tgl_jatuh_tempo, t1.tgl_jatuh_tempo) AS tgl_jatuh_tempo,
                    COALESCE(t2.jml_pinjaman, t1.jml_pinjaman, 0) AS jml_pinjaman,
                    {$t1Saldo} AS os_m1,
                    {$t2Saldo} AS os_curr,
                    COALESCE(trx.trx_bulan_ini, 0) AS trx_bulan_ini,
                    trx.tgl_bayar_ini,
                    GREATEST(0, COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) AS totung,
                    COALESCE(t2.hari_menunggak, 0) AS dpd_curr,
                    COALESCE(tb.saldo_akhir, 0) AS tabungan,
                    CASE WHEN {$lancarCur} THEN 'LANCAR' ELSE 'MENUNGGAK' END AS status_ket,
                    CASE WHEN {$lancarM1} THEN 'LANCAR' ELSE 'TIDAK LANCAR' END AS status_m1,
                    CASE WHEN {$lancarCur} THEN 'LANCAR' ELSE 'TIDAK LANCAR' END AS status_actual,
                    ({$t2Saldo} - {$t1Saldo}) AS delta_os,
                    '' AS status_pembayaran_code,
                    '' AS status_pembayaran,
                    0 AS hari_telat,
                    COALESCE(t2.hari_menunggak, 0) AS hari_menunggak_jt,
                    CASE WHEN (COALESCE(tb.saldo_akhir, 0) * 0.015) > (COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) THEN 'Aman' ELSE 'Belum Aman' END AS status_tabungan";

        if ($mode === 'ACTUAL') {
            $actualFilters = "";
            if ($kc && $kc !== '000') $actualFilters .= " AND t2.kode_cabang = :kc";
            if ($kankas) $actualFilters .= " AND t2.kode_group1 = :kankas";
            if ($ao) $actualFilters .= " AND t2.kode_group2 = :ao";
            if ($search !== '') {
                $actualFilters .= " AND (t2.no_rekening LIKE :search OR t2.nama_nasabah LIKE :search2 OR t2.hp LIKE :search3)";
            }

            $whereActual = "t2.created BETWEEN :s2 AND :e2
                            AND {$t2Saldo} > 0
                            AND COALESCE(t2.kolektibilitas, '') = 'L'
                            AND COALESCE(t2.hari_menunggak, 0) = 0
                            {$actualFilters}";

            $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) FROM nominatif t2 {$trxJoin} WHERE {$whereActual} {$detailExtraWhere}");
            $bindActualOnly($stmtCnt);
            $stmtCnt->execute();
            $total = (int)$stmtCnt->fetchColumn();

            $sql = "SELECT {$selectColumns}
                    FROM (
                        SELECT t2.no_rekening
                        FROM nominatif t2
                        {$trxJoin}
                        WHERE {$whereActual}
                          {$detailExtraWhere}
                        ORDER BY {$t2Saldo} DESC, t2.nama_nasabah ASC
                        LIMIT :lim OFFSET :off
                    ) pick
                    INNER JOIN nominatif t2 ON t2.no_rekening = pick.no_rekening AND t2.created BETWEEN :s2_join AND :e2_join
                    LEFT JOIN nominatif t1 ON t1.no_rekening = pick.no_rekening AND t1.created BETWEEN :s1 AND :e1
                    {$trxJoinOuter}
                    LEFT JOIN ao_kredit ao ON t2.kode_group2 = ao.kode_group2
                    LEFT JOIN kankas kn ON t2.kode_group1 = kn.kode_group1
                    LEFT JOIN tabungan tb ON t2.norek_tabungan = tb.no_rekening
                    ORDER BY {$t2Saldo} DESC, t2.nama_nasabah ASC";

            $stmt = $this->pdo->prepare($sql);
            $bind($stmt);
            $stmt->bindValue(':s2_join', $s2);
            $stmt->bindValue(':e2_join', $e2);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $filters = "";
            if ($kc && $kc !== '000') $filters .= " AND t2.kode_cabang = :kc";
            if ($kankas) $filters .= " AND t2.kode_group1 = :kankas";
            if ($ao) $filters .= " AND t2.kode_group2 = :ao";
            if ($search !== '') {
                $filters .= " AND (t2.no_rekening LIKE :search OR t2.nama_nasabah LIKE :search2 OR t2.hp LIKE :search3)";
            }

            $deltaJoinForFilter = $needsTrxFilter ? $trxJoin : "";
            $deltaWhere = "t2.created BETWEEN :s2 AND :e2
                           {$filters}
                           AND {$migrasiRr}
                           {$detailExtraWhere}";

            $stmtCnt = $this->pdo->prepare("SELECT COUNT(1)
                    FROM nominatif t2
                    LEFT JOIN nominatif t1 ON t1.no_rekening = t2.no_rekening AND t1.created BETWEEN :s1 AND :e1
                    {$deltaJoinForFilter}
                    WHERE {$deltaWhere}");
            $bind($stmtCnt);
            $stmtCnt->execute();
            $total = (int)$stmtCnt->fetchColumn();

            $sql = "SELECT {$selectColumns}
                    FROM (
                        SELECT t2.no_rekening
                        FROM nominatif t2
                        LEFT JOIN nominatif t1 ON t1.no_rekening = t2.no_rekening AND t1.created BETWEEN :s1_pick AND :e1_pick
                        {$deltaJoinForFilter}
                        WHERE " . str_replace([':s1', ':e1'], [':s1_pick', ':e1_pick'], $deltaWhere) . "
                        ORDER BY ABS(COALESCE(t2.{$saldoCol}, 0) - COALESCE(t1.{$saldoCol}, 0)) DESC, t2.nama_nasabah ASC
                        LIMIT :lim OFFSET :off
                    ) pick
                    INNER JOIN nominatif t2 ON t2.no_rekening = pick.no_rekening AND t2.created BETWEEN :s2_data AND :e2_data
                    LEFT JOIN nominatif t1 ON t1.no_rekening = pick.no_rekening AND t1.created BETWEEN :s1_data AND :e1_data
                    {$trxJoinOuter}
                    LEFT JOIN ao_kredit ao ON COALESCE(t2.kode_group2, t1.kode_group2) = ao.kode_group2
                    LEFT JOIN kankas kn ON COALESCE(t2.kode_group1, t1.kode_group1) = kn.kode_group1
                    LEFT JOIN tabungan tb ON COALESCE(t2.norek_tabungan, t1.norek_tabungan) = tb.no_rekening
                    ORDER BY ABS({$t2Saldo} - {$t1Saldo}) DESC, COALESCE(t2.nama_nasabah, t1.nama_nasabah) ASC
                    ";

            $stmt = $this->pdo->prepare($sql);
            $bind($stmt);
            $stmt->bindValue(':s1_pick', $s1);
            $stmt->bindValue(':e1_pick', $e1);
            $stmt->bindValue(':s2_data', $s2);
            $stmt->bindValue(':e2_data', $e2);
            $stmt->bindValue(':s1_data', $s1);
            $stmt->bindValue(':e1_data', $e1);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $paid = (float)($row['trx_bulan_ini'] ?? 0);
            $dpd = (int)($row['dpd_curr'] ?? 0);
            $statusInfo = $this->buildPaymentStatus($harian, $row['tgl_jatuh_tempo'] ?? null, $row['tgl_bayar_ini'] ?? null, $paid, $dpd);
            $row['status_pembayaran_code'] = $statusInfo['status_code'];
            $row['status_pembayaran'] = $statusInfo['label'];
            $row['hari_telat'] = $statusInfo['hari_telat'];
            $row['hari_menunggak_jt'] = $statusInfo['hari_menunggak'];
        }
        unset($row);

        $this->send(200, "Detail Rekap RR", [
            'pagination' => [
                'current_page' => $page,
                'total_records' => $total,
                'total_pages' => (int)ceil($total / $limit)
            ],
            'data' => $rows,
            'meta' => ['mode' => strtolower($mode), 'hitung_berdasarkan' => $saldoCol]
        ]);
    }

    /**
     * 3. DETAIL LUNAS (REFINANCING CHECK)
     */
    public function getDetailLunasRR($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = $b['kode_kankas'] ?? null; 
        $ao      = $b['kode_ao'] ?? null;     
        $search  = trim($b['search'] ?? '');
        $dpdBucket = $b['dpd_bucket'] ?? 'dpd0';
        $tglTagih = $b['tgl_tagih'] ?? 'ALL'; // 🔥 FIX: Set default to ALL
        $page    = $b['page'] ?? 1;
        $limit   = $b['limit'] ?? 10;
        $offset  = ($page - 1) * $limit;

        // 🔥 FIX: Terapkan Filter Produk 127
        $include127 = filter_var($b['include_127'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $whereProduk = !$include127 ? $this->getKppExclusionWhere('t1') : "";

        if (!$closing || !$harian) return $this->send(400, "Parameter kurang."); // 🔥 FIX: Hilangkan validasi !$tglMap

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        // 🔥 FIX: Filter DPD Bucket di M-1
        $whereDpd = $this->getDpdWhere('t1.hari_menunggak', $dpdBucket);

        // 🔥 FIX: Korwil mapping
        $kw_start = null; $kw_end = null;
        if ($korwil) {
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
        }

        // 🔥 FIX: Logika Tanggal
        $daysStr = implode(',', $this->getDueDaysForRequest($harian, $tglTagih));

        $baseQuery = "FROM nominatif t1 
                      LEFT JOIN nominatif t2 ON t1.no_rekening = t2.no_rekening 
                          AND (t2.created BETWEEN :s2 AND :e2)
                      LEFT JOIN ao_kredit ao ON t1.kode_group2 = ao.kode_group2
                      LEFT JOIN kankas kn ON t1.kode_group1 = kn.kode_group1
                      WHERE (t1.created BETWEEN :s1 AND :e1)
                      AND t1.kolektibilitas = 'L' 
                      AND t1.baki_debet > 0
                      $whereDpd 
                      AND (t2.no_rekening IS NULL OR t2.baki_debet <= 0)
                      AND DAY(t1.tgl_jatuh_tempo) IN ($daysStr)
                      $whereProduk";

        if ($kc && $kc !== '000') $baseQuery .= " AND t1.kode_cabang = :kc";
        elseif ($korwil && $kw_start && $kw_end) $baseQuery .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
        if ($kankas) $baseQuery .= " AND t1.kode_group1 = :kankas"; 
        if ($ao) $baseQuery .= " AND t1.kode_group2 = :ao"; 
        if ($search !== '') $baseQuery .= " AND (t1.no_rekening LIKE :search OR t1.nama_nasabah LIKE :search2 OR t1.nasabah_id LIKE :search3)";

        // Count
        $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) $baseQuery");
        $stmtCnt->bindValue(':s1', $s1); $stmtCnt->bindValue(':e1', $e1);
        $stmtCnt->bindValue(':s2', $s2); $stmtCnt->bindValue(':e2', $e2);
        if ($kc && $kc !== '000') $stmtCnt->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmtCnt->bindValue(':kw_start', $kw_start); $stmtCnt->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmtCnt->bindValue(':kankas', $kankas); 
        if ($ao) $stmtCnt->bindValue(':ao', $ao); 
        if ($search !== '') {
            $stmtCnt->bindValue(':search', "%$search%");
            $stmtCnt->bindValue(':search2', "%$search%");
            $stmtCnt->bindValue(':search3', "%$search%");
        }
        $stmtCnt->execute();
        $total = $stmtCnt->fetchColumn();

        $sqlData = "SELECT t1.nasabah_id, t1.no_rekening, t1.nama_nasabah, 
                           t1.alamat, t1.hp as no_hp, 
                           COALESCE(kn.deskripsi_group1, t1.kode_group1) as kankas,
                           t1.kode_group1 as kode_kankas,
                           COALESCE(ao.nama_ao, t1.kode_group2) as nama_ao,
                           t1.kode_group2 as kode_ao,
                           t1.jml_pinjaman as plafon_lama, 
                           t1.baki_debet as os_lunas, t1.tgl_realisasi as tgl_lama
                    $baseQuery 
                    ORDER BY t1.baki_debet DESC 
                    LIMIT :lim OFFSET :off";

        $stmt = $this->pdo->prepare($sqlData);
        $stmt->bindValue(':s1', $s1); $stmt->bindValue(':e1', $e1);
        $stmt->bindValue(':s2', $s2); $stmt->bindValue(':e2', $e2);
        if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmt->bindValue(':kw_start', $kw_start); $stmt->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmt->bindValue(':kankas', $kankas); 
        if ($ao) $stmt->bindValue(':ao', $ao); 
        if ($search !== '') {
            $stmt->bindValue(':search', "%$search%");
            $stmt->bindValue(':search2', "%$search%");
            $stmt->bindValue(':search3', "%$search%");
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $closingDateStr = date('Y-m-d', strtotime($closing));
        $harianDateStr  = date('Y-m-d', strtotime($harian));

        $sqlCheck = "SELECT no_rekening, jml_pinjaman, tgl_realisasi 
                     FROM nominatif 
                     WHERE created BETWEEN :s2 AND :e2
                     AND nasabah_id = :nid
                     AND no_rekening != :old_rek
                     AND tgl_realisasi > :closing_date 
                     AND tgl_realisasi <= :harian_date
                     LIMIT 1";
        $stmtCheck = $this->pdo->prepare($sqlCheck);

        foreach ($rows as &$r) {
            $stmtCheck->bindValue(':s2', $s2);
            $stmtCheck->bindValue(':e2', $e2);
            $stmtCheck->bindValue(':nid', $r['nasabah_id']);
            $stmtCheck->bindValue(':old_rek', $r['no_rekening']);
            $stmtCheck->bindValue(':closing_date', $closingDateStr);
            $stmtCheck->bindValue(':harian_date', $harianDateStr);
            $stmtCheck->execute();
            $newLoan = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($newLoan) {
                $r['status_lunas'] = 'REFINANCING / Top Up';
                $r['rek_baru']     = $newLoan['no_rekening'];
                $r['plafond_baru'] = $newLoan['jml_pinjaman'];
                $r['tgl_baru']     = $newLoan['tgl_realisasi'];
            } else {
                $r['status_lunas'] = 'PROSPEK (PELUNASAN)';
                $r['rek_baru']     = '-';
                $r['plafond_baru'] = 0;
                $r['tgl_baru']     = '-';
            }
        }

        $this->send(200, "Detail Lunas RR", [
            'pagination' => ['current_page' => $page, 'total_records' => (int)$total, 'total_pages' => ceil($total / $limit)],
            'data' => $rows
        ]);
    }

/*
     * 4. REKAP RR (Summary M-1 vs Actual)
     * Kolom: Total OS, Total NOA.
     * Persen: (OS DPD=0 / Total OS) * 100
     */
    public function getRekapRr($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $userKode = $b['kode_kantor'] ?? '000'; 
        $saldoCol = $this->getSaldoColumn($b);
        $saldoExpr = "COALESCE({$saldoCol}, 0)";

        if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        $isPusat = ($userKode === '000');
        $groupByCol = $isPusat ? 'kode_cabang' : 'kode_group1';

        // 1. QUERY M-1 (CLOSING)
        // 🔥 FIX: all_noa diisi dengan jumlah rekening yang masuk kriteria Lancar/RR
        $sqlM1 = "SELECT 
                    $groupByCol as grp,
                    SUM(CASE WHEN COALESCE(hari_menunggak, 0) = 0 AND kolektibilitas = 'L' THEN 1 ELSE 0 END) as all_noa,
                    SUM({$saldoExpr}) as all_os,
                    SUM(CASE WHEN COALESCE(hari_menunggak, 0) = 0 AND kolektibilitas = 'L' THEN {$saldoExpr} ELSE 0 END) as lancar_os
                  FROM nominatif
                  WHERE created BETWEEN :s1 AND :e1 
                  AND {$saldoExpr} > 0";
                  
        if (!$isPusat) $sqlM1 .= " AND kode_cabang = :kc";
        $sqlM1 .= " GROUP BY $groupByCol";

        $stmt1 = $this->pdo->prepare($sqlM1);
        $stmt1->bindValue(':s1', $s1); $stmt1->bindValue(':e1', $e1);
        if (!$isPusat) $stmt1->bindValue(':kc', $userKode);
        $stmt1->execute();
        $dataM1 = $stmt1->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // 2. QUERY ACTUAL (HARIAN)
        // 🔥 FIX: all_noa diisi dengan jumlah rekening yang masuk kriteria Lancar/RR
        $sqlCur = "SELECT 
                    $groupByCol as grp,
                    SUM(CASE WHEN COALESCE(hari_menunggak, 0) = 0 AND kolektibilitas = 'L' THEN 1 ELSE 0 END) as all_noa,
                    SUM({$saldoExpr}) as all_os,
                    SUM(CASE WHEN COALESCE(hari_menunggak, 0) = 0 AND kolektibilitas = 'L' THEN {$saldoExpr} ELSE 0 END) as lancar_os
                   FROM nominatif
                   WHERE created BETWEEN :s2 AND :e2 
                   AND {$saldoExpr} > 0";

        if (!$isPusat) $sqlCur .= " AND kode_cabang = :kc";
        $sqlCur .= " GROUP BY $groupByCol";

        $stmt2 = $this->pdo->prepare($sqlCur);
        $stmt2->bindValue(':s2', $s2); $stmt2->bindValue(':e2', $e2);
        if (!$isPusat) $stmt2->bindValue(':kc', $userKode);
        $stmt2->execute();
        $dataCur = $stmt2->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // Delta RR = rekening yang saat closing masih lancar, lalu pada actual tetap kolek L
        // tetapi sudah mulai punya hari menunggak. Query ini disamakan dengan detail klik Delta.
        $t1SaldoExpr = "COALESCE(t1.{$saldoCol}, 0)";
        $t2SaldoExpr = "COALESCE(t2.{$saldoCol}, 0)";
        $deltaGroupByCol = $isPusat ? 't2.kode_cabang' : 't2.kode_group1';
        $sqlDelta = "SELECT
                        {$deltaGroupByCol} AS grp,
                        COUNT(1) AS delta_noa,
                        COALESCE(SUM({$t2SaldoExpr} - {$t1SaldoExpr}), 0) AS delta_os_lancar
                     FROM nominatif t2
                     INNER JOIN nominatif t1
                        ON t1.no_rekening = t2.no_rekening
                       AND t1.created BETWEEN :s1_delta AND :e1_delta
                     WHERE t2.created BETWEEN :s2_delta AND :e2_delta
                       AND {$t1SaldoExpr} > 0
                       AND COALESCE(t1.kolektibilitas, '') = 'L'
                       AND COALESCE(t1.hari_menunggak, 0) = 0
                       AND {$t2SaldoExpr} > 0
                       AND COALESCE(t2.kolektibilitas, '') = 'L'
                       AND COALESCE(t2.hari_menunggak, 0) > 0";

        if (!$isPusat) $sqlDelta .= " AND t2.kode_cabang = :kc_delta";
        $sqlDelta .= " GROUP BY {$deltaGroupByCol}";

        $stmtDelta = $this->pdo->prepare($sqlDelta);
        $stmtDelta->bindValue(':s1_delta', $s1);
        $stmtDelta->bindValue(':e1_delta', $e1);
        $stmtDelta->bindValue(':s2_delta', $s2);
        $stmtDelta->bindValue(':e2_delta', $e2);
        if (!$isPusat) $stmtDelta->bindValue(':kc_delta', $userKode);
        $stmtDelta->execute();
        $dataDelta = $stmtDelta->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // 3. FETCH MASTER NAMA
        $namaMap = [];
        if ($isPusat) {
            try {
                $stmtN = $this->pdo->query("SELECT kode_kantor, nama_kantor FROM kode_kantor");
                while ($r = $stmtN->fetch(PDO::FETCH_ASSOC)) $namaMap[$r['kode_kantor']] = $r['nama_kantor'];
            } catch (Exception $e) {}
        } else {
            try {
                $stmtN = $this->pdo->prepare("SELECT kode_group1, deskripsi_group1 FROM kankas WHERE kode_group1 LIKE ?");
                $stmtN->execute([$userKode . '%']);
                while ($r = $stmtN->fetch(PDO::FETCH_ASSOC)) $namaMap[$r['kode_group1']] = $r['deskripsi_group1'];
            } catch (Exception $e) {}
        }

        // 4. MENGGABUNGKAN DATA M-1 DAN ACTUAL
        $finalData = [];
        $allKeys = array_unique(array_merge(array_keys($dataM1), array_keys($dataCur), array_keys($dataDelta)));

        $grandTotal = [
            'm1_all_noa' => 0, 'm1_all_os' => 0, 'm1_lancar_os' => 0,
            'cur_all_noa' => 0, 'cur_all_os' => 0, 'cur_lancar_os' => 0,
            'delta_noa' => 0, 'delta_os' => 0, 'delta_os_lancar' => 0,
            'm1_pct' => 0, 'cur_pct' => 0, 'delta_pct' => 0
        ];

        foreach ($allKeys as $grpId) {
            if (!$grpId) continue; 

            $nama = $namaMap[$grpId] ?? ($isPusat ? "Kc. $grpId" : "Kas $grpId");
            
            $m1  = $dataM1[$grpId] ?? ['all_noa'=>0, 'all_os'=>0, 'lancar_os'=>0];
            $cur = $dataCur[$grpId] ?? ['all_noa'=>0, 'all_os'=>0, 'lancar_os'=>0];
            $delta = $dataDelta[$grpId] ?? ['delta_noa'=>0, 'delta_os_lancar'=>0];

            $m1AllOs = (float)$m1['all_os'];
            $curAllOs = (float)$cur['all_os'];

            $m1LancarOs = (float)$m1['lancar_os'];
            $curLancarOs = (float)$cur['lancar_os'];
            $deltaNoa = (int)$delta['delta_noa'];
            $deltaOsLancar = (float)$delta['delta_os_lancar'];

            // Kalkulasi persentase RR per Cabang/Kankas
            $m1Pct  = $m1AllOs > 0 ? ($m1LancarOs / $m1AllOs) * 100 : 0;
            $curPct = $curAllOs > 0 ? ($curLancarOs / $curAllOs) * 100 : 0;

            $finalData[] = [
                'kode' => $grpId,
                'nama' => $nama,
                
                'm1_all_noa'    => (int)$m1['all_noa'],
                'm1_all_os'     => $m1AllOs,
                'm1_lancar_os'  => $m1LancarOs, 
                'm1_pct'        => round($m1Pct, 2),
                
                'cur_all_noa'   => (int)$cur['all_noa'],
                'cur_all_os'    => $curAllOs,
                'cur_lancar_os' => $curLancarOs, 
                'cur_pct'       => round($curPct, 2),

                'delta_noa'       => $deltaNoa,
                'delta_os'        => $curAllOs - $m1AllOs,
                'delta_os_lancar' => $deltaOsLancar,
                'delta_pct'       => $m1LancarOs > 0 ? round(($deltaOsLancar / $m1LancarOs) * 100, 2) : 0
            ];

            // Akumulasi Grand Total
            $grandTotal['m1_all_noa']   += (int)$m1['all_noa'];
            $grandTotal['m1_all_os']    += $m1AllOs;
            $grandTotal['m1_lancar_os'] += $m1LancarOs;

            $grandTotal['cur_all_noa']   += (int)$cur['all_noa'];
            $grandTotal['cur_all_os']    += $curAllOs;
            $grandTotal['cur_lancar_os'] += $curLancarOs;

            $grandTotal['delta_noa']       += $deltaNoa;
            $grandTotal['delta_os_lancar'] += $deltaOsLancar;
        }

        // Urutkan ASC by Kode
        usort($finalData, function($a, $b) { return strcmp($a['kode'], $b['kode']); });

        // Kalkulasi Persentase Grand Total
        $gtM1Pct  = $grandTotal['m1_all_os'] > 0 ? ($grandTotal['m1_lancar_os'] / $grandTotal['m1_all_os']) * 100 : 0;
        $gtCurPct = $grandTotal['cur_all_os'] > 0 ? ($grandTotal['cur_lancar_os'] / $grandTotal['cur_all_os']) * 100 : 0;

        $grandTotal['m1_pct']          = round($gtM1Pct, 2);
        $grandTotal['cur_pct']         = round($gtCurPct, 2);
        $grandTotal['delta_os']        = $grandTotal['cur_all_os'] - $grandTotal['m1_all_os'];
        $grandTotal['delta_pct']       = $grandTotal['m1_lancar_os'] > 0 ? round(($grandTotal['delta_os_lancar'] / $grandTotal['m1_lancar_os']) * 100, 2) : 0;

        $this->send(200, "Sukses", [
            'meta' => [
                'level'      => $isPusat ? 'PUSAT' : 'CABANG',
                'label_kode' => $isPusat ? 'KODE CABANG' : 'KODE KANKAS',
                'label_nama' => $isPusat ? 'NAMA CABANG' : 'NAMA KANKAS',
                'hitung_berdasarkan' => $saldoCol
            ],
            'grand_total' => $grandTotal,
            'data'        => $finalData
        ]);
    }

    /**
     * 1. REKAP UTAMA OTP BUCKET (Group by Tgl Jatuh Tempo + Migration)
     */
    public function getRekapOtpBucket($input = null) {
        set_time_limit(300); ini_set('memory_limit', '1024M');

        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = !empty($b['kode_kankas']) ? $b['kode_kankas'] : null; // 🔥 FIX: Tangkap filter kankas
        $typeB   = $b['type_bucket'] ?? 'fe_all'; // fe_all, 31-60, 61-90

        if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        $curTime  = strtotime($harian);
        $curMonth = date('n', $curTime);
        $curYear  = date('Y', $curTime);

        $kw_start = null; $kw_end = null;
        if ($korwil) {
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
        }

        // Tentukan Filter Bucket di M-1
        $whereBucket = "AND hari_menunggak BETWEEN 31 AND 90"; 
        if ($typeB === '31-60') $whereBucket = "AND hari_menunggak BETWEEN 31 AND 60";
        elseif ($typeB === '61-90') $whereBucket = "AND hari_menunggak BETWEEN 61 AND 90";

        // AMBIL DATA CLOSING (M1)
        $sqlM1 = "SELECT no_rekening, baki_debet, hari_menunggak as dpd_ori, DAY(tgl_jatuh_tempo) as tgl_ori 
                  FROM nominatif 
                  WHERE created BETWEEN :s1 AND :e1 
                  AND baki_debet > 0
                  $whereBucket"; 
        
        if ($kc && $kc !== '000') $sqlM1 .= " AND kode_cabang = :kc";
        elseif ($korwil && $kw_start && $kw_end) $sqlM1 .= " AND kode_cabang BETWEEN :kw_start AND :kw_end";
        if ($kankas) $sqlM1 .= " AND kode_group1 = :kankas"; // 🔥 FIX: Filter kankas di rekap

        $stmt1 = $this->pdo->prepare($sqlM1);
        $stmt1->bindValue(':s1', $s1); $stmt1->bindValue(':e1', $e1);
        if ($kc && $kc !== '000') $stmt1->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmt1->bindValue(':kw_start', $kw_start); $stmt1->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmt1->bindValue(':kankas', $kankas); // 🔥 FIX: Bind kankas
        $stmt1->execute();
        $dataM1 = $stmt1->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // AMBIL DATA HARIAN (CURRENT)
        $sqlCur = "SELECT no_rekening, baki_debet, hari_menunggak 
                   FROM nominatif 
                   WHERE created BETWEEN :s2 AND :e2";
        if ($kc && $kc !== '000') $sqlCur .= " AND kode_cabang = :kc";
        elseif ($korwil && $kw_start && $kw_end) $sqlCur .= " AND kode_cabang BETWEEN :kw_start AND :kw_end";
        if ($kankas) $sqlCur .= " AND kode_group1 = :kankas"; // 🔥 FIX: Filter kankas di current

        $stmt2 = $this->pdo->prepare($sqlCur);
        $stmt2->bindValue(':s2', $s2); $stmt2->bindValue(':e2', $e2);
        if ($kc && $kc !== '000') $stmt2->bindValue(':kc', $kc);
        elseif ($korwil && $kw_start && $kw_end) { $stmt2->bindValue(':kw_start', $kw_start); $stmt2->bindValue(':kw_end', $kw_end); }
        if ($kankas) $stmt2->bindValue(':kankas', $kankas); // 🔥 FIX: Bind kankas
        $stmt2->execute();
        $dataCur = $stmt2->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        // INISIALISASI BUCKET TANGGAL (1 - 31)
        $report = [];
        for ($i = 1; $i <= 31; $i++) {
            $report[$i] = [
                'tgl' => $i, 'm1_noa' => 0, 'm1_os' => 0,
                'btc_noa' => 0, 'btc_os' => 0, 'btc_pct' => 0,
                'backflow_noa' => 0, 'backflow_os' => 0, 'backflow_pct' => 0,
                'stay_noa' => 0, 'stay_os' => 0, 'stay_pct' => 0,
                'migrasi_noa' => 0, 'migrasi_os' => 0, 'migrasi_pct' => 0,
                'angsuran_os' => 0,
                'runoff_noa' => 0, 'runoff_os' => 0, 'runoff_pct' => 0
            ];
        }

        $grandTotal = [
            'm1_noa'=>0, 'm1_os'=>0, 'btc_noa'=>0, 'btc_os'=>0, 'btc_pct'=>0,
            'backflow_noa'=>0, 'backflow_os'=>0, 'backflow_pct'=>0,
            'stay_noa'=>0, 'stay_os'=>0, 'stay_pct'=>0,
            'migrasi_noa'=>0, 'migrasi_os'=>0, 'migrasi_pct'=>0,
            'angsuran_os'=>0,
            'runoff_noa'=>0, 'runoff_os'=>0, 'runoff_pct'=>0
        ];

        foreach ($dataM1 as $norek => $row) {
            $tglOri = (int)$row['tgl_ori']; 
            if ($tglOri < 1 || $tglOri > 31) continue;
            
            // Gunakan fungsi map tanggal yang Anda miliki
            $tglMap = $this->getMappedDay($tglOri, $curMonth, $curYear);
            $dpdOri = (int)$row['dpd_ori']; 
            $osM1   = (float)$row['baki_debet'];

            // Tentukan dynamic limit per nasabah
            if ($dpdOri >= 31 && $dpdOri <= 60) { $minB = 31; $maxB = 60; }
            else { $minB = 61; $maxB = 90; }

            $report[$tglMap]['m1_noa']++; $report[$tglMap]['m1_os'] += $osM1;
            $grandTotal['m1_noa']++; $grandTotal['m1_os'] += $osM1;

            if (!isset($dataCur[$norek])) {
                // Tidak ada di data harian = LUNAS
                $report[$tglMap]['runoff_noa']++; $report[$tglMap]['runoff_os'] += $osM1;
                $grandTotal['runoff_noa']++; $grandTotal['runoff_os'] += $osM1;
            } else {
                $osCur  = (float)$dataCur[$norek]['baki_debet'];
                $dpdCur = (int)$dataCur[$norek]['hari_menunggak'];

                if ($osCur <= 0) {
                    // Baki debet = 0 = LUNAS
                    $report[$tglMap]['runoff_noa']++; $report[$tglMap]['runoff_os'] += $osM1;
                    $grandTotal['runoff_noa']++; $grandTotal['runoff_os'] += $osM1;
                } else {
                    // 🔥 FIX: Pakai osCur (actual) bukan osM1 untuk OS kolom migrasi
                    if ($dpdCur == 0) {
                        $report[$tglMap]['btc_noa']++; $report[$tglMap]['btc_os'] += $osCur;
                        $grandTotal['btc_noa']++; $grandTotal['btc_os'] += $osCur;
                    } elseif ($dpdCur > 0 && $dpdCur < $minB) {
                        $report[$tglMap]['backflow_noa']++; $report[$tglMap]['backflow_os'] += $osCur;
                        $grandTotal['backflow_noa']++; $grandTotal['backflow_os'] += $osCur;
                    } elseif ($dpdCur >= $minB && $dpdCur <= $maxB) {
                        $report[$tglMap]['stay_noa']++; $report[$tglMap]['stay_os'] += $osCur;
                        $grandTotal['stay_noa']++; $grandTotal['stay_os'] += $osCur;
                    } elseif ($dpdCur > $maxB) {
                        $report[$tglMap]['migrasi_noa']++; $report[$tglMap]['migrasi_os'] += $osCur;
                        $grandTotal['migrasi_noa']++; $grandTotal['migrasi_os'] += $osCur;
                    }

                    // 🔥 Kolom ANGSURAN = selisih (osM1 - osCur) jika ada pengurangan
                    if ($osCur < $osM1) {
                        $selisih = $osM1 - $osCur;
                        $report[$tglMap]['angsuran_os'] += $selisih;
                        $grandTotal['angsuran_os'] += $selisih;
                    }
                }
            }
        }

        // Kalkulasi Persentase
        $calcPct = function($val, $tot) { return $tot > 0 ? round(($val / $tot) * 100, 2) : 0; };
        
        foreach ($report as &$r) {
            $r['btc_pct']      = $calcPct($r['btc_os'], $r['m1_os']);
            $r['backflow_pct'] = $calcPct($r['backflow_os'], $r['m1_os']);
            $r['stay_pct']     = $calcPct($r['stay_os'], $r['m1_os']);
            $r['migrasi_pct']  = $calcPct($r['migrasi_os'], $r['m1_os']);
            $r['runoff_pct']   = $calcPct($r['runoff_os'], $r['m1_os']);
        }

        $grandTotal['btc_pct']      = $calcPct($grandTotal['btc_os'], $grandTotal['m1_os']);
        $grandTotal['backflow_pct'] = $calcPct($grandTotal['backflow_os'], $grandTotal['m1_os']);
        $grandTotal['stay_pct']     = $calcPct($grandTotal['stay_os'], $grandTotal['m1_os']);
        $grandTotal['migrasi_pct']  = $calcPct($grandTotal['migrasi_os'], $grandTotal['m1_os']);
        $grandTotal['runoff_pct']   = $calcPct($grandTotal['runoff_os'], $grandTotal['m1_os']);

        return $this->send(200, "Sukses Rekap OTP Per Tanggal & Migration", [
            'meta' => ['m1' => $closing, 'cur' => $harian, 'type_bucket' => $typeB],
            'grand_total' => $grandTotal,
            'data' => array_values($report)
        ]);
    }

    /**
     * 2. DETAIL DATA OTP BUCKET (Filter per Tanggal Tagih + Status Migration)
     */
    public function getDetailOtpBucket($input = null) {
        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $kc      = !empty($b['kode_kantor']) ? str_pad($b['kode_kantor'], 3, '0', STR_PAD_LEFT) : null;
        $korwil  = !empty($b['korwil']) ? strtoupper($b['korwil']) : null;
        $kankas  = $b['kode_kankas'] ?? null; 
        $ao      = $b['kode_ao'] ?? null;
        $search  = trim($b['search'] ?? '');
        
        $tglMap  = isset($b['tgl_tagih']) ? (int)$b['tgl_tagih'] : null;
        $typeB   = $b['type_bucket'] ?? 'fe_all'; 
        $status  = strtoupper($b['status'] ?? 'ALL'); 
        $page    = $b['page'] ?? 1;
        $limit   = $b['limit'] ?? 10;
        $offset  = ($page - 1) * $limit;

        if (!$closing || !$harian) return $this->send(400, "Data kurang lengkap.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        // Logika Map Hari
        $daysStr = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";
        if ($tglMap) {
            $curTime = strtotime($harian);
            $month = date('n', $curTime); $year = date('Y', $curTime);
            $includedDays = [];
            for ($d = 1; $d <= 31; $d++) {
                if ($this->getMappedDay($d, $month, $year) == $tglMap) {
                    $includedDays[] = $d;
                }
            }
            if (!empty($includedDays)) $daysStr = implode(',', $includedDays);
            else $daysStr = $tglMap;
        }

        $kw_start = null; $kw_end = null;
        if ($korwil) {
            switch ($korwil) {
                case 'SEMARANG':   $kw_start = '001'; $kw_end = '007'; break;
                case 'SOLO':       $kw_start = '008'; $kw_end = '014'; break;
                case 'BANYUMAS':   $kw_start = '015'; $kw_end = '021'; break;
                case 'PEKALONGAN': $kw_start = '022'; $kw_end = '028'; break;
            }
        }

        // Tentukan Filter Type Bucket Asal (M-1)
        $whereBucket = "AND t1.hari_menunggak BETWEEN 31 AND 90"; 
        if ($typeB === '31-60') $whereBucket = "AND t1.hari_menunggak BETWEEN 31 AND 60";
        elseif ($typeB === '61-90') $whereBucket = "AND t1.hari_menunggak BETWEEN 61 AND 90";

        // Filter Logic Roll Rate / Migration Status pakai SQL Logic Aman
        $joinType = "LEFT JOIN";
        $whereStatus = "";
        
        if ($status === 'RUNOFF') {
            $whereStatus = "AND (t2.no_rekening IS NULL OR t2.baki_debet <= 0)";
        } elseif ($status === 'BTC') {
            $joinType = "JOIN";
            $whereStatus = "AND t2.baki_debet > 0 AND t2.hari_menunggak = 0";
        } elseif ($status === 'BACKFLOW') {
            $joinType = "JOIN";
            $whereStatus = "AND t2.baki_debet > 0 AND (
                (t1.hari_menunggak BETWEEN 31 AND 60 AND t2.hari_menunggak > 0 AND t2.hari_menunggak < 31) 
                OR 
                (t1.hari_menunggak BETWEEN 61 AND 90 AND t2.hari_menunggak > 0 AND t2.hari_menunggak < 61)
            )";
        } elseif ($status === 'STAY') {
            $joinType = "JOIN";
            $whereStatus = "AND t2.baki_debet > 0 AND (
                (t1.hari_menunggak BETWEEN 31 AND 60 AND t2.hari_menunggak BETWEEN 31 AND 60) 
                OR 
                (t1.hari_menunggak BETWEEN 61 AND 90 AND t2.hari_menunggak BETWEEN 61 AND 90)
            )";
        } elseif ($status === 'MIGRASI') {
            $joinType = "JOIN";
            $whereStatus = "AND t2.baki_debet > 0 AND (
                (t1.hari_menunggak BETWEEN 31 AND 60 AND t2.hari_menunggak > 60) 
                OR 
                (t1.hari_menunggak BETWEEN 61 AND 90 AND t2.hari_menunggak > 90)
            )";
        }

        // Base Query 
        $baseQuery = "FROM nominatif t1 
                      $joinType nominatif t2 ON t1.no_rekening = t2.no_rekening 
                          AND (t2.created BETWEEN :s2 AND :e2)
                      LEFT JOIN ao_kredit ao ON t1.kode_group2 = ao.kode_group2
                      LEFT JOIN tabungan tb ON t1.norek_tabungan = tb.no_rekening
                      LEFT JOIN kankas kn ON t1.kode_group1 = kn.kode_group1
                      LEFT JOIN (
                          SELECT 
                              no_rekening,
                              MAX(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT('$harian', '%Y-%m') THEN tgl_trans END) as tgl_trans_sekarang,
                              SUM(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT('$harian', '%Y-%m') THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) as total_bayar_sekarang,
                              MAX(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT(DATE_SUB('$harian', INTERVAL 1 MONTH), '%Y-%m') THEN tgl_trans END) as tgl_trans_lalu,
                              SUM(CASE WHEN DATE_FORMAT(tgl_trans, '%Y-%m') = DATE_FORMAT(DATE_SUB('$harian', INTERVAL 1 MONTH), '%Y-%m') THEN (COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) ELSE 0 END) as total_bayar_lalu
                          FROM transaksi_kredit
                          WHERE tgl_trans >= DATE_FORMAT(DATE_SUB('$harian', INTERVAL 1 MONTH), '%Y-%m-01')
                          GROUP BY no_rekening
                      ) trx ON t1.no_rekening = trx.no_rekening
                      WHERE (t1.created BETWEEN :s1 AND :e1)
                      AND t1.baki_debet > 0
                      AND DAY(t1.tgl_jatuh_tempo) IN ($daysStr)
                      $whereBucket
                      $whereStatus";
        
        if ($kc && $kc !== '000') $baseQuery .= " AND t1.kode_cabang = :kc";
        elseif ($korwil && $kw_start && $kw_end) $baseQuery .= " AND t1.kode_cabang BETWEEN :kw_start AND :kw_end";
        
        if ($kankas) $baseQuery .= " AND t1.kode_group1 = :kankas"; 
        if ($ao) $baseQuery .= " AND t1.kode_group2 = :ao";
        if ($search !== '') $baseQuery .= " AND (t1.no_rekening LIKE :search OR t1.nama_nasabah LIKE :search2)";

        $bindParams = function($stmt) use ($s1, $e1, $s2, $e2, $kc, $korwil, $kw_start, $kw_end, $kankas, $ao, $search) {
            $stmt->bindValue(':s1', $s1); $stmt->bindValue(':e1', $e1);
            $stmt->bindValue(':s2', $s2); $stmt->bindValue(':e2', $e2);
            if ($kc && $kc !== '000') $stmt->bindValue(':kc', $kc);
            elseif ($korwil && $kw_start && $kw_end) { $stmt->bindValue(':kw_start', $kw_start); $stmt->bindValue(':kw_end', $kw_end); }
            if ($kankas) $stmt->bindValue(':kankas', $kankas); 
            if ($ao) $stmt->bindValue(':ao', $ao);
            if ($search !== '') { $stmt->bindValue(':search', "%$search%"); $stmt->bindValue(':search2', "%$search%"); }
        };

        // Count Total Row
        $stmtCnt = $this->pdo->prepare("SELECT COUNT(1) $baseQuery");
        $bindParams($stmtCnt);
        $stmtCnt->execute();
        $total = $stmtCnt->fetchColumn();

        $cols = "t1.no_rekening, t1.nama_nasabah, t1.hari_menunggak as dpd_m1,
                 t1.alamat, t1.hp as no_hp, 
                 COALESCE(kn.deskripsi_group1, t1.kode_group1) as kankas,
                 t1.kode_group1,
                 COALESCE(tb.saldo_akhir, 0) as tabungan,
                 COALESCE(ao.nama_ao, t1.kode_group2) as nama_ao,
                 t1.kode_group2,
                 t1.tgl_jatuh_tempo, t1.jml_pinjaman, t1.tgl_realisasi,
                 t1.baki_debet as os_m1, 
                 COALESCE(t2.baki_debet, 0) as os_curr, 
                 COALESCE(t2.hari_menunggak, 0) as dpd_curr,
                 t2.kolektibilitas,
                 t2.tunggakan_pokok,
                 t2.tunggakan_bunga,
                 t2.hari_menunggak_pokok as dpd_pokok,
                 t2.hari_menunggak_bunga as dpd_bunga,
                 GREATEST(0, COALESCE(t2.tunggakan_pokok, 0) + COALESCE(t2.tunggakan_bunga, 0)) as totung,
                 trx.tgl_trans_sekarang,
                 COALESCE(trx.total_bayar_sekarang, 0) as total_bayar_sekarang,
                 trx.tgl_trans_lalu,
                 COALESCE(trx.total_bayar_lalu, 0) as total_bayar_lalu";
        
        $sqlData = "SELECT $cols $baseQuery ORDER BY t1.baki_debet DESC LIMIT :lim OFFSET :off";
        $stmt = $this->pdo->prepare($sqlData);
        $bindParams($stmt);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $osM1 = (float)$r['os_m1']; $osCur = (float)$r['os_curr']; 
            $dpdCur = (int)$r['dpd_curr']; $dpdM1 = (int)$r['dpd_m1'];
            $totung = (float)$r['totung']; $tabungan = (float)$r['tabungan'];
            
            if ($dpdM1 >= 31 && $dpdM1 <= 60) { $cMin = 31; $cMax = 60; }
            else { $cMin = 61; $cMax = 90; }

            if ($osCur <= 0) { $r['status_ket'] = 'RUNOFF (LUNAS)'; }
            elseif ($dpdCur == 0) { $r['status_ket'] = 'BTC (LANCAR)'; }
            elseif ($dpdCur > 0 && $dpdCur < $cMin) { $r['status_ket'] = 'BACKFLOW'; }
            elseif ($dpdCur >= $cMin && $dpdCur <= $cMax) { $r['status_ket'] = 'STAY'; }
            elseif ($dpdCur > $cMax) { $r['status_ket'] = 'MIGRASI (MEMBURUK)'; }

            $r['os_m1']=$osM1; $r['os_curr']=$osCur; 
            $r['bayar_pokok'] = ($osM1 > $osCur) ? ($osM1 - $osCur) : 0;
            $r['status_tabungan'] = (($tabungan * 0.015) > $totung) ? 'Aman' : 'Belum Aman';

            // 🔥 Format transaksi data
            $r['tgl_trans_sekarang'] = $r['tgl_trans_sekarang'] ?? null;
            $r['total_bayar_sekarang'] = (float)($r['total_bayar_sekarang'] ?? 0);
            $r['tgl_trans_lalu'] = $r['tgl_trans_lalu'] ?? null;
            $r['total_bayar_lalu'] = (float)($r['total_bayar_lalu'] ?? 0);
        }

        return $this->send(200, "Detail Data OTP By Tgl & Migration", [
            'pagination' => ['current_page' => $page, 'total_records' => (int)$total, 'total_pages' => ceil($total / $limit)],
            'data' => $rows
        ]);
    }

}
?>
