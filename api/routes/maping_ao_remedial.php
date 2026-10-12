<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/MappingAoRemedialController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendResponse(405, 'Gunakan metode POST.');
$controller = new MappingAoRemedialController($pdo);
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = $_POST ?: [];
$user = is_array($input['_user'] ?? null) ? $input['_user'] : [];
if (empty($user['employee_id'])) sendResponse(401, 'Data pengguna belum tersinkron. Muat ulang halaman setelah login.');

switch (strtolower(trim((string)($input['type'] ?? 'bootstrap')))) {
    case 'bootstrap': $controller->bootstrap($input, $user); break;
    case 'list': $controller->list($input, $user); break;
    case 'ao_options': $controller->aoOptions($input, $user); break;
    case 'recap': $controller->recap($input, $user); break;
    case 'detail': $controller->detail($input, $user); break;
    case 'assign': $controller->assign($input, $user); break;
    default: sendResponse(400, 'Type tidak dikenali.');
}
