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
namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\View\Helper;

class LinkHelper extends Helper
{
    /**
     * API base URL
     *
     * @var string
     */
    protected $apiBaseUrl = null;

    /**
     * WebApp base URL
     *
     * @var string
     */
    protected $webBaseUrl = null;

    /**
     * {@inheritDoc}
     *
     * Init API and WebAPP base URL
     */
    public function initialize(array $config)
    {
        $this->apiBaseUrl = Configure::read('API.apiBaseUrl');
        $this->webBaseUrl = Router::fullBaseUrl();
    }

    /**
     * Transform API url in web app URL, preserving path part.
     * Extremely simple for now
     *
     * @param string $apiUrl Api url
     * @return void
     */
    public function fromAPI($apiUrl)
    {
        echo str_replace($this->apiBaseUrl, $this->webBaseUrl, $apiUrl);
    }

    /**
     * Sort by field direction and link
     *
     * @param string $field the Field.
     * @return void
     */
    public function sort($field)
    {
        $class = '';
        $query = $this->request->query;
        $sortValue = $field; // <= ascendant order
        if (!empty($query) && in_array('sort', array_keys($query))) {
            if ($query['sort'] === $field) { // it was ascendant sort
                $class = 'sort down';
                $sortValue = '-' . $field; // <= descendant order
            } elseif ($query['sort'] === ('-' . $field)) { // it was descendant sort
                $class = 'sort up';
            }
        }
        $url = $this->replaceParamUrl('sort', $sortValue);
        echo '<a href="' . $url . '" class="' . $class . '">' . __($field) . '</a>';
    }

    /**
     * Pagination link for page
     *
     * @param int $page destination page.
     * @return void
     */
    public function page($page)
    {
        echo $this->replaceParamUrl('page', $page);
    }

    /**
     * Pagination link for page size
     *
     * @param int $pageSize new page size.
     * @return void
     */
    public function pageSize($pageSize)
    {
        echo $this->replaceParamUrl('page_size', $pageSize);
    }

    /**
     * Return url for current page
     *
     * @param array $options options for query
     * @return string url
     */
    public function here($options = [])
    {
        $query = $this->request->query;
        if (empty($query)) {
            return $this->webBaseUrl . $this->request->here;
        }

        if ($options['exclude']) {
            unset($query[$options['exclude']]);
        }

        return $this->webBaseUrl . $this->request->here . '?' . http_build_query($query);
    }

    /**
     * Utility to get query param by name
     *
     * @param string|null $name the query parameter.
     * @return string|null
     */
    public function query($name = null)
    {
        return $this->request->getQuery($name);
    }

    /**
     * Replace parameter on url.
     *
     * @param string $parameter parameter name.
     * @param string|int $value the Value to set for parameter.
     * @return string url
     */
    private function replaceParamUrl($parameter, $value)
    {
        $query = $this->request->query;
        $query[$parameter] = $value;

        return $this->webBaseUrl . $this->request->here . '?' . http_build_query($query);
    }
}
