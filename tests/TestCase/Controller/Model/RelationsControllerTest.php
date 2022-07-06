<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\RelationsController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Model\RelationsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\RelationsController
 * @uses \App\Controller\Model\RelationsController
 */
class RelationsControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\RelationsController
     */
    public $Relations;

    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

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
            'resource_type' => 'relations',
        ],
    ];

    /**
     * Relation test data
     *
     * @var array
     */
    public $testRelation = [
        'id' => 999,
        'type' => 'relations',
        'attributes' => [
            'name' => 'dummy',
        ],
        'relationships' => [
            'left_object_types' => [
                'data' => [
                    [
                        'id' => 1,
                        'type' => 'object_types',
                        'attributes' => [
                            'name' => 'users',
                        ],
                    ],
                ],
            ],
            'right_object_types' => [
                'data' => [
                    [
                        'id' => 2,
                        'type' => 'object_types',
                        'attributes' => [
                            'name' => 'documents',
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Relations);
        ApiClientProvider::setApiClient($this->apiClient);
        parent::tearDown();
    }

    /**
     * API client mock for index action
     *
     * @return void
     */
    protected function indexApiMock(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $response = [
            'data' => [
                $this->testRelation,
            ],
            'meta' => [],
            'links' => [],
        ];
        $apiClient->method('get')
            ->willReturn($response);

        ApiClientProvider::setApiClient($apiClient);
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     * @covers ::indexQuery()
     * @return void
     */
    public function testIndex(): void
    {
        $this->indexApiMock();
        $this->Relations = new RelationsController(new ServerRequest($this->defaultRequestConfig));
        $this->Relations->index();

        $resources = $this->Relations->viewBuilder()->getVar('resources');
        static::assertTrue(is_array($resources));
        static::assertNotEmpty($resources);
        static::assertEquals($this->testRelation['id'], $resources[0]['id']);
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     * @covers ::viewQuery()
     * @return void
     */
    public function testView(): void
    {
        $this->viewApiMock();
        $this->Relations = new RelationsController(new ServerRequest($this->defaultRequestConfig));
        $this->Relations->view(1);

        $resource = $this->Relations->viewBuilder()->getVar('resource');
        static::assertTrue(is_array($resource));
        static::assertNotEmpty($resource);
        static::assertEquals($this->testRelation['id'], $resource['id']);
    }

    /**
     * API client mock for view action
     *
     * @return void
     */
    protected function viewApiMock(): void
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $response = [
            'data' => $this->testRelation,
            'meta' => [],
            'links' => [],
        ];
        $apiClient->method('get')
            ->willReturn($response);

        ApiClientProvider::setApiClient($apiClient);
    }

    /**
     * Test `relatedTypes` method
     *
     * @covers ::relatedTypes()
     * @return void
     */
    public function testRelatedTypes(): void
    {
        $this->viewApiMock();
        $this->Relations = new RelationsController(new ServerRequest($this->defaultRequestConfig));
        $this->Relations->view(1);

        $left = $this->Relations->viewBuilder()->getVar('left_object_types');
        static::assertEquals(['users'], $left);

        $right = $this->Relations->viewBuilder()->getVar('right_object_types');
        static::assertEquals(['documents'], $right);
    }

    /**
     * Test `allTypes` method
     *
     * @covers ::allTypes()
     * @return void
     */
    public function testAllTypes(): void
    {
        $this->viewApiMock();
        $this->Relations = new RelationsController(new ServerRequest($this->defaultRequestConfig));
        $this->Relations->view(1);
        $actual = $this->Relations->viewBuilder()->getVar('all_types');
        static::assertIsArray($actual);
    }

    /**
     * Test `allTypes` method, exception case
     *
     * @covers ::allTypes()
     * @return void
     */
    public function testAllTypesException(): void
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willThrowException(new BEditaClientException('test'));
        ApiClientProvider::setApiClient($apiClient);
        $this->Relations = new RelationsController(new ServerRequest($this->defaultRequestConfig));
        $this->Relations->view(1);
        $actual = $this->Relations->viewBuilder()->getVar('all_types');
        static::assertIsArray($actual);
        $flash = $this->Relations->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('test', $flash);
    }

    /**
     * Test `save` method
     *
     * @covers ::save()
     * @covers ::updateRelatedTypes()
     * @covers ::relatedItems()
     * @return void
     */
    public function testSave(): void
    {
        $this->saveApiMock();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'change_left' => 'documents',
                'current_left' => 'documents',
                'change_right' => 'documents,events',
                'current_right' => 'events',
            ],
            'params' => [
                'resource_type' => 'relations',
            ],
        ];
        $controller = new RelationsController(
            new ServerRequest($config)
        );
        $controller->save();
        $data = $controller->getRequest()->getData();

        static::assertArrayNotHasKey('change_left', $data);
        static::assertArrayNotHasKey('change_right', $data);
        static::assertArrayNotHasKey('current_left', $data);
        static::assertArrayNotHasKey('current_right', $data);
    }

    /**
     * API client mock for save action
     *
     * @return void
     */
    protected function saveApiMock(): void
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn(['data' => ['id' => '1']]);
        $apiClient->method('post')
            ->withAnyParameters()
            ->willReturn(['data' => ['id' => '1']]);
        $apiClient->method('patch')
            ->willReturn([]);

        ApiClientProvider::setApiClient($apiClient);
    }
}
