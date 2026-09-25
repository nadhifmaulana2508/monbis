<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/components/bootstrap.php';

$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/report-dpk/v-2')), '/');
if ($baseUrl === '' || $baseUrl === '.') $baseUrl = '/report-dpk/v-2';
$v2Runtime = v2_runtime_config($baseUrl);
$legacyBase = $v2Runtime['backend_base_url'];
$apiBase = $v2Runtime['api_base_url'];
$rbbRouteBase = $v2Runtime['module'] === 'rbb' ? $baseUrl : $baseUrl . '/rbb';

// Preview hanya boleh dipakai di localhost; produksi tetap wajib login.
$previewRequested = isset($_GET['preview']) && $v2Runtime['allow_preview'];
if ($v2Runtime['page_auth'] && empty($_COOKIE['sso_token']) && !$previewRequested) {
    $loginBase = $v2Runtime['auth_base_url'] !== '' ? $v2Runtime['auth_base_url'] : $legacyBase;
    header('Location: ' . $loginBase . '/login');
    exit;
}

$page = strtolower(trim((string)($_GET['page'] ?? 'launcher')));
$allowedPages = ['launcher', 'kpi', 'rbb', 'rbb_print', 'components', 'collection', 'templates', 'settings'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'launcher';
}
if ($page === 'launcher' && in_array($v2Runtime['module'], ['rbb', 'kpi'], true)) {
    $page = $v2Runtime['module'];
}

$pageTitles = ['launcher' => 'MONBIS Workspace', 'kpi' => 'KPI Bisnis', 'rbb' => 'Input RBB', 'rbb_print' => 'Cetak RBB', 'components' => 'FE Component Library', 'collection' => 'Report NPL', 'templates' => 'Page Templates', 'settings' => 'Workspace Settings'];
v2_render_start('MONBIS · ' . $pageTitles[$page], $page);
$sidebarPage = $page === 'rbb_print' ? 'rbb' : $page;
v2_render_sidebar($sidebarPage, $baseUrl, $legacyBase, $v2Runtime['module']);
?>
<div class="v2-main" id="v2Main">
  <header class="v2-topbar">
    <div class="v2-topbar-left"><button type="button" class="v2-icon-button v2-mobile-menu" data-v2-sidebar-toggle aria-label="Buka menu" title="Buka menu"><span class="v2-mobile-menu-icon"><?= v2_icon('menu', 20) ?></span><span class="v2-mobile-close-icon"><?= v2_icon('close', 20) ?></span></button><button type="button" class="v2-icon-button v2-desktop-menu" data-v2-sidebar-setting aria-label="Ringkas sidebar" title="Ringkas sidebar"><?= v2_icon('menu', 18) ?></button><button type="button" class="v2-icon-button v2-back-button" onclick="history.back()" aria-label="Kembali"><?= v2_icon('arrow-left', 18) ?></button></div>
    <div class="v2-topbar-actions"><?= in_array($page, ['collection', 'kpi', 'rbb'], true) ? '<button type="button" class="v2-icon-button v2-filter-toggle" data-v2-filter-toggle title="Buka filter" aria-label="Buka filter">' . v2_icon('filter', 18) . '<span data-v2-filter-count hidden>0</span></button>' : '' ?><button type="button" class="v2-icon-button v2-theme-toggle" data-v2-theme-toggle title="Mode gelap" aria-label="Mode tampilan"><span class="v2-theme-light-icon"><?= v2_icon('sun', 18) ?></span><span class="v2-theme-dark-icon"><?= v2_icon('moon', 18) ?></span></button><button type="button" class="v2-icon-button v2-notification" aria-label="Notifikasi"><?= v2_icon('bell', 18) ?><i></i></button><div class="v2-user-chip"><span class="v2-avatar" data-v2-user-initials>MB</span><span class="v2-user-copy"><strong data-v2-user-name>Workspace User</strong><small>MONBIS</small></span><?= v2_icon('chevron', 14) ?></div></div>
  </header>
  <main class="v2-content">
    <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
  </main>
</div>
<div class="v2-sidebar-overlay" data-v2-sidebar-close></div>
<?php v2_render_end(); ?>
