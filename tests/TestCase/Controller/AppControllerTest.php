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

namespace App\Test\TestCase\Controller;

use App\Controller\AppController;
use BEdita\SDK\BEditaClient;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\AppController} Test Case
 *
 * @coversDefaultClass \App\Controller\AppController
 */
class AppControllerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\AppController
     */
    protected $AppController;

    /**
     * Setup controller to test with request config
     *
     * @param array $config configuration for controller setup
     * @return void
     */
    protected function setupController($config = null) : void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
        }
        $this->AppController = new AppController($request);
    }

    /**
     * Setup controller and manually login user
     *
     * @return array|null
     */
    protected function setupControllerAndLogin() : ?array
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'username' => env('BEDITA_ADMIN_USR'),
                'password' => env('BEDITA_ADMIN_PWD'),
            ],
        ];
        $this->setupController($config);

        $user = $this->AppController->Auth->identify();
        $this->AppController->Auth->setUser($user);

        return $user;
    }

    /**
     * test `initialize` function
     *
     * @covers ::initialize()
     *
     * @return void
     */
    public function testInitialize() : void
    {
        $this->setupController();

        static::assertNotEmpty($this->AppController->{'RequestHandler'});
        static::assertNotEmpty($this->AppController->{'Flash'});
        static::assertNotEmpty($this->AppController->{'Security'});
        static::assertNotEmpty($this->AppController->{'Csrf'});
        static::assertNotEmpty($this->AppController->{'Auth'});
        static::assertNotEmpty($this->AppController->{'Modules'});
        static::assertNotEmpty($this->AppController->{'Schema'});
    }

    /**
     * test `beforeFilter` not logged error
     *
     * @covers ::beforeFilter()
     *
     * @return void
     */
    public function testBeforeFilterLoginError() : void
    {
        $this->setupController();

        $event = $this->AppController->dispatchEvent('Controller.initialize');
        $this->AppController->beforeFilter($event);
        $flash = $this->AppController->request->getSession()->read('Flash');

        $expected = __('You are not logged or your session has expired, please provide login credentials');
        $message = $flash['flash'][0]['message'];

        static::assertEquals($expected, $message);
    }

    /**
     * test 'beforeFilter' for correct apiClient token setup
     *
     * @covers ::beforeFilter()
     *
     * @return void
     */
    public function testBeforeFilterCorrectTokens() : void
    {
        $expectedtokens = [];

        $this->setupControllerAndLogin();

        $expectedtokens = $this->AppController->Auth->user('tokens');

        $event = $this->AppController->dispatchEvent('Controller.initialize');
        $this->AppController->beforeFilter($event);

        $apiClient = $this->accessProperty($this->AppController, 'apiClient');
        $apiClientTokens = $this->accessProperty($apiClient, 'tokens');

        static::assertEquals($expectedtokens, $apiClientTokens);
    }

    /**
     * test `setupOutputTimezone`
     *
     * @covers ::setupOutputTimezone
     *
     * @return void
     */
    public function testSetupOutputTimezone() : void
    {
        $this->setupController();
        $expected = 'GMT';

        // mock for AuthComponent
        $mockedAuthComponent = $this->getMockBuilder('AuthComponent')
            ->setMethods(['user'])
            ->getMock();

        // moch for user method
        $mockedAuthComponent->method('user')
            ->with('timezone')
            ->willReturn($expected);

        $this->AppController->Auth = $mockedAuthComponent;

        $this->invokeMethod($this->AppController, 'setupOutputTimezone');

        $configTimezone = Configure::read('I18n.timezone');

        static::assertEquals($expected, $configTimezone);
    }

    /**
     * Test `beforeRender` method for correct user object in Controller viewVars
     *
     * @covers ::beforeRender()
     *
     * @return void
     */
    public function testBeforeRender() : void
    {
        $user = $this->setupControllerAndLogin();

        $event = $this->AppController->dispatchEvent('Controller.beforeRender');
        $this->AppController->beforeRender($event);

        static::assertArrayHasKey('user', $this->AppController->viewVars);
        static::assertEquals($user, $this->AppController->viewVars['user']);
    }

    /**
     * Test `beforeRender` method, when updating tokens in session
     *
     * @covers ::beforeRender()
     *
     * @return void
     */
    public function testBeforeRenderUpdateTokens() : void
    {
        $user = $this->setupControllerAndLogin();

        // fake updated token
        $updatedToken = [
            'jwt' => 'oldToken',
            'renew' => 'this is a new token',
        ];

        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();

            // mocked getTokens method returns fake tokens
        $apiClient->method('getTokens')
            ->willReturn($updatedToken);

        $this->AppController->apiClient = $apiClient;

        $event = $this->AppController->dispatchEvent('Controller.beforeRender');
        $this->AppController->beforeRender($event);

        // assert user objects has been updated
        static::assertArrayHasKey('user', $this->AppController->viewVars);
        static::assertEquals($updatedToken, $this->AppController->viewVars['user']['tokens']);
    }

    /**
     * Data provider for `testPrepareRequest` test case.
     *
     * @return array
     */
    public function prepareRequestProvider() : array
    {
        return [
            'documents' => [ // test _jsonKeys json_decode
                'documents', // object_type
                [ // expected
                    'title' => 'bibo',
                    'jsonKey1' => [
                        'a' => 1,
                        'b' => 2,
                        'c' => 3,
                    ],
                    'jsonKey2' => [
                        'gin' => 'vodka',
                        'fritz' => 'kola',
                    ],
                ],
                [ // data provided
                    'title' => 'bibo',
                    '_jsonKeys' => 'jsonKey1,jsonKey2',
                    'jsonKey1' => '{"a":1,"b":2,"c":3}',
                    'jsonKey2' => '{"gin":"vodka","fritz":"kola"}',
                ]
            ],
            'users' => [ // test: removing password from data
                'users', // object_type
                [ 'name' => 'giova' ], // expected
                [ // data provided
                    'name' => 'giova',
                    'password' => '',
                    'confirm-password' => '',
                ]
            ],
            'relations' => [
                'documents', // object_type
                [
                    'id' => '1',
                    'relations' => [
                        'attach' => [
                            'addRelated' => '[{ "id": "44", "type": "images"}]'
                        ]
                    ],
                    'api' => [ // expected new property in data
                        [
                            'method' => 'addRelated',
                            'id' => '1',
                            'relation' => 'attach',
                            'relatedIds' => [
                                [
                                    'id' => '44',
                                    'type' => 'images',
                                ]
                            ]
                        ]
                    ]
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'relations' => [
                        'attach' => [
                            'addRelated' => '[{ "id": "44", "type": "images"}]' // fake attached image
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Test `prepareRequest` method
     *
     * @param string $objectType The object type
     * @param array $expected The expected request data
     * @param array $data The payload data
     *
     * @covers ::prepareRequest()
     * @dataProvider prepareRequestProvider()
     *
     * @return void
     */
    public function testPrepareRequest($objectType, $expected, $data) : void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
            'params' => [
                'object_type' => 'documents',
            ],
        ];

        $this->setupController($config);

        $actual = $this->invokeMethod($this->AppController, 'prepareRequest', [$objectType]);

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testCheckRequest` test case.
     *
     * @return array
     */
    public function checkRequestProvider() : array
    {
        return [
            'badRequest' => [
                new BadRequestException('Empty request'),
                [],
                null,
            ],
            'methodNotAllowed' => [
                new MethodNotAllowedException(),
                ['allowedMethods' => 'POST'],
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ],
            ],
            'requiredParametersEmpty' => [
                new BadRequestException('Empty password'),
                ['requiredParameters' => ['password']],
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ],
            ],
            'requiredParametersSet' => [
                ['password' => 'bibo'],
                ['requiredParameters' => ['password']],
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => [
                        'password' => 'bibo',
                    ]
                ],
            ]
        ];
    }

    /**
     * Test `checkRequest` method
     *
     * @covers ::checkRequest()
     * @dataProvider checkRequestProvider()
     *
     * @return void
     */
    public function testCheckRequest($expected, $params, $config) : void
    {
        $this->setupController($config);

        if ($config == null) {
            $this->AppController->request = $config;
        }

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $actual = $this->invokeMethod($this->AppController, 'checkRequest', [$params]);

        static::assertEquals($expected, $actual);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Access protected/private property of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $propertyName Property name to access
     *
     * @return mixed Method return.
     */
    public function accessProperty(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
