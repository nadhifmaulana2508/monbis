<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/page-header.php';

if (!function_exists('mb_render_detail_modal')) {
    function mb_render_detail_modal(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbDetailModal';
        $titleId = $cfg['title_id'] ?? ($id . 'Title');
        $subtitleId = $cfg['subtitle_id'] ?? ($id . 'Subtitle');
        $summaryId = $cfg['summary_id'] ?? ($id . 'Summary');
        $bodyId = $cfg['body_id'] ?? ($id . 'Body');
        $footerId = $cfg['footer_id'] ?? ($id . 'Footer');
        $size = $cfg['size'] ?? 'lg';

        echo '<div id="' . mb_e($id) . '" class="mb-modal mb-modal--detail" role="dialog" aria-modal="true" aria-hidden="true">';
        echo '  <div class="mb-modal__backdrop" data-mb-close-modal="' . mb_e($id) . '"></div>';
        echo '  <section class="mb-modal__card mb-modal__card--' . mb_e($size) . '">';
        echo '    <header class="mb-detail-header">';
        echo '      <div class="mb-modal__heading">';
        echo '        <span class="mb-modal__icon">' . ($cfg['icon'] ?? mb_svg('file')) . '</span>';
        echo '        <div class="mb-modal__heading-copy"><h2 id="' . mb_e($titleId) . '" class="mb-modal__title">' . mb_e($cfg['title'] ?? 'Detail') . '</h2>';
        echo '        <p id="' . mb_e($subtitleId) . '" class="mb-modal__subtitle">' . mb_e($cfg['subtitle'] ?? '') . '</p></div>';
        echo '      </div>';

        echo '      <div class="mb-detail-toolbar">';
        if (!empty($cfg['search'])) {
            $search = is_array($cfg['search']) ? $cfg['search'] : [];
            $searchId = $search['id'] ?? ($id . 'Search');
            echo '<label class="mb-search">' . mb_svg('search') . '<input type="search" id="' . mb_e($searchId) . '" class="mb-field-control" placeholder="' . mb_e($search['placeholder'] ?? 'Cari nama / rekening...') . '" autocomplete="off"></label>';
        }
        foreach (($cfg['filters'] ?? []) as $field) mb_render_field($field);
        foreach (($cfg['actions'] ?? []) as $action) {
            $attrs = $action['attrs'] ?? [];
            $attrs['type'] = 'button';
            $attrs['class'] = 'mb-icon-button mb-icon-button--' . ($action['tone'] ?? 'success');
            if (!empty($action['title'])) $attrs['title'] = $action['title'];
            echo '<button' . mb_attrs($attrs) . '>' . mb_svg($action['icon'] ?? 'download') . '</button>';
        }
        echo '      </div>';
        echo '      <button type="button" class="mb-modal__close" data-mb-close-modal="' . mb_e($id) . '" aria-label="Tutup">' . mb_svg('close') . '</button>';
        echo '    </header>';

        echo '    <div id="' . mb_e($summaryId) . '" class="mb-summary is-hidden"></div>';
        echo '    <div class="mb-detail-content"><div id="' . mb_e($bodyId) . '" class="mb-detail-body"></div></div>';
        echo '    <footer id="' . mb_e($footerId) . '" class="mb-detail-footer' . (empty($cfg['footer_html']) ? ' is-hidden' : '') . '">' . ($cfg['footer_html'] ?? '') . '</footer>';
        echo '  </section>';
        echo '</div>';
    }
}
