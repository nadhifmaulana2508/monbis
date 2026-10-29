<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/api/helpers/sso_config.php';

/**
 * =========================
 * BASE APP (CONFIGURED PER ENVIRONMENT)
 * =========================
 * URL sumbernya api/.env; host request menentukan URL yang dipakai.
 */
$httpHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
$hostOnly = preg_replace('/:\d+$/', '', $httpHost) ?: $httpHost;
$appUrls = getAppEnvironmentUrls();
$knownEnvironment = 'custom';
$configuredBaseUrl = null;
$localHosts = ['localhost', '127.0.0.1', '::1'];
foreach ($appUrls as $environment => $configuredUrl) {
    $configuredHost = strtolower((string) (parse_url($configuredUrl, PHP_URL_HOST) ?? ''));
    $hostMatches = $configuredHost !== '' && $hostOnly === $configuredHost;
    if (!$hostMatches && $environment === 'local') {
        $hostMatches = in_array($hostOnly, $localHosts, true) && in_array($configuredHost, $localHosts, true);
    }
    if ($hostMatches) {
        $knownEnvironment = $environment;
        $configuredBaseUrl = $configuredUrl;
        break;
    }
}
if ($knownEnvironment === 'custom' && in_array($hostOnly, $localHosts, true)) {
    $knownEnvironment = 'local';
}
$forwardedProto = strtolower(trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
$requestIsHttps = !empty($_SERVER['HTTPS'])
    && strtolower((string) $_SERVER['HTTPS']) !== 'off';

$scheme = in_array($forwardedProto, ['http', 'https'], true)
    ? $forwardedProto
    : ($requestIsHttps || (int) ($_SERVER['SERVER_PORT'] ?? 80) === 443 ? 'https' : 'http');

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$appBasePath = str_replace('\\', '/', dirname($scriptName));
if ($appBasePath === '/' || $appBasePath === '.') {
    $appBasePath = '';
} else {
    $appBasePath = rtrim($appBasePath, '/');
}

$baseApp = $configuredBaseUrl ?: $scheme . '://' . $httpHost . $appBasePath;
$baseParts = parse_url($baseApp);
$scheme = strtolower((string) ($baseParts['scheme'] ?? $scheme));
define('BASE_APP', rtrim($baseApp, '/'));
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

// Validasi sesi browser di server; keberadaan cookie saja bukan bukti login.
$webAuthUser = null;
$webToken = trim((string) preg_replace('/^Bearer\s+/i', '', (string) ($_COOKIE['monbis_session'] ?? ($_COOKIE['sso_token'] ?? ''))));
if ($webToken !== '') {
    require_once __DIR__ . '/api/helpers/sso_guard.php';
    $webAuthUser = verifyAppToken($webToken) ?? ssoWhoami($webToken);
}
$isLoggedIn = is_array($webAuthUser) && !empty($webAuthUser['employee_id']);
if ($isLoggedIn) {
    refreshAppSessionCookie($webToken);
}

// Sajikan dokumen internal hanya melalui jalur yang memverifikasi token SSO.
// docs/.htaccess mengarahkan file fisik ke sini agar Apache tidak melewati guard.
if (preg_match('#^docs(?:/(.*))?$#i', $url, $docsMatch)) {
    require_once __DIR__ . '/api/helpers/sso_guard.php';
    $docsToken = trim((string)preg_replace('/^Bearer\\s+/i', '', (string)($_COOKIE['monbis_session'] ?? ($_COOKIE['sso_token'] ?? ''))));
    if ($docsToken === '') sendResponse(401, 'Token tidak ditemukan. Silakan login kembali.');
    // Jangan memakai fallback secret JWT lokal di jalur dokumen. Minta SSO
    // memvalidasi token agar token buatan tidak dapat membuka file internal.
    $docsUser = ssoWhoami($docsToken);
    if (empty($docsUser['employee_id'])) {
        sendResponse(401, 'Token SSO tidak valid atau sudah kedaluwarsa.');
    }

    $relativeDocsPath = str_replace('\\', '/', rawurldecode((string)($docsMatch[1] ?? '')));
    $segments = explode('/', $relativeDocsPath);
    if ($relativeDocsPath === '' || strpos($relativeDocsPath, "\0") !== false
        || in_array('.', $segments, true) || in_array('..', $segments, true)
        || preg_match('#(^|/)\.#', $relativeDocsPath)) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }

    $docsRoot = realpath(__DIR__ . '/docs');
    $docsFile = $docsRoot === false ? false : realpath($docsRoot . DIRECTORY_SEPARATOR . $relativeDocsPath);
    $docsPrefix = $docsRoot === false ? '' : rtrim($docsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if ($docsFile === false || !is_file($docsFile) || strpos($docsFile, $docsPrefix) !== 0) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }

    $docsTypes = [
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'json' => 'application/json; charset=utf-8',
        'md' => 'text/markdown; charset=utf-8',
        'pdf' => 'application/pdf',
        'sql' => 'text/plain; charset=utf-8',
        'txt' => 'text/plain; charset=utf-8',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];
    $docsExtension = strtolower(pathinfo($docsFile, PATHINFO_EXTENSION));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header('Content-Type: ' . ($docsTypes[$docsExtension] ?? 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . basename($docsFile) . '"');
    readfile($docsFile);
    exit;
}

// Fallback untuk hosting yang belum memuat aturan rewrite khusus React V2.
// Asset tetap dilayani dari dist, sedangkan route client-side memakai index.html.
if (preg_match('#^v2-react(?:/(.*))?$#i', $url, $v2ReactMatch)) {
    $v2Route = trim((string) ($v2ReactMatch[1] ?? ''), '/');
    $v2LoginRoute = preg_match('#^login-monbis-v2(?:/|$)#i', $v2Route) === 1;
    if (!$v2LoginRoute && !$isLoggedIn) {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $requestedPath = (string) (parse_url($requestUri, PHP_URL_PATH) ?: '');
        $requestedQuery = (string) (parse_url($requestUri, PHP_URL_QUERY) ?: '');
        $requestedPath .= $requestedQuery !== '' ? '?' . $requestedQuery : '';
        $loginUrl = BASE_APP . '/v2-react/login-monbis-v2?next=' . rawurlencode($requestedPath);
        header('Location: ' . $loginUrl, true, 302);
        exit;
    }

    $distRoot = realpath(__DIR__ . '/monbis-v2/dist');
    if ($distRoot === false || !is_file($distRoot . DIRECTORY_SEPARATOR . 'index.html')) {
        http_response_code(503);
        echo 'Build React V2 belum tersedia. Jalankan build monbis-v2 terlebih dahulu.';
        exit;
    }

    $relativePath = str_replace('\\', '/', rawurldecode((string) ($v2ReactMatch[1] ?? '')));
    $isAssetRequest = $relativePath !== ''
        && (strpos($relativePath, 'assets/') === 0 || preg_match('/\.[a-z0-9]{2,5}$/i', $relativePath));
    $filePath = $relativePath === '' ? false : realpath($distRoot . DIRECTORY_SEPARATOR . $relativePath);
    $distPrefix = rtrim($distRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $isInsideDist = $filePath !== false && strpos($filePath, $distPrefix) === 0;

    if ($isAssetRequest) {
        if (!$isInsideDist || !is_file($filePath)) {
            http_response_code(404);
            exit;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentTypes = [
            'css' => 'text/css; charset=utf-8',
            'html' => 'text/html; charset=utf-8',
            'ico' => 'image/x-icon',
            'js' => 'application/javascript; charset=utf-8',
            'json' => 'application/json; charset=utf-8',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain; charset=utf-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        ];
        header('Content-Type: ' . ($contentTypes[$extension] ?? 'application/octet-stream'));
        readfile($filePath);
        exit;
    }

    readfile($distRoot . DIRECTORY_SEPARATOR . 'index.html');
    exit;
}

// Halaman React KPI menangani login tanpa sesi; data/API tetap mewajibkan token SSO.
if (preg_match('#^kpi(?:/(.*))?$#i', $url, $kpiMatch)) {
    $kpiIndex = realpath(__DIR__ . '/kpi/index.html');
    if ($kpiIndex === false || !is_file($kpiIndex)) {
        http_response_code(503);
        exit('Build React KPI belum tersedia. Jalankan build kpi-tukin terlebih dahulu.');
    }
    header('Cache-Control: no-store');
    header('Content-Type: text/html; charset=utf-8');
    readfile($kpiIndex);
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
// CEK HAK AKSES HALAMAN
// =========================
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
    $next = trim((string) ($_GET['next'] ?? ''));
    $target = null;
    if ($next === 'tv_cabang') {
        $target = BASE_APP . '/tv_cabang';
    } elseif ($next !== '') {
        $parts = parse_url($next);
        if (is_array($parts)) {
            $targetPath = (string) ($parts['path'] ?? '');
            $basePrefix = rtrim($appBasePath, '/');
            $sameHost = !isset($parts['host']) || strtolower((string) $parts['host']) === $hostOnly;
            $targetScheme = strtolower((string) ($parts['scheme'] ?? $scheme));
            $sameScheme = $targetScheme === $scheme;
            $targetPort = isset($parts['port']) ? (int) $parts['port'] : null;
            $currentPort = isset(parse_url(BASE_APP)['port']) ? (int) parse_url(BASE_APP)['port'] : null;
            $samePort = !isset($parts['host'])
                || $targetPort === $currentPort
                || ($targetPort === null && $currentPort === null);
            $pathPrefixes = ['/v2-react/', '/kpi/', $basePrefix . '/v2-react/', $basePrefix . '/kpi/'];
            $appPath = false;
            foreach ($pathPrefixes as $prefix) {
                if ($prefix !== '/' && (strpos($targetPath, $prefix) === 0 || $targetPath === rtrim($prefix, '/'))) $appPath = true;
            }
            $localDevRoutes = [
                5173 => ['/', '/realisasi-kredit-growth', '/jatuh-tempo', '/pipeline-kredit', '/npl', '/rbb-realisasi-kredit', '/repayment-rate', '/recovery-npl', '/potensi-npl', '/update-komitmen-flow', '/flow-par', '/laba-rugi'],
                5174 => ['/kpi/setting_kpi_jabatan', '/kpi/nilai_kpi_ao', '/kpi/generate_kpi_ao', '/kpi/rekap_kpi_ao', '/kpi/report_cabang'],
            ];
            $isLocalDev = APP_ENV === 'local'
                && in_array(strtolower((string) ($parts['host'] ?? '')), ['localhost', '127.0.0.1'], true)
                && $targetScheme === 'http'
                && isset($targetPort, $localDevRoutes[$targetPort])
                && in_array($targetPath, $localDevRoutes[$targetPort], true);
            if (($sameHost && $sameScheme && $samePort && $appPath) || $isLocalDev) {
                $target = $next;
                if (!isset($parts['scheme'])) $target = BASE_APP . $next;
            }
        }
    }
    header('Location: ' . ($target ?: BASE_APP . '/dashboard'), true, 302);
    exit;
}

// Guard halaman RBAC secara server-side. Menu yang disembunyikan di browser
// tetap tidak dapat dibuka langsung tanpa hak akses matrix.
if ($isLoggedIn && !in_array($page, ['login', 'tv', 'tv_cabang'], true)) {
    try {
        require_once __DIR__ . '/api/controllers/config/database.php';
        require_once __DIR__ . '/api/helpers/rbac.php';
        $rbacInitialUser = rbacFindUserByEmployeeId($pdo, $webAuthUser['employee_id']);
        if ($rbacInitialUser) {
            $rbacInitialAccess = rbacEffectivePermissions($pdo, $rbacInitialUser);
            $rbacPageAccess = $page === 'setting_rbac'
                ? rbacCanManageMatrix($pdo, $rbacInitialUser)
                : rbacIsAdmin($rbacInitialUser)
                    || !empty($rbacInitialAccess['permissions']['page:' . trim($page)]['can_view']);
        } else {
            $rbacPageAccess = false;
        }
        if (!$rbacPageAccess) {
            http_response_code(403);
            echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Akses ditolak</title><style>body{margin:0;display:grid;place-items:center;min-height:100vh;background:#f5f8fb;color:#17324d;font:15px Arial,sans-serif}.box{width:min(440px,calc(100% - 32px));padding:28px;border:1px solid #dce6ed;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(23,50,77,.08)}h1{margin:0 0 8px;font-size:22px}p{margin:0 0 20px;color:#6e8294;line-height:1.5}a{display:inline-flex;align-items:center;min-height:40px;padding:0 14px;border-radius:8px;background:#1565d8;color:#fff;text-decoration:none;font-weight:700}</style></head><body><main class="box"><h1>Akses belum diberikan</h1><p>Halaman ini belum termasuk matrix akses jabatan, divisi, atau override pegawai Anda.</p><a href="' . htmlspecialchars(BASE_APP . '/dashboard', ENT_QUOTES, 'UTF-8') . '">Kembali ke dashboard</a></main></body></html>';
            exit;
        }
    } catch (Throwable $rbacError) {
        error_log('RBAC page guard failed: ' . $rbacError->getMessage());
        http_response_code(503);
        exit('Validasi akses sedang tidak tersedia. Silakan coba kembali.');
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
