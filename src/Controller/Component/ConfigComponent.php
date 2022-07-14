<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Controller\Component;

use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Collection\Collection;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Config component
 */
class ConfigComponent extends Component
{
    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * Cache config key
     *
     * @var string
     */
    public const CACHE_KEY = 'api_config';

    /**
     * @inheritDoc
     */
    public function startup(): void
    {
        // API config may not be set in `login` for a multi-project setup
        if (!Configure::check('API.apiBaseUrl')) {
            return;
        }
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->read();
    }

    /**
     * Read cached configuration items from API and update configuration
     * using `Configure::write`.
     *
     * @return void
     */
    protected function read(): void
    {
        try {
            $configs = (array)Cache::remember(
                CacheTools::cacheKey(static::CACHE_KEY),
                function () {
                    return $this->fetchConfig();
                }
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->getController()->Flash->error($e->getMessage(), ['params' => $e]);
        }

        if (empty($configs)) {
            return;
        }

        foreach ($configs as $config) {
            $content = (array)json_decode((string)Hash::get($config, 'attributes.content'), true);
            $key = (string)Hash::get($config, 'attributes.name');
            Configure::write($key, $content);
        }
    }

    /**
     * Fetch configuration from API
     *
     * @param null|string $key Configuration key, all keys if null
     * @return array
     */
    protected function fetchConfig(?string $key = null): array
    {
        $response = (array)$this->apiClient->get('/config', ['page_size' => 100]);
        $collection = new Collection((array)Hash::get($response, 'data'));

        return (array)$collection->reject(function ($item) use ($key) {
            $attr = (array)Hash::get((array)$item, 'attributes');
            if (
                empty($attr['application_id']) ||
                empty($attr['context']) || $attr['context'] !== 'app' ||
                empty($attr['name'])
            ) {
                return true;
            }

            if ($key === null) {
                return false;
            }

            return $attr['name'] !== $key;
        })->toArray();
    }

    /**
     * Get BEdita Manager application ID
     *
     * @return int
     */
    public function managerApplicationId(): int
    {
        $name = (string)Configure::read('ManagerAppName', 'manager');
        $filter = compact('name');
        $response = (array)$this->apiClient->get('/admin/applications', compact('filter'));

        return (int)Hash::get($response, 'data.0.id');
    }

    /**
     * Save configuration to API
     *
     * @param string $key Configuration key
     * @param array $data Configuration data
     * @return void
     */
    public function save(string $key, array $data): void
    {
        $items = array_values($this->fetchConfig($key));
        $config = (array)Hash::get($items, '0');
        $configId = Hash::get($config, 'id');
        $managerAppId = Hash::get($config, 'attributes.application_id');
        if (empty($managerAppId)) {
            $managerAppId = $this->managerApplicationId();
        }
        $endpoint = '/admin/config';
        $body = [
            'data' => [
                'type' => 'config',
                'attributes' => [
                    'name' => $key,
                    'context' => 'app',
                    'content' => json_encode($data),
                    'application_id' => $managerAppId,
                ],
            ],
        ];

        if (empty($configId)) {
            $this->apiClient->post($endpoint, json_encode($body));
        } else {
            $body['data']['id'] = (string)$configId;
            $this->apiClient->patch(sprintf('%s/%s', $endpoint, $configId), json_encode($body));
        }
        Cache::delete(CacheTools::cacheKey(static::CACHE_KEY));
    }
}
