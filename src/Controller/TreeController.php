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
     *
     * @return void
     */
    public function get(): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $query = $this->getRequest()->getQueryParams();
        $tree = $this->data($query);
        $this->set('tree', $tree);
        $this->setSerialize(['tree']);
    }

    /**
     * Get tree data by query params.
     * Use cache to store data.
     *
     * @param array $query Query params.
     * @return array
     */
    public function data(array $query): array
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
                    return $this->fetchData($query);
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
    protected function fetchData(array $query): array
    {
        $fields = 'id,status,title';
        $response = ApiClientProvider::getApiClient()->get('/folders', compact('fields') + $query);
        $data = (array)Hash::get($response, 'data');
        $meta = (array)Hash::get($response, 'meta');
        foreach ($data as &$item) {
            $item = [
                'id' => $item['id'],
                'type' => 'folders',
                'attributes' => $item['attributes'],
            ];
        }

        return compact('data', 'meta');
    }
}
