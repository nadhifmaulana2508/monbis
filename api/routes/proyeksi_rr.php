<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }

require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/ProyeksiRrController.php';

$controller = new ProyeksiRrController($pdo);
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
