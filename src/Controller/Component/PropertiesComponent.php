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
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Component to handle properties view in modules.
 */
class PropertiesComponent extends Component
{
    /**
     * Default properties groups
     *
     * @var array
     */
    protected $defaultGroups = [
        'view' => [
            // always open on the top
            'core' => [
                'title',
                'description',
            ],
            // publishing related
            'publish' => [
                'uname',
                'status',
                'publish_start',
                'publish_end',
            ],
            // power users
            'advanced' => [
                'extra',
            ],
            // remaining attributes
            // items listed here only used for ordering, not listed if present in other groups
            'other' => [
                'body',
                'lang',
                // remaining attributes
            ],
        ],
        // index properties list
        // don't include: 'id', 'status', and 'modified' => always listed
        'index' => [
            'title',
        ],

        'filter' => [
            'status',
        ],
        'bulk' => [
            'status',
        ],
    ];

    /**
     * Properties to exclude from groups view
     *
     * @var array
     */
    protected $excluded = [
        'categories',
        'date_ranges',
        'tags',
        'lang',
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->init();

        parent::initialize($config);
    }

    /**
     * Init properties
     *
     * @return void
     */
    protected function init(): void
    {
        $cacheKey = CacheTools::cacheKey('properties');
        $properties = Cache::read($cacheKey, 'default');
        if (!empty($properties)) {
            $this->setConfig('Properties', $properties);

            return;
        }

        Configure::load('properties');
        $properties = (array)Configure::read('Properties');
        $defaultProperties = (array)Configure::read('DefaultProperties');
        $keys = array_unique(
            array_merge(
                array_keys($properties),
                array_keys($defaultProperties)
            )
        );
        sort($keys);
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = array_merge(
                (array)Hash::get($defaultProperties, $key),
                (array)Hash::get($properties, $key)
            );
        }
        $this->setConfig('Properties', $config);
        Cache::write($cacheKey, $config);
    }

    /**
     * Setup object attributes into groups from configuration for the view.
     * An array with a key for each group will be created.
     *
     * Displayed view groups are
     *      + 'core' always open on the top
     *      + 'publish' publishing related
     *      + 'advanced' for power users (JSON mainly)
     *      + 'other' remaining attributes
     *
     * Customization available via `Properties.{type}.view` configuration where properties
     * names can be grouped in `core`, `publish`, `advanced` and `other` as well
     *
     * Properties not present in $object will not be set in any group unless they're listed
     * under `_keep` in the above configuration.
     *
     * Properties in internal `$excluded` array will be removed from groups.
     *
     * @param array  $object Object data to view
     * @param string $type   Object type
     *
     * @return array
     */
    public function viewGroups(array $object, string $type): array
    {
        $properties = $used = [];
        $keep = $this->getConfig(sprintf('Properties.%s.view._keep', $type), []);
        $attributes = array_merge(array_fill_keys($keep, ''), (array)Hash::get($object, 'attributes'));
        $attributes = array_diff_key($attributes, array_flip($this->excluded));
        $defaults = array_merge($this->getConfig(sprintf('Properties.%s.view', $type), []), $this->defaultGroups['view']);
        unset($defaults['_keep']);

        foreach ($defaults as $group => $items) {
            $key = sprintf('Properties.%s.view.%s', $type, $group);
            $list = $this->getConfig($key, $items);
            $p = [];
            foreach ($list as $item) {
                if (array_key_exists($item, $attributes)) {
                    $p[$item] = $attributes[$item];
                }
            }
            $properties[$group] = $p;
            if ($group !== 'other') {
                $used = array_merge($used, $list);
            }
        }
        // add remaining properties to 'other' group
        $properties['other'] = array_diff_key($properties['other'], array_flip($used));
        $properties['other'] += array_diff_key($attributes, array_flip($used));

        return $properties;
    }

    /**
     * List properties to display in `index` view
     *
     * @param string $type Object type name
     *
     * @return array
     */
    public function indexList(string $type): array
    {
        $list = $this->getConfig(sprintf('Properties.%s.index', $type), $this->defaultGroups['index']);

        return array_diff($list, ['id', 'status', 'modified']);
    }

    /**
     * List of filter to display in `filter` view
     *
     * @param string $type Object type name
     *
     * @return array
     */
    public function filterList(string $type): array
    {
        return $this->getConfig(sprintf('Properties.%s.filter', $type), $this->defaultGroups['filter']);
    }

    /**
     * List of all filters, grouped by type, for passed `$types` list
     *
     * @param string[] $types List of types to get filters of
     *
     * @return array
     */
    public function filtersByType(array $types): array
    {
        if (empty($types)) {
            return [];
        }

        return array_filter(
            array_reduce(
                $types,
                function (array $accumulator, string $type) {
                    $accumulator[$type] = $this->filterList($type);

                    return $accumulator;
                },
                []
            )
        );
    }

    /**
     * List of bulk actions to display in `index` view
     *
     * @param string $type Object type name
     *
     * @return array
     */
    public function bulkList(string $type): array
    {
        return $this->getConfig(sprintf('Properties.%s.bulk', $type), $this->defaultGroups['bulk']);
    }

    /**
     * List of ordered relations to display.
     * Relations not included will be displayed after these.
     *
     * @param string $type Object type name
     *
     * @return array
     */
    public function relationsList(string $type): array
    {
        return $this->getConfig(sprintf('Properties.%s.relations', $type), []);
    }
}
