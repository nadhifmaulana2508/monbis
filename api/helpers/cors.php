<?php
declare(strict_types=1);

function applyApiCors(): void
{
    $origin = trim((string)($_SERVER['HTTP_ORIGIN'] ?? ''));
    if ($origin === '') return;

    $allowedRaw = getenv('CORS_ALLOWED_ORIGINS') ?: '';
    if ($allowedRaw === '') {
        $rootEnv = dirname(__DIR__, 2) . '/.env';
        if (is_file($rootEnv)) {
            $env = parse_ini_file($rootEnv, false, INI_SCANNER_RAW);
            if (is_array($env)) $allowedRaw = (string)($env['CORS_ALLOWED_ORIGINS'] ?? '');
        }
    }
    $allowed = array_values(array_filter(array_map('trim', preg_split('/[,;\r\n]+/', $allowedRaw) ?: [])));
    if (!in_array($origin, $allowed, true)) return;

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Vary: Origin');

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
