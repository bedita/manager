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
namespace App\Controller\Component;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Handles JSON Schema of objects and resources.
 *
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class SchemaComponent extends Component
{
    /**
     * {@inheritDoc}
     */
    public $components = ['Flash'];

    /**
     * Cache config name for type schemas.
     *
     * @var string
     */
    const CACHE_CONFIG = '_schema_types_';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'type' => null, // resource or object type name
        'internalSchema' => false, // use internal schema
    ];

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

    /**
     * Read type JSON Schema from API using internal cache.
     *
     * @param string|null $type Type to get schema for. By default, configured type is used.
     * @param string|null $revision Schema revision.
     * @return array|bool JSON Schema.
     */
    public function getSchema(string $type = null, string $revision = null)
    {
        if ($type === null) {
            $type = $this->getConfig('type');
        }

        if ($this->getConfig('internalSchema')) {
            return $this->loadInternalSchema($type);
        }

        $schema = $this->loadWithRevision($type, $revision);
        if ($schema !== false) {
            return $schema;
        }

        try {
            $schema = Cache::remember(
                $this->cacheKey($type),
                function () use ($type) {
                    return $this->fetchSchema($type);
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Booleans **ARE** valid JSON Schemas: returning `false` instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e, LogLevel::ERROR);

            return false;
        }

        return $schema;
    }

    /**
     * Load schema from cache with revision check.
     * If cached revision don't match cache is removed.
     *
     * @param string $type Type to get schema for. By default, configured type is used.
     * @param string $revision Schema revision.
     * @return array|bool Cached schema if revision match, otherwise false
     */
    protected function loadWithRevision(string $type, string $revision = null)
    {
        $key = $this->cacheKey($type);
        $schema = Cache::read($key, self::CACHE_CONFIG);
        if ($schema === false) {
            return false;
        }
        $cacheRevision = empty($schema['revision']) ? null : $schema['revision'];
        if ($cacheRevision === $revision) {
            return $schema;
        }
        // remove from cache if revision don't match
        Cache::delete($key, self::CACHE_CONFIG);

        return false;
    }

    /**
     * Fetch JSON Schema via API.
     *
     * @param string $type Type to get schema for.
     * @return array|bool JSON Schema.
     */
    protected function fetchSchema(string $type)
    {
        $schema = ApiClientProvider::getApiClient()->schema($type);
        // add special property `roles` to `users`
        if ($type === 'users') {
            $schema['properties']['roles'] = [
                'type' => 'string',
                'enum' => $this->fetchRoles(),
            ];
        }

        return $schema;
    }

    /**
     * Fetch `roles` names
     *
     * @return array
     */
    protected function fetchRoles(): array
    {
        $query = [
            'fields' => 'name',
            'page_size' => 100,
        ];
        $response = ApiClientProvider::getApiClient()->get('/roles', $query);

        return Hash::extract((array)$response, 'data.{n}.attributes.name');
    }

    /**
     * Load internal schema properties from configuration.
     *
     * @param string $type Resource type name
     * @return array
     */
    protected function loadInternalSchema(string $type): array
    {
        Configure::load('schema_properties');
        $properties = (array)Configure::read(sprintf('SchemaProperties.%s', $type), []);

        return compact('properties');
    }

    /**
     * Read relations schema from API using internal cache.
     *
     * @return array Relations schema.
     */
    public function getRelationsSchema()
    {
        try {
            $schema = (array)Cache::remember(
                $this->cacheKey('relations'),
                function () {
                    return $this->fetchRelationData();
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            $schema = [];
        }

        return $schema;
    }

    /**
     * Fetch relations schema via API.
     *
     * @return array Relations schema.
     */
    protected function fetchRelationData()
    {
        $query = [
            'include' => 'left_object_types,right_object_types',
            'page_size' => 100,
        ];
        $response = ApiClientProvider::getApiClient()->get('/model/relations', $query);

        $relations = [];
        // retrieve relation right and left object types
        $typeNames = Hash::combine((array)$response, 'included.{n}.id', 'included.{n}.attributes.name');

        foreach ($response['data'] as $res) {
            $leftTypes = (array)Hash::extract($res, 'relationships.left_object_types.data.{n}.id');
            $rightTypes = (array)Hash::extract($res, 'relationships.right_object_types.data.{n}.id');
            $res['left'] = array_values(array_intersect_key($typeNames, array_flip($leftTypes)));
            $res['right'] = array_values(array_intersect_key($typeNames, array_flip($rightTypes)));
            unset($res['relationships'], $res['links']);
            $relations[$res['attributes']['name']] = $res;
            $relations[$res['attributes']['inverse_name']] = $res;
        }
        Configure::load('relations');

        return $relations + Configure::read('DefaultRelations');
    }
}
