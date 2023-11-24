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

use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Clone component
 */
class CloneComponent extends Component
{
    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * Reset configuration.
     * This is used to remove some fields from source object before cloning.
     * This is useful to avoid to clone some fields, like username, that must be unique.
     *
     * @var array
     */
    protected $reset = [
        'objects' => [
            'relationships',
            'uname',
        ],
    ];

    /**
     * Unique fields configuration.
     * This is used to set new data for unique fields, by appending -<timestamp>.
     *
     * @var array
     */
    protected $unique = [
        'users' => [
            'username',
        ],
    ];

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function startup(): void
    {
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * Prepare data to clone.
     *
     * @param string $objectType The object type
     * @param array $source The source object
     * @return array
     */
    public function prepareData(string $objectType, array $source): array
    {
        $data = (array)Hash::get($source, 'data.attributes');
        $data['title'] = $this->getController()->getRequest()->getQuery('title');
        $data['status'] = 'draft';
        $unique = (array)Hash::get($this->unique, sprintf('%s', $objectType));
        $ts = date('YmdHis');
        foreach ($unique as $field) {
            $data[$field] = sprintf('%s-%s', $data[$field], $ts);
        }
        $reset = array_merge(
            (array)Hash::get($this->reset, 'objects'),
            (array)Hash::get($this->reset, sprintf('%s', $objectType)),
            (array)Configure::read(sprintf('Clone.reset.%s', $objectType))
        );

        return array_filter(
            $data,
            function ($key) use ($reset) {
                return !in_array($key, $reset);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get the value of query 'cloneRelations'.
     * Return true when cloneRelations is not false.
     *
     * @return bool
     */
    public function queryCloneRelations(): bool
    {
        $cloneRelations = $this->getController()->getRequest()->getQuery('cloneRelations');

        return filter_var($cloneRelations, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    /**
     * Clone relation from source object $source to destination object ID $destination.
     * Exclude 'children', if present.
     *
     * @param array $source The source object
     * @param string $destinationId The destination ID
     * @return bool
     */
    public function relations(array $source, string $destinationId): bool
    {
        if (!$this->queryCloneRelations()) {
            return false;
        }
        $sourceId = (string)Hash::get($source, 'data.id');
        $type = (string)Hash::get($source, 'data.type');
        $relationships = array_keys((array)Hash::extract($source, 'data.relationships'));
        $relationships = $this->filterRelations($relationships);
        foreach ($relationships as $relation) {
            $this->relation($sourceId, $type, $relation, $destinationId);
        }

        return true;
    }

    /**
     * Filter relationships, remove not allowed 'children', 'parents', 'translations'
     *
     * @param array $relationships The relationships
     * @return array
     */
    public function filterRelations(array $relationships): array
    {
        return array_values(
            array_filter(
                $relationships,
                function ($relationship) {
                    return !in_array($relationship, ModulesComponent::FIXED_RELATIONSHIPS);
                }
            )
        );
    }

    /**
     * Clone single relation data.
     * This calls multiple times `BEditaClient::addRelated`, instead of calling it once,
     * to avoid time and resources consuming api operations locking tables.
     *
     * @param string $sourceId The source ID
     * @param string $type The object type
     * @param string $relation The relation name
     * @param string $destinationId The destination ID
     * @return bool
     */
    public function relation(string $sourceId, string $type, string $relation, string $destinationId): bool
    {
        $related = $this->apiClient->getRelated($sourceId, $type, $relation, ['page_size' => 100]);
        if (empty($related['data'])) {
            return false;
        }
        foreach ($related['data'] as $obj) {
            $this->apiClient->addRelated($destinationId, $type, $relation, [
                [
                    'id' => (string)Hash::get($obj, 'id'),
                    'type' => (string)Hash::get($obj, 'type'),
                ],
            ]);
        }

        return true;
    }

    /**
     * Clone stream if schema has Streams in associations and source object has a related stream.
     * Return media ID, or null.
     *
     * @param array $schema The object schema
     * @param array $source The object source to clone
     * @param array $attributes The destination attributes
     * @return ?string The media ID
     */
    public function stream(array $schema, array $source, array &$attributes): ?string
    {
        if (!in_array('Streams', (array)Hash::get($schema, 'associations'))) {
            return null;
        }
        $uuid = (string)Hash::get($source, 'data.relationships.streams.data.0.id');
        if (empty($uuid)) {
            return null;
        }
        $response = $this->apiClient->post(sprintf('/streams/clone/%s', $uuid), '');
        $streamId = (string)Hash::get($response, 'data.id');
        $type = (string)Hash::get($source, 'data.type');
        $data = compact('type');
        $response = $this->apiClient->createMediaFromStream($streamId, $type, compact('data'));
        $attributes['id'] = (string)Hash::get($response, 'data.id');

        return $attributes['id'];
    }
}
