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
     * Get control by options
     *
     * @param array $options The options
     * @return array
     */
    public static function control(array $options): array
    {
        $type = $options['propertyType'];
        $value = $options['value'];
        $format = self::format((array)$options['schema']);
        if ($type === 'text' && in_array($format, ['email', 'uri'])) {
            return call_user_func_array(Form::getMethod(self::class, $type, $format), [$options]);
        }
        if (!in_array($type, self::CONTROL_TYPES)) {
            return compact('type', 'value');
        }

        return call_user_func_array(Form::getMethod(self::class, $type), [$options]);
    }

    /**
     * Get format from schema
     *
     * @param array $schema The schema
     * @return string
     */
    public static function format(array $schema): string
    {
        if (empty($schema['oneOf'])) {
            return '';
        }
        foreach ($schema['oneOf'] as $item) {
            if (array_key_exists('format', $item)) {
                return (string)$item['format'];
            }
        }

        return '';
    }

    /**
     * Control for json data
     *
     * @param array $options The options
     * @return array
     */
    public static function json(array $options): array
    {
        return [
            'type' => 'textarea',
            'v-jsoneditor' => 'true',
            'class' => 'json',
            'value' => json_encode(Hash::get($options, 'value')),
        ];
    }

    /**
     * Control for plaintext
     *
     * @param array $options The options
     * @return array
     */
    public static function plaintext(array $options): array
    {
        return [
            'type' => 'textarea',
            'value' => Hash::get($options, 'value'),
        ];
    }

    /**
     * Control for richtext
     *
     * @param array $options The options
     * @return array
     */
    public static function richtext(array $options): array
    {
        $schema = (array)Hash::get($options, 'schema');
        $value = Hash::get($options, 'value');
        $key = !empty($schema['placeholders']) ? 'v-richeditor.placeholders' : 'v-richeditor';

        return [
            'type' => 'textarea',
            $key => json_encode(Configure::read('RichTextEditor.default.toolbar', '')),
            'value' => $value,
        ];
    }

    /**
     * Control for datetime
     *
     * @param array $options The options
     * @return array
     */
    public static function datetime(array $options): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'time' => 'true',
            'value' => Hash::get($options, 'value'),
            'templates' => [
                'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
            ],
        ];
    }

    /**
     * Control for date
     *
     * @param array $options The options
     * @return array
     */
    public static function date(array $options): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'value' => Hash::get($options, 'value'),
            'templates' => [
                'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
            ],
        ];
    }

    /**
     * Control for categories
     *
     * @param array $options The options
     * @return array
     */
    public static function categories(array $options): array
    {
        $schema = (array)Hash::get($options, 'schema');
        $value = Hash::get($options, 'value');
        $categories = (array)Hash::get($schema, 'categories');
        $options = array_map(
            function ($category) {
                return [
                    'value' => Hash::get($category, 'name'),
                    'text' => empty($category['label']) ? $category['name'] : $category['label'],
                ];
            },
            $categories
        );

        $checked = [];
        if (!empty($value)) {
            $names = (array)Hash::extract($value, '{n}.name');
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
     * @param array $options The options
     * @return array
     */
    public static function checkbox(array $options): array
    {
        $schema = (array)Hash::get($options, 'schema');
        $value = Hash::get($options, 'value');
        if (empty($schema['oneOf'])) {
            return [
                'type' => 'checkbox',
                'checked' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        $options = [];
        foreach ($schema['oneOf'] as $one) {
            self::oneOptions($options, $one);
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
     * Set options for one of `oneOf` items.
     *
     * @param array $options The options to update
     * @param array $one The one item to check
     * @return void
     */
    public static function oneOptions(array &$options, array $one): void
    {
        $type = Hash::get($one, 'type');
        if ($type !== 'array') {
            return;
        }
        $options = array_map(
            function ($item) {
                return ['value' => $item, 'text' => Inflector::humanize($item)];
            },
            (array)Hash::extract($one, 'items.enum')
        );
    }

    /**
     * Control for enum
     *
     * @param array $options The options
     * @return array
     */
    public static function enum(array $options): array
    {
        $schema = (array)Hash::get($options, 'schema');
        $value = Hash::get($options, 'value');
        $objectType = Hash::get($options, 'objectType');
        $property = Hash::get($options, 'property');

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
                function (string $value) use ($objectType, $property) {
                    $text = self::label((string)$objectType, (string)$property, $value);

                    return compact('text', 'value');
                },
                $schema['enum']
            ),
            'value' => $value,
        ];
    }

    /**
     * Control for email
     *
     * @param array $options The options
     * @return array
     */
    public static function email(array $options): array
    {
        return [
            'type' => 'text',
            'v-email' => 'true',
            'class' => 'email',
            'value' => Hash::get($options, 'value'),
        ];
    }

    /**
     * Control for uri
     *
     * @param array $options The options
     * @return array
     */
    public static function uri(array $options): array
    {
        return [
            'type' => 'text',
            'v-uri' => 'true',
            'class' => 'uri',
            'value' => Hash::get($options, 'value'),
        ];
    }

    /**
     * Label for property.
     * If set in config Properties.<type>.labels.options.<property>, return it.
     * Return humanize of value, otherwise.
     *
     * @param string $type The object type
     * @param string $property The property name
     * @param string $value The value
     * @return string
     */
    public static function label(string $type, string $property, string $value): string
    {
        $label = Configure::read(sprintf('Properties.%s.labels.options.%s', $type, $property));
        if (empty($label)) {
            return Inflector::humanize($value);
        }
        $labelVal = (string)Configure::read(sprintf('Properties.%s.labels.options.%s.%s', $type, $property, $value));
        if (empty($labelVal)) {
            return Inflector::humanize($value);
        }

        return $labelVal;
    }
}
