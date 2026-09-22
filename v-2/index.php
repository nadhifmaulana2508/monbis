<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Preview dapat dibuka tanpa cookie saat fondasi FE sedang dikerjakan.
if (empty($_COOKIE['sso_token']) && !isset($_GET['preview'])) {
    header('Location: ../login');
    exit;
}

require_once __DIR__ . '/components/bootstrap.php';

$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/report-dpk/v-2')), '/');
$baseRoot = preg_replace('#/v-2$#', '', $baseUrl) ?: '/report-dpk';
$page = strtolower(trim((string)($_GET['page'] ?? 'dashboard')));
$allowedPages = ['dashboard', 'kpi', 'rbb', 'reports', 'components', 'settings'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

v2_render_start('MONBIS v2 · ' . ucfirst($page), $page);
v2_render_sidebar($page, $baseUrl, $baseRoot);
?>
<div class="v2-main" id="v2Main">
  <header class="v2-topbar">
    <div class="v2-topbar-left"><button type="button" class="v2-icon-button v2-mobile-menu" data-v2-sidebar-toggle aria-label="Buka menu"><?= v2_icon('menu', 20) ?></button><button type="button" class="v2-icon-button v2-desktop-menu" data-v2-sidebar-setting aria-label="Ringkas sidebar" title="Ringkas sidebar"><?= v2_icon('menu', 18) ?></button><button type="button" class="v2-icon-button v2-back-button" onclick="history.back()" aria-label="Kembali"><?= v2_icon('arrow-left', 18) ?></button><div class="v2-breadcrumb"><span>Workspace</span><span class="v2-breadcrumb-separator">›</span><strong><?= v2_e($page === 'components' ? 'FE Component Library' : ucfirst($page)) ?></strong></div></div>
    <div class="v2-topbar-center"><label class="v2-global-search"><span><?= v2_icon('search', 16) ?></span><input type="search" placeholder="Cari apa saja..." aria-label="Cari apa saja"></label></div>
    <div class="v2-topbar-actions"><button type="button" class="v2-icon-button v2-theme-toggle" data-v2-theme-toggle title="Mode gelap" aria-label="Mode tampilan"><span class="v2-theme-light-icon"><?= v2_icon('sun', 18) ?></span><span class="v2-theme-dark-icon"><?= v2_icon('moon', 18) ?></span></button><button type="button" class="v2-icon-button v2-notification" aria-label="Notifikasi"><?= v2_icon('bell', 18) ?><i></i></button><div class="v2-user-chip"><span class="v2-avatar" data-v2-user-initials>MB</span><span class="v2-user-copy"><strong data-v2-user-name>Admin</strong><small>Administrator</small></span><?= v2_icon('chevron', 14) ?></div></div>
  </header>
  <main class="v2-content">
    <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
  </main>
</div>
<div class="v2-sidebar-overlay" data-v2-sidebar-close></div>
<?php v2_render_end(); ?>
