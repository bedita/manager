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
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\Model\PropertyTypesController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\PropertyTypesController
 * @uses \App\Controller\Model\PropertyTypesController
 */
class PropertyTypesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\PropertyTypesController
     */
    public $ModelController;

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
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->ModelController = new PropertyTypesController($request);
        $this->setupApi();
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider(): array
    {
        return [
            // test with empty object
            'emptyRequest' => [
                new BadRequestException('empty request'),
                [],
                '',
            ],
            'addPropertyTypesRequest' => [
                [
                    [
                        // 'id' => '13',
                        'type' => 'property_types',
                        'attributes' => [
                                'name' => 'giovanni',
                                'params' => [
                                        'type' => 'string',
                                ],
                        ],
                    ],
                ],
                [
                    'addPropertyTypes' => [
                        [
                            'name' => 'giovanni',
                            'params' => json_encode([
                                'type' => 'string',
                            ]),
                        ],
                    ],
                ],
                'saved',
            ],
            'editPropertyTypesRequest' => [
                [
                    [
                        'id' => '13',
                        'type' => 'property_types',
                        'attributes' => [
                            'name' => 'enrico',
                            'params' => [
                                'type' => 'object',
                            ],
                        ],
                    ],
                ],
                [
                    'editPropertyTypes' => [
                        [
                            'id' => '13',
                            'attributes' => [
                                'name' => 'enrico',
                                'params' => [
                                    'type' => 'object',
                                ],
                            ],
                        ],
                    ],
                ],
                'edited',
            ],
            'removePropertyTypesRequest' => [
                [ '13' ],
                [
                    'removePropertyTypes' => [
                        '13',
                    ],
                ],
                'removed',
            ],
            'request error' => [
                [
                  'error' => '[404] Not Found',
                ],
                [
                    'removePropertyTypes' => [
                        '12345',
                    ],
                ],
                'removed',
            ],

        ];
    }

    /**
     * Test `save` method
     *
     * @param array|\Exception $expectedResponse expected results from test
     * @param bool|null $data setup data for test
     * @param string $action tested action
     * @dataProvider saveProvider()
     * @covers ::save()
     * @covers ::addPropertyTypes()
     * @covers ::editPropertyTypes()
     * @covers ::removePropertyTypes()
     * @return void
     */
    public function testSave($expectedResponse, $data, $action): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
        ];

        $this->setupController($config);
        $this->ModelController->setResourceType('property_types');

        if ($expectedResponse instanceof \Exception) {
            $this->expectException(get_class($expectedResponse));
            $this->expectExceptionCode($expectedResponse->getCode());
            $this->expectExceptionMessage($expectedResponse->getMessage());
        }

        $this->ModelController->save();

        $actualResponse = (array)Hash::get($this->ModelController->viewBuilder()->getVars(), $action);

        if ($action == 'saved') {
            foreach ($actualResponse as &$element) {
                unset($element['id']);
            }
        }
        if (is_array($expectedResponse)) {
            $actualResponse = Hash::remove($actualResponse, '{n}.meta');
            if (!empty($expectedResponse['error'])) {
                $actualResponse = Hash::get($this->ModelController->viewBuilder()->getVars(), 'error');
                $expectedResponse = $expectedResponse['error'];
            }
        }

        static::assertEquals($expectedResponse, $actualResponse);
    }

    /**
     * Test `getResourceType` and `setResourceType`.
     *
     * @return void
     * @covers ::getResourceType()
     * @covers ::setResourceType()
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
