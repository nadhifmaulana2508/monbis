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
    <button type="button" class="v2-icon-button v2-mobile-menu" data-v2-sidebar-toggle aria-label="Buka menu">
      <?= v2_icon('menu', 20) ?>
    </button>
    <div class="v2-breadcrumb">
      <span>MONBIS</span><span class="v2-breadcrumb-separator">/</span><strong><?= v2_e(ucfirst($page)) ?></strong>
    </div>
    <div class="v2-topbar-actions">
      <button type="button" class="v2-icon-button" data-v2-theme-toggle title="Mode tampilan" aria-label="Mode tampilan"><?= v2_icon('sun', 18) ?></button>
      <div class="v2-user-chip">
        <span class="v2-avatar" data-v2-user-initials>MB</span>
        <span class="v2-user-copy"><strong data-v2-user-name>Pengguna</strong><small>MONBIS v2</small></span>
      </div>
    </div>
  </header>
  <main class="v2-content">
    <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
  </main>
</div>
<div class="v2-sidebar-overlay" data-v2-sidebar-close></div>
<?php v2_render_end(); ?>
