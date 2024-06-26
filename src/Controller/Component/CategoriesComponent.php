<?php
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
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Categories component
 */
class CategoriesComponent extends Component
{
    /**
     * Fetch categories list.
     *
     * @param string|null $objectType The object type filter for categories.
     * @param array|null $options Query options.
     * @return array The BEdita API response for the categories list.
     */
    public function index(?string $objectType = null, ?array $options = []): array
    {
        $apiClient = ApiClientProvider::getApiClient();

        $options = $options + [
            'page_size' => 100,
        ];
        if (!empty($objectType)) {
            $options['filter'] = $options['filter'] ?? [];
            $options['filter']['type'] = $objectType;
        }

        return (array)$apiClient->get('/model/categories', $options);
    }

    /**
     * Fetch categories names
     *
     * @param string|null $objectType The object type
     * @return array
     */
    public function names(?string $objectType = null): array
    {
        $apiClient = ApiClientProvider::getApiClient();
        $query = [
            'fields' => 'name',
            'page_size' => 500, // BE4 API MAX_LIMIT
        ];
        if (!empty($objectType)) {
            $query['filter']['type'] = $objectType;
        }
        $response = $apiClient->get('/model/categories', $query);

        return (array)Hash::extract($response, 'data.{n}.attributes.name');
    }

    /**
     * Create a key/value map of categories from the BEdita categories list response.
     *
     * @param array $response The BEdita API response for the categories list.
     * @return array A map with the category ids as keys and the category attributes as values.
     */
    public function map(?array $response): array
    {
        return Hash::combine((array)Hash::get($response, 'data'), '{n}.id', '{n}');
    }

    /**
     * Create an id-based categories tree.
     * Sort children by label or name.
     *
     * @param array $map The categories map returned by the map function.
     * @return array The categories tree.
     */
    public function tree(?array $map): array
    {
        $tree = [
            '_' => [],
        ];
        foreach ($map as $category) {
            if (empty($category['attributes']['parent_id'])) {
                $tree['_'][] = $category;
            } else {
                $tree[$category['attributes']['parent_id']][] = $category;
            }
        }
        // sort by label or name
        foreach ($tree as $key => $children) {
            usort($children, function ($a, $b) {
                $tmpA = Hash::get($a, 'attributes.label') ?? Hash::get($a, 'attributes.name');
                $tmpB = Hash::get($b, 'attributes.label') ?? Hash::get($b, 'attributes.name');

                return strcasecmp($tmpA, $tmpB);
            });
            $tree[$key] = Hash::extract($children, '{n}.id');
        }

        return $tree;
    }

    /**
     * Get an id/label map of available category roots.
     *
     * @param array $map The categories map returned by the map function.
     * @return array The list of available roots.
     */
    public function getAvailableRoots(?array $map): array
    {
        $roots = ['' => ['id' => 0, 'label' => '-', 'name' => '', 'object_type_name' => '']];
        foreach ($map as $category) {
            if (empty($category['attributes']['parent_id'])) {
                $roots[$category['id']] = [
                    'id' => $category['id'],
                    'label' => $category['attributes']['label'] ?? $category['attributes']['name'],
                    'name' => $category['attributes']['name'],
                    'object_type_name' => $category['attributes']['object_type_name'],
                ];
            }
        }

        return $roots;
    }

    /**
     * Save a category using the `/model/` API.
     *
     * @param array $data Data to save.
     * @return array|null The BEdita API response for the saved category.
     */
    public function save(array $data): ?array
    {
        $id = Hash::get($data, 'id');
        $type = Hash::get($data, 'object_type_name');
        unset($data['id']);
        $body = [
            'data' => [
                'type' => 'categories',
                'attributes' => $data,
            ],
        ];

        $apiClient = ApiClientProvider::getApiClient();
        $endpoint = '/model/categories';
        $response = null;
        if (empty($id)) {
            $response = $apiClient->post($endpoint, json_encode($body));
        } else {
            $body['data']['id'] = $id;

            $response = $apiClient->patch(sprintf('%s/%s', $endpoint, $id), json_encode($body));
        }

        if (!empty($type)) {
            $this->invalidateSchemaCache($type);
        }

        return $response;
    }

    /**
     * Delete a category using the `/model/` API.
     *
     * @param string $id The category id to delete.
     * @param string $type The object type name of the category.
     * @return array|null The BEdita API response for the deleted category.
     */
    public function delete(string $id, $type = null): ?array
    {
        $apiClient = ApiClientProvider::getApiClient();

        $response = $apiClient->delete(sprintf('/model/%s/%s', 'categories', $id));
        if (!empty($type)) {
            $this->invalidateSchemaCache($type);
        }

        return $response;
    }

    /**
     * Check if a category has changed.
     *
     * @param array $oldValue The old category value.
     * @param array $newValue The new category value.
     * @return bool True if the category has changed, false otherwise.
     */
    public function hasChanged(array $oldValue, array $newValue): bool
    {
        $old = (array)Hash::extract($oldValue, '{n}.name');
        sort($old);
        $old = implode(',', $old);
        $new = (array)Hash::extract($newValue, '{n}.name');
        sort($new);
        $new = implode(',', $new);

        return $old !== $new;
    }

    /**
     * Invalidate schema cache for forms.
     *
     * @param string $type The object type name of the category.
     * @return void
     */
    private function invalidateSchemaCache(string $type): void
    {
        $key = CacheTools::cacheKey($type);
        Cache::delete($key, SchemaComponent::CACHE_CONFIG);
    }
}
