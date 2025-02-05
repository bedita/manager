<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
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
 * Api utility methods
 */
class ApiTools
{
    /**
     * Clean response.
     *
     * @param array $response The response.
     * @param array $remove The keys to remove.
     * @return array
     */
    public static function cleanResponse(
        array $response,
        array $remove = [
            'fromData' => ['links', 'relationships'],
            'fromMeta' => ['schema'],
        ]
    ): array {
        $data = (array)Hash::get($response, 'data');
        $removeFromData = (array)Hash::get($remove, 'fromData');
        foreach ($data as &$item) {
            foreach ($removeFromData as $key) {
                unset($item[$key]);
            }
        }
        $meta = (array)Hash::get($response, 'meta');
        $removeFromMeta = (array)Hash::get($remove, 'fromMeta');
        foreach ($meta as $metaKey => &$item) {
            if (in_array($metaKey, $removeFromMeta)) {
                unset($meta[$metaKey]);
            }
        }

        return compact('data', 'meta');
    }
}
