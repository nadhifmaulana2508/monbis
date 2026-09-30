<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/RbbKinerjaReportController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, 'Gunakan metode POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];

if (strtolower(trim((string)($input['type'] ?? ''))) !== 'annual_report') {
    sendResponse(400, 'Type laporan tidak dikenali.');
}

$controller = new RbbKinerjaReportController($pdo);
$controller->getAnnualReport($input);
