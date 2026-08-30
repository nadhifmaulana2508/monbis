<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/JWT.php';

function getAppAuthToken(): string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('getallheaders')) {
        $headers = getallheaders();
        $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    if ($header === '') $header = (string)($_COOKIE['sso_token'] ?? '');
    return trim((string)preg_replace('/^Bearer\s+/i', '', trim($header)));
}

function ssoWhoami(string $token): ?array
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'report_dpk_sso_auth';
    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $token) . '.json';
    if (is_file($file) && time() - filemtime($file) <= 300) {
        $cached = json_decode((string)file_get_contents($file), true);
        if (is_array($cached) && !empty($cached['employee_id'])) return $cached;
    }
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $local = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');
    $base = getenv('SSO_API_BASE') ?: ($local ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id');
    $url = rtrim($base, '/') . '/api/auth/whoami';
    $body = false; $status = 0;
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>3, CURLOPT_TIMEOUT=>8,
            CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token, 'Accept: application/json'],
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http'=>[
            'method'=>'GET','timeout'=>8,'ignore_errors'=>true,
            'header'=>"Authorization: Bearer {$token}\r\nAccept: application/json\r\n",
        ]]);
        $body = @file_get_contents($url, false, $context);
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) $status = (int)$match[1];
    }
    if ($status !== 200 || !is_string($body)) return null;
    $json = json_decode($body, true);
    $user = is_array($json['data'] ?? null) ? $json['data'] : null;
    if (!$user || empty($user['employee_id'])) return null;
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    @file_put_contents($file, json_encode($user));
    return $user;
}

function requireAppAuth(): array
{
    $token = getAppAuthToken();
    if ($token === '') sendResponse(401, 'Token tidak ditemukan. Silakan login kembali.');
    $local = verifyJWT($token, $_ENV['JWT_SECRET'] ?? 'your-secret-key');
    if (is_array($local) && !empty($local['employee_id'])) return $local;
    $sso = ssoWhoami($token);
    if (!$sso) sendResponse(401, 'Token SSO tidak valid atau sudah kedaluwarsa.');
    return $sso;
}
