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
     * @param string|null $revision Schema revision.
     * @return array|bool JSON Schema.
     */
    public function getSchema(string $type = null, string $revision = null)
    {
        // TODO: handle multiple projects -> key schema may differ
        if ($type === null) {
            $type = $this->getConfig('type');
        }

        $schema = $this->loadWithRevision($type, $revision);
        if ($schema !== false) {
            return $schema;
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
     * Load schema from cache with revision check.
     * If cached revision don't match cache is removed.
     *
     * @param string $type Type to get schema for. By default, configured type is used.
     * @param string $revision Schema revision.
     * @return array|bool Cached schema if revision match, otherwise false
     */
    protected function loadWithRevision(string $type, string $revision = null)
    {
        $schema = Cache::read($type, self::CACHE_CONFIG);
        if ($schema === false) {
            return false;
        }
        $cacheRevision = empty($schema['revision']) ? null : $schema['revision'];
        if ($cacheRevision === $revision) {
            return $schema;
        }
        // remove from cache if revision don't match
        Cache::delete($type, self::CACHE_CONFIG);

        return false;
    }

    /**
     * Fetch JSON Schema via API.
     *
     * @param string $type Type to get schema for.
     * @return array|bool JSON Schema.
     */
    protected function fetchSchema(string $type)
    {
        /** @var \BEdita\SDK\BEditaClient */
        $apiClient = $this->getConfig('apiClient');

        return $apiClient->schema($type);
    }
}
