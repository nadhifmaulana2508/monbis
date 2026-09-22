<?php
function v2_render_start(string $title, string $active): void
{
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#8DBCC7"><title>' . v2_e($title) . '</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/app.css?v=2"><link rel="stylesheet" href="assets/css/tokens.css?v=1"><link rel="stylesheet" href="assets/css/components.css?v=1"></head><body class="v2-body" data-v2-page="' . v2_e($active) . '">';
}

function v2_render_sidebar(string $active, string $baseUrl): void
{
    $link = static fn(string $page): string => $baseUrl . '/?page=' . rawurlencode($page);
    $is = static fn(string $page): string => $active === $page ? ' is-active' : '';
    echo '<aside class="v2-sidebar" id="v2Sidebar"><div class="v2-brand"><span class="v2-brand-mark">F</span><div><strong>FE WORKSPACE</strong><small>Component System</small></div><button type="button" class="v2-icon-button v2-sidebar-collapse" data-v2-sidebar-setting aria-label="Ringkas sidebar" title="Ringkas sidebar">' . v2_icon('arrow-left', 17) . '</button><button type="button" class="v2-icon-button v2-sidebar-close" data-v2-sidebar-close aria-label="Tutup menu">' . v2_icon('close', 18) . '</button></div>';
    echo '<nav class="v2-nav" aria-label="Navigasi v2">';
    echo '<p class="v2-nav-label">MAIN MENU</p>';
    echo '<a class="v2-nav-item' . $is('components') . '" href="' . v2_e($link('components')) . '">' . v2_icon('settings') . '<span>Component Library</span></a>';
    echo '<a class="v2-nav-item' . $is('collection') . '" href="' . v2_e($link('collection')) . '">' . v2_icon('users') . '<span>Collection</span></a>';
    echo '<a class="v2-nav-item' . $is('templates') . '" href="' . v2_e($link('templates')) . '">' . v2_icon('file') . '<span>Page Templates</span></a>';
    echo '<p class="v2-nav-label v2-nav-label--system">SYSTEM</p>';
    echo '<a class="v2-nav-item' . $is('settings') . '" href="' . v2_e($link('settings')) . '">' . v2_icon('settings') . '<span>Pengaturan</span></a>';
    echo '<a class="v2-nav-item" href="' . v2_e($link('components')) . '#assets">' . v2_icon('database') . '<span>Tokens &amp; Assets</span></a>';
    echo '</nav><div class="v2-sidebar-account"><span class="v2-avatar">FE</span><div><strong>FE Workspace</strong><small>Component authoring</small></div>' . v2_icon('chevron', 15) . '</div><div class="v2-sidebar-footer"><span>FE Workspace v0.1</span></div></aside>';
}

function v2_render_end(): void
{
    echo '<script src="assets/js/components.js?v=1" defer></script><script src="assets/js/app.js?v=2" defer></script></body></html>';
}
