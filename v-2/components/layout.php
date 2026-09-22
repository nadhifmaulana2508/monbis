<?php
function v2_render_start(string $title, string $active): void
{
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#8DBCC7"><title>' . v2_e($title) . '</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/app.css?v=2"><link rel="stylesheet" href="assets/css/tokens.css?v=1"><link rel="stylesheet" href="assets/css/components.css?v=1"></head><body class="v2-body" data-v2-page="' . v2_e($active) . '">';
}

function v2_render_sidebar(string $active, string $baseUrl, string $baseRoot): void
{
    $link = static fn(string $page): string => $baseUrl . '/?page=' . rawurlencode($page);
    $is = static fn(string $page): string => $active === $page ? ' is-active' : '';
    echo '<aside class="v2-sidebar" id="v2Sidebar"><div class="v2-brand"><span class="v2-brand-mark">M</span><div><strong>MONBIS</strong><small>Business Intelligence</small></div><button type="button" class="v2-icon-button v2-sidebar-collapse" data-v2-sidebar-setting aria-label="Ringkas sidebar" title="Ringkas sidebar">' . v2_icon('arrow-left', 17) . '</button><button type="button" class="v2-icon-button v2-sidebar-close" data-v2-sidebar-close aria-label="Tutup menu">' . v2_icon('close', 18) . '</button></div>';
    echo '<nav class="v2-nav" aria-label="Navigasi v2">';
    echo '<p class="v2-nav-label">MAIN MENU</p>';
    echo '<a class="v2-nav-item' . $is('dashboard') . '" href="' . v2_e($link('dashboard')) . '">' . v2_icon('home') . '<span>Overview</span></a>';
    echo '<div class="v2-nav-group"><button type="button" class="v2-nav-group-title" data-v2-nav-group><span>' . v2_icon('chart') . '<span>KPI Bisnis</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub"><a class="v2-nav-sub-item' . $is('kpi') . '" href="' . v2_e($link('kpi')) . '">Ringkasan KPI</a><a class="v2-nav-sub-item" href="' . v2_e($baseRoot . '/dashboard') . '">Dashboard Lama</a></div></div>';
    echo '<div class="v2-nav-group"><button type="button" class="v2-nav-group-title" data-v2-nav-group><span>' . v2_icon('file') . '<span>Laporan</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub"><a class="v2-nav-sub-item' . $is('reports') . '" href="' . v2_e($link('reports')) . '">Report Center</a><a class="v2-nav-sub-item" href="' . v2_e($baseRoot . '/report_npl') . '">NPL Lama</a></div></div>';
    echo '<div class="v2-nav-group"><button type="button" class="v2-nav-group-title" data-v2-nav-group><span>' . v2_icon('chart') . '<span>Perencanaan</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub"><a class="v2-nav-sub-item' . $is('rbb') . '" href="' . v2_e($link('rbb')) . '">RBB 2027</a><a class="v2-nav-sub-item" href="' . v2_e($baseRoot . '/input_rbb') . '">RBB Lama</a></div></div>';
    echo '<p class="v2-nav-label v2-nav-label--system">SYSTEM</p>';
    echo '<a class="v2-nav-item' . $is('components') . '" href="' . v2_e($link('components')) . '">' . v2_icon('settings') . '<span>UI Components</span></a>';
    echo '<a class="v2-nav-item' . $is('settings') . '" href="' . v2_e($link('settings')) . '">' . v2_icon('settings') . '<span>Pengaturan</span></a>';
    echo '<a class="v2-nav-item" href="' . v2_e($baseRoot . '/admin') . '">' . v2_icon('database') . '<span>Administrasi Lama</span></a>';
    echo '</nav><div class="v2-sidebar-account"><span class="v2-avatar">A</span><div><strong>Admin MONBIS</strong><small>Administrator</small></div>' . v2_icon('chevron', 15) . '</div><div class="v2-sidebar-footer"><span>MONBIS v2 · FE Library</span></div></aside>';
}

function v2_render_end(): void
{
    echo '<script src="assets/js/components.js?v=1" defer></script><script src="assets/js/app.js?v=2" defer></script></body></html>';
}
