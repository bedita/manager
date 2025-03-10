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
use App\Utility\Message;
use App\Utility\PermissionsTrait;
use App\Utility\Schema;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\Utility\ApiTools;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\I18n;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Modules controller: list, add, edit, remove objects
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 * @property \App\Controller\Component\ChildrenComponent $Children
 * @property \App\Controller\Component\HistoryComponent $History
 * @property \App\Controller\Component\ObjectsEditorsComponent $ObjectsEditors
 * @property \App\Controller\Component\ParentsComponent $Parents
 * @property \App\Controller\Component\ProjectConfigurationComponent $ProjectConfiguration
 * @property \App\Controller\Component\PropertiesComponent $Properties
 * @property \App\Controller\Component\QueryComponent $Query
 * @property \App\Controller\Component\ThumbsComponent $Thumbs
 * @property \BEdita\WebTools\Controller\Component\ApiFormatterComponent $ApiFormatter
 */
class ModulesController extends AppController
{
    use PermissionsTrait;

    /**
     * Object type currently used
     *
     * @var string|null
     */
    protected ?string $objectType = null;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Children');
        $this->loadComponent('History');
        $this->loadComponent('ObjectsEditors');
        $this->loadComponent('Parents');
        $this->loadComponent('Properties');
        $this->loadComponent('ProjectConfiguration');
        $this->loadComponent('Query');
        $this->loadComponent('Thumbs', Configure::read('Thumbs', []));
        $this->loadComponent('BEdita/WebTools.ApiFormatter');
        if ($this->getRequest()->getParam('object_type')) {
            $this->objectType = $this->getRequest()->getParam('object_type');
            $this->Modules = $this->components()->get('Modules');
            $this->Modules->setConfig('currentModuleName', $this->objectType);
            $this->Schema = $this->components()->get('Schema');
            $this->Schema->setConfig('type', $this->objectType);
        }
        $this->FormProtection->setConfig('unlockedActions', ['save']);
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
            $params = $this->Query->index();
            $response = $this->apiClient->getObjects($this->objectType, $params);
            if (empty($params['q']) && empty($params['filter'])) {
                CacheTools::setModuleCount((array)$response, $this->Modules->getConfig('currentModuleName'));
            }
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
        // custom properties
        $this->set('customProps', $this->Schema->customProps($this->objectType));

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
    public function view(string|int $id): ?Response
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
        $typeIncluded = Hash::combine($included, '{n}.id', '{n}', '{n}.type');
        $streams = Hash::get($typeIncluded, 'streams');
        $this->History->load($id, $object);
        $this->set(compact('object', 'included', 'schema', 'streams'));
        $this->set('properties', $this->Properties->viewGroups($object, $this->objectType));
        $this->set('foldersSchema', $this->Schema->getSchema('folders'));

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
    public function uname(string|int $id): ?Response
    {
        try {
            $response = $this->apiClient->get(sprintf('/objects/%s', $id));
        } catch (BEditaClientException $e) {
            $msg = $e->getMessage();
            $msgNotFound = sprintf(__('Resource "%s" not found', true), $id);
            $msgNotAvailable = sprintf(__('Resource "%s" not available. Error: %s', true), $id, $msg);
            $error = $e->getCode() === 404 ? $msgNotFound : $msgNotAvailable;
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
            null
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
            $uname = Hash::get($requestData, 'uname');
            if (!empty($uname) && is_numeric($uname)) {
                $this->set(['error' => __('Invalid numeric uname. Change it to a valid string')]);
                $this->setSerialize(['error']);

                return;
            }
            $id = Hash::get($requestData, 'id');
            // skip save if no data changed
            if (empty($relatedData) && count($requestData) === 1 && !empty($id)) {
                $response = $this->apiClient->getObject($id, $this->objectType, ['count' => 'all']);
                $this->Thumbs->urls($response);
                $this->set((array)$response);
                $this->setSerialize(array_keys($response));

                return;
            }

            // upload file (if available)
            $this->Modules->upload($requestData);

            // save data
            $lang = I18n::getLocale();
            $headers = ['Accept-Language' => $lang];
            $response = $this->apiClient->save($this->objectType, $requestData, $headers);
            $this->savePermissions(
                (array)$response,
                (array)$this->Schema->getSchema($this->objectType),
                (array)Hash::get($requestData, 'permissions')
            );
            $id = (string)Hash::get($response, 'data.id');
            $this->Modules->saveRelated($id, $this->objectType, $relatedData);
            $options = [
                'id' => Hash::get($response, 'data.id'),
                'type' => $this->objectType,
                'data' => $requestData,
            ];
            $event = new Event('Controller.afterSave', $this, $options);
            $this->getEventManager()->dispatch($event);
        } catch (BEditaClientException $error) {
            $message = new Message($error);
            $this->log($message->get(), LogLevel::ERROR);
            $this->Flash->error($message->get(), ['params' => $error]);
            $this->set(['error' => $message->get()]);
            $this->setSerialize(['error']);

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
    public function clone(string|int $id): ?Response
    {
        $this->viewBuilder()->setTemplate('view');
        $schema = $this->Schema->getSchema();
        if (!is_array($schema)) {
            $this->Flash->error(__('Cannot create abstract objects or objects without schema'));

            return $this->redirect(['_name' => 'modules:list', 'object_type' => $this->objectType]);
        }
        try {
            $modified = [
                'title' => $this->getRequest()->getQuery('title'),
                'status' => 'draft',
            ];
            $reset = (array)Configure::read(sprintf('Clone.%s.reset', $this->objectType));
            foreach ($reset as $field) {
                $modified[$field] = null;
            }
            $included = [];
            foreach (['relationships', 'translations'] as $attribute) {
                if ($this->getRequest()->getQuery($attribute) === 'true') {
                    $included[] = $attribute;
                }
            }
            $clone = $this->apiClient->clone($this->objectType, $id, $modified, $included);
            $id = (string)Hash::get($clone, 'data.id');
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
        $id = $this->getRequest()->getData('id');
        $ids = $this->getRequest()->getData('ids');
        $ids = is_string($ids) ? explode(',', $ids) : $ids;
        $ids = empty($ids) ? [$id] : $ids;
        try {
            $this->apiClient->deleteObjects($ids, $this->objectType);
            $eventManager = $this->getEventManager();
            foreach ($ids as $id) {
                $event = new Event('Controller.afterDelete', $this, ['id' => $id, 'type' => $this->objectType]);
                $eventManager->dispatch($event);
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            $id = $this->getRequest()->getData('id');
            $options = empty($id) ? $this->referer() : ['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id];

            return $this->redirect($options);
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
    public function related(string|int $id, string $relation): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        if ($id === 'new') {
            $this->set('data', []);
            $this->setSerialize(['data']);

            return;
        }
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
    public function resources(string|int $id, string $type): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
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
     * Relation data load calling api `GET /:object_type/:id/relationships/:relation`
     * Json response
     *
     * @param string|int $id The object ID.
     * @param string $relation The relation name.
     * @return void
     */
    public function relationships(string|int $id, string $relation): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
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

        return count($types) === 1 ? sprintf('/%s', $types[0]) : '/objects?filter[type][]=' . implode('&filter[type][]=', $types);
    }

    /**
     * get object properties and format them for index
     *
     * @param string $objectType objecte type name
     * @return array $schema
     */
    public function getSchemaForIndex(string $objectType): array
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
        // setup relations schema
        $relationsSchema = $this->Schema->getRelationsSchema();
        $this->set('relationsSchema', $relationsSchema);

        // setup relations metadata
        $this->Modules->setupRelationsMeta(
            $relationsSchema,
            $relations,
            $this->Properties->relationsList($this->objectType),
            $this->Properties->hiddenRelationsList($this->objectType),
            $this->Properties->readonlyRelationsList($this->objectType)
        );

        // set right types, considering the object type relations
        $rel = (array)$this->viewBuilder()->getVar('relationsSchema');
        $rightTypes = Schema::rightTypes($rel);
        $this->set('rightTypes', $rightTypes);

        // set schemas for relations right types
        $schemasByType = $this->Schema->getSchemasByType($rightTypes);
        $this->set('schemasByType', $schemasByType);
        $this->set('filtersByType', $this->Properties->filtersByType($rightTypes));
    }

    /**
     * Get list of users / no email, no relationships, no links, no schema, no included.
     *
     * @return void
     */
    public function users(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $query = array_merge(
            $this->getRequest()->getQueryParams(),
            ['fields' => 'id,title,username,name,surname']
        );
        $response = (array)$this->apiClient->get('users', $query);
        $response = ApiTools::cleanResponse($response);
        $data = (array)Hash::get($response, 'data');
        $meta = (array)Hash::get($response, 'meta');
        $this->set(compact('data', 'meta'));
        $this->setSerialize(['data', 'meta']);
    }

    /**
     * Get single resource, minimal data / no relationships, no links, no schema, no included.
     *
     * @param string $id The object ID
     * @return void
     */
    public function get(string $id): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $response = (array)$this->apiClient->getObject($id, 'objects');
        $query = array_merge(
            $this->getRequest()->getQueryParams(),
            ['fields' => 'id,title,description,uname,status,media_url']
        );
        $response = (array)$this->apiClient->getObject($id, $response['data']['type'], $query);
        $response = ApiTools::cleanResponse($response);
        $data = (array)Hash::get($response, 'data');
        $meta = (array)Hash::get($response, 'meta');
        $this->set(compact('data', 'meta'));
        $this->setSerialize(['data', 'meta']);
    }
}
