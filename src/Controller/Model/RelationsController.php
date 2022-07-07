<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Model;

use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Relations Model Controller: list, add, edit, remove relations
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class RelationsController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'relations';

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        parent::index();
        $resources = (array)$this->viewBuilder()->getVar('resources');
        foreach ($resources as &$resource) {
            $resource['left_object_types'] = $this->relatedTypes($resource, 'left');
            $resource['right_object_types'] = $this->relatedTypes($resource, 'right');
        }
        $this->set(compact('resources'));

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function indexQuery(): array
    {
        return parent::indexQuery() + ['include' => 'left_object_types,right_object_types'];
    }

    /**
     * @inheritDoc
     */
    protected function viewQuery(): array
    {
        return parent::viewQuery() + ['include' => 'left_object_types,right_object_types'];
    }

    /**
     * @inheritDoc
     */
    public function view($id): ?Response
    {
        parent::view($id);
        $resource = (array)$this->viewBuilder()->getVar('resource');
        $this->set('left_object_types', $this->relatedTypes($resource, 'left'));
        $this->set('right_object_types', $this->relatedTypes($resource, 'right'));
        $this->set('all_types', $this->allTypes());

        return null;
    }

    /**
     * Save relation.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $data = (array)$this->request->getData();
        $this->updateRelatedTypes($data, 'left');
        $this->updateRelatedTypes($data, 'right');
        $this->request = $this->request->withoutData('change_left')
            ->withoutData('change_right')
            ->withoutData('current_left')
            ->withoutData('current_right');

        return parent::save();
    }

    /**
     * Update relation types on the `left` or `right` side of a relation
     *
     * @param array $data Request data
     * @param string $side Relation side, `left` or `right`
     * @return void
     */
    protected function updateRelatedTypes(array $data, string $side): void
    {
        $current = array_filter(explode(',', (string)Hash::get($data, sprintf('current_%s', $side))));
        $change = array_filter(explode(',', (string)Hash::get($data, sprintf('change_%s', $side))));
        sort($current);
        sort($change);
        if ($current == $change) {
            return;
        }
        $id = Hash::get($data, 'id');
        $endpoint = sprintf('/model/relations/%s/relationships/%s_object_types', $id, $side);
        $data = $this->relatedItems($change);
        $this->apiClient->patch($endpoint, json_encode(compact('data')));
    }

    /**
     * Retrieve body item for API call
     *
     * @param array $types Object type names array
     * @return array
     */
    protected function relatedItems(array $types): array
    {
        return array_map(
            function ($item) {
                $response = $this->apiClient->get(sprintf('/model/object_types/%s', trim($item)));
                $id = Hash::get((array)$response, 'data.id');

                return compact('id') + ['type' => 'object_types'];
            },
            $types
        );
    }

    /**
     * Get related types by relation resource and side
     *
     * @param array $resource The resource data
     * @param string $side The side, can be 'left' or 'right'
     * @return array
     */
    protected function relatedTypes(array $resource, string $side): array
    {
        $path = sprintf('relationships.%s_object_types.data.{n}.attributes.name', $side);

        return (array)Hash::extract($resource, $path);
    }

    /**
     * Get all types
     *
     * @return array
     */
    protected function allTypes(): array
    {
        try {
            $response = $this->apiClient->get('/model/object_types', [
                'page_size' => 100,
                'filter' => ['enabled' => true],
            ]);
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return [];
        }

        return (array)Hash::extract($response, 'data.{n}.attributes.name');
    }
}
