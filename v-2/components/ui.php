<?php
function v2_e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function v2_button(string $label, string $tone = 'primary', string $icon = '', array $attrs = []): string
{
    $attr = '';
    foreach ($attrs as $key => $value) $attr .= ' ' . v2_e($key) . '="' . v2_e((string)$value) . '"';
    return '<button type="button" class="v2-button v2-button--' . v2_e($tone) . '"' . $attr . '>'
        . ($icon ? v2_icon($icon, 16) : '') . '<span>' . v2_e($label) . '</span></button>';
}

function v2_input(string $id, string $label, string $value = '', string $placeholder = '', string $type = 'text'): string
{
    return '<label class="v2-field" for="' . v2_e($id) . '"><span>' . v2_e($label) . '</span><input id="' . v2_e($id) . '" name="' . v2_e($id) . '" type="' . v2_e($type) . '" value="' . v2_e($value) . '" placeholder="' . v2_e($placeholder) . '"></label>';
}

function v2_select(string $id, string $label, array $options, string $selected = ''): string
{
    $html = '<label class="v2-field" for="' . v2_e($id) . '"><span>' . v2_e($label) . '</span><select id="' . v2_e($id) . '" name="' . v2_e($id) . '">';
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
