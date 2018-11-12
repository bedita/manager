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

namespace App\Core\Util;

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
    public const CUSTOM_CONTROLS = ['lang', 'status', 'old_password', 'password', 'confirm-password', 'title'];

    /**
     * Control types
     */
    public const CONTROL_TYPES = ['json', 'textarea', 'date-time', 'date', 'checkbox', 'enum'];

    /**
     * Get control by schema, control type, and value
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    public static function control(array $schema, string $type, $value) : array
    {
        if (!in_array($type, Form::CONTROL_TYPES)) {
            return compact('type', 'value');
        }
        $method = sprintf('%sControl', $type);
        $method = str_replace('-', '', $method);

        return Form::{$method}($schema, $type, $value);
    }

    /**
     * Control for json data
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function jsonControl(array $schema, string $type, $value) : array
    {
        return [
            'type' => 'textarea',
            'v-jsoneditor' => '',
            'class' => 'json',
            'value' => json_encode($value),
        ];
    }

    /**
     * Control for textarea
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function textareaControl(array $schema, string $type, $value) : array
    {
        return [
            'type' => 'textarea',
            'v-richeditor' => '',
            'ckconfig' => 'configNormal',
            'value' => $value,
        ];
    }

    /**
     * Control for datetime
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function datetimeControl(array $schema, string $type, $value) : array
    {
        return [
            'type' => 'text',
            'v-datepicker' => '',
            'time' => 'true',
            'value' => $value,
        ];
    }

    /**
     * Control for date
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function dateControl(array $schema, string $type, $value) : array
    {
        return [
            'type' => 'text',
            'v-datepicker' => '',
            'time' => 'false',
            'value' => $value,
        ];
    }

    /**
     * Control for checkbox
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function checkboxControl(array $schema, string $type, $value) : array
    {
        if (!empty($schema['oneOf'])) {
            $options = [];
            foreach ($schema['oneOf'] as $one) {
                if (!empty($one['type']) && ($one['type'] === 'array')) {
                    $options = array_map(
                        function ($value) {
                            return ['value' => $value, 'text' => Inflector::humanize($value)];
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
        }

        return [
            'type' => 'checkbox',
            'checked' => (bool)$value,
        ];
    }

    /**
     * Control for enum
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @return array
     */
    private static function enumControl(array $schema, string $type, $value) : array
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
     *
     * @param mixed $schema The property schema
     * @return string
     */
    public static function controlTypeFromSchema($schema) : string
    {
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
        $method = sprintf('typeFrom%s', lcfirst($schema['type']));

        return Form::{$method}($schema);
    }

    /**
     * Get controls option by property name.
     *
     * @param string $name The field name.
     * @return array
     */
    public static function customControlOptions($name) : array
    {
        if (!in_array($name, Form::CUSTOM_CONTROLS)) {
            return [];
        }
        $method = sprintf('%sOptions', $name);
        $method = str_replace('-', '', $method);
        $method = str_replace('_', '', $method);

        return Form::{$method}();
    }

    /**
     * Options for lang
     * If available, use config `Project.I18n.languages`
     *
     * @return array
     */
    private static function langOptions() : array
    {
        $languages = Configure::read('Project.I18n.languages');
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
     * Options for `status` (radio).
     *
     *   - On ('on')
     *   - Draft ('draft')
     *   - Off ('off')
     *
     * @return array
     */
    private static function statusOptions() : array
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
    private static function oldpasswordOptions() : array
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
    private static function passwordOptions() : array
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
    private static function confirmpasswordOptions() : array
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
    private static function titleOptions() : array
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
    private static function typeFromString(array $schema) : string
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
    private static function typeFromNumber(array $schema) : string
    {
        return 'number';
    }

    /**
     * Return the type for integer: 'number'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    private static function typeFromInteger(array $schema) : string
    {
        return 'number';
    }

    /**
     * Return the type for boolean: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    private static function typeFromBoolean(array $schema) : string
    {
        return 'checkbox';
    }

    /**
     * Return the type for array: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    private static function typeFromArray(array $schema) : string
    {
        return 'checkbox';
    }

    /**
     * Return the type for object: 'json'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    private static function typeFromObject(array $schema) : string
    {
        return 'json';
    }
}
