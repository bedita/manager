<?php

declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
 * Relations tools
 */
class RelationsTools
{
    /**
     * Convert relations to string.
     *
     * @param array $relations Relations to convert.
     * @return string
     */
    public static function toString(array $relations): string
    {
        $rrs = [];
        foreach ($relations as $item) {
            $id = (string)Hash::get($item, 'id');
            $type = (string)Hash::get($item, 'type');
            $meta = json_encode((array)Hash::get($item, 'meta.relation', []));
            $rrs[] = sprintf(
                '%s-%s-%s',
                $id,
                $type,
                $meta
            );
        }

        return implode(',', $rrs);
    }
}
