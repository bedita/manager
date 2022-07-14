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
use App\Utility\OEmbed;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Component to load available modules.
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \App\Controller\Component\ConfigComponent $Config
 * @property \App\Controller\Component\SchemaComponent $Schema
 */
class ModulesComponent extends Component
{
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
    public $components = ['Authentication', 'Config', 'Schema'];

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'currentModuleName' => null,
        'clearHomeCache' => false,
    ];

    /**
     * Project modules for a user from `/home` endpoint
     *
     * @var array
     */
    protected $modules = [];

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

        $modules = $this->getModules();
        $project = $this->getProject();
        $uploadable = (array)Hash::get($this->Schema->objectTypesFeatures(), 'uploadable');
        $this->getController()->set(compact('modules', 'project', 'uploadable'));

        $currentModuleName = $this->getConfig('currentModuleName');
        if (!empty($currentModuleName)) {
            $currentModule = Hash::get($modules, $currentModuleName);
        }

        if (!empty($currentModule)) {
            $this->getController()->set(compact('currentModule'));
        }
    }

    /**
     * Getter for home endpoint metadata.
     *
     * @return array
     */
    protected function getMeta(): array
    {
        try {
            /** @var \Authentication\Identity|null $user */
            $user = $this->Authentication->getIdentity();
            $home = Cache::remember(
                sprintf('home_%d', $user->get('id')),
                function () {
                    return ApiClientProvider::getApiClient()->get('/home');
                }
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Returning an empty array instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return !empty($home['meta']) ? $home['meta'] : [];
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
        $metaModules = $this->modulesFromMeta();
        $modules = array_intersect_key($modules, $metaModules);
        array_walk(
            $modules,
            function (&$data, $key) use ($metaModules) {
                $data = array_merge((array)Hash::get($metaModules, $key), $data);
            }
        );
        $this->modules = array_merge(
            $modules,
            array_diff_key($metaModules, $modules),
            $pluginModules
        );
        $this->modulesByAccessControl();

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

        $roles = (array)$user->get('roles');
        $hidden = [];
        $readonly = [];
        foreach ($roles as $role) {
            $h = (array)Hash::get($accessControl, sprintf('%s.hidden', $role));
            $hidden = empty($hidden) ? $h : array_intersect($hidden, $h);
            $r = (array)Hash::get($accessControl, sprintf('%s.readonly', $role));
            $readonly = empty($readonly) ? $r : array_intersect($readonly, $r);
        }
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
        $meta = $this->getMeta();
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
        $meta = $this->getMeta();
        $project = (array)Configure::read('Project');
        $name = (string)Hash::get($project, 'name', Hash::get($meta, 'project.name'));
        $version = Hash::get($meta, 'version', '');

        return compact('name', 'version');
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

            return $response['data']['id'];
        }

        // assoc existing media to stream
        $id = $requestData['id'];
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
     * Set session data for `failedSave.{type}.{id}` and `failedSave.{type}.{id}__timestamp`.
     *
     * @param string $type The object type.
     * @param array $data The data to store into session.
     * @return void
     */
    public function setDataFromFailedSave(string $type, array $data): void
    {
        if (empty($data) || empty($data['id']) || empty($type)) {
            return;
        }
        $key = sprintf('failedSave.%s.%s', $type, $data['id']);
        $session = $this->getController()->getRequest()->getSession();
        unset($data['id']); // remove 'id', avoid future merged with attributes
        $session->write($key, $data);
        $session->write(sprintf('%s__timestamp', $key), time());
    }

    /**
     * Set current attributes from loaded $object data in `currentAttributes`.
     * Load session failure data if available.
     *
     * @param array $object The object.
     * @return void
     */
    public function setupAttributes(array &$object): void
    {
        $currentAttributes = json_encode((array)Hash::get($object, 'attributes'));
        $this->getController()->set(compact('currentAttributes'));

        $this->updateFromFailedSave($object);
    }

    /**
     * Update object, when failed save occurred.
     * Check session data by `failedSave.{type}.{id}` key and `failedSave.{type}.{id}__timestamp`.
     * If data is set and timestamp is not older than 5 minutes.
     *
     * @param array $object The object.
     * @return void
     */
    protected function updateFromFailedSave(array &$object): void
    {
        // check session data for object id => use `failedSave.{type}.{id}` as key
        $session = $this->getController()->getRequest()->getSession();
        $key = sprintf(
            'failedSave.%s.%s',
            Hash::get($object, 'type'),
            Hash::get($object, 'id')
        );
        $data = $session->read($key);
        if (empty($data)) {
            return;
        }

        // read timestamp session key
        $timestampKey = sprintf('%s__timestamp', $key);
        $timestamp = $session->read($timestampKey);

        // if data exist for {type} and {id} and `__timestamp` not too old (<= 5 minutes)
        if ($timestamp > strtotime('-5 minutes')) {
            //  => merge with $object['attributes']
            $object['attributes'] = array_merge($object['attributes'], (array)$data);
        }

        // remove session data
        $session->delete($key);
        $session->delete($timestampKey);
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
                $names
            )
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
     * Save objects and set 'id' inside each object in $objects
     *
     * @param array $objects The objects
     * @return void
     */
    public function saveObjects(array &$objects): void
    {
        if (empty($objects)) {
            return;
        }
        foreach ($objects as &$obj) {
            $this->saveObject($obj);
        }
    }

    /**
     * Save single object and set 'id' inside param $object
     *
     * @param array $object The object
     * @return void
     */
    public function saveObject(array &$object): void
    {
        // no attributes? then return
        if (empty($object['attributes'])) {
            return;
        }
        foreach ($object['attributes'] as $key => $val) {
            if ($key === 'status' || empty($val)) {
                continue;
            }
            $apiClient = ApiClientProvider::getApiClient();
            $saved = $apiClient->save($object['type'], array_merge(['id' => $object['id'] ?? null], $object['attributes']));
            $object['id'] = Hash::get($saved, 'data.id');

            return; // not necessary cycling over all attributes
        }
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
            $method = (string)Hash::get($data, 'method');
            $relation = (string)Hash::get($data, 'relation');
            $relatedIds = (array)Hash::get($data, 'relatedIds');
            // Do we need to call `saveObjects`? (probably NOT)
            $this->saveObjects($relatedIds);
            if (!in_array($method, ['addRelated', 'removeRelated', 'replaceRelated'])) {
                throw new BadRequestException(__('Bad related data method'));
            }
            if ($relation === 'children' && $type === 'folders' && $method === 'removeRelated') {
                $this->folderChildrenRemove($id, $relatedIds);

                return;
            }
            if ($relation === 'children' && $type === 'folders' && in_array($method, ['addRelated', 'replaceRelated'])) {
                $this->folderChildrenRelated($id, $relatedIds);

                return;
            }
            ApiClientProvider::getApiClient()->{$method}($id, $type, $relation, $relatedIds);
        }
    }

    /**
     * Remove folder children.
     * When a child is a subfolder, this use `PATCH /folders/:id/relationships/parent` to set parent to null
     * When a child is not a subfolder, it's removed as usual by `removeRelated`
     *
     * @param string $id The Object ID
     * @param array $related Related objects
     * @return void
     */
    protected function folderChildrenRemove(string $id, array $related): void
    {
        $children = [];
        // seek for subfolders, remove them changing parent to null
        foreach ($related as $obj) {
            if ($obj['type'] === 'folders') {
                $endpoint = sprintf('/folders/%s/relationships/parent', $obj['id']);
                ApiClientProvider::getApiClient()->patch($endpoint, null);
                continue;
            }
            $children[] = $obj;
        }
        // children, not folders
        if (!empty($children)) {
            foreach ($children as $child) {
                ApiClientProvider::getApiClient()->removeRelated($id, 'folders', 'children', [$child]);
            }
        }
    }

    /**
     * Handle special case of `children` relation on `folders`
     *
     * @param string $id Object ID.
     * @param array $relatedIds Related objects as id/type pairs.
     * @return void
     */
    protected function folderChildrenRelated(string $id, array $relatedIds): void
    {
        $notFolders = [];
        $apiClient = ApiClientProvider::getApiClient();
        foreach ($relatedIds as $item) {
            $relType = Hash::get($item, 'type');
            $relId = Hash::get($item, 'id');
            if ($relType !== 'folders') {
                $notFolders[] = $item;
                continue;
            }
            // invert relation call => use 'parent' relation on children folder
            $data = compact('id') + ['type' => 'folders'];
            if (Hash::check((array)$item, 'meta')) {
                $data += ['meta' => Hash::get((array)$item, 'meta')];
            }
            $apiClient->replaceRelated($relId, 'folders', 'parent', $data);
        }

        if (!empty($notFolders)) {
            foreach ($notFolders as $notFolder) {
                $apiClient->addRelated($id, 'folders', 'children', [$notFolder]);
            }
        }
    }
}
