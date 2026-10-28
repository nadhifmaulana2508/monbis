<?php
declare(strict_types=1);
if (!defined('API_ROUTER_ENTRY')) { http_response_code(404); exit; }

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/RbacController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, 'Gunakan metode POST.');
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    $body = [];
}

$controller = new RbacController($pdo);
$type = strtolower(trim((string)($body['type'] ?? 'me')));
switch ($type) {
    case 'me':
        $controller->me();
        break;
    case 'bootstrap':
        $controller->bootstrap();
        break;
    case 'matrix':
        $controller->matrix($body);
        break;
    case 'save_role':
        $controller->saveRole($body);
        break;
    case 'save_users':
        $controller->saveUsers($body);
        break;
    case 'reset_users':
        $controller->resetUsers($body);
        break;
    default:
        sendResponse(400, 'Tipe RBAC tidak dikenali.');
}

