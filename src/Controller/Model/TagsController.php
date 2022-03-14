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

use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Tags Model Controller: list, add, edit, remove tags
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 */
class TagsController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'tags';

    /**
     * Single resource view exists
     *
     * @var bool
     */
    protected $singleView = false;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Categories');
    }

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $options = array_merge((array)$this->getRequest()->getQueryParams(), ['page_size' => 100]);
        $response = ApiClientProvider::getApiClient()->get('/model/tags', $options);
        $resources = (array)Hash::combine((array)Hash::get($response, 'data'), '{n}.id', '{n}');
        $roots = $this->Categories->getAvailableRoots($resources);
        $tagsTree = $this->Categories->tree($resources);
        $this->set(compact('resources', 'roots', 'tagsTree'));
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->indexList('tags'));
        $this->set('filter', $this->Properties->filterList('tags'));

        return null;
    }
}
