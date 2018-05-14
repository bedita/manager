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
                if (!empty($schema['format']) && $schema['format'] === 'date-time') {
                    return 'date-time'; // TODO: replace with "datetime".
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

            case 'password':
                return [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                    'v-validate' => 'validate.password', // vue directive for vee-validate validation schema
                    ':class' => 'validateResult("password")', // validation results method (ModulesView component)
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
                    'v-validate' => 'validate.confirmPassword', // vue directive for vee-validate validation schema
                    ':class' => 'validateResult("confirm-password")', // validation results method (ModulesView component)
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
            return $options;
        }

        $type = $this->controlTypeFromSchema($schema);
        if ($type === 'json') {
            return [
                'type' => 'textarea',
                'v-jsoneditor' => '',
                'class' => 'json',
                'value' => json_encode($value),
            ];
        } elseif ($type === 'textarea') {
            return [
                'type' => 'textarea',
                'v-richeditor' => '',
                'ckconfig' => 'configNormal',
            ];
        } elseif ($type === 'date-time') {
            return [
                'type' => 'text',
                'v-datepicker' => '',
                'time' => 'true',
            ];
        } elseif ($type === 'checkbox') {
            return [
                'type' => 'checkbox',
                'checked' => $value,
            ];
        } elseif ($type === 'enum') {
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
}
