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
     * Get application name by application ID
     *
     * @param string $applicationId The application ID
     * @return string
     */
    public static function getName(string $applicationId): string
    {
        $applications = self::list();
        if (empty($applications)) {
            return '';
        }

        return (string)Hash::get($applications, $applicationId);
    }

    /**
     * Get applications, in the form [<id>: <name>].
     * This uses cache 'applications'.
     *
     * @return array
     */
    public static function list(): array
    {
        try {
            $applications = Cache::remember(
                'applications',
                function () {
                    $response = ApiClientProvider::getApiClient()->get('/admin/applications');

                    return (array)Hash::combine($response, 'data.{n}.id', 'data.{n}.attributes.name');
                }
            );
        } catch (BEditaClientException $e) {
            return [];
        }

        return $applications;
    }
}
