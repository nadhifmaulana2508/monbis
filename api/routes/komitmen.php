<?php
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }

require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../helpers/rbac.php';
require_once __DIR__ . '/../controllers/KomitmenController.php';

try {
    $actor = rbacRequireUser($pdo);
    $access = rbacEffectivePermissions($pdo, $actor);
    $permissions = $access['permissions'] ?? [];
} catch (Throwable $error) {
    error_log('[Komitmen] permission lookup failed: ' . $error->getMessage());
    sendResponse(503, 'Hak akses komitmen tidak dapat diverifikasi. Coba lagi.');
}

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$action = (string)($segments[1] ?? 'metadata');
$canViewCalculator = !empty($permissions['page:komitmen_kalkulator']['can_view']);
$canCreateCalculator = !empty($permissions['page:komitmen_kalkulator']['can_create']);
$canViewRecap = !empty($permissions['page:komitmen_rekap']['can_view']);

$authUser = is_array($user ?? null) ? $user : [];
$divisionCycleManager = KomitmenController::isCycleManagementDivision($actor);
$office = trim((string)($authUser['kode'] ?? ''));
if ($office === '' || $office === '*') $office = trim((string)($authUser['kode_kantor'] ?? ''));
if ($office === '' || $office === '*') $office = rbacUserOffice($actor);
if ($office === '' || $office === '*') {
    if ($divisionCycleManager) $office = '000';
    else sendResponse(403, 'Unit kerja akun tidak dapat ditentukan.');
}
$office = str_pad(strtoupper(trim($office)), 3, '0', STR_PAD_LEFT);
$canManageCycles = KomitmenController::canManageCyclesFor($actor, $office);
if ($office !== '000' && !preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', $office)) {
    sendResponse(403, 'Akses kantor akun tidak dapat ditentukan.');
}

if ($method === 'GET' && in_array($action, ['metadata', 'position'], true) && !$canViewCalculator) {
    sendResponse(403, 'Anda tidak memiliki akses ke Kalkulator Komitmen.');
}
if ($method === 'POST' && $action === 'save' && (!$canViewCalculator || !$canCreateCalculator)) {
    sendResponse(403, 'Anda tidak memiliki hak untuk menyimpan komitmen.');
}
if ($method === 'POST' && in_array($action, ['cycle-open', 'cycle-close'], true) && (!$canViewRecap || !$canManageCycles)) {
    sendResponse(403, 'Hanya Kantor Pusat dan divisi yang berwenang dapat membuka atau menutup siklus komitmen.');
}
if ($method === 'GET' && $action === 'list' && !$canViewRecap) {
    sendResponse(403, 'Anda tidak memiliki akses ke Rekap Komitmen.');
}
if ($method === 'GET' && $action === 'detail' && !$canViewRecap && !$canViewCalculator) {
    sendResponse(403, 'Anda tidak memiliki akses untuk melihat detail komitmen.');
}

$controller = new KomitmenController($pdo, $office, $actor);
$input = null;
if ($method === 'POST') {
    $decoded = json_decode((string)file_get_contents('php://input'), true);
    $input = is_array($decoded) ? $decoded : [];
}
if ($method === 'GET' && $action === 'metadata') {
    $controller->getMetadata($_GET);
}
if ($method === 'GET' && $action === 'position') {
    $controller->getCyclePosition($_GET);
}
if ($method === 'GET' && $action === 'list') {
    $controller->getCycleList($_GET);
}
if ($method === 'GET' && $action === 'detail') {
    $controller->getDetail((int)($_GET['id'] ?? 0));
}
if ($method === 'POST' && $action === 'save') {
    $controller->saveCycle($input ?? [], $actor);
}
if ($method === 'POST' && $action === 'cycle-open') {
    $controller->openCycle($input ?? [], $actor);
}
if ($method === 'POST' && $action === 'cycle-close') {
    $controller->closeCycle($input ?? [], $actor);
}

if (!in_array($action, ['metadata', 'position', 'list', 'detail', 'save', 'cycle-open', 'cycle-close'], true)) {
    sendResponse(404, 'Route komitmen tidak ditemukan.');
}
sendResponse(405, 'Metode request tidak diizinkan.');
