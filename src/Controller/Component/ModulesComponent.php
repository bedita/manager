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

use BEdita\WebTools\ApiClientProvider;
use BEdita\SDK\BEditaClientException;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
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
     * Read modules and project info from `/home' endpoint.
     *
     * @return void
     */
    public function beforeRender() : void
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
        if (isset($currentModuleName)) {
            $currentModule = $this->getModuleByName($modules, $currentModuleName);
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
     * Get list of available modules.
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

        return $modules;
    }

    /**
     * Get a module by its name.
     *
     * @param array $modules List of all modules.
     * @param string $name Name of module to extract.
     * @return array|null
     */
    public function getModuleByName(array $modules, string $name) : ?array
    {
        foreach ($modules as $module) {
            if (Hash::get($module, 'name') === $name) {
                return $module;
            }
        }

        return null;
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
}
