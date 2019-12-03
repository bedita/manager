<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use Psr\Log\LogLevel;

/**
 * Object Types Model Controller: list, add, edit, remove object types
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ObjectTypesController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'object_types';

    /**
     * {@inheritDoc}
     */
    public function view($id): ?Response
    {
        parent::view($id);

        // retrieve additional data
        $resource = (array)Hash::get($this->viewVars, 'resource');
        $filter = ['object_type' => Hash::get($resource, 'attributes.name', 'undefined')];
        try {
            $response = $this->apiClient->get(
                '/model/properties',
                compact('filter') + ['page_size' => 100]
            );
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
        }

        $this->set('objectTypeProperties', (array)$response['data']);

        return null;
    }
}
