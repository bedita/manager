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

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
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
     * Get list of available modules as an array with `name` as key
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

        return Hash::combine($modules, '{n}.name', '{n}');
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
     * This method must be called in `beforeRender` since controller `viewVars` is used
     *
     * @param string $name Name of object type.
     * @return bool True if abstract, false if concrete
     */
    public function isAbstract(string $name) : bool
    {
        $modules = Hash::get($this->getController()->viewVars, 'modules', []);

        return (bool)Hash::get($modules, sprintf('%s.hints.multiple_types', $name), false);
    }

    /**
     * Get list of object types
     * This method must be called in `beforeRender` since controller `viewVars` is used
     *
     * @param bool|null $abstract Only abstract or concrete types.
     * @return array Type names list
     */
    public function objectTypes(?bool $abstract = null) : array
    {
        $modules = Hash::get($this->getController()->viewVars, 'modules', []);
        $types = [];
        foreach ($modules as $name => $data) {
            if (!$data['hints']['object_type']) {
                continue;
            }
            if ($abstract === null) {
                $types[] = $name;
            } elseif ($abstract === $data['hints']['multiple_types']) {
                $types[] = $name;
            }
        }

        return $types;
    }
}
