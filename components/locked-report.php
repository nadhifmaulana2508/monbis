<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/info-modal.php';

if (!function_exists('mb_locked_view_button')) {
    function mb_locked_view_button(array $cfg = []): string
    {
        $id = $cfg['id'] ?? 'mbLockedView';
        $modalId = $cfg['modal_id'] ?? 'mbLockedReportInfo';
        $label = $cfg['label'] ?? 'Report Terkunci';
        return '<button type="button" id="' . mb_e($id) . '" class="mb-segmented__btn mb-segmented__btn--locked" '
            . 'data-mb-open-modal="' . mb_e($modalId) . '" aria-label="' . mb_e($label . ' masih terkunci') . '" title="' . mb_e($label . ' masih terkunci') . '">'
            . '<span class="mb-locked-view__icon">' . mb_svg('lock') . '</span><span>' . mb_e($label) . '</span></button>';
    }
}

if (!function_exists('mb_render_locked_menu_modal')) {
    /** Modal compact untuk membuka menu/report yang dilindungi kode akses. */
    function mb_render_locked_menu_modal(array $cfg = []): void
    {
        $cfg['card_class'] = trim('mb-access-gate--locked-menu ' . ($cfg['card_class'] ?? ''));
        mb_render_access_gate_modal($cfg);
    }
}

if (!function_exists('mb_render_locked_report_modal')) {
    function mb_render_locked_report_modal(array $cfg = []): void
    {
        $body = '<div class="mb-locked-report"><span class="mb-locked-report__icon">' . mb_svg('lock') . '</span><div><strong>'
            . mb_e($cfg['description'] ?? 'Report ini belum dibuka untuk penggunaan umum.') . '</strong><span>'
            . mb_e($cfg['note'] ?? 'Gunakan report utama sampai akses report ini dibuka.') . '</span></div></div>';
        mb_render_info_modal([
            'id' => $cfg['id'] ?? 'mbLockedReportInfo',
            'title' => $cfg['title'] ?? 'Report Masih Terkunci',
            'subtitle' => $cfg['subtitle'] ?? 'Akses dibatasi sementara.',
            'body_html' => $body,
        ]);
    }
}

if (!function_exists('mb_render_access_gate_modal')) {
    function mb_render_access_gate_modal(array $cfg = []): void
    {
        $id = $cfg['id'] ?? 'mbAccessGate';
        $formId = $cfg['form_id'] ?? ($id . 'Form');
        $inputId = $cfg['input_id'] ?? ($id . 'Input');
        $errorId = $cfg['error_id'] ?? ($id . 'Error');
        $cardClass = trim('mb-modal__card mb-access-gate ' . ($cfg['card_class'] ?? ''));
        echo '<div id="' . mb_e($id) . '" class="mb-modal mb-modal--access" role="dialog" aria-modal="true" aria-hidden="true">';
        echo '<div class="mb-modal__backdrop" data-mb-close-modal="' . mb_e($id) . '"></div>';
        echo '<form id="' . mb_e($formId) . '" class="' . mb_e($cardClass) . '" autocomplete="off">';
        echo '<header class="mb-access-gate__header"><span class="mb-modal__icon">' . mb_svg('lock') . '</span><div class="mb-modal__heading-copy"><h2 class="mb-modal__title">' . mb_e($cfg['title'] ?? 'Akses Report') . '</h2><p class="mb-modal__subtitle">' . mb_e($cfg['subtitle'] ?? 'Masukkan kode akses untuk membuka report.') . '</p></div><button type="button" class="mb-modal__close" data-mb-close-modal="' . mb_e($id) . '" aria-label="Tutup">' . mb_svg('close') . '</button></header>';
        echo '<div class="mb-access-gate__body"><label class="mb-access-gate__label" for="' . mb_e($inputId) . '">Kode Akses</label><div class="mb-access-gate__input-wrap"><input id="' . mb_e($inputId) . '" type="password" class="mb-field-control" placeholder="Masukkan kode akses" spellcheck="false" autocomplete="off"><button type="button" class="mb-access-gate__toggle" data-mb-access-toggle="' . mb_e($inputId) . '" aria-label="Tampilkan kode">' . mb_svg('eye') . '</button></div><div id="' . mb_e($errorId) . '" class="mb-access-gate__error" aria-live="polite"></div>';
        echo '<div class="mb-access-gate__note">' . mb_e($cfg['note'] ?? 'Akses berlaku selama tab ini masih dibuka. Tutup tab atau browser untuk mengunci kembali report.') . '</div>';
        echo '<div class="mb-access-gate__actions"><button type="button" class="mb-detail-page-btn" data-mb-close-modal="' . mb_e($id) . '">Batal</button><button type="submit" class="mb-access-gate__submit">' . mb_e($cfg['submit_label'] ?? 'Buka Report') . '</button></div></div>';
        echo '</form></div>';
    }
}
