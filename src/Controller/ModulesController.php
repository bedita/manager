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
 * Modules controller: list, add, edit, remove objects
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 * @property \App\Controller\Component\CloneComponent $Clone
 * @property \App\Controller\Component\HistoryComponent $History
 * @property \App\Controller\Component\ObjectsEditorsComponent $ObjectsEditors
 * @property \App\Controller\Component\ProjectConfigurationComponent $ProjectConfiguration
 * @property \App\Controller\Component\PropertiesComponent $Properties
 * @property \App\Controller\Component\QueryComponent $Query
 * @property \App\Controller\Component\ThumbsComponent $Thumbs
 * @property \BEdita\WebTools\Controller\Component\ApiFormatterComponent $ApiFormatter
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
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Categories');
        $this->loadComponent('Clone');
        $this->loadComponent('History');
        $this->loadComponent('ObjectsEditors');
        $this->loadComponent('Properties');
        $this->loadComponent('ProjectConfiguration');
        $this->loadComponent('Query');
        $this->loadComponent('Thumbs');
        $this->loadComponent('BEdita/WebTools.ApiFormatter');
        if ($this->getRequest()->getParam('object_type')) {
            $this->objectType = $this->getRequest()->getParam('object_type');
            $this->Modules->setConfig('currentModuleName', $this->objectType);
            $this->Schema->setConfig('type', $this->objectType);
        }
        $this->Security->setConfig('unlockedActions', ['save']);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(EventInterface $event): ?Response
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
        $this->getRequest()->allowMethod(['get']);

        // handle filter and query parameters using session
        $result = $this->applySessionFilter();
        if ($result != null) {
            return $result;
        }

        try {
            $response = $this->apiClient->getObjects($this->objectType, $this->Query->index());
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            // remove session filter to avoid error repetition
            $session = $this->getRequest()->getSession();
            $session->delete(sprintf('%s.filter', $this->Modules->getConfig('currentModuleName')));

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->ProjectConfiguration->read();

        $response = $this->ApiFormatter->embedIncluded((array)$response);
        $objects = (array)Hash::get($response, 'data');
        $this->set('objects', $objects);
        $this->set('meta', (array)Hash::get($response, 'meta'));
        $this->set('links', (array)Hash::get($response, 'links'));
        $this->set('types', ['right' => $this->Schema->descendants($this->objectType)]);

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
     * View single resource.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id): ?Response
    {
        $this->getRequest()->allowMethod(['get']);

        try {
            $query = ['count' => 'all'];
            $response = $this->apiClient->getObject($id, $this->objectType, $query);
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error(__('Error retrieving the requested content'), ['params' => $e]);

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }
        $this->ProjectConfiguration->read();

        $revision = Hash::get($response, 'meta.schema.' . $this->objectType . '.revision', null);
        $schema = $this->Schema->getSchema($this->objectType, $revision);

        $object = $response['data'];

        // setup `currentAttributes` and recover failure data from session.
        $this->Modules->setupAttributes($object);

        $included = !empty($response['included']) ? $response['included'] : [];
        $typeIncluded = (array)Hash::combine($included, '{n}.id', '{n}', '{n}.type');
        $streams = Hash::get($typeIncluded, 'streams');
        $this->History->load($id, $object);
        $this->set(compact('object', 'included', 'schema', 'streams'));
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));

        $computedRelations = array_reduce(
            array_keys($object['relationships']),
            function ($acc, $relName) use ($schema) {
                $acc[$relName] = (array)Hash::get($schema, sprintf('relations.%s', $relName), []);

                return $acc;
            },
            []
        );
        $this->setupViewRelations($computedRelations);

        // set objectNav
        $objectNav = $this->getObjectNav((string)$id);
        $this->set('objectNav', $objectNav);

        $this->ObjectsEditors->update((string)$id);

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
            $msg = $e->getMessage();
            $error = $e->getCode() === 404 ?
                sprintf(__('Resource "%s" not found', true), $id) :
                sprintf(__('Resource "%s" not available. Error: %s', true), $id, $msg);
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

        $this->setupViewRelations((array)Hash::get($schema, 'relations'));

        return null;
    }

    /**
     * Create new object from ajax request.
     *
     * @return void
     */
    public function save(): void
    {
        $this->viewBuilder()->setClassName('Json'); // force json response
        $this->getRequest()->allowMethod(['post']);
        $requestData = $this->prepareRequest($this->objectType);
        unset($requestData['_csrfToken']);
        // extract related objects data
        $relatedData = (array)Hash::get($requestData, '_api');
        unset($requestData['_api']);

        try {
            // upload file (if available)
            $this->Modules->upload($requestData);

            // save data
            $response = $this->apiClient->save($this->objectType, $requestData);
            $objectId = (string)Hash::get($response, 'data.id');
            $this->Modules->saveRelated($objectId, $this->objectType, $relatedData);
        } catch (BEditaClientException $error) {
            $this->log($error->getMessage(), LogLevel::ERROR);
            $this->Flash->error($error->getMessage(), ['params' => $error]);

            $this->set(['error' => $error->getAttributes()]);
            $this->setSerialize(['error']);

            // set session data to recover form
            $this->Modules->setDataFromFailedSave($this->objectType, $requestData);

            return;
        }
        if ($response['data']) {
            $response['data'] = [ $response['data'] ];
        }

        $this->Thumbs->urls($response);

        $this->set((array)$response);
        $this->setSerialize(array_keys($response));
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
            $source = $this->apiClient->getObject($id, $this->objectType);
            $attributes = $source['data']['attributes'];
            $attributes['uname'] = '';
            unset($attributes['relationships']);
            $attributes['title'] = $this->getRequest()->getQuery('title');
            $attributes['status'] = 'draft';
            $save = $this->apiClient->save($this->objectType, $attributes);
            $destination = (string)Hash::get($save, 'data.id');
            $this->Clone->relations($source, $destination);
            $id = $destination;
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id]);
    }

    /**
     * Delete single resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $ids = [];
        if (!empty($this->getRequest()->getData('ids'))) {
            if (is_string($this->getRequest()->getData('ids'))) {
                $ids = explode(',', (string)$this->getRequest()->getData('ids'));
            }
        } elseif (!empty($this->getRequest()->getData('id'))) {
            $ids = [$this->getRequest()->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->deleteObject($id, $this->objectType);
            } catch (BEditaClientException $e) {
                $this->log($e->getMessage(), LogLevel::ERROR);
                $this->Flash->error($e->getMessage(), ['params' => $e]);
                if (!empty($this->getRequest()->getData('id'))) {
                    return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $this->getRequest()->getData('id')]);
                }

                return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id]);
            }
        }
        $this->Flash->success(__('Object(s) deleted'));

        return $this->redirect([
            '_name' => 'modules:list',
            'object_type' => $this->objectType,
        ]);
    }

    /**
     * Relation data load via API => `GET /:object_type/:id/related/:relation`
     *
     * @param string|int $id The object ID.
     * @param string $relation The relation name.
     * @return void
     */
    public function related($id, string $relation): void
    {
        if ($id === 'new') {
            $this->set('data', []);
            $this->setSerialize(['data']);

            return;
        }

        $this->getRequest()->allowMethod(['get']);
        $query = $this->Query->prepare($this->getRequest()->getQueryParams());
        try {
            $response = $this->apiClient->getRelated($id, $this->objectType, $relation, $query);
            $response = $this->ApiFormatter->embedIncluded((array)$response);
        } catch (BEditaClientException $error) {
            $this->log($error->getMessage(), LogLevel::ERROR);

            $this->set(compact('error'));
            $this->setSerialize(['error']);

            return;
        }

        $this->Thumbs->urls($response);

        $this->set((array)$response);
        $this->setSerialize(array_keys($response));
    }

    /**
     * Load resources of $type callig api `GET /:type/`
     * Json response
     *
     * @param string|int $id the object identifier.
     * @param string $type the resource type name.
     * @return void
     */
    public function resources($id, string $type): void
    {
        $this->getRequest()->allowMethod(['get']);
        $query = $this->Query->prepare($this->getRequest()->getQueryParams());
        try {
            $response = $this->apiClient->get($type, $query);
        } catch (BEditaClientException $error) {
            $this->log($error, LogLevel::ERROR);

            $this->set(compact('error'));
            $this->setSerialize(['error']);

            return;
        }

        $this->set((array)$response);
        $this->setSerialize(array_keys($response));
    }

    /**
     * Relation data load callig api `GET /:object_type/:id/relationships/:relation`
     * Json response
     *
     * @param string|int $id The object ID.
     * @param string $relation The relation name.
     * @return void
     */
    public function relationships($id, string $relation): void
    {
        $this->getRequest()->allowMethod(['get']);
        $available = $this->availableRelationshipsUrl($relation);

        try {
            $query = $this->Query->prepare($this->getRequest()->getQueryParams());
            $response = $this->apiClient->get($available, $query);

            $this->Thumbs->urls($response);
        } catch (BEditaClientException $ex) {
            $this->log($ex->getMessage(), LogLevel::ERROR);

            $this->set('error', $ex->getMessage());
            $this->setSerialize(['error']);

            return;
        }

        $this->set((array)$response);
        $this->setSerialize(array_keys($response));
    }

    /**
     * Retrieve URL to get objects available for a relation
     *
     * @param string $relation The relation name.
     * @return string
     */
    protected function availableRelationshipsUrl(string $relation): string
    {
        $defaults = [
            'children' => '/objects',
            'parent' => '/folders',
            'parents' => '/folders',
        ];
        $defaultUrl = (string)Hash::get($defaults, $relation);
        if (!empty($defaultUrl)) {
            return $defaultUrl;
        }

        $relationsSchema = $this->Schema->getRelationsSchema();
        $types = $this->Modules->relatedTypes($relationsSchema, $relation);
        if (count($types) === 1) {
            return sprintf('/%s', $types[0]);
        }

        return '/objects?filter[type][]=' . implode('&filter[type][]=', $types);
    }

    /**
     * get object properties and format them for index
     *
     * @param string $objectType objecte type name
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

    /**
     * Get objectType
     *
     * @return string|null
     */
    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    /**
     * Set objectType
     *
     * @param string|null $objectType The object type
     * @return void
     */
    public function setObjectType(?string $objectType): void
    {
        $this->objectType = $objectType;
    }

    /**
     * Set schemasByType and filtersByType, considering relations and schemas.
     *
     * @param array $relations The relations
     * @return void
     */
    private function setupViewRelations(array $relations): void
    {
        // setup relations metadata
        $this->Modules->setupRelationsMeta(
            $this->Schema->getRelationsSchema(),
            $relations,
            $this->Properties->relationsList($this->objectType),
            $this->Properties->hiddenRelationsList($this->objectType),
            $this->Properties->readonlyRelationsList($this->objectType)
        );
        $rel = (array)$this->viewBuilder()->getVar('relationsSchema');
        $rightTypes = \App\Utility\Schema::rightTypes($rel);

        // set schemas for relations right types
        $schemasByType = $this->Schema->getSchemasByType($rightTypes);
        $this->set('schemasByType', $schemasByType);
        $this->set('filtersByType', $this->Properties->filtersByType($rightTypes));
    }
}
