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
        $resource = (array)$this->viewBuilder()->getVar('resource');
        $name = Hash::get($resource, 'attributes.name', 'undefined');
        $filter = ['object_type' => $name];
        try {
            $response = $this->apiClient->get(
                '/model/properties',
                compact('filter') + ['page_size' => 100]
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
        }

        $objectTypeProperties = $this->prepareProperties((array)$response['data'], $name);
        $this->set(compact('objectTypeProperties'));

        return null;
    }

    /**
     * Separate properties between `inherited`, `core`  and `custom`
     *
     * @param array $data Property array
     * @param string $name Object type name
     * @return array
     */
    protected function prepareProperties(array $data, string $name): array
    {
        $inherited = $core = $custom = [];
        foreach ($data as $prop) {
            if (!is_numeric($prop['id'])) {
                $type = $prop['attributes']['object_type_name'];
                if ($type == $name) {
                    $core[] = $prop;
                } else {
                    $inherited[] = $prop;
                }
            } else {
                $custom[] = $prop;
            }
        }

        return compact('inherited', 'core', 'custom');
    }
}
