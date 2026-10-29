<?php
declare(strict_types=1);

require_once __DIR__ . '/response.php';

/**
 * RBAC MONBIS.
 *
 * Baseline akses dapat disimpan per divisi atau jabatan, sedangkan
 * rbac_user_permission menjadi override untuk employee_id tertentu.
 */

function rbacFeatureCatalog(): array
{
    static $catalog = null;
    if ($catalog !== null) {
        return $catalog;
    }

    $known = [
        ['dashboard', 'Dashboard', 'Utama'],
        ['tv', 'TV', 'Lainnya'],
        ['realisasi_kredit_growth', 'Realisasi Kredit', 'Pemasaran'],
        ['realisasi_ao', 'Realisasi Kredit AO', 'Pemasaran'],
        ['realisasi_rbb', 'Produksi vs RBB', 'Pemasaran'],
        ['migrasi_bucket_sc', 'Migrasi Soft Collection', 'Pemasaran'],
        ['recom_pipelane', 'Rekomendasi Pipeline AO Kredit', 'Pemasaran'],
        ['jatuh_tempo', 'Jatuh Tempo Kredit', 'Pemasaran'],
        ['search_debitur', 'Search Debitur Kredit', 'Monitoring'],
        ['mob', 'MOB 6 Bulan', 'Monitoring'],
        ['otp_baru', 'Ontime Payment (OTP)', 'Monitoring'],
        ['rekap_rr', 'Repayment Rate (RR)', 'Monitoring'],
        ['potensi_npl', 'Potensi NPL', 'Monitoring'],
        ['flow_par', 'Flow PAR', 'Monitoring'],
        ['otp_bucket_fe', 'OTP Bucket FE', 'Monitoring'],
        ['npl', 'Report NPL', 'Collection'],
        ['migrasi_kolek', 'Migrasi Kolek', 'Collection'],
        ['actual_kredit', 'Report Actual Kredit', 'Collection'],
        ['migrasi_bucket', 'Migrasi Bucket', 'Collection'],
        ['recovery_npl', 'Recovery NPL', 'Collection'],
        ['npl_25_besar', '25 NPL Besar', 'Collection'],
        ['recovery_ph', 'Hapus Buku', 'Collection'],
        ['maping_ao_remedial', 'Mapping AO Remedial', 'Collection'],
        ['lapkeu_kantor', 'Laporan Keuangan', 'Laporan'],
        ['lap_neraca', 'Lap Neraca', 'Laporan'],
        ['lap_laba_rugi', 'Lap Laba Rugi', 'Laporan'],
        ['rekap_lapkeu', 'Rekap Lapkeu', 'Laporan'],
        ['rbb_vs_realisasi', 'RBB vs Realisasi', 'Laporan'],
        ['ikhtisar', 'Ikhtisar', 'Laporan'],
        ['paparan_rbb_realisasi', 'Paparan RBB Direksi', 'Laporan'],
        ['aging_kredit', 'Rekap Aging Kredit', 'Laporan'],
        ['layanan_digital', 'Dashboard Layanan Digital', 'Layanan Digital'],
        ['va', 'Virtual Account (VA)', 'Layanan Digital'],
        ['branchless', 'Branchless', 'Layanan Digital'],
        ['qris_merchant', 'QRIS Merchant', 'Layanan Digital'],
        ['report_npl', 'Report NPL Dev', 'Dev Report'],
        ['report_recovery_npl', 'Report Recovery NPL', 'Dev Report'],
        ['report_mutasi_kredit', 'Report Mutasi Kredit', 'Dev Report'],
        ['report_potensi_npl', 'Report Potensi NPL', 'Dev Report'],
        ['report_flowpar', 'Report Flow PAR', 'Dev Report'],
        ['rbb_produksi_kredit', 'Report Produksi vs RBB', 'Dev Report'],
        ['report_realisasi_ao', 'Report Realisasi AO', 'Dev Report'],
        ['report_otp', 'Report OTP', 'Dev Report'],
        ['pipelane_monitoring_kredit', 'Monitoring Pipeline Kredit', 'Dev Report'],
        ['prospek', 'Pipeline Prospek', 'Dev Report'],
        ['gambaran_kredit_cabang', 'Gambaran Kredit Cabang', 'Dev Report'],
        ['input_rbb', 'Proyeksi RBB', 'Input RBB'],
        ['input_rbb_aba', 'Input RBB ABA', 'Input RBB'],
        ['input_rbb_detail', 'Input RBB Detail', 'Input RBB'],
        ['setting_kpi_jabatan', 'Setting KPI Jabatan', 'KPI Bisnis'],
        ['hitung_kpi_ao', 'Nilai KPI AO', 'KPI Bisnis'],
        ['generate_kpi_ao', 'Generate KPI AO', 'KPI Bisnis'],
        ['rekap_kpi_ao', 'Rekap KPI AO', 'KPI Bisnis'],
        ['raport_cabang', 'Raport Cabang', 'KPI Bisnis'],
        ['event_theme_admin', 'Setting Event', 'Admin'],
        ['setting_rbac', 'Matrix Akses Menu', 'Admin'],
        ['tv_cabang', 'TV Cabang', 'Lainnya'],
    ];

    $catalog = [];
    $seen = [];
    $order = 10;
    foreach ($known as $item) {
        [$slug, $label, $module] = $item;
        $catalog[] = [
            'feature_key' => 'page:' . $slug,
            'page_slug' => $slug,
            'feature_label' => $label,
            'module_name' => $module,
            'sort_order' => $order,
        ];
        $seen[$slug] = true;
        $order += 10;
    }

    // Tambahkan page root lain agar menu internal/fitur baru tetap dapat
    // dikelola dari matrix tanpa perlu mengubah kode RBAC lagi.
    $pageDir = dirname(__DIR__, 2) . '/pages';
    $files = glob($pageDir . '/*.php') ?: [];
    sort($files, SORT_STRING);
    foreach ($files as $file) {
        $slug = pathinfo($file, PATHINFO_FILENAME);
        if (isset($seen[$slug])) {
            continue;
        }
        if (preg_match('/(?:^|\s)(copy|lama|ori|sebelum|oke|demo|play|zzzz)(?:\s|$)|_(?:copy|lama|ori|impl)$/i', $slug)) {
            continue;
        }
        if (preg_match('/^(login|tv|home)(?:_|$)/i', $slug)) {
            continue;
        }
        $label = ucwords(str_replace('_', ' ', $slug));
        $catalog[] = [
            'feature_key' => 'page:' . $slug,
            'page_slug' => $slug,
            'feature_label' => $label,
            'module_name' => 'Lainnya',
            'sort_order' => $order,
        ];
        $seen[$slug] = true;
        $order += 10;
    }

    return $catalog;
}

function rbacEnsureSchema(PDO $pdo): void
{
    static $ready = [];
    $connectionKey = spl_object_hash($pdo);
    if (isset($ready[$connectionKey])) {
        return;
    }

    /*
     * Halaman biasa hanya perlu memastikan tabel RBAC sudah tersedia.
     * DDL, SHOW INDEX, dan sinkronisasi katalog dijalankan hanya saat
     * instalasi awal atau ketika tabel belum lengkap.
     */
    $requiredTables = [
        'rbac_menu',
        'rbac_jabatan_permission',
        'rbac_user_permission',
    ];
    $tableList = implode(', ', array_map(
        static fn(string $table): string => $pdo->quote($table),
        $requiredTables
    ));
    $existingTables = $pdo->query(
        "SELECT table_name
         FROM information_schema.tables
         WHERE table_schema = DATABASE()
           AND table_name IN ({$tableList})"
    )->fetchAll(PDO::FETCH_COLUMN);
    if (count(array_intersect($requiredTables, $existingTables)) === count($requiredTables)) {
        $catalogKeys = array_column(rbacFeatureCatalog(), 'feature_key');
        $existingFeatureKeys = $pdo->query("SELECT feature_key FROM rbac_menu")->fetchAll(PDO::FETCH_COLUMN);
        $missingFeatureKeys = array_diff($catalogKeys, $existingFeatureKeys);
        if (!$missingFeatureKeys) {
            $ready[$connectionKey] = true;
            return;
        }
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS rbac_menu (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        feature_key VARCHAR(120) NOT NULL,
        feature_label VARCHAR(180) NOT NULL,
        page_slug VARCHAR(120) NULL,
        module_name VARCHAR(100) NOT NULL DEFAULT 'Lainnya',
        sort_order INT NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_rbac_menu_feature (feature_key),
        KEY idx_rbac_menu_page (page_slug),
        KEY idx_rbac_menu_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS rbac_jabatan_permission (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        unit_kerja VARCHAR(150) NOT NULL DEFAULT '',
        job_position VARCHAR(150) NOT NULL,
        group_jabatan VARCHAR(80) NOT NULL DEFAULT '',
        kode_kantor VARCHAR(5) NOT NULL DEFAULT '*',
        menu_id BIGINT UNSIGNED NOT NULL,
        can_view TINYINT(1) NOT NULL DEFAULT 0,
        can_create TINYINT(1) NOT NULL DEFAULT 0,
        can_update TINYINT(1) NOT NULL DEFAULT 0,
        can_delete TINYINT(1) NOT NULL DEFAULT 0,
        can_export TINYINT(1) NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        updated_by VARCHAR(50) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_rbac_jabatan_menu (unit_kerja, job_position, group_jabatan, kode_kantor, menu_id),
        KEY idx_rbac_jabatan_lookup (unit_kerja, job_position, group_jabatan, kode_kantor, is_active),
        KEY idx_rbac_jabatan_menu (menu_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $columns = $pdo->query("SHOW COLUMNS FROM rbac_jabatan_permission")->fetchAll(PDO::FETCH_ASSOC);
    $hasUnitColumn = false;
    foreach ($columns as $column) {
        if (($column['Field'] ?? '') === 'unit_kerja') {
            $hasUnitColumn = true;
            break;
        }
    }
    if (!$hasUnitColumn) {
        $pdo->exec("ALTER TABLE rbac_jabatan_permission
            ADD COLUMN unit_kerja VARCHAR(150) NOT NULL DEFAULT '' AFTER id");
    }

    $indexRows = $pdo->query("SHOW INDEX FROM rbac_jabatan_permission")->fetchAll(PDO::FETCH_ASSOC);
    $uniqueRows = array_values(array_filter($indexRows, static function (array $row): bool {
        return ($row['Key_name'] ?? '') === 'uq_rbac_jabatan_menu';
    }));
    usort($uniqueRows, static function (array $left, array $right): int {
        return ((int)($left['Seq_in_index'] ?? 0)) <=> ((int)($right['Seq_in_index'] ?? 0));
    });
    $uniqueColumns = array_values(array_map(static function (array $row): string {
        return (string)($row['Column_name'] ?? '');
    }, $uniqueRows));
    $expectedUniqueColumns = ['unit_kerja', 'job_position', 'group_jabatan', 'kode_kantor', 'menu_id'];
    if ($uniqueColumns !== $expectedUniqueColumns) {
        if ($uniqueRows) {
            $pdo->exec("ALTER TABLE rbac_jabatan_permission DROP INDEX uq_rbac_jabatan_menu");
        }
        $pdo->exec("ALTER TABLE rbac_jabatan_permission
            ADD UNIQUE KEY uq_rbac_jabatan_menu (unit_kerja, job_position, group_jabatan, kode_kantor, menu_id)");
    }

    $lookupRows = array_values(array_filter($indexRows, static function (array $row): bool {
        return ($row['Key_name'] ?? '') === 'idx_rbac_jabatan_lookup';
    }));
    usort($lookupRows, static function (array $left, array $right): int {
        return ((int)($left['Seq_in_index'] ?? 0)) <=> ((int)($right['Seq_in_index'] ?? 0));
    });
    $lookupColumns = array_values(array_map(static function (array $row): string {
        return (string)($row['Column_name'] ?? '');
    }, $lookupRows));
    $expectedLookupColumns = ['unit_kerja', 'job_position', 'group_jabatan', 'kode_kantor', 'is_active'];
    if ($lookupColumns !== $expectedLookupColumns) {
        if ($lookupRows) {
            $pdo->exec("ALTER TABLE rbac_jabatan_permission DROP INDEX idx_rbac_jabatan_lookup");
        }
        $pdo->exec("ALTER TABLE rbac_jabatan_permission
            ADD KEY idx_rbac_jabatan_lookup (unit_kerja, job_position, group_jabatan, kode_kantor, is_active)");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS rbac_user_permission (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        employee_id VARCHAR(50) NOT NULL,
        menu_id BIGINT UNSIGNED NOT NULL,
        can_view TINYINT(1) NOT NULL DEFAULT 0,
        can_create TINYINT(1) NOT NULL DEFAULT 0,
        can_update TINYINT(1) NOT NULL DEFAULT 0,
        can_delete TINYINT(1) NOT NULL DEFAULT 0,
        can_export TINYINT(1) NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        updated_by VARCHAR(50) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_rbac_user_menu (employee_id, menu_id),
        KEY idx_rbac_user_lookup (employee_id, is_active),
        KEY idx_rbac_user_menu (menu_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $insert = $pdo->prepare("INSERT INTO rbac_menu
        (feature_key, feature_label, page_slug, module_name, sort_order)
        VALUES (:feature_key, :feature_label, :page_slug, :module_name, :sort_order)
        ON DUPLICATE KEY UPDATE
          feature_label = VALUES(feature_label),
          page_slug = VALUES(page_slug),
          module_name = VALUES(module_name),
          sort_order = VALUES(sort_order)");
    foreach (rbacFeatureCatalog() as $feature) {
        $insert->execute($feature);
    }

    $ready[$connectionKey] = true;
}

function rbacNormalizeOffice($value): string
{
    $value = trim((string)$value);
    if ($value === '' || strtolower($value) === 'all' || $value === '*') {
        return '*';
    }
    return str_pad($value, 3, '0', STR_PAD_LEFT);
}

function rbacNormalizeEmployeeId($value): string
{
    return trim((string)$value);
}

function rbacSimpegPdo(): PDO
{
    static $simpegPdo = null;
    if ($simpegPdo instanceof PDO) {
        return $simpegPdo;
    }

    require_once __DIR__ . '/../controllers/config/database_simpeg.php';
    if (!($pdoSimpeg instanceof PDO)) {
        throw new RuntimeException('Koneksi database Simpeg belum tersedia.');
    }

    $simpegPdo = $pdoSimpeg;
    return $simpegPdo;
}

function rbacSimpegActiveEmployeeFromSql(): string
{
    return "FROM tb_jabatan j
        INNER JOIN tb_pegawai p ON j.id_peg = p.id_peg
        INNER JOIN tb_master_jabatan mj
            ON CAST(j.kode_jabatan AS CHAR) = CAST(mj.kode_jabatan AS CHAR)
        LEFT JOIN tb_kantor k ON j.unit_kerja = k.kode_kantor_detail
        WHERE j.status_jab = 'Aktif'
          AND p.status_aktif = '1'";
}

function rbacFindUserByEmployeeId(PDO $pdo, $employeeId): ?array
{
    $employeeId = rbacNormalizeEmployeeId($employeeId);
    if ($employeeId === '') {
        return null;
    }

    $simpeg = rbacSimpegPdo();
    $stmt = $simpeg->prepare("SELECT
        j.id_peg AS employee_id,
        COALESCE(NULLIF(TRIM(k.kode_cabang), ''), '*') AS kode,
        p.nama AS full_name,
        COALESCE(k.nama_kantor, '') AS branch_name,
        COALESCE(mj.nama_unit_kerja, '') AS unit_kerja,
        COALESCE(mj.nama_jabatan, '') AS job_position,
        COALESCE(mj.level, '') AS level,
        COALESCE(mj.group_jabatan, '') AS group_jabatan,
        p.status_kepeg,
        p.status_aktif,
        j.status_jab
        " . rbacSimpegActiveEmployeeFromSql() . "
        AND j.id_peg = :employee_id
        ORDER BY j.tmt_jabatan DESC, j.id_jab DESC
        LIMIT 1");
    $stmt->execute(['employee_id' => $employeeId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function rbacIsAdmin(array $user): bool
{
    return rbacNormalizeEmployeeId($user['employee_id'] ?? '') === '102-119';
}

function rbacUserOffice(array $user): string
{
    return rbacNormalizeOffice($user['kode'] ?? ($user['kode_kantor'] ?? '*'));
}

function rbacTokenFromRequest(): string
{
    $headers = [];
    if (function_exists('getallheaders')) {
        $headers = getallheaders() ?: [];
    }
    $authorization = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? ($headers['Authorization'] ?? ($headers['authorization'] ?? ''));
    $authorization = trim((string)$authorization);
    if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $match)) {
        return trim($match[1]);
    }
    return $authorization;
}

function rbacVerifyToken(string $token): ?array
{
    if ($token === '') {
        return null;
    }
    require_once __DIR__ . '/sso_guard.php';
    return ssoWhoami($token);
}

function rbacRequireUser(PDO $pdo): array
{
    $payload = rbacVerifyToken(rbacTokenFromRequest());
    $employeeId = $payload['employee_id'] ?? ($payload['id_peg'] ?? '');
    $user = rbacFindUserByEmployeeId($pdo, $employeeId);
    if (!$user) {
        sendResponse(401, 'Sesi pengguna tidak valid atau data pegawai tidak ditemukan.');
    }
    return $user;
}

function rbacRequireAdmin(PDO $pdo): array
{
    $user = rbacRequireUser($pdo);
    if (!rbacCanManageMatrix($pdo, $user)) {
        sendResponse(403, 'Anda tidak memiliki hak untuk mengatur akses menu.');
    }
    return $user;
}

function rbacPermissionColumns(): array
{
    return ['can_view', 'can_create', 'can_update', 'can_delete', 'can_export'];
}

function rbacEmptyPermission(): array
{
    return [
        'can_view' => 0,
        'can_create' => 0,
        'can_update' => 0,
        'can_delete' => 0,
        'can_export' => 0,
    ];
}

function rbacEffectivePermissions(PDO $pdo, array $user): array
{
    rbacEnsureSchema($pdo);
    $menus = $pdo->query("SELECT id, feature_key, feature_label, page_slug, module_name,
        sort_order FROM rbac_menu WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll(PDO::FETCH_ASSOC);

    $permissionMap = [];
    foreach ($menus as $menu) {
        $permissionMap[$menu['feature_key']] = rbacEmptyPermission();
    }

    if (rbacIsAdmin($user)) {
        foreach ($permissionMap as &$permission) {
            $permission['can_view'] = 1;
            $permission['can_create'] = 1;
            $permission['can_update'] = 1;
            $permission['can_delete'] = 1;
            $permission['can_export'] = 1;
        }
        unset($permission);
        return [
            'admin' => true,
            'configured' => true,
            'legacy_fallback' => false,
            'permissions' => $permissionMap,
        ];
    }

    $job = trim((string)($user['job_position'] ?? ''));
    $group = trim((string)($user['group_jabatan'] ?? ''));
    $unit = trim((string)($user['unit_kerja'] ?? ''));
    $office = rbacUserOffice($user);

    $applyRows = static function (array $rows) use (&$permissionMap): void {
        foreach ($rows as $row) {
            $key = (string)($row['feature_key'] ?? '');
            if (!isset($permissionMap[$key])) {
                continue;
            }
            foreach (rbacPermissionColumns() as $column) {
                $permissionMap[$key][$column] = (int)$row[$column];
            }
        }
    };

    $divisionRows = [];
    if ($unit !== '') {
        $divisionStmt = $pdo->prepare("SELECT m.feature_key, jp.can_view, jp.can_create,
            jp.can_update, jp.can_delete, jp.can_export
            FROM rbac_jabatan_permission jp
            INNER JOIN rbac_menu m ON m.id = jp.menu_id AND m.is_active = 1
            WHERE jp.is_active = 1 AND jp.unit_kerja = :unit
              AND jp.job_position = '' AND jp.group_jabatan = ''
              AND (jp.kode_kantor = '*' OR jp.kode_kantor = :division_office)
            ORDER BY (jp.kode_kantor = :division_office_exact) ASC");
        $divisionStmt->execute([
            'unit' => $unit,
            'division_office' => $office,
            'division_office_exact' => $office,
        ]);
        $divisionRows = $divisionStmt->fetchAll(PDO::FETCH_ASSOC);
        $applyRows($divisionRows);
    }

    $jobRows = [];
    if ($job !== '') {
        $jobStmt = $pdo->prepare("SELECT m.feature_key, jp.can_view, jp.can_create,
            jp.can_update, jp.can_delete, jp.can_export
            FROM rbac_jabatan_permission jp
            INNER JOIN rbac_menu m ON m.id = jp.menu_id AND m.is_active = 1
            WHERE jp.is_active = 1 AND jp.unit_kerja = '' AND jp.job_position = :job
              AND (jp.group_jabatan = :job_group OR jp.group_jabatan = '')
              AND (jp.kode_kantor = '*' OR jp.kode_kantor = :job_office)
            ORDER BY (jp.kode_kantor = :job_office_exact) ASC,
                     (jp.group_jabatan = :job_group_exact) ASC");
        $jobStmt->execute([
            'job' => $job,
            'job_group' => $group,
            'job_office' => $office,
            'job_office_exact' => $office,
            'job_group_exact' => $group,
        ]);
        $jobRows = $jobStmt->fetchAll(PDO::FETCH_ASSOC);
        $applyRows($jobRows);
    }

    $userStmt = $pdo->prepare("SELECT m.feature_key, up.can_view, up.can_create,
        up.can_update, up.can_delete, up.can_export
        FROM rbac_user_permission up
        INNER JOIN rbac_menu m ON m.id = up.menu_id AND m.is_active = 1
        WHERE up.is_active = 1 AND up.employee_id = :employee_id");
    $userStmt->execute(['employee_id' => rbacNormalizeEmployeeId($user['employee_id'] ?? '')]);
    $userRows = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    $userConfigured = count($userRows) > 0;
    $applyRows($userRows);
    $hasScopedConfiguration = count($divisionRows) > 0 || count($jobRows) > 0 || $userConfigured;
    if (!$hasScopedConfiguration && isset($permissionMap['page:dashboard'])) {
        $permissionMap['page:dashboard']['can_view'] = 1;
    }

    return [
        'admin' => false,
        'configured' => true,
        'legacy_fallback' => false,
        'permissions' => $permissionMap,
    ];
}

function rbacCanAccessPage(PDO $pdo, array $user, string $page): bool
{
    if (rbacIsAdmin($user)) {
        return true;
    }
    $access = rbacEffectivePermissions($pdo, $user);
    $key = 'page:' . trim($page);
    return !empty($access['permissions'][$key]['can_view']);
}

function rbacCanManageMatrix(PDO $pdo, array $user): bool
{
    if (rbacIsAdmin($user)) {
        return true;
    }

    $access = rbacEffectivePermissions($pdo, $user);
    return !empty($access['permissions']['page:setting_rbac']['can_view']);
}

function rbacUserFromCookie(PDO $pdo): ?array
{
    $token = trim((string)($_COOKIE['monbis_session'] ?? ($_COOKIE['sso_token'] ?? '')));
    $payload = rbacVerifyToken($token);
    if (!$payload) {
        return null;
    }
    $employeeId = $payload['employee_id'] ?? ($payload['id_peg'] ?? '');
    return rbacFindUserByEmployeeId($pdo, $employeeId);
}

function rbacAccessFromCookie(PDO $pdo, string $page): ?bool
{
    $user = rbacUserFromCookie($pdo);
    if (!$user) {
        return null;
    }
    if (trim($page) === 'setting_rbac') {
        return rbacCanManageMatrix($pdo, $user);
    }
    return rbacCanAccessPage($pdo, $user, $page);
}

function rbacPermissionKeysFromInput(array $input): array
{
    $raw = $input['feature_keys'] ?? ($input['permissions'] ?? []);
    if (!is_array($raw)) {
        return [];
    }
    $keys = [];
    foreach ($raw as $key => $value) {
        if (is_string($value) && str_starts_with($value, 'page:')) {
            $keys[] = trim($value);
        } elseif (is_string($key) && str_starts_with($key, 'page:') && (int)$value === 1) {
            $keys[] = trim($key);
        } elseif (is_array($value) && str_starts_with((string)($value['feature_key'] ?? ''), 'page:') && (int)($value['can_view'] ?? 0) === 1) {
            $keys[] = trim((string)$value['feature_key']);
        }
    }
    return array_values(array_unique(array_filter($keys)));
}

function rbacMenuRows(PDO $pdo): array
{
    rbacEnsureSchema($pdo);
    return $pdo->query("SELECT id, feature_key, feature_label, page_slug, module_name,
        sort_order, is_active FROM rbac_menu ORDER BY sort_order, feature_label")->fetchAll(PDO::FETCH_ASSOC);
}

function rbacPermissionRowsForRole(PDO $pdo, array $scope): array
{
    rbacEnsureSchema($pdo);
    $scopeType = strtolower(trim((string)($scope['scope_type'] ?? 'jabatan')));
    $isDivision = $scopeType === 'divisi' || $scopeType === 'division';
    if ($isDivision) {
        $stmt = $pdo->prepare("SELECT m.feature_key, jp.can_view, jp.can_create,
            jp.can_update, jp.can_delete, jp.can_export
            FROM rbac_jabatan_permission jp
            INNER JOIN rbac_menu m ON m.id = jp.menu_id
            WHERE jp.unit_kerja = :unit AND jp.job_position = ''
              AND jp.group_jabatan = '' AND jp.kode_kantor = :office AND jp.is_active = 1");
        $stmt->execute([
            'unit' => trim((string)($scope['unit_kerja'] ?? '')),
            'office' => rbacNormalizeOffice($scope['kode_kantor'] ?? '*'),
        ]);
    } else {
        $stmt = $pdo->prepare("SELECT m.feature_key, jp.can_view, jp.can_create,
            jp.can_update, jp.can_delete, jp.can_export
            FROM rbac_jabatan_permission jp
            INNER JOIN rbac_menu m ON m.id = jp.menu_id
            WHERE jp.unit_kerja = '' AND jp.job_position = :job
              AND jp.group_jabatan = :group AND jp.kode_kantor = :office AND jp.is_active = 1");
        $stmt->execute([
            'job' => trim((string)($scope['job_position'] ?? '')),
            'group' => trim((string)($scope['group_jabatan'] ?? '')),
            'office' => rbacNormalizeOffice($scope['kode_kantor'] ?? '*'),
        ]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function rbacPermissionRowsForUsers(PDO $pdo, array $employeeIds): array
{
    rbacEnsureSchema($pdo);
    $employeeIds = array_values(array_unique(array_filter(array_map('rbacNormalizeEmployeeId', $employeeIds))));
    if (!$employeeIds) {
        return [];
    }
    $user = rbacFindUserByEmployeeId($pdo, $employeeIds[0]);
    if (!$user) {
        return [];
    }

    $effective = rbacEffectivePermissions($pdo, $user);
    $rows = [];
    foreach ($effective['permissions'] as $featureKey => $permission) {
        $rows[] = array_merge(
            ['employee_id' => $employeeIds[0], 'feature_key' => $featureKey],
            $permission
        );
    }
    return $rows;
}

function rbacSaveRole(PDO $pdo, array $input, array $actor): void
{
    rbacEnsureSchema($pdo);
    $scopeType = strtolower(trim((string)($input['scope_type'] ?? 'jabatan')));
    $isDivision = $scopeType === 'divisi' || $scopeType === 'division';
    $unit = trim((string)($input['unit_kerja'] ?? ''));
    $job = trim((string)($input['job_position'] ?? ''));
    $group = trim((string)($input['group_jabatan'] ?? ''));
    if ($isDivision) {
        if ($unit === '') {
            sendResponse(422, 'Divisi wajib dipilih.');
        }
        $job = '';
        $group = '';
    } elseif ($job === '') {
        sendResponse(422, 'Jabatan wajib dipilih.');
    } else {
        $unit = '';
    }
    $office = rbacNormalizeOffice($input['kode_kantor'] ?? '*');
    $keys = rbacPermissionKeysFromInput($input);
    $menus = rbacMenuRows($pdo);
    $allowed = array_fill_keys($keys, true);
    $actorId = rbacNormalizeEmployeeId($actor['employee_id'] ?? '');

    $pdo->beginTransaction();
    try {
        $delete = $pdo->prepare("DELETE FROM rbac_jabatan_permission
            WHERE unit_kerja = :unit AND job_position = :job
              AND group_jabatan = :group AND kode_kantor = :office");
        $delete->execute(['unit' => $unit, 'job' => $job, 'group' => $group, 'office' => $office]);
        $insert = $pdo->prepare("INSERT INTO rbac_jabatan_permission
            (unit_kerja, job_position, group_jabatan, kode_kantor, menu_id, can_view, updated_by)
            VALUES (:unit, :job, :group, :office, :menu_id, :can_view, :updated_by)");
        foreach ($menus as $menu) {
            $insert->execute([
                'unit' => $unit,
                'job' => $job,
                'group' => $group,
                'office' => $office,
                'menu_id' => (int)$menu['id'],
                'can_view' => isset($allowed[$menu['feature_key']]) ? 1 : 0,
                'updated_by' => $actorId,
            ]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function rbacSaveUsers(PDO $pdo, array $input, array $actor): void
{
    rbacEnsureSchema($pdo);
    $ids = $input['employee_ids'] ?? [];
    if (!is_array($ids)) {
        $ids = [$ids];
    }
    $ids = array_values(array_unique(array_filter(array_map('rbacNormalizeEmployeeId', $ids))));
    if (!$ids) {
        sendResponse(422, 'Pilih minimal satu ID pegawai.');
    }
    $keys = rbacPermissionKeysFromInput($input);
    $allowed = array_fill_keys($keys, true);
    $menus = rbacMenuRows($pdo);
    $actorId = rbacNormalizeEmployeeId($actor['employee_id'] ?? '');

    $pdo->beginTransaction();
    try {
        $delete = $pdo->prepare("DELETE FROM rbac_user_permission WHERE employee_id = :employee_id");
        $insert = $pdo->prepare("INSERT INTO rbac_user_permission
            (employee_id, menu_id, can_view, updated_by)
            VALUES (:employee_id, :menu_id, :can_view, :updated_by)");
        foreach ($ids as $employeeId) {
            $delete->execute(['employee_id' => $employeeId]);
            foreach ($menus as $menu) {
                $insert->execute([
                    'employee_id' => $employeeId,
                    'menu_id' => (int)$menu['id'],
                    'can_view' => isset($allowed[$menu['feature_key']]) ? 1 : 0,
                    'updated_by' => $actorId,
                ]);
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function rbacResetUsers(PDO $pdo, array $input): void
{
    $ids = $input['employee_ids'] ?? [];
    if (!is_array($ids)) {
        $ids = [$ids];
    }
    $ids = array_values(array_unique(array_filter(array_map('rbacNormalizeEmployeeId', $ids))));
    if (!$ids) {
        sendResponse(422, 'Pilih minimal satu ID pegawai.');
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM rbac_user_permission WHERE employee_id IN ({$placeholders})");
    $stmt->execute($ids);
}

