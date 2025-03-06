<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Model;

use App\Utility\CacheTools;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Tags Model Controller: list, add, edit, remove tags
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 * @property \App\Controller\Component\ProjectConfigurationComponent $ProjectConfiguration
 */
class TagsController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected string $resourceType = 'tags';

    /**
     * Single resource view exists
     *
     * @var bool
     */
    protected bool $singleView = false;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('ProjectConfiguration');
    }

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $params = $this->getRequest()->getQueryParams();
        $options = $params;
        $options['page_size'] = empty($options['page_size']) ? 20 : $options['page_size'];
        $options['sort'] = empty($options['sort']) ? 'name' : $options['sort'];
        $response = ApiClientProvider::getApiClient()->get('/model/tags', $options);
        $resources = Hash::combine((array)Hash::get($response, 'data'), '{n}.id', '{n}');
        if (empty($params['q']) && empty($params['filter'])) {
            CacheTools::setModuleCount((array)$response, 'tags');
        }
        $roots = $this->Categories->getAvailableRoots($resources);
        $tagsTree = $this->Categories->tree($resources);
        $this->set(compact('resources', 'roots', 'tagsTree'));
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->indexList('tags'));
        $this->set('filter', $this->Properties->filterList('tags'));
        $this->ProjectConfiguration->read();

        return null;
    }
}
