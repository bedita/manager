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

use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;

/**
 * Read and write configuration via API
 */
trait ApiClientTrait
{
    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaClient|null
     */
    protected ?BEditaClient $apiClient = null;

    /**
     * Get API client.
     *
     * @return \BEdita\SDK\BEditaClient
     */
    public function getClient(): BEditaClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = ApiClientProvider::getApiClient();
        }

        return $this->apiClient;
    }
}
