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

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Handles JSON SCHEMA of objects and resources
 *
 */
class SchemaComponent extends Component
{
    /**
     * Cache config name for type schemas.
     *
     * @var string
     */
    const CACHE_CONFIG = '_schema_types_';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'apiClient' => null, // BE4 api client
        'type' => null, // resource or object type name
    ];

    /**
     * Read type json schema from API using internal cache
     *
     * @param null|string $type Resource or object type (optional)
     * @return array JSON SCHEMA in array format
     */
    public function getSchema($type = null) {

        // TODO: handle multiple projects -> key schema may differ
        $type = empty($type) ? $this->getConfig('type') : $type;

        $schema = Cache::remember(
            $type,
            function () use ($type) {
                return $this->fetchSchema($type);
            },
            self::CACHE_CONFIG
        );

        return $schema;
    }

    /**
     * Fetch JSON SCHEMA via API
     *
     * @param string $type Resource or object type name
     * @return array JSON SCHEMA in array format
     */
    public function fetchSchema($type) {
        /** @var \App\Model\API\BEditaClient */
        $apiClient = $this->getConfig('apiClient');

        return $apiClient->schema($type);
    }
}
