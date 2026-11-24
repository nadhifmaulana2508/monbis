<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';

final class ProyeksiRrController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getMetadata(string $requestedBranch = 'ALL'): void
    {
        try {
            $branch = $this->normalizeBranch($requestedBranch);
            $scope = "WHERE kode_cabang BETWEEN '001' AND '028'";
            $params = [];
            if ($branch !== 'ALL') {
                $scope .= ' AND kode_cabang = :branch';
                $params[':branch'] = $branch;
            }
            $dateStmt = $this->pdo->prepare("SELECT DATE(created) FROM nominatif {$scope} ORDER BY created DESC LIMIT 1");
            $dateStmt->execute($params);
            $latestDate = $dateStmt->fetchColumn();
            $latestDate = $latestDate === false ? null : (string)$latestDate;

            $defaultClosingDate = null;
            if ($latestDate !== null) {
                $candidate = $this->parseDate($latestDate)?->modify('first day of this month')->modify('-1 day')->format('Y-m-d');
                if ($candidate !== null && $candidate !== false) {
                    $closingStart = $candidate . ' 00:00:00';
                    $closingEnd = $this->parseDate($candidate)->modify('+1 day')->format('Y-m-d') . ' 00:00:00';
                    $closingParams = [':closing_start' => $closingStart, ':closing_end' => $closingEnd];
                    $closingScope = "AND kode_cabang BETWEEN '001' AND '028'";
                    if ($branch !== 'ALL') {
                        $closingScope .= ' AND kode_cabang = :branch';
                        $closingParams[':branch'] = $branch;
                    }
                    $closingStmt = $this->pdo->prepare("SELECT 1 FROM nominatif WHERE created >= :closing_start AND created < :closing_end {$closingScope} LIMIT 1");
                    $closingStmt->execute($closingParams);
                    if ($closingStmt->fetchColumn() !== false) $defaultClosingDate = $candidate;
                }
            }

            $branches = $this->pdo->query(
                "SELECT kode_kantor AS code, COALESCE(nama_kantor, CONCAT('CABANG ', kode_kantor)) AS name
                 FROM kode_kantor
                 WHERE kode_kantor BETWEEN '001' AND '028'
                 ORDER BY CAST(kode_kantor AS UNSIGNED)"
            )->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, 'Metadata proyeksi berhasil dimuat.', [
                'branches' => $branches,
                'selected_branch' => $branch,
                'latest_actual_date' => $latestDate,
                'default_closing_date' => $defaultClosingDate,
                'default_projection_date' => $latestDate === null ? null : $this->parseDate($latestDate)?->modify('last day of this month')->format('Y-m-d'),
            ]);
        } catch (Throwable $error) {
            error_log('[Proyeksi RR] metadata load failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat daftar tanggal dan cabang.');
        }
    }

    public function getSnapshot(array $input): void
    {
        $closingDate = $this->validDate($input['closing_date'] ?? null, 'Closing date');
        $actualDate = $this->validDate($input['harian_date'] ?? null, 'Actual date');
        if (!$this->isMonthEnd($closingDate->format('Y-m-d'))) sendResponse(422, 'Closing date harus tanggal akhir bulan.');
        $previousMonthEnd = $actualDate->modify('first day of this month')->modify('-1 day');
        if ($closingDate->format('Y-m-d') !== $previousMonthEnd->format('Y-m-d')) {
            sendResponse(422, 'Closing date harus akhir bulan sebelum tanggal actual.');
        }

        $nominalField = strtolower(trim((string)($input['nominal_field'] ?? 'saldo_bank')));
        if (!in_array($nominalField, ['saldo_bank', 'baki_debet'], true)) {
            sendResponse(422, 'Basis nominal harus Saldo Bank atau Baki Debet.');
        }

        $branch = $this->normalizeBranch((string)($input['kode_cabang'] ?? 'ALL'));

        try {
            $summary = $this->aggregateSnapshots($closingDate->format('Y-m-d'), $actualDate->format('Y-m-d'), $nominalField, $branch);
            $closing = $summary['closing'];
            $actual = $summary['actual'];
            if ((int)$closing['row_count'] === 0 || (int)$actual['row_count'] === 0) {
                sendResponse(422, 'Snapshot closing atau actual tidak tersedia untuk cabang dan tanggal yang dipilih.');
            }

            $closingKyd = $closing['l'] + $closing['dp'] + $closing['kl'] + $closing['d'] + $closing['m'];
            $actualKyd = $actual['l'] + $actual['dp'] + $actual['kl'] + $actual['d'] + $actual['m'];

            sendResponse(200, 'Snapshot berhasil dimuat.', [
                'closing_date' => $closingDate->format('Y-m-d'),
                'harian_date' => $actualDate->format('Y-m-d'),
                'projection_date' => $closingDate->modify('first day of next month')->modify('last day of this month')->format('Y-m-d'),
                'kode_cabang' => $branch,
                'nominal_field' => $nominalField,
                'closing' => $closing,
                'actual' => $actual,
                'actual_movements' => ['growth_net' => $actualKyd - $closingKyd],
            ]);
        } catch (Throwable $error) {
            error_log('[Proyeksi RR] closing/actual snapshot query failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat snapshot dan pergerakan actual.');
        }
    }

    public function getMovementSummary(array $input): void
    {
        $closingDate = $this->validDate($input['closing_date'] ?? null, 'Closing date');
        $actualDate = $this->validDate($input['harian_date'] ?? null, 'Actual date');
        if (!$this->isMonthEnd($closingDate->format('Y-m-d'))) sendResponse(422, 'Closing date harus tanggal akhir bulan.');
        $previousMonthEnd = $actualDate->modify('first day of this month')->modify('-1 day');
        if ($closingDate->format('Y-m-d') !== $previousMonthEnd->format('Y-m-d')) {
            sendResponse(422, 'Closing date harus akhir bulan sebelum tanggal actual.');
        }

        $nominalField = strtolower(trim((string)($input['nominal_field'] ?? 'saldo_bank')));
        if (!in_array($nominalField, ['saldo_bank', 'baki_debet'], true)) {
            sendResponse(422, 'Basis nominal harus Saldo Bank atau Baki Debet.');
        }
        $branch = $this->normalizeBranch((string)($input['kode_cabang'] ?? 'ALL'));

        try {
            sendResponse(200, 'Ringkasan pergerakan actual berhasil dimuat.', $this->aggregateMovements(
                $closingDate->format('Y-m-d'), $actualDate->format('Y-m-d'), $nominalField, $branch
            ));
        } catch (Throwable $error) {
            error_log('[Proyeksi RR] movement summary query failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat ringkasan pergerakan actual.');
        }
    }

    public function getMovementDetail(array $input): void
    {
        $closingDate = $this->validDate($input['closing_date'] ?? null, 'Closing date');
        $actualDate = $this->validDate($input['harian_date'] ?? null, 'Actual date');
        if (!$this->isMonthEnd($closingDate->format('Y-m-d'))) sendResponse(422, 'Closing date harus tanggal akhir bulan.');
        $previousMonthEnd = $actualDate->modify('first day of this month')->modify('-1 day');
        if ($closingDate->format('Y-m-d') !== $previousMonthEnd->format('Y-m-d')) {
            sendResponse(422, 'Closing date harus akhir bulan sebelum tanggal actual.');
        }

        $movement = strtolower(trim((string)($input['movement'] ?? '')));
        $movementRules = [
            'flow_par' => "c.kolek_closing IN ('L','1','DP','DPK','2') AND c.kolek_actual IN ('KL','D','M') AND c.saldo_actual > 0",
            'recovery_npl' => "c.kolek_closing IN ('KL','D','M') AND (
                c.kolek_actual IS NULL OR c.kolek_actual IN ('L','1','DP','DPK','2') OR
                (c.kolek_actual IN ('KL','D','M') AND c.saldo_closing > c.saldo_actual)
            )",
            'btc' => "c.dpd_closing > 0 AND c.kolek_actual IN ('L','1') AND c.dpd_actual = 0 AND c.saldo_actual > 0",
            'migration_d0' => "c.kolek_closing IN ('L','1') AND c.dpd_closing = 0
                AND c.kolek_actual IN ('L','1','DP','DPK','2') AND c.dpd_actual > 0 AND c.saldo_actual > 0",
            'growth' => "(c.kolek_closing IN ('L','1','DP','DPK','2','KL','D','M') OR
                c.kolek_actual IN ('L','1','DP','DPK','2','KL','D','M')) AND c.saldo_actual <> c.saldo_closing",
        ];
        if (!isset($movementRules[$movement])) sendResponse(422, 'Jenis pergerakan tidak dikenal.');

        $nominalField = strtolower(trim((string)($input['nominal_field'] ?? 'saldo_bank')));
        if (!in_array($nominalField, ['saldo_bank', 'baki_debet'], true)) {
            sendResponse(422, 'Basis nominal harus Saldo Bank atau Baki Debet.');
        }
        $branch = $this->normalizeBranch((string)($input['kode_cabang'] ?? 'ALL'));
        $page = max(1, (int)($input['page'] ?? 1));
        $pageSize = min(100, max(10, (int)($input['page_size'] ?? 25)));
        $offset = ($page - 1) * $pageSize;
        $search = trim((string)($input['search'] ?? ''));

        try {
            $closingFilter = "AND kode_cabang BETWEEN '001' AND '028'";
            $actualFilter = "AND kode_cabang BETWEEN '001' AND '028'";
            $params = [
                ':closing_date' => $closingDate->format('Y-m-d'),
                ':actual_date' => $actualDate->format('Y-m-d'),
            ];
            if ($branch !== 'ALL') {
                $closingFilter .= ' AND kode_cabang = :closing_branch';
                $actualFilter .= ' AND kode_cabang = :actual_branch';
                $params[':closing_branch'] = $branch;
                $params[':actual_branch'] = $branch;
            }

            $baseSql = "WITH closing_rows AS (
                    SELECT no_rekening, kode_cabang, nama_nasabah,
                           UPPER(TRIM(CAST(kolektibilitas AS CHAR))) AS kolek,
                           COALESCE(hari_menunggak,0) AS dpd, COALESCE({$nominalField},0) AS saldo
                    FROM nominatif
                    WHERE created = :closing_date {$closingFilter}
                ), actual_rows AS (
                    SELECT no_rekening, kode_cabang, nama_nasabah,
                           UPPER(TRIM(CAST(kolektibilitas AS CHAR))) AS kolek,
                           COALESCE(hari_menunggak,0) AS dpd, COALESCE({$nominalField},0) AS saldo,
                           COALESCE(tunggakan_pokok,0) AS tunggakan_pokok,
                           COALESCE(tunggakan_bunga,0) AS tunggakan_bunga
                    FROM nominatif
                    WHERE created = :actual_date {$actualFilter}
                ), compared AS (
                    SELECT COALESCE(a.no_rekening,c.no_rekening) AS no_rekening,
                           COALESCE(a.kode_cabang,c.kode_cabang) AS kode_cabang,
                           COALESCE(a.nama_nasabah,c.nama_nasabah) AS nama_nasabah,
                           c.kolek AS kolek_closing, a.kolek AS kolek_actual,
                           COALESCE(c.dpd,0) AS dpd_closing, COALESCE(a.dpd,0) AS dpd_actual,
                           COALESCE(c.saldo,0) AS saldo_closing, COALESCE(a.saldo,0) AS saldo_actual,
                           COALESCE(a.tunggakan_pokok,0) AS tunggakan_pokok,
                           COALESCE(a.tunggakan_bunga,0) AS tunggakan_bunga
                    FROM closing_rows c
                    LEFT JOIN actual_rows a ON a.no_rekening = c.no_rekening AND a.kode_cabang = c.kode_cabang
                    UNION ALL
                    SELECT a.no_rekening, a.kode_cabang, a.nama_nasabah,
                           NULL AS kolek_closing, a.kolek AS kolek_actual,
                           0 AS dpd_closing, COALESCE(a.dpd,0) AS dpd_actual,
                           0 AS saldo_closing, COALESCE(a.saldo,0) AS saldo_actual,
                           COALESCE(a.tunggakan_pokok,0) AS tunggakan_pokok,
                           COALESCE(a.tunggakan_bunga,0) AS tunggakan_bunga
                    FROM actual_rows a
                    LEFT JOIN closing_rows c ON c.no_rekening = a.no_rekening AND c.kode_cabang = a.kode_cabang
                    WHERE c.no_rekening IS NULL
                )";
            $movementAmount = match ($movement) {
                'recovery_npl' => "CASE
                    WHEN c.kolek_actual IS NULL THEN c.saldo_closing
                    WHEN c.kolek_actual IN ('L','1','DP','DPK','2') THEN c.saldo_actual
                    WHEN c.kolek_actual IN ('KL','D','M') AND c.saldo_closing > c.saldo_actual THEN c.saldo_closing-c.saldo_actual
                    ELSE 0 END",
                'growth' => '(c.saldo_actual-c.saldo_closing)',
                default => 'c.saldo_actual',
            };
            $whereSql = $movementRules[$movement];
            if ($movement === 'growth') $whereSql .= ' AND c.saldo_actual <> c.saldo_closing';
            if ($search !== '') {
                $whereSql .= ' AND (c.no_rekening LIKE :search_account OR c.nama_nasabah LIKE :search_name)';
                $params[':search_account'] = '%' . $search . '%';
                $params[':search_name'] = '%' . $search . '%';
            }

            $detailSql = $baseSql . " SELECT c.*, {$movementAmount} AS movement_amount,
                COUNT(*) OVER() AS __total_rows,
                COALESCE(SUM({$movementAmount}) OVER(),0) AS __total_amount
                FROM compared c WHERE {$whereSql}
                ORDER BY ABS({$movementAmount}) DESC, c.nama_nasabah ASC
                LIMIT {$pageSize} OFFSET {$offset}";
            $detailStmt = $this->pdo->prepare($detailSql);
            $detailStmt->execute($params);
            $rows = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
            $totalRows = (int)($rows[0]['__total_rows'] ?? 0);
            $totalAmount = (float)($rows[0]['__total_amount'] ?? 0);
            foreach ($rows as &$row) {
                unset($row['__total_rows'], $row['__total_amount']);
                foreach (['saldo_closing','saldo_actual','movement_amount','tunggakan_pokok','tunggakan_bunga'] as $key) {
                    $row[$key] = (float)$row[$key];
                }
                foreach (['dpd_closing','dpd_actual'] as $key) $row[$key] = (int)$row[$key];
            }
            unset($row);

            sendResponse(200, 'Detail pergerakan actual berhasil dimuat.', [
                'movement' => $movement,
                'nominal_field' => $nominalField,
                'total_rows' => $totalRows,
                'total_amount' => $totalAmount,
                'page' => $page,
                'page_size' => $pageSize,
                'total_pages' => (int)ceil($totalRows / $pageSize),
                'rows' => $rows,
            ]);
        } catch (Throwable $error) {
            error_log('[Proyeksi RR] movement detail query failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat detail debitur untuk pergerakan actual.');
        }
    }

    private function aggregateSnapshots(string $closingDate, string $actualDate, string $nominalField, string $branch): array
    {
        $closingScope = "AND kode_cabang BETWEEN '001' AND '028'";
        $actualScope = "AND kode_cabang BETWEEN '001' AND '028'";
        $snapshotParams = [
            ':closing_start' => $closingDate,
            ':actual_start' => $actualDate,
        ];
        if ($branch !== 'ALL') {
            $closingScope .= ' AND kode_cabang = :closing_branch';
            $actualScope .= ' AND kode_cabang = :actual_branch';
            $snapshotParams[':closing_branch'] = $branch;
            $snapshotParams[':actual_branch'] = $branch;
        }

        $snapshotSql = "
            SELECT 'closing' AS snapshot, COUNT(*) AS row_count,
                   SUM(CASE WHEN COALESCE({$nominalField},0) < 0 THEN 1 ELSE 0 END) AS negative_rows,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('L','1') THEN COALESCE({$nominalField},0) ELSE 0 END) AS l,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('DP','DPK','2') THEN COALESCE({$nominalField},0) ELSE 0 END) AS dp,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'KL' THEN COALESCE({$nominalField},0) ELSE 0 END) AS kl,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'D' THEN COALESCE({$nominalField},0) ELSE 0 END) AS d,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'M' THEN COALESCE({$nominalField},0) ELSE 0 END) AS m,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('L','1') AND COALESCE(hari_menunggak,0) = 0 THEN COALESCE({$nominalField},0) ELSE 0 END) AS dpd0
            FROM nominatif WHERE created = :closing_start {$closingScope}
            UNION ALL
            SELECT 'actual' AS snapshot, COUNT(*) AS row_count,
                   SUM(CASE WHEN COALESCE({$nominalField},0) < 0 THEN 1 ELSE 0 END) AS negative_rows,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('L','1') THEN COALESCE({$nominalField},0) ELSE 0 END) AS l,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('DP','DPK','2') THEN COALESCE({$nominalField},0) ELSE 0 END) AS dp,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'KL' THEN COALESCE({$nominalField},0) ELSE 0 END) AS kl,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'D' THEN COALESCE({$nominalField},0) ELSE 0 END) AS d,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) = 'M' THEN COALESCE({$nominalField},0) ELSE 0 END) AS m,
                   SUM(CASE WHEN UPPER(TRIM(CAST(kolektibilitas AS CHAR))) IN ('L','1') AND COALESCE(hari_menunggak,0) = 0 THEN COALESCE({$nominalField},0) ELSE 0 END) AS dpd0
            FROM nominatif WHERE created = :actual_start {$actualScope}";
        $snapshotStmt = $this->pdo->prepare($snapshotSql);
        $snapshotStmt->execute($snapshotParams);
        $snapshots = ['closing' => [], 'actual' => []];
        foreach ($snapshotStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $type = $row['snapshot'] === 'closing' ? 'closing' : 'actual';
            $snapshots[$type] = [
                'row_count' => (int)($row['row_count'] ?? 0),
                'negative_rows' => (int)($row['negative_rows'] ?? 0),
                'l' => (float)($row['l'] ?? 0), 'dp' => (float)($row['dp'] ?? 0),
                'kl' => (float)($row['kl'] ?? 0), 'd' => (float)($row['d'] ?? 0),
                'm' => (float)($row['m'] ?? 0), 'dpd0' => (float)($row['dpd0'] ?? 0),
            ];
        }

        return $snapshots;
    }

    private function aggregateMovements(string $closingDate, string $actualDate, string $nominalField, string $branch): array
    {
        $movementScope = "AND kode_cabang BETWEEN '001' AND '028'";
        $branchParams = [];
        if ($branch !== 'ALL') {
            $movementScope .= ' AND kode_cabang = :branch';
            $branchParams[':branch'] = $branch;
        }
        $selectRows = function (string $date) use ($movementScope, $branchParams, $nominalField): PDOStatement {
            $statement = $this->pdo->prepare("SELECT no_rekening,kode_cabang,kolektibilitas,hari_menunggak,COALESCE({$nominalField},0) AS saldo
                FROM nominatif WHERE created = :snapshot_date {$movementScope}");
            $statement->execute([':snapshot_date' => $date] + $branchParams);
            return $statement;
        };

        // Scan each indexed daily snapshot once. Joining each closing row to the 15M-row table
        // caused tens of thousands of random index lookups for the consolidated view.
        $actualRows = [];
        $actualStmt = $selectRows($actualDate);
        while (($row = $actualStmt->fetch(PDO::FETCH_NUM)) !== false) {
            if ($row[0] === null) continue;
            $key = (string)$row[1] . "\0" . (string)$row[0];
            $actualRows[$key] = [
                strtoupper(trim((string)($row[2] ?? ''))),
                (int)($row[3] ?? 0),
                (float)($row[4] ?? 0),
            ];
        }
        $actualStmt->closeCursor();

        $movements = [
            'flow_par' => 0.0, 'flow_par_noa' => 0,
            'recovery_npl' => 0.0, 'recovery_npl_noa' => 0,
            'btc' => 0.0, 'btc_noa' => 0,
            'migration_d0' => 0.0, 'migration_d0_noa' => 0,
        ];
        $goodCollections = ['L', '1', 'DP', 'DPK', '2'];
        $closingStmt = $selectRows($closingDate);
        while (($row = $closingStmt->fetch(PDO::FETCH_NUM)) !== false) {
            $closingCollection = strtoupper(trim((string)($row[2] ?? '')));
            $closingDpd = (int)($row[3] ?? 0);
            $closingBalance = (float)($row[4] ?? 0);
            $key = $row[0] === null ? null : (string)$row[1] . "\0" . (string)$row[0];
            $actual = $key === null ? null : ($actualRows[$key] ?? null);
            $actualCollection = $actual[0] ?? '';
            $actualDpd = $actual[1] ?? 0;
            $actualBalance = $actual[2] ?? 0.0;
            $closingIsNpl = in_array($closingCollection, ['KL', 'D', 'M'], true);
            $actualIsNpl = in_array($actualCollection, ['KL', 'D', 'M'], true);
            $actualIsGood = in_array($actualCollection, $goodCollections, true);

            if (in_array($closingCollection, $goodCollections, true)
                && $actualIsNpl && $actualBalance > 0) {
                $movements['flow_par'] += $actualBalance;
                $movements['flow_par_noa']++;
            }
            if ($closingIsNpl) {
                if ($actual === null) {
                    $movements['recovery_npl'] += $closingBalance;
                    $movements['recovery_npl_noa']++;
                } elseif ($actualIsGood) {
                    $movements['recovery_npl'] += $actualBalance;
                    $movements['recovery_npl_noa']++;
                } elseif ($actualIsNpl && $closingBalance > $actualBalance) {
                    $movements['recovery_npl'] += $closingBalance - $actualBalance;
                    $movements['recovery_npl_noa']++;
                }
            }
            if ($closingDpd > 0 && in_array($actualCollection, ['L', '1'], true) && $actualDpd === 0 && $actualBalance > 0) {
                $movements['btc'] += $actualBalance;
                $movements['btc_noa']++;
            }
            if (in_array($closingCollection, ['L', '1'], true) && $closingDpd === 0
                && $actualIsGood && $actualDpd > 0 && $actualBalance > 0) {
                $movements['migration_d0'] += $actualBalance;
                $movements['migration_d0_noa']++;
            }
        }
        $closingStmt->closeCursor();

        return $movements;
    }

    private function validDate($value, string $label): ?DateTimeImmutable
    {
        $date = $this->parseDate(is_scalar($value) ? (string)$value : '');
        if ($date === null) {
            sendResponse(422, $label . ' tidak valid.');
        }
        return $date;
    }

    private function normalizeBranch(string $value): string
    {
        $branch = strtoupper(trim($value));
        if ($branch === '' || $branch === 'ALL') return 'ALL';
        $branch = str_pad($branch, 3, '0', STR_PAD_LEFT);
        if (!preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', $branch)) {
            sendResponse(422, 'Cabang tidak valid.');
        }
        return $branch;
    }

    private function parseDate(string $value): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $date : null;
    }

    private function isMonthEnd(string $date): bool
    {
        $parsed = $this->parseDate($date);
        return $parsed !== null && $parsed->format('Y-m-d') === $parsed->modify('last day of this month')->format('Y-m-d');
    }
}
