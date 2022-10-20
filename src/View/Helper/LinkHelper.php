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
use Cake\Utility\Hash;
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
     * {@inheritDoc}
     *
     * Default configuration
     *
     *  - 'apiBaseUrl': API base URL
     *  - 'webBaseUrl': WebApp base URL
     *  - 'query': Request Query params
     */
    protected $_defaultConfig = [
        'apiBaseUrl' => '',
        'webBaseUrl' => '',
        'query' => [],
        'manifest' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * Init API and WebAPP base URL
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        $default = [
            'apiBaseUrl' => Configure::read('API.apiBaseUrl'),
            'webBaseUrl' => Router::fullBaseUrl(),
            'query' => $this->getView()->getRequest()->getQueryParams(),
            'manifest' => json_decode(file_get_contents(WWW_ROOT . 'manifest.json'), true),
        ];
        $this->setConfig(array_merge($default, $config));
    }

    /**
     * Return base url.
     * If it ends with ':80' (proxy case), remove it.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        $url = (string)$this->getConfig('webBaseUrl');
        if (substr_compare($url, ':80', -strlen(':80')) === 0) {
            return substr($url, 0, strpos($url, ':80'));
        }

        return $url;
    }

    /**
     * Transform API url in web app URL, preserving path part.
     * Extremely simple for now
     *
     * @param string $apiUrl Api url
     * @return void
     */
    public function fromAPI($apiUrl): void
    {
        echo str_replace($this->getConfig('apiBaseUrl'), $this->getConfig('webBaseUrl'), $apiUrl);
    }

    /**
     * Returns sort url by field direction
     *
     * @param string $field the Field.
     * @param bool $resetPage flag to reset pagination.
     * @return string
     */
    public function sortUrl($field, $resetPage = true): string
    {
        $sort = (string)Hash::get($this->getConfig('query'), 'sort');
        $sort = $this->sortValue($field, $sort);
        $replace = compact('sort');
        $currentPage = Hash::get($this->getConfig('query'), 'page');
        if (isset($currentPage) && $resetPage) {
            $replace['page'] = 1;
        }

        return $this->replaceQueryParams($replace);
    }

    /**
     * Define sort query string value using sort string and current
     * 'sort' value
     *
     * @param string $field Field to sort.
     * @param string $currentSort Current sort value
     * @return string
     */
    protected function sortValue(string $field, string $currentSort): string
    {
        $sort = $this->sortField($field); // <= ascendant order
        if ($currentSort === $sort) { // it was ascendant sort
            $sort = '-' . $sort; // <= descendant order
        }

        return $sort;
    }

    /**
     * Retrieve 'sort' field from field name.
     *
     * @param string $field Field name.
     * @return string
     */
    protected function sortField(string $field): string
    {
        if ($field === 'date_ranges') {
            return 'date_ranges_min_start_date';
        }

        return $field;
    }

    /**
     * Returns sort class by field direction
     *
     * @param string $field the Field.
     * @return string
     */
    public function sortClass(string $field): string
    {
        $sort = (string)Hash::get($this->getConfig('query'), 'sort');
        if (empty($sort)) {
            return '';
        }
        $sortField = $this->sortField($field);
        if ($sort === $sortField) { // it was ascendant sort
            return 'sort down';
        }
        if ($sort === '-' . $sortField) { // it was descendant sort
            return 'sort up';
        }

        return '';
    }

    /**
     * Pagination link for page
     *
     * @param int $page destination page.
     * @return void
     */
    public function page($page): void
    {
        echo $this->replaceQueryParams(['page' => $page]);
    }

    /**
     * Pagination link for page size
     *
     * @param int $pageSize new page size.
     * @return void
     */
    public function pageSize($pageSize): void
    {
        echo $this->replaceQueryParams(['page_size' => $pageSize]);
    }

    /**
     * Return url for current page
     *
     * @param array $options options for query
     * @return string url
     */
    public function here($options = []): string
    {
        $url = (string)$this->getConfig('webBaseUrl');
        $here = sprintf(
            '%s%s',
            $url,
            $this->getView()->getRequest()->getAttribute('here')
        );
        $query = (array)$this->getConfig('query');
        if (empty($query) || !empty($options['no-query'])) {
            return $here;
        }
        if (isset($options['exclude'])) {
            $key = sprintf('query.%s', $options['exclude']);
            $this->setConfig($key, null);
            $query = (array)$this->getConfig('query');
        }
        $q = http_build_query($query);
        if (!empty($q)) {
            return $here . '?' . $q;
        }

        return $here;
    }

    /**
     * Replace query parameters on current request.
     *
     * @param array $queryParams list of query params to replace.
     * @return string new uri
     */
    private function replaceQueryParams(array $queryParams): string
    {
        $request = $this->getView()->getRequest();
        $query = array_merge((array)$this->getConfig('query'), $queryParams);

        return (string)$request->getUri()->withQuery(http_build_query($query));
    }

    /**
     * Include plugin js and css bundles.
     *
     * @return void
     */
    public function pluginsBundle(): void
    {
        $plugins = Configure::read('Plugins', []);
        foreach (['js', 'css'] as $extension) {
            foreach ($plugins as $plugin => $v) {
                echo $this->pluginAsset($plugin, $extension);
            }
        }
    }

    /**
     * Return js/css plugin asset by plugin and extension
     *
     * @param string $plugin The plugin
     * @param string $extension the extension
     * @return string
     */
    public function pluginAsset(string $plugin, string $extension): string
    {
        $path = sprintf('%swebroot%s%s%s%s.plugin.%s', Plugin::path($plugin), DS, $extension, DS, $plugin, $extension);
        if (!file_exists($path)) {
            return '';
        }
        $method = $extension === 'js' ? 'script' : 'css';

        return $this->Html->{$method}(sprintf('%s.%s.plugin.%s', $plugin, $plugin, $extension));
    }

    /**
     * Include statically imported JS splitted vendors.
     *
     * @param array $filter list of file name filters
     * @return void
     */
    public function jsBundle(array $filter = []): void
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
    public function cssBundle(array $filter = []): void
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
    public function findFiles(array $filter, string $type): array
    {
        $ext = '.' . $type;
        $len = strlen($ext);
        $prefixLen = strlen(sprintf("/%s/", $type));
        $files = [];
        $manifest = (array)$this->getConfig('manifest');
        foreach ($manifest as $k => $v) {
            if (substr_compare($k, $ext, -$len) !== 0) {
                continue;
            }
            foreach ($filter as $filterName) {
                if (strpos($k, $filterName) !== false) {
                    $files[] = substr($v, $prefixLen);
                }
            }
        }

        return $files;
    }

    /**
     * Build object nav
     *
     * @param array $data Object nav data
     * @return string
     */
    public function objectNav($data): string
    {
        $prev = '‹';
        $next = '›';
        $objectType = Hash::get($data, 'object_type', null);
        if (!empty($data['prev'])) {
            $prev = $this->Html->link(
                $prev,
                [
                    '_name' => 'modules:view',
                    'object_type' => $objectType,
                    'id' => $data['prev'],
                ]
            );
        }
        if (!empty($data['next'])) {
            $next = $this->Html->link(
                $next,
                [
                    '_name' => 'modules:view',
                    'object_type' => $objectType,
                    'id' => $data['next'],
                ]
            );
        }
        $index = Hash::get($data, 'index', 0);
        $total = Hash::get($data, 'total', 0);
        $counts = sprintf('<div>%d / %d</div>', $index, $total);

        return sprintf('<div class="listobjnav">%s%s%s</div>', $prev, $next, $counts);
    }
}
