<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
        $currentModule = Hash::extract($this->modules, '{n}[name=' . $this->objectType . ']')[0];
        $this->set(compact('currentModule'));
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        foreach ($this->apiResponse as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Display items index
     *
     * @return void
     */
    public function index()
    {
        $this->apiResponse = $this->apiClient->get('/' . $this->objectType);
    }

    /**
     * View single item
     *
     * @param mixed $id Item ID.
     * @return void
     */
    public function view($id)
    {
        $this->apiResponse = $this->apiClient->get('/' . $this->objectType . '/' . $id);
    }
}
