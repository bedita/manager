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

use Cake\Controller\Component;
use Cake\Core\Configure;

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
            'other' => [
                'body',
                'lang',
                // other properties
            ],
        ],
        // index properties list
        // don't include: 'id', 'status', and 'modified' => always listed
        'index' => [
            'title',
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        Configure::load('properties');
        $propConfig = array_merge(Configure::read('DefaultProperties'), Configure::read('Properties'));
        $this->setConfig('Properties', $propConfig);

        parent::initialize($config);
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
     * @param array $object Object data to view
     * @param string $type Object type
     * @return array
     */
    public function viewGroups(array $object, string $type): array
    {
        $properties = $used = [];
        $keep = $this->getConfig(sprintf('Properties.%s.view._keep', $type), []);
        $attributes = array_merge(array_fill_keys($keep, ''), $object['attributes']);

        foreach ($this->defaultGroups['view'] as $group => $items) {
            $key = sprintf('Properties.%s.view.%s', $type, $group);
            $list = $this->getConfig($key, $items);
            $p = [];
            foreach ($list as $item) {
                if (array_key_exists($item, $attributes)) {
                    $p[$item] = $attributes[$item];
                }
            }
            $properties[$group] = $p;
            $used = array_merge($used, $list);
        }
        // add remaining properties to 'other' group
        $properties['other'] += array_diff_key($attributes, array_flip($used));

        return $properties;
    }

    /**
     * List properties to display in `index` view
     *
     * @param string $type Object type name
     * @return array
     */
    public function indexList(string $type): array
    {
        $list = $this->getConfig(sprintf('Properties.%s.index', $type), $this->defaultGroups['index']);

        return array_diff($list, ['id', 'status', 'modified']);
    }
}
