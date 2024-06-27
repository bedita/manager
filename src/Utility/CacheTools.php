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

use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

/**
 * Add cache related utility methods
 */
class CacheTools
{
    /**
     * Create multi project cache key.
     *
     * @param string $name Cache item name.
     * @return string
     */
    public static function cacheKey(string $name): string
    {
        $apiSignature = md5(ApiClientProvider::getApiClient()->getApiBaseUrl());

        return sprintf('%s_%s', $name, $apiSignature);
    }

    /**
     * Check if module count exists in cache.
     *
     * @param string $moduleName Module name.
     * @return bool
     */
    public static function existsCount(string $moduleName): bool
    {
        $name = sprintf('statistics_%s_count', $moduleName);
        $cacheKey = self::cacheKey($name);

        return Cache::read($cacheKey) !== null;
    }

    /**
     * Get module count from cache.
     *
     * @param string $moduleName Module name.
     * @return string
     */
    public static function getModuleCount(string $moduleName): string
    {
        $name = sprintf('statistics_%s_count', $moduleName);
        $cacheKey = self::cacheKey($name);
        $count = Cache::read($cacheKey);

        return is_int($count) ? (int)$count : '-';
    }

    /**
     * Set module count in cache.
     *
     * @param array $response API response.
     * @param string $moduleName Module name.
     * @return void
     */
    public static function setModuleCount(array $response, string $moduleName): void
    {
        $count = Hash::get($response, 'meta.pagination.count', 0);
        $name = sprintf('statistics_%s_count', $moduleName);
        $cacheKey = self::cacheKey($name);
        Cache::write($cacheKey, $count);
    }
}
