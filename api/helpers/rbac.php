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
        ['tv_cabang', 'TV Cabang', 'Lainnya'],
        ['profile', 'Profile', 'Lainnya'],
        ['update_flowpar', 'Update Flow PAR', 'Lainnya'],
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
        ['proyeksi_rr', 'Proyeksi RR & NPL', 'Kalkulator'],
        ['komitmen_rekap', 'Rekap Komitmen', 'Kalkulator'],
        ['komitmen_kalkulator', 'Kalkulator Komitmen', 'Kalkulator'],
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
        ['audit_log', 'Audit Log', 'Admin'],
    ];

    $catalog = [];
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
        $order += 10;
    }

    return $catalog;
}

function rbacRetireUnusedOtherMenu(PDO $pdo): void
{
    $pdo->exec("UPDATE rbac_menu
        SET is_active = 0
        WHERE module_name = 'Lainnya'
          AND feature_key NOT IN ('page:profile', 'page:tv', 'page:tv_cabang', 'page:update_flowpar')");
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
        'rbac_menu_access_log',
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
        $auditColumns = $pdo->query("SHOW COLUMNS FROM rbac_menu_access_log")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('employee_name', $auditColumns, true)) {
            $pdo->exec("ALTER TABLE rbac_menu_access_log ADD COLUMN employee_name VARCHAR(180) NULL AFTER employee_id");
            $auditColumns[] = 'employee_name';
        }
        if (!in_array('action_type', $auditColumns, true)) {
            $pdo->exec("ALTER TABLE rbac_menu_access_log ADD COLUMN action_type ENUM('menu_view', 'detail', 'download', 'update') NOT NULL DEFAULT 'menu_view' AFTER feature_key");
            $auditColumns[] = 'action_type';
        }
        $actionTypeColumn = $pdo->query("SHOW COLUMNS FROM rbac_menu_access_log LIKE 'action_type'")->fetch(PDO::FETCH_ASSOC);
        if (is_array($actionTypeColumn) && stripos((string)($actionTypeColumn['Type'] ?? ''), "'update'") === false) {
            $pdo->exec("ALTER TABLE rbac_menu_access_log MODIFY action_type ENUM('menu_view', 'detail', 'download', 'update') NOT NULL DEFAULT 'menu_view'");
        }
        if (!in_array('action_label', $auditColumns, true)) {
            $pdo->exec("ALTER TABLE rbac_menu_access_log ADD COLUMN action_label VARCHAR(180) NULL AFTER action_type");
        }
        $auditMetadataColumns = [
            'request_path' => 'VARCHAR(255) NULL',
            'request_method' => 'VARCHAR(10) NULL',
            'http_status' => 'SMALLINT UNSIGNED NULL',
            'ip_address' => 'VARCHAR(45) NULL',
        ];
        foreach ($auditMetadataColumns as $column => $definition) {
            if (!in_array($column, $auditColumns, true)) {
                $pdo->exec("ALTER TABLE rbac_menu_access_log ADD COLUMN {$column} {$definition}");
            }
        }

        rbacRetireUnusedOtherMenu($pdo);
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

    $pdo->exec("CREATE TABLE IF NOT EXISTS rbac_menu_access_log (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        employee_id VARCHAR(50) NOT NULL,
        employee_name VARCHAR(180) NULL,
        feature_key VARCHAR(120) NOT NULL,
        request_path VARCHAR(255) NULL,
        request_method VARCHAR(10) NULL,
        http_status SMALLINT UNSIGNED NULL,
        ip_address VARCHAR(45) NULL,
        action_type ENUM('menu_view', 'detail', 'download', 'update') NOT NULL DEFAULT 'menu_view',
        action_label VARCHAR(180) NULL,
        access_result ENUM('allowed', 'denied') NOT NULL DEFAULT 'allowed',
        accessed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_rbac_access_time (accessed_at),
        KEY idx_rbac_access_user_time (employee_id, accessed_at),
        KEY idx_rbac_access_menu_time (feature_key, accessed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $insert = $pdo->prepare("INSERT INTO rbac_menu
        (feature_key, feature_label, page_slug, module_name, sort_order)
        VALUES (:feature_key, :feature_label, :page_slug, :module_name, :sort_order)
        ON DUPLICATE KEY UPDATE
          feature_label = VALUES(feature_label),
          page_slug = VALUES(page_slug),
          module_name = VALUES(module_name),
          sort_order = VALUES(sort_order),
          is_active = 1");
    foreach (rbacFeatureCatalog() as $feature) {
        $insert->execute($feature);
    }
    $commitmentMenus = $pdo->prepare("SELECT feature_key, id FROM rbac_menu
        WHERE feature_key IN ('page:komitmen_rekap', 'page:komitmen_kalkulator') AND is_active = 1");
    $commitmentMenus->execute();
    $menuIds = $commitmentMenus->fetchAll(PDO::FETCH_KEY_PAIR);
    $sourceId = $pdo->query("SELECT id FROM rbac_menu WHERE feature_key = 'page:proyeksi_rr' AND is_active = 1 LIMIT 1")->fetchColumn();
    if ($sourceId !== false) {
        foreach ($menuIds as $targetId) {
            $copyRoleAccess = $pdo->prepare("INSERT IGNORE INTO rbac_jabatan_permission
                (unit_kerja, job_position, group_jabatan, kode_kantor, menu_id, can_view, can_create, can_update, can_delete, can_export, is_active, updated_by)
                SELECT unit_kerja, job_position, group_jabatan, kode_kantor, :target_menu, can_view,
                    CASE WHEN can_view = 1 THEN 1 ELSE 0 END, can_update, can_delete, can_export, is_active, updated_by
                FROM rbac_jabatan_permission WHERE menu_id = :source_menu AND is_active = 1");
            $copyRoleAccess->execute(['target_menu' => $targetId, 'source_menu' => $sourceId]);
            $copyUserAccess = $pdo->prepare("INSERT IGNORE INTO rbac_user_permission
                (employee_id, menu_id, can_view, can_create, can_update, can_delete, can_export, is_active, updated_by)
                SELECT employee_id, :target_menu, can_view,
                    CASE WHEN can_view = 1 THEN 1 ELSE 0 END, can_update, can_delete, can_export, is_active, updated_by
                FROM rbac_user_permission WHERE menu_id = :source_menu AND is_active = 1");
            $copyUserAccess->execute(['target_menu' => $targetId, 'source_menu' => $sourceId]);
        }
    }
    rbacRetireUnusedOtherMenu($pdo);

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
    // api/index.php sudah memvalidasi sesi dari cookie HttpOnly atau header
    // Authorization sebelum route RBAC dipanggil. Gunakan identitas itu dulu;
    // token parser di bawah hanya fallback untuk pemanggilan lama di luar router.
    $payload = $GLOBALS['user'] ?? null;
    if (!is_array($payload) || empty($payload['employee_id'] ?? $payload['id_peg'] ?? $payload['id'] ?? '')) {
        $payload = rbacVerifyToken(rbacTokenFromRequest());
    }
    $employeeId = $payload['employee_id'] ?? ($payload['id_peg'] ?? ($payload['id'] ?? ''));
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

    $applyRows = static function (array $rows, bool $viewOnly = false) use (&$permissionMap): void {
        foreach ($rows as $row) {
            $key = (string)($row['feature_key'] ?? '');
            if (!isset($permissionMap[$key])) {
                continue;
            }
            foreach (rbacPermissionColumns() as $column) {
                if ($viewOnly && $column !== 'can_view') {
                    continue;
                }
                if (array_key_exists($column, $row)) {
                    $permissionMap[$key][$column] = (int)$row[$column];
                }
            }
        }
    };

    if (rbacIsAdmin($user)) {
        foreach ($permissionMap as &$permission) {
            $permission['can_view'] = 1;
            $permission['can_create'] = 1;
            $permission['can_update'] = 1;
            $permission['can_delete'] = 1;
            $permission['can_export'] = 1;
        }
        unset($permission);

        $userStmt = $pdo->prepare("SELECT m.feature_key, up.can_view
            FROM rbac_user_permission up
            INNER JOIN rbac_menu m ON m.id = up.menu_id AND m.is_active = 1
            WHERE up.is_active = 1 AND up.employee_id = :employee_id");
        $userStmt->execute(['employee_id' => rbacNormalizeEmployeeId($user['employee_id'] ?? '')]);
        $userRows = $userStmt->fetchAll(PDO::FETCH_ASSOC);
        $applyRows($userRows, true);
        if (isset($permissionMap['page:setting_rbac'])) {
            $permissionMap['page:setting_rbac']['can_view'] = 1;
        }

        return [
            'admin' => count($userRows) === 0,
            'configured' => true,
            'legacy_fallback' => false,
            'permissions' => $permissionMap,
        ];
    }

    $job = trim((string)($user['job_position'] ?? ''));
    $group = trim((string)($user['group_jabatan'] ?? ''));
    $unit = trim((string)($user['unit_kerja'] ?? ''));
    $office = rbacUserOffice($user);

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
    $access = rbacEffectivePermissions($pdo, $user);
    $key = 'page:' . trim($page);
    if (rbacIsAdmin($user) && !isset($access['permissions'][$key])) {
        return true;
    }
    return !empty($access['permissions'][$key]['can_view']);
}

function rbacLogMenuAccess(
    PDO $pdo,
    string $employeeId,
    string $pageSlug,
    string $result = 'allowed',
    string $employeeName = '',
    ?string $routeParam = null,
    ?int $httpStatus = null
): void
{
    $employeeId = trim($employeeId);
    $employeeName = trim($employeeName);
    $pageSlug = strtolower(trim($pageSlug));
    if ($employeeId === '' || $pageSlug === '' || !in_array($result, ['allowed', 'denied'], true)) {
        return;
    }

    $featureKey = 'page:' . $pageSlug;
    $knownKeys = array_column(rbacFeatureCatalog(), 'feature_key');
    if (!in_array($featureKey, $knownKeys, true)) {
        return;
    }

    $requestPath = rbacAuditPageData($pageSlug, $routeParam);
    $requestMethod = rbacAuditRequestMethod((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $httpStatus = $httpStatus ?? ($result === 'denied' ? 403 : (int)(http_response_code() ?: 200));
    $ipAddress = rbacAuditClientIpAddress();

    try {
        $stmt = $pdo->prepare("INSERT INTO rbac_menu_access_log
            (employee_id, employee_name, feature_key, request_path, request_method, http_status, ip_address, action_type, access_result, accessed_at)
            VALUES (:employee_id, :employee_name, :feature_key, :request_path, :request_method, :http_status, :ip_address, 'menu_view', :access_result, :accessed_at)");
        $stmt->execute([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName !== '' ? $employeeName : null,
            'feature_key' => $featureKey,
            'request_path' => $requestPath,
            'request_method' => $requestMethod,
            'http_status' => $httpStatus,
            'ip_address' => $ipAddress,
            'access_result' => $result,
            'accessed_at' => gmdate('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $error) {
        error_log('Menu access audit logging failed: ' . $error->getMessage());
    }
}

function rbacScheduleMenuAccessLog(
    PDO $pdo,
    string $employeeId,
    string $pageSlug,
    string $employeeName = '',
    ?string $routeParam = null
): void
{
    register_shutdown_function(static function () use ($pdo, $employeeId, $pageSlug, $employeeName, $routeParam): void {
        $status = http_response_code();
        rbacLogMenuAccess(
            $pdo,
            $employeeId,
            $pageSlug,
            'allowed',
            $employeeName,
            $routeParam,
            is_int($status) && $status >= 100 ? $status : 200
        );
    });
}

function rbacAuditPageData(string $pageSlug, ?string $routeParam = null): string
{
    $pageSlug = strtolower(trim($pageSlug, " /\\\t\n\r\0\x0B"));
    $path = '/' . $pageSlug;
    $routeParam = trim((string)$routeParam, " /\\\t\n\r\0\x0B");
    if ($routeParam !== '') {
        $routeParam = preg_replace('/[\x00-\x1F\x7F?#]+/u', '', $routeParam) ?? '';
        if ($routeParam !== '') $path .= '/' . $routeParam;
    }
    $path .= ' [page=' . $pageSlug . ']';
    return function_exists('mb_substr') ? mb_substr($path, 0, 255, 'UTF-8') : substr($path, 0, 255);
}

function rbacAuditRequestMethod(string $method): string
{
    $method = strtoupper(trim($method));
    return preg_match('/^[A-Z]{3,10}$/', $method) ? $method : 'GET';
}

function rbacAuditClientIpAddress(): ?string
{
    $ipAddress = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
    return filter_var($ipAddress, FILTER_VALIDATE_IP) ? $ipAddress : null;
}

function rbacLogAuditEvent(PDO $pdo, array $user, string $pageSlug, string $actionType, string $actionLabel, ?string $routeParam = null): void
{
    $employeeId = trim((string)($user['employee_id'] ?? $user['id_peg'] ?? ''));
    $pageSlug = strtolower(trim($pageSlug));
    $actionType = strtolower(trim($actionType));
    $featureKey = 'page:' . $pageSlug;
    if ($employeeId === ''
        || !in_array($featureKey, array_column(rbacFeatureCatalog(), 'feature_key'), true)
        || !in_array($actionType, ['detail', 'download', 'update'], true)) {
        return;
    }

    $employeeName = trim((string)($user['full_name'] ?? $user['name'] ?? ''));
    $actionLabel = trim(strip_tags($actionLabel));
    $actionLabel = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $actionLabel) ?? '';
    $actionLabel = preg_replace('/\s+/u', ' ', $actionLabel) ?? trim($actionLabel);
    $actionLabel = function_exists('mb_substr')
        ? mb_substr($actionLabel, 0, 180, 'UTF-8')
        : substr($actionLabel, 0, 180);

    try {
        rbacEnsureSchema($pdo);
        $stmt = $pdo->prepare("INSERT INTO rbac_menu_access_log
            (employee_id, employee_name, feature_key, request_path, request_method, http_status, ip_address, action_type, action_label, access_result, accessed_at)
            VALUES (:employee_id, :employee_name, :feature_key, :request_path, :request_method, 200, :ip_address, :action_type, :action_label, 'allowed', :accessed_at)");
        $stmt->execute([
            'employee_id' => $employeeId,
            'employee_name' => $employeeName !== '' ? $employeeName : null,
            'feature_key' => $featureKey,
            'request_path' => rbacAuditPageData($pageSlug, $routeParam),
            'request_method' => rbacAuditRequestMethod((string)($_SERVER['REQUEST_METHOD'] ?? 'POST')),
            'ip_address' => rbacAuditClientIpAddress(),
            'action_type' => $actionType,
            'action_label' => $actionLabel !== '' ? $actionLabel : null,
            'accessed_at' => gmdate('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $error) {
        error_log('Audit event logging failed: ' . $error->getMessage());
    }
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
        sort_order, is_active FROM rbac_menu WHERE is_active = 1 ORDER BY sort_order, feature_label")->fetchAll(PDO::FETCH_ASSOC);
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
    $employeeFeatureKeys = $input['employee_feature_keys'] ?? [];
    if (!is_array($employeeFeatureKeys)) {
        $employeeFeatureKeys = [];
    }
    $menus = rbacMenuRows($pdo);
    $actorId = rbacNormalizeEmployeeId($actor['employee_id'] ?? '');

    $pdo->beginTransaction();
    try {
        $delete = $pdo->prepare("DELETE FROM rbac_user_permission WHERE employee_id = :employee_id");
        $insert = $pdo->prepare("INSERT INTO rbac_user_permission
            (employee_id, menu_id, can_view, updated_by)
            VALUES (:employee_id, :menu_id, :can_view, :updated_by)");
        foreach ($ids as $employeeId) {
            $employeeKeys = isset($employeeFeatureKeys[$employeeId]) && is_array($employeeFeatureKeys[$employeeId])
                ? rbacPermissionKeysFromInput(['feature_keys' => $employeeFeatureKeys[$employeeId]])
                : $keys;
            if ($employeeId === '102-119') {
                $employeeKeys[] = 'page:setting_rbac';
            }
            $employeeAllowed = array_fill_keys($employeeKeys, true);
            $delete->execute(['employee_id' => $employeeId]);
            foreach ($menus as $menu) {
                $insert->execute([
                    'employee_id' => $employeeId,
                    'menu_id' => (int)$menu['id'],
                    'can_view' => isset($employeeAllowed[$menu['feature_key']]) ? 1 : 0,
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

