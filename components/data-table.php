<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_build_grouped_thead')) {
    /**
     * Membuat thead satu atau dua tingkat dari konfigurasi kolom.
     * Kolom biasa otomatis memakai rowspan=2 ketika ada group children.
     */
    function mb_build_grouped_thead(array $columns): string
    {
        $hasGroups = false;
        foreach ($columns as $column) {
            if (!empty($column['children']) && is_array($column['children'])) {
                $hasGroups = true;
                break;
            }
        }

        $renderTh = static function (array $column, array $extraAttrs = []): string {
            $sort = $column['sort'] ?? null;
            $class = trim(($column['class'] ?? '') . ($sort ? ' mb-sort' : ''));
            $attrs = array_merge($column['attrs'] ?? [], $extraAttrs);
            if ($class !== '') $attrs['class'] = $class;
            if ($sort) $attrs['data-sort'] = $sort;

            $label = mb_e($column['label'] ?? '');
            if ($sort) $label .= ' <span class="mb-sort-icon"></span>';
            return '<th' . mb_attrs($attrs) . '>' . $label . '</th>';
        };

        $firstRow = '';
        $secondRow = '';
        foreach ($columns as $column) {
            $children = (!empty($column['children']) && is_array($column['children']))
                ? $column['children']
                : [];

            if ($children) {
                $firstRow .= $renderTh($column, ['colspan' => count($children), 'scope' => 'colgroup']);
                foreach ($children as $child) {
                    $secondRow .= $renderTh($child, ['scope' => 'col']);
                }
                continue;
            }

            $attrs = ['scope' => 'col'];
            if ($hasGroups) $attrs['rowspan'] = 2;
            $firstRow .= $renderTh($column, $attrs);
        }

        return '<tr>' . $firstRow . '</tr>' . ($hasGroups ? '<tr>' . $secondRow . '</tr>' : '');
    }
}

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
        if (!empty($cfg['footer_html'])) echo $cfg['footer_html'];
        echo '</div>';
    }
}
