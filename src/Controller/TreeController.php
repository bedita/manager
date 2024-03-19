<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use App\Event\TreeCacheEventHandler;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Tree Controller: get tree data using cache
 */
class TreeController extends AppController
{
    /**
     * Get tree data.
     * Use this for /tree?filter[roots]&... and /tree?filter[parent]=x&...
     * Use cache to store data.
     *
     * @return void
     */
    public function get(): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $query = $this->getRequest()->getQueryParams();
        $tree = $this->treeData($query);
        $this->set('tree', $tree);
        $this->setSerialize(['tree']);
    }

    /**
     * Get node by ID.
     * Use cache to store data.
     *
     * @param string $id The ID.
     * @return void
     */
    public function node(string $id): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $node = $this->fetchNodeData($id);
        $this->set('node', $node);
        $this->setSerialize(['node']);
    }

    /**
     * Get parent by ID.
     * Use cache to store data.
     *
     * @param string $id The ID.
     * @return void
     */
    public function parent(string $id): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $parent = $this->fetchParentData($id);
        $this->set('parent', $parent);
        $this->setSerialize(['parent']);
    }

    /**
     * Get parents by ID and type.
     * Use cache to store data.
     *
     * @param string $type The type.
     * @param string $id The ID.
     * @return void
     */
    public function parents(string $type, string $id): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $parents = $this->fetchParentsData($id, $type);
        $this->set('parents', $parents);
        $this->setSerialize(['parents']);
    }

    /**
     * Get tree data by query params.
     * Use cache to store data.
     *
     * @param array $query Query params.
     * @return array
     */
    public function treeData(array $query): array
    {
        $filter = Hash::get($query, 'filter', []);
        $subkey = !empty($filter['parent']) ? sprintf('parent-%s', $filter['parent']) : 'roots';
        $tmp = array_filter(
            $query,
            function ($key) {
                return $key !== 'filter';
            },
            ARRAY_FILTER_USE_KEY
        );
        $key = CacheTools::cacheKey(sprintf('tree-%s-%s', $subkey, md5(serialize($tmp))));
        $data = [];
        try {
            $data = Cache::remember(
                $key,
                function () use ($query) {
                    return $this->fetchTreeData($query);
                },
                TreeCacheEventHandler::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $data;
    }

    /**
     * Get node from ID.
     * It uses cache to store data.
     *
     * @param string $id The ID.
     * @return array|null
     */
    public function fetchNodeData(string $id): ?array
    {
        $key = CacheTools::cacheKey(sprintf('tree-node-%s', $id));
        $data = [];
        try {
            $data = Cache::remember(
                $key,
                function () use ($id) {
                    $response = ApiClientProvider::getApiClient()->get(sprintf('/folders/%s', $id));
                    $data = (array)Hash::get($response, 'data');

                    return $this->minimalData($data);
                },
                TreeCacheEventHandler::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $data;
    }

    /**
     * Get parent from ID.
     * It uses cache to store data.
     *
     * @param string $id The ID.
     * @return array|null
     */
    public function fetchParentData(string $id): ?array
    {
        $key = CacheTools::cacheKey(sprintf('tree-parent-%s', $id));
        $data = [];
        try {
            $data = Cache::remember(
                $key,
                function () use ($id) {
                    $response = ApiClientProvider::getApiClient()->get(sprintf('/folders/%s/parent', $id));
                    $data = (array)Hash::get($response, 'data');

                    return $this->minimalDataWithMeta($data);
                },
                TreeCacheEventHandler::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $data;
    }

    /**
     * Get parent from ID.
     * It uses cache to store data.
     *
     * @param string $id The ID.
     * @param string $type The type.
     * @return array
     */
    public function fetchParentsData(string $id, string $type): array
    {
        $key = CacheTools::cacheKey(sprintf('tree-parents-%s-%s', $id, $type));
        $data = [];
        try {
            $data = Cache::remember(
                $key,
                function () use ($id, $type) {
                    $response = ApiClientProvider::getApiClient()->get(sprintf('/%s/%s?include=parents', $type, $id));
                    $included = (array)Hash::get($response, 'included');
                    foreach ($included as &$item) {
                        $item = $this->minimalData((array)$item);
                    }

                    return $included;
                },
                TreeCacheEventHandler::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $data;
    }

    /**
     * Fetch tree data from API.
     * Retrieve minimal data for folders: id, status, title.
     * Return data and meta (no links, no included).
     *
     * @param array $query Query params.
     * @return array
     */
    protected function fetchTreeData(array $query): array
    {
        $fields = 'id,status,title';
        $response = ApiClientProvider::getApiClient()->get('/folders', compact('fields') + $query);
        $data = (array)Hash::get($response, 'data');
        $meta = (array)Hash::get($response, 'meta');
        foreach ($data as &$item) {
            $item = $this->minimalData((array)$item);
        }

        return compact('data', 'meta');
    }

    /**
     * Get minimal data for object.
     *
     * @param array $fullData Full data.
     * @return array
     */
    protected function minimalData(array $fullData): array
    {
        if (empty($fullData)) {
            return [];
        }

        return [
            'id' => (string)Hash::get($fullData, 'id'),
            'type' => (string)Hash::get($fullData, 'type'),
            'attributes' => [
                'title' => (string)Hash::get($fullData, 'attributes.title'),
                'status' => (string)Hash::get($fullData, 'attributes.status'),
            ],
        ];
    }

    /**
     * Get minimal data for object with meta.
     *
     * @param array $fullData Full data.
     * @return array|null
     */
    protected function minimalDataWithMeta(array $fullData): ?array
    {
        if (empty($fullData)) {
            return null;
        }

        return [
            'id' => (string)Hash::get($fullData, 'id'),
            'type' => (string)Hash::get($fullData, 'type'),
            'attributes' => [
                'title' => (string)Hash::get($fullData, 'attributes.title'),
                'status' => (string)Hash::get($fullData, 'attributes.status'),
            ],
            'meta' => [
                'path' => (string)Hash::get($fullData, 'meta.path'),
                'relation' => [
                    'canonical' => (string)Hash::get($fullData, 'meta.relation.canonical'),
                    'depth_level' => (string)Hash::get($fullData, 'meta.relation.depth_level'),
                    'menu' => (string)Hash::get($fullData, 'meta.relation.menu'),
                ],
            ],
        ];
    }
}
