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
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Categories Controller: list, save, delete categories
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class CategoriesController extends AppController
{
    /**
     * Object type currently used
     *
     * @var string
     */
    protected $objectType = null;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Categories');
        $this->loadComponent('Properties');
        if ($this->getRequest()->getParam('object_type')) {
            $this->objectType = $this->getRequest()->getParam('object_type');
            $this->Modules->setConfig('currentModuleName', $this->objectType);
            $this->Schema->setConfig('type', $this->objectType);
        }
        $this->Security->setConfig('unlockedActions', ['delete', 'save']);
    }

    /**
     * List categories for the object type.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $response = $this->Categories->index($this->objectType, $this->getRequest()->getQueryParams());
        $resources = $this->Categories->map($response);
        $roots = $this->Categories->getAvailableRoots($resources);
        $categoriesTree = $this->Categories->tree($resources);
        $names = [$this->objectType => $this->Categories->names($this->objectType)];

        $this->set(compact('resources', 'roots', 'categoriesTree', 'names'));
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema($this->objectType));
        $this->set('properties', $this->Properties->indexList('categories'));
        $this->set('filter', $this->Properties->filterList('categories'));
        $this->set('object_types', [$this->objectType]);
        $this->set('objectType', $this->objectType);

        return null;
    }

    /**
     * Save category.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');
        $response = $error = null;
        try {
            $data = (array)$this->getRequest()->getData();
            $enabled = (string)Hash::get($data, 'enabled', null);
            $data['enabled'] = $enabled === 'true';
            $response = $this->Categories->save($data);
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('response', $response);
        $this->set('error', $error);
        $this->setSerialize(['response', 'error']);

        return null;
    }

    /**
     * Remove single category.
     *
     * @param string $id Category ID.
     * @return \Cake\Http\Response|null
     */
    public function delete(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');
        $response = $error = null;
        try {
            $type = $this->getRequest()->getData('object_type_name');
            $response = $this->Categories->delete($id, $type);
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('response', $response);
        $this->set('error', $error);
        $this->setSerialize(['response', 'error']);

        return null;
    }
}
