<?php
declare(strict_types=1);

const MONBIS_DEFAULT_PASSWORD = 'bkkjtg123';
const MONBIS_PASSWORD_CHANGE_SESSION_KEY = 'monbis_password_change_policy';

function passwordPolicyEnsureSession(): void
{
    if (session_status() === PHP_SESSION_NONE && !session_start()) {
        throw new RuntimeException('Sesi PHP tidak dapat dimulai.');
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException('Sesi PHP tidak aktif.');
    }
}

function passwordPolicyIsRequired(string $employeeId): bool
{
    passwordPolicyEnsureSession();
    $employeeId = trim($employeeId);
    $state = $_SESSION[MONBIS_PASSWORD_CHANGE_SESSION_KEY] ?? null;
    return $employeeId !== ''
        && is_array($state)
        && !empty($state['required'])
        && hash_equals((string)($state['employee_id'] ?? ''), $employeeId);
}

function passwordPolicyMarkRequired(string $employeeId): void
{
    passwordPolicyEnsureSession();
    $employeeId = trim($employeeId);
    if ($employeeId === '') {
        throw new InvalidArgumentException('ID pegawai kosong saat menyimpan kewajiban ganti password.');
    }
    $_SESSION[MONBIS_PASSWORD_CHANGE_SESSION_KEY] = [
        'employee_id' => $employeeId,
        'required' => true,
    ];
}

function passwordPolicyClearRequired(string $employeeId = ''): void
{
    passwordPolicyEnsureSession();
    $employeeId = trim($employeeId);
    if ($employeeId === '') {
        unset($_SESSION[MONBIS_PASSWORD_CHANGE_SESSION_KEY]);
        return;
    }

    // Simpan status selesai dalam sesi agar claim lama di token SSO tidak
    // mengunci ulang akun setelah password berhasil diganti.
    $_SESSION[MONBIS_PASSWORD_CHANGE_SESSION_KEY] = [
        'employee_id' => $employeeId,
        'required' => false,
    ];
}

function passwordPolicyAdoptUpstreamRequirement(string $employeeId, bool $upstreamRequired): void
{
    passwordPolicyEnsureSession();
    $employeeId = trim($employeeId);
    if ($employeeId === '') {
        return;
    }

    $state = $_SESSION[MONBIS_PASSWORD_CHANGE_SESSION_KEY] ?? null;
    if (is_array($state) && hash_equals((string)($state['employee_id'] ?? ''), $employeeId)) {
        return;
    }
    if ($upstreamRequired) {
        passwordPolicyMarkRequired($employeeId);
    }
}
