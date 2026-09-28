<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database_simpeg.php';
require_once __DIR__ . '/../controllers/AnggotaController.php';

$controller = new AnggotaController($pdoSimpeg);
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method !== 'POST') {
    sendResponse(405, 'Metode tidak diizinkan');
}

if (!is_array($input)) {
    $input = [];
}

$type = strtolower(trim((string)($input['type'] ?? '')));
if ($type === 'rekap_anggota') {
    $controller->getRekapAnggota($input);
}

sendResponse(400, "Type tidak dikenali");
