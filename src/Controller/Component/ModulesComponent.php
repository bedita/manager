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

namespace App\Controller\Component;

use App\Core\Exception\UploadException;
use App\Utility\OEmbed;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Network\Exception\InternalErrorException;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Component to load available modules.
 *
 * @property \Cake\Controller\Component\AuthComponent $Auth
 */
class ModulesComponent extends Component
{

    /**
     * {@inheritDoc}
     */
    public $components = ['Auth'];

    /**
     * {@inheritDoc}
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
    public function startup() : void
    {
        if (empty($this->Auth->user('id'))) {
            $this->getController()->set(['modules' => [], 'project' => []]);

            return;
        }

        if ($this->getConfig('clearHomeCache')) {
            Cache::delete(sprintf('home_%d', $this->Auth->user('id')));
        }

        $modules = $this->getModules();
        $project = $this->getProject();

        $currentModuleName = $this->getConfig('currentModuleName');
        if (!empty($currentModuleName)) {
            $currentModule = Hash::get($modules, $currentModuleName);
        }

        $this->getController()->set(compact('currentModule', 'modules', 'project'));
    }

    /**
     * Getter for home endpoint metadata.
     *
     * @return array
     */
    protected function getMeta() : array
    {
        try {
            $home = Cache::remember(
                sprintf('home_%d', $this->Auth->user('id')),
                function () {
                    return ApiClientProvider::getApiClient()->get('/home');
                }
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Returning an empty array instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e, LogLevel::ERROR);

            return [];
        }

        return !empty($home['meta']) ? $home['meta'] : [];
    }

    /**
     * Create internal list of available modules in `$this->modules` as an array with `name` as key
     * and return it.
     * Modules are read from `/home` endpoint
     *
     * @return array
     */
    public function getModules() : array
    {
        $modulesOrder = Configure::read('Modules.order');

        $meta = $this->getMeta();
        $modules = collection(Hash::get($meta, 'resources', []))
            ->map(function (array $data, $endpoint) {
                $name = substr($endpoint, 1);

                return $data + compact('name');
            })
            ->reject(function (array $data) {
                return Hash::get($data, 'hints.object_type') !== true && Hash::get($data, 'name') !== 'trash';
            })
            ->sortBy(function (array $data) use ($modulesOrder) {
                $name = Hash::get($data, 'name');
                $idx = array_search($name, $modulesOrder);
                if ($idx === false) {
                    // No configured order for this module. Use hash to preserve order, and ensure it is after other modules.
                    $idx = count($modulesOrder) + hexdec(hash('crc32', $name));

                    if ($name === 'trash') {
                        // Trash eventually.
                        $idx = PHP_INT_MAX;
                    }
                }

                return -$idx;
            })
            ->toList();
        $plugins = Configure::read('Modules.plugins');
        if ($plugins) {
            $modules = array_merge($modules, $plugins);
        }
        $this->modules = Hash::combine($modules, '{n}.name', '{n}');

        return $this->modules;
    }

    /**
     * Get information about current project.
     *
     * @return array
     */
    public function getProject() : array
    {
        $meta = $this->getMeta();
        $project = [
            'name' => Hash::get($meta, 'project.name', ''),
            'version' => Hash::get($meta, 'version', ''),
            'colophon' => '', // TODO: populate this value.
        ];

        return $project;
    }

    /**
     * Check if an object type is abstract or concrete.
     * This method MUST NOT be called from `beforeRender` since `$this->modules` array is still not initialized.
     *
     * @param string $name Name of object type.
     * @return bool True if abstract, false if concrete
     */
    public function isAbstract(string $name) : bool
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
    public function objectTypes(?bool $abstract = null) : array
    {
        $types = [];
        foreach ($this->modules as $name => $data) {
            if (!$data['hints']['object_type']) {
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
    protected function oEmbedMeta(string $url) : ?array
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
    public function upload(array &$requestData) : void
    {
        if (empty($requestData['upload_behavior'])) {
            return;
        }
        if ($requestData['upload_behavior'] === 'embed' && !empty($requestData['remote_url'])) {
            $data = $this->oEmbedMeta($requestData['remote_url']);
            $requestData = array_filter($requestData) + $data;

            return;
        }
        if ($requestData['upload_behavior'] !== 'file' || empty($requestData['file'])) {
            return;
        }

        // verify upload form data
        if ($this->checkRequestForUpload($requestData)) {
            // has another stream? drop it
            $this->removeStream($requestData);

            // upload file
            $filename = $requestData['file']['name'];
            $filepath = $requestData['file']['tmp_name'];
            $headers = ['Content-Type' => $requestData['file']['type']];
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
     * @return void
     */
    public function removeStream(array $requestData) : void
    {
        if (empty($requestData['id'])) {
            return;
        }

        $apiClient = ApiClientProvider::getApiClient();
        $response = $apiClient->get(sprintf('/%s/%s/streams', $requestData['model-type'], $requestData['id']));
        if (empty($response['data'])) { // no streams for media
            return;
        }
        $streamId = Hash::get($response, 'data.0.id');
        $apiClient->deleteObject($streamId, 'streams');
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
    public function assocStreamToMedia(string $streamId, array &$requestData, string $defaultTitle) : string
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
     *
     * @param array $requestData The request data
     * @return bool true if upload is possible and needed
     */
    public function checkRequestForUpload(array $requestData) : bool
    {
        // check if change file is empty
        if ($requestData['file']['error'] === UPLOAD_ERR_NO_FILE) {
            return false;
        }

        // if upload error, throw exception
        if ($requestData['file']['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException(null, $requestData['file']['error']);
        }

        // verify presence and value of 'name', 'tmp_name', 'type'
        foreach (['name', 'tmp_name', 'type'] as $field) {
            if (empty($requestData['file'][$field]) || !is_string($requestData['file'][$field])) {
                throw new InternalErrorException(sprintf('Invalid form data: file.%s', $field));
            }
        }
        // verify 'model-type'
        if (empty($requestData['model-type']) || !is_string($requestData['model-type'])) {
            throw new InternalErrorException('Invalid form data: model-type');
        }

        return true;
    }
}
