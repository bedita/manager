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
namespace App\Test\TestCase\Controller;

use App\Controller\Model\TagsController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Model\TagsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\TagsController
 * @uses \App\Controller\Model\TagsController
 */
class TagsControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\TagsController
     */
    public $Tags;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'tags',
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
        $this->Tags = new TagsController($request);
        $this->setupApi();
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
        $this->Tags->index();
        $resources = $this->Tags->viewBuilder()->getVar('resources');
        $tagsTree = $this->Tags->viewBuilder()->getVar('tagsTree');
        $schema = $this->Tags->viewBuilder()->getVar('schema');
        static::assertTrue(is_array($resources));
        static::assertTrue(is_array($tagsTree));
        static::assertTrue(is_array($schema));
    }
}
