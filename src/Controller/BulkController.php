<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Bulk Controller.
 */
class BulkController extends AppController
{
    /**
     * Object type currently used
     *
     * @var string
     */
    protected $objectType = null;

    /**
     * Selected objects IDs
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Selected categories
     *
     * @var array
     */
    protected $categories = [];

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->objectType = $this->request->getParam('object_type');
    }

    /**
     * Bulk change attribute value for selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function attribute(): ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->request->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $this->saveAttribute($requestData['attributes']);
        $this->errors();

        return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType, '?' => $this->request->getQuery()]);
    }

    /**
     * Bulk associate categories to selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function categories(): ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->request->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $this->categories = (string)Hash::get($requestData, 'categoriesSelected');
        $this->loadCategories();
        $this->saveCategories();
        $this->errors();

        return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType, '?' => $this->request->getQuery()]);
    }

    /**
     * Bulk associate position in tree to selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function position(): ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->request->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $folder = (string)Hash::get($requestData, 'folderSelected');
        $action = (string)Hash::get($requestData, 'action');
        if ($action === 'copy') {
            $this->copyToPosition($folder);
        } else { // move
            $this->moveToPosition($folder);
        }
        $this->errors();

        return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType, '?' => $this->request->getQuery()]);
    }

    /**
     * Save attribute for selected objects.
     *
     * @param array $attributes The attributes data
     * @return void
     */
    protected function saveAttribute(array $attributes): void
    {
        foreach ($this->ids as $id) {
            try {
                $this->apiClient->save($this->objectType, compact('id') + $attributes);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
        }
    }

    /**
     * Load categories by type and ids.
     *
     * @return void
     */
    protected function loadCategories(): void
    {
        $url = sprintf('/model/categories?filter[type]=%s&filter[id]=%s', $this->objectType, $this->categories);
        try {
            $response = $this->apiClient->get($url, ['page_size' => 100]);
            $this->categories = (array)Hash::extract($response, 'data.{n}.attributes.name');
        } catch (BEditaClientException $ex) {
            $this->errors[] = ['message' => $ex->getAttributes()];
        }
    }

    /**
     * Save categories for selected objects.
     *
     * @return void
     */
    protected function saveCategories(): void
    {
        foreach ($this->ids as $id) {
            try {
                $object = $this->apiClient->getObject($id, $this->objectType);
                $objectCategories = (array)Hash::extract($object, 'data.attributes.categories.{n}.name');
                $this->categories = !empty($objectCategories) ? array_unique(array_merge($this->categories, $objectCategories)) : $this->categories;
                $payload = compact('id');
                $payload['categories'] = array_map(function ($category) { return ['name' => $category]; }, $this->categories);
                $this->apiClient->save($this->objectType, $payload);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
        }
    }

    /**
     * Copy selected objects to position.
     *
     * @param string $position The folder ID
     * @return void
     */
    protected function copyToPosition(string $position): void
    {
        $payload = [];
        foreach ($this->ids as $id) {
            $payload[] = compact('id') + ['type' => $this->objectType];
        }
        try {
            $this->apiClient->addRelated($position, 'folders', 'children', $payload);
        } catch (BEditaClientException $e) {
            $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
        }
    }

    /**
     * Move selected objects to position.
     *
     * @param string $position The folder ID
     * @return void
     */
    protected function moveToPosition(string $position): void
    {
        $payload = ['id' => $position, 'type' => 'folders'];
        foreach ($this->ids as $id) {
            try {
                $this->apiClient->replaceRelated($id, $this->objectType, 'parents', $payload);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
        }
    }

    /**
     * Handle page errors.
     *
     * @return void
     */
    protected function errors(): void
    {
        if (empty($this->errors)) {
            return;
        }
        $this->log($this->errors, LogLevel::ERROR);
        $this->Flash->error(__('Bulk Action failed on: '), ['params' => $this->errors]);
    }

    /**
     * Return errors.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
