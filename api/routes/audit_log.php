<?php
declare(strict_types=1);
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/AuditLogController.php';

header('Cache-Control: private, no-store');

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if (!in_array($method, ['GET', 'POST'], true)) {
    sendResponse(405, 'Gunakan metode GET atau POST.');
}

$controller = new AuditLogController($pdo);
if ($method === 'GET') {
    $controller->list($_GET);
}
if ($method === 'POST') {
    $body = json_decode((string)file_get_contents('php://input'), true);
    $controller->recordEvent(is_array($body) ? $body : []);
}
