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
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\AppController} Test Case
 *
 * @coversDefaultClass \App\Controller\AppController
 * @uses \App\Controller\AppController
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
    protected function setupController($config = null): void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
            if (!empty($config['?'])) {
                $request = $request->withQueryParams($config['?']);
            }
        }
        $this->AppController = new AppController($request);
    }

    /**
     * Setup controller and manually login user
     *
     * @return array|null
     */
    protected function setupControllerAndLogin(): ?array
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
    public function testInitialize(): void
    {
        $this->setupController();

        static::assertNotEmpty($this->AppController->{'RequestHandler'});
        static::assertNotEmpty($this->AppController->{'Flash'});
        static::assertNotEmpty($this->AppController->{'Security'});
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
    public function testBeforeFilterLoginError(): void
    {
        $this->setupController();

        $event = $this->AppController->dispatchEvent('Controller.initialize');

        $flash = $this->AppController->request->getSession()->read('Flash');

        $expected = __('Login required');
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
    public function testBeforeFilterCorrectTokens(): void
    {
        $expectedtokens = [];

        $this->setupControllerAndLogin();

        $expectedtokens = $this->AppController->Auth->user('tokens');

        $event = $this->AppController->dispatchEvent('Controller.initialize');

        $apiClient = $this->accessProperty($this->AppController, 'apiClient');
        $apiClientTokens = $this->accessProperty($apiClient, 'tokens');

        static::assertEquals($expectedtokens, $apiClientTokens);
    }

    /**
     * Data provider for `testLoginRedirectRoute` test case.
     *
     * @return array
     */
    public function loginRedirectRouteProvider(): array
    {
        return [
            'request is not a get' => [
                ['environment' => ['REQUEST_METHOD' => 'POST']], // config
                ['_name' => 'login'], // expected
            ],
            'request app webroot' => [
                ['environment' => ['REQUEST_METHOD' => 'GET'], 'webroot' => '/'], // config
                ['_name' => 'login'], // expected
            ],
            'redirect to /' => [
                ['environment' => ['REQUEST_METHOD' => 'GET'], 'params' => ['object_type' => 'documents']], // config
                ['_name' => 'login', 'redirect' => '/'], // expected
            ],
        ];
    }

    /**
     * test 'loginRedirectRoute'.
     *
     * @covers ::loginRedirectRoute()
     * @dataProvider loginRedirectRouteProvider()
     *
     * @return void
     */
    public function testLoginRedirectRoute($config, $expected): void
    {
        $this->setupController($config);
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('loginRedirectRoute');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->AppController, []);
        static::assertEquals($expected, $actual);
    }

    /**
     * test `setupOutputTimezone`
     *
     * @covers ::setupOutputTimezone
     *
     * @return void
     */
    public function testSetupOutputTimezone(): void
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
    public function testBeforeRender(): void
    {
        $user = $this->setupControllerAndLogin();

        $event = $this->AppController->dispatchEvent('Controller.beforeRender');

        static::assertArrayHasKey('user', $this->AppController->viewVars);
        static::assertArrayHasKey('tokens', $this->AppController->viewVars['user']);
        static::assertArrayHasKey('jwt', $this->AppController->viewVars['user']['tokens']);
        static::assertArrayHasKey('renew', $this->AppController->viewVars['user']['tokens']);
    }

    /**
     * Test `beforeRender` method, when updating tokens in session
     *
     * @covers ::beforeRender()
     *
     * @return void
     */
    public function testBeforeRenderUpdateTokens(): void
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

        // assert user objects has been updated
        static::assertArrayHasKey('user', $this->AppController->viewVars);
        static::assertEquals($updatedToken, $this->AppController->viewVars['user']['tokens']);
    }

    /**
     * Data provider for `testPrepareRequest` test case.
     *
     * @return array
     */
    public function prepareRequestProvider(): array
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
                ],
            ],
            'actual attrs' => [ // test '_actualAttributes'
                'documents', // object_type
                [ // expected
                    'description' => 'dido',
                ],
                [ // data provided
                    'title' => 'bibo',
                    'description' => 'dido',
                    '_actualAttributes' => '{"title":"bibo","description":""}',
                ],
            ],
            'wrong json string actual attrs' => [ // test '_actualAttributes'
                'documents', // object_type
                [ // expected
                    'title' => 'bibo',
                    'description' => 'dido',
                ],
                [ // data provided
                    'title' => 'bibo',
                    'description' => 'dido',
                    '_actualAttributes' => '{"title":"bibo","description":""}abcde',
                ],
            ],
            'fields null value' => [ // fields with value null, not changed and changed
                'documents', // object_type
                [ // expected
                    'id' => 23,
                    'title' => null, // null, changed
                    // 'description' => null, not changed
                ],
                [ // data provided
                    'id' => 23,
                    'title' => null,
                    'description' => null,
                    '_actualAttributes' => '{"title":"bibo","description":null}',
                ],
            ],
            'fields null new' => [ // fields with value null on new resources
                'documents', // object_type
                [ // expected
                    'description' => 'some text',
                ],
                [ // data provided
                    'title' => null,
                    'description' => 'some text',
                ],
            ],
            'users' => [ // test: removing password from data
                'users', // object_type
                [ 'name' => 'giova' ], // expected
                [ // data provided
                    'name' => 'giova',
                    'password' => '',
                    'confirm-password' => '',
                ],
            ],
            'supporters' => [
                'supporters',
                [
                    'id' => '9',
                    'username' => 'gustavo',
                ],
                [
                    'id' => '9',
                    'username' => 'gustavo',
                    'password' => '',
                ],
            ],
            'date ranges' => [ // test date_ranges array
                'events', // object_type
                [ // expected
                    'id' => '7',
                    'date_ranges' => [],
                ],
                [ // data provided
                    'id' => '7',
                    'date_ranges' => [
                        [
                            'start_date' => '',
                            'end_date' => '',
                        ],
                    ],
                ],
            ],
            'relations' => [
                'documents', // object_type
                [
                    'id' => '1',
                    '_api' => [ // expected new property in data
                        [
                            'method' => 'addRelated',
                            'id' => '1',
                            'relation' => 'attach',
                            'relatedIds' => [
                                [
                                    'id' => '44',
                                    'type' => 'images',
                                ],
                            ],
                        ],
                        [
                            'method' => 'replaceRelated',
                            'id' => '1',
                            'relation' => 'children',
                            'relatedIds' => [
                                [
                                    'id' => '44',
                                    'type' => 'images',
                                ],
                            ],
                        ],
                    ],
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'relations' => [
                        'attach' => [
                            'addRelated' => '[{ "id": "44", "type": "images"}]', // fake attached image
                        ],
                        'children' => [
                            'replaceRelated' => [
                                '{ "id": "44", "type": "images"}',
                            ],
                        ],
                    ],
                ],
            ],
            'remove parent' => [
                'documents', // object_type
                [
                    'id' => '1',
                    '_api' => [ // expected new property in data
                        [
                            'method' => 'replaceRelated',
                            'id' => '1',
                            'relation' => 'parents',
                            'relatedIds' => [],
                        ],
                    ],
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'relations' => [],
                    '_changedParents' => true,
                ],
            ],

            'no parents action' => [
                'folders', // object_type
                [
                    'id' => '2',
                ],
                [ // data provided
                    'id' => '2',
                    'relations' => [
                        'parent' => [
                            'replaceRelated' => [
                                '{ "id": "1", "type": "folders"}',
                            ],
                        ],
                    ],
                ],
            ],
            'replace parents' => [
                'documents', // object_type
                [
                    'id' => '2',
                    '_api' => [ // expected new property in data
                        [
                            'method' => 'replaceRelated',
                            'id' => '2',
                            'relation' => 'parents',
                            'relatedIds' => [
                                [
                                    'id' => '1',
                                    'type' => 'folders',
                                ],
                            ],
                        ],
                    ],
                ],
                [ // data provided
                    'id' => '2',
                    'relations' => [
                        'parents' => [
                            'replaceRelated' => [
                                '{ "id": "1", "type": "folders"}',
                            ],
                        ],
                    ],
                    '_changedParents' => true,
                ],
            ],
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
     * @covers ::specialAttributes()
     * @covers ::prepareRelations()
     * @covers ::setupParentsRelation()
     * @covers ::changedAttributes()
     * @dataProvider prepareRequestProvider()
     *
     * @return void
     */
    public function testPrepareRequest($objectType, $expected, $data): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
            'params' => [
                'object_type' => $objectType,
            ],
        ];

        $this->setupController($config);

        $actual = $this->invokeMethod($this->AppController, 'prepareRequest', [$objectType]);

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `hasFieldChanged` test case.
     *
     * @return array
     */
    public function hasFieldChangedProvider(): array
    {
        $d1 = new \DateTime('2019-01-01T15:03:01.012345Z');
        $d2 = new \DateTime('2019-01-01T16:03:01.012345Z');

        return [
            'null and empty | unchanged' => [ null, '', false ],
            'empty and null | unchanged' => [ '', null, false ],
            'bool and int | changed' => [ true, 0, true ],
            'bool and int | unchanged' => [ true, 1, false ],
            'bool and string | changed' => [ true, '0', true ],
            'bool and string | unchanged' => [ true, '1', false ],
            'string and string | changed' => [ 'one', 'two', true ],
            'string and string | unchanged' => [ 'three', 'three', false ],
            'int and string | changed' => [ 1, '2', true ],
            'int and string | unchanged' => [ 1, '1', false ],
            'string and int | changed' => [ '1', 2, true ],
            'string and int | unchanged' => [ '1', 1, false ],
            'object and object | changed' => [ ['four'], ['five'], true ],
            'object and object | unchanged' => [ ['six'], ['six'], false ],
            'date and date | changed' => [ $d1, $d2, true ],
            'date and date | unchanged' => [ $d1, $d1, false ],
        ];
    }

    /**
     * Test `hasFieldChanged` method
     *
     * @param mixed $val1 The first value
     * @param mixed $val2 The second value
     * @param bool $expected The expected result from function hasFieldChanged
     *
     * @covers ::hasFieldChanged()
     * @dataProvider hasFieldChangedProvider()
     *
     * @return void
     */
    public function testHasFieldChanged($val1, $val2, $expected): void
    {
        $this->setupController();
        $actual = $this->invokeMethod($this->AppController, 'hasFieldChanged', [$val1, $val2]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testCheckRequest` test case.
     *
     * @return array
     */
    public function checkRequestProvider(): array
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
                    ],
                ],
            ],
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
    public function testCheckRequest($expected, $params, $config): void
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

    /**
     * Data provider for `applySessionFilter` test case.
     *
     * @return array
     */
    public function applySessionFilterProvider(): array
    {
        return [
            'reset' => [ // expected remove of session filter and redirect
                [ // request config
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    '?' => ['reset' => '1'],
                    'params' => [
                        'object_type' => 'documents',
                    ],
                ],
                'App.filter', // session key
                'anything', // session value
                null, // expected session value
                '302', // expected http status code
                '\Cake\Http\Response', // result type
            ],
            'query parameters' => [ // expected write session filter and return null
                [ // request config
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    '?' => ['any' => 'thing'],
                    'params' => [
                        'object_type' => 'documents',
                    ],
                ],
                'App.filter', // session key
                null, // session value
                ['any' => 'thing'], // expected session value
                null, // expected http status code
                null, // result type
            ],
            'data from session' => [ // expected read session filter and redirect
                [ // request config
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    'params' => [
                        'object_type' => 'documents',
                    ],
                ],
                'App.filter', // session key
                ['any' => 'thing'], // session value
                ['any' => 'thing'], // expected session value
                '302', // expected http status code
                '\Cake\Http\Response', // result type
            ],
        ];
    }

    /**
     * Test `applySessionFilter` method
     *
     * @covers ::applySessionFilter()
     * @dataProvider applySessionFilterProvider()
     *
     * @param array $requestConfig
     * @param string $sessionKey
     * @param mixed|null $sessionValue
     * @param mixed|null $expectedSessionValue
     * @param string|null $expectedHttpStatusCode
     * @param string|null $expectedResultType
     *
     * @return void
     */
    public function testApplySessionFilter($requestConfig, $sessionKey, $sessionValue, $expectedSessionValue, $expectedHttpStatusCode, $expectedResultType): void
    {
        // Setup controller for test
        $this->setupController($requestConfig);

        // get session and write data on it
        $session = $this->AppController->request->getSession();
        $session->write($sessionKey, $sessionValue);

        // do controller call
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('applySessionFilter');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->AppController, []);

        // verify session data and http status code
        static::assertEquals($session->read($sessionKey), $expectedSessionValue);
        if ($result == null) {
            static::assertNull($expectedResultType);
        } else {
            static::assertTrue($result instanceof $expectedResultType);
        }
        if ($expectedResultType === '\Cake\Http\Response') {
            static::assertEquals($result->getStatusCode(), $expectedHttpStatusCode);
        }
    }

    /**
     * Data provider for `testSetObjectNav` test case.
     *
     * @return array
     */
    public function setObjectNavProvider(): array
    {
        return [
            'animals' => [
                'animals', // $moduleName
                [
                    ['id' => 1001, 'object_type' => 'cats'],
                    ['id' => 1002, 'object_type' => 'dogs'],
                    ['id' => 1003, 'object_type' => 'snakes'],
                ], // $objects
                [
                    'animals' => [
                        1001 => [
                            'prev' => null,
                            'next' => 1002,
                            'index' => 1,
                            'total' => 3,
                            'object_type' => 'cats',
                        ],
                        1002 => [
                            'prev' => 1001,
                            'next' => 1003,
                            'index' => 2,
                            'total' => 3,
                            'object_type' => 'dogs',
                        ],
                        1003 => [
                            'prev' => 1002,
                            'next' => null,
                            'index' => 3,
                            'total' => 3,
                            'object_type' => 'snakes',
                        ],
                    ],
                ], // $expectedObjectNav
                'animals', // expectedObjectNavModule
            ],
            'snakes' => [
                'snakes', // $moduleName
                [
                    ['id' => 5003, 'object_type' => 'snakes'],
                    ['id' => 5004, 'object_type' => 'snakes'],
                    ['id' => 5005, 'object_type' => 'snakes'],
                ], // $objects
                [
                    'snakes' => [
                        5003 => [
                            'prev' => null,
                            'next' => 5004,
                            'index' => 1,
                            'total' => 3,
                            'object_type' => 'snakes',
                        ],
                        5004 => [
                            'prev' => 5003,
                            'next' => 5005,
                            'index' => 2,
                            'total' => 3,
                            'object_type' => 'snakes',
                        ],
                        5005 => [
                            'prev' => 5004,
                            'next' => null,
                            'index' => 3,
                            'total' => 3,
                            'object_type' => 'snakes',
                        ],
                    ],
                ], // $expectedObjectNav
                'snakes', // expectedObjectNavModule
            ],
        ];
    }

    /**
     * Test `setObjectNav`
     *
     * @param string $moduleName The module name for the test
     * @param array $objects The objects to filter to set object nav
     * @param array $expectedObjectNav The object nav array expected
     * @param string $expectedObjectNavModule The object type string type expected
     *
     * @covers ::setObjectNav()
     * @dataProvider setObjectNavProvider()
     *
     * @return void
     */
    public function testSetObjectNav(string $moduleName, array $objects, array $expectedObjectNav, string $expectedObjectNavModule): void
    {
        // Setup controller for test
        $this->setupController();
        $this->AppController->Modules->setConfig('currentModuleName', $moduleName);

        // do controller call
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('setObjectNav');
        $method->setAccessible(true);
        $method->invokeArgs($this->AppController, [ $objects ]);

        // verify session data
        $session = $this->AppController->request->getSession();
        static::assertEquals($session->read('objectNav'), $expectedObjectNav);
        static::assertEquals($session->read('objectNavModule'), $expectedObjectNavModule);
    }

    /**
     * Data provider for `testGetObjectNav` test case.
     *
     * @return array
     */
    public function getObjectNavProvider(): array
    {
        return [
            'empty' => [
                'animals', // $moduleName
                [], // $objects
            ],
            'animals' => [
                'animals', // $moduleName
                [
                    ['id' => 1001, 'object_type' => 'cats'],
                    ['id' => 1002, 'object_type' => 'dogs'],
                    ['id' => 1003, 'object_type' => 'snakes'],
                ], // $objects
            ],
            'snakes' => [
                'snakes', // $moduleName
                [
                    ['id' => 5003, 'object_type' => 'snakes'],
                    ['id' => 5004, 'object_type' => 'snakes'],
                    ['id' => 5005, 'object_type' => 'snakes'],
                ], // $objects
            ],
        ];
    }

    /**
     * Test `getObjectNav`
     *
     * @param string $moduleName The module name for the test
     * @param array $objects The objects to filter to set object nav
     *
     * @covers ::getObjectNav()
     * @dataProvider getObjectNavProvider()
     *
     * @return void
     */
    public function testGetObjectNav(string $moduleName, array $objects): void
    {
        // Setup controller for test
        $this->setupController();
        $this->AppController->Modules->setConfig('currentModuleName', $moduleName);

        // set objectNav data
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('setObjectNav');
        $method->setAccessible(true);
        $method->invokeArgs($this->AppController, [ $objects ]);

        // get session data
        $session = $this->AppController->request->getSession();
        $objectNav = $session->read('objectNav');

        foreach ($objects as $object) {
            // do controller call
            $method = $reflectionClass->getMethod('getObjectNav');
            $method->setAccessible(true);
            $result = $method->invokeArgs($this->AppController, [ (string)$object['id'] ]);

            // verify objectNav data for id
            static::assertEquals($objectNav[$moduleName][$object['id']], $result);
        }

        // verify objectNavModule
        static::assertEquals($session->read('objectNavModule'), $moduleName);
    }
}
