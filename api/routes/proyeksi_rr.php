<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }

require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../helpers/rbac.php';
require_once __DIR__ . '/../controllers/ProyeksiRrController.php';

$authUser = is_array($user ?? null) ? $user : [];
$office = trim((string)($authUser['kode'] ?? ''));
if ($office === '' || $office === '*') $office = trim((string)($authUser['kode_kantor'] ?? ''));
if ($office === '' || $office === '*') {
    try {
        $profile = rbacFindUserByEmployeeId($pdo, $authUser['employee_id'] ?? '');
        $office = trim((string)($profile['kode'] ?? ''));
    } catch (Throwable $error) {
        error_log('[Proyeksi RR] office scope lookup failed: ' . $error->getMessage());
        sendResponse(503, 'Unit kerja akun tidak dapat diverifikasi. Silakan coba lagi.');
    }
}
if ($office === '' || $office === '*') sendResponse(403, 'Unit kerja akun tidak ditemukan.');
$office = str_pad($office, 3, '0', STR_PAD_LEFT);
if ($office !== '000' && !preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', $office)) {
    sendResponse(403, 'Akses kantor akun tidak dapat ditentukan.');
}

$controller = new ProyeksiRrController($pdo, $office);
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$action = (string)($segments[1] ?? 'metadata');

if ($method === 'GET' && $action === 'metadata') {
    $controller->getMetadata((string)($_GET['kode_cabang'] ?? 'ALL'));
}

if ($method === 'POST' && $action === 'snapshot') {
    $input = json_decode(file_get_contents('php://input'), true);
    $controller->getSnapshot(is_array($input) ? $input : []);
}

if ($method === 'POST' && $action === 'movements') {
    $input = json_decode(file_get_contents('php://input'), true);
    $controller->getMovementSummary(is_array($input) ? $input : []);
}

if ($method === 'POST' && $action === 'detail') {
    $input = json_decode(file_get_contents('php://input'), true);
    $controller->getMovementDetail(is_array($input) ? $input : []);
}

if (!in_array($action, ['metadata', 'snapshot', 'movements', 'detail'], true)) {
    sendResponse(404, 'Route proyeksi tidak ditemukan.');
}
sendResponse(405, 'Metode request tidak diizinkan.');
