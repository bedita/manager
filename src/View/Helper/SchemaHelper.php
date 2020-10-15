<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\View\Helper;

use App\Form\Control;
use App\Form\ControlType;
use App\Form\Options;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Schema helper
 *
 * @property \Cake\View\Helper\TimeHelper $Time
 */
class SchemaHelper extends Helper
{
    /**
     * {@inheritDoc}
     *
     * @var array
     */
    public $helpers = ['Time'];

    /**
     * Get control options for a property schema.
     *
     * @param string $name Property name.
     * @param mixed $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public function controlOptions(string $name, $value, $schema = []): array
    {
        $options = Options::customControl($name, $value);
        if (!empty($options)) {
            return $options;
        }
        $type = ControlType::fromSchema((array)$schema);

        return Control::control((array)$schema, $type, $value);
    }

    /**
     * Display a formatted property value using schema.
     *
     * @param mixed $value Property value.
     * @param array $schema Property schema array.
     * @return string
     */
    public function format($value, $schema = []): string
    {
        $type = static::typeFromSchema((array)$schema);
        $type = Inflector::variable(str_replace('-', '_', $type));
        $methodName = sprintf('format%s', ucfirst($type));
        if (method_exists($this, $methodName) && $value !== null) {
            return call_user_func_array([$this, $methodName], [$value]);
        }
        if (is_array($value)) {
            return json_encode($value);
        }

        return (string)$value;
    }

    /**
     * Format boolean value
     *
     * @param mixed $value Property value.
     * @return string
     */
    protected function formatBoolean($value): string
    {
        $res = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return (string)($res ? __('Yes') : __('No'));
    }

    /**
     * Format date value
     *
     * @param mixed $value Property value.
     * @return string
     */
    protected function formatDate($value): string
    {
        return (string)$this->Time->format($value);
    }

    /**
     * Format date-time value
     *
     * @param mixed $value Property value.
     * @return string
     */
    protected function formatDateTime($value): string
    {
        return (string)$this->Time->format($value);
    }

    /**
     * Infer type from property schema in JSON-SCHEMA format
     * Possible return values:
     *
     *   'string'
     *   'number'
     *   'integer'
     *   'boolean'
     *   'array'
     *   'object'
     *   'date-time'
     *   'date'
     *
     * @param mixed $schema The property schema
     * @return string
     */
    public static function typeFromSchema(array $schema): string
    {
        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return static::typeFromSchema($subSchema);
            }
        }
        if (empty($schema['type']) || !in_array($schema['type'], ControlType::SCHEMA_PROPERTY_TYPES)) {
            return 'string';
        }
        $format = Hash::get($schema, 'format');
        if ($schema['type'] === 'string' && in_array($format, ['date', 'date-time'])) {
            return $format;
        }

        return $schema['type'];
    }

    /**
     * Provides list of translatable fields per schema properties
     *
     * @param array $properties The array of schema properties
     * @return array
     */
    public function translatableFields(array $properties): array
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
