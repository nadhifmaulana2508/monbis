<?php

// =============================================================
// ROUTER KHUSUS: REPAYMENT RATE (RR) - FIXED
// =============================================================

// 1. Load Dependencies (Gunakan require_once ke helper pusat)
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/RbbController.php';

// 2. Init Database (Cara Aman agar $pdo tidak null)
// Cek apakah function getPDO ada (dari config), atau ambil variabel $pdo langsung
$pdo = function_exists('getPDO') ? getPDO() : ($pdo ?? null);

if (!$pdo) {
    sendResponse(500, "Database Connection Failed (PDO is null)");
}

// 3. Init Controller
try {
    $controller = new RbbController($pdo);
} catch (Exception $e) {
    sendResponse(500, "Controller Init Failed: " . $e->getMessage());
}

// 4. Ambil Method & Input Body
$method = $_SERVER['REQUEST_METHOD'];
$raw    = file_get_contents("php://input");
$input  = json_decode($raw, true);

// Fallback: Jika input bukan JSON (misal form-data), ambil dari $_POST
if (!is_array($input)) {
    $input = $_POST ?: [];
}

$rbbAuth = static function (): array {
    if (defined('RBB_V2_PUBLIC') && RBB_V2_PUBLIC) {
        return ['employee_id' => '102-119'];
    }
    require_once __DIR__ . '/../helpers/sso_guard.php';
    return requireAppAuth();
};

// 5. Switch Logic
switch ($method) {
    case 'POST':
        // Validasi Parameter Type
        if (empty($input['type'])) {
            sendResponse(400, "Parameter 'type' diperlukan.");
        }

        // Normalisasi input (kecilkan huruf & hapus spasi)
        $type = strtolower(trim($input['type']));

        // --- A. REKAP UTAMA (RR) ---
        if ($type === 'aset_realisasi') {
            $controller->getAsetRealisasi($input);

        } elseif ($type === 'rbb_projection_data') {
            $controller->getRbbProjectionData($input, $rbbAuth());

        } elseif ($type === 'rbb_aba_data') {
            $controller->getRbbAbaData($input, $rbbAuth());

        } elseif ($type === 'rbb_aba_save') {
            $controller->saveRbbAba($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_data') {
            $controller->getRbbPlanningData($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_save') {
            $controller->saveRbbPlanning($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_submit') {
            $controller->submitRbbPlanning($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_reopen') {
            $controller->reopenRbbPlanning($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_approve') {
            $controller->approveRbbPlanning($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_coa') {
            $controller->getRbbPlanningCoa($input, $rbbAuth());

        } elseif ($type === 'rbb_planning_coa_save') {
            $controller->saveRbbPlanningCoa($input, $rbbAuth());

        // --- B. DETAIL DRILL DOWN ---
        } elseif ($type === 'aset_mom_yoy') {
            $controller->getAsetMomYoy($input);

        } elseif ($type === 'realisasi_rbb_bulan_berjalan') {
            $controller->getRealisasiRbbBulanBerjalan($input);

        } elseif ($type === 'lapkeu_rbb_vs_realisasi') {
            $controller->getLapkeuRbbVsRealisasi($input);

        } elseif ($type === 'ikhtisar_rbb') {
            $controller->getIkhtisarRbb($input);

        // --- ERROR: TYPE TIDAK DIKENAL ---
        } else {
            sendResponse(400, "Type request tidak dikenali: " . $type);
        }
        break;

    default:
        sendResponse(405, "Metode tidak diizinkan (Gunakan POST)");
        break;
}

