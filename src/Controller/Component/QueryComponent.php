<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
use Cake\Utility\Hash;

/**
 * Handles query strings.
 */
class QueryComponent extends Component
{
    /**
     * Retrieve `index` module query string array
     *
     * @return array
     */
    public function index(): array
    {
        $query = $this->getController()->getRequest()->getQueryParams();
        if (array_key_exists('sort', $query)) {
            $sort = (string)Hash::get($query, 'sort');
            $this->handleSort($sort, $query);
        }
        $query = $this->handleInclude($query);

        // make sure `filter[history_editor]` is empty in order to use logged user id
        if (isset($query['filter']['history_editor'])) {
            $query['filter']['history_editor'] = '';
        }

        // return URL query string if `filter`, `sort`, or `q` are set
        if (!empty(array_intersect_key($query, array_flip(['filter', 'sort', 'q'])))) {
            return $query;
        }

        // set sort order: use `currentModule.sort` or default '-id'
        $module = (array)$this->getController()->viewBuilder()->getVar('currentModule');
        $sort = (string)Hash::get($module, 'sort');
        $this->handleSort($sort, $query);

        return $query;
    }

    /**
     * Handle include parameter
     *
     * @param array $query Query string
     * @return array
     */
    protected function handleInclude(array $query): array
    {
        $decoded = [];
        $include = array_filter(
            (array)Configure::read(
                sprintf(
                    'Properties.%s.index',
                    $this->getController()->getRequest()->getParam('object_type')
                )
            ),
            function ($value) {
                return is_array($value);
            }
        );
        $decoded = empty($include) ? $include : array_keys(reset($include));
        if ($this->getConfig('include') != null) {
            $decoded = array_unique(
                array_merge(
                    $decoded,
                    explode(',', (string)$this->getConfig('include'))
                )
            );
        }
        if (!empty($decoded)) {
            $query['include'] = implode(',', $decoded);
        }

        return $query;
    }

    /**
     * Handle sort order
     *
     * @param string $sort Sort order
     * @param array $query Query string
     * @return void
     */
    protected function handleSort(string $sort, array &$query): void
    {
        // remove sort from query if `q` search is set: order is done by search engine
        if (!empty($query['q'])) {
            unset($query['sort']);

            return;
        }
        // set sort order from query string or default '-id'
        $query['sort'] = !empty($sort) ? $sort : '-id';
    }

    /**
     * Prepare query string to make BE4 API call
     *
     * @param array $query Input query string
     * @return array
     */
    public function prepare(array $query): array
    {
        // cleanup `filter`, remove empty keys
        $filter = array_filter((array)Hash::get($query, 'filter'));
        $remove = array_flip(['count', 'page_items', 'page_count', 'filter']);
        $query = array_diff_key($query, $remove);
        if (!empty($filter)) {
            $query += compact('filter');
        }

        return $query;
    }
}
