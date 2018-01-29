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

use Cake\Event\Event;

/**
 * Modules controller: list, add, edit, remove items (default objects)
 */
class ModulesController extends AppController
{

    /**
     * Object type currently used
     *
     * @var string
     */
    protected $objectType = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->objectType = $this->request->getParam('object_type');
        $this->Modules->setConfig('currentModuleName', $this->objectType);

        $this->loadComponent('Schema', [
            'apiClient' => $this->apiClient,
            'type' => $this->objectType,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $this->set('objectType', $this->objectType);

        $status = $this->apiClient->getStatusCode();
        if ($status >= 400) {
            $reason = $this->apiClient->getStatusMessage();
            if (empty($reason)) {
                $reason = 'Response status code is ' . $status;
            }
            $this->Flash->error(__($reason));
        }
    }

    /**
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $res = $this->apiClient->getObjects($this->objectType, $this->request->getQueryParams());
        $objects = $res['data'];

        $this->set(compact('objects'));
    }

    /**
     * View single item
     *
     * @param string|int $id Item ID.
     * @return void
     */
    public function view($id)
    {
        $this->request->allowMethod(['get']);

        $res = $this->apiClient->getObject($id, $this->objectType);
        $object = $res['data'];

        $this->set(compact('object'));
        $this->set('schema', $this->Schema->getSchema());
    }

    /**
     * Display new item form
     *
     * @return void
     */
    public function create()
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
        $this->request->allowMethod(['post']);

        $apiResponse = $this->apiClient->saveObject($this->objectType, $this->request->getData());
        $this->Flash->info(__('Object saved'));

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => $apiResponse['data']['id'],
        ]);
    }

    /**
     * Delete single item
     *
     * @return \Cake\Http\Response
     */
    public function delete()
    {
        $this->request->allowMethod(['post']);

        $apiResponse = $this->apiClient->deleteObject($this->request->getData('id'), $this->objectType);
        $this->Flash->info(__('Object deleted'));

        return $this->redirect([
            '_name' => 'modules:list',
            'object_type' => $this->objectType,
        ]);
    }
}
