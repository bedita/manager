<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use Cake\Http\Response;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Appearence Controller
 *
 * @property \App\Controller\Component\ConfigComponent $Config
 */
class AppearenceController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'config';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * Properties to handle for config
     *
     * @var array
     */
    protected $properties = [
        'alert_message',
        'export',
        'modules',
        'pagination',
        'project',
        'properties',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $configs = [];
        foreach ($this->properties as $property) {
            $configs[$property] = $this->Config->read(Inflector::camelize($property));
        }
        $this->set('configs', $configs);

        return null;
    }

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $data = (array)$this->getRequest()->getData();
        $propertyName = (string)Hash::get($data, 'property_name');
        $content = (string)Hash::get($data, Inflector::camelize($propertyName));
        $this->Config->save(Inflector::camelize($propertyName), (array)json_decode($content, true));

        return $this->redirect(['_name' => 'admin:list:appearence', '?' => ['configKey' => $propertyName]]);
    }
}
