<?php

require_once __DIR__ . '/../controllers/LapkeuController.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../helpers/response.php';

// Inisialisasi Controller
$controller = new LaporanKeuanganController($pdo);

// Ambil method HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Ambil input dari JSON Body (Trik Sakti Postman)
$input = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'POST':
        $type = $input['type'] ?? '';

        // ==========================================
        // ENDPOINT LAPORAN NERACA (1, 2, 3)
        // ==========================================
        if ($type === 'neraca pivot') {
            // Menampilkan matrix 000-028 + Konsolidasi
            $controller->apiGetNeraca($input);
        } 
        elseif ($type === 'default_acc_history_date') {
            $controller->apiGetDefaultAccHistoryDate();
        }
        elseif ($type === 'lap_neraca_actual') {
            $controller->apiGetLapNeracaActual($input);
        }
        elseif ($type === 'lap_laba_rugi_actual') {
            $controller->apiGetLapLabaRugiActual($input);
        }
        elseif ($type === 'rekap_lapkeu_actual') {
            $controller->apiGetRekapLapkeuActual($input);
        }
        
        // ==========================================
        // ENDPOINT LAPORAN LABA RUGI (4, 5)
        // ==========================================
        elseif ($type === 'laba rugi pivot') {
            // Menampilkan matrix 000-028 + Konsolidasi
            $controller->apiGetLabaRugi($input);
        }

        elseif ($type === 'test tren perkiraan') {
            // Menampilkan matrix 000-028 + Konsolidasi
            $controller->apiGetTrenPerkiraan($input);
        

        // 🔥 TAMBAHKAN INI BROKU 🔥
        } elseif ($type === 'tren_makro_mingguan') {
            $controller->apiGetTrenMakroMingguan($input);

        } elseif ($type === 'distribusi_makro_kantor') {
            $controller->apiGetDistribusiMakroKantor($input);

        } elseif ($type === 'list_coa') {
            $controller->apiGetCoaList();

        } elseif ($type === 'financial_kpi') {
            $controller->apiGetFinancialKPI($input);

        } elseif ($type === 'tv_makro_summary') {
            $controller->apiGetTvMakroSummary($input);

        } elseif ($type === 'summary_perbandingan') {
            $controller->GetSummaryPerbandingan($input);

        // ==========================================
        // ENDPOINT TESTING (Kalo lu butuh breakdown per kantor saja)
        // ==========================================
        }elseif ($type === 'neraca detail kantor' || $type === 'laba rugi detail kantor') {
            // Panggil fungsi detail, bukan matrix
            $controller->getReportDetail($input);
        }

        else {
            sendResponse(400, "Type laporan salah atau tidak ditemukan, brotherkuuu");
        }
        break;

    default:
        sendResponse(405, "Method $method kagak boleh masuk sini!");
        break;
}

