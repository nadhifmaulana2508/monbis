<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/rbac.php';

class RbacController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        rbacEnsureSchema($pdo);
    }

    private function user(): array
    {
        return rbacRequireUser($this->pdo);
    }

    private function admin(): array
    {
        return rbacRequireAdmin($this->pdo);
    }

    public function me(): void
    {
        $user = $this->user();
        sendResponse(200, 'Hak akses berhasil dimuat.', rbacEffectivePermissions($this->pdo, $user));
    }

    public function bootstrap(): void
    {
        $this->admin();
        $simpeg = rbacSimpegPdo();
        $activeEmployeeFrom = rbacSimpegActiveEmployeeFromSql();

        $roles = $simpeg->query("SELECT DISTINCT
            COALESCE(NULLIF(TRIM(mj.nama_jabatan), ''), '') AS job_position,
            COALESCE(mj.group_jabatan, '') AS group_jabatan,
            COALESCE(NULLIF(TRIM(k.kode_cabang), ''), '*') AS kode,
            COALESCE(NULLIF(TRIM(k.nama_kantor), ''), '') AS branch_name
            {$activeEmployeeFrom}
            AND mj.nama_jabatan IS NOT NULL AND TRIM(mj.nama_jabatan) <> ''
            ORDER BY job_position, group_jabatan, kode")->fetchAll(PDO::FETCH_ASSOC);

        $offices = [];
        foreach ($roles as $role) {
            $code = trim((string)($role['kode'] ?? '*')) ?: '*';
            $offices[$code] = [
                'kode' => $code,
                'branch_name' => trim((string)($role['branch_name'] ?? '')),
            ];
        }
        $offices['*'] = ['kode' => '*', 'branch_name' => ''];
        uasort($offices, static function (array $left, array $right): int {
            if ($left['kode'] === '*') return -1;
            if ($right['kode'] === '*') return 1;
            return strnatcasecmp($left['branch_name'] . ' ' . $left['kode'], $right['branch_name'] . ' ' . $right['kode']);
        });

        $divisions = $simpeg->query("SELECT DISTINCT TRIM(mj.nama_unit_kerja) AS unit_kerja
            {$activeEmployeeFrom}
            AND mj.nama_unit_kerja IS NOT NULL AND TRIM(mj.nama_unit_kerja) <> ''
            ORDER BY unit_kerja")->fetchAll(PDO::FETCH_ASSOC);

        $users = $simpeg->query("SELECT DISTINCT
            j.id_peg AS employee_id,
            p.nama AS full_name,
            COALESCE(mj.nama_jabatan, '') AS job_position,
            COALESCE(mj.group_jabatan, '') AS group_jabatan,
            COALESCE(NULLIF(TRIM(k.kode_cabang), ''), '*') AS kode,
            COALESCE(k.nama_kantor, '') AS branch_name,
            COALESCE(mj.nama_unit_kerja, '') AS unit_kerja
            {$activeEmployeeFrom}
            AND j.id_peg IS NOT NULL AND j.id_peg <> ''
            ORDER BY full_name, employee_id")->fetchAll(PDO::FETCH_ASSOC);
        sendResponse(200, 'Master RBAC berhasil dimuat.', [
            'menus' => rbacMenuRows($this->pdo),
            'roles' => $roles,
            'offices' => array_values($offices),
            'divisions' => $divisions,
            'users' => $users,
        ]);
    }

    public function matrix(array $input): void
    {
        $this->admin();
        $scope = strtolower(trim((string)($input['scope'] ?? 'role')));
        if ($scope === 'user') {
            $ids = $input['employee_ids'] ?? [];
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            sendResponse(200, 'Matrix pegawai berhasil dimuat.', [
                'rows' => rbacPermissionRowsForUsers($this->pdo, $ids),
            ]);
        }
        sendResponse(200, 'Matrix jabatan berhasil dimuat.', [
            'rows' => rbacPermissionRowsForRole($this->pdo, $input),
        ]);
    }

    public function saveRole(array $input): void
    {
        $actor = $this->admin();
        rbacSaveRole($this->pdo, $input, $actor);
        sendResponse(200, 'Akses jabatan berhasil disimpan.');
    }

    public function saveUsers(array $input): void
    {
        $actor = $this->admin();
        rbacSaveUsers($this->pdo, $input, $actor);
        sendResponse(200, 'Override akses pegawai berhasil disimpan.');
    }

    public function resetUsers(array $input): void
    {
        $this->admin();
        rbacResetUsers($this->pdo, $input);
        sendResponse(200, 'Override pegawai dikembalikan ke jabatan.');
    }
}

