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
namespace App\Controller\Model;

use Cake\Http\Response;

/**
 * Categories Model Controller: list, add, edit, remove categories
 *
 * @property \App\Controller\Component\CategoriesComponent $Categories
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class CategoriesController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'categories';

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

        $objectTypes = $this->Schema->objectTypesFeatures()['categorized'];
        $objectTypes = array_combine($objectTypes, $objectTypes);
        $response = $this->Categories->index(null, $this->getRequest()->getQueryParams());
        $resources = $this->Categories->map($response);
        $roots = $this->Categories->getAvailableRoots($resources);
        $categoriesTree = $this->Categories->tree($resources);
        $names = $this->Categories->names($this->objectType);

        $this->set(compact('resources', 'roots', 'categoriesTree', 'names'));
        $this->set('object_types', $objectTypes);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->indexList('categories'));
        $this->set('filter', $this->Properties->filterList('categories'));

        return null;
    }
}
