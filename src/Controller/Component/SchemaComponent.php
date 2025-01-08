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
namespace App\Controller\Component;

use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Handles model schema of objects and resources.
 *
 * @property \App\Controller\Component\FlashComponent $Flash
 */
class SchemaComponent extends Component
{
    /**
     * @inheritDoc
     */
    public $components = ['Flash'];

    /**
     * Cache config name for type schemas.
     *
     * @var string
     */
    public const CACHE_CONFIG = '_schema_types_';

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'type' => null, // resource or object type name
        'internalSchema' => false, // use internal schema
    ];

    /**
     * Read type JSON Schema from API using internal cache.
     *
     * @param string|null $type Type to get schema for. By default, configured type is used.
     * @param string|null $revision Schema revision.
     * @return array|bool JSON Schema.
     */
    public function getSchema(?string $type = null, ?string $revision = null)
    {
        if ($type === null) {
            $type = $this->getConfig('type');
        }

        if ($this->getConfig('internalSchema')) {
            return $this->loadInternalSchema($type);
        }

        $schema = $this->loadWithRevision($type, $revision);
        if (!empty($schema)) {
            return $schema;
        }

        try {
            $schema = Cache::remember(
                CacheTools::cacheKey($type),
                function () use ($type) {
                    return $this->fetchSchema($type);
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Booleans **ARE** valid JSON Schemas: returning `false` instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e->getMessage(), LogLevel::ERROR);

            return false;
        }

        return $schema;
    }

    /**
     * Get schemas by types and return them group by type
     *
     * @param array $types The types
     * @return array
     */
    public function getSchemasByType(array $types): array
    {
        $schemas = [];
        foreach ($types as $type) {
            $schemas[$type] = $this->getSchema($type);
        }

        return $schemas;
    }

    /**
     * Load schema from cache with revision check.
     * If cached revision don't match cache is removed.
     *
     * @param string $type Type to get schema for. By default, configured type is used.
     * @param string|null $revision Schema revision.
     * @return array|null Cached schema if revision match, null otherwise
     */
    protected function loadWithRevision(string $type, ?string $revision = null): ?array
    {
        $key = CacheTools::cacheKey($type);
        $schema = Cache::read($key, self::CACHE_CONFIG);
        if (empty($schema)) {
            return null;
        }
        $cacheRevision = empty($schema['revision']) ? null : $schema['revision'];
        if ($revision === null || $cacheRevision === $revision) {
            return $schema;
        }
        // remove from cache if revision don't match
        Cache::delete($key, self::CACHE_CONFIG);

        return null;
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
        if (empty($schema)) {
            return false;
        }
        // add special property `roles` to `users`
        if ($type === 'users') {
            $schema['properties']['roles'] = [
                'type' => 'string',
                'enum' => $this->fetchRoles(),
            ];
        }
        $categories = $this->fetchCategories($type);
        $objectTypeMeta = $this->fetchObjectTypeMeta($type);

        return $schema + $objectTypeMeta + array_filter(compact('categories'));
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

        return (array)Hash::extract((array)$response, 'data.{n}.attributes.name');
    }

    /**
     * Check if tags are in use
     *
     * @return bool
     */
    public function tagsInUse(): bool
    {
        $features = $this->objectTypesFeatures();
        $tagged = (array)Hash::get($features, 'tagged');

        return !empty($tagged);
    }

    /**
     * Fetch object type metadata
     *
     * @param string $type Object type.
     * @return array
     */
    protected function fetchObjectTypeMeta(string $type): array
    {
        $query = [
            'fields' => 'associations,relations',
        ];
        $response = ApiClientProvider::getApiClient()->get(
            sprintf('/model/object_types/%s', $type),
            $query
        );

        return [
            'associations' => (array)Hash::get((array)$response, 'data.attributes.associations'),
            'relations' => array_flip((array)Hash::get((array)$response, 'data.meta.relations')),
        ];
    }

    /**
     * Fetch `categories`
     * This should be called only for types having `"Categories"` association
     *
     * @param string $type Object type name
     * @return array
     */
    protected function fetchCategories(string $type): array
    {
        $data = [];
        $url = sprintf('/model/categories?filter[type]=%s', $type);
        $pageCount = $page = 1;
        $pageSize = 100;
        $query = ['page_size' => $pageSize];
        try {
            while ($page <= $pageCount) {
                $response = (array)ApiClientProvider::getApiClient()->get($url, $query + compact('page'));
                $categories = (array)Hash::get($response, 'data');
                $data = array_merge($data, $categories);
                $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
                $page++;
            }
        } catch (BEditaClientException $ex) {
            // we ignore filter errors for now
            $data = [];
        }

        return array_map(
            function ($item) {
                return [
                    'id' => Hash::get((array)$item, 'id'),
                    'name' => Hash::get((array)$item, 'attributes.name'),
                    'label' => Hash::get((array)$item, 'attributes.label'),
                    'parent_id' => Hash::get((array)$item, 'attributes.parent_id'),
                    'enabled' => Hash::get((array)$item, 'attributes.enabled'),
                ];
            },
            $data
        );
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
    public function getRelationsSchema(): array
    {
        try {
            $schema = (array)Cache::remember(
                CacheTools::cacheKey('relations'),
                function () {
                    return $this->fetchRelationData();
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e->getMessage(), LogLevel::ERROR);
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
    protected function fetchRelationData(): array
    {
        $query = [
            'include' => 'left_object_types,right_object_types',
            'page_size' => 100,
        ];
        $response = ApiClientProvider::getApiClient()->get('/model/relations', $query);

        $relations = [];
        // retrieve relation right and left object types
        $typeNames = Hash::combine((array)$response, 'included.{n}.id', 'included.{n}.attributes.name');
        $descendants = (array)Hash::get($this->objectTypesFeatures(), 'descendants');

        foreach ($response['data'] as $res) {
            $left = (array)Hash::extract($res, 'relationships.left_object_types.data.{n}.id');
            $types = array_intersect_key($typeNames, array_flip($left));
            $left = $this->concreteTypes($types, $descendants);

            $right = (array)Hash::extract($res, 'relationships.right_object_types.data.{n}.id');
            $types = array_intersect_key($typeNames, array_flip($right));
            $right = $this->concreteTypes($types, $descendants);

            unset($res['relationships'], $res['links']);
            $relations[$res['attributes']['name']] = $res + compact('left', 'right');
            $relations[$res['attributes']['inverse_name']] = $res + [
                'left' => $right,
                'right' => $left,
            ];
        }
        Configure::load('relations');
        $schema = $relations + Configure::read('DefaultRelations');
        if (Configure::read('ChildrenParams')) {
            $schema['children']['attributes']['params'] = Configure::read('ChildrenParams');
        }

        return $schema;
    }

    /**
     * Retrieve concrete types from types list using `descendants` array
     *
     * @param array $types Object types
     * @param array $descendants Descendants array
     * @return array
     */
    protected function concreteTypes(array $types, array $descendants): array
    {
        $res = [];
        foreach ($types as $type) {
            if (!empty($descendants[$type])) {
                $res = array_merge($res, $descendants[$type]);
            } else {
                $res[] = $type;
            }
        }
        sort($res);

        return array_values(array_unique($res));
    }

    /**
     * Retrieve concrete type descendants of an object $type if any.
     *
     * @param string $type Object type name.
     * @return array
     */
    public function descendants(string $type): array
    {
        $features = $this->objectTypesFeatures();

        return (array)Hash::get($features, sprintf('descendants.%s', $type));
    }

    /**
     * Retrieve list of custom properties for a given object type.
     *
     * @param string $type Object type name.
     * @return array
     */
    public function customProps(string $type): array
    {
        return (array)Cache::remember(
            CacheTools::cacheKey(sprintf('custom_props_%s', $type)),
            function () use ($type) {
                return $this->fetchCustomProps($type);
            },
            self::CACHE_CONFIG
        );
    }

    /**
     * Fetch custom properties for a given object type.
     *
     * @param string $type Object type name.
     * @return array
     */
    protected function fetchCustomProps(string $type): array
    {
        if ($this->getConfig('internalSchema')) {
            return []; // internal resources don't have custom properties
        }
        $query = [
            'fields' => 'name',
            'filter' => ['object_type' => $type, 'type' => 'dynamic'],
            'page_size' => 100,
        ];
        $response = ApiClientProvider::getApiClient()->get('/model/properties', $query);

        return (array)Hash::extract($response, 'data.{n}.attributes.name');
    }

    /**
     * Read object types features from API
     *
     * @return array
     */
    public function objectTypesFeatures(): array
    {
        try {
            $features = (array)Cache::remember(
                CacheTools::cacheKey('types_features'),
                function () {
                    return $this->fetchObjectTypesFeatures();
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $features;
    }

    /**
     * Fetch object types information via API and manipulate response array.
     *
     * Resulting array will contain:
     *  * `descendants` - associative array having abstract types as keys
     *          and all concrete descendant types list as value
     *  * `uploadable` - list of concrete types having "Streams" associated,
     *          types that can be instantiated via file upload (like images, files)
     *  * `categorized` - list of concrete types having "Categories" associated
     *
     * @return array
     */
    protected function fetchObjectTypesFeatures(): array
    {
        $query = [
            'page_size' => 100,
            'fields' => 'name,is_abstract,associations,parent_name',
            'filter' => ['enabled' => true],
        ];
        $response = (array)ApiClientProvider::getApiClient()->get('/model/object_types', $query);

        $descendants = (array)Hash::extract($response, 'data.{n}.attributes.parent_name');
        $descendants = array_filter(array_unique($descendants));
        $types = Hash::combine($response, 'data.{n}.attributes.name', 'data.{n}.attributes');
        $descendants = array_fill_keys($descendants, []);
        $uploadable = $categorized = $tagged = [];
        foreach ($types as $name => $data) {
            $abstract = (bool)Hash::get($data, 'is_abstract');
            if ($abstract) {
                continue;
            }
            $parent = (string)Hash::get($data, 'parent_name');
            $this->setDescendant($name, $parent, $types, $descendants);
            if (!(bool)Hash::get($types, $name . '.is_abstract')) {
                $assoc = (array)Hash::get($types, $name . '.associations');
                if (in_array('Streams', $assoc)) {
                    $uploadable[] = $name;
                }
                if (in_array('Categories', $assoc)) {
                    $categorized[] = $name;
                }
                if (in_array('Tags', $assoc)) {
                    $tagged[] = $name;
                }
            }
        }
        sort($categorized);
        sort($tagged);
        sort($uploadable);

        return compact('descendants', 'uploadable', 'categorized', 'tagged');
    }

    /**
     * Set descendant in $descendants array
     *
     * @param string $name Object type name
     * @param string $parent Parent type name
     * @param array $types Types array
     * @param array $descendants Descendants array
     * @return void
     */
    protected function setDescendant(string $name, string $parent, array &$types, array &$descendants): void
    {
        $desc = (array)Hash::get($descendants, $parent);
        if (empty($parent) || in_array($name, $desc)) {
            return;
        }
        $descendants[$parent][] = $name;
        $superParent = (string)Hash::get($types, $parent . '.parent_name');
        $this->setDescendant($name, $superParent, $types, $descendants);
    }

    /**
     * Get abstract types
     *
     * @return array
     */
    public function abstractTypes(): array
    {
        $features = $this->objectTypesFeatures();
        $types = array_keys($features['descendants']);
        sort($types);

        return $types;
    }

    /**
     * Clear schema cache
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function clearCache(): void
    {
        Cache::clear(static::CACHE_CONFIG);
    }
}
