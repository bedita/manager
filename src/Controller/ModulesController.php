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
     * Object type name
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

        $object = $this->apiClient->getObject($this->objectType, $id);

        $this->set(compact('object'));
    }
}
