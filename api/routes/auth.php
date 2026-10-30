<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/sso_guard.php';
require_once __DIR__ . '/../helpers/sso_config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$request = trim((string)($_GET['request'] ?? ''), '/');
$endpoint = explode('/', $request)[1] ?? '';
header('Cache-Control: no-store, private');

function forwardSsoAuthPost(string $endpoint, array $input, string $token = ''): void
{
    $appHeaders = getSsoAppHeaders();
    if (!$appHeaders) sendResponse(503, 'Token aplikasi SSO belum dikonfigurasi di api/.env.');

    $url = rtrim(getSsoApiBase(), '/') . '/api/auth/' . $endpoint;
    $headers = array_merge($appHeaders, ['Content-Type: application/json', 'Accept: application/json']);
    if ($token !== '') $headers[] = 'Authorization: Bearer ' . $token;
    $payload = json_encode($input);
    $body = false;
    $status = 0;
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_RESOLVE => getSsoCurlResolveEntries(),
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'timeout' => 15,
            'ignore_errors' => true,
            'header' => implode("\r\n", $headers) . "\r\n",
            'content' => $payload,
        ]]);
        $body = @file_get_contents($url, false, $context);
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) {
            $status = (int)$match[1];
        }
    }

    if (!is_string($body) || $status < 200 || $status >= 600) {
        sendResponse(502, 'Layanan autentikasi SSO sedang tidak dapat digunakan.');
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo $body;
    exit;
}

if ($endpoint === 'login' && $method === 'POST') {
    $origin = trim((string)($_SERVER['HTTP_ORIGIN'] ?? ''));
    $originHost = strtolower((string)(parse_url($origin, PHP_URL_HOST) ?? ''));
    $requestHost = strtolower((string)preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    if ($originHost !== '' && !hash_equals($requestHost, $originHost)) {
        sendResponse(403, 'Asal permintaan login tidak diizinkan.');
    }

    $input = json_decode((string)file_get_contents('php://input'), true);
    $employeeId = trim((string)($input['id_peg'] ?? ''));
    $password = (string)($input['password'] ?? '');
    if ($employeeId === '' || $password === '') {
        sendResponse(422, 'ID pegawai dan password wajib diisi.');
    }
    $appHeaders = getSsoAppHeaders();
    if (!$appHeaders) sendResponse(503, 'Token aplikasi SSO belum dikonfigurasi di api/.env.');

    $loginUrl = rtrim(getSsoApiBase(), '/') . '/api/auth/login';
    $payload = json_encode(['id_peg' => $employeeId, 'password' => $password, 'app' => 'monbis']);
    $body = false;
    $status = 0;
    $curlErrorNumber = 0;
    $curlErrorMessage = '';
    if (function_exists('curl_init')) {
        $curl = curl_init($loginUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_RESOLVE => getSsoCurlResolveEntries(),
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array_merge($appHeaders, ['Content-Type: application/json', 'Accept: application/json']),
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNumber = curl_errno($curl);
        $curlErrorMessage = curl_error($curl);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'timeout' => 15,
            'ignore_errors' => true,
            'header' => implode("\r\n", array_merge($appHeaders, ['Content-Type: application/json', 'Accept: application/json'])) . "\r\n",
            'content' => $payload,
        ]]);
        $body = @file_get_contents($loginUrl, false, $context);
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) {
            $status = (int)$match[1];
        }
    }

    $loginData = is_string($body) ? json_decode($body, true) : null;
    $token = trim((string)($loginData['data']['token'] ?? ''));
    if ($status !== 200 || (int)($loginData['status'] ?? 0) !== 200 || $token === '') {
        error_log(sprintf(
            'Monbis SSO login failed: http=%d curl_errno=%d curl_error=%s response_status=%s token_present=%s',
            $status,
            $curlErrorNumber,
            $curlErrorMessage !== '' ? $curlErrorMessage : 'none',
            isset($loginData['status']) ? (string)$loginData['status'] : 'missing',
            $token !== '' ? 'yes' : 'no'
        ));
        $publicStatus = $status === 401 ? 401 : 502;
        sendResponse($publicStatus, $publicStatus === 401
            ? 'ID pegawai atau password tidak sesuai.'
            : 'Layanan autentikasi SSO sedang tidak dapat digunakan.');
    }

    $user = ssoWhoami($token);
    if (!is_array($user) || empty($user['employee_id'])) {
        sendResponse(502, 'Data pegawai dari SSO tidak dapat diverifikasi.');
    }

    $appBaseUrl = getCurrentAppBaseUrl();
    $appPath = (string)(parse_url($appBaseUrl, PHP_URL_PATH) ?? '/');
    $cookiePath = $appPath === '' ? '/' : rtrim($appPath, '/') . '/';
    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
    $secure = strtolower((string)parse_url($appBaseUrl, PHP_URL_SCHEME)) === 'https'
        || $forwardedProto === 'https'
        || (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
    setcookie('monbis_session', $token, [
        'expires' => time() + 7200,
        'path' => $cookiePath,
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    sendResponse(200, 'Login berhasil.', $user);
}

if ($endpoint === 'logout' && $method === 'POST') {
    $appBaseUrl = getCurrentAppBaseUrl();
    $appPath = (string)(parse_url($appBaseUrl, PHP_URL_PATH) ?? '/');
    $cookiePath = $appPath === '' ? '/' : rtrim($appPath, '/') . '/';
    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
    $secure = strtolower((string)parse_url($appBaseUrl, PHP_URL_SCHEME)) === 'https'
        || $forwardedProto === 'https'
        || (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
    $expiredAt = time() - 3600;
    $baseOptions = [
        'expires' => $expiredAt,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    setcookie('monbis_session', '', [
        'expires' => $expiredAt,
        'path' => $cookiePath,
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    if ($cookiePath !== '/') setcookie('monbis_session', '', $baseOptions);
    // Sesi versi lama memakai sso_token pada path root, termasuk cookie lintas subdomain.
    setcookie('sso_token', '', $baseOptions);
    $requestHost = strtolower((string)preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    if ($requestHost === 'bkkjateng.co.id' || str_ends_with($requestHost, '.bkkjateng.co.id')) {
        $domainOptions = $baseOptions;
        $domainOptions['domain'] = '.bkkjateng.co.id';
        setcookie('sso_token', '', $domainOptions);
    }
    sendResponse(200, 'Logout berhasil.');
}

if ($endpoint === 'keepalive' && $method === 'GET') {
    requireAppAuth();
    sendResponse(200, 'Sesi masih aktif.');
}

if (in_array($endpoint, ['forgot-password', 'verify-otp', 'reset-password'], true) && $method === 'POST') {
    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) sendResponse(400, 'Data permintaan tidak valid.');
    forwardSsoAuthPost($endpoint, $input);
}

if (in_array($endpoint, ['update-profile', 'change-password'], true) && $method === 'POST') {
    requireAppAuth();
    $token = trim((string)($_COOKIE['monbis_session'] ?? ''));
    if ($token === '') $token = getAppAuthToken();
    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) sendResponse(400, 'Data permintaan tidak valid.');

    $url = rtrim(getSsoApiBase(), '/') . '/api/auth/' . $endpoint;
    $payload = json_encode($input);
    $body = false;
    $status = 0;
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_RESOLVE => getSsoCurlResolveEntries(),
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array_merge(getSsoAppHeaders(), [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
            ]),
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'timeout' => 15,
            'ignore_errors' => true,
            'header' => implode("\r\n", array_merge(getSsoAppHeaders(), ["Content-Type: application/json", 'Accept: application/json', "Authorization: Bearer {$token}"])) . "\r\n",
            'content' => $payload,
        ]]);
        $body = @file_get_contents($url, false, $context);
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) {
            $status = (int)$match[1];
        }
    }

    if (!is_string($body) || $status < 200 || $status >= 600) {
        sendResponse(502, 'Layanan autentikasi SSO sedang tidak dapat digunakan.');
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo $body;
    exit;
}

if ($endpoint === 'whoami' && $method === 'GET') {
    $user = requireAppAuth();
    sendResponse(200, 'Data user berhasil diambil', $user);
}

sendResponse(404, 'Auth endpoint tidak ditemukan');
