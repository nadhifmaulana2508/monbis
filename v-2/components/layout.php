<?php
function v2_render_start(string $title, string $active): void
{
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#8DBCC7"><title>' . v2_e($title) . '</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/app.css?v=2"><link rel="stylesheet" href="assets/css/tokens.css?v=1"><link rel="stylesheet" href="assets/css/components.css?v=1"></head><body class="v2-body" data-v2-page="' . v2_e($active) . '">';
}

function v2_render_sidebar(string $active, string $baseUrl): void
{
    $link = static fn(string $page): string => $baseUrl . '/' . rawurlencode($page);
    $is = static fn(string $page): string => $active === $page ? ' is-active' : '';
    $logoUrl = rtrim(str_replace('\\', '/', dirname($baseUrl)), '/') . '/img/monbis-icon.webp';
    echo '<aside class="v2-sidebar" id="v2Sidebar"><div class="v2-brand"><a class="v2-brand-link" href="' . v2_e($link('report_npl')) . '" title="MONBIS"><img class="v2-brand-logo" src="' . v2_e($logoUrl) . '" alt="MONBIS"><div><strong>MONBIS</strong><small>Monitoring Bisnis</small></div></a><button type="button" class="v2-icon-button v2-sidebar-close" data-v2-sidebar-close aria-label="Tutup menu">' . v2_icon('close', 18) . '</button></div>';
    echo '<nav class="v2-nav" aria-label="Navigasi v2">';
    echo '<p class="v2-nav-label">MAIN MENU</p>';
    echo '<a class="v2-nav-item' . $is('components') . '" href="' . v2_e($link('components')) . '" title="Komponen UI">' . v2_icon('settings') . '<span>Komponen UI</span></a>';
    echo '<div class="v2-nav-group"><button type="button" class="v2-nav-group-title" data-v2-nav-group title="Collection"><span>' . v2_icon('users') . '<span>Collection</span></span>' . v2_icon('chevron', 15) . '</button><div class="v2-nav-sub"><a class="v2-nav-sub-item' . $is('collection') . '" href="' . v2_e($link('report_npl')) . '" title="Report NPL">Report NPL</a></div></div>';
    echo '<p class="v2-nav-label v2-nav-label--system">SYSTEM</p>';
    echo '<a class="v2-nav-item' . $is('settings') . '" href="' . v2_e($link('settings')) . '" title="Pengaturan">' . v2_icon('settings') . '<span>Pengaturan</span></a>';
    echo '<a class="v2-nav-item" href="' . v2_e($link('components')) . '#assets" title="Asset UI">' . v2_icon('database') . '<span>Asset UI</span></a>';
    echo '</nav><div class="v2-sidebar-account"><span class="v2-avatar">FE</span><div><strong>MONBIS</strong><small>Workspace</small></div>' . v2_icon('chevron', 15) . '</div><div class="v2-sidebar-footer"><span>MONBIS v2</span></div></aside>';
}

function v2_render_end(): void
{
    echo '<script src="assets/js/components.js?v=1" defer></script><script src="assets/js/app.js?v=2" defer></script></body></html>';
}
