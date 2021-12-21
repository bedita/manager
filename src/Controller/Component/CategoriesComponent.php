<?php
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

        return $apiClient->get('/model/categories', $options);
    }

    /**
     * Create a key/value map of categories from the BEdita categories list response.
     *
     * @param array $response The BEdita API response for the categories list.
     * @return array A map with the category ids as keys and the category attributes as values.
     */
    public function map(?array $response): array
    {
        return (array)Hash::combine((array)Hash::get($response, 'data'), '{n}.id', '{n}');
    }

    /**
     * Create an id-based categories tree.
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
                $tree['_'][] = $category['id'];
            } else {
                $tree[$category['attributes']['parent_id']][] = $category['id'];
            }
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
        $roots = ['' => '-'];
        foreach ($map as $category) {
            if (empty($category['attributes']['parent_id'])) {
                $roots[$category['id']] = empty($category['attributes']['label']) ? $category['attributes']['name'] : $category['attributes']['label'];
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
     * @param string|int $id The category id to delete.
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
