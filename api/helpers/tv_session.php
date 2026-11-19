<?php

const MONBIS_TV_SESSION_KEY = 'monbis_tv_access';

function getMonbisTvSession(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) return null;

    $tvSession = $_SESSION[MONBIS_TV_SESSION_KEY] ?? null;
    if (!is_array($tvSession)
        || !in_array(($tvSession['scope'] ?? ''), ['direksi', 'cabang'], true)
        || !is_numeric($tvSession['expires_at'] ?? null)
        || (int)$tvSession['expires_at'] <= time()
        || !is_array($tvSession['user'] ?? null)) {
        unset($_SESSION[MONBIS_TV_SESSION_KEY]);
        return null;
    }

    return $tvSession;
}

function getMonbisTvSessionUser(?string $scope = null): ?array
{
    $tvSession = getMonbisTvSession();
    if (!$tvSession || ($scope !== null && $tvSession['scope'] !== $scope)) return null;
    return $tvSession['user'];
}

function createMonbisTvSession(string $scope, array $user, int $ttlSeconds): int
{
    if (session_status() === PHP_SESSION_NONE && !session_start()) {
        throw new RuntimeException('Sesi TV tidak dapat dimulai.');
    }
    if (session_status() !== PHP_SESSION_ACTIVE
        || !in_array($scope, ['direksi', 'cabang'], true)
        || $ttlSeconds < 1) {
        throw new RuntimeException('Sesi TV tidak dapat dibuat.');
    }

    if ($scope === 'direksi') {
        $user = ['employee_id' => 'tv-direksi', 'full_name' => 'TV Direksi'];
    } else {
        $user = array_intersect_key($user, array_flip([
            'employee_id', 'id_peg', 'full_name', 'name', 'kode_kantor', 'kode',
            'kode_cabang', 'branch_code', 'office_code', 'job_position', 'unit_kerja',
        ]));
        if (empty($user['employee_id'])) {
            $user['employee_id'] = trim((string)($user['id_peg'] ?? $user['id'] ?? ''));
        }
        if (empty($user['employee_id']) || monbisTvBranchScope($user) === null) {
            throw new RuntimeException('Akun belum memiliki kode kantor TV Cabang yang valid.');
        }
    }

    if (!session_regenerate_id(true)) {
        throw new RuntimeException('ID sesi TV tidak dapat diperbarui.');
    }

    $expiresAt = time() + $ttlSeconds;
    $_SESSION[MONBIS_TV_SESSION_KEY] = [
        'scope' => $scope,
        'expires_at' => $expiresAt,
        'user' => $user,
    ];

    $cookie = session_get_cookie_params();
    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
    $secure = !empty($cookie['secure'])
        || $forwardedProto === 'https'
        || (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
    $sameSite = (string)($cookie['samesite'] ?? 'Lax');
    if (!in_array(strtolower($sameSite), ['lax', 'strict', 'none'], true)) $sameSite = 'Lax';
    $options = [
        'expires' => $expiresAt,
        'path' => (string)($cookie['path'] ?? '/') ?: '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => $sameSite,
    ];
    if (!empty($cookie['domain'])) $options['domain'] = $cookie['domain'];
    setcookie(session_name(), session_id(), $options);

    return $expiresAt;
}

function clearMonbisTvSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) unset($_SESSION[MONBIS_TV_SESSION_KEY]);
}

function monbisTvBranchScope(array $user): ?array
{
    foreach (['kode_kantor', 'kode', 'kode_cabang', 'branch_code', 'office_code'] as $key) {
        $raw = trim((string)($user[$key] ?? ''));
        if ($raw === '') continue;
        $digits = preg_replace('/\D/', '', $raw);
        if ($digits === '' || strlen($digits) > 3) continue;
        $number = (int)$digits;
        if ($number < 1 || $number > 28) continue;

        $branch = str_pad((string)$number, 3, '0', STR_PAD_LEFT);
        $korwil = $number <= 7 ? 'SEMARANG'
            : ($number <= 14 ? 'SOLO'
                : ($number <= 21 ? 'BANYUMAS' : 'PEKALONGAN'));
        return ['branch' => $branch, 'korwil' => $korwil];
    }

    return null;
}

function getMonbisTvSessionUserForApi(): ?array
{
    if (strtolower(trim((string)($_SERVER['HTTP_X_MONBIS_TV_REQUEST'] ?? ''))) !== 'kiosk') {
        return null;
    }

    $tvSession = getMonbisTvSession();
    if (!$tvSession) return null;

    $request = trim((string)($_GET['request'] ?? ''), '/');
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $allowedMethods = [
        'date' => ['GET'],
        'kode' => ['POST'],
        'dashboard' => ['POST'],
        'lapkeu' => ['POST'],
    ];
    $allowed = in_array($method, $allowedMethods[$request] ?? [], true);
    if ($tvSession['scope'] === 'cabang' && $request === 'auth/whoami' && $method === 'GET') {
        $allowed = true;
    }
    if (!$allowed) return null;

    $_SERVER['MONBIS_TV_SESSION_ACTIVE'] = '1';
    return $tvSession['user'];
}

function monbisTvPayloadTypeAllowed(string $endpoint, array $input): bool
{
    $types = [
        'kode' => ['kode_kantor'],
        'dashboard' => [
            'saldo_bank', 'realisasi_by_produk', 'test tren npl', 'test rr cabang',
            'test runoff korwil', 'test flow recovery npl', 'test top bottom npl',
            'test top realisasi', 'test delta npl', 'test perkembangan deposito',
            'test perkembangan tabungan', 'tv realisasi monitoring',
            'tren_portofolio_kredit', 'tren_runoff_realisasi',
        ],
        'lapkeu' => [
            'tv_makro_summary', 'tren_makro_mingguan', 'distribusi_makro_kantor',
            'financial_kpi', 'summary_perbandingan',
        ],
    ];
    return in_array((string)($input['type'] ?? ''), $types[$endpoint] ?? [], true);
}

function constrainMonbisTvCabangInput(array $input): array
{
    $tvSession = getMonbisTvSession();
    if (!$tvSession || $tvSession['scope'] !== 'cabang') return $input;

    $scope = monbisTvBranchScope($tvSession['user']);
    if (!$scope) return $input;

    $requestedOffice = strtoupper(trim((string)($input['korwil'] ?? $input['kode_kantor'] ?? '')));
    unset($input['korwil'], $input['kode_kantor']);
    if ($requestedOffice === $scope['korwil']) {
        $input['korwil'] = $scope['korwil'];
    } else {
        $input['kode_kantor'] = $scope['branch'];
    }

    return $input;
}
