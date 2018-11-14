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

use App\Core\Utility\Form;
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
     * Get control options for a property schema.
     *
     * @param string $name Property name.
     * @param mixed $value Property value.
     * @param array $schema Object schema array.
     * @return array
     */
    public function controlOptions($name, $value, $schema) : array
    {
        $options = Form::customControlOptions($name);
        if (!empty($options)) {
            $options['value'] = $value;

            return $options;
        }
        $type = Form::controlTypeFromSchema($schema);

        return Form::control($schema, $type, $value);
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
