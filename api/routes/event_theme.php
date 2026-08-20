<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/EventThemeController.php';

$controller = new EventThemeController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$request = trim($_GET['request'] ?? '', '/');
$segments = explode('/', $request);
$action = $segments[1] ?? 'active';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST ?: array();
}
if ($method === 'GET') {
    $input = array_merge($_GET, $input);
}

switch ($action) {
    case 'active':
        if ($method !== 'GET' && $method !== 'POST') {
            sendResponse(405, 'Metode tidak diizinkan.');
        }
        $controller->active();
        break;

    case 'list':
        if ($method !== 'POST') {
            sendResponse(405, 'Gunakan POST.');
        }
        $controller->listing($input);
        break;

    case 'save':
        if ($method !== 'POST') {
            sendResponse(405, 'Gunakan POST.');
        }
        $controller->save($input);
        break;

    case 'delete':
        if ($method !== 'POST') {
            sendResponse(405, 'Gunakan POST.');
        }
        $controller->delete($input);
        break;

    default:
        sendResponse(404, 'Endpoint event theme tidak ditemukan.');
        break;
}
