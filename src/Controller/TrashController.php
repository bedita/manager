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

/**
 * Trash controller: list, restore, delete
 */
class TrashController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->Modules->setConfig('currentModuleName', 'trash');
    }

    /**
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $res = $this->apiClient->getObjects('trash', $this->request->getQueryParams());
        $objects = $res['data'];

        $this->set(compact('objects'));
    }

    /**
     * View single item
     *
     * @param mixed $id Item ID.
     * @return void
     */
    public function view($id)
    {
        $this->request->allowMethod(['get']);

        $res = $this->apiClient->getObject($id, 'trash');
        $object = $res['data'];

        $this->set(compact('object'));
    }

    /**
     * Restore object, remove from trashcan
     *
     * @return \Cake\Http\Response
     */
    public function restore()
    {
        $this->request->allowMethod(['post']);

        $apiResponse = $this->apiClient->restoreObject($this->request->getData('id'), 'objects');

        return $this->redirect(['_name' => 'trash:list'] + $this->request->getQuery());
    }

    /**
     * Delete object, remove from trashcan
     *
     * @return \Cake\Http\Response
     */
    public function delete()
    {
        $this->request->allowMethod(['post']);

        $apiResponse = $this->apiClient->remove($this->request->getData('id'));

        return $this->redirect(['_name' => 'trash:list'] + $this->request->getQuery());
    }
}
