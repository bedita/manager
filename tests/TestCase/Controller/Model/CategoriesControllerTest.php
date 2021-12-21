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

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\CategoriesController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Model\CategoriesController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\CategoriesController
 * @uses \App\Controller\Model\CategoriesController
 */
class CategoriesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\CategoriesController
     */
    public $Categories;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'query' => [
            'filter' => ['type' => 'documents'],
        ],
        'params' => [
            'resource_type' => 'categories',
        ],
    ];

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->Categories = new CategoriesController($request);
        $this->setupApi();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Categories);

        parent::tearDown();
    }

    /**
     * Test `index` method
     *
     * @covers ::initialize()
     * @covers ::index()
     * @return void
     */
    public function testIndex(): void
    {
        $this->setupController();
        $this->Categories->index();
        $resources = $this->Categories->viewVars['resources'];
        $categoriesTree = $this->Categories->viewVars['categoriesTree'];
        $schema = $this->Categories->viewVars['schema'];
        static::assertTrue(is_array($resources));
        static::assertTrue(is_array($categoriesTree));
        static::assertTrue(is_array($schema));
    }
}
