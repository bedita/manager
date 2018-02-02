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

use App\Model\API\BEditaClientException;
use Cake\Event\Event;
use Psr\Log\LogLevel;

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
        $this->Schema->setConfig('type', $this->objectType);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $this->set('objectType', $this->objectType);
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObjects($this->objectType, $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            // Error! Back to dashboard.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $objects = (array)$response['data'];

        $this->set(compact('objects'));

        return null;
    }

    /**
     * View single resource.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, $this->objectType);
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $object = $response['data'];
        $schema = $this->Schema->getSchema();

        $this->set(compact('object', 'schema'));

        return null;
    }

    /**
     * Display new resource form.
     *
     * @return void
     */
    public function create()
    {
        $this->viewBuilder()->setTemplate('view');

        // Create stub object with empty `attributes`.
        $schema = $this->Schema->getSchema();
        $attributes = array_fill_keys(
            array_keys(
                array_filter(
                    $schema['properties'],
                    function ($schema) {
                        return empty($schema['readOnly']);
                    }
                )
            ),
            ''
        );
        $object = [
            'type' => $this->objectType,
            'attributes' => $attributes,
        ];

        $this->set(compact('object', 'schema'));
    }

    /**
     * Create or edit single resource.
     *
     * @return \Cake\Http\Response
     */
    public function save()
    {
        $this->request->allowMethod(['post']);

        try {
            $response = $this->apiClient->saveObject($this->objectType, $this->request->getData());
        } catch (BEditaClientException $e) {
            // Error! Back to object view or index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            if ($this->request->getData('id')) {
                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
            }

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $this->Flash->success(__('Object saved'));

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => $response['data']['id'],
        ]);
    }

    /**
     * Delete single resource.
     *
     * @return \Cake\Http\Response
     */
    public function delete()
    {
        $this->request->allowMethod(['post']);

        try {
            $this->apiClient->deleteObject($this->request->getData('id'), $this->objectType);
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
        }

        $this->Flash->success(__('Object deleted'));

        return $this->redirect([
            '_name' => 'modules:list',
            'object_type' => $this->objectType,
        ]);
    }
}
