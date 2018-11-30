<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Model controller: list, add, edit, remove modeling related resources
 *
 * Object types, relations, properties and property_types
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ModelController extends AppController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->loadComponent('Properties');

        $this->resourceType = $this->request->getParam('resource_type', 'object_types');
        $this->Schema->setConfig([
            'type' => $this->resourceType,
            'internalSchema' => true,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event) : ?Response
    {
        $roles = $this->Auth->user('roles');
        if (empty($roles) || !in_array('admin', $roles)) {
            throw new UnauthorizedException(__('Module access not authorized'));
        }

        return parent::beforeFilter($event);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event) : ?Response
    {
        $this->set('resourceType', $this->resourceType);
        $this->set('moduleLink', ['_name' => 'model:list', 'resource_type' => 'object_types']);

        return parent::beforeRender($event);
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index() : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->get(
                sprintf('/model/%s', $this->resourceType),
                $this->request->getQueryParams()
            );
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('resources', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);

        $this->set('properties', $this->Properties->indexList($this->resourceType));

        return null;
    }

    /**
     * View single resource.
     *
     * @param string|int $id Resource ID.
     *
     * @return \Cake\Http\Response|null
     */
    public function view($id) : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->get(sprintf('/model/%s/%s', $this->resourceType, $id));
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'model:list', 'resource_type' => $this->resourceType]);
        }

        $resource = (array)$response['data'];
        $this->set(compact('resource'));
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->viewGroups($resource, $this->resourceType));

        return null;
    }
}
