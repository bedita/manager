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
use Cake\Core\Configure;
use Cake\I18n\Number;
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
     * Untranslatable fields
     *
     * @var array
     */
    public const UNTRANSLATABLE = [
        'id',
        'uname',
        'status',
        'lang',
        'locked',
        'published',
        'created',
        'modified',
        'created_by',
        'modified_by',
        'publish_start',
        'publish_end',
        'categories',
        'tags',
        'extra',
    ];

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
        // verify if there's an handler by $type.$name
        $objectType = (string)$this->_View->get('objectType');
        if (!empty(Configure::read(sprintf('Control.handlers.%s.%s', $objectType, $name)))) {
            $handler = Configure::read(sprintf('Control.handlers.%s.%s', $objectType, $name));

            return call_user_func_array([$handler['class'], $handler['method']], [$value, $schema]);
        }
        $type = ControlType::fromSchema((array)$schema);

        return Control::control([
            'objectType' => $objectType,
            'property' => $name,
            'value' => $value,
            'schema' => (array)$schema,
            'propertyType' => $type,
        ]);
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
     * Format byte value
     *
     * @param mixed $value Property value.
     * @return string
     */
    protected function formatByte($value): string
    {
        return (string)Number::toReadableSize((int)$value);
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
        if (empty($value)) {
            return '';
        }

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
        if (empty($value)) {
            return '';
        }

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
     * @param array $schema The property schema
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
     * @param string $objectType The object type
     * @return array
     */
    public function translatableFields(array $properties, ?string $objectType = null): array
    {
        $fields = array_diff(
            array_keys($properties),
            self::UNTRANSLATABLE
        );
        array_walk(
            $properties,
            function (&$schema, $field, $accepted) {
                $schema['translatable'] = in_array($field, $accepted)
                    && in_array(self::typeFromSchema($schema), ['object', 'string']);
            },
            array_values($fields)
        );
        $properties = array_filter($properties, function ($property) {
            return $property['translatable'] === true;
        });
        $translatable = array_keys($properties);
        $translatable = array_unique(array_merge(
            (array)Configure::read(sprintf('Properties.%s.translatable', (string)$objectType)),
            $translatable
        ));
        usort($translatable, function ($item1, $item2) {
            if ($item1 === 'title') {
                return -1;
            }
            if ($item2 === 'title') {
                return 1;
            }
            if ($item1 === 'description') {
                return -1;
            }
            if ($item2 === 'description') {
                return 1;
            }
            if ($item1 === 'body') {
                return -1;
            }
            if ($item2 === 'body') {
                return 1;
            }

            return $item1 > $item2;
        });

        return $translatable;
    }

    /**
     * Verify field's schema, return true if field is sortable.
     *
     * @param string $field The field to check
     * @return bool
     */
    public function sortable(string $field): bool
    {
        // exception 'date_ranges' default sortable
        if ($field === 'date_ranges') {
            return true;
        }
        $schema = (array)$this->_View->get('schema');
        $schema = Hash::get($schema, sprintf('properties.%s', $field), []);

        // empty schema, then not sortable
        if (empty($schema)) {
            return false;
        }
        $type = self::typeFromSchema($schema);

        // not sortable: 'array', 'object'
        if (in_array($type, ['array', 'object'])) {
            return false;
        }
        // other types are sortable: 'string', 'number', 'integer', 'boolean', 'date-time', 'date'

        return true;
    }

    /**
     * Return unique right types from schema "relationsSchema".
     *
     * @return array
     */
    public function rightTypes(): array
    {
        $relationsSchema = (array)$this->_View->get('relationsSchema');

        return \App\Utility\Schema::rightTypes($relationsSchema);
    }
}
