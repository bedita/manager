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

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\ObjectTypesController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use ReflectionClass;

/**
 * {@see \App\Controller\Model\ObjectTypesController} Test Case
 */
#[CoversClass(ObjectTypesController::class)]
#[CoversMethod(ObjectTypesController::class, 'addCustomProperties')]
#[CoversMethod(ObjectTypesController::class, 'create')]
#[CoversMethod(ObjectTypesController::class, 'prepareProperties')]
#[CoversMethod(ObjectTypesController::class, 'propertiesData')]
#[CoversMethod(ObjectTypesController::class, 'save')]
#[CoversMethod(ObjectTypesController::class, 'tables')]
#[CoversMethod(ObjectTypesController::class, 'updateSchema')]
#[CoversMethod(ObjectTypesController::class, 'view')]
class ObjectTypesControllerTest extends TestCase
{
    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient|null
     */
    protected ?BEditaClient $apiClient = null;

    /**
     * Test subject
     *
     * @var \App\Controller\Model\ObjectTypesController
     */
    public ObjectTypesController $ModelController;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

    /**
     * Test default request config
     *
     * @var array
     */
    public array $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'object_types',
        ],
    ];

    /**
     * Test `save` request config
     *
     * @var array
     */
    public array $saveRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'POST',
        ],
        'post' => [
            'addedProperties' => '[{"name": "my_prop", "type": "datetime"}]',
        ],
        'params' => [
            'resource_type' => 'object_types',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->loadRoutes();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

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
        $this->ModelController = new ObjectTypesController($request);
        $this->setupApi();
    }

    /**
     * Test `create` method
     *
     * @return void
     */
    public function testCreate(): void
    {
        $this->setupController();
        $this->ModelController->create();
        $vars = ['resource', 'schema', 'properties', 'propertyTypesOptions'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `view` method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->setupController();
        $this->ModelController->view(2);
        $vars = ['resource', 'schema', 'properties', 'propertyTypesOptions', 'associationsOptions', 'objectTypeSchema'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewBuilder()->getVar($var));
        }
        $objectTypeProperties = $this->ModelController->viewBuilder()->getVar('objectTypeProperties');
        static::assertNotEmpty($objectTypeProperties);
        static::assertArrayHasKey('inherited', $objectTypeProperties);
        static::assertArrayHasKey('core', $objectTypeProperties);
        static::assertArrayHasKey('custom', $objectTypeProperties);
    }

    /**
     * Test `view` failure method
     *
     * @return void
     */
    public function testViewFail(): void
    {
        $this->setupController();
        $result = $this->ModelController->view(0);
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Test `prepareProperties` method
     *
     * @return void
     */
    public function testPrepareCustomProperties(): void
    {
        $controller = new class (new ServerRequest()) extends ObjectTypesController {
            public function prepareProperties(array $data, string $name): array
            {
                return parent::prepareProperties($data, $name);
            }
        };
        $data = [
            [
                'id' => '123',
                'name' => 'my_prop',
                'type' => 'datetime',
                'description' => 'My custom property',
                'default' => null,
                'required' => false,
                'multiple' => false,
            ],
        ];
        $expected = [
            'core' => [],
            'inherited' => [],
            'custom' => [
                [
                    'id' => '123',
                    'name' => 'my_prop',
                    'type' => 'datetime',
                    'description' => 'My custom property',
                    'default' => null,
                    'required' => false,
                    'multiple' => false,
                ],
            ],
        ];
        $actual = $controller->prepareProperties($data, 'test');
        static::assertSame($expected, $actual);
    }

    /**
     * Test `updateSchema`
     *
     * @return void
     */
    public function testUpdateSchema(): void
    {
        $this->setupController();
        $expected = $schema = ['whatever'];
        $resource = ['meta' => ['core_type' => true]];
        $reflectionClass = new ReflectionClass($this->ModelController);
        $method = $reflectionClass->getMethod('updateSchema');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->ModelController, [$schema, $resource]);
        static::assertSame($expected, $actual);

        $resource = ['meta' => ['core_type' => false]];
        $expected = [
            'whatever',
            'properties' => [
                'table' => [
                    'type' => 'string',
                    'enum' => [
                        'BEdita/Core.Folders',
                        'BEdita/Core.Links',
                        'BEdita/Core.Locations',
                        'BEdita/Core.Media',
                        'BEdita/Core.Objects',
                        'BEdita/Core.Profiles',
                        'BEdita/Core.Publications',
                        'BEdita/Core.Users',
                    ],
                ],
                'parent_name' => [
                    'type' => 'string',
                    'enum' => [
                        '',
                        'media',
                        'objects',
                    ],
                ],
            ],
        ];
        $actual = $method->invokeArgs($this->ModelController, [$schema, $resource]);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `save` method
     *
     * @return void
     */
    public function testSave(): void
    {
        $this->saveApiMock();
        $controller = new ObjectTypesController(
            new ServerRequest($this->saveRequestConfig),
        );
        $controller->save();

        $data = $controller->getRequest()->getData();
        static::assertArrayNotHasKey('addedProperties', $data);
    }

    /**
     * Test `save` method with empty data
     *
     * @return void
     */
    public function testSaveEmpty(): void
    {
        $this->saveApiMock();
        $config = $this->saveRequestConfig;
        $config['post'] = ['addedProperties' => '[]'];
        $controller = new ObjectTypesController(new ServerRequest($config));
        $controller->save();

        $data = $controller->getRequest()->getData();
        static::assertArrayNotHasKey('addedProperties', $data);
    }

    /**
     * Test `save` method with empty associations
     *
     * @return void
     */
    public function testEmptyAssocSave(): void
    {
        $this->saveApiMock();
        $config = $this->saveRequestConfig;
        $config['post'] = ['associations' => ''];
        $controller = new ObjectTypesController(new ServerRequest($config));
        $controller->save();

        $data = $controller->getRequest()->getData();
        static::assertEquals(['associations' => null], $data);
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
        $apiClient->method('post')
            ->withAnyParameters()
            ->willReturn(['data' => ['id' => '1']]);

        ApiClientProvider::setApiClient($apiClient);
    }

    /**
     * Test `tables`
     *
     * @return void
     */
    public function testTables(): void
    {
        $testTables = ['Dummy.Cats', 'Dummy.Dogs', 'Dummy.Horses'];
        Configure::write(
            'Model',
            array_merge(
                (array)Configure::read('Model'),
                [
                    'objectTypesTables' => $testTables,
                ],
            ),
        );
        $expected = array_unique(array_merge(ObjectTypesController::TABLES, $testTables));
        $expected = array_unique(array_merge($expected, ['dummy']));
        sort($expected);
        $this->setupController();
        $reflectionClass = new ReflectionClass($this->ModelController);
        $method = $reflectionClass->getMethod('tables');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->ModelController, [['attributes' => ['table' => 'dummy']]]);
        static::assertSame($expected, $actual);
    }
}
