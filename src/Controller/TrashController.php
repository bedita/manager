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

use App\Utility\CacheTools;
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
            CacheTools::setModuleCount($response, 'trash');
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
        $this->set('schema', ['properties' => [
            'title' => ['type' => 'string'],
            'type' => ['type' => 'string'],
            'status' => ['type' => 'string'],
            'modified' => ['type' => 'date-time'],
            'id' => ['type' => 'integer'],
        ]]);

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
        $id = $this->getRequest()->getData('id');
        $ids = $this->getRequest()->getData('ids');
        $ids = is_string($ids) ? explode(',', $ids) : $ids;
        $ids = empty($ids) ? [$id] : $ids;
        try {
            $this->apiClient->restoreObjects($ids);
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
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
        $id = $this->getRequest()->getData('id');
        $ids = $this->getRequest()->getData('ids');
        $ids = is_string($ids) ? explode(',', $ids) : $ids;
        $ids = empty($ids) ? [$id] : $ids;
        if ($this->deleteMulti($ids)) {
            $this->Flash->success(__('Object(s) deleted from trash'));
        }

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
            $ids = Hash::extract($response, 'data.{n}.id');
            if (!$this->deleteMulti($ids)) {
                return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
            }
            $counter += count($ids);
            $response = $this->apiClient->getObjects('trash', $query);
        }
        $this->Flash->success(__(sprintf('%d objects deleted from trash', $counter)));

        return $this->redirect(['_name' => 'trash:list'] + $this->listQuery());
    }

    /**
     * Delete data and related streams, if any.
     *
     * @param string $id Object ID
     * @return void
     */
    public function deleteData(string $id): void
    {
        $response = $this->apiClient->get('/streams', ['filter' => ['object_id' => $id]]);
        $this->apiClient->remove($id);
        // this for BE versions < 5.25.1, where streams are not deleted with media on delete
        $streams = (array)Hash::get($response, 'data');
        foreach ($streams as $stream) {
            $search = $this->apiClient->get('/streams', ['filter' => ['uuid' => $stream['id']]]);
            $count = (int)Hash::get($search, 'meta.pagination.count', 0);
            if ($count === 1) {
                $this->apiClient->delete(sprintf('/streams/%s', $stream['id']));
            }
        }
    }

    /**
     * Delete multiple data and related streams, if any.
     *
     * @param array $ids Object IDs
     * @return bool
     */
    public function deleteMulti(array $ids): bool
    {
        try {
            $response = $this->apiClient->get('/streams', ['filter' => ['object_id' => $ids]]);
            $this->apiClient->removeObjects($ids);
            $streams = (array)Hash::get($response, 'data');
            $this->removeStreams($streams);
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return false;
        }

        return true;
    }

    /**
     * Remove streams
     *
     * @param array $streams The streams to remove
     * @return void
     */
    public function removeStreams(array $streams): void
    {
        // this for BE versions < 5.25.1, where streams are not deleted with media on delete
        $uuids = (array)Hash::extract($streams, '{n}.id');
        if (empty($uuids)) {
            return;
        }
        $search = $this->apiClient->get('/streams', ['filter' => ['uuid' => $uuids]]);
        $search = (array)Hash::get($search, 'data');
        foreach ($search as $stream) {
            $this->apiClient->delete(sprintf('/streams/%s', $stream['id']));
        }
    }
}
