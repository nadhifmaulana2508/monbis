<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_summary')) {
    function mb_render_summary(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbSummary';
        $cards = $cfg['cards'] ?? [];
        $hidden = !empty($cfg['hidden']) ? ' is-hidden' : '';
        echo '<div id="' . mb_e($id) . '" class="mb-summary' . $hidden . '">';
        foreach ($cards as $card) {
            $tone = $card['tone'] ?? 'default';
            echo '<div class="mb-summary-card mb-summary-card--' . mb_e($tone) . '">';
            echo '<div class="mb-summary-card__label">' . mb_e($card['label'] ?? '') . '</div>';
            echo '<div class="mb-summary-card__value">' . mb_e($card['value'] ?? '-') . '</div>';
            if (!empty($card['meta'])) echo '<div class="mb-summary-card__meta">' . mb_e($card['meta']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
}
