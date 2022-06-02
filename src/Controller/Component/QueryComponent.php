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
        $query = (array)$this->getController()->getRequest()->getQueryParams();

        // set include, if set in config
        if ($this->getConfig('include') != null) {
            $query['include'] = (string)$this->getConfig('include');
        }

        // make sure `filter[history_editor]` is empty in order to use logged user id
        if (isset($query['filter']['history_editor'])) {
            $query['filter']['history_editor'] = '';
        }

        // return URL query string if `filter`, `sort`, or `q` are set
        $subQuery = array_intersect_key($query, array_flip(['filter', 'sort', 'q']));
        if (!empty($subQuery)) {
            return $query;
        }

        // set sort order: use `currentModule.sort` or default '-id'
        $module = (array)$this->getController()->viewBuilder()->getVar('currentModule');
        $query['sort'] = (string)Hash::get($module, 'sort', '-id');

        return $query;
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
