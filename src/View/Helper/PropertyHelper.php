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
use Cake\Routing\Router;
use Cake\View\Helper;

class PropertyHelper extends Helper
{
    /**
     * Display input view for a property using schema info
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @return void
     */
    public function display($name, $value)
    {
        echo $this->_View->element(
                'Property/' . $this->selectElement($name),
                compact('name', 'value')
            );
    }

    /**
     * Select view element from JSON SCHEMA
     *
     * @param string $name Property name
     * @return string View element to use
     */
    protected function selectElement($name)
    {
        $schema = $this->_View->get('schema');
        if (empty($schema['properties'][$name])) {
            return 'string';
        }
        $property = $schema['properties'][$name];
        if (!empty($property['oneOf'])) {
            foreach ($property['oneOf'] as $data) {
                if ($data['type'] !== 'null') {
                    $property = array_merge($property, $data);
                }
            }
        }
        if ($property['type'] !== 'string') {
            return 'string';
        }
        if (!empty($property['format']) && $property['format'] === 'date-time') {
            return 'date';
        }
        if (!empty($property['contentMediaType']) && $property['contentMediaType'] === 'text/html') {
            return 'text';
        }
        return 'string';
    }
}
