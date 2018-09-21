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
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
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

        $this->loadComponent('Properties');

        if (!empty($this->request)) {
            $this->objectType = $this->request->getParam('object_type');
            $this->Modules->setConfig('currentModuleName', $this->objectType);
            $this->Schema->setConfig('type', $this->objectType);
        }
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);

        $this->set('objectType', $this->objectType);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function beforeFilter(Event $event) : void
    {
        parent::beforeFilter($event);

        $actions = [
            'delete', 'changeStatus',
        ];

        if (in_array($this->request->params['action'], $actions)) {
            // for csrf
            $this->getEventManager()->off($this->Csrf);

            // for security component
            $this->Security->setConfig('unlockedActions', $actions);
        }
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
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

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

        $this->set('properties', $this->Properties->indexList($this->objectType));

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
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $revision = Hash::get($response, 'meta.schema.' . $this->objectType . '.revision', null);
        $schema = $this->Schema->getSchema($this->objectType, $revision);

        $object = $response['data'];
        $included = (!empty($response['included'])) ? $response['included'] : [];
        $this->set(compact('object', 'included', 'schema'));
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));

        $excluded = ['parent', 'parents', 'streams'];
        $this->set('relations', array_diff(array_keys($object['relationships']), $excluded));

        return null;
    }

    /**
     * View single translation.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function translation($id) : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $include = 'object';
            $response = $this->apiClient->getObject($id, 'translations', compact('include'));
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        $this->set('schema', $this->Schema->getSchema($this->objectType));

        $translation = Hash::extract($response, 'data');
        $object = Hash::extract($response, 'included.0');
        $this->set('translation', $translation);
        $this->set('object', $object);

        $this->set('translatable', [
            'title',
            'description',
            'body',
        ]);

        return null;
    }

    /**
     * View single resource by id, doing a proper redirect (302) to resource module view by type.
     * If no resource found by ID, redirect to referer.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function uname($id) : ?Response
    {
        try {
            $response = $this->apiClient->get(sprintf('/objects/%s', $id));
        } catch (BEditaClientException $e) {
            if ($e->getCode() === 404) {
                $error = sprintf(__('Resource "%s" not found', true), $id);
            } else {
                $error = sprintf(__('Resource "%s" not available. Error: %s', true), $id, $e->getMessage());
            }
            $this->Flash->error($error);

            return $this->redirect($this->referer());
        }
        $_name = 'modules:view';
        $object_type = $response['data']['type'];
        $id = $response['data']['id'];

        return $this->redirect(compact('_name', 'object_type', 'id'));
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
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));

        return null;
    }

    /**
     * Create or edit single resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function save() : ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->prepareRequest($this->objectType);

        try {
            if (!empty($requestData['api'])) {
                foreach ($requestData['api'] as $api) {
                    extract($api); // method, id, type, relation, relatedIds
                    if (in_array($method, ['addRelated', 'removeRelated', 'replaceRelated'])) {
                        $this->apiClient->{$method}($id, $this->objectType, $relation, $relatedIds);
                    }
                }
            }

            $response = $this->apiClient->save($this->objectType, $requestData);
        } catch (BEditaClientException $e) {
            // Error! Back to object view or index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            if ($this->request->getData('id')) {
                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
            }

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        // annoying message removed, restore with https://github.com/bedita/web/issues/71
        // $this->Flash->success(__('Object saved'));

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => Hash::get($response, 'data.id'),
        ]);
    }

    /**
     * Delete single resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete() : ?Response
    {
        $this->request->allowMethod(['post']);
        $ids = [];
        if (!empty($this->request->getData('ids'))) {
            if (is_string($this->request->getData('ids'))) {
                $ids = explode(',', $this->request->getData('ids'));
            }
        } else {
            $ids = [$this->request->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->deleteObject($id, $this->objectType);
            } catch (BEditaClientException $e) {
                $this->log($e, LogLevel::ERROR);
                $this->Flash->error($e, ['params' => $e->getAttributes()]);
                if (!empty($this->request->getData('id'))) {
                    return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
                }

                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType]);
            }
        }
        $this->Flash->success(__('Object(s) deleted'));

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
    public function relatedJson($id, string $relation) : void
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getRelated($id, $this->objectType, $relation, $this->request->getQueryParams());
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->updateMediaUrls($response);

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }

    /**
     * Provide stream media URL of related objects in `meta.url` if present.
     *
     * @param array $response Related objects response.
     * @return void
     */
    protected function updateMediaUrls(array &$response) : void
    {
        if (empty($response['data'])) {
            return;
        }
        $included = Hash::combine($response, 'included.{n}.id', 'included.{n}');
        foreach ($response['data'] as &$item) {
            $thumbnail = Hash::get($item, 'attributes.provider_thumbnail');
            if ($thumbnail) {
                $item['meta']['url'] = $thumbnail;
            } else {
                $streamId = Hash::get($item, 'relationships.streams.data.0.id');
                if ($streamId) {
                    $item['meta']['url'] = Hash::get($included, sprintf('%s.meta.url', $streamId));
                }
            }
        }
    }

    /**
     * Relation schema request callig api `GET /model/relations/:relation`
     * Json response
     *
     * @param string|int $id the object identifier.
     * @param string $relation the relating name.
     * @return void
     */
    public function relationData($id, string $relation) : void
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->relationData($relation);
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->set((array)$response);
        $this->set('_serialize', true);
    }

    /**
     * Relation data load callig api `GET /:object_type/:id/relationships/:relation`
     * Json response
     *
     * @param string|int $id the object identifier.
     * @param string $relation the relating name.
     * @return void
     */
    public function relationshipsJson($id, string $relation) : void
    {
        $this->request->allowMethod(['get']);
        $response = null;
        $path = sprintf('/%s/%s/%s', $this->objectType, $id, $relation);

        try {
            switch ($relation) {
                case 'children':
                    $available = '/objects';
                    break;
                case 'parent':
                case 'parents':
                    $available = '/folders';
                    break;
                default:
                    $response = $this->apiClient->get($path, ['page_size' => 1]); // page_size 1: we need just the available link from response
                    $available = $response['links']['available'];
            }
            $response = $this->apiClient->get($available, $this->request->getQueryParams());
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }

    /**
     * Upload a file and store it in a media stream
     *
     * @return \Cake\Http\Response|null
     */
    public function upload()
    {
        try {
            // upload file
            if (empty($this->request->getData('file.name')) || !is_string($this->request->getData('file.name'))) {
                throw new \RuntimeException('Invalid form data: file.name');
            }
            $filename = $this->request->getData('file.name');
            if (empty($this->request->getData('file.tmp_name')) || !is_string($this->request->getData('file.tmp_name'))) {
                throw new \RuntimeException('Invalid form data: file.tmp_name');
            }
            $filepath = $this->request->getData('file.tmp_name');
            $headers = ['Content-type' => $this->request->getData('file.type')];
            $response = $this->apiClient->upload($filename, $filepath, $headers);
            // create media from stream
            $streamId = $response['data']['id'];
            if (empty($this->request->getData('model-type')) || !is_string($this->request->getData('model-type'))) {
                throw new \RuntimeException('Invalid form data: model-type');
            }
            $type = $this->request->getData('model-type');
            $title = $filename;
            $attributes = compact('title');
            $data = compact('type', 'attributes');
            $body = compact('data');
            $response = $this->apiClient->createMediaFromStream($streamId, $type, $body);
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

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

    /**
     * Bulk change status for objects
     *
     * @return \Cake\Http\Response|null
     */
    public function changeStatus() : ?Response
    {
        $this->request->allowMethod(['post']);
        if (!empty($this->request->getData('ids') && is_string($this->request->getData('ids')))) {
            $ids = $this->request->getData('ids');
            $status = $this->request->getData('status');
            if (!empty($ids)) { // export selected (filter by id)
                $ids = explode(',', $ids);
                foreach ($ids as $id) {
                    $data = [
                        'id' => $id,
                        'status' => $status,
                    ];
                    $this->apiClient->save($this->objectType, $data);
                }
            }
        }

        return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
    }
}
