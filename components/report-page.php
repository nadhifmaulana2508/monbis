<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/page-header.php';
require_once __DIR__ . '/data-table.php';

if (!function_exists('mb_build_report_legend')) {
    /** Membuat legenda singkatan report yang ringkas dan dapat dipakai ulang. */
    function mb_build_report_legend(array $items, array $attrs = []): string
    {
        $attrs['class'] = trim('mb-report-legend ' . ($attrs['class'] ?? ''));
        $html = '<div' . mb_attrs($attrs) . '>';
        foreach ($items as $item) {
            $tone = preg_replace('/[^a-z0-9_-]/i', '', (string)($item['tone'] ?? 'blue'));
            $html .= '<div class="mb-report-legend__item mb-report-legend__item--' . mb_e($tone) . '"><strong>'
                . mb_e($item['code'] ?? '') . '</strong><span>' . mb_e($item['text'] ?? '') . '</span></div>';
        }
        return $html . '</div>';
    }
}

if (!function_exists('mb_render_report_toolbar')) {
    function mb_render_report_toolbar(array $cfg): void
    {
        $title = $cfg['title'] ?? 'Rekap Data';
        $titleId = $cfg['title_id'] ?? '';

        echo '<div class="mb-report-toolbar">';
        echo '  <div class="mb-report-toolbar__title"' . ($titleId !== '' ? ' id="' . mb_e($titleId) . '"' : '') . '>' . mb_e($title) . '</div>';
        echo '  <div class="mb-report-toolbar__tools">';

        if (!empty($cfg['before_html'])) echo $cfg['before_html'];

        if (!empty($cfg['search'])) {
            $search = is_array($cfg['search']) ? $cfg['search'] : [];
            echo '<label class="mb-search">' . mb_svg('search');
            echo '<input type="search" id="' . mb_e($search['id'] ?? 'mbReportSearch') . '" class="mb-field-control" placeholder="' . mb_e($search['placeholder'] ?? 'Cari data...') . '" autocomplete="off">';
            echo '</label>';
        }

        foreach (($cfg['actions'] ?? []) as $action) {
            $attrs = $action['attrs'] ?? [];
            $variant = $action['variant'] ?? 'icon';
            $attrs['type'] = $action['type'] ?? 'button';
            $baseClass = $variant === 'view-switch'
                ? 'mb-view-switch'
                : 'mb-icon-button mb-icon-button--' . ($action['tone'] ?? 'primary');
            $attrs['class'] = trim($baseClass . ' ' . ($action['class'] ?? ''));
            if (!empty($action['title'])) $attrs['title'] = $action['title'];
            if (!empty($action['aria_label'])) $attrs['aria-label'] = $action['aria_label'];
            echo '<button' . mb_attrs($attrs) . '>' . mb_svg($action['icon'] ?? 'download');
            if (!empty($action['label'])) echo '<span class="mb-icon-button__label">' . mb_e($action['label']) . '</span>';
            echo '</button>';
        }

        if (!empty($cfg['after_html'])) echo $cfg['after_html'];

        echo '  </div>';
        echo '</div>';
    }
}

if (!function_exists('mb_render_report_page')) {
    function mb_render_report_page(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbReportPage';
        $class = trim('mb-report-page mb-report-standard ' . ($cfg['class'] ?? ''));
        $header = $cfg['header'] ?? [];
        $toolbar = $cfg['toolbar'] ?? [];
        $table = $cfg['table'] ?? [];

        echo '<main id="' . mb_e($id) . '" class="' . mb_e($class) . '">';
        mb_render_page_header($header);
        echo '<section class="mb-report-card mb-report-card--grow">';
        mb_render_report_toolbar($toolbar);
        if (!empty($cfg['legend_html'])) echo $cfg['legend_html'];
        mb_render_table_shell($table);
        echo '</section>';
        echo '</main>';
    }
}
