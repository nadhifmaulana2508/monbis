<?php
function v2_e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function v2_attributes(array $attrs = []): string
{
    $html = '';
    foreach ($attrs as $key => $value) {
        if ($value === null || $value === false) continue;
        $html .= ' ' . v2_e((string)$key) . '="' . v2_e((string)$value) . '"';
    }
    return $html;
}

function v2_button(string $label, string $tone = 'primary', string $icon = '', array $attrs = []): string
{
    return '<button type="button" class="v2-button v2-button--' . v2_e($tone) . '"' . v2_attributes($attrs) . '>'
        . ($icon ? v2_icon($icon, 16) : '') . '<span>' . v2_e($label) . '</span></button>';
}

function v2_input(string $id, string $label, string $value = '', string $placeholder = '', string $type = 'text'): string
{
    return '<label class="v2-field" for="' . v2_e($id) . '"><span>' . v2_e($label) . '</span><input id="' . v2_e($id) . '" name="' . v2_e($id) . '" type="' . v2_e($type) . '" value="' . v2_e($value) . '" placeholder="' . v2_e($placeholder) . '"></label>';
}

function v2_select(string $id, string $label, array $options, string $selected = '', array $attrs = []): string
{
    $html = '<label class="v2-field" for="' . v2_e($id) . '"><span>' . v2_e($label) . '</span><select id="' . v2_e($id) . '" name="' . v2_e($id) . '"' . v2_attributes($attrs) . '>';
    foreach ($options as $value => $text) $html .= '<option value="' . v2_e((string)$value) . '"' . ((string)$value === $selected ? ' selected' : '') . '>' . v2_e((string)$text) . '</option>';
    return $html . '</select></label>';
}

function v2_card_open(string $title, string $subtitle = ''): string
{
    return '<section class="v2-card"><div class="v2-card-heading"><div><h2>' . v2_e($title) . '</h2>' . ($subtitle ? '<p>' . v2_e($subtitle) . '</p>' : '') . '</div>';
}

function v2_card_close(): string
{
    return '</section>';
}

function v2_modal(string $id, string $title, string $bodyHtml = ''): string
{
    return '<div class="v2-modal" id="' . v2_e($id) . '" hidden><div class="v2-modal-backdrop" data-v2-modal-close></div><div class="v2-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="' . v2_e($id) . 'Title"><div class="v2-modal-heading"><h2 id="' . v2_e($id) . 'Title">' . v2_e($title) . '</h2><button type="button" class="v2-icon-button" data-v2-modal-close aria-label="Tutup">' . v2_icon('close', 18) . '</button></div><div class="v2-modal-body">' . $bodyHtml . '</div></div></div>';
}

function v2_alert(string $message, string $tone = 'info', string $title = ''): string
{
    return '<div class="v2-alert v2-alert--' . v2_e($tone) . '">' . ($title ? '<strong>' . v2_e($title) . '</strong>' : '') . '<span>' . v2_e($message) . '</span></div>';
}

function v2_progress(int $value, string $label = ''): string
{
    $safe = max(0, min(100, $value));
    return '<div class="v2-progress-wrap">' . ($label ? '<div class="v2-progress-label"><span>' . v2_e($label) . '</span><strong>' . $safe . '%</strong></div>' : '') . '<div class="v2-progress"><span style="width:' . $safe . '%"></span></div></div>';
}

function v2_tabs(array $tabs, string $active): string
{
    $html = '<div class="v2-tabs" role="tablist">';
    foreach ($tabs as $key => $label) {
        $html .= '<button type="button" class="v2-tab' . ((string)$key === $active ? ' is-active' : '') . '" data-v2-tab="' . v2_e((string)$key) . '" role="tab">' . v2_e((string)$label) . '</button>';
    }
    return $html . '</div>';
}

function v2_badge(string $label, string $tone = 'default'): string
{
    return '<span class="v2-badge v2-badge--' . v2_e($tone) . '">' . v2_e($label) . '</span>';
}

function v2_icon_button(string $icon, string $label, string $tone = 'default', array $attrs = []): string
{
    $attrs['aria-label'] = $attrs['aria-label'] ?? $label;
    $attrs['title'] = $attrs['title'] ?? $label;
    return '<button type="button" class="v2-icon-button v2-icon-button--' . v2_e($tone) . '"' . v2_attributes($attrs) . '>' . v2_icon($icon, 18) . '</button>';
}
