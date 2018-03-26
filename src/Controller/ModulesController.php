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
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

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
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->objectType = $this->request->getParam('object_type');
        $this->Modules->setConfig('currentModuleName', $this->objectType);
        $this->Schema->setConfig('type', $this->objectType);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);

        $this->set('objectType', $this->objectType);
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index() : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObjects($this->objectType, $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            // @TODO: ajax error display

            // Error! Back to dashboard.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $objects = (array)$response['data'];
        $meta = (array)$response['meta'];
        $links = (array)$response['links'];

        $this->set(compact('objects'));
        $this->set(compact('meta'));
        $this->set(compact('links'));

        if (!empty($this->request->getQueryParams()['autocomplete'])) {
            $this->render('autocomplete');
        }

        return null;
    }

    /**
     * View single resource.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id) : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, $this->objectType);
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $object = $response['data'];
        $revision = Hash::get($response, 'meta.schema.' . $this->objectType . '.revision', null);
        $schema = $this->Schema->getSchema($this->objectType, $revision);

        $this->set(compact('object', 'schema'));

        return null;
    }

    /**
     * Display new resource form.
     *
     * @return \Cake\Http\Response|null
     */
    public function create() : ?Response
    {
        $this->viewBuilder()->setTemplate('view');

        // Create stub object with empty `attributes`.
        $schema = $this->Schema->getSchema();
        if (!is_array($schema)) {
            $this->Flash->error(__('Cannot create abstract objects or objects without schema'));

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }
        $attributes = array_fill_keys(
            array_keys(
                array_filter(
                    $schema['properties'],
                    function ($schema) {
                        return empty($schema['readOnly']);
                    }
                )
            ),
            ''
        );
        $object = [
            'type' => $this->objectType,
            'attributes' => $attributes,
        ];

        $this->set(compact('object', 'schema'));

        return null;
    }

    /**
     * Create or edit single resource.
     *
     * @return \Cake\Http\Response
     */
    public function save() : Response
    {
        $this->request->allowMethod(['post']);
        $this->prepareRequest();
        $requestData = $this->request->getData();
        try {
            if (!empty($requestData['api'])) {
                foreach ($requestData['api'] as $api) {
                    extract($api); // method, id, type, relation, data
                    if (in_array($method, ['addRelated', 'removeRelated'])) {
                        $response = $this->apiClient->{$method}($id, $this->objectType, $relation, $data);
                    }
                }
            }
            $response = $this->apiClient->saveObject($this->objectType, $requestData);
        } catch (BEditaClientException $e) {
            // Error! Back to object view or index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            if ($this->request->getData('id')) {
                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
            }

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $this->Flash->success(__('Object saved'));

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => $response['data']['id'],
        ]);
    }

    /**
     * Delete single resource.
     *
     * @return \Cake\Http\Response
     */
    public function delete() : Response
    {
        $this->request->allowMethod(['post']);

        try {
            $this->apiClient->deleteObject($this->request->getData('id'), $this->objectType);
        } catch (BEditaClientException $e) {
            // Error! Back to object view.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
        }

        $this->Flash->success(__('Object deleted'));

        return $this->redirect([
            '_name' => 'modules:list',
            'object_type' => $this->objectType,
        ]);
    }

    /**
     * Relation data load callig api `GET /:object_type/:id/related/:relation`
     *
     * @param string|int $id the object identifier.
     * @param string $relation the relating name.
     * @return void
     */
    public function related($id, $relation)
    {
        $this->request->allowMethod(['get']);
        $response = null;
        $this->set(compact('relation'));

        try {
            $response = $this->apiClient->getRelated($id, $this->objectType, $relation, $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->set('error', $e);

            return;
        }

        $objects = (array)$response['data'];
        $meta = (array)$response['meta'];
        $links = (array)$response['links'];

        $this->set(compact('objects'));
        $this->set(compact('meta'));
        $this->set(compact('links'));
    }

    /**
     * Relation data load callig api `GET /:object_type/:id/relationships/:relation`
     *
     * @param string|int $id the object identifier.
     * @param string $relation the relating name.
     * @return void
     */
    public function relationships($id, $relation)
    {
        $this->request->allowMethod(['get']);
        $response = null;
        $path = sprintf('/%s/%s/%s', $this->objectType, $id, $relation);
        $this->set(compact('relation'));

        try {
            $response = $this->apiClient->get($path, $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->set('error', $e);

            return;
        }

        $objects = (array)$response['data'];
        $meta = (array)$response['meta'];
        $links = (array)$response['links'];

        $this->set(compact('objects'));
        $this->set(compact('meta'));
        $this->set(compact('links'));
    }

    /**
     * Upload a file and store it in a media stream
     *
     * @return \Cake\Http\Response|null
     */
    public function upload()
    {
        $object = $included = [];
        try {
            // upload file
            $filename = $this->request->getData('file.name');
            $filepath = $this->request->getData('file.tmp_name');
            $headers = ['Content-type' => $this->request->getData('file.type')];
            $response = $this->apiClient->upload($filename, $filepath, $headers);
            // create media from stream
            $streamId = $response['data']['id'];
            $type = $this->request->getData('model-type');
            $title = $filename;
            $attributes = compact('title');
            $data = compact('type', 'attributes');
            $body = compact('data');
            $response = $this->apiClient->createMediaFromStream($streamId, $type, $body);
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e);

            return $this->redirect([
                '_name' => 'modules:create',
                'object_type' => $this->objectType,
            ]);
        }

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => $response['data']['id'],
        ]);
    }
}
