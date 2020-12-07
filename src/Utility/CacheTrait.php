<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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

/**
 * Add cache related utility methods
 */
trait CacheTrait
{
    /**
     * Create multi project cache key.
     *
     * @param string $name Cache item name.
     * @return string
     */
    protected function cacheKey(string $name): string
    {
        $apiSignature = md5(ApiClientProvider::getApiClient()->getApiBaseUrl());

        return sprintf('%s_%s', $name, $apiSignature);
    }
}
