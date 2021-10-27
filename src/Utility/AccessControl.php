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

use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Utility class to handle Access Control data
 */
class AccessControl
{
    /**
     * Access control filtered data by module name and user roles.
     * This looks into `AccessControl` config, to find <role>.filtered.<module> config(s).
     * Return config(s) or empty array.
     *
     * @param string $moduleName The module name
     * @param array $roles The user roles
     * @return array
     */
    public static function filtered(string $moduleName, array $roles): array
    {
        $accessControl = (array)Configure::read('AccessControl');
        if (empty($accessControl)) {
            return [];
        }
        $result = [];
        foreach ($roles as $roleName) {
            $filtered = (array)Hash::get($accessControl, sprintf('%s.filtered.%s', $roleName, $moduleName));
            if (!empty($filtered)) {
                $result[] = $filtered;
            }
        }

        return $result;
    }

    /**
     * Access control filtered class and method data by module name and user roles.
     *
     * @param string $moduleName The module name
     * @param array $roles The user roles
     * @return array
     */
    public static function filteredClassMethod(string $moduleName, array $roles): array
    {
        $filtered = self::filtered($moduleName, $roles);
        foreach ($filtered as $cfg) {
            if (!empty($cfg['class']) && !empty($cfg['method'])) {
                return $cfg;
            }
        }

        return [];
    }

    /**
     * Access control filtered query by module name and user roles.
     *
     * @param string $moduleName The module name
     * @param array $roles The user roles
     * @return array
     */
    public static function filteredQuery(string $moduleName, array $roles): array
    {
        $filtered = self::filtered($moduleName, $roles);
        foreach ($filtered as $cfg) {
            if (!empty($cfg['query'])) {
                return $cfg['query'];
            }
        }

        return [];
    }
}
