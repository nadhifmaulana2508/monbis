<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/sso_guard.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$request = trim((string)($_GET['request'] ?? ''), '/');
$endpoint = explode('/', $request)[1] ?? '';

if ($endpoint === 'login') {
    // Login lokal lama dinonaktifkan. Aplikasi menggunakan endpoint SSO resmi.
    sendResponse(410, 'Gunakan layanan autentikasi SSO MONBIS.');
}

if ($endpoint === 'whoami' && $method === 'GET') {
    $user = requireAppAuth();
    sendResponse(200, 'Data user berhasil diambil', $user);
}

sendResponse(404, 'Auth endpoint tidak ditemukan');
