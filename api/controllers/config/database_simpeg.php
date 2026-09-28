<?php
declare(strict_types=1);

require_once __DIR__ . '/../../helpers/response.php';

$projectRoot = dirname(__DIR__, 3);
$envFile = $projectRoot . '/api/.env';
$env = is_file($envFile) ? parse_ini_file($envFile, false, INI_SCANNER_RAW) : false;

if ($env === false) {
    sendResponse(500, 'Konfigurasi database Simpeg tidak dapat dibaca.');
}

$envValue = static function (string $key, $default = null) use ($env) {
    $systemValue = getenv($key);
    return $systemValue !== false ? $systemValue : ($env[$key] ?? $default);
};

$host = (string)$envValue('SIMPEG_DB_HOST', '127.0.0.1');
$user = (string)$envValue('SIMPEG_DB_USER', 'root');
$pass = (string)$envValue('SIMPEG_DB_PASS', '');
$name = (string)$envValue('SIMPEG_DB_NAME', '');
$port = (int)$envValue('SIMPEG_DB_PORT', 3306);

if ($name === '') {
    sendResponse(500, 'SIMPEG_DB_NAME belum diisi pada api/.env.');
}

try {
    $pdoSimpeg = new PDO(
        "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    sendResponse(500, 'Koneksi database Simpeg gagal: ' . $e->getMessage());
}
