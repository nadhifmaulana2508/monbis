<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/sso_guard.php';
require_once __DIR__ . '/../helpers/sso_config.php';
require_once __DIR__ . '/../helpers/password_policy.php';
passwordPolicyEnsureSession();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$request = trim((string)($_GET['request'] ?? ''), '/');
$endpoint = explode('/', $request)[1] ?? '';
header('Cache-Control: no-store, private');

function logSsoAuthForwardFailure(string $failure, array $context): void
{
    $parts = parse_url((string)($context['url'] ?? '')) ?: [];
    $scheme = strtolower((string)($parts['scheme'] ?? 'https'));
    $host = preg_replace('/[^a-zA-Z0-9.\-:\[\]]/', '', (string)($parts['host'] ?? 'unknown')) ?: 'unknown';
    $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
    $path = (string)($parts['path'] ?? '/');
    $path = preg_replace('/[^a-zA-Z0-9._\/\-]/', '', $path) ?: '/';
    $target = $scheme . '://' . $host . $port . $path;
    $resolve = !empty($context['resolve']) ? implode(',', $context['resolve']) : 'dns';
    $resolve = preg_replace('/[^a-zA-Z0-9.,:\-\[\]]/', '', $resolve) ?: 'invalid';
    $error = preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string)($context['transport_error'] ?? ''));
    $error = substr((string)$error, 0, 400);
    $contentType = preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string)($context['content_type'] ?? 'unknown'));

    error_log(sprintf(
        '[Monbis SSO proxy] request_id=%s endpoint=%s failure=%s target=%s base_source=%s connect_ip_source=%s resolve=%s app_code=%s app_token_configured=%s http_status=%d upstream_status=%d transport=%s transport_errno=%d transport_error=%s content_type=%s',
        (string)($context['request_id'] ?? 'unknown'),
        (string)($context['endpoint'] ?? 'unknown'),
        $failure,
        $target,
        (string)($context['base_source'] ?? 'unknown'),
        (string)($context['connect_ip_source'] ?? 'unknown'),
        $resolve,
        getSsoAppCode(),
        !empty($context['app_headers']) ? 'yes' : 'no',
        (int)($context['http_status'] ?? 0),
        (int)($context['upstream_status'] ?? 0),
        (string)($context['transport'] ?? 'unknown'),
        (int)($context['transport_errno'] ?? 0),
        $error !== '' ? $error : 'none',
        $contentType !== '' ? $contentType : 'unknown'
    ));
}

function forwardSsoAuthPost(string $endpoint, array $input, string $token = ''): void
{
    $requestId = bin2hex(random_bytes(8));
    header('X-Request-ID: ' . $requestId);

    $baseSource = null;
    $base = getSsoApiBase($baseSource);
    $connectIpSource = null;
    $resolveEntries = getSsoCurlResolveEntries($connectIpSource);
    $url = rtrim($base, '/') . '/api/auth/' . $endpoint;
    $appHeaders = getSsoAppHeaders();
    $context = [
        'request_id' => $requestId,
        'endpoint' => $endpoint,
        'url' => $url,
        'base_source' => $baseSource,
        'connect_ip_source' => $connectIpSource,
        'resolve' => $resolveEntries,
        'app_headers' => (bool)$appHeaders,
    ];
    if (!$appHeaders) {
        logSsoAuthForwardFailure('app_token_missing', $context);
        sendResponse(503, 'Token aplikasi SSO belum dikonfigurasi di api/.env.');
    }

    $headers = array_merge($appHeaders, [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Request-ID: ' . $requestId,
    ]);
    if ($token !== '') $headers[] = 'Authorization: Bearer ' . $token;
    $payload = json_encode($input);
    $body = false;
    $status = 0;
    $transport = 'stream';
    $transportErrorNumber = 0;
    $transportErrorMessage = '';
    $contentType = 'unknown';
    if (function_exists('curl_init')) {
        $transport = 'curl';
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_RESOLVE => $resolveEntries,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $transportErrorNumber = curl_errno($curl);
        $transportErrorMessage = curl_error($curl);
        $contentType = (string)(curl_getinfo($curl, CURLINFO_CONTENT_TYPE) ?: 'unknown');
        curl_close($curl);
    } else {
        $result = ssoStreamRequest($url, 'POST', $headers, (string)$payload, 15);
        $body = $result['body'];
        $status = $result['status'];
        $transportErrorMessage = $result['error'];
    }
    $context['http_status'] = $status;
    $context['transport'] = $transport;
    $context['transport_errno'] = $transportErrorNumber;
    $context['transport_error'] = $transportErrorMessage;
    $context['content_type'] = $contentType;

    if (!is_string($body) || $status < 200 || $status >= 600) {
        logSsoAuthForwardFailure('transport_failure', $context);
        sendResponse(502, 'Layanan autentikasi SSO sedang tidak dapat digunakan.');
    }

    $responsePayload = json_decode($body, true);
    if (!is_array($responsePayload)) {
        $jsonStart = strpos($body, '{');
        if ($jsonStart !== false) {
            $responsePayload = json_decode(substr($body, $jsonStart), true);
        }
    }
    if (!is_array($responsePayload)) {
        $failure = in_array($status, [401, 403], true)
            ? 'app_auth_rejected_invalid_response'
            : ($status >= 500 ? 'upstream_server_error_invalid_response' : 'invalid_upstream_response');
        logSsoAuthForwardFailure($failure, $context);
        sendResponse(502, 'Layanan autentikasi SSO memberikan respons yang tidak valid. Silakan coba lagi.');
    }

    $upstreamStatus = (int)($responsePayload['status'] ?? 0);
    $context['upstream_status'] = $upstreamStatus;
    $reportedStatus = $upstreamStatus > 0 ? $upstreamStatus : $status;
    if (in_array($reportedStatus, [401, 403], true)) {
        logSsoAuthForwardFailure('app_auth_rejected', $context);
    } elseif (max($status, $upstreamStatus) >= 500) {
        logSsoAuthForwardFailure('upstream_server_error', $context);
    }
    if ($endpoint === 'forgot-password' && max($status, $upstreamStatus) >= 500) {
        $responsePayload['message'] = 'Pengiriman OTP sedang bermasalah di server. Silakan coba lagi nanti.';
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($responsePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function monbisLogPasswordChangeRequest(PDO $pdo, array $user, int $httpStatus): void
{
    try {
        $ipAddress = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) $ipAddress = null;
        $auditStmt = $pdo->prepare("INSERT INTO rbac_menu_access_log
            (employee_id, employee_name, feature_key, request_path, request_method, http_status, ip_address, action_type, action_label, access_result, accessed_at)
            VALUES (:employee_id, :employee_name, 'page:profile', '/api/auth/change-password [page=profile]', 'POST', :http_status, :ip_address, 'detail', 'Ganti password', 'allowed', :accessed_at)");
        $auditStmt->execute([
            'employee_id' => (string)($user['employee_id'] ?? ''),
            'employee_name' => trim((string)($user['full_name'] ?? $user['name'] ?? '')) ?: null,
            'http_status' => max(100, min(599, $httpStatus)),
            'ip_address' => $ipAddress,
            'accessed_at' => gmdate('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $auditError) {
        error_log('Password change audit logging failed.');
    }
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
        $result = ssoStreamRequest(
            $loginUrl,
            'POST',
            array_merge($appHeaders, ['Content-Type: application/json', 'Accept: application/json']),
            (string)$payload,
            15
        );
        $body = $result['body'];
        $status = $result['status'];
        $curlErrorMessage = $result['error'];
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

    try {
        if (hash_equals(MONBIS_DEFAULT_PASSWORD, $password) || !empty($user['must_change_password'])) {
            passwordPolicyMarkRequired((string)$user['employee_id']);
        } else {
            passwordPolicyClearRequired();
        }
        $user['must_change_password'] = passwordPolicyIsRequired((string)$user['employee_id']);
    } catch (Throwable $passwordPolicyError) {
        error_log('Password change session state could not be saved.');
        sendResponse(503, 'Status keamanan sesi tidak dapat dipastikan. Silakan coba lagi.');
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
    passwordPolicyClearRequired();
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
    $user = requireAppAuth();
    $token = trim((string)($_COOKIE['monbis_session'] ?? ''));
    if ($token === '') $token = getAppAuthToken();
    $input = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($input)) sendResponse(400, 'Data permintaan tidak valid.');

    try {
        $passwordChangeRequired = passwordPolicyIsRequired((string)($user['employee_id'] ?? ''));
    } catch (Throwable $passwordPolicyError) {
        error_log('Password change session validation failed for auth update.');
        sendResponse(503, 'Validasi keamanan akun sedang tidak tersedia. Silakan coba lagi.');
    }
    if ($endpoint === 'change-password') {
        require_once __DIR__ . '/../controllers/config/database.php';
        require_once __DIR__ . '/../helpers/rbac.php';
        rbacEnsureSchema($pdo);
    }
    if ($passwordChangeRequired && $endpoint !== 'change-password') {
        sendResponse(403, 'Ganti password wajib diselesaikan sebelum memperbarui profil.');
    }
    if ($passwordChangeRequired && hash_equals(MONBIS_DEFAULT_PASSWORD, (string)($input['new_password'] ?? ''))) {
        monbisLogPasswordChangeRequest($pdo, $user, 422);
        sendResponse(422, 'Password baru harus berbeda dari password sementara.');
    }

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
        $result = ssoStreamRequest(
            $url,
            'POST',
            array_merge(getSsoAppHeaders(), [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
            ]),
            (string)$payload,
            15
        );
        $body = $result['body'];
        $status = $result['status'];
    }

    if (!is_string($body) || $status < 200 || $status >= 600) {
        if ($endpoint === 'change-password') {
            monbisLogPasswordChangeRequest($pdo, $user, 502);
        }
        sendResponse(502, 'Layanan autentikasi SSO sedang tidak dapat digunakan.');
    }
    $responseData = json_decode($body, true);
    $upstreamStatus = (int)($responseData['status'] ?? $status);
    if ($endpoint === 'change-password') {
        $passwordChangeSucceeded = $status >= 200 && $status < 300 && $upstreamStatus >= 200 && $upstreamStatus < 300;
        if ($passwordChangeSucceeded) {
            passwordPolicyClearRequired((string)($user['employee_id'] ?? ''));
        }
        monbisLogPasswordChangeRequest($pdo, $user, $status);
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
