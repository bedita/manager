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
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Trash controller: list, restore, delete
 */
class TrashController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->Modules->setConfig('currentModuleName', 'trash');
    }

    /**
     * Display deleted resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index() : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObjects('trash', $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            // Error! Back to dashboard.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $objects = (array)$response['data'];
        $meta = (array)$response['meta'];
        $links = (array)$response['links'];

        $this->set(compact('objects'));
        $this->set(compact('meta'));
        $this->set(compact('links'));

        return null;
    }

    /**
     * View single deleted resource.
     *
     * @param mixed $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id) : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, 'trash');
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'trash:list']);
        }

        $object = $response['data'];
        $schema = $this->Schema->getSchema($object['type']);

        $this->set(compact('object', 'schema'));

        return null;
    }

    /**
     * Restore resource.
     *
     * @return \Cake\Http\Response
     */
    public function restore() : Response
    {
        $this->request->allowMethod(['post']);

        try {
            $this->apiClient->restoreObject($this->request->getData('id'), 'objects');
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'trash:view', 'id' => $this->request->getData('id')]);
        }

        $this->Flash->success(__('Object restored'));

        return $this->redirect(['_name' => 'trash:list'] + $this->request->getQuery());
    }

    /**
     * Permanently delete resource.
     *
     * @return \Cake\Http\Response
     */
    public function delete() : Response
    {
        $this->request->allowMethod(['post']);

        try {
            $this->apiClient->remove($this->request->getData('id'));
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'trash:view', 'id' => $this->request->getData('id')]);
        }

        $this->Flash->success(__('Object deleted from trash'));

        return $this->redirect(['_name' => 'trash:list'] + $this->request->getQuery());
    }
}
