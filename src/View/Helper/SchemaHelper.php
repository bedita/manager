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
    public function getControlTypeFromSchema($schema) : string
    {
        if (!is_array($schema)) {
            return 'text';
        }

        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return $this->getControlTypeFromSchema($subSchema);
            }
        }

        if (empty($schema['type'])) {
            return 'text';
        }

        switch ($schema['type']) {
            case 'string':
                if (!empty($schema['format']) && $schema['format'] === 'date-time') {
                    return 'text'; // TODO: replace with "datetime".
                }
                if (!empty($schema['contentMediaType']) && $schema['contentMediaType'] === 'text/html') {
                    return 'textarea';
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
     * Get object type from property schema.
     *
     * @param mixed $schema Property schema.
     * @return string|null
     */
    public function getTypeFromSchema($schema) : ?string
    {
        if (!is_array($schema)) {
            return null;
        }

        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return $this->getTypeFromSchema($subSchema);
            }
        }

        if (empty($schema['type'])) {
            return null;
        }

        if ($schema['type'] === 'object') {
            return 'json';
        }

        return $schema['type'];
    }

    /**
     * Get control option object for field type, name and value.
     *
     * @param string $type The type (i.e. 'radio', 'text', 'textarea', 'json', etc.)
     * @param string $name The field name.
     * @param string $value The field value.
     * @return array
     */
    public function getControlOptionFromTypeAndName($type, $name, $value) : array
    {
        if ($name === 'status') {
            return [
                'type' => 'radio',
                'options' => [
                    ['value' => 'on', 'text' => __('On')],
                    ['value' => 'draft', 'text' => __('Draft')],
                    ['value' => 'off', 'text' => __('Off')],
                ],
            ];
        }
        if ($name === 'password') {
            return [
                'class' => 'password',
                'placeholder' => __('new password'),
                'autocomplete' => 'new-password',
                'default' => '',
            ];
        }
        if ($name === 'confirm-password') {
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
        if ($type === 'json') {
            return [
                'type' => 'textarea',
                'class' => 'json',
                'value' => json_encode($value),
            ];
        }

        return compact('type');
    }
}
