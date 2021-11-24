<?php
namespace App\Controller\Component;

use BEdita\WebTools\ApiClientProvider;
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
    public function index(?string $objectType = null, ?array $options = [])
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
    public function map(?array $response)
    {
        return (array)Hash::combine((array)$response['data'], '{n}.id', '{n}');
    }

    /**
     * Create an id-based categories tree.
     *
     * @param array $map The categories map returned by the map function.
     * @return array The categories tree.
     */
    public function tree(?array $map)
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
     * Save a category using the `/model/` API.
     *
     * @param array $data Data to save.
     * @return array The BEdita API response for the saved category.
     */
    public function save(array $data)
    {
        $id = Hash::get($data, 'id');
        unset($data['id']);
        $body = [
            'data' => [
                'type' => 'categories',
                'attributes' => $data,
            ],
        ];

        $apiClient = ApiClientProvider::getApiClient();
        $endpoint = sprintf('/model/%s', 'categories');
        if (empty($id)) {
            return $apiClient->post($endpoint, json_encode($body));
        }

        $body['data']['id'] = $id;

        return $apiClient->patch(sprintf('%s/%s', $endpoint, $id), json_encode($body));
    }

    /**
     * Delete a category using the `/model/` API.
     *
     * @param string|int $id The category id to delete.
     * @return array The BEdita API response for the deleted category.
     */
    public function delete(string $id)
    {
        $apiClient = ApiClientProvider::getApiClient();

        return $apiClient->delete(sprintf('/model/%s/%s', 'categories', $id));
    }
}
