<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Utility;

use Cake\Cache\Cache;
use Psr\Log\LogLevel;
use Cake\Utility\Hash;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;

trait SchemaTrait
{
    /**
     * Getter for home endpoint metadata.
     *
     * @return array
     */
    public function getMeta(): array
    {
        try {
            /** @var \Authentication\Identity|null $user */
            $user = $this->Authentication->getIdentity();
            $home = Cache::remember(
                sprintf('home_%d', $user->get('id')),
                function () {
                    $client = ApiClientProvider::getApiClient();

                    return $client->get('/home');
                }
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Returning an empty array instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return (array)Hash::get($home, 'meta');
    }
}
