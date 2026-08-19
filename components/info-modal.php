<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_info_modal')) {
    function mb_render_info_modal(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbInfoModal';
        $title = $cfg['title'] ?? 'Informasi';
        $subtitle = $cfg['subtitle'] ?? '';
        $bodyId = $cfg['body_id'] ?? ($id . 'Body');
        $bodyHtml = $cfg['body_html'] ?? '';
        $icon = $cfg['icon'] ?? mb_svg('info');

        echo '<div id="' . mb_e($id) . '" class="mb-modal mb-modal--info" role="dialog" aria-modal="true" aria-hidden="true">';
        echo '  <div class="mb-modal__backdrop" data-mb-close-modal="' . mb_e($id) . '"></div>';
        echo '  <section class="mb-modal__card mb-modal__card--info">';
        echo '    <header class="mb-modal__header">';
        echo '      <div class="mb-modal__heading">';
        echo '        <span class="mb-modal__icon">' . $icon . '</span>';
        echo '        <div class="mb-modal__heading-copy"><h2 class="mb-modal__title">' . mb_e($title) . '</h2>';
        if ($subtitle !== '') echo '<p class="mb-modal__subtitle">' . mb_e($subtitle) . '</p>';
        echo '        </div>';
        echo '      </div>';
        echo '      <button type="button" class="mb-modal__close" data-mb-close-modal="' . mb_e($id) . '" aria-label="Tutup">' . mb_svg('close') . '</button>';
        echo '    </header>';
        echo '    <div id="' . mb_e($bodyId) . '" class="mb-modal__body mb-info-body">' . $bodyHtml . '</div>';
        echo '  </section>';
        echo '</div>';
    }
}
