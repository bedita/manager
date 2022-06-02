<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Utility;

use Cake\Utility\Hash;

/**
 * Utility class to get schema data
 */
class Schema
{
    /**
     * Return unique alphabetically ordered right types from schema $schema
     *
     * @param array $schema Schema data.
     * @return array
     */
    public static function rightTypes(array $schema): array
    {
        $relationsRightTypes = (array)Hash::extract($schema, '{s}.right');
        $types = [];
        foreach ($relationsRightTypes as $rightTypes) {
            $types = array_unique(array_merge($types, $rightTypes));
        }
        sort($types);

        return $types;
    }
}
