<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_assignment_modal')) {
    function mb_render_assignment_modal(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbAssignmentModal';
        $submitId = $cfg['submit_id'] ?? ($id . 'Submit');
        $selects = $cfg['selects'] ?? [[
            'id'=>$cfg['select_id'] ?? ($id . 'Select'),
            'label'=>$cfg['select_label'] ?? 'Petugas',
            'placeholder'=>$cfg['select_placeholder'] ?? 'Pilih petugas',
        ]];
        echo '<div id="' . mb_e($id) . '" class="mb-modal mb-modal--assignment" role="dialog" aria-modal="true" aria-hidden="true">';
        echo '<div class="mb-modal__backdrop" data-mb-close-modal="' . mb_e($id) . '"></div><section class="mb-modal__card mb-modal__card--assignment">';
        echo '<header class="mb-detail-header"><div class="mb-modal__heading"><span class="mb-modal__icon">' . ($cfg['icon'] ?? mb_svg('edit')) . '</span><div class="mb-modal__heading-copy">';
        echo '<h2 class="mb-modal__title">' . mb_e($cfg['title'] ?? 'Tetapkan Petugas') . '</h2><p id="' . mb_e($id) . 'Subtitle" class="mb-modal__subtitle">' . mb_e($cfg['subtitle'] ?? '') . '</p></div></div>';
        echo '<button type="button" class="mb-modal__close" data-mb-close-modal="' . mb_e($id) . '" aria-label="Tutup">' . mb_svg('close') . '</button></header>';
        echo '<div class="mb-assignment-body"><div id="' . mb_e($id) . 'Notice" class="mb-assignment-notice"></div>';
        echo '<div class="mb-assignment-fields">';
        foreach ($selects as $select) {
            echo '<label class="mb-assignment-field"><span>' . mb_e($select['label'] ?? 'Petugas') . '</span><select id="' . mb_e($select['id'] ?? '') . '" class="mb-field-control"><option value="">' . mb_e($select['placeholder'] ?? 'Pilih petugas') . '</option></select></label>';
        }
        echo '</div>';
        echo '<p class="mb-assignment-help">' . mb_e($cfg['help'] ?? '') . '</p></div>';
        echo '<footer class="mb-assignment-footer"><button type="button" class="mb-btn mb-btn--ghost" data-mb-close-modal="' . mb_e($id) . '">Batal</button><button type="button" id="' . mb_e($submitId) . '" class="mb-btn mb-btn--primary">Simpan Mapping</button></footer>';
        echo '</section></div>';
    }
}
