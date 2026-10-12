<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/ChatbotController.php';

$envFile = dirname(__DIR__) . '/.env';
$env = is_file($envFile) ? parse_ini_file($envFile, false, INI_SCANNER_RAW) : array();
if (!is_array($env)) {
    $env = array();
}

$controller = new ChatbotController($env);
$method = $_SERVER['REQUEST_METHOD'];
$request = trim($_GET['request'] ?? '', '/');
$segments = explode('/', $request);
$action = $segments[1] ?? 'ask';

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST ?: array();
}

switch ($action) {
    case 'ask':
        if ($method !== 'POST') {
            sendResponse(405, 'Gunakan POST.');
        }
        $controller->ask($input);
        break;

    default:
        sendResponse(404, 'Endpoint chatbot tidak ditemukan.');
        break;
}
