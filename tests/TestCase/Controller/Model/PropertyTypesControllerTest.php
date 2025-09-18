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

use App\Controller\Model\PropertyTypesController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Model\PropertyTypesController} Test Case
 */
#[CoversClass(PropertyTypesController::class)]
#[CoversMethod(PropertyTypesController::class, 'addPropertyTypes')]
#[CoversMethod(PropertyTypesController::class, 'editPropertyTypes')]
#[CoversMethod(PropertyTypesController::class, 'getResourceType')]
#[CoversMethod(PropertyTypesController::class, 'removePropertyTypes')]
#[CoversMethod(PropertyTypesController::class, 'save')]
#[CoversMethod(PropertyTypesController::class, 'setResourceType')]
class PropertyTypesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\PropertyTypesController
     */
    public PropertyTypesController $ModelController;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

    /**
     * Test request config
     *
     * @var array
     */
    public array $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'property_types',
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
        $this->setupApi();
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
    }

    /**
     * Test `save` method on method not allowed
     *
     * @return void
     */
    public function testSaveMethodNotAllowed(): void
    {
        $this->setupApi();
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->expectException(MethodNotAllowedException::class);
        $this->ModelController->save();
    }

    /**
     * Test `save` method on request error
     *
     * @return void
     */
    public function testSaveRequestError(): void
    {
        $this->setupApi();
        $config = array_merge($this->defaultRequestConfig, [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'property_types',
            ],
            'post' => [
                'removePropertyTypes' => [
                    '12345',
                ],
            ],
        ]);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->ModelController->save();
        $actual = (string)Hash::get($this->ModelController->viewBuilder()->getVars(), 'error');
        $expected = '[404] Not Found';
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `save` method on empty request
     *
     * @return void
     */
    public function testSaveEmptyRequest(): void
    {
        $exception = new BadRequestException('empty request');
        $this->expectException(get_class($exception));
        $this->expectExceptionCode($exception->getCode());
        $this->expectExceptionMessage($exception->getMessage());
        $this->setupApi();
        $config = array_merge($this->defaultRequestConfig, [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'property_types',
            ],
        ]);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->ModelController->save();
    }

    /**
     * Test `save` method
     *
     * @return void
     */
    public function testSaveAddEditRemovePropertyTypes(): void
    {
        $this->setupApi();
        $propertyName = 'mynewpropertytype';
        $config = array_merge($this->defaultRequestConfig, [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'property_types',
            ],
            'post' => [
                'addPropertyTypes' => [
                    [
                        'name' => $propertyName,
                        'params' => json_encode(['type' => 'string']),
                    ],
                ],
            ],
        ]);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->ModelController->save();
        $actual = (array)Hash::get($this->ModelController->viewBuilder()->getVars(), 'saved');
        $expected = [
            [
                'id' => $actual[0]['id'],
                'type' => 'property_types',
                'attributes' => [
                    'name' => $propertyName,
                    'params' => [
                        'type' => 'string',
                    ],
                ],
                'meta' => $actual[0]['meta'],
            ],
        ];
        static::assertEquals($expected, $actual);

        // edit property
        $config = array_merge($this->defaultRequestConfig, [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'property_types',
            ],
            'post' => [
                'editPropertyTypes' => [
                    [
                        'id' => $actual[0]['id'],
                        'attributes' => [
                            'name' => $propertyName,
                            'params' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->ModelController->save();
        $actual = (array)Hash::get($this->ModelController->viewBuilder()->getVars(), 'edited');
        $expected = [
            [
                'id' => $actual[0]['id'],
                'type' => 'property_types',
                'attributes' => [
                    'name' => $propertyName,
                    'params' => [
                        'type' => 'integer',
                    ],
                ],
                'meta' => $actual[0]['meta'],
            ],
        ];
        static::assertEquals($expected, $actual);

        // remove property
        $expected = [$actual[0]['id']];
        $config = array_merge($this->defaultRequestConfig, [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'property_types',
            ],
            'post' => [
                'removePropertyTypes' => [
                    $expected[0],
                ],
            ],
        ]);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->ModelController->save();
        $actual = (array)Hash::get($this->ModelController->viewBuilder()->getVars(), 'removed');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getResourceType` and `setResourceType`.
     *
     * @return void
     */
    public function testGetSetResourceType(): void
    {
        $this->setupController();
        $expected = 'dummies';
        $this->ModelController->setResourceType($expected);
        $actual = $this->ModelController->getResourceType();
        static::assertSame($expected, $actual);
    }
}
