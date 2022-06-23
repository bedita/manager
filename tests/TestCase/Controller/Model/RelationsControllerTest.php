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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->Relations = new RelationsController(
            new ServerRequest($this->defaultRequestConfig)
        );
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.com'])
            ->getMock();
        $response = [
            'data' => [['id' => 999, 'attributes' => ['name' => 'dummy']]],
            'meta' => [],
            'links' => [],
        ];
        $apiClient->method('get')
            ->willReturn($response);

        // set $this->Relations->apiClient
        $property = new \ReflectionProperty(RelationsController::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Relations, $apiClient);
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
     * Test `index` method
     *
     * @covers ::index()
     * @return void
     */
    public function testIndex(): void
    {
        $this->Relations->index();
        $actual = $this->Relations->viewBuilder()->getVar('resources');
        static::assertTrue(is_array($actual));
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     * @return void
     */
    public function testView(): void
    {
        $this->Relations->view(1);
        foreach (['left', 'right'] as $side) {
            $actual = $this->Relations->viewBuilder()->getVar(sprintf('%s_object_types', $side));
            static::assertTrue(is_array($actual));
        }
    }

    /**
     * Test `relatedTypes` method
     *
     * @covers ::relatedTypes()
     * @return void
     */
    public function testRelatedTypes(): void
    {
        foreach (['left', 'right'] as $side) {
            $actual = $this->Relations->relatedTypes('1', $side);
            static::assertTrue(is_array($actual));
        }
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
