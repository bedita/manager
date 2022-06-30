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
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function startup(): void
    {
        // API config may not be set in `login` for a multi-project setup
        if (Configure::check('API.apiBaseUrl')) {
            $this->apiClient = ApiClientProvider::getApiClient();
        }
    }

    /**
     * Get a cached configuration key from API.
     * Get standard config [from config/app.php etc.] if no configuration from API was found.
     *
     * @param string $key Configuration key
     * @return array
     */
    public function read(string $key): array
    {
        try {
            $config = Cache::remember(
                CacheTools::cacheKey(sprintf('config.%s', $key)),
                function () use ($key) {
                    return $this->fetchConfig($key);
                }
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->getController()->Flash->error($e->getMessage(), ['params' => $e]);
        }

        if (!empty($config)) {
            return (array)json_decode((string)Hash::get($config, 'attributes.content'), true);
        }

        return (array)Configure::read($key);
    }

    /**
     * Fetch configuration from API
     *
     * @param string $key Configuration key
     * @return array
     */
    protected function fetchConfig(string $key): array
    {
        $response = (array)$this->apiClient->get('/config');
        $collection = new Collection((array)Hash::get($response, 'data'));

        return (array)$collection->reject(function ($item) use ($key) {
            $attr = (array)Hash::get((array)$item, 'attributes');

            return empty($attr['application_id']) ||
                empty($attr['context']) || $attr['context'] !== 'app' ||
                empty($attr['name']) || $attr['name'] !== $key;
        })->first();
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
        $config = $this->fetchConfig($key);
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
        Cache::delete(CacheTools::cacheKey(sprintf('config.%s', $key)));
    }
}
