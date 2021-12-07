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
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Control class provides methods for form controls, customized by control type.
 * Used by SchemaHelper to build control options for a property schema (@see \App\View\Helper\SchemaHelper::controlOptions)
 */
class Control
{
    /**
     * Control types
     */
    public const CONTROL_TYPES = ['json', 'richtext', 'plaintext', 'date-time', 'date', 'checkbox', 'enum', 'categories'];

    /**
     * Get control by schema, control type, and value
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function control(array $schema, string $type, $value): array
    {
        if (!in_array($type, self::CONTROL_TYPES)) {
            return compact('type', 'value');
        }

        return call_user_func_array(Form::getMethod(self::class, $type), [$value, $schema]);
    }

    /**
     * Control for json data
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function json($value): array
    {
        return [
            'type' => 'textarea',
            'v-jsoneditor' => 'true',
            'class' => 'json',
            'value' => json_encode($value),
        ];
    }

    /**
     * Control for plaintext
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function plaintext($value): array
    {
        return [
            'type' => 'textarea',
            'value' => $value,
        ];
    }

    /**
     * Control for richtext
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function richtext($value): array
    {
        return [
            'type' => 'textarea',
            'v-richeditor' => json_encode(Configure::read('RichTextEditor.default.toolbar', '')),
            'value' => $value,
        ];
    }

    /**
     * Control for datetime
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function datetime($value): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'time' => 'true',
            'value' => $value,
            'templates' => [
                'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
            ],
        ];
    }

    /**
     * Control for date
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function date($value): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'value' => $value,
            'templates' => [
                'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
            ],
        ];
    }

    /**
     * Control for categories
     *
     * @param array $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public static function categories($value, array $schema): array
    {
        $categories = $schema['categories'];
        $options = array_map(
            function ($category) {
                return [
                    'value' => $category['name'],
                    'text' => empty($category['label']) ? $category['name'] : $category['label'],
                ];
            },
            $categories
        );

        $checked = [];
        if (!empty($value)) {
            $names = Hash::extract($value, '{n}.name');
            foreach ($categories as $category) {
                if (in_array($category['name'], $names)) {
                    $checked[] = $category['name'];
                }
            }
        }

        return [
            'type' => 'select',
            'options' => $options,
            'multiple' => 'checkbox',
            'value' => $checked,
        ];
    }

    /**
     * Control for checkbox
     *
     * @param mixed|null $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public static function checkbox($value, array $schema): array
    {
        if (empty($schema['oneOf'])) {
            return [
                'type' => 'checkbox',
                'checked' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        $options = [];
        foreach ($schema['oneOf'] as $one) {
            if (!empty($one['type']) && ($one['type'] === 'array')) {
                $options = array_map(
                    function ($item) {
                        return ['value' => $item, 'text' => Inflector::humanize($item)];
                    },
                    (array)Hash::extract($one, 'items.enum')
                );
            }
        }
        if (!empty($options)) {
            return [
                'type' => 'select',
                'options' => $options,
                'multiple' => 'checkbox',
            ];
        }

        return [
            'type' => 'checkbox',
            'checked' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * Control for enum
     *
     * @param mixed|null $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public static function enum($value, array $schema): array
    {
        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $one) {
                if (!empty($one['enum'])) {
                    $schema['enum'] = $one['enum'];
                    array_unshift($schema['enum'], '');
                }
            }
        }

        return [
            'type' => 'select',
            'options' => array_map(
                function ($value) {
                    return ['value' => $value, 'text' => Inflector::humanize($value)];
                },
                $schema['enum']
            ),
        ];
    }
}
