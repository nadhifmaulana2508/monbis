<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/ProspekSyncController.php';

function prospek_sync_find_project_root($startDir)
{
    $dir = $startDir;
    for ($i = 0; $i < 6; $i++) {
        if (is_file($dir . '/.env')) return $dir;
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }
    return dirname($startDir, 2);
}

function prospek_sync_env_value($env, $key, $default = null)
{
    if (array_key_exists($key, $env)) return $env[$key];
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

function prospek_sync_create_pdo()
{
    $root = prospek_sync_find_project_root(__DIR__);
    $envFile = $root . '/.env';
    if (!is_file($envFile)) {
        sendResponse(500, 'File .env tidak ditemukan untuk koneksi Prospek.');
    }

    $env = parse_ini_file($envFile, false, INI_SCANNER_RAW);
    if ($env === false) {
        sendResponse(500, 'Gagal membaca .env untuk koneksi Prospek.');
    }

    $host = (string)prospek_sync_env_value($env, 'DB_HOST1', '127.0.0.1');
    $user = (string)prospek_sync_env_value($env, 'DB_USER1', 'root');
    $pass = (string)prospek_sync_env_value($env, 'DB_PASS1', '');
    $name = (string)prospek_sync_env_value($env, 'DB_NAME1', '');
    $port = (int)prospek_sync_env_value($env, 'DB_PORT1', prospek_sync_env_value($env, 'DB_ROOT1', 3306));

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

$controller = new ProspekSyncController(prospek_sync_create_pdo());
$method = $_SERVER['REQUEST_METHOD'];
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!is_array($input)) {
    $input = $_POST ?: [];
}

switch ($method) {
    case 'POST':
        $type = strtolower(trim((string)($input['type'] ?? 'create_prospek_from_eprospek')));
        if ($type === 'create_prospek_from_eprospek' || $type === 'create') {
            $controller->createFromEprospek($input);
        }
        sendResponse(400, 'Type request tidak dikenali: ' . $type);
        break;

    default:
        sendResponse(405, 'Metode tidak diizinkan. Gunakan POST.');
        break;
}
?>
