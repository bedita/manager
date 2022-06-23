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
            $resource['left_object_types'] = $this->relatedTypes($resource['id'], 'left');
            $resource['right_object_types'] = $this->relatedTypes($resource['id'], 'right');
        }
        $this->set(compact('resources'));

        return null;
    }

    /**
     * @inheritDoc
     */
    public function view($id): ?Response
    {
        parent::view($id);
        $this->set('left_object_types', $this->relatedTypes($id, 'left'));
        $this->set('right_object_types', $this->relatedTypes($id, 'right'));

        return null;
    }

    /**
     * Save resource.
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
            ->withoutData('current_leftt')
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
     * Get related types by relation ID and side
     *
     * @param string $id The relation ID
     * @param string $side The side, can be 'left' or 'right'
     * @return array
     */
    public function relatedTypes(string $id, string $side): array
    {
        if (!is_numeric($id)) {
            $resource = (array)$this->viewBuilder()->getVar('resource');
            $id = Hash::get($resource, 'id');
        }
        $endpoint = sprintf('/model/relations/%s/%s_object_types', $id, $side);
        $response = $this->apiClient->get($endpoint);

        return (array)Hash::extract($response, 'data.{n}.attributes.name');
    }
}
