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
        $fieldTypeClass = $type === 'checkbox' ? 'mb-field--checkbox ' : ($type === 'checklist' ? 'mb-field--checklist ' : '');
        $fieldAttrs = ['class' => trim('mb-field ' . $fieldTypeClass . ($field['field_class'] ?? ''))];
        if ($style) $fieldAttrs['style'] = $style;

        $wrapperTag = in_array($type, ['checklist', 'checklist-dropdown'], true) ? 'div' : 'label';
        echo '<' . $wrapperTag . mb_attrs($fieldAttrs) . '>';
        $labelAttrs = [];
        if (!empty($field['label_id'])) $labelAttrs['id'] = $field['label_id'];
        echo '<span class="mb-field-label"' . mb_attrs($labelAttrs) . '>' . mb_e($label) . '</span>';

        if ($type === 'select') {
            echo '<select id="' . mb_e($id) . '" class="' . mb_e($class) . '"' . mb_attrs($attrs) . '>';
            foreach (($field['options'] ?? []) as $value => $text) {
                $selected = ((string)($field['value'] ?? '') === (string)$value) ? ' selected' : '';
                echo '<option value="' . mb_e($value) . '"' . $selected . '>' . mb_e($text) . '</option>';
            }
            echo '</select>';
        } elseif ($type === 'checklist' || $type === 'checklist-dropdown') {
            $isDropdown = $type === 'checklist-dropdown';
            if ($isDropdown) {
                $checkedOptions = [];
                foreach (($field['options'] ?? []) as $value => $option) {
                    $option = is_array($option) ? $option : ['label' => $option];
                    if (!empty($option['checked'])) {
                        $optionId = $id . '_' . preg_replace('/[^a-z0-9_-]/i', '_', (string)$value);
                        $checkedOptions[] = ['id' => $optionId, 'label' => $option['label'] ?? $value];
                    }
                }
                $selectedHtml = '';
                foreach ($checkedOptions as $checkedOption) {
                    $selectedHtml .= '<span class="mb-checklist-tag">' . mb_e($checkedOption['label']) . '<button type="button" class="mb-checklist-tag__remove" data-mb-checklist-remove="' . mb_e($checkedOption['id']) . '" aria-label="Hapus ' . mb_e($checkedOption['label']) . '">×</button></span>';
                }
                $summary = count($checkedOptions) === 0
                    ? 'Semua Tahun'
                    : (count($checkedOptions) === count($field['options'] ?? [])
                        ? 'Semua Tahun'
                        : count($checkedOptions) . ' Tahun Dipilih');
                echo '<div class="mb-checklist-dropdown" data-mb-checklist-dropdown>';
                echo '<div class="mb-checklist-dropdown__toggle" data-mb-checklist-toggle role="button" tabindex="0" aria-expanded="false"><span class="mb-checklist-dropdown__summary" data-mb-checklist-summary>' . mb_e($summary) . '</span><span class="mb-checklist-dropdown__chevron" aria-hidden="true">⌄</span></div>';
                echo '<div class="mb-checklist-dropdown__selected" data-mb-checklist-selected>' . $selectedHtml . '</div>';
                echo '<div class="mb-checklist-dropdown__menu" data-mb-checklist-menu>';
            } else {
                echo '<div class="mb-checklist" role="group" aria-label="' . mb_e($label) . '">';
            }
            foreach (($field['options'] ?? []) as $value => $option) {
                $option = is_array($option) ? $option : ['label' => $option];
                $optionId = $id . '_' . preg_replace('/[^a-z0-9_-]/i', '_', (string)$value);
                $optionAttrs = $option['attrs'] ?? [];
                $optionAttrs['type'] = 'checkbox';
                $optionAttrs['id'] = $optionId;
                $optionAttrs['name'] = $field['name'] ?? ($id . '[]');
                $optionAttrs['value'] = (string)$value;
                $optionAttrs['class'] = trim('mb-checklist__input ' . ($option['class'] ?? ''));
                if (!empty($option['checked'])) $optionAttrs['checked'] = true;
                echo '<label class="mb-checklist__option" for="' . mb_e($optionId) . '">';
                echo '<input' . mb_attrs($optionAttrs) . '><span class="mb-checklist__mark" aria-hidden="true"></span><span class="mb-checklist__text">' . mb_e($option['label'] ?? $value) . '</span>';
                echo '</label>';
            }
            echo '</div>' . ($isDropdown ? '</div>' : '');
        } elseif ($type === 'checkbox') {
            $attrs['type'] = 'checkbox';
            $attrs['id'] = $id;
            $attrs['class'] = trim('mb-checkbox-control ' . ($field['class'] ?? ''));
            if (!empty($field['value'])) $attrs['checked'] = true;
            echo '<span class="mb-checkbox-shell"><input' . mb_attrs($attrs) . '><span class="mb-checkbox-mark" aria-hidden="true"></span></span>';
        } else {
            $attrs['type'] = $type;
            $attrs['id'] = $id;
            $attrs['class'] = $class;
            if (array_key_exists('value', $field)) $attrs['value'] = $field['value'];
            if (!empty($field['placeholder'])) $attrs['placeholder'] = $field['placeholder'];
            echo '<input' . mb_attrs($attrs) . '>';
        }
        echo '</' . $wrapperTag . '>';
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
        if ($infoModalId || !empty($cfg['info_button_onclick'])) {
            $infoAttrs = [
                'type' => 'button',
                'class' => 'mb-info-button',
                'aria-label' => 'Informasi ' . $title,
            ];
            if ($infoModalId) $infoAttrs['data-mb-open-modal'] = $infoModalId;
            if (!empty($cfg['info_button_id'])) $infoAttrs['id'] = $cfg['info_button_id'];
            if (!empty($cfg['info_button_onclick'])) $infoAttrs['onclick'] = $cfg['info_button_onclick'];
            echo '    <button' . mb_attrs($infoAttrs) . '>' . mb_svg('info') . '</button>';
        }
        echo '      </div>';
        if ($subtitle !== '') echo '<p class="mb-page-header__subtitle">' . mb_e($subtitle) . '</p>';
        echo '    </div>';
        $filterAttrs = [
            'type' => 'button',
            'class' => 'mb-filter-toggle',
            'data-mb-filter-target' => $filterPanelId,
            'aria-expanded' => 'false',
        ];
        if (!empty($cfg['filter_toggle_id'])) $filterAttrs['id'] = $cfg['filter_toggle_id'];
        if (!empty($cfg['filter_toggle_onclick'])) $filterAttrs['onclick'] = $cfg['filter_toggle_onclick'];
        echo '    <button' . mb_attrs($filterAttrs) . '>' . mb_svg('filter') . '<span>Filter</span></button>';
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
