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

use App\Model\API\BEditaClientException;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Psr\Log\LogLevel;

/**
 * Handles JSON Schema of objects and resources.
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
     * Read type JSON Schema from API using internal cache.
     *
     * @param string|null $type Type to get schema for. By default, configured type is used.
     * @return array|bool JSON Schema.
     */
    public function getSchema($type = null)
    {
        // TODO: handle multiple projects -> key schema may differ
        if ($type === null) {
            $type = $this->getConfig('type');
        }

        try {
            $schema = Cache::remember(
                $type,
                function () use ($type) {
                    return $this->fetchSchema($type);
                },
                self::CACHE_CONFIG
            );
        } catch (BEditaClientException $e) {
            // Something bad happened. Booleans **ARE** valid JSON Schemas: returning `false` instead.
            // The exception is being caught _outside_ of `Cache::remember()` to avoid caching the fallback.
            $this->log($e, LogLevel::ERROR);

            return false;
        }

        return $schema;
    }

    /**
     * Fetch JSON Schema via API.
     *
     * @param string $type Type to get schema for.
     * @return array|bool JSON Schema.
     */
    protected function fetchSchema($type)
    {
        /** @var \App\Model\API\BEditaClient */
        $apiClient = $this->getConfig('apiClient');

        return $apiClient->schema($type);
    }
}
