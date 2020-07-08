<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Core\Utility;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

class Form
{
    /**
     * Schema property types
     */
    public const SCHEMA_PROPERTY_TYPES = ['string', 'number', 'integer', 'boolean', 'array', 'object'];

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
    ];

    /**
     * Control types
     */
    public const CONTROL_TYPES = ['json', 'textarea', 'date-time', 'date', 'checkbox', 'enum', 'categories'];

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
        if (!in_array($type, Form::CONTROL_TYPES)) {
            return compact('type', 'value');
        }
        $method = Form::getMethod(sprintf('%sControl', $type));

        return call_user_func_array($method, [$value, $schema]);
    }

    /**
     * Control for json data
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    protected static function jsonControl($value): array
    {
        return [
            'type' => 'textarea',
            'v-jsoneditor' => 'true',
            'class' => 'json',
            'value' => json_encode($value),
        ];
    }

    /**
     * Control for textarea
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    protected static function textareaControl($value): array
    {
        return [
            'type' => 'textarea',
            'v-richeditor' => 'true',
            'ckconfig' => 'configNormal',
            'value' => $value,
        ];
    }

    /**
     * Control for datetime
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    protected static function datetimeControl($value): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'time' => 'true',
            'value' => $value,
        ];
    }

    /**
     * Control for date
     *
     * @param mixed|null $value Property value.
     * @return array
     */
    protected static function dateControl($value): array
    {
        return [
            'type' => 'text',
            'v-datepicker' => 'true',
            'date' => 'true',
            'value' => $value,
        ];
    }

    /**
     * Control for categories
     *
     * @param array $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    protected static function categoriesControl($value, array $schema): array
    {
        $categories = $schema['categories'];
        $options = array_map(
            function ($category) {
                return [
                    'value' => $category['name'],
                    'text' => $category['label'],
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
    protected static function checkboxControl($value, array $schema): array
    {
        if (empty($schema['oneOf'])) {
            return [
                'type' => 'checkbox',
                'checked' => (bool)$value,
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
            'checked' => (bool)$value,
        ];
    }

    /**
     * Control for enum
     *
     * @param mixed|null $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    protected static function enumControl($value, array $schema): array
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

    /**
     * Infer control type from property schema
     * Possible return values:
     *
     *   'text'
     *   'date-time'
     *   'date'
     *   'textarea'
     *   'enum'
     *   'number'
     *   'checkbox'
     *   'json'
     *   'categories'
     *
     * @param mixed $schema The property schema
     * @return string
     */
    public static function controlTypeFromSchema($schema): string
    {
        if (isset($schema['type']) && $schema['type'] === 'categories') {
            return 'categories';
        }
        if (!is_array($schema)) {
            return 'text';
        }
        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return Form::controlTypeFromSchema($subSchema);
            }
        }
        if (empty($schema['type']) || !in_array($schema['type'], Form::SCHEMA_PROPERTY_TYPES)) {
            return 'text';
        }
        $method = Form::getMethod(sprintf('typeFrom%s', ucfirst($schema['type'])));

        return call_user_func_array($method, [$schema]);
    }

    /**
     * Get controls option by property name.
     *
     * @param string $name The field name.
     * @return array
     */
    public static function customControlOptions($name): array
    {
        if (!in_array($name, Form::CUSTOM_CONTROLS)) {
            return [];
        }
        $method = Form::getMethod(sprintf('%sOptions', $name));

        return call_user_func_array($method, []);
    }

    /**
     * Options for lang, using configuration loaded from API
     * If available, use config `Project.config.I18n.languages`
     *
     * @return array
     */
    protected static function langOptions(): array
    {
        $languages = Configure::read('Project.config.I18n.languages');
        if (empty($languages)) {
            return [
                'type' => 'text',
            ];
        }
        $options = [];
        foreach ($languages as $key => $description) {
            $options[] = ['value' => $key, 'text' => __($description)];
        }

        return ['type' => 'select'] + compact('options');
    }

    /**
     * Options for `date_ranges`
     *
     * @return array
     */
    protected static function dateRangesOptions(): array
    {
        return static::datetimeControl(null);
    }

    /**
     * Options for `start_date`
     *
     * @return array
     */
    protected static function startDateOptions(): array
    {
        return static::datetimeControl(null);
    }

    /**
     * Options for `end_date`
     *
     * @return array
     */
    protected static function endDateOptions(): array
    {
        return static::datetimeControl(null);
    }

    /**
     * Options for `status` (radio).
     *
     *   - On ('on')
     *   - Draft ('draft')
     *   - Off ('off')
     *
     * @return array
     */
    protected static function statusOptions(): array
    {
        return [
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
     * @return array
     */
    protected static function oldpasswordOptions(): array
    {
        return [
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
     * @return array
     */
    protected static function passwordOptions(): array
    {
        return [
            'class' => 'password',
            'placeholder' => __('new password'),
            'autocomplete' => 'new-password',
            'default' => '',
        ];
    }

    /**
     * Options for confirm password
     *
     * @return array
     */
    protected static function confirmpasswordOptions(): array
    {
        return [
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
     * @return array
     */
    protected static function titleOptions(): array
    {
        return [
            'class' => 'title',
            'type' => 'text',
        ];
    }

    /**
     * Get control type per string, according to schema property data.
     * Possibilities:
     *
     *    format 'date-time' => 'date-time'
     *    format 'date' => 'date'
     *    contentMediaType => 'textarea'
     *    schema.enum is an array => 'enum'
     *
     * Return 'text' otherwise.
     *
     * @param array $schema The property schema
     * @return string
     */
    protected static function typeFromString(array $schema): string
    {
        if (!empty($schema['format']) && in_array($schema['format'], ['date', 'date-time'])) {
            return $schema['format'];
        }
        if (!empty($schema['contentMediaType']) && $schema['contentMediaType'] === 'text/html') {
            return 'textarea';
        }
        if (!empty($schema['enum']) && is_array($schema['enum'])) {
            return 'enum';
        }

        return 'text';
    }

    /**
     * Return the type for number: 'number'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    protected static function typeFromNumber(array $schema): string
    {
        return 'number';
    }

    /**
     * Return the type for integer: 'number'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    protected static function typeFromInteger(array $schema): string
    {
        return 'number';
    }

    /**
     * Return the type for boolean: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    protected static function typeFromBoolean(array $schema): string
    {
        return 'checkbox';
    }

    /**
     * Return the type for array: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    protected static function typeFromArray(array $schema): string
    {
        return 'checkbox';
    }

    /**
     * Return the type for object: 'json'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    protected static function typeFromObject(array $schema): string
    {
        return 'json';
    }

    /**
     * Return method [Form::class, $methodName], if it's callable.
     * Otherwise, throw \InvalidArgumentException
     *
     * @param string $name The method name
     * @return array
     */
    public static function getMethod(string $name): array
    {
        $methodName = Inflector::variable(str_replace('-', '_', $name));
        $method = [Form::class, $methodName];
        if (!is_callable($method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" is not callable', $methodName));
        }

        return $method;
    }
}
