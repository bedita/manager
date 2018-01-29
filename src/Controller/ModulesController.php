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

use App\Model\API\BEditaClient;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Modules controller: list, add, edit, remove items (default objects)
 */
class ModulesController extends AppController
{

    /**
     * Object type name in use
     *
     * @var string
     */
    protected $objectType = null;

    /**
     * Module name, defaults to $objectType
     * Exceptions: `trash` and optional plugin modules
     *
     * @var string
     */
    protected $moduleName = null;

    /**
     * Main API response array
     *
     * @var array
     */
    protected $apiResponse = [];

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->objectType = $this->request->getParam('object_type');
        $this->moduleName = $this->objectType;

        $this->loadComponent('Schema', [
            'apiClient' => $this->apiClient,
            'type' => $this->objectType,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        if (empty($this->modules)) {
            $this->readModules();
        }
        $modules = Hash::extract($this->modules, '{n}[name=' . $this->moduleName . ']');
        $currentModule = (!empty($modules[0])) ? $modules[0] : null;
        $this->set(compact('currentModule'));
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        if ($this->apiResponse) {
            foreach ($this->apiResponse as $key => $value) {
                $this->set($key, $value);
            }
            $status = $this->apiClient->getStatusCode();
            if ($status >= 400) {
                $reason = $this->apiClient->getStatusMessage();
                if (empty($reason)) {
                    $reason = 'Response status code is ' . $status;
                }
                $this->Flash->error(__($reason));
            }
        }
    }

    /**
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $this->apiResponse = $this->apiClient->getObjects($this->objectType, $this->request->getQueryParams());
    }

    /**
     * View single item
     *
     * @param mixed $id Item ID.
     * @return void
     */
    public function view($id)
    {
        $this->apiResponse = $this->apiClient->getObject($id, $this->objectType);
        $this->set('schema', $this->Schema->getSchema());
    }

    /**
     * Display new item form
     *
     * @return void
     */
    public function new()
    {
        $this->viewBuilder()->setTemplate('view');

        // set 'data' with empty 'attributes' for the view
        $schema = $this->Schema->getSchema();
        $this->set('schema', $schema);
        foreach ($schema['properties'] as $name => $data) {
            if (empty($data['readOnly'])) {
                $attributes[$name] = '';
            }
        }
        $this->set('data', compact('attributes'));
    }

    /**
     * Create or edit single item
     *
     * @return \Cake\Http\Response
     */
    public function save()
    {
        $this->apiResponse = $this->apiClient->saveObject($this->objectType, $this->request->getData());
        // TODO: error if $this->apiResponse['data']['id'] empty or  $this->apiResponse['error'] not empty

        $this->Flash->info(__('Object saved'));

        return $this->redirect(Router::url(sprintf('/%s/view/%s', $this->objectType, $this->apiResponse['data']['id'])));
    }

    /**
     * Delete single item
     *
     * @return \Cake\Http\Response
     */
    public function delete()
    {
        $this->apiResponse = $this->apiClient->deleteObject($this->request->getData('id'), $this->objectType);

        $this->Flash->info(__('Object deleted'));

        return $this->redirect(Router::url(sprintf('/%s', $this->objectType)));
    }
}
