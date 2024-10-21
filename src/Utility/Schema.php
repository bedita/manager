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

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Utility class to get schema data
 */
class Schema
{
    /**
     * Cache config name for roles.
     *
     * @var string
     */
    public const CACHE_CONFIG = '_roles_';

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

    /**
     * Fetch roles from API.
     * If roles are already in cache, return them.
     *
     * @return array
     */
    public static function roles(): array
    {
        $roles = Configure::read('Roles');
        if (!empty($roles)) {
            return $roles;
        }
        try {
            $roles = Cache::remember(
                CacheTools::cacheKey('roles'),
                function () {
                    $response = ApiClientProvider::getApiClient()->get('/roles', ['page_size' => 100]);
                    $data = (array)Hash::get($response, 'data', []);
                    $roles = Hash::combine($data, '{n}.attributes.name', '{n}.meta.priority');

                    return $roles;
                },
                self::CACHE_CONFIG
            );
            Configure::write('Roles', $roles);
        } catch (BEditaClientException $e) {
            // Something bad happened

            return [];
        }

        return $roles;
    }
}
