<?php
/**
 * Doorway site
 *
 * Copyright 2021 ChannelWeb Srl
 */
namespace App\Controller\Component;

use Cake\Collection\Collection;
use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Helper class to format API response data.
 */
class ApiFormatterComponent extends Component
{
    /**
     * Embed included data into relationships.
     *
     * @param array $response The response from API
     * @return array
     */
    public function embedIncluded(array $response): array
    {
        $data = Hash::get($response, 'data');
        if (empty($data)) {
            return $data;
        }

        $included = Hash::get($response, 'included');
        if (empty($included)) {
            return $response;
        }

        $included = collection($included);
        if (!Hash::numeric(array_keys($data))) {
            $response['data'] = $this->addIncluded($data, $included);

            return $response;
        }

        foreach ($data as &$d) {
            $d = $this->addIncluded($d, $included);
        }
        unset($d);

        $response['data'] = $data;

        return $response;
    }

    /**
     * Add included data to main resource.
     *
     * @param array $resource The resource.
     * @param \Cake\Collection\Collection $included The included collection.
     * @return array
     */
    protected function addIncluded(array $resource, Collection $included): array
    {
        foreach ($resource['relationships'] as &$relation) {
            if (empty($relation['data'])) {
                continue;
            }

            $relation['data'] = $this->extractFromIncluded($included, (array)$relation['data']);
        }
        unset($relation);

        return $resource;
    }

    /**
     * Extract items from included starting from $relationship data.
     *
     * @param \Cake\Collection\Collection $included The included collection
     * @param array $relationshipData Array of relationship data.
     *                                Every item must contain 'type' and 'id'.
     * @return array
     */
    protected function extractFromIncluded(Collection $included, array $relationshipData): array
    {
        foreach ($relationshipData as &$data) {
            $data = (array)$included->firstMatch([
                'type' => $data['type'],
                'id' => $data['id'],
            ]);
        }
        unset($data);

        return $relationshipData;
    }
}
