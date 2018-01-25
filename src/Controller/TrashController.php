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
 * Trash controller: list, restore, delete
 */
class TrashController extends AppController
{
    /**
     * Object type name
     *
     * @var string
     */
    protected $objectType = null;

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
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        if (empty($this->modules)) {
            $this->readModules();
        }
        $currentModule = Hash::extract($this->modules, '{n}[name=trash]')[0];
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
        }
        $params = $this->request->getQueryParams();
        if (!empty($params['filter']['type'])) {
            $this->set('filterType', $params['filter']['type']);
        }
    }

    /**
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $query = $this->request->getQueryParams();
        if (!empty($this->objectType)) {
            $query = [
                'filter' => [
                    'type' => $this->objectType,
                ]
            ];
        }
        $this->apiResponse = $this->apiClient->getObjects('trash', $query);
    }

    /**
     * View single item
     *
     * @param mixed $id Item ID.
     * @return void
     */
    public function view($id)
    {
        $this->apiResponse = $this->apiClient->getObject($id, 'trash');
    }

    /**
     * Restore object, remove from trashcan
     *
     * @return \Cake\Http\Response
     */
    public function restore()
    {
        $this->apiResponse = $this->apiClient->restoreObject($this->request->getData('id'));

        return $this->redir();
    }

    /**
     * Delete object, remove from trashcan
     *
     * @return \Cake\Http\Response
     */
    public function delete()
    {
        $this->apiResponse = $this->apiClient->remove($this->request->getData('id'));

        return $this->redir();
    }

    /**
     * Redirect to proper page according to object type
     *
     * @return \Cake\Http\Response
     */
    private function redir()
    {
        $destination = '/trash';
        if (!empty($this->objectType)) {
            $destination .= '?filter[type]=' . $this->objectType;
        } elseif (!empty($this->request->getData('filter_type'))) {
            $destination .= '?filter[type]=' . $this->request->getData('filter_type');
        }

        return $this->redirect(Router::url($destination));
    }
}
