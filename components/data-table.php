<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_table_shell')) {
    function mb_render_table_shell(array $cfg): void
    {
        $wrapId = $cfg['wrapper_id'] ?? 'mbTableWrap';
        $tableId = $cfg['table_id'] ?? 'mbTable';
        $loadingId = $cfg['loading_id'] ?? ($tableId . 'Loading');
        $class = trim('mb-table ' . ($cfg['class'] ?? ''));

        echo '<div class="mb-table-region">';
        echo '  <div id="' . mb_e($loadingId) . '" class="mb-loading is-hidden"><span class="mb-spinner"></span><span>' . mb_e($cfg['loading_text'] ?? 'Memuat data...') . '</span></div>';
        echo '  <div id="' . mb_e($wrapId) . '" class="mb-table-wrap">';
        echo '    <table id="' . mb_e($tableId) . '" class="' . mb_e($class) . '">';
        if (!empty($cfg['colgroup_html'])) echo $cfg['colgroup_html'];
        echo '      <thead>' . ($cfg['thead_html'] ?? '') . '</thead>';
        foreach (($cfg['tbody_ids'] ?? ['mbTableBody']) as $tbodyId) {
            echo '      <tbody id="' . mb_e($tbodyId) . '"></tbody>';
        }
        echo '    </table>';
        echo '  </div>';
        echo '</div>';
    }
}
