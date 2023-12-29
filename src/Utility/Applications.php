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
use Cake\Utility\Hash;

/**
 * Applications class, to get applications data
 */
class Applications
{
    /**
     * Applications
     *
     * @var array
     */
    public static $applications = null;

    /**
     * Get application name by application ID
     *
     * @param string $applicationId The application ID
     * @return string
     */
    public static function getName(string $applicationId): string
    {
        return (string)Hash::get(self::list(), $applicationId);
    }

    /**
     * Get applications, in the form [<id>: <name>].
     * This uses cache 'applications'.
     *
     * @return array
     */
    public static function list(): array
    {
        if (!empty(static::$applications)) {
            return static::$applications;
        }
        try {
            static::$applications = Cache::remember(
                CacheTools::cacheKey('applications'),
                function () {
                    $response = (array)ApiClientProvider::getApiClient()->get('applications');

                    return Hash::combine($response, 'data.{n}.id', 'data.{n}.attributes.name');
                }
            );
        } catch (BEditaClientException $e) {
            return [];
        }

        return static::$applications;
    }
}
