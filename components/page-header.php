<?php
require_once __DIR__ . '/helpers.php';

if (!function_exists('mb_render_field')) {
    function mb_render_field(array $field): void
    {
        $type = $field['type'] ?? 'text';
        $id = $field['id'] ?? '';
        $label = $field['label'] ?? '';
        $class = trim('mb-field-control ' . ($field['class'] ?? ''));
        $style = !empty($field['width']) ? 'min-width:' . $field['width'] . ';width:' . $field['width'] . ';' : '';
        $attrs = $field['attrs'] ?? [];
        $fieldAttrs = [];
        if ($style) $fieldAttrs['style'] = $style;

        echo '<label class="mb-field"' . mb_attrs($fieldAttrs) . '>';
        echo '<span class="mb-field-label">' . mb_e($label) . '</span>';

        if ($type === 'select') {
            echo '<select id="' . mb_e($id) . '" class="' . mb_e($class) . '"' . mb_attrs($attrs) . '>';
            foreach (($field['options'] ?? []) as $value => $text) {
                $selected = ((string)($field['value'] ?? '') === (string)$value) ? ' selected' : '';
                echo '<option value="' . mb_e($value) . '"' . $selected . '>' . mb_e($text) . '</option>';
            }
            echo '</select>';
        } else {
            $attrs['type'] = $type;
            $attrs['id'] = $id;
            $attrs['class'] = $class;
            if (array_key_exists('value', $field)) $attrs['value'] = $field['value'];
            if (!empty($field['placeholder'])) $attrs['placeholder'] = $field['placeholder'];
            echo '<input' . mb_attrs($attrs) . '>';
        }
        echo '</label>';
    }
}

if (!function_exists('mb_render_page_header')) {
    function mb_render_page_header(array $cfg): void
    {
        $id = $cfg['id'] ?? 'mbPageHeader';
        $title = $cfg['title'] ?? 'Monbis';
        $subtitle = $cfg['subtitle'] ?? '';
        $icon = $cfg['icon'] ?? mb_svg('chart');
        $filterPanelId = $cfg['filter_panel_id'] ?? ($id . 'Filters');
        $infoModalId = $cfg['info_modal_id'] ?? '';

        echo '<section id="' . mb_e($id) . '" class="mb-page-header">';
        echo '  <div class="mb-page-header__identity">';
        echo '    <span class="mb-page-header__icon">' . $icon . '</span>';
        echo '    <div class="mb-page-header__copy">';
        echo '      <div class="mb-page-header__title-row">';
        echo '        <h1 class="mb-page-header__title">' . mb_e($title) . '</h1>';
        if ($infoModalId) {
            echo '    <button type="button" class="mb-info-button" data-mb-open-modal="' . mb_e($infoModalId) . '" aria-label="Informasi ' . mb_e($title) . '">' . mb_svg('info') . '</button>';
        }
        echo '      </div>';
        if ($subtitle !== '') echo '<p class="mb-page-header__subtitle">' . mb_e($subtitle) . '</p>';
        echo '    </div>';
        echo '    <button type="button" class="mb-filter-toggle" data-mb-filter-target="' . mb_e($filterPanelId) . '" aria-expanded="false">' . mb_svg('filter') . '<span>Filter</span></button>';
        echo '  </div>';

        echo '  <div id="' . mb_e($filterPanelId) . '" class="mb-page-header__filters">';
        echo '    <div class="mb-filter-row">';
        foreach (($cfg['filters'] ?? []) as $field) mb_render_field($field);

        if (!empty($cfg['actions'])) {
            echo '<div class="mb-header-actions">';
            foreach ($cfg['actions'] as $action) {
                $tone = $action['tone'] ?? 'primary';
                $label = $action['label'] ?? '';
                $iconName = $action['icon'] ?? 'download';
                $attrs = $action['attrs'] ?? [];
                $attrs['type'] = $action['type'] ?? 'button';
                $attrs['class'] = trim('mb-icon-button mb-icon-button--' . $tone . ' ' . ($action['class'] ?? ''));
                if (!empty($action['title'])) $attrs['title'] = $action['title'];
                echo '<button' . mb_attrs($attrs) . '>' . mb_svg($iconName);
                if ($label !== '') echo '<span class="mb-icon-button__label">' . mb_e($label) . '</span>';
                echo '</button>';
            }
            echo '</div>';
        }
        echo '    </div>';
        echo '  </div>';
        echo '</section>';
    }
}
