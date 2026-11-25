<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/ProyeksiRrController.php';

final class KomitmenController
{
    private PDO $pdo;
    private string $office;
    private ?string $branchScope;
    private bool $canManageCycles;
    private ProyeksiRrController $positions;

    public function __construct(PDO $pdo, string $office, array $actor = [])
    {
        $this->pdo = $pdo;
        $this->office = str_pad(strtoupper(trim($office)), 3, '0', STR_PAD_LEFT);
        $this->canManageCycles = self::canManageCyclesFor($actor, $this->office);
        $this->branchScope = $this->office === '000' ? null : $this->normalizeBranch($this->office);
        $this->positions = new ProyeksiRrController($pdo, $this->office);
        $this->ensureStorage();
    }

    public static function isCycleManagementDivision(array $actor): bool
    {
        $allowed = ['divisi pemasaran', 'divisi penyelesaian kredit', 'divisi operasional'];
        foreach (['unit_kerja', 'job_position'] as $field) {
            $value = mb_strtolower(trim((string)($actor[$field] ?? '')));
            if (in_array($value, $allowed, true)) return true;
        }
        return false;
    }

    public static function canManageCyclesFor(array $actor, string $office): bool
    {
        return str_pad(strtoupper(trim($office)), 3, '0', STR_PAD_LEFT) === '000'
            || self::isCycleManagementDivision($actor);
    }

    private function ensureStorage(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS rr_npl_commitment_cycles (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            period_month DATE NOT NULL,
            cycle_no SMALLINT UNSIGNED NOT NULL,
            cycle_name VARCHAR(100) NOT NULL,
            target_rr DECIMAL(5,2) NOT NULL,
            target_npl DECIMAL(5,2) NOT NULL,
            status ENUM('open','closed') NOT NULL DEFAULT 'open',
            active_month DATE NULL,
            opened_by VARCHAR(50) NOT NULL,
            opened_name VARCHAR(180) NOT NULL DEFAULT '',
            opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            closed_by VARCHAR(50) NULL,
            closed_name VARCHAR(180) NULL,
            closed_at DATETIME NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_rr_commitment_cycle_no (period_month, cycle_no),
            UNIQUE KEY uq_rr_commitment_cycle_active_month (active_month),
            KEY idx_rr_commitment_cycle_period (period_month, status, id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS rr_npl_commitments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            period_month DATE NOT NULL,
            cycle_id BIGINT UNSIGNED NULL,
            branch_code CHAR(3) NOT NULL,
            stage ENUM('awal','h3','siklus') NOT NULL DEFAULT 'siklus',
            scenario_name VARCHAR(120) NOT NULL DEFAULT '',
            nominal_field ENUM('saldo_bank','baki_debet') NOT NULL DEFAULT 'saldo_bank',
            closing_date DATE NOT NULL,
            position_date DATE NOT NULL,
            projection_date DATE NOT NULL,
            inputs_json LONGTEXT NOT NULL,
            closing_json LONGTEXT NOT NULL,
            position_json LONGTEXT NOT NULL,
            projection_json LONGTEXT NOT NULL,
            status ENUM('draft','diajukan') NOT NULL DEFAULT 'draft',
            created_by VARCHAR(50) NOT NULL,
            created_name VARCHAR(180) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            submitted_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY idx_rr_commitment_period_branch (period_month, branch_code, id),
            KEY idx_rr_commitment_stage (period_month, stage, branch_code, id),
            KEY idx_rr_commitment_cycle_branch (cycle_id, branch_code, id),
            KEY idx_rr_commitment_actor (created_by, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $columns = $this->pdo->query('SHOW COLUMNS FROM rr_npl_commitments')->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'Field');
        if (!in_array('cycle_id', $columnNames, true)) {
            $this->pdo->exec('ALTER TABLE rr_npl_commitments ADD cycle_id BIGINT UNSIGNED NULL AFTER period_month, ADD KEY idx_rr_commitment_cycle_branch (cycle_id, branch_code, id)');
        }
        foreach ($columns as $column) {
            if (($column['Field'] ?? '') === 'stage' && !str_contains(strtolower((string)$column['Type']), 'siklus')) {
                $this->pdo->exec("ALTER TABLE rr_npl_commitments MODIFY stage ENUM('awal','h3','siklus') NOT NULL DEFAULT 'siklus'");
                break;
            }
        }
    }

    public function getMetadata(array $query): void
    {
        $period = $this->validPeriod((string)($query['period'] ?? date('Y-m')));
        $branch = $this->resolveBranch((string)($query['kode_cabang'] ?? ($this->branchScope ?? '')));
        $nominalField = $this->validNominal((string)($query['nominal_field'] ?? 'saldo_bank'));
        $branchStmt = $this->pdo->prepare("SELECT kode_kantor AS code,
                COALESCE(nama_kantor, CONCAT('CABANG ', kode_kantor)) AS name
            FROM kode_kantor WHERE kode_kantor BETWEEN '001' AND '028'" .
            ($this->branchScope !== null ? ' AND kode_kantor = :branch' : '') .
            " ORDER BY CAST(kode_kantor AS UNSIGNED)");
        $branchStmt->execute($this->branchScope !== null ? [':branch' => $this->branchScope] : []);
        $branches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);
        if ($branch === 'ALL' && $branches) $branch = str_pad((string)$branches[0]['code'], 3, '0', STR_PAD_LEFT);

        $projectionDate = $period->modify('last day of this month');
        $closingDate = $period->modify('first day of this month')->modify('-1 day');
        $monthStart = $period->format('Y-m-01');
        $latestActual = $this->latestSnapshotDate($monthStart, $projectionDate->format('Y-m-d'), $branch);
        $cyclesStmt = $this->pdo->prepare('SELECT * FROM rr_npl_commitment_cycles WHERE period_month = :period ORDER BY cycle_no DESC');
        $cyclesStmt->execute([':period' => $period->format('Y-m-d')]);
        $cycles = array_map(fn(array $row): array => $this->formatCycle($row), $cyclesStmt->fetchAll(PDO::FETCH_ASSOC));
        $requestedCycleId = max(0, (int)($query['cycle_id'] ?? 0));
        $cycle = null;
        foreach ($cycles as $candidate) if ($requestedCycleId > 0 && $candidate['id'] === $requestedCycleId) { $cycle = $candidate; break; }
        if (!$cycle) foreach ($cycles as $candidate) if ($candidate['status'] === 'open') { $cycle = $candidate; break; }
        if (!$cycle && $cycles) $cycle = $cycles[0];

        sendResponse(200, 'Filter komitmen berhasil dimuat.', [
            'period' => $period->format('Y-m'),
            'projection_date' => $projectionDate->format('Y-m-d'),
            'closing_date' => $closingDate->format('Y-m-d'),
            'latest_actual_date' => $latestActual,
            'branches' => $branches,
            'cycles' => $cycles,
            'selected_branch' => $branch,
            'nominal_field' => $nominalField,
            'allow_branch_selection' => $this->branchScope === null,
            'can_manage_cycles' => $this->canManageCycles,
            'cycle' => $cycle,
        ]);
    }

    public function openCycle(array $input, array $actor): void
    {
        $this->requireCycleManager();
        $period = $this->validPeriod((string)($input['period'] ?? ''));
        $name = trim((string)($input['cycle_name'] ?? ''));
        if ($name === '') sendResponse(422, 'Nama siklus komitmen wajib diisi.');
        if (mb_strlen($name) > 100) sendResponse(422, 'Nama siklus maksimal 100 karakter.');
        $targetRr = $this->validTarget($input['target_rr'] ?? null, 'Target RR');
        $targetNpl = $this->validTarget($input['target_npl'] ?? null, 'Target NPL');
        $employeeId = trim((string)($actor['employee_id'] ?? ''));
        if ($employeeId === '') sendResponse(401, 'Identitas pengguna tidak ditemukan. Silakan login kembali.');
        $employeeName = trim((string)($actor['full_name'] ?? $actor['name'] ?? ''));

        try {
            $active = $this->pdo->prepare('SELECT id FROM rr_npl_commitment_cycles WHERE active_month = :period LIMIT 1');
            $active->execute([':period' => $period->format('Y-m-d')]);
            if ($active->fetchColumn() !== false) sendResponse(409, 'Tutup siklus yang sedang terbuka sebelum membuka siklus berikutnya.');
            $number = $this->pdo->prepare('SELECT COALESCE(MAX(cycle_no), 0) + 1 FROM rr_npl_commitment_cycles WHERE period_month = :period');
            $number->execute([':period' => $period->format('Y-m-d')]);
            $cycleNo = (int)$number->fetchColumn();
            $insert = $this->pdo->prepare("INSERT INTO rr_npl_commitment_cycles
                (period_month, cycle_no, cycle_name, target_rr, target_npl, status, active_month, opened_by, opened_name)
                VALUES (:period, :cycle_no, :name, :target_rr, :target_npl, 'open', :active_month, :opened_by, :opened_name)");
            $insert->execute([
                ':period' => $period->format('Y-m-d'), ':cycle_no' => $cycleNo, ':name' => $name,
                ':target_rr' => $targetRr, ':target_npl' => $targetNpl, ':active_month' => $period->format('Y-m-d'),
                ':opened_by' => $employeeId, ':opened_name' => $employeeName,
            ]);
            $id = (int)$this->pdo->lastInsertId();
            sendResponse(200, 'Siklus komitmen berhasil dibuka.', ['cycle' => $this->cycleById($id, $period->format('Y-m-d'))]);
        } catch (PDOException $error) {
            error_log('[Komitmen] open cycle failed: ' . $error->getMessage());
            sendResponse(409, 'Siklus lain baru saja dibuka. Muat ulang rekap lalu coba lagi.');
        }
    }

    public function closeCycle(array $input, array $actor): void
    {
        $this->requireCycleManager();
        $id = max(0, (int)($input['cycle_id'] ?? 0));
        if ($id < 1) sendResponse(422, 'Siklus komitmen tidak valid.');
        $employeeId = trim((string)($actor['employee_id'] ?? ''));
        if ($employeeId === '') sendResponse(401, 'Identitas pengguna tidak ditemukan. Silakan login kembali.');
        $employeeName = trim((string)($actor['full_name'] ?? $actor['name'] ?? ''));
        $stmt = $this->pdo->prepare("UPDATE rr_npl_commitment_cycles
            SET status = 'closed', active_month = NULL, closed_by = :closed_by, closed_name = :closed_name, closed_at = NOW()
            WHERE id = :id AND status = 'open'");
        $stmt->execute([':closed_by' => $employeeId, ':closed_name' => $employeeName, ':id' => $id]);
        if ($stmt->rowCount() < 1) sendResponse(409, 'Siklus sudah ditutup atau tidak ditemukan.');
        sendResponse(200, 'Siklus komitmen berhasil ditutup.', ['cycle_id' => $id]);
    }

    public function getCyclePosition(array $query): void
    {
        $period = $this->validPeriod((string)($query['period'] ?? ''));
        $branch = $this->resolveBranch((string)($query['kode_cabang'] ?? ''));
        if ($branch === 'ALL') sendResponse(422, 'Pilih satu cabang untuk membuat komitmen.');
        $nominalField = $this->validNominal((string)($query['nominal_field'] ?? 'saldo_bank'));
        $cycle = $this->requestedCycle($period, (int)($query['cycle_id'] ?? 0));
        if (!$cycle) sendResponse(422, 'Pusat belum membuka siklus komitmen untuk periode ini.');
        $dates = $this->periodDates($period);
        $selectedDate = $this->actualDateInPeriod((string)($query['actual_date'] ?? ''), $period, $branch);
        try {
            $closing = $this->positions->snapshotForCommitment($dates['closing_date'], $nominalField, $branch);
            $positions = $this->positions->branchSnapshotsOnOrBeforeForCommitment($period->format('Y-m-01'), $selectedDate, $nominalField, $branch);
            $position = $positions[$branch] ?? null;
            if (!$position) sendResponse(422, 'Snapshot actual bulan ini belum tersedia untuk cabang tersebut.');
            $actualMovements = $this->positions->commitmentMovementDefaults(
                $dates['closing_date'], (string)$position['snapshot_date'], $nominalField, $branch, $closing, $position
            );
            sendResponse(200, 'Posisi komitmen berhasil dimuat.', [
                'period' => $period->format('Y-m'), 'branch_code' => $branch, 'nominal_field' => $nominalField,
                'closing_date' => $dates['closing_date'], 'selected_actual_date' => $selectedDate,
                'position_date' => $position['snapshot_date'], 'projection_date' => $dates['projection_date'],
                'closing' => $closing, 'position' => $position, 'actual_movements' => $actualMovements, 'cycle' => $cycle,
            ]);
        } catch (Throwable $error) {
            error_log('[Komitmen] cycle position snapshot load failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat posisi closing dan aktual.');
        }
    }

    public function saveCycle(array $input, array $actor): void
    {
        $period = $this->validPeriod((string)($input['period'] ?? ''));
        $branch = $this->resolveBranch((string)($input['kode_cabang'] ?? ''));
        if ($branch === 'ALL') sendResponse(422, 'Pilih satu cabang untuk menyimpan komitmen.');
        $nominalField = $this->validNominal((string)($input['nominal_field'] ?? 'saldo_bank'));
        $cycle = $this->requestedCycle($period, (int)($input['cycle_id'] ?? 0));
        if (!$cycle || $cycle['status'] !== 'open') sendResponse(409, 'Input komitmen sedang ditutup oleh pusat.');
        $status = strtolower(trim((string)($input['status'] ?? 'diajukan')));
        if (!in_array($status, ['draft', 'diajukan'], true)) sendResponse(422, 'Status penyimpanan tidak valid.');
        $scenarioName = trim((string)($input['scenario_name'] ?? ''));
        if ($scenarioName === '') $scenarioName = $cycle['cycle_name'];
        if (mb_strlen($scenarioName) > 120) sendResponse(422, 'Nama skenario maksimal 120 karakter.');
        $rawInputs = $input['inputs'] ?? [];
        if (!is_array($rawInputs)) sendResponse(422, 'Format asumsi komitmen tidak valid.');
        $rawInputs['target_rr'] = $cycle['target_rr'];
        $rawInputs['target_npl'] = $cycle['target_npl'];
        $values = $this->normalizeInputs($rawInputs);
        $dates = $this->periodDates($period);
        $selectedDate = $this->actualDateInPeriod((string)($input['actual_date'] ?? ''), $period, $branch);
        $employeeId = trim((string)($actor['employee_id'] ?? ''));
        if ($employeeId === '') sendResponse(401, 'Identitas pengguna tidak ditemukan. Silakan login kembali.');
        $employeeName = trim((string)($actor['full_name'] ?? $actor['name'] ?? ''));

        try {
            $closing = $this->positions->snapshotForCommitment($dates['closing_date'], $nominalField, $branch);
            $positions = $this->positions->branchSnapshotsOnOrBeforeForCommitment($period->format('Y-m-01'), $selectedDate, $nominalField, $branch);
            $position = $positions[$branch] ?? null;
            if (!$position) sendResponse(422, 'Snapshot actual bulan ini belum tersedia untuk cabang tersebut.');
            $values['actual_date_selected'] = $selectedDate;
            $projection = $this->calculateProjection($closing, $values);
            if ($status === 'diajukan' && $projection['issues']) {
                sendResponse(422, 'Perbaiki asumsi yang membuat saldo proyeksi tidak valid sebelum mengajukan.', ['issues' => $projection['issues']]);
            }
            $this->pdo->beginTransaction();
            $lock = $this->pdo->prepare("SELECT status FROM rr_npl_commitment_cycles WHERE id = :id AND period_month = :period FOR UPDATE");
            $lock->execute([':id' => $cycle['id'], ':period' => $period->format('Y-m-d')]);
            if ($lock->fetchColumn() !== 'open') {
                $this->pdo->rollBack();
                sendResponse(409, 'Siklus ditutup sebelum komitmen tersimpan. Muat ulang halaman.');
            }
            $stmt = $this->pdo->prepare("INSERT INTO rr_npl_commitments
                (period_month, cycle_id, branch_code, stage, scenario_name, nominal_field, closing_date, position_date, projection_date,
                 inputs_json, closing_json, position_json, projection_json, status, created_by, created_name, submitted_at)
                VALUES (:period, :cycle_id, :branch, 'siklus', :scenario, :nominal, :closing_date, :position_date, :projection_date,
                 :inputs, :closing, :position, :projection, :status, :created_by, :created_name, :submitted_at)");
            $stmt->execute([
                ':period' => $period->format('Y-m-d'), ':cycle_id' => $cycle['id'], ':branch' => $branch,
                ':scenario' => $scenarioName, ':nominal' => $nominalField, ':closing_date' => $dates['closing_date'],
                ':position_date' => $position['snapshot_date'], ':projection_date' => $dates['projection_date'],
                ':inputs' => $this->encodeJson($values), ':closing' => $this->encodeJson($closing),
                ':position' => $this->encodeJson($position), ':projection' => $this->encodeJson($projection),
                ':status' => $status, ':created_by' => $employeeId, ':created_name' => $employeeName,
                ':submitted_at' => $status === 'diajukan' ? date('Y-m-d H:i:s') : null,
            ]);
            $id = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();
            sendResponse(200, 'Komitmen siklus berhasil disimpan.', ['id' => $id, 'cycle_id' => $cycle['id'], 'status' => $status, 'projection' => $projection]);
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log('[Komitmen] cycle save failed: ' . $error->getMessage());
            sendResponse(500, 'Komitmen gagal disimpan. Coba lagi atau hubungi administrator.');
        }
    }

    public function getCycleList(array $query): void
    {
        $period = $this->validPeriod((string)($query['period'] ?? date('Y-m')));
        $branch = $this->resolveBranch((string)($query['kode_cabang'] ?? 'ALL'));
        $nominalField = $this->validNominal((string)($query['nominal_field'] ?? 'saldo_bank'));
        $dates = $this->periodDates($period);
        $month = $period->format('Y-m-d');
        $cyclesStmt = $this->pdo->prepare('SELECT * FROM rr_npl_commitment_cycles WHERE period_month = :period ORDER BY cycle_no DESC');
        $cyclesStmt->execute([':period' => $month]);
        $cycles = array_map(fn(array $row): array => $this->formatCycle($row), $cyclesStmt->fetchAll(PDO::FETCH_ASSOC));
        $requestedCycleId = max(0, (int)($query['cycle_id'] ?? 0));
        $cycle = null;
        foreach ($cycles as $candidate) {
            if ($requestedCycleId > 0 && $candidate['id'] === $requestedCycleId) { $cycle = $candidate; break; }
        }
        if (!$cycle) {
            foreach ($cycles as $candidate) {
                if ($candidate['status'] === 'open') { $cycle = $candidate; break; }
            }
        }
        if (!$cycle && $cycles) $cycle = $cycles[0];

        $branchesStmt = $this->pdo->prepare("SELECT kode_kantor AS code,
                COALESCE(nama_kantor, CONCAT('CABANG ', kode_kantor)) AS name
            FROM kode_kantor WHERE kode_kantor BETWEEN '001' AND '028'" .
            ($this->branchScope !== null ? ' AND kode_kantor = :branch' : '') .
            ' ORDER BY CAST(kode_kantor AS UNSIGNED)');
        $branchesStmt->execute($this->branchScope !== null ? [':branch' => $this->branchScope] : []);
        $branches = $branchesStmt->fetchAll(PDO::FETCH_ASSOC);

        $selectedDate = (string)($query['actual_date'] ?? '');
        if ($selectedDate !== '') {
            $selectedDate = $this->actualDateInPeriod($selectedDate, $period, $branch);
        } else {
            $selectedDate = $this->latestSnapshotDate($period->format('Y-m-01'), $dates['projection_date'], $branch)
                ?? min(date('Y-m-d'), $dates['projection_date']);
        }
        try {
            $closing = $this->positions->branchSnapshotsForCommitment($dates['closing_date'], $nominalField, $branch);
            $actual = $this->positions->branchSnapshotsOnOrBeforeForCommitment($period->format('Y-m-01'), $selectedDate, $nominalField, $branch);
        } catch (Throwable $error) {
            error_log('[Komitmen] cycle recap snapshot load failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat posisi closing dan actual untuk rekap.');
        }

        $latest = [];
        if ($cycle) {
            $sql = 'SELECT * FROM rr_npl_commitments WHERE period_month = :period AND cycle_id = :cycle AND nominal_field = :nominal';
            $params = [':period' => $month, ':cycle' => $cycle['id'], ':nominal' => $nominalField];
            if ($branch !== 'ALL') { $sql .= ' AND branch_code = :branch'; $params[':branch'] = $branch; }
            $stmt = $this->pdo->prepare($sql . ' ORDER BY id DESC');
            $stmt->execute($params);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $record) {
                $code = str_pad((string)$record['branch_code'], 3, '0', STR_PAD_LEFT);
                if (!isset($latest[$code])) $latest[$code] = $this->formatRecord($record);
            }
        }

        $rows = [];
        foreach ($branches as $row) {
            $code = str_pad((string)$row['code'], 3, '0', STR_PAD_LEFT);
            if ($branch !== 'ALL' && $code !== $branch) continue;
            $rows[] = [
                'branch_code' => $code, 'branch_name' => (string)$row['name'],
                'closing' => $closing[$code] ?? null, 'commitment' => $latest[$code] ?? null,
                'actual' => $actual[$code] ?? null, 'actual_date' => $actual[$code]['snapshot_date'] ?? null,
            ];
        }
        sendResponse(200, 'Rekap komitmen berhasil dimuat.', [
            'period' => $period->format('Y-m'), 'closing_date' => $dates['closing_date'],
            'projection_date' => $dates['projection_date'], 'actual_date_selected' => $selectedDate,
            'allow_branch_selection' => $this->branchScope === null, 'can_manage_cycles' => $this->canManageCycles,
            'selected_branch' => $branch, 'branches' => $branches, 'cycles' => $cycles,
            'selected_cycle' => $cycle, 'rows' => $rows,
        ]);
    }

    public function getPosition(array $query): void
    {
        $period = $this->validPeriod((string)($query['period'] ?? ''));
        $stage = strtolower(trim((string)($query['stage'] ?? 'awal')));
        if (!in_array($stage, ['awal', 'h3'], true)) sendResponse(422, 'Jenis komitmen harus awal atau H-3.');
        $branch = $this->resolveBranch((string)($query['kode_cabang'] ?? ''));
        if ($branch === 'ALL') sendResponse(422, 'Pilih satu cabang untuk membuat komitmen.');
        $nominalField = $this->validNominal((string)($query['nominal_field'] ?? 'saldo_bank'));
        $dates = $this->periodDates($period);
        $positionDate = $dates['closing_date'];

        if ($stage === 'h3') {
            $this->requireInitialCommitment($period->format('Y-m-d'), $branch, $nominalField);
            $positionDate = $this->latestSnapshotDate($period->format('Y-m-01'), $dates['h3_cutoff'], $branch);
            if ($positionDate === null) sendResponse(422, 'Snapshot actual sampai batas H-3 belum tersedia untuk cabang ini.');
        }

        try {
            $closing = $this->positions->snapshotForCommitment($dates['closing_date'], $nominalField, $branch);
            $position = $stage === 'awal'
                ? $closing
                : $this->positions->snapshotForCommitment($positionDate, $nominalField, $branch);
            sendResponse(200, 'Posisi komitmen berhasil dimuat.', [
                'period' => $period->format('Y-m'),
                'stage' => $stage,
                'branch_code' => $branch,
                'nominal_field' => $nominalField,
                'closing_date' => $dates['closing_date'],
                'position_date' => $positionDate,
                'projection_date' => $dates['projection_date'],
                'h3_cutoff' => $dates['h3_cutoff'],
                'closing' => $closing,
                'position' => $position,
            ]);
        } catch (Throwable $error) {
            error_log('[Komitmen] position snapshot load failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat posisi closing dan aktual.');
        }
    }

    public function save(array $input, array $actor): void
    {
        $period = $this->validPeriod((string)($input['period'] ?? ''));
        $stage = strtolower(trim((string)($input['stage'] ?? 'awal')));
        if (!in_array($stage, ['awal', 'h3'], true)) sendResponse(422, 'Jenis komitmen harus awal atau H-3.');
        $branch = $this->resolveBranch((string)($input['kode_cabang'] ?? ''));
        if ($branch === 'ALL') sendResponse(422, 'Pilih satu cabang untuk menyimpan komitmen.');
        $nominalField = $this->validNominal((string)($input['nominal_field'] ?? 'saldo_bank'));
        if ($stage === 'h3') $this->requireInitialCommitment($period->format('Y-m-d'), $branch, $nominalField);
        $status = strtolower(trim((string)($input['status'] ?? 'draft')));
        if (!in_array($status, ['draft', 'diajukan'], true)) sendResponse(422, 'Status penyimpanan tidak valid.');
        $scenarioName = trim((string)($input['scenario_name'] ?? ''));
        if ($scenarioName === '') $scenarioName = $stage === 'awal' ? 'Komitmen awal bulan' : 'Komitmen H-3';
        if (mb_strlen($scenarioName) > 120) sendResponse(422, 'Nama skenario maksimal 120 karakter.');
        $rawInputs = $input['inputs'] ?? [];
        if (!is_array($rawInputs)) sendResponse(422, 'Format asumsi komitmen tidak valid.');
        $values = $this->normalizeInputs($rawInputs);

        $dates = $this->periodDates($period);
        $positionDate = $dates['closing_date'];
        if ($stage === 'h3') {
            $positionDate = $this->latestSnapshotDate($period->format('Y-m-01'), $dates['h3_cutoff'], $branch);
            if ($positionDate === null) sendResponse(422, 'Snapshot actual sampai batas H-3 belum tersedia untuk cabang ini.');
        }

        try {
            $closing = $this->positions->snapshotForCommitment($dates['closing_date'], $nominalField, $branch);
            $position = $stage === 'awal'
                ? $closing
                : $this->positions->snapshotForCommitment($positionDate, $nominalField, $branch);
            $projection = $this->calculateProjection($stage === 'awal' ? $closing : $position, $values);
            if ($status === 'diajukan' && $projection['issues']) {
                sendResponse(422, 'Perbaiki asumsi yang membuat saldo proyeksi tidak valid sebelum mengajukan.', ['issues' => $projection['issues']]);
            }

            $employeeId = trim((string)($actor['employee_id'] ?? ''));
            if ($employeeId === '') sendResponse(401, 'Identitas pengguna tidak ditemukan. Silakan login kembali.');
            $employeeName = trim((string)($actor['full_name'] ?? $actor['name'] ?? ''));
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO rr_npl_commitments
                (period_month, branch_code, stage, scenario_name, nominal_field, closing_date, position_date, projection_date,
                 inputs_json, closing_json, position_json, projection_json, status, created_by, created_name, submitted_at)
                VALUES (:period, :branch, :stage, :scenario, :nominal, :closing_date, :position_date, :projection_date,
                 :inputs, :closing, :position, :projection, :status, :created_by, :created_name, :submitted_at)");
            $stmt->execute([
                ':period' => $period->format('Y-m-d'), ':branch' => $branch, ':stage' => $stage,
                ':scenario' => $scenarioName, ':nominal' => $nominalField,
                ':closing_date' => $dates['closing_date'], ':position_date' => $positionDate,
                ':projection_date' => $dates['projection_date'],
                ':inputs' => $this->encodeJson($values), ':closing' => $this->encodeJson($closing),
                ':position' => $this->encodeJson($position), ':projection' => $this->encodeJson($projection),
                ':status' => $status, ':created_by' => $employeeId, ':created_name' => $employeeName,
                ':submitted_at' => $status === 'diajukan' ? date('Y-m-d H:i:s') : null,
            ]);
            $id = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();
            sendResponse(200, $status === 'draft' ? 'Draf komitmen berhasil disimpan.' : 'Komitmen berhasil diajukan.', [
                'id' => $id,
                'status' => $status,
                'stage' => $stage,
                'projection' => $projection,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log('[Komitmen] save failed: ' . $error->getMessage());
            sendResponse(500, 'Komitmen gagal disimpan. Coba lagi atau hubungi administrator.');
        }
    }

    public function getList(array $query): void
    {
        $period = $this->validPeriod((string)($query['period'] ?? date('Y-m')));
        $branch = $this->resolveBranch((string)($query['kode_cabang'] ?? 'ALL'));
        $nominalField = $this->validNominal((string)($query['nominal_field'] ?? 'saldo_bank'));
        $dates = $this->periodDates($period);
        $branchStmt = $this->pdo->prepare("SELECT kode_kantor AS code,
                COALESCE(nama_kantor, CONCAT('CABANG ', kode_kantor)) AS name
            FROM kode_kantor WHERE kode_kantor BETWEEN '001' AND '028'" .
            ($this->branchScope !== null ? ' AND kode_kantor = :branch' : '') .
            " ORDER BY CAST(kode_kantor AS UNSIGNED)");
        $branchStmt->execute($this->branchScope !== null ? [':branch' => $this->branchScope] : []);
        $branches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);

        $commitmentSql = "SELECT * FROM rr_npl_commitments WHERE period_month = :period AND nominal_field = :nominal";
        $params = [':period' => $period->format('Y-m-d'), ':nominal' => $nominalField];
        if ($branch !== 'ALL') {
            $commitmentSql .= ' AND branch_code = :branch';
            $params[':branch'] = $branch;
        }
        $commitmentSql .= ' ORDER BY id DESC';
        $commitmentStmt = $this->pdo->prepare($commitmentSql);
        $commitmentStmt->execute($params);
        $latest = [];
        foreach ($commitmentStmt->fetchAll(PDO::FETCH_ASSOC) as $record) {
            $code = str_pad((string)$record['branch_code'], 3, '0', STR_PAD_LEFT);
            $stage = (string)$record['stage'];
            if (isset($latest[$code][$stage])) continue;
            $latest[$code][$stage] = $this->formatRecord($record);
        }

        try {
            $closing = $this->positions->branchSnapshotsForCommitment($dates['closing_date'], $nominalField, $branch);
            $actual = $this->positions->branchSnapshotsForCommitment($dates['projection_date'], $nominalField, $branch);
        } catch (Throwable $error) {
            error_log('[Komitmen] recap snapshot load failed: ' . $error->getMessage());
            sendResponse(500, 'Gagal memuat snapshot closing atau EOM untuk rekap.');
        }

        $rows = [];
        foreach ($branches as $row) {
            $code = str_pad((string)$row['code'], 3, '0', STR_PAD_LEFT);
            if ($branch !== 'ALL' && $code !== $branch) continue;
            $rows[] = [
                'branch_code' => $code,
                'branch_name' => (string)$row['name'],
                'closing' => $closing[$code] ?? null,
                'awal' => $latest[$code]['awal'] ?? null,
                'h3' => $latest[$code]['h3'] ?? null,
                'actual_eom' => $actual[$code] ?? null,
            ];
        }

        sendResponse(200, 'Rekap komitmen berhasil dimuat.', [
            'period' => $period->format('Y-m'),
            'closing_date' => $dates['closing_date'],
            'projection_date' => $dates['projection_date'],
            'h3_cutoff' => $dates['h3_cutoff'],
            'nominal_field' => $nominalField,
            'allow_branch_selection' => $this->branchScope === null,
            'selected_branch' => $branch,
            'branches' => $branches,
            'rows' => $rows,
        ]);
    }

    public function getDetail(int $id): void
    {
        if ($id < 1) sendResponse(422, 'Nomor komitmen tidak valid.');
        $sql = 'SELECT * FROM rr_npl_commitments WHERE id = :id';
        $params = [':id' => $id];
        if ($this->branchScope !== null) {
            $sql .= ' AND branch_code = :branch';
            $params[':branch'] = $this->branchScope;
        }
        $stmt = $this->pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) sendResponse(404, 'Komitmen tidak ditemukan atau bukan milik cabang Anda.');
        $formatted = $this->formatRecord($record);
        if ((int)($formatted['cycle_id'] ?? 0) > 0) {
            $formatted['cycle'] = $this->cycleById((int)$formatted['cycle_id'], (string)$formatted['period_month']);
        }
        sendResponse(200, 'Detail komitmen berhasil dimuat.', $formatted);
    }

    private function calculateProjection(array $base, array $inputs): array
    {
        $issues = [];
        if (($base['negative_rows'] ?? 0) > 0) $issues[] = 'Snapshot posisi mengandung saldo negatif.';
        $kyd = $base['l'] + $base['dp'] + $base['kl'] + $base['d'] + $base['m'];
        if ($kyd <= 0) $issues[] = 'KYD posisi harus lebih besar dari nol.';
        if ($base['dpd0'] > $base['l']) $issues[] = 'DPD 0 melebihi saldo L pada posisi awal.';

        $dpd1To30 = $base['l'] - $base['dpd0'];
        $dpd0AfterMovement = $base['dpd0'] + $inputs['growth'] + $inputs['btc'] - $inputs['migration'];
        $dpd1To30AfterMovement = $dpd1To30 - $inputs['btc'] + $inputs['migration'];
        $flowFromDp = min($inputs['flow_par'], max($base['dp'], 0));
        $flowFromL = max(0, $inputs['flow_par'] - $flowFromDp);
        $flowFromDpd1To30 = min($flowFromL, max($dpd1To30AfterMovement, 0));
        $flowFromL -= $flowFromDpd1To30;
        $flowFromDpd0 = $flowFromL;
        $dpd1To30Projection = $dpd1To30AfterMovement - $flowFromDpd1To30;
        $dpd0BeforeUpgrade = $dpd0AfterMovement - $flowFromDpd0;
        $flowParRemaining = max(0, $flowFromDpd0 - max($dpd0AfterMovement, 0));

        $recoveryRemaining = $inputs['recovery_npl'];
        $recoveryKl = min($recoveryRemaining, max($base['kl'], 0));
        $recoveryRemaining -= $recoveryKl;
        $recoveryD = min($recoveryRemaining, max($base['d'], 0));
        $recoveryRemaining -= $recoveryD;
        $recoveryM = min($recoveryRemaining, max($base['m'], 0));
        $recoveryRemaining -= $recoveryM;
        $afterRecoveryKl = max($base['kl'] - $recoveryKl, 0);
        $afterRecoveryD = max($base['d'] - $recoveryD, 0);
        $afterRecoveryM = max($base['m'] - $recoveryM, 0);
        $afterRecoveryNpl = $afterRecoveryKl + $afterRecoveryD + $afterRecoveryM;

        $upgradeRemaining = $inputs['npl_upgrade'];
        $upgradeKl = min($upgradeRemaining, $afterRecoveryKl);
        $upgradeRemaining -= $upgradeKl;
        $upgradeD = min($upgradeRemaining, $afterRecoveryD);
        $upgradeRemaining -= $upgradeD;
        $upgradeM = min($upgradeRemaining, $afterRecoveryM);
        $upgradeRemaining -= $upgradeM;
        $upgradeApplied = $inputs['npl_upgrade'] - $upgradeRemaining;

        $backflowRemaining = $inputs['npl_backflow'];
        $backflowKl = min($backflowRemaining, max(0, $afterRecoveryKl - $upgradeKl));
        $backflowRemaining -= $backflowKl;
        $backflowD = min($backflowRemaining, max(0, $afterRecoveryD - $upgradeD));
        $backflowRemaining -= $backflowD;
        $backflowM = min($backflowRemaining, max(0, $afterRecoveryM - $upgradeM));
        $backflowRemaining -= $backflowM;
        $backflowApplied = $inputs['npl_backflow'] - $backflowRemaining;

        $dpd0 = $dpd0BeforeUpgrade + $upgradeApplied;
        $kl = max($afterRecoveryKl - $upgradeKl - $backflowKl, 0) + $inputs['flow_par'];
        $d = max($afterRecoveryD - $upgradeD - $backflowD, 0);
        $m = max($afterRecoveryM - $upgradeM - $backflowM, 0);
        $dp = $base['dp'] - $flowFromDp + $backflowApplied;
        $projectionKyd = $dpd0 + $dpd1To30Projection + $dp + $kl + $d + $m;
        $npl = $kl + $d + $m;

        if ($dpd0 < -0.01) $issues[] = 'Proyeksi DPD 0 negatif; kurangi migrasi DPD 0.';
        if ($dpd1To30Projection < -0.01) $issues[] = 'Proyeksi DPD 1–30 negatif; kurangi BTC atau sesuaikan migrasi.';
        if ($flowParRemaining > 0.01) $issues[] = 'Flow PAR melebihi total saldo L dan DP yang tersedia.';
        if ($recoveryRemaining > 0.01) $issues[] = 'Recovery melebihi total saldo KL, D, dan M yang tersedia.';
        if ($upgradeRemaining > 0.01) $issues[] = 'Upgrade NPL melebihi saldo NPL yang tersedia setelah recovery.';
        if ($backflowRemaining > 0.01) $issues[] = 'Backflow melebihi saldo NPL yang tersedia.';
        if ($projectionKyd <= 0) $issues[] = 'KYD proyeksi harus lebih besar dari nol.';

        $rr = $projectionKyd > 0 ? $dpd0 / $projectionKyd : null;
        $nplRatio = $projectionKyd > 0 ? $npl / $projectionKyd : null;
        $targetRr = $inputs['target_rr'] / 100;
        $targetNpl = $inputs['target_npl'] / 100;
        return [
            'balances' => [
                'dpd0' => $dpd0, 'dpd1_30' => $dpd1To30Projection, 'l' => $dpd0 + $dpd1To30Projection,
                'dp' => $dp, 'kl' => $kl, 'd' => $d, 'm' => $m,
                'npl' => $npl, 'kyd' => $projectionKyd,
            ],
            'ratios' => ['rr' => $rr, 'npl' => $nplRatio],
            'targets' => [
                'rr' => $inputs['target_rr'], 'npl' => $inputs['target_npl'],
                'rr_met' => $rr !== null && $rr >= $targetRr,
                'npl_met' => $nplRatio !== null && $nplRatio <= $targetNpl,
                'dpd0_needed' => $rr === null ? null : max(0, $targetRr * $projectionKyd - $dpd0),
                'npl_reduction_needed' => $nplRatio === null ? null : max(0, $npl - $targetNpl * $projectionKyd),
            ],
            'applied' => [
                'recovery_npl' => $inputs['recovery_npl'] - $recoveryRemaining,
                'npl_upgrade' => $upgradeApplied,
                'npl_backflow' => $backflowApplied,
            ],
            'issues' => array_values(array_unique($issues)),
        ];
    }

    private function normalizeInputs(array $input): array
    {
        $fields = ['growth', 'btc', 'migration', 'flow_par', 'recovery_npl', 'npl_upgrade', 'npl_backflow'];
        $values = [];
        foreach ($fields as $field) {
            $value = filter_var($input[$field] ?? 0, FILTER_VALIDATE_FLOAT);
            $minimum = $field === 'growth' ? -1000000000000000 : 0;
            if ($value === false || !is_finite((float)$value) || $value < $minimum || $value > 1000000000000000) {
                sendResponse(422, 'Nominal asumsi tidak valid. Growth boleh negatif; asumsi pergerakan lainnya harus nol atau positif.');
            }
            $values[$field] = (float)$value;
        }
        foreach (['target_rr' => 74.15, 'target_npl' => 14.90] as $field => $default) {
            $value = filter_var($input[$field] ?? $default, FILTER_VALIDATE_FLOAT);
            if ($value === false || !is_finite((float)$value) || $value < 0 || $value > 100) {
                sendResponse(422, 'Target RR dan NPL harus berada di antara 0% dan 100%.');
            }
            $values[$field] = (float)$value;
        }
        $commitmentNote = trim((string)($input['commitment_note'] ?? ''));
        if (mb_strlen($commitmentNote) > 4000) sendResponse(422, 'Rencana aksi dan komitmen maksimal 4.000 karakter.');
        $values['commitment_note'] = $commitmentNote;
        return $values;
    }

    private function formatRecord(array $record): array
    {
        foreach (['inputs_json' => 'inputs', 'closing_json' => 'closing', 'position_json' => 'position', 'projection_json' => 'projection'] as $column => $key) {
            $decoded = json_decode((string)($record[$column] ?? ''), true);
            $record[$key] = is_array($decoded) ? $decoded : [];
            unset($record[$column]);
        }
        $record['id'] = (int)$record['id'];
        $record['branch_code'] = str_pad((string)$record['branch_code'], 3, '0', STR_PAD_LEFT);
        $record['cycle_id'] = (int)($record['cycle_id'] ?? 0);
        if ($record['cycle_id'] > 0) $record['stage'] = 'siklus';
        return $record;
    }

    private function requireCycleManager(): void
    {
        if (!$this->canManageCycles) sendResponse(403, 'Akun ini tidak berhak membuka atau menutup siklus komitmen.');
    }

    private function validTarget($value, string $label): float
    {
        $number = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($number === false || !is_finite((float)$number) || $number < 0 || $number > 100) {
            sendResponse(422, $label . ' harus berada di antara 0% dan 100%.');
        }
        return (float)$number;
    }

    private function formatCycle(array $cycle): array
    {
        $cycle['id'] = (int)$cycle['id'];
        $cycle['cycle_no'] = (int)$cycle['cycle_no'];
        $cycle['target_rr'] = (float)$cycle['target_rr'];
        $cycle['target_npl'] = (float)$cycle['target_npl'];
        return $cycle;
    }

    private function cycleById(int $id, string $period): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rr_npl_commitment_cycles WHERE id = :id AND period_month = :period LIMIT 1');
        $stmt->execute([':id' => $id, ':period' => $period]);
        $cycle = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cycle ? $this->formatCycle($cycle) : null;
    }

    private function cycleForPeriod(string $period, bool $openOnly): ?array
    {
        $sql = 'SELECT * FROM rr_npl_commitment_cycles WHERE period_month = :period';
        if ($openOnly) $sql .= " AND status = 'open'";
        $stmt = $this->pdo->prepare($sql . ' ORDER BY cycle_no DESC LIMIT 1');
        $stmt->execute([':period' => $period]);
        $cycle = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cycle ? $this->formatCycle($cycle) : null;
    }

    private function requestedCycle(DateTimeImmutable $period, int $cycleId): ?array
    {
        $month = $period->format('Y-m-d');
        if ($cycleId > 0) return $this->cycleById($cycleId, $month);
        return $this->cycleForPeriod($month, true) ?? $this->cycleForPeriod($month, false);
    }

    private function actualDateInPeriod(string $requested, DateTimeImmutable $period, string $branch): string
    {
        $dates = $this->periodDates($period);
        $start = $period->format('Y-m-01');
        if (trim($requested) === '') {
            return $this->latestSnapshotDate($start, $dates['projection_date'], $branch)
                ?? min(date('Y-m-d'), $dates['projection_date']);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $requested)) sendResponse(422, 'Tanggal actual tidak valid.');
        $selected = DateTimeImmutable::createFromFormat('!Y-m-d', $requested);
        $errors = DateTimeImmutable::getLastErrors();
        if (!$selected || ($errors && ($errors['warning_count'] || $errors['error_count'])) || $selected->format('Y-m-d') !== $requested) {
            sendResponse(422, 'Tanggal actual tidak valid.');
        }
        if ($selected->format('Y-m') !== $period->format('Y-m')) sendResponse(422, 'Tanggal actual harus berada di bulan yang dipilih.');
        return $requested;
    }

    private function requireInitialCommitment(string $period, string $branch, string $nominalField): void
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM rr_npl_commitments
            WHERE period_month = :period AND branch_code = :branch AND nominal_field = :nominal AND stage = 'awal' LIMIT 1");
        $stmt->execute([':period' => $period, ':branch' => $branch, ':nominal' => $nominalField]);
        if ($stmt->fetchColumn() === false) sendResponse(422, 'Buat dan simpan komitmen awal untuk cabang dan periode ini terlebih dahulu.');
    }

    private function latestSnapshotDate(string $start, string $cutoff, string $branch): ?string
    {
        $end = (new DateTimeImmutable($cutoff))->modify('+1 day')->format('Y-m-d');
        $sql = "SELECT MAX(DATE(created)) AS snapshot_date FROM nominatif
            WHERE created >= :start_date AND created < :end_date AND kode_cabang BETWEEN '001' AND '028'";
        $params = [':start_date' => $start, ':end_date' => $end];
        if ($branch !== 'ALL') { $sql .= ' AND kode_cabang = :branch'; $params[':branch'] = $branch; }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $date = $stmt->fetchColumn();
        return $date === false || $date === null ? null : (string)$date;
    }

    private function validPeriod(string $value): DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) sendResponse(422, 'Periode komitmen tidak valid.');
        return new DateTimeImmutable($value . '-01');
    }

    private function periodDates(DateTimeImmutable $period): array
    {
        $projection = $period->modify('last day of this month');
        return [
            'closing_date' => $period->modify('first day of this month')->modify('-1 day')->format('Y-m-d'),
            'projection_date' => $projection->format('Y-m-d'),
            'h3_cutoff' => $projection->modify('-3 days')->format('Y-m-d'),
        ];
    }

    private function validNominal(string $value): string
    {
        $value = strtolower(trim($value));
        if (!in_array($value, ['saldo_bank', 'baki_debet'], true)) sendResponse(422, 'Basis nominal harus Saldo Bank atau Baki Debet.');
        return $value;
    }

    private function resolveBranch(string $requested): string
    {
        if ($this->branchScope !== null) return $this->branchScope;
        $requested = strtoupper(trim($requested));
        if ($requested === '' || $requested === 'ALL') return 'ALL';
        return $this->normalizeBranch($requested);
    }

    private function normalizeBranch(string $value): string
    {
        $branch = str_pad(strtoupper(trim($value)), 3, '0', STR_PAD_LEFT);
        if (!preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', $branch)) sendResponse(422, 'Cabang tidak valid.');
        return $branch;
    }

    private function encodeJson(array $value): string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        if (!is_string($encoded)) sendResponse(500, 'Data komitmen gagal disiapkan untuk disimpan.');
        return $encoded;
    }
}
