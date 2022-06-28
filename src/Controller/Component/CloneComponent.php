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
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function startup(): void
    {
        $this->apiClient = ApiClientProvider::getApiClient();
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
}
