<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * =========================
 * BASE APP (AUTO)
 * =========================
 * Local      : http://localhost/report-dpk/
 * Development: https://dev.monbis.bkkjateng.co.id/
 * Production : https://monbis.bkkjateng.co.id/
 *
 * URL dibentuk dari host aktif agar source code yang sama dapat dipakai
 * di ketiga environment tanpa mengganti URL secara manual.
 */
$httpHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
$hostOnly = preg_replace('/:\d+$/', '', $httpHost) ?: $httpHost;
$forwardedProto = strtolower(trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
$requestIsHttps = !empty($_SERVER['HTTPS'])
    && strtolower((string) $_SERVER['HTTPS']) !== 'off';

// Gunakan if/elseif agar tetap kompatibel dengan PHP 7.x di server Dev.
if (in_array($hostOnly, ['localhost', '127.0.0.1', '::1'], true)) {
    $knownEnvironment = 'local';
} elseif ($hostOnly === 'dev.monbis.bkkjateng.co.id') {
    $knownEnvironment = 'development';
} elseif ($hostOnly === 'monbis.bkkjateng.co.id') {
    $knownEnvironment = 'production';
} else {
    $knownEnvironment = 'custom';
}

$scheme = in_array($forwardedProto, ['http', 'https'], true)
    ? $forwardedProto
    : ($requestIsHttps || (int) ($_SERVER['SERVER_PORT'] ?? 80) === 443 ? 'https' : 'http');

// Domain Dev dan Production selalu dipublikasikan melalui HTTPS.
if (in_array($knownEnvironment, ['development', 'production'], true)) {
    $scheme = 'https';
}
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$appBasePath = str_replace('\\', '/', dirname($scriptName));
if ($appBasePath === '/' || $appBasePath === '.') {
    $appBasePath = '';
} else {
    $appBasePath = rtrim($appBasePath, '/');
}

define(
    'BASE_APP',
    $scheme . '://' . $httpHost . $appBasePath
);
define('APP_ENV', $knownEnvironment);
define('APP_HOST', $hostOnly);
define('APP_SCHEME', $scheme);

// =========================
// AMBIL URL DARI REWRITE
// =========================
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

// =========================
// JANGAN LEWATKAN API KE ROUTER HALAMAN
// =========================
if (strpos($url, 'api/') === 0) {
    $apiPath = __DIR__ . '/' . $url . '.php';

    if (is_file($apiPath)) {
        require $apiPath;
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => false,
            'message' => 'API endpoint not found'
        ]);
    }
    exit;
}

// V2 memakai router sendiri, tetapi pada beberapa deployment aaPanel
// URL bersih tetap diteruskan ke router legacy ini. Teruskan prefix /v-2
// sebelum layout v1 sempat dirender agar URL tetap bersih di browser.
if (preg_match('#^v-2(?:/(.*))?$#i', $url, $v2Match)) {
    $v2Route = trim((string)($v2Match[1] ?? ''), '/');
    $requestPath = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '');
    $v2Marker = '/v-2';
    $v2Position = stripos($requestPath, $v2Marker);
    $v2Base = $v2Position === false
        ? '/v-2'
        : substr($requestPath, 0, $v2Position + strlen($v2Marker));
    $v2Base = rtrim(str_replace('\\', '/', $v2Base), '/');
    $_SERVER['SCRIPT_NAME'] = $v2Base . '/index.php';
    $_GET['v2_route'] = $v2Route;
    require __DIR__ . '/v-2/index.php';
    exit;
}

// =========================
// CEK STATUS LOGIN (VIA COOKIE SSO)
// =========================
// Sekarang kita cek pakai Cookie sso_token, bukan $_SESSION lagi
$isLoggedIn = isset($_COOKIE['sso_token']) && !empty($_COOKIE['sso_token']);
$rbacInitialUser = null;
$rbacInitialAccess = null;

// =========================
// ROUTING DEFAULT
// =========================
if ($url === '') {
    $url = $isLoggedIn ? 'dashboard' : 'login';
}

// page / param
[$page, $param] = array_pad(explode('/', $url, 2), 2, null);

// =========================
// PROTEKSI HALAMAN (AUTH GUARD)
// =========================
// 1. Kalau belum login tapi maksa buka halaman selain login dan atv_direksi, lempar ke login!
// (atv_direksi di-whitelist agar bisa diakses langsung lewat URL)
if (!$isLoggedIn && $page !== 'login' && $page !== 'tv') {
    $loginUrl = BASE_APP . "/login";
    if ($page === 'tv_cabang') {
        $loginUrl .= '?next=tv_cabang';
    }
    header("Location: " . $loginUrl);
    exit;
}

// 2. Kalau SUDAH login tapi malah buka halaman login, paksa masuk ke dashboard!
if ($isLoggedIn && $page === 'login') {
    header("Location: " . BASE_APP . "/dashboard");
    exit;
}

// Guard halaman RBAC secara server-side. Menu yang disembunyikan di browser
// tetap tidak dapat dibuka langsung tanpa hak akses matrix.
if ($isLoggedIn && !in_array($page, ['login', 'tv', 'tv_cabang'], true)) {
    try {
        require_once __DIR__ . '/api/controllers/config/database.php';
        require_once __DIR__ . '/api/helpers/rbac.php';
        $rbacInitialUser = rbacUserFromCookie($pdo);
        if ($rbacInitialUser) {
            $rbacInitialAccess = rbacEffectivePermissions($pdo, $rbacInitialUser);
            $rbacPageAccess = $page === 'setting_rbac'
                ? rbacCanManageMatrix($pdo, $rbacInitialUser)
                : rbacIsAdmin($rbacInitialUser)
                    || !empty($rbacInitialAccess['permissions']['page:' . trim($page)]['can_view']);
        } else {
            $rbacPageAccess = null;
        }
        if ($rbacPageAccess === false) {
            http_response_code(403);
            echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Akses ditolak</title><style>body{margin:0;display:grid;place-items:center;min-height:100vh;background:#f5f8fb;color:#17324d;font:15px Arial,sans-serif}.box{width:min(440px,calc(100% - 32px));padding:28px;border:1px solid #dce6ed;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(23,50,77,.08)}h1{margin:0 0 8px;font-size:22px}p{margin:0 0 20px;color:#6e8294;line-height:1.5}a{display:inline-flex;align-items:center;min-height:40px;padding:0 14px;border-radius:8px;background:#1565d8;color:#fff;text-decoration:none;font-weight:700}</style></head><body><main class="box"><h1>Akses belum diberikan</h1><p>Halaman ini belum termasuk matrix akses jabatan, divisi, atau override pegawai Anda.</p><a href="' . htmlspecialchars(BASE_APP . '/dashboard', ENT_QUOTES, 'UTF-8') . '">Kembali ke dashboard</a></main></body></html>';
            exit;
        }
    } catch (Throwable $rbacError) {
        // Jika token SSO berasal dari deployment yang belum memakai secret
        // lokal, middleware frontend tetap dapat memuat hak akses melalui API.
    }
}

$baseDir = __DIR__;

// =========================
// HEADER
// =========================
include $baseDir . "/views/header.php";

// =========================
// LOAD NAVBAR
// =========================
// login dan atv_direksi tidak pakai navbar (tampilan full screen)
if ($page !== 'login' && $page !== 'tv' && $page !== 'tv_cabang') {
    include $baseDir . "/views/navbar.php";
}

// =========================
// LOAD PAGE
// =========================
$path = $baseDir . "/pages/{$page}.php";

if (is_file($path)) {
    if ($param !== null) {
        $_GET['id'] = $param;
    }
    include $path;
} else {
    http_response_code(404);
    echo "<h1>404 - Halaman tidak ditemukan</h1>";
}

// =========================
// FOOTER
// =========================
include $baseDir . "/views/script.php";
include $baseDir . "/views/footer.php";
