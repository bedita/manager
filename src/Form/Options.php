<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Form;

use Cake\Core\Configure;

/**
 * Options class provides methods to get custom control options, according to schema property data.
 * Used by SchemaHelper to build control options for a property schema (@see \App\View\Helper\SchemaHelper::controlOptions)
 */
class Options
{
    /**
     * Custom controls
     */
    public const CUSTOM_CONTROLS = [
        'confirm-password',
        'date_ranges',
        'end_date',
        'lang',
        'old_password',
        'password',
        'start_date',
        'status',
        'title',
        'coords',
        'children_order',
    ];

    /**
     * Get controls option by property name.
     *
     * @param string $name The field name.
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function customControl(string $name, mixed $value): array
    {
        if (!in_array($name, Options::CUSTOM_CONTROLS)) {
            return [];
        }
        $method = Form::getMethod(Options::class, $name);
        $options = call_user_func_array($method, [$value]);
        ksort($options);

        return $options;
    }

    /**
     * Options for lang, using configuration loaded from API
     * If available, use config `Project.config.I18n.languages`.
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function lang(mixed $value): array
    {
        $languages = Configure::read('Project.config.I18n.languages');
        if (empty($languages)) {
            return compact('value') + ['type' => 'text'];
        }
        $options[] = ['value' => '', 'text' => ''];
        foreach ($languages as $key => $description) {
            $options[] = ['value' => $key, 'text' => __($description)];
        }

        return ['type' => 'select'] + compact('options', 'value');
    }

    /**
     * Options for `date_ranges`
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function dateRanges(mixed $value): array
    {
        return Control::datetime(compact('value'));
    }

    /**
     * Options for `start_date`
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function startDate(mixed $value): array
    {
        return Control::datetime(compact('value'));
    }

    /**
     * Options for `end_date`
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function endDate(mixed $value): array
    {
        return Control::datetime(compact('value'));
    }

    /**
     * Options for `status` (radio).
     *
     *   - On ('on')
     *   - Draft ('draft')
     *   - Off ('off')
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function status(mixed $value): array
    {
        return compact('value') + [
            'type' => 'radio',
            'options' => [
                ['value' => 'on', 'text' => __('On')],
                ['value' => 'draft', 'text' => __('Draft')],
                ['value' => 'off', 'text' => __('Off')],
            ],
            'templateVars' => [
                'containerClass' => 'status',
            ],
        ];
    }

    /**
     * Options for old password
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function oldPassword(mixed $value): array
    {
        return compact('value') + [
            'class' => 'password',
            'label' => __('Current password'),
            'placeholder' => __('current password'),
            'autocomplete' => 'current-password',
            'type' => 'password',
            'default' => '',
        ];
    }

    /**
     * Options for password
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function password(mixed $value): array
    {
        return compact('value') + [
            'class' => 'password',
            'placeholder' => __('new password'),
            'autocomplete' => 'new-password',
            'default' => '',
        ];
    }

    /**
     * Options for confirm password
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function confirmPassword(mixed $value): array
    {
        return compact('value') + [
            'label' => __('Retype password'),
            'id' => 'confirm_password',
            'name' => 'confirm-password',
            'class' => 'confirm-password',
            'placeholder' => __('confirm password'),
            'autocomplete' => 'new-password',
            'default' => '',
            'type' => 'password',
        ];
    }

    /**
     * Options for title
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function title(mixed $value): array
    {
        return compact('value') + [
            'class' => 'title',
            'type' => 'text',
            'templates' => [
                'inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>',
            ],
        ];
    }

    /**
     * Options for coords
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function coords(mixed $value): array
    {
        $label = sprintf('<label>%s</label>', __('Long Lat Coordinates'));
        $options = json_encode((array)Configure::read('Location.google'));
        $coordinatesView = sprintf('<coordinates-view coordinates="%s" options=%s />', $value, $options);

        return [
            'type' => 'readonly',
            'class' => 'coordinates',
            'templates' => [
                'inputContainer' => sprintf('<div class="input coordinates {{type}}{{required}}">%s%s</div>', $label, $coordinatesView),
            ],
        ];
    }

    /**
     * Children order
     *
     * @param mixed|null $value The field value.
     * @return array
     */
    public static function childrenOrder(mixed $value): array
    {
        return compact('value') + [
            'type' => 'select',
            'options' => [
                ['value' => 'position', 'text' => __('Position ↑')],
                ['value' => '-position', 'text' => __('Position ↓')],
                ['value' => 'title', 'text' => __('Title ↑')],
                ['value' => '-title', 'text' => __('Title ↓')],
                ['value' => 'created', 'text' => __('Created ↑ Oldest on top')],
                ['value' => '-created', 'text' => __('Created ↓ Newest on top')],
                ['value' => 'modified', 'text' => __('Modified ↑ Oldest on top')],
                ['value' => '-modified', 'text' => __('Modified ↓ Newest on top')],
                ['value' => 'publish_start', 'text' => __('Publish date ↑ Oldest on top')],
                ['value' => '-publish_start', 'text' => __('Publish date ↓ Newest on top')],
            ],
            'templateVars' => [
                'containerClass' => 'childrenOrder',
            ],
        ];
    }
}
