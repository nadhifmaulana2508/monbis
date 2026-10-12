<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/PipelaneMonitoringKreditController.php';

$pdoDpk = isset($pdo) ? $pdo : null;

if (!$pdoDpk) {
    sendResponse(500, 'Database DPK gagal terkoneksi.');
}

function pmk_find_project_root($startDir)
{
    $dir = $startDir;
    for ($i = 0; $i < 6; $i++) {
        if (is_file($dir . '/.env')) {
            return $dir;
        }
        $parent = dirname($dir);
        if ($parent === $dir) {
            break;
        }
        $dir = $parent;
    }
    return dirname($startDir, 2);
}

function pmk_env_value($env, $key, $default = null)
{
    if (array_key_exists($key, $env)) {
        return $env[$key];
    }
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    return $default;
}

function pmk_create_prospek_pdo()
{
    $root = pmk_find_project_root(__DIR__);
    $envFile = $root . '/.env';
    if (!is_file($envFile)) {
        sendResponse(500, 'File .env tidak ditemukan untuk koneksi Prospek.');
    }

    $env = parse_ini_file($envFile, false, INI_SCANNER_RAW);
    if ($env === false) {
        sendResponse(500, 'Gagal membaca .env untuk koneksi Prospek.');
    }

    $host = (string)pmk_env_value($env, 'DB_HOST1', '127.0.0.1');
    $user = (string)pmk_env_value($env, 'DB_USER1', 'root');
    $pass = (string)pmk_env_value($env, 'DB_PASS1', '');
    $name = (string)pmk_env_value($env, 'DB_NAME1', '');
    $port = (int)pmk_env_value($env, 'DB_PORT1', pmk_env_value($env, 'DB_ROOT1', 3306));

    if ($name === '') {
        sendResponse(500, 'DB_NAME1 untuk database Prospek belum diisi.');
    }

    try {
        return new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        sendResponse(500, 'Koneksi database Prospek gagal: ' . $e->getMessage());
    }
}

$pdoProspek = pmk_create_prospek_pdo();

try {
    $controller = new PipelaneMonitoringKreditController($pdoDpk, $pdoProspek);
} catch (Exception $e) {
    sendResponse(500, 'Controller Init Failed: ' . $e->getMessage());
}

$method = $_SERVER['REQUEST_METHOD'];
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!is_array($input)) {
    $input = $_POST ?: [];
}

switch ($method) {
    case 'POST':
        if (empty($input['type'])) {
            sendResponse(400, "Parameter 'type' diperlukan.");
        }

        $type = strtolower(trim($input['type']));

        if ($type === 'ensure_tables') {
            $controller->ensureTables();
        } elseif ($type === 'options') {
            $controller->getOptions();
        } elseif ($type === 'dashboard') {
            $controller->getDashboard($input);
        } elseif ($type === 'daftar_pipeline') {
            $controller->getDaftarPipeline($input);
        } elseif ($type === 'monitoring_mingguan') {
            $controller->getMonitoringMingguan($input);
        } elseif ($type === 'save_monitoring') {
            $controller->saveMonitoring($input);
        } elseif ($type === 'history') {
            $controller->getHistory($input);
        } else {
            sendResponse(400, 'Type request tidak dikenali: ' . $type);
        }
        break;

    default:
        sendResponse(405, 'Metode tidak diizinkan. Gunakan POST.');
        break;
}
?>
