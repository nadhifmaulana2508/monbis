<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/page-header.php';
require_once __DIR__ . '/info-modal.php';
require_once __DIR__ . '/summary-cards.php';
require_once __DIR__ . '/detail-modal.php';
require_once __DIR__ . '/locked-report.php';
require_once __DIR__ . '/assignment-modal.php';
require_once __DIR__ . '/data-table.php';
require_once __DIR__ . '/report-page.php';

if (!function_exists('mb_ui_assets')) {
    function mb_ui_assets(string $base = '.'): void
    {
        $base = rtrim($base, '/');
        echo '<link rel="stylesheet" href="' . mb_e($base . '/assets/css/monbis-components.css') . '?v=64">' . PHP_EOL;
        echo '<script src="' . mb_e($base . '/assets/js/monbis-components.js') . '?v=17" defer></script>' . PHP_EOL;
    }
}
