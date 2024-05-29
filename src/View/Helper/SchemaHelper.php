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
     * Default translatable fields to be prepended in translations
     *
     * @var array
     */
    public const DEFAULT_TRANSLATABLE = ['title', 'description', 'body'];

    /**
     * Translatable media types
     *
     * @var array
     */
    public const TRANSLATABLE_MEDIATYPES = ['text/html', 'text/plain'];

    /**
     * Get control options for a property schema.
     *
     * @param string $name Property name.
     * @param mixed $value Property value.
     * @param array|null $schema Property schema.
     * @return array
     */
    public function controlOptions(string $name, $value, ?array $schema = null): array
    {
        $options = Options::customControl($name, $value);
        $objectType = (string)$this->_View->get('objectType');
        $ctrlOptionsPath = sprintf('Properties.%s.options.%s', $objectType, $name);
        $ctrlOptions = (array)Configure::read($ctrlOptionsPath);

        if (!empty($options)) {
            return array_merge($options, [
                'label' => Hash::get($ctrlOptions, 'label'),
                'readonly' => Hash::get($ctrlOptions, 'readonly', false),
                'disabled' => Hash::get($ctrlOptions, 'readonly', false),
            ]);
        }
        if (empty($ctrlOptions['type'])) {
            $ctrlOptionsType = ControlType::fromSchema($schema);
            $ctrlOptions['type'] = $ctrlOptionsType;
            if (in_array($ctrlOptionsType, ['integer', 'number'])) {
                $ctrlOptions['step'] = $ctrlOptionsType === 'number' ? 'any' : '1';
                $ctrlOptions['type'] = 'number';
            }
        }
        // verify if there's a custom control handler for $type and $name
        $custom = $this->customControl($name, $value, $ctrlOptions);
        if (!empty($custom)) {
            return $custom;
        }
        $opts = [
            'objectType' => $objectType,
            'property' => $name,
            'value' => $value,
            'schema' => (array)$schema,
            'propertyType' => (string)$ctrlOptions['type'],
            'label' => Hash::get($ctrlOptions, 'label'),
            'readonly' => Hash::get($ctrlOptions, 'readonly', false),
            'disabled' => Hash::get($ctrlOptions, 'readonly', false),
        ];
        if (!empty($ctrlOptions['step'])) {
            $opts['step'] = $ctrlOptions['step'];
        }

        return Control::control($opts);
    }

    /**
     * Return custom control array if a custom handler has been defined or null otherwise.
     *
     * @param string $name Property name
     * @param mixed $value Property value.
     * @param array $options Control options.
     * @return array|null
     */
    protected function customControl($name, $value, array $options): ?array
    {
        $handlerClass = Hash::get($options, 'handler');
        if (empty($handlerClass)) {
            return null;
        }
        /** @var \App\Form\CustomHandlerInterface $handler */
        $handler = new $handlerClass();

        return $handler->control($name, $value, $options);
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
        return Number::toReadableSize((int)$value);
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

        return $res ? __('Yes') : __('No');
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
        return $this->formatDate($value);
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
        $isStringDate = $schema['type'] === 'string' && in_array($format, ['date', 'date-time']);

        return $isStringDate ? $format : $schema['type'];
    }

    /**
     * Provides list of translatable fields.
     * If set Properties.<objectType>.translatable, it will be added to translatable fields.
     *
     * @param array $schema The object type schema
     * @return array
     */
    public function translatableFields(array $schema): array
    {
        if (isset($schema['translatable'])) {
            $priorityFields = array_intersect(static::DEFAULT_TRANSLATABLE, (array)$schema['translatable']);
            $otherFields = array_diff((array)$schema['translatable'], $priorityFields);
        } else {
            $properties = (array)Hash::get($schema, 'properties');
            $priorityFields = array_intersect(static::DEFAULT_TRANSLATABLE, array_keys($properties));
            $otherFields = array_keys(array_filter(
                array_diff_key($properties, array_flip($priorityFields)),
                [$this, 'translatableType']
            ));
        }

        return array_unique(array_values(array_merge($priorityFields, $otherFields)));
    }

    /**
     * Helper recursive method to check if a property is translatable checking its JSON SCHEMA
     *
     * @param array $schema Property schema
     * @return bool
     */
    protected function translatableType(array $schema): bool
    {
        if (!empty($schema['oneOf'])) {
            return array_reduce(
                (array)$schema['oneOf'],
                function ($carry, $item) {
                    if ($carry) {
                        return true;
                    }

                    return $this->translatableType((array)$item);
                }
            );
        }
        // accept as translatable 'string' type having text/html or tex/plain 'contentMediaType'
        $type = (string)Hash::get($schema, 'type');
        $contentMediaType = Hash::get($schema, 'contentMediaType');

        return $type === 'string' &&
            in_array($contentMediaType, static::TRANSLATABLE_MEDIATYPES);
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
        // other types are sortable: 'string', 'number', 'integer', 'boolean', 'date-time', 'date'

        return !in_array($type, ['array', 'object']);
    }

    /**
     * Return unique right types from schema "relationsSchema".
     *
     * @return array
     * @deprecated It will be removed in version 5.x.
     */
    public function rightTypes(): array
    {
        $relationsSchema = (array)$this->_View->get('relationsSchema');

        return \App\Utility\Schema::rightTypes($relationsSchema);
    }

    /**
     * Get filter list from filters and schema properties
     *
     * @param array $filters Filters list
     * @param array|null $schemaProperties Schema properties
     * @return array
     */
    public function filterList(array $filters, ?array $schemaProperties): array
    {
        $list = [];
        foreach ($filters as $f) {
            $fname = is_array($f) ? (string)Hash::get($f, 'name', __('untitled')) : $f;
            $flabel = is_array($f) ? (string)Hash::get($f, 'label', $fname) : Inflector::humanize($f);
            $noProperties = empty($schemaProperties) || !array_key_exists($fname, $schemaProperties);
            $schema = $noProperties ? null : (array)Hash::get($schemaProperties, $fname);
            $item = self::controlOptions($fname, null, $schema);
            $item['name'] = $fname;
            $item['label'] = $flabel;
            $list[] = $item;
        }

        return $list;
    }

    /**
     * Get filter list by type
     *
     * @param array $filtersByType Filters list by type
     * @param array|null $schemasByType Schema properties by type
     * @return array
     */
    public function filterListByType(array $filtersByType, ?array $schemasByType): array
    {
        $list = [];
        foreach ($filtersByType as $type => $filters) {
            $noSchema = empty($schemasByType) || !array_key_exists($type, $schemasByType);
            $schemaProperties = $noSchema ? null : Hash::get($schemasByType, $type);
            $list[$type] = self::filterList($filters, $schemaProperties);
        }

        return $list;
    }
}
