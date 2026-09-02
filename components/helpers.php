<?php
/**
 * Monbis UI helpers.
 * Semua class memakai prefix mb- agar tidak bocor ke navbar/layout utama.
 */

if (!function_exists('mb_e')) {
    function mb_e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('mb_attrs')) {
    function mb_attrs(array $attrs = []): string
    {
        $html = '';
        foreach ($attrs as $key => $value) {
            if ($value === null || $value === false) continue;
            if ($value === true) {
                $html .= ' ' . mb_e($key);
                continue;
            }
            $html .= ' ' . mb_e($key) . '="' . mb_e($value) . '"';
        }
        return $html;
    }
}

if (!function_exists('mb_svg')) {
    function mb_svg(string $name): string
    {
        $icons = [
            'chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20V10M12 20V4M19 20v-7"/><path d="M3 20h18"/></svg>',
            'info' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>',
            'download' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M5 21h14a2 2 0 0 0 2-2v-2M3 17v2a2 2 0 0 0 2 2"/></svg>',
            'save' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h12l3 3v15H4V3h1Z"/><path d="M8 3v6h8V3M8 21v-6h8v6"/></svg>',
            'edit' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>',
            'filter' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M7 12h10M10 18h4"/></svg>',
            'close' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>',
            'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
            'file' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>',
            'warning' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/></svg>',
            'lock' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>',
            'eye' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>',
            'list' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>',
            'percent' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h.01M17 17h.01"/><path d="m6 18 12-12"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/></svg>',
            'zap' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/></svg>',
        ];
        return $icons[$name] ?? $icons['chart'];
    }
}
