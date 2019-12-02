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

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Model base controller class
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
abstract class ModelBaseController extends AppController
{
    /**
     * Resource type in use (object_types, properties, property_types)
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');

        $this->Schema->setConfig([
            'type' => $this->resourceType,
            'internalSchema' => true,
        ]);
    }

    /**
     * Restrict `model` module access to `admin` for now
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): ?Response
    {
        $roles = $this->Auth->user('roles');
        if (empty($roles) || !in_array('admin', $roles)) {
            throw new UnauthorizedException(__('Module access not authorized'));
        }

        return parent::beforeFilter($event);
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->get(
                sprintf('/model/%s', $this->resourceType),
                $this->request->getQueryParams()
            );
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('resources', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
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
    public function view($id): ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->get(sprintf('/model/%s/%s', $this->resourceType, $id));
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
        }

        $resource = (array)$response['data'];
        $this->set(compact('resource'));
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->viewGroups($resource, $this->resourceType));

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event): ?Response
    {
        $this->set('resourceType', $this->resourceType);
        $this->set('moduleLink', ['_name' => 'model:list:object_types']);

        return parent::beforeRender($event);
    }
}
