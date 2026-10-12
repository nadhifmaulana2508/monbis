<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_aging_navigation')) {
    /** Navigasi bersama untuk report Aging Kredit dan By Produk. */
    function mb_render_aging_navigation(string $active = 'aging'): void
    {
        $items = [
            ['key' => 'aging', 'label' => 'Aging Kredit', 'href' => 'aging_kredit', 'icon' => 'chart'],
            ['key' => 'produk', 'label' => 'By Produk', 'href' => 'aging_produk', 'icon' => 'list'],
        ];

        echo '<nav class="mb-aging-nav" aria-label="Pilihan report kredit">';
        foreach ($items as $item) {
            $isActive = $item['key'] === $active;
            echo '<a class="mb-aging-nav__item' . ($isActive ? ' is-active' : '') . '" href="'
                . mb_e($item['href']) . '"' . ($isActive ? ' aria-current="page"' : '') . '>';
            echo mb_svg($item['icon']);
            echo '<span>' . mb_e($item['label']) . '</span>';
            echo '</a>';
        }
        echo '</nav>';
    }
}
?>

<style>
  .mb-aging-nav {
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:4px;
    border:1px solid #dbe3ee;
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.05);
  }
  .mb-aging-nav__item {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-height:32px;
    padding:0 11px;
    border-radius:8px;
    color:#64748b;
    font-size:10px;
    font-weight:850;
    text-decoration:none;
    transition:background .15s ease,color .15s ease,box-shadow .15s ease;
  }
  .mb-aging-nav__item svg { width:15px; height:15px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
  .mb-aging-nav__item:hover { background:#eff6ff; color:#2563eb; }
  .mb-aging-nav__item.is-active {
    background:#2563eb;
    color:#fff;
    box-shadow:0 5px 12px rgba(37,99,235,.22);
  }
  @media (max-width:767px) {
    .mb-aging-nav { width:100%; }
    .mb-aging-nav__item { flex:1 1 0; padding:0 7px; font-size:9px; }
  }
</style>
