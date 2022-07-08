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
use Cake\Core\Configure;
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
     * Core tables list.
     *
     * @var array
     */
    public const TABLES = [
        'BEdita/Core.Annotations',
        'BEdita/Core.Applications',
        'BEdita/Core.AsyncJobs',
        'BEdita/Core.AuthProviders',
        'BEdita/Core.Categories',
        'BEdita/Core.Config',
        'BEdita/Core.DateRanges',
        'BEdita/Core.EndpointPermissions',
        'BEdita/Core.Endpoints',
        'BEdita/Core.ExternalAuth',
        'BEdita/Core.Folders',
        'BEdita/Core.History',
        'BEdita/Core.Links',
        'BEdita/Core.Locations',
        'BEdita/Core.Media',
        'BEdita/Core.ObjectCategories',
        'BEdita/Core.ObjectPermissions',
        'BEdita/Core.ObjectProperties',
        'BEdita/Core.ObjectRelations',
        'BEdita/Core.ObjectsBase',
        'BEdita/Core.Objects',
        'BEdita/Core.ObjectTags',
        'BEdita/Core.ObjectTypes',
        'BEdita/Core.Profiles',
        'BEdita/Core.Properties',
        'BEdita/Core.PropertyTypes',
        'BEdita/Core.Publications',
        'BEdita/Core.Relations',
        'BEdita/Core.RelationTypes',
        'BEdita/Core.Roles',
        'BEdita/Core.RolesUsers',
        'BEdita/Core.StaticProperties',
        'BEdita/Core.Streams',
        'BEdita/Core.Tags',
        'BEdita/Core.Translations',
        'BEdita/Core.Trees',
        'BEdita/Core.Users',
        'BEdita/Core.UserTokens',
    ];
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'object_types';

    /**
     * @inheritDoc
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
        $schema = $this->Schema->getSchema();
        $schema['properties']['table'] = ['type' => 'string', 'enum' => $this->tables()];
        $schema['properties']['parent_name'] = ['type' => 'string', 'enum' => $this->abstractTypes()];
        $this->set('schema', $schema);
        $this->set('properties', $this->Properties->viewGroups($resource, $this->resourceType));

        return null;
    }

    /**
     * Get available tables list
     *
     * @return array
     */
    protected function tables(): array
    {
        $tables = array_unique(
            array_merge(
                self::TABLES,
                (array)Configure::read('Model.objectTypesTables')
            )
        );
        sort($tables);

        return $tables;
    }

    /**
     * Get abstract types from api
     *
     * @return array
     */
    protected function abstractTypes(): array
    {
        $response = [];
        try {
            $response = $this->apiClient->get(
                '/model/object_types',
                ['filter' => ['is_abstract' => true]]
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return [];
        }
        $types = (array)Hash::extract($response, 'data.{n}.attributes.name');
        sort($types);

        return $types;
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

    /**
     * Save object type.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->addCustomProperty();
        $this->request = $this->request->withoutData('prop_name')
            ->withoutData('prop_type');

        return parent::save();
    }

    /**
     * Add custom property
     *
     * @return void
     */
    protected function addCustomProperty(): void
    {
        $name = $this->request->getData('prop_name');
        $type = $this->request->getData('prop_type');
        if (empty($name) || empty($type)) {
            return;
        }

        $data = [
            'type' => 'properties',
            'attributes' => compact('name') + [
                'property_type_name' => $type,
                'object_type_name' => $this->request->getData('name'),
            ],
        ];
        $this->apiClient->post('/model/properties', json_encode(compact('data')));
    }
}
