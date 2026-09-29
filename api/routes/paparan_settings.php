<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/sso_guard.php';
require_once __DIR__ . '/../controllers/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, 'Gunakan metode POST.');
}

$user = requireAppAuth();
$employeeId = trim((string)($user['employee_id'] ?? ''));
if ($employeeId === '') {
    sendResponse(401, 'Identitas pengguna tidak ditemukan. Silakan login kembali.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];
$type = strtolower(trim((string)($input['type'] ?? '')));

try {
    // Bootstrap tabel kecil ini agar fitur langsung bisa dipakai di instalasi baru.
    $pdo->exec("CREATE TABLE IF NOT EXISTS paparan_rbb_dashboard_preferences (
        employee_id VARCHAR(100) NOT NULL,
        preferences_json LONGTEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (employee_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if ($type === 'get') {
        $stmt = $pdo->prepare('SELECT preferences_json FROM paparan_rbb_dashboard_preferences WHERE employee_id = ? LIMIT 1');
        $stmt->execute([$employeeId]);
        $raw = $stmt->fetchColumn();
        $preferences = is_string($raw) ? json_decode($raw, true) : null;
        sendResponse(200, 'Preferensi dashboard dimuat.', [
            'preferences' => is_array($preferences) ? $preferences : null,
        ]);
    }

    if ($type !== 'save') {
        sendResponse(400, 'Type pengaturan tidak dikenali.');
    }

    $preferences = $input['preferences'] ?? null;
    if (!is_array($preferences)) sendResponse(422, 'Format pengaturan tidak valid.');

    $allowedWidgets = [
        'kpis', 'trend', 'rbb', 'ratios', 'risk', 'kolektibilitas', 'risk_summary',
        'alerts', 'map', 'ranking', 'compliance', 'members', 'notary',
    ];
    $allowedKorwils = ['korwil_semarang', 'korwil_solo', 'korwil_banyumas', 'korwil_pekalongan'];
    $tabs = $preferences['tabs'] ?? null;
    $widgets = $preferences['widgets'] ?? null;
    $fontScale = filter_var($preferences['fontScale'] ?? 1.15, FILTER_VALIDATE_FLOAT);
    if ($fontScale === false || $fontScale < 0.85 || $fontScale > 1.40) {
        sendResponse(422, 'Ukuran teks dashboard berada di luar rentang yang diizinkan.');
    }
    if (!is_array($tabs) || !is_array($widgets) || count($tabs) > 100 || count($widgets) > count($allowedWidgets)) {
        sendResponse(422, 'Pilihan wilayah atau panel tidak valid.');
    }

    $cleanTabs = [];
    foreach ($tabs as $tab) {
        if (!is_string($tab)) continue;
        $tab = trim($tab);
        if ($tab === 'kinerja_pusat' || in_array($tab, $allowedKorwils, true) || preg_match('/^cabang_[0-9]{3,6}$/', $tab)) {
            $cleanTabs[] = $tab;
        }
    }
    $cleanWidgets = [];
    foreach ($widgets as $widget) {
        if (is_string($widget) && in_array($widget, $allowedWidgets, true)) $cleanWidgets[] = $widget;
    }
    $cleanTabs = array_values(array_unique($cleanTabs));
    $cleanWidgets = array_values(array_unique($cleanWidgets));
    if (!$cleanTabs || !$cleanWidgets) sendResponse(422, 'Pilih minimal satu wilayah dan satu panel dashboard.');

    $json = json_encode(['tabs' => $cleanTabs, 'widgets' => $cleanWidgets, 'fontScale' => (float)$fontScale], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json) || strlen($json) > 16000) sendResponse(422, 'Ukuran pengaturan terlalu besar.');

    $stmt = $pdo->prepare('INSERT INTO paparan_rbb_dashboard_preferences (employee_id, preferences_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE preferences_json = VALUES(preferences_json)');
    $stmt->execute([$employeeId, $json]);
    sendResponse(200, 'Pengaturan dashboard tersimpan pada akun Anda.', ['preferences' => ['tabs' => $cleanTabs, 'widgets' => $cleanWidgets, 'fontScale' => (float)$fontScale]]);
} catch (Throwable $error) {
    error_log('Paparan dashboard settings error: ' . $error->getMessage());
    sendResponse(500, 'Pengaturan akun tidak dapat diakses. Pengaturan lokal tetap tersedia.');
}
