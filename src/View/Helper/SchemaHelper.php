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

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Schema helper
 */
class SchemaHelper extends Helper
{

    /**
     * Infer control type from property schema.
     *
     * @param mixed $schema Property schema.
     * @return string
     */
    public function controlTypeFromSchema($schema) : string
    {
        if (!is_array($schema)) {
            return 'text';
        }

        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return $this->controlTypeFromSchema($subSchema);
            }
        }

        if (empty($schema['type'])) {
            return 'text';
        }

        switch ($schema['type']) {
            case 'object':
                return 'json';

            case 'string':
                if (!empty($schema['format']) && ($schema['format'] === 'date-time')) {
                    return 'date-time'; // TODO: replace with "datetime".
                }
                if (!empty($schema['format']) && $schema['format'] === 'date') {
                    return 'date';
                }
                if (!empty($schema['contentMediaType']) && $schema['contentMediaType'] === 'text/html') {
                    return 'textarea';
                }
                if (!empty($schema['enum']) && is_array($schema['enum'])) {
                    return 'enum';
                }

                return 'text';

            case 'number':
            case 'integer':
                return 'number';

            case 'boolean':
            case 'array':
                return 'checkbox';
        }

        return 'text';
    }

    /**
     * Get controls option by property name.
     *
     * @param string $name The field name.
     * @return array
     */
    protected function customControlOptions($name) : array
    {
        switch ($name) {
            case 'lang':
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

            case 'status':
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

            case 'old_password':
                return [
                    'class' => 'password',
                    'label' => __('Current password'),
                    'placeholder' => __('current password'),
                    'autocomplete' => 'current-password',
                    'type' => 'password',
                    'default' => '',
                ];

            case 'password':
                return [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                ];

            case 'confirm-password':
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

            case 'title':
                return [
                    'class' => 'title',
                    'type' => 'text',
                ];

            default:
                return [];
        }
    }

    /**
     * Get control options for a property schema.
     *
     * @param string $name Property name.
     * @param string $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public function controlOptions($name, $value, $schema) : array
    {
        $options = $this->customControlOptions($name);
        if ($options) {
            $options['value'] = $value;

            return $options;
        }

        $type = $this->controlTypeFromSchema($schema);
        if ($type === 'json') {
            return [
                'type' => 'textarea',
                'v-jsoneditor' => 'true',
                'class' => 'json',
                'value' => json_encode($value),
            ];
        } elseif ($type === 'textarea') {
            return [
                'type' => 'textarea',
                'v-richeditor' => 'true',
                'ckconfig' => 'configNormal',
                'value' => $value,
            ];
        } elseif ($type === 'date-time') {
            return [
                'type' => 'text',
                'v-datepicker' => 'true',
                'date' => 'true',
                'time' => 'true',
                'value' => $value,
            ];
        } elseif ($type === 'date') {
            return [
                'type' => 'text',
                'v-datepicker' => 'true',
                'date' => 'true',
                'time' => 'false',
                'value' => $value,
            ];
        } elseif ($type === 'checkbox') {
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
                'checked' => $value,
            ];
        } elseif ($type === 'enum') {
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

        return compact('type', 'value');
    }

    /**
     * Provides list of translatable fields per schema properties
     *
     * @param array $properties The array of schema properties
     * @return array
     */
    public function translatableFields(array $properties) : array
    {
        if (empty($properties)) {
            return [];
        }

        $fields = [];
        foreach ($properties as $name => $property) {
            if (!empty($property['oneOf'])) {
                foreach ($property['oneOf'] as $one) {
                    if (!empty($one['type']) && $one['type'] === 'null') {
                        continue;
                    }

                    if (!empty($one['type']) && !empty($one['contentMediaType']) && $one['type'] === 'string' && $one['contentMediaType'] === 'text/html') {
                        $fields[] = $name;
                    }
                }
            } elseif (!empty($property['type']) && $property['type'] === 'string') {
                if (!empty($property['contentMediaType']) && $property['contentMediaType'] === 'text/html') { // textarea
                    $fields[] = $name;
                }
            }
        }
        // put specific fields at the beginning of the fields array
        $prefields = array_reverse(['title', 'description']);
        foreach ($prefields as $field) {
            if (in_array($field, $fields)) {
                unset($fields[array_search($field, $fields)]);
                array_unshift($fields, $field);
            }
        }

        return $fields;
    }
}
