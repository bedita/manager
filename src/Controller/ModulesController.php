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
     * Module name, defaults to $objectType
     * Exceptions: `trash` and optional plugin modules
     *
     * @var string
     */
    protected $moduleName = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->objectType = $this->request->getParam('object_type');
        $this->Modules->setConfig('currentModuleName', $this->objectType);
        $this->moduleName = $this->objectType;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        if (empty($this->modules)) {
            $this->readModules();
        }
        $currentModule = Hash::extract($this->modules, '{n}[name=' . $this->moduleName . ']')[0];
        $this->set(compact('currentModule'));
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
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $objects = $this->apiClient->getObjects($this->objectType, $this->request->getQueryParams());

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

        $object = $this->apiClient->getObject($id, $this->objectType);
    }

    /**
     * Display new item form
     *
     * @return void
     */
    public function new()
    {
        $this->viewBuilder()->setTemplate('view');
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

        return $this->redirect(Router::url(sprintf('/%s', $this->objectType)));

        $this->set(compact('object'));
    }
}
