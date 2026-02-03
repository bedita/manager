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

use Cake\Utility\Hash;

/**
 * ControlType class provides methods to get control types per type, according to schema property data.
 * Used by SchemaHelper to build control options for a property schema (@see \App\View\Helper\SchemaHelper::controlOptions)
 */
class ControlType
{
    /**
     * Schema property types
     */
    public const SCHEMA_PROPERTY_TYPES = ['string', 'number', 'integer', 'boolean', 'array', 'object', 'byte'];

    /**
     * Map JSON Schema `contentMediaType` to supported control types
     *
     * @var array
     */
    public const CONTENT_MEDIA_TYPES = [
        'text/html' => 'richtext',
        'text/plain' => 'plaintext',
    ];

    /**
     * Infer control type from property schema
     * Possible return values:
     *
     *   'categories'
     *   'checkbox'
     *   'checkboxNullable'
     *   'date-time'
     *   'date'
     *   'enum'
     *   'json'
     *   'number'
     *   'plaintext'
     *   'richtext'
     *   'text'
     *
     * @param array|null $schema The property schema
     * @param bool|null $canBeNull Whether the property can be null
     * @return string
     */
    public static function fromSchema(?array $schema, ?bool $canBeNull = false): string
    {
        if ($schema === null) {
            return 'text';
        }
        if (empty($schema)) {
            return 'json';
        }
        $schemaType = (string)Hash::get($schema, 'type');
        if ($schemaType === 'categories') {
            return $schemaType;
        }
        if (!empty($schema['oneOf'])) {
            $nullable = false;
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    $nullable = true;
                    continue;
                }

                return ControlType::fromSchema($subSchema, $nullable);
            }
        }
        if (!in_array($schemaType, ControlType::SCHEMA_PROPERTY_TYPES)) {
            return 'text';
        }
        $callable = Form::getMethod(ControlType::class, sprintf('from%s', ucfirst($schemaType)));
        $args = [$schema + compact('canBeNull')];

        return call_user_func_array($callable, $args);
    }

    /**
     * Get control type per string, according to schema property data.
     * Possibilities:
     *
     *    format 'date-time' => 'date-time'
     *    format 'date' => 'date'
     *    contentMediaType 'text/plain' => 'plaintext'
     *    contentMediaType 'text/html' => 'richtext'
     *    schema.enum is an array => 'enum'
     *
     * Return 'text' otherwise.
     *
     * @param array $schema The property schema
     * @return string
     */
    public static function fromString(array $schema): string
    {
        if (!empty($schema['format']) && in_array($schema['format'], ['date', 'date-time'])) {
            return $schema['format'];
        }
        $contentType = (string)Hash::get($schema, 'contentMediaType');
        $controlType = (string)Hash::get(self::CONTENT_MEDIA_TYPES, $contentType);
        if (!empty($controlType)) {
            return $controlType;
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
    public static function fromNumber(array $schema): string
    {
        return 'number';
    }

    /**
     * Return the type for integer: 'integer'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromInteger(array $schema): string
    {
        return 'integer';
    }

    /**
     * Return the type for boolean: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromBoolean(array $schema): string
    {
        if (Hash::get($schema, 'canBeNull') === true) {
            return 'checkboxNullable';
        }

        return 'checkbox';
    }

    /**
     * Return the type for array: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromArray(array $schema): string
    {
        $isArray = Hash::get($schema, 'type') === 'array';
        $uniqueItems = Hash::get($schema, 'uniqueItems') === true;
        $isEnumStrings = Hash::get($schema, 'items.type') === 'string' && !empty($schema['items']['enum']);
        if ($isArray && $uniqueItems && $isEnumStrings) {
            return 'enum';
        }

        return 'checkbox';
    }

    /**
     * Return the type for object: 'json'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromObject(array $schema): string
    {
        return 'json';
    }
}
