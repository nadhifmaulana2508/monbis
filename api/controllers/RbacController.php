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
        $roles = $this->pdo->query("SELECT job_position, COALESCE(group_jabatan, '') AS group_jabatan,
            COALESCE(kode, '*') AS kode FROM users WHERE job_position IS NOT NULL AND job_position <> ''
            GROUP BY job_position, group_jabatan, kode ORDER BY job_position, group_jabatan, kode")->fetchAll(PDO::FETCH_ASSOC);
        $divisions = $this->pdo->query("SELECT DISTINCT TRIM(unit_kerja) AS unit_kerja
            FROM users WHERE unit_kerja IS NOT NULL AND TRIM(unit_kerja) <> ''
            ORDER BY unit_kerja")->fetchAll(PDO::FETCH_ASSOC);
        $users = $this->pdo->query("SELECT employee_id, full_name, job_position, group_jabatan, kode,
            branch_name, unit_kerja FROM users WHERE employee_id IS NOT NULL AND employee_id <> ''
            ORDER BY full_name, employee_id")->fetchAll(PDO::FETCH_ASSOC);
        sendResponse(200, 'Master RBAC berhasil dimuat.', [
            'menus' => rbacMenuRows($this->pdo),
            'roles' => $roles,
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

