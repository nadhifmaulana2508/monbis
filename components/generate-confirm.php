<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_generate_confirm')) {
    function mb_render_generate_confirm(array $cfg = []): void
    {
        $id = $cfg['id'] ?? 'mbGenerateConfirm';
        echo '<div id="' . mb_e($id) . '" class="mb-modal mb-modal--info mb-kpi-generate-confirm" role="dialog" aria-modal="true" aria-hidden="true">';
        echo '<div class="mb-modal__backdrop" data-mb-close-modal="' . mb_e($id) . '"></div>';
        echo '<section class="mb-modal__card mb-modal__card--info">';
        echo '<header class="mb-modal__header"><div class="mb-modal__heading"><span class="mb-modal__icon">' . mb_svg('info') . '</span><div class="mb-modal__heading-copy">';
        echo '<h2 class="mb-modal__title">Konfirmasi Generate KPI</h2><p class="mb-modal__subtitle">Pilih periode yang ingin diproses.</p></div></div>';
        echo '<button type="button" class="mb-modal__close" data-mb-close-modal="' . mb_e($id) . '" aria-label="Tutup">' . mb_svg('close') . '</button></header>';
        echo '<div class="mb-modal__body"><div class="mb-kpi-generate-confirm__copy"><strong id="' . mb_e($id) . 'Title">Sebagian data sudah tersedia.</strong><span id="' . mb_e($id) . 'Message">Pilih Generate Semua untuk menghitung ulang, atau Generate yang Belum untuk melewati periode yang sudah ada.</span></div></div>';
        echo '<footer class="mb-kpi-generate-confirm__actions"><button type="button" class="mb-btn mb-btn--ghost" data-kpi-generate-choice="cancel">Batal</button><button type="button" class="mb-btn mb-btn--ghost" data-kpi-generate-choice="pending">Yang Belum</button><button type="button" class="mb-btn mb-btn--primary" data-kpi-generate-choice="all">Semua</button></footer>';
        echo '</section></div>';
    }
}
