<?php

require_once __DIR__ . '/../controllers/FlowParController.php';
require_once __DIR__ . '/../controllers/config/database.php';

$controller = new FlowParController($pdo);

// Ambil method
$method = $_SERVER['REQUEST_METHOD'];

// POST body
$input = json_decode(file_get_contents("php://input"), true);

// Basis nominal hanya boleh memakai kolom yang memang tersedia.
// Default sengaja SALDO BANK agar endpoint aman saat dipanggil tanpa filter.
if (is_array($input) && in_array($input['type'] ?? '', ['Flow Par', 'Flow Par Dashboard', 'Flow Par Dashboard Detail', 'KL Baru'], true)) {
    $modeHitung = strtolower(trim((string)($input['hitung_berdasarkan'] ?? 'saldo_bank')));
    $input['hitung_berdasarkan'] = $modeHitung === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
}

switch ($method) {
    case 'POST':
        if (!isset($input['type'])) {
            sendResponse(400, "Parameter 'type' diperlukan ('Flow Par', 'KL Baru', atau 'Last Created Nominatif')");
            exit;
        }

        if ($input['type'] === 'Flow Par') {
            $controller->getFlowPar($input);

        } elseif ($input['type'] === 'Flow Par Dashboard') {
            $controller->getFlowParDashboard($input);

        } elseif ($input['type'] === 'Flow Par Dashboard Detail') {
            $controller->getFlowParDashboardDetail($input);

        } elseif ($input['type'] === 'KL Baru') {
            if (!isset($input['kode_kantor'])) {
                sendResponse(400, "Parameter 'kode_kantor' wajib untuk type 'KL Baru'");
                exit;
            }
            $controller->getDebiturFlowPar($input);

        } elseif ($input['type'] === 'Last Created Nominatif') {
            $controller->getLastCreatedDate();
        } elseif ($input['type'] === '50 Besar') {
            $controller->getTop50FlowPar($input);
        } elseif ($input['type'] === 'Update KL Baru') {
            $controller->updateKomitmenKlBaru($input);
        } elseif ($input['type'] === 'detail debitur') {
            $controller->getDetailDebitur($input);
        } elseif ($input['type'] === 'cari debitur') {
            $controller->searchDebiturKredit($input);
        } else {
            sendResponse(400, "Type tidak dikenali. Gunakan 'Flow Par', 'KL Baru', atau 'Last Created Nominatif'");
        }
        break;

    default:
        sendResponse(405, "Metode tidak diizinkan");
        break;
}

