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

use App\Core\Bulk\CustomBulkActionInterface;
use BEdita\SDK\BEditaClientException;
use Cake\Core\App;
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
     * @var array|string
     */
    protected $categories = [];

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->objectType = $this->getRequest()->getParam('object_type');
    }

    /**
     * Bulk change attribute value for selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function attribute(): ?Response
    {
        $requestData = $this->getRequest()->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $this->saveAttribute($requestData['attributes']);
        $this->showResult();

        return $this->modulesListRedirect();
    }

    /**
     * Bulk custom action for selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function custom(): ?Response
    {
        $requestData = $this->request->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $this->performCustomAction((string)Hash::get($requestData, 'custom_action'));
        $this->showResult();

        return $this->modulesListRedirect();
    }

    /**
     * Perform bulk action via custom class.
     *
     * @param string $bulkClass Custom action class name.
     * @return void
     */
    protected function performCustomAction(string $bulkClass): void
    {
        $class = App::className($bulkClass);
        if (empty($class)) {
            $this->errors[] = __('Custom action class {0} not found', $bulkClass);

            return;
        }
        $bulkAction = new $class();
        if (!$bulkAction instanceof CustomBulkActionInterface) {
            $this->errors[] = __('Custom action class {0} is not valid', $bulkClass);

            return;
        }

        $this->errors = $bulkAction->bulkAction($this->ids, $this->objectType);
    }

    /**
     * Redirect to modules index.
     *
     * @return \Cake\Http\Response|null
     */
    protected function modulesListRedirect(): ?Response
    {
        return $this->redirect([
            '_name' => 'modules:list',
            'object_type' => $this->objectType,
            '?' => $this->request->getQuery(),
        ]);
    }

    /**
     * Bulk associate categories to selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function categories(): ?Response
    {
        $requestData = $this->getRequest()->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $this->categories = (string)Hash::get($requestData, 'categories');
        $this->loadCategories();
        $this->saveCategories();
        $this->showResult();

        return $this->modulesListRedirect();
    }

    /**
     * Bulk associate position in tree to selected ids.
     *
     * @return \Cake\Http\Response|null
     */
    public function position(): ?Response
    {
        $requestData = $this->getRequest()->getData();
        $this->ids = explode(',', (string)Hash::get($requestData, 'ids'));
        $folder = (string)Hash::get($requestData, 'folderSelected');
        $action = (string)Hash::get($requestData, 'action');
        if ($action === 'copy') {
            $this->copyToPosition($folder);
        } else { // move
            $this->moveToPosition($folder);
        }
        $this->showResult();

        return $this->modulesListRedirect();
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
        $schema = (array)$this->Schema->getSchema($this->objectType);
        $schemaCategories = (array)Hash::extract($schema, 'categories');
        $ids = explode(',', (string)$this->categories);
        $this->categories = [];
        foreach ($schemaCategories as $schemaCategory) {
            if (in_array($schemaCategory['id'], $ids)) {
                $this->categories[] = $schemaCategory['name'];
            }
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
                $object = $this->apiClient->getObject($id, $this->objectType, ['fields' => 'categories']);
                $objectCategories = (array)Hash::extract($object, 'data.attributes.categories.{n}.name');
                $this->categories = array_unique(array_merge((array)$this->categories, $objectCategories));
                $payload = compact('id');
                $payload['categories'] = $this->remapCategories((array)$this->categories);
                $this->apiClient->save($this->objectType, $payload);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
        }
    }

    /**
     * Remap categories, returning an array of items 'name':<category>
     *
     * @param array $input The input categories
     * @return array
     */
    protected function remapCategories(array $input): array
    {
        return array_map(function ($category) {

            return ['name' => $category];
        }, $input);
    }

    /**
     * Copy selected objects to position.
     *
     * @param string $position The folder ID
     * @return void
     */
    protected function copyToPosition(string $position): void
    {
        $ids = array_reverse($this->ids);
        foreach ($ids as $id) {
            try {
                $this->apiClient->addRelated($position, 'folders', 'children', [
                    [
                        'id' => $id,
                        'type' => $this->objectType,
                    ],
                ]);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
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
        $ids = array_reverse($this->ids);
        foreach ($ids as $id) {
            try {
                $this->apiClient->replaceRelated($id, $this->objectType, 'parents', [
                    'id' => $position,
                    'type' => 'folders',
                ]);
            } catch (BEditaClientException $e) {
                $this->errors[] = ['id' => $id, 'message' => $e->getAttributes()];
            }
        }
    }

    /**
     * Display success or error messages.
     *
     * @return void
     */
    protected function showResult(): void
    {
        if (empty($this->errors)) {
            $this->Flash->success(__('Bulk action performed on {0} objects', count($this->ids)));

            return;
        }
        $this->log('Error: ' . json_encode($this->errors), LogLevel::ERROR);
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
