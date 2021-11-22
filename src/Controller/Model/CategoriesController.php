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

use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;

/**
 * Categories Model Controller: list, add, edit, remove categories
 *
 *
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
    public function index(): ?Response
    {
        $this->request->allowMethod(['get']);
        $query = $this->request->getQueryParams() + [
            'page_size' => 500,
        ];

        try {
            $response = $this->apiClient->get('/model/categories', $query);
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $resources = [];
        foreach ((array)$response['data'] as $category) {
            $resources[$category['id']] = $category;
        }

        $grouped = [
            '_' => [],
        ];
        foreach ($resources as $category) {
            if (empty($category['attributes']['parent_id'])) {
                $grouped['_'][] = $category['id'];
            } else {
                $grouped[$category['attributes']['parent_id']][] = $category['id'];
            }
        }

        $object_types = $this->Schema->objectTypesFeatures()['categorized'];

        $this->set(compact('resources', 'grouped', 'object_types'));
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->indexList('categories'));
        $this->set('filter', $this->Properties->filterList('categories'));

        return null;
    }
}
