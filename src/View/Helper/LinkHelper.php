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
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * Helper class to generate links or link tags
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class LinkHelper extends Helper
{

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];

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
    public function fromAPI($apiUrl) : void
    {
        echo str_replace($this->apiBaseUrl, $this->webBaseUrl, $apiUrl);
    }

    /**
     * Sort by field direction and link
     *
     * @param string $field the Field.
     * @return void
     */
    public function sort($field) : void
    {
        $class = '';
        $query = $this->getView()->getRequest()->getQuery();
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
    public function page($page) : void
    {
        echo $this->replaceParamUrl('page', $page);
    }

    /**
     * Pagination link for page size
     *
     * @param int $pageSize new page size.
     * @return void
     */
    public function pageSize($pageSize) : void
    {
        echo $this->replaceParamUrl('page_size', $pageSize);
    }

    /**
     * Return url for current page
     *
     * @param array $options options for query
     * @return string url
     */
    public function here($options = []) : string
    {
        $query = $this->getView()->getRequest()->getQuery();
        if (empty($query) || !empty($options['no-query'])) {
            return $this->webBaseUrl . $this->getView()->getRequest()->getAttribute('here');
        }

        if (isset($options['exclude'])) {
            unset($query[$options['exclude']]);
        }

        return $this->webBaseUrl . $this->getView()->getRequest()->getAttribute('here') . '?' . http_build_query($query);
    }

    /**
     * Utility to get query param by name
     *
     * @param string|null $name the query parameter.
     * @return string|null
     */
    public function query($name = null) : ?string
    {
        return $this->getView()->getRequest()->getQuery($name);
    }

    /**
     * Replace parameter on url.
     *
     * @param string $parameter parameter name.
     * @param string|int $value the Value to set for parameter.
     * @return string url
     */
    private function replaceParamUrl($parameter, $value) : string
    {
        $query = $this->getView()->getRequest()->getQuery();
        $query[$parameter] = $value;
        $here = $this->getView()->getRequest()->getAttribute('here');

        return $this->webBaseUrl . $here . '?' . http_build_query($query);
    }

    /**
     * Include plugin JS bundles.
     *
     * Plugin bundle MUST be in `plugins/MyPlugin/webroot/js/MyPlugin.plugin.js`
     *
     * @return void
     */
    public function pluginsJsBundle() : void
    {
        $plugins = Configure::read('Plugins', []);
        foreach ($plugins as $plugin => $v) {
            $path = Plugin::path($plugin) . sprintf('webroot%sjs%s%s.plugin.js', DS, DS, $plugin);
            if (file_exists($path)) {
                echo $this->Html->script(sprintf('%s.%s.plugin.js', $plugin, $plugin));
            }
        }
    }

    /**
     * Include statically imported JS splitted vendors.
     *
     * @param array $filter list of file name filters
     * @return void
     */
    public function jsBundle(array $filter = []) : void
    {
        $jsFiles = $this->findFiles($filter, 'js');
        foreach ($jsFiles as $jsFile) {
            echo $this->Html->script(sprintf('%s', $jsFile));
        }
    }

    /**
     * Include statically imported CSS splitted vendors.
     *
     * @param array $filter list of file name filters
     * @return void
     */
    public function cssBundle(array $filter = []) : void
    {
        $cssFiles = $this->findFiles($filter, 'css');
        foreach ($cssFiles as $cssFile) {
            echo $this->Html->css(sprintf('%s', $cssFile));
        }
    }

    /**
     * find files under webroot directory specifing a ordered list of filters and the file type
     * to search for
     *
     * @param array $filter list of file name filters
     * @param string $type file type (js/css)

     * @return array files found
     */
    protected function findFiles(array $filter, string $type) : array
    {
        $files = [];
        $filesPath = WWW_ROOT . $type;
        if (!file_exists($filesPath)) {
            return [];
        }

        $dir = new Folder($filesPath);

        $filesFound = $dir->find('.*\.' . $type);
        if (!empty($filter)) {
            foreach ($filter as $filterName) {
                foreach ($filesFound as $fileName) {
                    if (strpos($fileName, $filterName) !== false) {
                        $files[] = sprintf('%s', $fileName);
                    }
                }
            }
        } else {
            foreach ($filesFound as $fileName) {
                $files[] = sprintf('%s', $fileName);
            }
        }

        return $files;
    }
}
