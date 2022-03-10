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
     *   'date-time'
     *   'date'
     *   'enum'
     *   'json'
     *   'number'
     *   'plaintext'
     *   'richtext'
     *   'text'
     *
     * @param mixed $schema The property schema
     * @return string
     */
    public static function fromSchema($schema): string
    {
        if (!is_array($schema)) {
            return 'text';
        }
        $schemaType = Hash::get($schema, 'type', null);
        if ($schemaType === 'categories') {
            return $schemaType;
        }
        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return ControlType::fromSchema($subSchema);
            }
        }
        if (empty($schemaType) || !in_array($schemaType, ControlType::SCHEMA_PROPERTY_TYPES)) {
            return 'text';
        }
        $method = Form::getMethod(ControlType::class, sprintf('from%s', ucfirst($schemaType)));

        return call_user_func_array($method, [$schema]);
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
     * Return the type for integer: 'number'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromInteger(array $schema): string
    {
        return 'number';
    }

    /**
     * Return the type for boolean: 'checkbox'
     *
     * @param array $schema Object schema array.
     * @return string
     */
    public static function fromBoolean(array $schema): string
    {
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
