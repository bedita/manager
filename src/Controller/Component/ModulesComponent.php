<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Component;

use App\Core\Exception\UploadException;
use App\Utility\DateRangesTools;
use App\Utility\OEmbed;
use App\Utility\RelationsTools;
use App\Utility\SchemaTrait;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Client\Response;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\I18n\I18n;
use Cake\Utility\Hash;

/**
 * Component to load available modules.
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \App\Controller\Component\ChildrenComponent $Children
 * @property \App\Controller\Component\ConfigComponent $Config
 * @property \App\Controller\Component\ParentsComponent $Parents
 * @property \App\Controller\Component\SchemaComponent $Schema
 */
class ModulesComponent extends Component
{
    use SchemaTrait;

    /**
     * Fixed relationships to be loaded for each object
     *
     * @var array
     */
    public const FIXED_RELATIONSHIPS = [
        'parent',
        'children',
        'parents',
        'translations',
        'streams',
        'roles',
    ];

    /**
     * @inheritDoc
     */
    public array $components = ['Authentication', 'Children', 'Config', 'Parents', 'Schema'];

    /**
     * @inheritDoc
     */
    protected array $_defaultConfig = [
        'currentModuleName' => null,
        'clearHomeCache' => false,
    ];

    /**
     * Project modules for a user from `/home` endpoint
     *
     * @var array
     */
    protected array $modules = [];

    /**
     * Other "logic" modules, non objects
     *
     * @var array
     */
    protected array $otherModules = [
        'tags' => [
            'name' => 'tags',
            'hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if (!empty($user)) {
            $this->getController()->set('modules', $this->getModules());
        }

        return null;
    }

    /**
     * Read modules and project info from `/home' endpoint.
     *
     * @return void
     */
    public function startup(): void
    {
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if (empty($user) || !$user->get('id')) {
            $this->getController()->set(['modules' => [], 'project' => []]);

            return;
        }

        if ($this->getConfig('clearHomeCache')) {
            Cache::delete(sprintf('home_%d', $user->get('id')));
        }

        $project = $this->getProject();
        $uploadable = (array)Hash::get($this->Schema->objectTypesFeatures(), 'uploadable');
        $this->getController()->set(compact('project', 'uploadable'));

        $currentModuleName = $this->getConfig('currentModuleName');
        $modules = (array)$this->getController()->viewBuilder()->getVar('modules');
        if (!empty($currentModuleName)) {
            $currentModule = Hash::get($modules, $currentModuleName);
        }

        if (!empty($currentModule)) {
            $this->getController()->set(compact('currentModule'));
        }
    }

    /**
     * Create internal list of available modules in `$this->modules` as an array with `name` as key
     * and return it.
     * Modules are created from configuration and merged with information read from `/home` endpoint
     *
     * @return array
     */
    public function getModules(): array
    {
        $modules = (array)Configure::read('Modules');
        $pluginModules = array_filter($modules, function ($item) {
            return !empty($item['route']);
        });
        $metaModules = $this->modulesFromMeta() + $this->otherModules;
        $modules = array_intersect_key($modules, $metaModules);
        array_walk(
            $modules,
            function (&$data, $key) use ($metaModules): void {
                $data = array_merge((array)Hash::get($metaModules, $key), $data);
            },
        );
        $this->modules = array_merge(
            $modules,
            array_diff_key($metaModules, $modules),
            $pluginModules,
        );
        $this->modulesByAccessControl();
        if (!$this->Schema->tagsInUse()) {
            unset($this->modules['tags']);
        }

        return $this->modules;
    }

    /**
     * This filters modules and apply 'AccessControl' config by user role, if any.
     * Module can be "hidden": remove from modules.
     * Module can be "readonly": adjust "hints.allow" for module.
     *
     * @return void
     */
    protected function modulesByAccessControl(): void
    {
        $accessControl = (array)Configure::read('AccessControl');
        if (empty($accessControl)) {
            return;
        }
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if (empty($user) || empty($user->getOriginalData())) {
            return;
        }
        $roles = array_intersect(array_keys($accessControl), (array)$user->get('roles'));
        $modules = (array)array_keys($this->modules);
        $hidden = [];
        $readonly = [];
        $write = [];
        foreach ($roles as $role) {
            $h = (array)Hash::get($accessControl, sprintf('%s.hidden', $role));
            $hidden = empty($hidden) ? $h : array_intersect($hidden, $h);
            $r = (array)Hash::get($accessControl, sprintf('%s.readonly', $role));
            $readonly = empty($readonly) ? $r : array_intersect($readonly, $r);
            $write = array_unique(array_merge($write, array_diff($modules, $hidden, $readonly)));
        }
        // Note: https://github.com/bedita/manager/issues/969 Accesses priority is "write" > "read" > "hidden"
        $readonly = array_diff($readonly, $write);
        $hidden = array_diff($hidden, $readonly, $write);
        if (empty($hidden) && empty($readonly)) {
            return;
        }
        // remove "hidden"
        $this->modules = array_diff_key($this->modules, array_flip($hidden));
        // make sure $readonly contains valid module names
        $readonly = array_intersect($readonly, array_keys($this->modules));
        foreach ($readonly as $key) {
            $path = sprintf('%s.hints.allow', $key);
            $allow = (array)Hash::get($this->modules, $path);
            $this->modules[$key]['hints']['allow'] = array_diff($allow, ['POST', 'PATCH', 'DELETE']);
        }
    }

    /**
     * Modules data from `/home` endpoint 'meta' response.
     * Modules are object endpoints from BE4 API
     *
     * @return array
     */
    protected function modulesFromMeta(): array
    {
        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $meta = $this->getMeta($user);
        $modules = collection(Hash::get($meta, 'resources', []))
            ->map(function (array $data, $endpoint) {
                $name = substr($endpoint, 1);

                return $data + compact('name');
            })
            ->reject(function (array $data) {
                return Hash::get($data, 'hints.object_type') !== true && !in_array(Hash::get($data, 'name'), ['trash', 'translations']);
            })
            ->toList();

        return Hash::combine($modules, '{n}.name', '{n}');
    }

    /**
     * Get information about current project.
     *
     * @return array
     */
    public function getProject(): array
    {
        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $api = $this->getMeta($user);
        $apiName = (string)Hash::get($api, 'project.name');
        $apiName = str_replace('API', '', $apiName);
        $api['project']['name'] = $apiName;

        return [
            'api' => (array)Hash::get($api, 'project'),
            'beditaApi' => [
                'name' => (string)Hash::get(
                    (array)Configure::read('Project'),
                    'name',
                    (string)Hash::get($api, 'project.name'),
                ),
                'version' => (string)Hash::get($api, 'version'),
            ],
        ];
    }

    /**
     * Check if an object type is abstract or concrete.
     * This method MUST NOT be called from `beforeRender` since `$this->modules` array is still not initialized.
     *
     * @param string $name Name of object type.
     * @return bool True if abstract, false if concrete
     */
    public function isAbstract(string $name): bool
    {
        return (bool)Hash::get($this->modules, sprintf('%s.hints.multiple_types', $name), false);
    }

    /**
     * Get list of object types
     * This method MUST NOT be called from `beforeRender` since `$this->modules` array is still not initialized.
     *
     * @param bool|null $abstract Only abstract or concrete types.
     * @return array Type names list
     */
    public function objectTypes(?bool $abstract = null): array
    {
        $types = [];
        foreach ($this->modules as $name => $data) {
            if (empty($data['hints']['object_type'])) {
                continue;
            }
            if ($abstract === null || $data['hints']['multiple_types'] === $abstract) {
                $types[] = $name;
            }
        }

        return $types;
    }

    /**
     * Read oEmbed metadata
     *
     * @param string $url Remote URL
     * @return array|null
     * @codeCoverageIgnore
     */
    protected function oEmbedMeta(string $url): ?array
    {
        return (new OEmbed())->readMetadata($url);
    }

    /**
     * Upload a file and store it in a media stream
     * Or create a remote media trying to get some metadata via oEmbed
     *
     * @param array $requestData The request data from form
     * @return void
     */
    public function upload(array &$requestData): void
    {
        $uploadBehavior = Hash::get($requestData, 'upload_behavior', 'file');

        if ($uploadBehavior === 'embed' && !empty($requestData['remote_url'])) {
            $data = $this->oEmbedMeta($requestData['remote_url']);
            $requestData = array_filter($requestData) + $data;

            return;
        }
        if (empty($requestData['file'])) {
            return;
        }

        // verify upload form data
        if ($this->checkRequestForUpload($requestData)) {
            // has another stream? drop it
            $this->removeStream($requestData);

            /** @var \Laminas\Diactoros\UploadedFile $file */
            $file = $requestData['file'];

            // upload file
            $filename = basename($file->getClientFileName());
            $filepath = $file->getStream()->getMetadata('uri');
            $headers = ['Content-Type' => $file->getClientMediaType()];
            $apiClient = ApiClientProvider::getApiClient();
            $response = $apiClient->upload($filename, $filepath, $headers);

            // assoc stream to media
            $streamId = $response['data']['id'];
            $requestData['id'] = $this->assocStreamToMedia($streamId, $requestData, $filename);
        }
        unset($requestData['file'], $requestData['remote_url']);
    }

    /**
     * Remove a stream from a media, if any
     *
     * @param array $requestData The request data from form
     * @return bool
     */
    public function removeStream(array $requestData): bool
    {
        if (empty($requestData['id'])) {
            return false;
        }

        $apiClient = ApiClientProvider::getApiClient();
        $response = $apiClient->get(sprintf('/%s/%s/streams', $requestData['model-type'], $requestData['id']));
        if (empty($response['data'])) { // no streams for media
            return false;
        }
        $streamId = Hash::get($response, 'data.0.id');
        $apiClient->deleteObject($streamId, 'streams');

        return true;
    }

    /**
     * Associate a stream to a media using API
     * If $requestData['id'] is null, create media from stream.
     * If $requestData['id'] is not null, replace properly related stream.
     *
     * @param string $streamId The stream ID
     * @param array $requestData The request data
     * @param string $defaultTitle The default title for media
     * @return string The media ID
     */
    public function assocStreamToMedia(string $streamId, array &$requestData, string $defaultTitle): string
    {
        $apiClient = ApiClientProvider::getApiClient();
        $type = $requestData['model-type'];
        if (empty($requestData['id'])) {
            // create media from stream
            // save only `title` (filename if not set) and `status` in new media object
            $attributes = array_filter([
                'title' => !empty($requestData['title']) ? $requestData['title'] : $defaultTitle,
                'status' => Hash::get($requestData, 'status'),
            ]);
            $data = compact('type', 'attributes');
            $body = compact('data');
            $response = $apiClient->createMediaFromStream($streamId, $type, $body);
            // `title` and `status` saved here, remove from next save
            unset($requestData['title'], $requestData['status']);

            return (string)Hash::get($response, 'data.id');
        }

        // assoc existing media to stream
        $id = (string)Hash::get($requestData, 'id');
        $data = compact('id', 'type');
        $apiClient->replaceRelated($streamId, 'streams', 'object', $data);

        return $id;
    }

    /**
     * Check request data for upload and return true if upload is boht possible and needed
     *
     * @param array $requestData The request data
     * @return bool true if upload is possible and needed
     */
    public function checkRequestForUpload(array $requestData): bool
    {
        /** @var \Laminas\Diactoros\UploadedFile $file */
        $file = $requestData['file'];
        $error = $file->getError();
        // check if change file is empty
        if ($error === UPLOAD_ERR_NO_FILE) {
            return false;
        }

        // if upload error, throw exception
        if ($error !== UPLOAD_ERR_OK) {
            throw new UploadException(null, $error);
        }

        // verify presence and value of 'name', 'tmp_name', 'type'
        $name = $file->getClientFileName();
        if (empty($name)) {
            throw new InternalErrorException('Invalid form data: file.name');
        }
        $uri = $file->getStream()->getMetadata('uri');
        if (empty($uri)) {
            throw new InternalErrorException('Invalid form data: file.tmp_name');
        }

        // verify 'model-type'
        if (empty($requestData['model-type']) || !is_string($requestData['model-type'])) {
            throw new InternalErrorException('Invalid form data: model-type');
        }

        return true;
    }

    /**
     * Check if save can be skipped.
     * This is used to avoid saving object with no changes.
     *
     * @param string $id The object ID
     * @param array $requestData The request data
     * @return bool True if save can be skipped, false otherwise
     */
    public function skipSaveObject(string $id, array &$requestData): bool
    {
        if (empty($id)) {
            return false;
        }
        if (isset($requestData['date_ranges'])) {
            // check if date_ranges has changed
            $type = $this->getController()->getRequest()->getParam('object_type');
            $response = ApiClientProvider::getApiClient()->getObject($id, $type, ['fields' => 'date_ranges']);
            $actualDateRanges = (array)Hash::get($response, 'data.attributes.date_ranges');
            $dr1 = DateRangesTools::toString($actualDateRanges);
            $requestDateRanges = (array)Hash::get($requestData, 'date_ranges');
            $dr2 = DateRangesTools::toString($requestDateRanges);
            if ($dr1 === $dr2) {
                unset($requestData['date_ranges']);
            } else {
                return false;
            }
        }
        $data = array_filter($requestData, function ($key) {
            return !in_array($key, ['id', 'date_ranges', 'permissions']);
        }, ARRAY_FILTER_USE_KEY);

        return empty($data);
    }

    /**
     * Check if save related can be skipped.
     * This is used to avoid saving object relations with no changes.
     *
     * @param string $id The object ID
     * @param array $relatedData The related data
     * @return bool True if save related can be skipped, false otherwise
     */
    public function skipSaveRelated(string $id, array &$relatedData): bool
    {
        if (empty($relatedData)) {
            return true;
        }
        $methods = (array)Hash::extract($relatedData, '{n}.method');
        if (in_array('addRelated', $methods) || in_array('removeRelated', $methods)) {
            return false;
        }
        // check replaceRelated
        $type = $this->getController()->getRequest()->getParam('object_type');
        $rr = $relatedData;
        foreach ($rr as $method => $data) {
            $actualRelated = (array)ApiClientProvider::getApiClient()->getRelated($id, $type, $data['relation']);
            $actualRelated = (array)Hash::get($actualRelated, 'data');
            $actualRelated = RelationsTools::toString($actualRelated);
            $requestRelated = (array)Hash::get($data, 'relatedIds', []);
            $requestRelated = RelationsTools::toString($requestRelated);
            if ($actualRelated === $requestRelated) {
                unset($relatedData[$method]);
                continue;
            }

            return false;
        }

        return empty($relatedData);
    }

    /**
     * Check if save permissions can be skipped.
     * This is used to avoid saving object permissions with no changes.
     *
     * @param string $id The object ID
     * @param array $requestPermissions The request permissions
     * @param array $schema The object type schema
     * @return bool True if save permissions can be skipped, false otherwise
     */
    public function skipSavePermissions(string $id, array $requestPermissions, array $schema): bool
    {
        if (!in_array('Permissions', (array)Hash::get($schema, 'associations'))) {
            return true;
        }
        $requestPermissions = array_map(
            function ($role) {
                return (int)$role;
            },
            $requestPermissions,
        );
        sort($requestPermissions);
        $query = ['filter' => ['object_id' => $id], 'page_size' => 100];
        $objectPermissions = (array)ApiClientProvider::getApiClient()->getObjects('object_permissions', $query);
        $actualPermissions = (array)Hash::extract($objectPermissions, 'data.{n}.attributes.role_id');
        sort($actualPermissions);

        return $actualPermissions === $requestPermissions;
    }

    /**
     * Set current attributes from loaded $object data in `currentAttributes`.
     *
     * @param array $object The object.
     * @return void
     */
    public function setupAttributes(array &$object): void
    {
        $currentAttributes = json_encode((array)Hash::get($object, 'attributes'));
        $this->getController()->set(compact('currentAttributes'));
    }

    /**
     * Setup relations information metadata.
     *
     * @param array $schema Relations schema.
     * @param array $relationships Object relationships.
     * @param array $order Ordered names inside 'main' and 'aside' keys.
     * @param array $hidden List of hidden relations.
     * @param array $readonly List of readonly relations.
     * @return void
     */
    public function setupRelationsMeta(array $schema, array $relationships, array $order = [], array $hidden = [], array $readonly = []): void
    {
        // relations between objects
        $relationsSchema = $this->relationsSchema($schema, $relationships, $hidden, $readonly);
        // relations between objects and resources
        $resourceRelations = array_diff(array_keys($relationships), array_keys($relationsSchema), $hidden, self::FIXED_RELATIONSHIPS);
        // set objectRelations array with name as key and label as value
        $relationNames = array_keys($relationsSchema);

        // define 'main' and 'aside' relation groups
        $aside = array_intersect((array)Hash::get($order, 'aside'), $relationNames);
        $relationNames = array_diff($relationNames, $aside);
        $main = array_intersect((array)Hash::get($order, 'main'), $relationNames);
        $main = array_unique(array_merge($main, $relationNames));

        $objectRelations = [
            'main' => $this->relationLabels($relationsSchema, $main),
            'aside' => $this->relationLabels($relationsSchema, $aside),
        ];

        $this->getController()->set(compact('relationsSchema', 'resourceRelations', 'objectRelations'));
    }

    /**
     * Relations schema by schema and relationships.
     *
     * @param array $schema The schema
     * @param array $relationships The relationships
     * @param array $hidden Hidden relationships
     * @param array $readonly Readonly relationships
     * @return array
     */
    protected function relationsSchema(array $schema, array $relationships, array $hidden = [], array $readonly = []): array
    {
        $types = $this->objectTypes(false);
        sort($types);
        $relationsSchema = array_diff_key(array_intersect_key($schema, $relationships), array_flip($hidden));

        foreach ($relationsSchema as $relName => &$relSchema) {
            if (in_array('objects', (array)Hash::get($relSchema, 'right'))) {
                $relSchema['right'] = $types;
            }
            if (!empty($relationships[$relName]['readonly']) || in_array($relName, $readonly)) {
                $relSchema['readonly'] = true;
            }
        }

        return $relationsSchema;
    }

    /**
     * Retrieve associative array with names as keys and labels as values.
     *
     * @param array $relationsSchema Relations schema.
     * @param array $names Relation names.
     * @return array
     */
    protected function relationLabels(array &$relationsSchema, array $names): array
    {
        return (array)array_combine(
            $names,
            array_map(
                function ($r) use ($relationsSchema) {
                    // return 'label' or 'inverse_label' looking at 'name'
                    $attributes = $relationsSchema[$r]['attributes'];
                    if ($r === $attributes['name']) {
                        return $attributes['label'];
                    }

                    return $attributes['inverse_label'];
                },
                $names,
            ),
        );
    }

    /**
     * Get related types from relation name.
     *
     * @param array $schema Relations schema.
     * @param string $relation Relation name.
     * @return array
     */
    public function relatedTypes(array $schema, string $relation): array
    {
        $relationsSchema = (array)Hash::get($schema, $relation);

        return (array)Hash::get($relationsSchema, 'right');
    }

    /**
     * Save related objects.
     *
     * @param string $id Object ID
     * @param string $type Object type
     * @param array $relatedData Related objects data
     * @return void
     */
    public function saveRelated(string $id, string $type, array $relatedData): void
    {
        foreach ($relatedData as $data) {
            $this->saveRelatedObjects($id, $type, $data);
            $event = new Event('Controller.afterSaveRelated', $this, compact('id', 'type', 'data'));
            $this->getController()->getEventManager()->dispatch($event);
        }
    }

    /**
     * Save related objects per object by ID.
     *
     * @param string $id Object ID
     * @param string $type Object type
     * @param array $data Related object data
     * @return array|null
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function saveRelatedObjects(string $id, string $type, array $data): ?array
    {
        $method = (string)Hash::get($data, 'method');
        if (!in_array($method, ['addRelated', 'removeRelated', 'replaceRelated'])) {
            throw new BadRequestException(__('Bad related data method'));
        }
        $relation = (string)Hash::get($data, 'relation');
        $related = $this->getRelated($data);
        if ($relation === 'parent' && $type === 'folders') {
            return $this->Parents->{$method}($id, $related);
        }
        if ($relation === 'children' && $type === 'folders') {
            return $this->Children->{$method}($id, $related);
        }
        $lang = I18n::getLocale();
        $headers = ['Accept-Language' => $lang];

        return ApiClientProvider::getApiClient()->{$method}($id, $type, $relation, $related, $headers);
    }

    /**
     * Get related objects.
     * If related object has no ID, it will be created.
     *
     * @param array $data Related object data
     * @return array
     */
    public function getRelated(array $data): array
    {
        $related = (array)Hash::get($data, 'relatedIds');
        if (empty($related)) {
            return [];
        }
        $relatedObjects = [];
        foreach ($related as $obj) {
            if (!empty($obj['id'])) {
                $relatedObjects[] = [
                    'id' => $obj['id'],
                    'type' => $obj['type'],
                    'meta' => (array)Hash::get($obj, 'meta'),
                ];
                continue;
            }
            $response = ApiClientProvider::getApiClient()->save(
                (string)Hash::get($obj, 'type'),
                (array)Hash::get($obj, 'attributes'),
            );
            $relatedObjects[] = [
                'id' => Hash::get($response, 'data.id'),
                'type' => Hash::get($response, 'data.type'),
                'meta' => (array)Hash::get($response, 'data.meta'),
            ];
        }

        return $relatedObjects;
    }
}
