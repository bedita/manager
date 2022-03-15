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

use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Read project configuration from `/config`.
 */
class ProjectConfigurationComponent extends Component
{
    /**
     * Cache config name for project configuration.
     *
     * @var string
     */
    public const CACHE_CONFIG = '_project_config_';

    /**
     * Read project configuration from API using cache ad write it in `Project.config` config key.
     *
     * @return array Project configuration in `key => value` format.
     */
    public function read(): array
    {
        $config = Configure::read('Project.config');
        if (!empty($config)) {
            return $config;
        }
        try {
            $config = Cache::remember(
                CacheTools::cacheKey('project'),
                function () {
                    return $this->fetchConfig();
                },
                self::CACHE_CONFIG
            );
            Configure::write('Project.config', $config);
        } catch (BEditaClientException $e) {
            // Something bad happened
            $this->log($e->getMessage(), LogLevel::ERROR);

            return [];
        }

        return $config;
    }

    /**
     * Fetch Project configuration via API and manipulate response array.
     *
     * @return array Configuration array with a config key => data structure.
     */
    protected function fetchConfig(): array
    {
        $response = (array)ApiClientProvider::getApiClient()->get('config', ['page_size' => 100]);

        $config = Hash::combine($response, 'data.{n}.attributes.name', 'data.{n}.attributes.content');
        array_walk(
            $config,
            function (&$value, $key) {
                $value = json_decode($value, true);
            }
        );

        return $config;
    }
}
