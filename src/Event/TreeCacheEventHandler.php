<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Event;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;

/**
 * Event listener for cache management.
 */
class TreeCacheEventHandler implements EventListenerInterface
{
    /**
     * Cache config name for tree data.
     *
     * @var string
     */
    public const CACHE_CONFIG = '_tree_data_';

    /**
     * @inheritDoc
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.afterDelete' => 'afterDelete',
            'Controller.afterSave' => 'afterSave',
            'Controller.afterSaveRelated' => 'afterSaveRelated',
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterDelete(Event $event)
    {
        if ((string)Hash::get((array)$event->getData(), 'type') === 'folders') {
            Cache::clearGroup('tree', self::CACHE_CONFIG);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterSave(Event $event)
    {
        $this->updateCache((array)$event->getData());
    }

    /**
     * @inheritDoc
     */
    public function afterSaveRelated(Event $event): void
    {
        $this->updateCache((array)$event->getData());
    }

    /**
     * Update cache data if folders tree has changed somehow.
     *
     * @param array $data Event data.
     * @return void
     */
    protected function updateCache(array $data): void
    {
        $type = (string)Hash::get($data, 'type');
        if ($type !== 'folders') {
            return;
        }
        $children = (string)Hash::get($data, 'data.relation') === 'children';
        $intersection = array_intersect(array_keys((array)Hash::get($data, 'data')), ['title', 'status']);
        if (empty($intersection) && !$children) {
            return;
        }
        Cache::clearGroup('tree', self::CACHE_CONFIG);
    }
}
