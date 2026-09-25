<?php
function v2_render_start(string $title, string $active): void
{
    $baseUrl = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/report-dpk/v-2/index.php'))), '/');
    if ($baseUrl === '' || $baseUrl === '.') $baseUrl = '/report-dpk/v-2';
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#8DBCC7"><title>' . v2_e($title) . '</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="' . v2_e($baseUrl) . '/assets/css/app.css?v=3"><link rel="stylesheet" href="' . v2_e($baseUrl) . '/assets/css/tokens.css?v=2"><link rel="stylesheet" href="' . v2_e($baseUrl) . '/assets/css/components.css?v=28"></head><body class="v2-body v2-sidebar-auto" data-v2-page="' . v2_e($active) . '">';
}

function v2_render_sidebar(string $active, string $baseUrl, string $legacyBase = '', string $module = 'workspace'): void
{
    $standalone = in_array($module, ['rbb', 'kpi'], true);
    $link = static fn(string $page): string => $standalone && $page === $module ? $baseUrl : $baseUrl . '/' . rawurlencode($page);
    $moduleLink = static fn(string $page, string $tab): string => ($standalone && $page === $module ? $baseUrl : $link($page)) . '/' . rawurlencode($tab);
    $legacyLink = static fn(string $route): string => rtrim($legacyBase ?: dirname($baseUrl), '/') . '/' . ltrim($route, '/');
    $is = static fn(string $page): string => $active === $page ? ' is-active' : '';
    $kpiOpen = $active === 'kpi' ? ' is-open' : '';
    $rbbOpen = $active === 'rbb' ? ' is-open' : '';
    $logoBase = $legacyBase !== '' ? $legacyBase : dirname($baseUrl);
    $logoUrl = rtrim(str_replace('\\', '/', $logoBase), '/') . '/img/monbis-icon.webp';
    echo '<aside class="v2-sidebar" id="v2Sidebar"><div class="v2-brand"><a class="v2-brand-link" href="' . v2_e($link('launcher')) . '" title="MONBIS"><img class="v2-brand-logo" src="' . v2_e($logoUrl) . '" alt="MONBIS"><div><strong>MONBIS</strong><small>Monitoring Bisnis</small></div></a></div>';
    echo '<nav class="v2-nav" aria-label="Navigasi v2">';
    echo '<p class="v2-nav-label">MAIN MENU</p>';
    echo '<a class="v2-nav-item' . $is('launcher') . '" href="' . v2_e($link('launcher')) . '" title="Launcher MONBIS">' . v2_icon('home') . '<span>Launcher</span></a>';
    if (!$standalone || $module === 'kpi') {
        echo '<div class="v2-nav-group' . $kpiOpen . '" data-v2-access="kpi"><button type="button" class="v2-nav-group-title" data-v2-nav-group title="KPI Bisnis"><span>' . v2_icon('chart') . '<span>KPI Bisnis</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub">';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('kpi', 'setting')) . '" title="Setting KPI Jabatan">Setting KPI Jabatan</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('kpi', 'calculate')) . '" title="Nilai KPI AO">Nilai KPI AO</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('kpi', 'calculate')) . '" title="Generate KPI AO">Generate KPI AO</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('kpi', 'summary')) . '" title="Rekap KPI AO">Rekap KPI AO</a>';
        echo '</div></div>';
    }
    if (!$standalone || $module === 'rbb') {
        echo '<div class="v2-nav-group' . $rbbOpen . '" data-v2-access="rbb"><button type="button" class="v2-nav-group-title" data-v2-nav-group title="Input RBB"><span>' . v2_icon('file') . '<span>Input RBB</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub">';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('rbb', 'projection')) . '" title="Proyeksi RBB">Proyeksi RBB</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($moduleLink('rbb', 'aba')) . '" title="Input RBB ABA">Input RBB ABA</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($link('rbb') . '/detail/kredit') . '" title="Input RBB Kredit">Input RBB Kredit</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($link('rbb') . '/detail/damas') . '" title="Input RBB DAMAS">Input RBB DAMAS</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($link('rbb') . '/detail/pendapatan') . '" title="Input RBB Pendapatan">Input RBB Pendapatan</a>';
        echo '<a class="v2-nav-sub-item" href="' . v2_e($link('rbb') . '/detail/beban') . '" title="Input RBB Beban">Input RBB Beban</a>';
        echo '</div></div>';
    }
    if (!$standalone) {
        echo '<div class="v2-nav-group"><button type="button" class="v2-nav-group-title" data-v2-nav-group title="Collection"><span>' . v2_icon('users') . '<span>Collection</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub"><a class="v2-nav-sub-item' . $is('collection') . '" href="' . v2_e($link('report_npl')) . '" title="Report NPL">Report NPL</a></div></div>';
    }
    echo '<p class="v2-nav-label v2-nav-label--system">SYSTEM</p>';
    echo '<a class="v2-nav-item' . $is('settings') . '" href="' . v2_e($link('settings')) . '" title="Pengaturan">' . v2_icon('settings') . '<span>Pengaturan</span></a>';
    echo '</nav><div class="v2-sidebar-account"><span class="v2-avatar">MB</span><div><strong>MONBIS</strong><small>Workspace</small></div>' . v2_icon('chevron', 15) . '</div><div class="v2-sidebar-footer"><span>MONBIS v2</span></div></aside>';
}

function v2_render_end(): void
{
    $baseUrl = rtrim(str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/report-dpk/v-2/index.php'))), '/');
    if ($baseUrl === '' || $baseUrl === '.') $baseUrl = '/report-dpk/v-2';
    echo '<script src="' . v2_e($baseUrl) . '/assets/js/components.js?v=4" defer></script><script src="' . v2_e($baseUrl) . '/assets/js/app.js?v=4" defer></script></body></html>';
}
