<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App;

use BEdita\SDK\BEditaClient;
use Cake\Core\Configure;

/**
 * BEdita4 API client provider singleton class.
 */
class ApiClientProvider
{

    use SingletonTrait;

    /**
     * BEdita4 API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    private $apiClient = null;

    /**
     * Read singleton API client data.
     *
     * @return \BEdita\SDK\BEditaClient
     */
    public static function getApiClient() : BEditaClient
    {
        if (static::getInstance()->apiClient) {
            return static::getInstance()->apiClient;
        }

        return static::getInstance()->createClient();
    }

    /**
     * Create new default API client.
     *
     * @return \BEdita\SDK\BEditaClient
     */
    private function createClient() : BEditaClient
    {
        $this->apiClient = new BEditaClient(Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey'));

        return $this->apiClient;
    }

    /**
     * Set a new API client.
     *
     * @param \BEdita\SDK\BEditaClient|null $client New API client to set
     * @return void
     */
    public static function setApiClient(?BEditaClient $client) : void
    {
        static::getInstance()->apiClient = $client;
    }
}
