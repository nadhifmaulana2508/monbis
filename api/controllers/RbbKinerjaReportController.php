<?php

require_once __DIR__ . '/../helpers/response.php';

/**
 * Consolidated month-by-month source for the branch performance report.
 * Kept separate from the existing dashboard/report endpoints.
 */
class RbbKinerjaReportController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAnnualReport(array $input): void
    {
        set_time_limit(90);

        $closingText = trim((string)($input['harian_date'] ?? ''));
        $closing = DateTimeImmutable::createFromFormat('!Y-m-d', $closingText);
        if (!$closing || $closing->format('Y-m-d') !== $closingText) {
            sendResponse(422, 'Tanggal closing harus berformat YYYY-MM-DD.');
            return;
        }

        $scope = $this->resolveScope($input);
        if ($scope === null) return;

        $year = (int)$closing->format('Y');
        $closingMonth = (int)$closing->format('n');
        $previousYear = $year - 1;
        $monthDates = [];
        $previousYearDates = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthDates[$month] = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))
                ->modify('last day of this month')->format('Y-m-d');
            $previousYearDates[$month] = (new DateTimeImmutable(sprintf('%04d-%02d-01', $previousYear, $month)))
                ->modify('last day of this month')->format('Y-m-d');
        }
        $previousYearEnd = $previousYearDates[12];
        $reportDates = array_values(array_unique(array_merge(array_values($previousYearDates), array_values($monthDates))));

        try {
            $ledger = $this->loadLedger($reportDates, $scope);
            $credit = $this->loadCreditSnapshots($reportDates, $scope);
            $deposits = $this->loadDpkSnapshots($reportDates, $scope);
            $production = $this->loadProduction($year, $closing, $scope);
            $recovery = $this->loadRecovery($year, $closing, $scope);
            $targets = $this->loadTargets($year, $scope);

            $monthActualAvailable = [];
            for ($month = 1; $month <= $closingMonth; $month++) {
                $date = $monthDates[$month];
                $monthActualAvailable[$month] = isset($ledger[$date]) || isset($credit[$date]) || isset($deposits[$date]);
            }
            $latestMonth = 0;
            foreach ($monthActualAvailable as $month => $available) {
                if ($available) $latestMonth = (int)$month;
            }

            $productiveAverages = $this->productiveAssetAverages($ledger, $previousYearDates, $monthDates, $closingMonth);
            // Akun pendapatan dan beban tersimpan kumulatif YTD pada setiap closing.
            $incomeMonthly = $this->metricFromLedger($ledger, $monthDates, $closingMonth, '4');
            $expenseMonthly = $this->metricFromLedger($ledger, $monthDates, $closingMonth, '5');
            $profitMonthly = [];
            for ($month = 1; $month <= 12; $month++) {
                $profitMonthly[$month] = $incomeMonthly[$month] === null || $expenseMonthly[$month] === null
                    ? null
                    : $incomeMonthly[$month] - $expenseMonthly[$month];
            }

            $ratioByDate = [];
            foreach ($previousYearDates as $month => $date) {
                $ratioByDate[$date] = $this->calculateRatios(
                    $ledger[$date] ?? null,
                    $credit[$date] ?? null,
                    $deposits[$date] ?? null,
                    $productiveAverages[$date] ?? null,
                    $month,
                    $scope['type'] === 'consolidated'
                );
            }
            foreach ($monthDates as $month => $date) {
                if ($month > $closingMonth) continue;
                $ratioByDate[$date] = $this->calculateRatios(
                    $ledger[$date] ?? null,
                    $credit[$date] ?? null,
                    $deposits[$date] ?? null,
                    $productiveAverages[$date] ?? null,
                    $month,
                    $scope['type'] === 'consolidated'
                );
            }

            $monthlyAssets = $this->metricFromLedger($ledger, $monthDates, $closingMonth, 'asset', $scope['type'] === 'consolidated');
            $monthlyDpk = $this->metricFromDpk($deposits, $monthDates, $closingMonth, 'total');
            $monthlySavings = $this->metricFromDpk($deposits, $monthDates, $closingMonth, 'savings');
            $monthlyDeposits = $this->metricFromDpk($deposits, $monthDates, $closingMonth, 'deposit');
            $monthlySaldoBank = $this->metricFromCredit($credit, $monthDates, $closingMonth, 'saldo_bank');
            $monthlyBakiDebet = $this->metricFromCredit($credit, $monthDates, $closingMonth, 'baki_debet');
            $monthlyNplBank = $this->metricFromCredit($credit, $monthDates, $closingMonth, 'npl_saldo_bank');
            $monthlyNplBaki = $this->metricFromCredit($credit, $monthDates, $closingMonth, 'npl_baki_debet');
            $monthlyCkpn = $this->metricFromLedger($ledger, $monthDates, $closingMonth, 'ckpn');
            $monthlyDpd = [];
            foreach (['dpd0_saldo_bank','dpd1_30_saldo_bank','dpd31_60_saldo_bank','dpd61_90_saldo_bank','kl_saldo_bank','d_saldo_bank','m_saldo_bank',
                         'dpd0_baki_debet','dpd1_30_baki_debet','dpd31_60_baki_debet','dpd61_90_baki_debet','kl_baki_debet','d_baki_debet','m_baki_debet',
                         'dpd0_rr_saldo_bank','dpd0_rr_baki_debet'] as $key) {
                $monthlyDpd[$key] = $this->metricFromCredit($credit, $monthDates, $closingMonth, $key);
            }

            $monthlyCreditProduction = $this->fillMonthlyMap($production, $closingMonth);
            $monthlyRecovery = $this->fillMonthlyMap($recovery, $closingMonth);
            $sumTarget = function (string $code) use ($targets): ?float {
                if (!isset($targets[$code])) return null;
                $values = array_values(array_filter($targets[$code], static function ($value) { return $value !== null; }));
                return $values ? array_sum($values) : null;
            };
            $yearEndTarget = function (string $code) use ($targets): ?float {
                return isset($targets[$code][12]) ? (float)$targets[$code][12] : null;
            };

            $rows = [];
            $add = function (string $section, string $key, string $label, string $format, array $monthly, ?float $previous,
                ?float $annualTarget, ?string $targetCode = null, string $annualBasis = 'latest', bool $inverse = false,
                ?array $monthlyTargetOverride = null, bool $comparison = true) use (&$rows, $latestMonth, $targets, $monthActualAvailable): void {
                $monthlyTargets = $monthlyTargetOverride ?? ($targetCode !== null ? ($targets[$targetCode] ?? []) : []);
                $currentActual = $latestMonth > 0 ? ($monthly[$latestMonth] ?? null) : null;
                $currentTarget = $latestMonth > 0 ? ($monthlyTargets[$latestMonth] ?? null) : null;
                $yearActual = null;
                if ($annualBasis === 'sum') {
                    $availableValues = [];
                    for ($m = 1; $m <= 12; $m++) {
                        if (!empty($monthActualAvailable[$m]) && isset($monthly[$m]) && $monthly[$m] !== null) $availableValues[] = (float)$monthly[$m];
                    }
                    $yearActual = $availableValues ? array_sum($availableValues) : null;
                } elseif ($annualBasis === 'change') {
                    $yearActual = $currentActual !== null && $previous !== null ? $currentActual - $previous : null;
                } else {
                    $yearActual = $currentActual;
                }

                $currentDelta = $comparison && $currentActual !== null && $currentTarget !== null
                    ? $currentActual - $currentTarget : null;
                $yearDelta = $comparison && $yearActual !== null && $annualTarget !== null
                    ? $yearActual - $annualTarget : null;
                $currentAchievement = $comparison ? $this->achievement($currentActual, $currentTarget, $inverse) : null;
                $yearAchievement = $comparison ? $this->achievement($yearActual, $annualTarget, $inverse) : null;
                $rows[] = [
                    'section' => $section,
                    'key' => $key,
                    'label' => $label,
                    'format' => $format,
                    'previous' => $previous,
                    'monthly' => array_values($monthly),
                    'monthly_targets' => $this->normalizeMonths($monthlyTargets),
                    'year_target' => $annualTarget,
                    'current_actual' => $currentActual,
                    'current_target' => $currentTarget,
                    'current_delta' => $currentDelta,
                    'current_achievement' => $currentAchievement,
                    'year_actual' => $yearActual,
                    'year_delta' => $yearDelta,
                    'year_achievement' => $yearAchievement,
                    'comparison' => $comparison,
                ];
            };

            $add('Kinerja Bisnis', 'aset', 'Aset', 'nominal', $monthlyAssets, $this->ledgerMetric($ledger[$previousYearEnd] ?? null, 'asset', $scope['type'] === 'consolidated'), $yearEndTarget('1'), '1');
            $add('Kinerja Bisnis', 'pendapatan', 'Pendapatan', 'nominal', $incomeMonthly, $this->ledgerMetric($ledger[$previousYearEnd] ?? null, '4'), $yearEndTarget('6'), '6');
            $add('Kinerja Bisnis', 'biaya', 'Biaya', 'nominal', $expenseMonthly, $this->ledgerMetric($ledger[$previousYearEnd] ?? null, '5'), $yearEndTarget('7'), '7');
            $previousProfit = $this->ledgerMetric($ledger[$previousYearEnd] ?? null, '4') !== null && $this->ledgerMetric($ledger[$previousYearEnd] ?? null, '5') !== null
                ? $this->ledgerMetric($ledger[$previousYearEnd], '4') - $this->ledgerMetric($ledger[$previousYearEnd], '5') : null;
            $add('Kinerja Bisnis', 'laba', 'Laba (Rugi) Sebelum Pajak', 'nominal', $profitMonthly, $previousProfit, $yearEndTarget('8'), '8');

            $productionTargets = array_replace(array_fill(0, 12, null), $targets['31'] ?? []);
            $rows[] = [
                'section' => 'Realisasi Kredit', 'key' => 'rbb_produksi', 'label' => 'a. RBB Produksi Kredit', 'format' => 'nominal',
                'previous' => null, 'monthly' => $this->normalizeMonths($productionTargets), 'monthly_targets' => $this->normalizeMonths($productionTargets),
                'year_target' => $sumTarget('31'), 'current_actual' => null, 'current_target' => null, 'current_delta' => null,
                'current_achievement' => null, 'year_actual' => null, 'year_delta' => null, 'year_achievement' => null, 'comparison' => false,
            ];
            $add('Realisasi Kredit', 'realisasi_mtm', 'b. Realisasi Month to Month', 'nominal', $monthlyCreditProduction, null, $sumTarget('31'), '31', 'sum');

            $add('Dana Pihak Ketiga (DPK)', 'dpk', 'Dana Pihak Ketiga (DPK)', 'nominal', $monthlyDpk, $this->dpkMetric($deposits[$previousYearEnd] ?? null, 'total'), $yearEndTarget('2'), '2');
            $add('Dana Pihak Ketiga (DPK)', 'tabungan', 'a. Tabungan', 'nominal', $monthlySavings, $this->dpkMetric($deposits[$previousYearEnd] ?? null, 'savings'), $yearEndTarget('3'), '3');
            $add('Dana Pihak Ketiga (DPK)', 'deposito', 'b. Deposito', 'nominal', $monthlyDeposits, $this->dpkMetric($deposits[$previousYearEnd] ?? null, 'deposit'), $yearEndTarget('4'), '4');

            $add('KYD (Saldo Bank)', 'kyd_saldo_bank', 'KYD (Saldo Bank)', 'nominal', $monthlySaldoBank, $credit[$previousYearEnd]['saldo_bank'] ?? null, $yearEndTarget('5'), '5');
            foreach ([
                ['dpd0_saldo_bank','DPD 0'], ['dpd1_30_saldo_bank','DPD 1–30'], ['dpd31_60_saldo_bank','DPD 31–60'],
                ['dpd61_90_saldo_bank','DPD 61–90'], ['kl_saldo_bank','Kurang Lancar (KL)'], ['d_saldo_bank','Diragukan (D)'], ['m_saldo_bank','Macet (M)'],
            ] as [$key,$label]) $add('KYD (Saldo Bank)', $key, $label, 'nominal', $monthlyDpd[$key], $credit[$previousYearEnd][$key] ?? null, null, null);

            $add('KYD OSC', 'kyd_osc', 'KYD OSC (Baki Debet)', 'nominal', $monthlyBakiDebet, $credit[$previousYearEnd]['baki_debet'] ?? null, $yearEndTarget('64'), '64');
            foreach ([
                ['dpd0_baki_debet','DPD 0'], ['dpd1_30_baki_debet','DPD 1–30'], ['dpd31_60_baki_debet','DPD 31–60'],
                ['dpd61_90_baki_debet','DPD 61–90'], ['kl_baki_debet','Kurang Lancar (KL)'], ['d_baki_debet','Diragukan (D)'], ['m_baki_debet','Macet (M)'],
            ] as [$key,$label]) $add('KYD OSC', $key, $label, 'nominal', $monthlyDpd[$key], $credit[$previousYearEnd][$key] ?? null, null, null);

            $add('NPL', 'npl_saldo_bank', 'Kredit NPL (Saldo Bank)', 'nominal', $monthlyNplBank, $credit[$previousYearEnd]['npl_saldo_bank'] ?? null, null, null);
            $add('NPL', 'npl_osc', 'Kredit NPL (OSC)', 'nominal', $monthlyNplBaki, $credit[$previousYearEnd]['npl_baki_debet'] ?? null, null, null);
            $add('NPL', 'ckpn', 'CKPN Kredit', 'nominal', $monthlyCkpn, $this->ledgerMetric($ledger[$previousYearEnd] ?? null, 'ckpn'), $yearEndTarget('70'), '70');
            $add('NPL', 'recovery_ph', 'Recovery PH (Pokok)', 'nominal', $monthlyRecovery, null, null, null, 'sum');

            $add('Repayment Rate (RR)', 'rr_saldo_bank', 'RR – DPD 0 Saldo Bank', 'nominal', $monthlyDpd['dpd0_rr_saldo_bank'], $credit[$previousYearEnd]['dpd0_rr_saldo_bank'] ?? null, null, null);
            $add('Repayment Rate (RR)', 'rr_baki_debet', 'RR – DPD 0 Baki Debet', 'nominal', $monthlyDpd['dpd0_rr_baki_debet'], $credit[$previousYearEnd]['dpd0_rr_baki_debet'] ?? null, null, null);

            $ratioDefinitions = [
                ['kap','a. KAP','kap',false], ['ppap','b. PPAP terhadap PPAPWD','ppap',false], ['rr','c. RR','rr',false],
                ['npl_gross','d. NPL Gross','npl_gross',true], ['npl_netto','1) Netto','npl_netto',true],
                ['credit_productive','e. Kredit terhadap Total Aset Produktif','credit_productive',false], ['roa','f. ROA','roa',false],
                ['nim','g. NIM','nim',false], ['bopo','h. BOPO','bopo',false], ['cash','i. Cash Ratio','cash',false],
                ['ldr','j. LDR','ldr',false], ['casa','k. CASA','casa',false],
            ];
            $ratioTargets = $this->deriveRatioTargets($targets);
            foreach ($ratioDefinitions as [$key,$label,$targetKey,$inverse]) {
                $series = [];
                foreach ($monthDates as $month => $date) {
                    $series[$month] = $month <= $closingMonth ? ($ratioByDate[$date][$key] ?? null) : null;
                }
                $previousRatio = $ratioByDate[$previousYearEnd][$key] ?? null;
                $ratioTargetSeries = $ratioTargets[$targetKey] ?? array_fill(1, 12, null);
                $yearRatioTarget = $ratioTargetSeries[12] ?? null;
                $add('Rasio Keuangan', $key, $label, 'ratio', $series, $previousRatio, $yearRatioTarget, null, 'latest', $inverse, $ratioTargetSeries);
            }

            $monthLabels = [];
            $monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            for ($month = 1; $month <= 12; $month++) {
                $monthLabels[] = [
                    'month' => $month,
                    'label' => $monthNames[$month - 1],
                    'closing_date' => $monthDates[$month],
                    'available' => $month <= $closingMonth && !empty($monthActualAvailable[$month]),
                ];
            }

            $scopeLabel = $this->scopeLabel($scope);
            sendResponse(200, 'Laporan kinerja tahunan berhasil dimuat.', [
                'meta' => [
                    'year' => $year,
                    'harian_date' => $closingText,
                    'closing_month' => $closingMonth,
                    'latest_actual_month' => $latestMonth,
                    'latest_actual_date' => $latestMonth > 0 ? $monthDates[$latestMonth] : null,
                    'scope_type' => $scope['type'],
                    'scope_value' => $scope['value'],
                    'scope_label' => $scopeLabel,
                    'months' => $monthLabels,
                    'notes' => [
                        'Saldo nominal dan rasio diambil dari snapshot closing bulanan.',
                        'Pendapatan, beban, dan laba merupakan saldo kumulatif tahun berjalan pada closing bulanan.',
                        'RR nominal adalah baki debet/saldo bank pada DPD 0 dan kolektibilitas L atau DP.',
                        'Kinerja SDM menggunakan jumlah pegawai aktif dari roster SIMPEG saat ini karena histori pegawai per closing tidak tersedia.',
                    ],
                ],
                'rows' => $rows,
            ]);
        } catch (Throwable $error) {
            error_log('RBB annual performance report error: ' . $error->getMessage());
            sendResponse(500, 'Data laporan kinerja tahunan belum dapat dimuat.');
        }
    }

    private function resolveScope(array $input): ?array
    {
        $ranges = [
            'SEMARANG' => ['001', '007'],
            'SOLO' => ['008', '014'],
            'BANYUMAS' => ['015', '021'],
            'PEKALONGAN' => ['022', '028'],
        ];
        $korwil = strtoupper(trim((string)($input['korwil'] ?? '')));
        $office = trim((string)($input['kode_kantor'] ?? '000'));
        if ($korwil !== '') {
            if (!isset($ranges[$korwil])) {
                sendResponse(422, 'Korwil tidak dikenali.');
                return null;
            }
            return ['type' => 'korwil', 'value' => $korwil, 'range' => $ranges[$korwil], 'target_codes' => range((int)$ranges[$korwil][0], (int)$ranges[$korwil][1]), 'office' => null];
        }
        if ($office === '' || $office === '000' || strtolower($office) === 'konsolidasi') {
            return ['type' => 'consolidated', 'value' => '000', 'range' => ['001', '028'], 'target_codes' => range(1, 28), 'office' => null];
        }
        $office = str_pad($office, 3, '0', STR_PAD_LEFT);
        if (!preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', $office)) {
            sendResponse(422, 'Kode kantor harus berada pada rentang 001 sampai 028.');
            return null;
        }
        return ['type' => 'branch', 'value' => $office, 'range' => [$office, $office], 'target_codes' => [(int)$office], 'office' => $office];
    }

    private function scopeLabel(array $scope): string
    {
        if ($scope['type'] === 'korwil') return 'Korwil ' . ucfirst(strtolower((string)$scope['value']));
        if ($scope['type'] === 'consolidated') return 'Konsolidasi';
        try {
            $stmt = $this->pdo->prepare('SELECT nama_kantor FROM kode_kantor WHERE kode_kantor = ? LIMIT 1');
            $stmt->execute([$scope['office']]);
            $name = trim((string)$stmt->fetchColumn());
            if ($name !== '') return $scope['office'] . ' – ' . $name;
        } catch (Throwable $ignored) {}
        return 'Kantor ' . $scope['office'];
    }

    private function officeFilter(string $column, array $scope, bool $includeHeadOffice = false): string
    {
        if ($scope['type'] === 'branch') return "{$column} = '" . $scope['office'] . "'";
        $start = $scope['range'][0];
        $end = $scope['range'][1];
        if ($includeHeadOffice && $scope['type'] === 'consolidated') $start = '000';
        return "{$column} BETWEEN '{$start}' AND '{$end}'";
    }

    private function datePlaceholders(array $dates, string $prefix = 'd'): array
    {
        $placeholders = [];
        $params = [];
        foreach (array_values($dates) as $index => $date) {
            $key = ':' . $prefix . $index;
            $placeholders[] = $key;
            $params[$key] = $date;
        }
        return [$placeholders, $params];
    }

    private function loadLedger(array $dates, array $scope): array
    {
        $codes = [
            '1','2','3','4','5','101','102','103','104','10401','10402','105','106','10601','10602','10604','10605','10606',
            '107','108','109','110','11102','112','113','116','117','118','119','120','121','201','20202','204','20401','20402',
            '205','20603','210','211','30106','401','40101','501','50101'
        ];
        [$datePlaceholders, $params] = $this->datePlaceholders($dates, 'ledger_date_');
        $codePlaceholders = [];
        foreach ($codes as $index => $code) {
            $key = ':ledger_code_' . $index;
            $codePlaceholders[] = $key;
            $params[$key] = $code;
        }
        $officeFilter = $this->officeFilter('kode_kantor', $scope, true);
        $sql = "SELECT tanggal, kode_perk, SUM(COALESCE(saldo_akhir,0)) AS saldo
                FROM acc_history
                WHERE tanggal IN (" . implode(',', $datePlaceholders) . ")
                  AND {$officeFilter}
                  AND kode_perk IN (" . implode(',', $codePlaceholders) . ")
                GROUP BY tanggal, kode_perk";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $date = (string)$row['tanggal'];
            $code = (string)$row['kode_perk'];
            if (!isset($rows[$date])) $rows[$date] = [];
            $rows[$date][$code] = (float)($row['saldo'] ?? 0);
        }
        return $rows;
    }

    private function loadCreditSnapshots(array $dates, array $scope): array
    {
        [$datePlaceholders, $params] = $this->datePlaceholders($dates, 'credit_date_');
        $officeFilter = $this->officeFilter('kode_cabang', $scope);
        $sql = "SELECT created,
                    SUM(COALESCE(saldo_bank,0)) AS saldo_bank,
                    SUM(COALESCE(baki_debet,0)) AS baki_debet,
                    SUM(CASE WHEN hari_menunggak = 0 AND kolektibilitas IN ('L','DP') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS dpd0_saldo_bank,
                    SUM(CASE WHEN hari_menunggak BETWEEN 1 AND 30 AND kolektibilitas IN ('L','DP') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS dpd1_30_saldo_bank,
                    SUM(CASE WHEN hari_menunggak BETWEEN 31 AND 60 AND kolektibilitas IN ('L','DP') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS dpd31_60_saldo_bank,
                    SUM(CASE WHEN hari_menunggak BETWEEN 61 AND 90 AND kolektibilitas IN ('L','DP') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS dpd61_90_saldo_bank,
                    SUM(CASE WHEN kolektibilitas = 'KL' THEN COALESCE(saldo_bank,0) ELSE 0 END) AS kl_saldo_bank,
                    SUM(CASE WHEN kolektibilitas = 'D' THEN COALESCE(saldo_bank,0) ELSE 0 END) AS d_saldo_bank,
                    SUM(CASE WHEN kolektibilitas = 'M' THEN COALESCE(saldo_bank,0) ELSE 0 END) AS m_saldo_bank,
                    SUM(CASE WHEN kolektibilitas IN ('KL','D','M') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS npl_saldo_bank,
                    SUM(CASE WHEN hari_menunggak = 0 AND kolektibilitas IN ('L','DP') THEN COALESCE(baki_debet,0) ELSE 0 END) AS dpd0_baki_debet,
                    SUM(CASE WHEN hari_menunggak BETWEEN 1 AND 30 AND kolektibilitas IN ('L','DP') THEN COALESCE(baki_debet,0) ELSE 0 END) AS dpd1_30_baki_debet,
                    SUM(CASE WHEN hari_menunggak BETWEEN 31 AND 60 AND kolektibilitas IN ('L','DP') THEN COALESCE(baki_debet,0) ELSE 0 END) AS dpd31_60_baki_debet,
                    SUM(CASE WHEN hari_menunggak BETWEEN 61 AND 90 AND kolektibilitas IN ('L','DP') THEN COALESCE(baki_debet,0) ELSE 0 END) AS dpd61_90_baki_debet,
                    SUM(CASE WHEN kolektibilitas = 'KL' THEN COALESCE(baki_debet,0) ELSE 0 END) AS kl_baki_debet,
                    SUM(CASE WHEN kolektibilitas = 'D' THEN COALESCE(baki_debet,0) ELSE 0 END) AS d_baki_debet,
                    SUM(CASE WHEN kolektibilitas = 'M' THEN COALESCE(baki_debet,0) ELSE 0 END) AS m_baki_debet,
                    SUM(CASE WHEN kolektibilitas IN ('KL','D','M') THEN COALESCE(baki_debet,0) ELSE 0 END) AS npl_baki_debet,
                    SUM(CASE WHEN hari_menunggak = 0 AND kolektibilitas IN ('L','DP') THEN COALESCE(saldo_bank,0) ELSE 0 END) AS dpd0_rr_saldo_bank,
                    SUM(CASE WHEN hari_menunggak = 0 AND kolektibilitas IN ('L','DP') THEN COALESCE(baki_debet,0) ELSE 0 END) AS dpd0_rr_baki_debet
                FROM nominatif
                WHERE created IN (" . implode(',', $datePlaceholders) . ")
                  AND {$officeFilter}
                GROUP BY created";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $date = (string)$row['created'];
            foreach ($row as $key => $value) {
                if ($key !== 'created') $row[$key] = (float)($value ?? 0);
            }
            $rows[$date] = $row;
        }
        return $rows;
    }

    private function loadDpkSnapshots(array $dates, array $scope): array
    {
        [$savingDatePlaceholders, $savingParams] = $this->datePlaceholders($dates, 'saving_date_');
        [$depositDatePlaceholders, $depositParams] = $this->datePlaceholders($dates, 'deposit_date_');
        $params = array_merge($savingParams, $depositParams);
        $savingsOffice = $this->officeFilter('kode_kantor', $scope);
        $depositOffice = $this->officeFilter('kode_kantor', $scope);
        $sql = "SELECT tanggal,
                    SUM(tabungan) AS tabungan,
                    SUM(deposito) AS deposito
                FROM (
                    SELECT created AS tanggal, SUM(COALESCE(saldo,0)) AS tabungan, 0 AS deposito
                    FROM nominatif_tabungan
                    WHERE created IN (" . implode(',', $savingDatePlaceholders) . ") AND {$savingsOffice}
                    GROUP BY created
                    UNION ALL
                    SELECT created AS tanggal, 0 AS tabungan, SUM(COALESCE(saldo_akhir,0)) AS deposito
                    FROM nominatif_deposito
                    WHERE created IN (" . implode(',', $depositDatePlaceholders) . ") AND {$depositOffice}
                    GROUP BY created
                ) dpk_source
                GROUP BY tanggal";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $date = (string)$row['tanggal'];
            $savings = (float)($row['tabungan'] ?? 0);
            $deposit = (float)($row['deposito'] ?? 0);
            $rows[$date] = ['savings' => $savings, 'deposit' => $deposit, 'total' => $savings + $deposit];
        }
        return $rows;
    }

    private function loadProduction(int $year, DateTimeImmutable $closing, array $scope): array
    {
        $officeFilter = $this->officeFilter('kode_kantor', $scope);
        $start = sprintf('%04d-01-01', $year);
        $end = $closing->modify('+1 day')->format('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT MONTH(tanggal_realisasi) AS bulan, SUM(COALESCE(realisasi_pokok,0)) AS nilai
            FROM update_realisasi_kredit
            WHERE kode_trans = '110' AND tanggal_realisasi >= :start_date AND tanggal_realisasi < :end_date AND {$officeFilter}
            GROUP BY MONTH(tanggal_realisasi)");
        $stmt->execute([':start_date' => $start, ':end_date' => $end]);
        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) $map[(int)$row['bulan']] = (float)($row['nilai'] ?? 0);
        return $map;
    }

    private function loadRecovery(int $year, DateTimeImmutable $closing, array $scope): array
    {
        $officeFilter = $this->officeFilter('kode_kantor', $scope);
        $start = sprintf('%04d-01-01', $year);
        $end = $closing->modify('+1 day')->format('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT MONTH(tanggal_transaksi) AS bulan, SUM(COALESCE(pokok,0)) AS nilai
            FROM transaksi_ph WHERE tanggal_transaksi >= :start_date AND tanggal_transaksi < :end_date AND {$officeFilter}
            GROUP BY MONTH(tanggal_transaksi)");
        $stmt->execute([':start_date' => $start, ':end_date' => $end]);
        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) $map[(int)$row['bulan']] = (float)($row['nilai'] ?? 0);
        return $map;
    }

    private function loadTargets(int $year, array $scope): array
    {
        $columns = [];
        $nullChecks = [];
        foreach ($scope['target_codes'] as $code) {
            $column = 'r.' . chr(96) . str_pad((string)$code, 3, '0', STR_PAD_LEFT) . chr(96);
            $nullChecks[] = $column . ' IS NULL';
            $columns[] = 'COALESCE(' . $column . ',0)';
        }
        $targetExpression = implode(' + ', $columns);
        $targetHasValue = 'NOT (' . implode(' AND ', $nullChecks) . ')';
        $start = sprintf('%04d-01-01', $year);
        $end = sprintf('%04d-01-01', $year + 1);
        $stmt = $this->pdo->prepare("SELECT r.periode, r.kode_monbis, MAX(CASE WHEN {$targetHasValue} THEN {$targetExpression} ELSE NULL END) AS target
            FROM rbb r WHERE r.periode >= :start_date AND r.periode < :end_date
            GROUP BY r.periode, r.kode_monbis");
        $stmt->execute([':start_date' => $start, ':end_date' => $end]);
        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $month = (int)date('n', strtotime((string)$row['periode']));
            $code = (string)$row['kode_monbis'];
            if (!isset($map[$code])) $map[$code] = [];
            $map[$code][$month] = $row['target'] === null ? null : (float)$row['target'];
        }
        return $map;
    }

    private function deriveRatioTargets(array $targets): array
    {
        $result = [];
        $ratio = static function (?float $numerator, ?float $denominator): ?float {
            if ($numerator === null || $denominator === null || abs($denominator) < 0.000001) return null;
            return $numerator / $denominator * 100;
        };
        for ($month = 1; $month <= 12; $month++) {
            $target = static function (string $code) use ($targets, $month): ?float {
                return isset($targets[$code][$month]) ? (float)$targets[$code][$month] : null;
            };
            $asset = $target('95') ?? $target('1');
            $credit = $target('63') ?? $target('5');
            $dpk = $target('105') ?? $target('2');
            $income = $target('196') ?? $target('6');
            $expense = $target('258') ?? $target('7');
            $profit = $target('261') ?? $target('8');
            if ($profit === null && $income !== null && $expense !== null) $profit = $income - $expense;
            $npl = $target('56');
            $ckpn = $target('70');
            $productive = null;
            $productiveParts = [$target('61'), $target('64'), $target('65')];
            if (!in_array(null, $productiveParts, true)) $productive = array_sum($productiveParts);
            $savings = $target('106') ?? $target('27');
            $cashNumerator = $target('57') !== null && $target('61') !== null ? $target('57') + $target('61') : null;
            $cashParts = [$target('96'), $dpk, $target('120'), $target('110'), $target('117')];
            $cashDenominator = in_array(null, $cashParts, true) ? null : array_sum($cashParts);
            $interestIncome = $target('163');
            $interestExpense = $target('198');

            $result['kap'][$month] = $ratio($npl, $credit);
            $result['ppap'][$month] = $ckpn === null ? null : $ratio(abs($ckpn), $npl);
            $result['rr'][$month] = null;
            $result['npl_gross'][$month] = $ratio($npl, $credit);
            $result['npl_netto'][$month] = $npl === null || $ckpn === null ? null : $ratio($npl - abs($ckpn), $credit);
            $result['credit_productive'][$month] = $ratio($credit, $productive);
            $result['roa'][$month] = $ratio($profit, $asset);
            if ($result['roa'][$month] !== null) $result['roa'][$month] *= 12 / $month;
            $result['nim'][$month] = $interestIncome === null || $interestExpense === null ? null : $ratio($interestIncome - $interestExpense, $productive);
            if ($result['nim'][$month] !== null) $result['nim'][$month] *= 12 / $month;
            $result['bopo'][$month] = $ratio($expense, $income);
            $result['cash'][$month] = $ratio($cashNumerator, $cashDenominator);
            $result['ldr'][$month] = $ratio($credit, $dpk);
            $result['casa'][$month] = $ratio($savings, $dpk);
        }
        return $result;
    }

    private function normalizeMonths(array $values): array
    {
        $normalized = [];
        for ($month = 1; $month <= 12; $month++) {
            $normalized[] = array_key_exists($month, $values) ? $values[$month] : null;
        }
        return $normalized;
    }

    private function productiveAssetAverages(array $ledger, array $previousYearDates, array $monthDates, int $closingMonth): array
    {
        $out = [];
        foreach ([[$previousYearDates, 12], [$monthDates, $closingMonth]] as [$dates, $lastMonth]) {
            $total = 0.0;
            $count = 0;
            foreach ($dates as $month => $date) {
                if ((int)$month > $lastMonth) break;
                if (!isset($ledger[$date])) continue;
                $total += $this->ledgerSum($ledger[$date], ['104','10601','10606']);
                $count++;
                $out[$date] = $total / $count;
            }
        }
        return $out;
    }

    private function metricFromLedger(array $ledger, array $monthDates, int $closingMonth, string $metric, bool $subtractHeadOffice = false): array
    {
        $out = array_fill(1, 12, null);
        for ($month = 1; $month <= $closingMonth; $month++) {
            $row = $ledger[$monthDates[$month]] ?? null;
            $out[$month] = $row === null ? null : $this->ledgerMetric($row, $metric, $subtractHeadOffice);
        }
        return $out;
    }

    private function metricFromCredit(array $credit, array $monthDates, int $closingMonth, string $metric): array
    {
        $out = array_fill(1, 12, null);
        for ($month = 1; $month <= $closingMonth; $month++) {
            $row = $credit[$monthDates[$month]] ?? null;
            $out[$month] = $row === null ? null : (float)($row[$metric] ?? 0);
        }
        return $out;
    }

    private function metricFromDpk(array $dpk, array $monthDates, int $closingMonth, string $metric): array
    {
        $out = array_fill(1, 12, null);
        for ($month = 1; $month <= $closingMonth; $month++) {
            $row = $dpk[$monthDates[$month]] ?? null;
            $out[$month] = $row === null ? null : (float)($row[$metric] ?? 0);
        }
        return $out;
    }

    private function fillMonthlyMap(array $source, int $closingMonth): array
    {
        $out = array_fill(1, 12, null);
        for ($month = 1; $month <= $closingMonth; $month++) {
            $out[$month] = array_key_exists($month, $source) ? (float)$source[$month] : 0.0;
        }
        return $out;
    }

    private function calculateRatios(?array $ledger, ?array $credit, ?array $dpk, ?float $averageProductive,
        int $month, bool $subtractHeadOffice = false): array
    {
        if ($ledger === null || $credit === null) return [];
        $value = function (array $row, string $code): float { return (float)($row[$code] ?? 0); };
        $divide = static function (float $numerator, float $denominator): ?float {
            return abs($denominator) < 0.000001 ? null : $numerator / $denominator * 100;
        };
        $npl = (float)($credit['npl_baki_debet'] ?? 0);
        $totalBaki = (float)($credit['baki_debet'] ?? 0);
        $ckpn = abs($value($ledger, '107'));
        $assets = (float)($this->ledgerMetric($ledger, 'asset', $subtractHeadOffice) ?? 0);
        $assetProductive = $averageProductive;
        $profitAnnualized = (($value($ledger, '4') - $value($ledger, '5')) / max(1, $month)) * 12;
        $interestNetAnnualized = (($value($ledger, '40101') - $value($ledger, '50101')) / max(1, $month)) * 12;
        $dpkTotal = $dpk === null ? $value($ledger, '20401') + $value($ledger, '20402') : (float)($dpk['total'] ?? 0);
        $funds = $value($ledger, '204') + $value($ledger, '20603') + $value($ledger, '30106');
        $cash = $value($ledger, '101') + $value($ledger, '10401') + $value($ledger, '10402');
        $currentDebt = $value($ledger, '201') + $value($ledger, '20401') + $value($ledger, '20202') + $value($ledger, '205') + $value($ledger, '211');
        $dpdZero = (float)($credit['dpd0_saldo_bank'] ?? 0);

        return [
            'kap' => $divide($npl, $value($ledger, '106')),
            'ppap' => $divide($ckpn, $npl),
            'rr' => $divide($dpdZero, (float)($credit['saldo_bank'] ?? 0)),
            'npl_gross' => $divide($npl, $totalBaki),
            'npl_netto' => $divide($npl - $ckpn, $totalBaki),
            'credit_productive' => $assetProductive === null || $assetProductive <= 0 ? null : $divide($totalBaki, $assetProductive),
            'roa' => $divide($profitAnnualized, $assets),
            'nim' => $assetProductive === null || $assetProductive <= 0 ? null : $divide($interestNetAnnualized, $assetProductive),
            'bopo' => $divide($value($ledger, '501'), $value($ledger, '401')),
            'cash' => $divide($cash, $currentDebt),
            'ldr' => $divide($totalBaki, $funds),
            'casa' => $dpkTotal > 0 ? ((float)($dpk['savings'] ?? $value($ledger, '20401')) / $dpkTotal * 100) : null,
        ];
    }

    private function ledgerSum(?array $ledger, array $codes): float
    {
        if ($ledger === null) return 0.0;
        $sum = 0.0;
        foreach ($codes as $code) $sum += (float)($ledger[$code] ?? 0);
        return $sum;
    }

    private function ledgerMetric(?array $ledger, string $metric, bool $subtractHeadOffice = false)
    {
        if ($ledger === null) return null;
        if ($metric === 'asset') {
            $assets = $this->ledgerSum($ledger, ['101','102','103','104','105','10601','10602','10604','10605','10606','107','108','109','110','11102','112','113','116','117','118','119','120','121']);
            return $subtractHeadOffice ? $assets - (float)($ledger['210'] ?? 0) : $assets;
        }
        if ($metric === 'ckpn') return abs((float)($ledger['107'] ?? 0));
        return isset($ledger[$metric]) ? (float)$ledger[$metric] : null;
    }

    private function dpkMetric(?array $dpk, string $metric)
    {
        return $dpk === null ? null : (float)($dpk[$metric] ?? 0);
    }

    private function achievement(?float $actual, ?float $target, bool $inverse): ?float
    {
        if ($actual === null || $target === null || abs($target) < 0.000001 || ($inverse && abs($actual) < 0.000001)) return null;
        return $inverse ? round($target / $actual * 100, 2) : round($actual / $target * 100, 2);
    }
}
