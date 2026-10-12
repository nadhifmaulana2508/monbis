<?php

require_once __DIR__ . '/../controllers/KolekController.php';
require_once __DIR__ . '/../controllers/config/database.php';

$controller = new KolekController($pdo);

// Ambil method
$method = $_SERVER['REQUEST_METHOD'];

// POST body
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST':
        if ($input['type'] === 'kolek m1 and actual') {
            $controller->getRekapKolektabilitas($input);
        } elseif(strtolower((string)($input['type'] ?? '')) === 'migrasi kolek'){
            $controller->getMigrasiKolektabilitasV2($input);
        } elseif(strtolower((string)($input['type'] ?? '')) === 'migrasi payment projection'){
            $controller->getMigrasiPaymentProjection($input);
        } elseif($input['type'] === 'migrasi bucket'){
            $controller->migrasiBucketOsc($input);
        } elseif($input['type'] === 'detail debutir migrasi'){
            $controller->getMigrasiBucketDetail($input);
        } elseif($input['type'] === 'bucket osc'){
            $controller->getBucketOsc($input);
        } elseif(strtolower((string)($input['type'] ?? '')) === 'migrasi ckpn summary'){
            $controller->getMigrasiCkpnSummary($input);
        } elseif($input['type'] === 'bucket ckpn'){
            $controller->getBucketCkpn($input);

        } else {
            sendResponse(400, "Endpoit salah");
        }
        break;

    default:
        sendResponse(405, "Metode tidak diizinkan");
        break;
}

