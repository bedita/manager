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

use App\Utility\ApiConfigTrait;
use BEdita\SDK\BEditaClientException;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Appearance Controller
 *
 * @property \App\Controller\Component\ConfigComponent $Config
 */
class AppearanceController extends AdministrationBaseController
{
    use ApiConfigTrait;

    /**
     * Resource type in use
     *
     * @var string
     */
    protected string $resourceType = 'config';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Security->setConfig('unlockedActions', ['save']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $configs = [];
        foreach (static::$configKeys as $property) {
            $configs[Inflector::underscore($property)] = Configure::read($property);
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
        $this->viewBuilder()->setClassName('Json');
        try {
            $data = (array)$this->getRequest()->getData();
            $propertyName = (string)Hash::get($data, 'property_name');
            $propertyValue = (array)Hash::get($data, 'property_value');
            $this->saveApiConfig(Inflector::camelize($propertyName), $propertyValue);
            $response = 'Configuration saved';
            $this->set('response', $response);
            $this->setSerialize(['response']);
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->set('error', $error);
            $this->setSerialize(['error']);
        }

        return null;
    }
}
