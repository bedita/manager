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

use App\Core\Exception\UploadException;
use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Modules controller: list, add, edit, remove objects
 *
 * @property \App\Controller\Component\ProjectConfigurationComponent $ProjectConfiguration
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
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');
        $this->loadComponent('ProjectConfiguration');

        if (!empty($this->request)) {
            $this->objectType = $this->request->getParam('object_type');
            $this->Modules->setConfig('currentModuleName', $this->objectType);
            $this->Schema->setConfig('type', $this->objectType);
        }

        $this->Security->setConfig('unlockedActions', ['saveJson']);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function beforeRender(Event $event): ?Response
    {
        $this->set('objectType', $this->objectType);

        return parent::beforeRender($event);
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->request->allowMethod(['get']);

        // handle filter and query parameters using session
        $result = $this->applySessionFilter();
        if ($result != null) {
            return $result;
        }

        try {
            $response = $this->apiClient->getObjects($this->objectType, $this->indexQuery());
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            // remove session filter to avoid error repetition
            $session = $this->request->getSession();
            $session->delete(sprintf('%s.filter', $this->Modules->getConfig('currentModuleName')));

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->ProjectConfiguration->read();

        $objects = (array)$response['data'];
        $this->set('objects', $objects);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('types', ['right' => $this->descendants()]);

        $this->set('properties', $this->Properties->indexList($this->objectType));

        // base/custom filters for filter view
        $this->set('filter', $this->Properties->filterList($this->objectType));

        // base/custom bulk actions for index view
        $this->set('bulkActions', $this->Properties->bulkList($this->objectType));

        // objectTypes schema
        $this->set('schema', $this->getSchemaForIndex($this->objectType));

        // set prevNext for views navigations
        $this->setObjectNav($objects);

        return null;
    }

    /**
     * Retrieve `index` module query string
     *
     * @return array
     */
    protected function indexQuery()
    {
        $query = $this->request->getQueryParams();
        // return URL query string if `filter`, `sort`, or `q` are set
        $subQuery = array_intersect_key($query, array_flip(['filter', 'sort', 'q']));
        if (!empty($subQuery)) {
            return $query;
        }

        // set sort order: use `currentModule.sort` or default '-id'
        $query['sort'] = (string)Hash::get($this->viewVars, 'currentModule.sort', '-id');

        return $query;
    }

    /**
     * Retrieve descendants of `$this->objectType` if any
     *
     * @return array
     */
    protected function descendants(): array
    {
        if (!$this->Modules->isAbstract($this->objectType)) {
            return [];
        }
        $filter = [
            'parent' => $this->objectType,
            'enabled' => true,
        ];
        $sort = 'name';

        try {
            $descendants = $this->apiClient->get('/model/object_types', compact('filter', 'sort') + ['fields' => 'name']);
        } catch (BEditaClientException $e) {
            // Error! Return empty list.
            $this->log($e, LogLevel::ERROR);

            return [];
        }

        return (array)Hash::extract($descendants, 'data.{n}.attributes.name');
    }

    /**
     * View single resource.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id): ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $query = [];
            if ($this->objectType !== 'folders') {
                $query['include'] = 'parents';
            }
            $response = $this->apiClient->getObject($id, $this->objectType, $query);
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error(__('Error retrieving the requested content'), ['params' => $e]);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }
        $this->ProjectConfiguration->read();

        $revision = Hash::get($response, 'meta.schema.' . $this->objectType . '.revision', null);
        $schema = $this->Schema->getSchema($this->objectType, $revision);

        $object = $response['data'];

        // setup `currentAttributes` and recover failure data from session.
        $this->Modules->setupAttributes($object);

        $included = (!empty($response['included'])) ? $response['included'] : [];
        $typeIncluded = (array)Hash::combine($included, '{n}.id', '{n}', '{n}.type');
        $streams = Hash::get($typeIncluded, 'streams');
        $this->set(compact('object', 'included', 'schema', 'streams'));
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));

        // setup relations metadata
        $this->Modules->setupRelationsMeta($this->Schema->getRelationsSchema(), $object['relationships']);

        // set objectNav
        $objectNav = $this->getObjectNav((string)$id);
        $this->set('objectNav', $objectNav);

        return null;
    }

    /**
     * View single resource by id, doing a proper redirect (302) to resource module view by type.
     * If no resource found by ID, redirect to referer.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function uname($id): ?Response
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
    public function create(): ?Response
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
        $this->ProjectConfiguration->read();

        // setup relations metadata
        $relationships = array_flip((array)Hash::get($schema, 'relations'));
        $this->Modules->setupRelationsMeta($this->Schema->getRelationsSchema(), $relationships);

        return null;
    }

    /**
     * Create or edit single resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->prepareRequest($this->objectType);

        try {
            if (!empty($requestData['_api'])) {
                foreach ($requestData['_api'] as $api) {
                    extract($api); // method, id, type, relation, relatedIds
                    if (in_array($method, ['addRelated', 'removeRelated', 'replaceRelated'])) {
                        $this->apiClient->{$method}($id, $this->objectType, $relation, $relatedIds);
                    }
                }
            }
            unset($requestData['_api']);

            // upload file (if available)
            $this->Modules->upload($requestData);

            // save data
            $response = $this->apiClient->save($this->objectType, $requestData);
        } catch (InternalErrorException | BEditaClientException | UploadException $e) {
            // Error! Back to object view or index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            // set session data to recover form
            $this->Modules->setDataFromFailedSave($this->objectType, $requestData);

            if ($this->request->getData('id')) {
                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->request->getData('id')]);
            }

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }

        // annoying message removed, restore with https://github.com/bedita/manager/issues/71
        // $this->Flash->success(__('Object saved'));

        return $this->redirect([
            '_name' => 'modules:view',
            'object_type' => $this->objectType,
            'id' => Hash::get($response, 'data.id'),
        ]);
    }

    /**
     * Create new object from ajax request.
     *
     * @return void
     */
    public function saveJson(): void
    {
        $this->viewBuilder()->setClassName('Json'); // force json response
        $this->request->allowMethod(['post']);
        $requestData = $this->prepareRequest($this->objectType);

        try {
            // upload file (if available)
            $this->Modules->upload($requestData);

            // save data
            $response = $this->apiClient->save($this->objectType, $requestData);
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }
        if ($response['data']) {
            $response['data'] = [ $response['data'] ];
        }

        $this->getThumbsUrls($response);

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }

    /**
     * Clone single object.
     *
     * @param string|int $id Object ID.
     * @return \Cake\Http\Response|null
     */
    public function clone($id): ?Response
    {
        $this->viewBuilder()->setTemplate('view');

        $schema = $this->Schema->getSchema();
        if (!is_array($schema)) {
            $this->Flash->error(__('Cannot create abstract objects or objects without schema'));

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }
        try {
            $response = $this->apiClient->getObject($id, $this->objectType);
            $attributes = $response['data']['attributes'];
            $attributes['uname'] = '';
            unset($attributes['relationships']);
            $attributes['title'] = $this->request->getQuery('title');
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id]);
        }
        $object = [
            'type' => $this->objectType,
            'attributes' => $attributes,
        ];
        $this->set(compact('object', 'schema'));
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));

        return null;
    }

    /**
     * Delete single resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(): ?Response
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
                $this->Flash->error($e->getMessage(), ['params' => $e]);
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
    public function relatedJson($id, string $relation): void
    {
        $this->request->allowMethod(['get']);
        $query = $this->Modules->prepareQuery($this->request->getQueryParams());
        try {
            $response = $this->apiClient->getRelated($id, $this->objectType, $relation, $query);
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->getThumbsUrls($response);

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }

    /**
     * Load resources of $type callig api `GET /:type/`
     * Json response
     *
     * @param string|int $id the object identifier.
     * @param string $type the resource type name.
     * @return void
     */
    public function resourcesJson($id, string $type): void
    {
        $this->request->allowMethod(['get']);
        $query = $this->Modules->prepareQuery($this->request->getQueryParams());
        try {
            $response = $this->apiClient->get($type, $query);
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
     * Relation data load callig api `GET /:object_type/:id/relationships/:relation`
     * Json response
     *
     * @param string|int $id the object identifier.
     * @param string $relation the relating name.
     * @return void
     */
    public function relationshipsJson($id, string $relation): void
    {
        $this->request->allowMethod(['get']);
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
                    $response = $this->apiClient->get($path, ['page_size' => 1]); // page_size 1: we need just the available
                    $available = $response['links']['available'];
            }

            $query = $this->Modules->prepareQuery($this->request->getQueryParams());
            $response = $this->apiClient->get($available, $query);

            $this->getThumbsUrls($response);
        } catch (BEditaClientException $ex) {
            $this->log($ex, LogLevel::ERROR);

            $this->set([
                'error' => $ex->getMessage(),
                '_serialize' => ['error'],
            ]);

            return;
        }

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }

    /**
     * Retrieve thumbnails URL of related objects in `meta.url` if present.
     *
     * @param array $response Related objects response.
     * @return void
     */
    public function getThumbsUrls(array &$response): void
    {
        if (empty($response['data'])) {
            return;
        }

        // extract ids of objects
        $ids = (array)Hash::extract($response, 'data.{n}[type=/images|videos/].id');
        if (empty($ids)) {
            return;
        }

        $thumbs = '/media/thumbs?ids=' . implode(',', $ids) . '&options[w]=400'; // TO-DO this hardcoded 400 should be in param/conf of some sort

        $query = $this->Modules->prepareQuery($this->request->getQueryParams());
        $thumbsResponse = $this->apiClient->get($thumbs, $query);

        $thumbsUrl = $thumbsResponse['meta']['thumbnails'];

        foreach ($response['data'] as &$object) {
            $thumbnail = Hash::get($object, 'attributes.provider_thumbnail');
            if ($thumbnail) {
                $object['meta']['thumb_url'] = $thumbnail;
                continue; // if provider_thumbnail is found there's no need to extract it from thumbsResponse
            }

            // extract url of the matching objectid's thumb
            $thumbnail = (array)Hash::extract($thumbsUrl, sprintf('{*}[id=%s].url', $object['id']));
            if (count($thumbnail)) {
                $object['meta']['thumb_url'] = $thumbnail[0];
            }
        }
    }

    /**
     * Bulk change actions for objects
     *
     * @return \Cake\Http\Response|null
     */
    public function bulkActions(): ?Response
    {
        $requestData = $this->request->getData();
        $this->request->allowMethod(['post']);

        if (!empty($requestData['ids'] && is_string($requestData['ids']))) {
            $ids = $requestData['ids'];
            $errors = [];

            // extract valid attributes to change
            $attributes = array_filter(
                $requestData['attributes'],
                function ($value) {
                    return ($value !== null && $value !== '');
                }
            );

            // export selected (filter by id)
            $ids = explode(',', $ids);
            foreach ($ids as $id) {
                $data = array_merge($attributes, ['id' => $id]);
                try {
                    $this->apiClient->save($this->objectType, $data);
                } catch (BEditaClientException $e) {
                    $errors[] = [
                        'id' => $id,
                        'message' => $e->getAttributes(),
                    ];
                }
            }

            // if errors occured on any single save show error message
            if (!empty($errors)) {
                $this->log($errors, LogLevel::ERROR);
                $this->Flash->error(__('Bulk Action failed on: '), ['params' => $errors]);
            }
        }

        return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType, '?' => $this->request->getQuery()]);
    }

    /**
     * get object properties and format them for index
     *
     * @param string $objectType objecte type name
     *
     * @return array $schema
     */
    public function getSchemaForIndex($objectType): array
    {
        $schema = (array)$this->Schema->getSchema($objectType);

        // if prop is an enum then prepend an empty string for select element
        if (!empty($schema['properties'])) {
            foreach ($schema['properties'] as &$property) {
                if (isset($property['enum'])) {
                    array_unshift($property['enum'], '');
                }
            }
        }

        return $schema;
    }
}
