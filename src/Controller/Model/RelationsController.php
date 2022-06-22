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
