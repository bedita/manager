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
namespace App\Utility;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Utility\Hash;

/**
 * Read and write configuration via API
 */
trait ApiConfigTrait
{
    /**
     * Cache config key
     *
     * @var string
     */
    protected static $cacheKey = 'api_config';

    /**
     * Read cached configuration items from API and update configuration
     * using `Configure::write`.
     *
     * @return void
     */
    protected function readApiConfig(): void
    {
        try {
            $configs = (array)Cache::remember(
                CacheTools::cacheKey(static::$cacheKey),
                function () {
                    return $this->fetchConfig();
                }
            );
        } catch (BEditaClientException $e) {
            Log::error($e->getMessage());

            return;
        }

        foreach ($configs as $config) {
            $content = (array)json_decode((string)Hash::get($config, 'attributes.content'), true);
            $key = (string)Hash::get($config, 'attributes.name');
            Configure::write($key, $content);
        }
    }

    /**
     * Fetch configurations from API
     *
     * @param null|string $key Configuration key to fetch, fetch all keys if null.
     * @return array
     */
    protected function fetchConfig(?string $key = null): array
    {
        $query = ['page_size' => 100];
        $response = (array)ApiClientProvider::getApiClient()->get('/config', $query);
        $collection = new Collection((array)Hash::get($response, 'data'));

        return (array)$collection->reject(function ($item) use ($key) {
            return !$this->isAppConfig($key, (array)$item);
        })->toArray();
    }

    /**
     * Check if a configuration is a valid application configuration.
     *
     * @param string|null $key Configuration key, if `null` consider any configuration key as valid
     * @param array $config Configuration data array from API.
     * @return bool
     */
    protected function isAppConfig(?string $key = null, array $config): bool
    {
        $attr = (array)Hash::get($config, 'attributes');
        if (
            empty($attr['application_id']) ||
            empty($attr['context']) || $attr['context'] !== 'app' ||
            empty($attr['name'])
        ) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return $attr['name'] === $key;
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
        $response = (array)ApiClientProvider::getApiClient()->get('/admin/applications', compact('filter'));

        return (int)Hash::get($response, 'data.0.id');
    }

    /**
     * Save configuration to API
     *
     * @param string $key Configuration key
     * @param array $data Configuration data
     * @return void
     */
    public function saveApiConfig(string $key, array $data): void
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
            ApiClientProvider::getApiClient()->post($endpoint, json_encode($body));
        } else {
            $body['data']['id'] = (string)$configId;
            ApiClientProvider::getApiClient()->patch(sprintf('%s/%s', $endpoint, $configId), json_encode($body));
        }
        Cache::delete(CacheTools::cacheKey(static::$cacheKey));
        Cache::delete(CacheTools::cacheKey('properties'));
    }
}
