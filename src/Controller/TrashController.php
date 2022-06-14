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

use BEdita\SDK\BEditaClientException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Trash can controller: list, restore & remove permanently objects
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class TrashController extends AppController
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');

        $this->Modules->setConfig('currentModuleName', 'trash');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        $this->set('moduleLink', ['_name' => 'trash:list']);

        return parent::beforeRender($event);
    }

    /**
     * Display deleted resources list.
     *
     * @return \Cake\Http\Response|null
     * @codeCoverageIgnore
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObjects('trash', $this->getRequest()->getQueryParams());
        } catch (BEditaClientException $e) {
            // Error! Back to dashboard.
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('objects', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('types', ['right' => $this->Modules->objectTypes(false)]);

        $this->set('properties', $this->Properties->indexList('trash'));

        return null;
    }

    /**
     * View single deleted resource.
     *
     * @param mixed $id Resource ID.
     * @return \Cake\Http\Response|null
     * @codeCoverageIgnore
     */
    public function view($id): ?Response
    {
        $this->getRequest()->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, 'trash');
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'trash:list']);
        }

        $object = $response['data'];
        $schema = $this->Schema->getSchema($object['type']);

        $this->set(compact('object', 'schema'));
        $this->set('properties', $this->Properties->viewGroups($object, $object['type']));

        return null;
    }

    /**
     * Restore resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function restore(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $ids = [];
        if (!empty($this->getRequest()->getData('ids'))) {
            $ids = $this->getRequest()->getData('ids');
            if (is_string($ids)) {
                $ids = explode(',', (string)$this->getRequest()->getData('ids'));
            }
        } else {
            $ids = [$this->getRequest()->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->restoreObject($id, 'objects');
            } catch (BEditaClientException $e) {
                // Error! Back to object view.
                $this->log($e->getMessage(), LogLevel::ERROR);
                $this->Flash->error($e->getMessage(), ['params' => $e]);

                if (!empty($this->getRequest()->getData('ids'))) {
                    return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
                }

                return $this->redirect(['_name' => 'trash:view', 'id' => $id]);
            }
        }

        return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
    }

    /**
     * Permanently delete resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $ids = [];
        if (!empty($this->getRequest()->getData('ids'))) {
            $ids = $this->getRequest()->getData('ids');
            if (is_string($ids)) {
                $ids = explode(',', (string)$this->getRequest()->getData('ids'));
            }
        } else {
            $ids = [$this->getRequest()->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->remove($id);
            } catch (BEditaClientException $e) {
                // Error! Back to object view.
                $this->log($e->getMessage(), LogLevel::ERROR);
                $this->Flash->error($e->getMessage(), ['params' => $e]);

                if (!empty($this->getRequest()->getData('ids'))) {
                    return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
                }

                return $this->redirect(['_name' => 'trash:view', 'id' => $id]);
            }
        }

        $this->Flash->success(__('Object(s) deleted from trash'));

        return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
    }

    /**
     * Return query filter array from request to be used in redirects
     *
     * @return array
     */
    protected function listQuery(): array
    {
        $query = $this->getRequest()->getData('query');
        if (empty($query)) {
            return [];
        }
        $query = htmlspecialchars_decode($query);

        return (array)unserialize($query);
    }

    /**
     * Permanently delete multiple data.
     * If filter type is active, empty trash by type
     *
     * @return \Cake\Http\Response|null
     */
    public function emptyTrash(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);

        $query = array_filter(array_intersect_key($this->listQuery(), ['filter' => '']));
        // cycle over trash results
        $response = $this->apiClient->getObjects('trash', $query);
        $counter = 0;
        while (Hash::get($response, 'meta.pagination.count', 0) > 0) {
            foreach ($response['data'] as $index => $data) {
                try {
                    $this->apiClient->remove($data['id']);
                    $counter++;
                } catch (BEditaClientException $e) {
                    // Error! Back to trash index.
                    $this->log($e->getMessage(), LogLevel::ERROR);
                    $this->Flash->error($e->getMessage(), ['params' => $e]);

                    return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
                }
            }
            $response = $this->apiClient->getObjects('trash', $query);
        }
        $this->Flash->success(__(sprintf('%d objects deleted from trash', $counter)));

        return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
    }
}
