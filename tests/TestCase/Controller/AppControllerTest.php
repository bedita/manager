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
use App\Form\Form;
use App\Identifier\ApiIdentifier;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\AppController} Test Case
 *
 * @coversDefaultClass \App\Controller\AppController
 * @uses \App\Controller\AppController
 */
class AppControllerTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

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

        // Mock Authentication component
        ApiClientProvider::getApiClient()->setupTokens([]); // reset client
        $service = new AuthenticationService();
        $service->loadIdentifier(ApiIdentifier::class);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                IdentifierInterface::CREDENTIAL_USERNAME => 'username',
                IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
            ],
        ]);
        $this->AppController->setRequest($this->AppController->getRequest()->withAttribute('authentication', $service));
        $result = $this->AppController->Authentication->getAuthenticationService()->authenticate($this->AppController->getRequest());
        $identity = new Identity($result->getData());
        $request = $this->AppController->getRequest()->withAttribute('identity', $identity);
        $this->AppController->setRequest($request);
        $user = $this->AppController->Authentication->getIdentity() ?: new Identity([]);
        $this->AppController->Authentication->setIdentity($user);

        return $user->getOriginalData();
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }

    /**
     * test `initialize` function
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $this->setupController();

        static::assertNotEmpty($this->AppController->{'RequestHandler'});
        static::assertNotEmpty($this->AppController->{'Flash'});
        static::assertNotEmpty($this->AppController->{'Security'});
        static::assertNotEmpty($this->AppController->{'Authentication'});
        static::assertNotEmpty($this->AppController->{'Modules'});
        static::assertNotEmpty($this->AppController->{'Schema'});
    }

    /**
     * test `beforeFilter` not logged error
     *
     * @covers ::beforeFilter()
     * @return void
     */
    public function testBeforeFilterLoginError(): void
    {
        $this->setupController();

        // Mock Authentication component
        $this->AppController->setRequest($this->AppController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        $event = $this->AppController->dispatchEvent('Controller.initialize');

        $flash = $this->AppController->getRequest()->getSession()->read('Flash');

        $expected = __('Login required');
        $message = $flash['flash'][0]['message'];

        static::assertEquals($expected, $message);
    }

    /**
     * test 'initialize' and 'beforeFilter' for correct apiClient token setup
     *
     * @return void
     * @covers ::beforeFilter()
     * @covers ::initialize()
     */
    public function testCorrectTokens(): void
    {
        // no auth
        $this->setupControllerAndLogin();
        $expected = ['jwt', 'renew'];
        $emptyUser = new Identity([]);
        $this->AppController->Authentication->setIdentity($emptyUser);
        $this->AppController->dispatchEvent('Controller.initialize');
        $apiClient = $this->accessProperty($this->AppController, 'apiClient');
        $apiClientTokens = $apiClient->getTokens();
        $actual = array_keys($apiClientTokens);
        static::assertEquals($expected, $actual);

        // auth
        $this->setupControllerAndLogin();
        /** @var \Authentication\Identity|null $user */
        $user = $this->AppController->Authentication->getIdentity();
        $expectedtokens = $user->get('tokens');
        $this->AppController->initialize();
        $this->AppController->dispatchEvent('Controller.initialize');
        $apiClient = $this->accessProperty($this->AppController, 'apiClient');
        $apiClientTokens = $apiClient->getTokens();
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
                ['_name' => 'login', '?' => ['redirect' => '/']], // expected
            ],
        ];
    }

    /**
     * test 'loginRedirectRoute'.
     *
     * @covers ::loginRedirectRoute()
     * @dataProvider loginRedirectRouteProvider()
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
     * @return void
     */
    public function testSetupOutputTimezone(): void
    {
        $this->setupController();

        $expected = Configure::read('I18n.timezone');
        $this->AppController->setRequest($this->AppController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->invokeMethod($this->AppController, 'setupOutputTimezone');
        $configTimezone = Configure::read('I18n.timezone');
        static::assertEquals($expected, $configTimezone);

        $this->AppController->setRequest($this->AppController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->AppController->Authentication->setIdentity(new Identity(['timezone' => null]));
        $this->invokeMethod($this->AppController, 'setupOutputTimezone');
        $configTimezone = Configure::read('I18n.timezone');
        static::assertEquals($expected, $configTimezone);

        $expected = 'GMT';
        $this->AppController->Authentication->setIdentity(new Identity(['timezone' => $expected]));
        $this->invokeMethod($this->AppController, 'setupOutputTimezone');
        $configTimezone = Configure::read('I18n.timezone');
        static::assertEquals($expected, $configTimezone);
    }

    /**
     * Test `beforeRender` method for correct user object in Controller viewVars
     *
     * @covers ::beforeRender()
     * @return void
     */
    public function testBeforeRender(): void
    {
        $user = $this->setupControllerAndLogin();

        $event = $this->AppController->dispatchEvent('Controller.beforeRender');

        static::assertArrayHasKey('user', $this->AppController->viewBuilder()->getVars());
        $user = $this->AppController->viewBuilder()->getVar('user');
        static::assertArrayHasKey('tokens', $user);
        static::assertArrayHasKey('jwt', $user['tokens']);
        static::assertArrayHasKey('renew', $user['tokens']);
    }

    /**
     * Test `beforeRender` method, when updating tokens in session
     *
     * @covers ::beforeRender()
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

        // set $this->AppController->apiClient
        $property = new \ReflectionProperty(AppController::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->AppController, $apiClient);

        $this->AppController->dispatchEvent('Controller.beforeRender');

        // assert user objects has been updated
        $user = $this->AppController->viewBuilder()->getVar('user');
        static::assertNotEmpty($user);
        static::assertEquals($updatedToken, $user['tokens']);
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
            'boolean with null value' => [
                'documents', // object_type
                [ // expected
                    'id' => '1', // fake document id
                    //'unchanged-a' => true,
                    //'unchanged-b' => false,
                    //'unchanged-c' => null,
                    'changed-d' => false,
                    'changed-e' => true,
                    'changed-f' => true,
                    'changed-g' => false,
                    'changed-h' => null,
                    'changed-i' => null,
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'unchanged-a' => true,
                    'unchanged-b' => false,
                    'unchanged-c' => Form::NULL_VALUE,
                    'changed-d' => false,
                    'changed-e' => true,
                    'changed-f' => true,
                    'changed-g' => false,
                    'changed-h' => Form::NULL_VALUE,
                    'changed-i' => Form::NULL_VALUE,
                    '_actualAttributes' => '{"unchanged-a":true,"unchanged-b":false,"unchanged-c":null,"changed-d":true,"changed-e":false,"changed-f":null,"changed-g":null,"changed-h":false,"changed-i":true}',
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
                    'date_ranges' => [
                        [
                            'start_date' => '2020-01-01T00:00:00.000Z',
                            'end_date' => '2020-01-01T23:59:59.000Z',
                        ],
                        [
                            'start_date' => '2020-01-01T00:00:00.000Z',
                            'end_date' => '',
                        ],
                    ],
                ],
                [ // data provided
                    'id' => '7',
                    'date_ranges' => json_encode([
                        [
                            'start_date' => '',
                            'end_date' => '',
                        ],
                        [
                            'start_date' => '2020-01-01T00:00:00.000Z',
                            'end_date' => '2020-01-01T23:59:00.000Z',
                        ],
                        [
                            'start_date' => '2020-01-01T00:00:00.000Z',
                            'end_date' => '',
                        ],
                    ]),
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
                    '_api' => [],
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'relations' => [],
                    '_changedParents' => '1',
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
                            'method' => 'addRelated',
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
                    '_changedParents' => '1',
                ],
            ],
            'categories' => [
                'documents',
                [
                    'id' => '2',
                    'categories' => [
                        ['name' => 'Blu'],
                        ['name' => 'Red'],
                        ['name' => 'Green'],
                    ],
                ],
                [
                    'id' => '2',
                    'categories' => json_encode(['Blu', 'Red', 'Green']),
                ],
            ],
            'types' => [
                'documents',
                [
                    'id' => '3',
                    'a' => ['a' => 1],
                    'b' => ['b' => 2],
                    'c' => 'c',
                ],
                [
                    'id' => '3',
                    'a' => '{"a": 1}',
                    'b' => '{"b": 2}',
                    'c' => 'c',
                    '_types' => [
                        'a' => 'json',
                        'b' => 'json',
                        'c' => 'other',
                    ],
                ],
            ],
            'empty json' => [
                'documents',
                [
                    'json_prop' => new \stdClass(),
                    'json_prop2' => [
                        'gin' => 'vodka',
                    ],
                ],
                [
                    '_jsonKeys' => 'json_prop,json_prop2',
                    'json_prop' => '{ }',
                    'json_prop2' => '{"gin":"vodka"}',
                ],
            ],
            'remove related, not parent' => [
                'documents', // object_type
                [
                    'id' => '1',
                    '_api' => [
                        [
                            'method' => 'addRelated',
                            'id' => '1',
                            'relation' => 'parents',
                            'relatedIds' => [
                                [
                                    'id' => '44',
                                    'type' => 'images',
                                ],
                            ],
                        ],
                        [
                            'method' => 'removeRelated',
                            'id' => '1',
                            'relation' => 'parents',
                            'relatedIds' => [
                                [
                                    'id' => '999',
                                    'type' => 'folders',
                                ],
                                [
                                    'id' => '888',
                                    'type' => 'folders',
                                ],
                            ],
                        ],
                    ],
                ],
                [ // data provided
                    'id' => '1', // fake document id
                    'relations' => [
                        'parents' => [
                            'replaceRelated' => [
                                '{ "id": "44", "type": "images"}',
                            ],
                        ],
                    ],
                    '_changedParents' => '44',
                    '_originalParents' => '999,888',
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
     * @covers ::prepareRequest()
     * @covers ::specialAttributes()
     * @covers ::decodeJsonAttributes()
     * @covers ::prepareDateRanges()
     * @covers ::prepareRelations()
     * @covers ::setupParentsRelation()
     * @covers ::changedAttributes()
     * @covers ::filterEmpty()
     * @dataProvider prepareRequestProvider()
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
     * Data provider for `testChangedAttributes` test case.
     *
     * @return array
     */
    public function changedAttributesProvider(): array
    {
        return [
            'missing _actualAttributes' => [
                ['what' => 'ever'],
                ['what' => 'ever'],
            ],
            'invalid json _actualAttributes' => [
                [
                    '_actualAttributes' => '{"""]',
                    'a' => 'whatever',
                    'b' => Form::NULL_VALUE,
                ],
                [
                    'a' => 'whatever',
                    'b' => Form::NULL_VALUE,
                ],
            ],
            'valid json _actualAttributes' => [
                [
                    '_actualAttributes' => '{"a":null,"b":"whatever","c":null}',
                    'a' => Form::NULL_VALUE,
                    'b' => 'whatever',
                ],
                [
                ],
            ],
        ];
    }

    /**
     * Test `changedAttributes` method.
     *
     * @param array $data The data
     * @param array $expected The expected data
     * @return void
     * @covers ::changedAttributes()
     * @dataProvider changedAttributesProvider()
     */
    public function testChangedAttributes(array $data, array $expected): void
    {
        $controller = new class extends AppController {
            /**
             * Wrapper for changedAttributes() method.
             *
             * @param array $data The data
             * @return array
             */
            public function myChangedAttributes(array $data): array
            {
                $this->changedAttributes($data);

                return $data;
            }
        };
        $actual = $controller->myChangedAttributes($data);
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
            'null and empty | unchanged' => [ null, '', 'null_and_empty', false ],
            'empty and null | unchanged' => [ '',null, 'null_and_empty', false ],
            'bool and int | changed' => [ true, 0, 'bool_and_int', true ],
            'bool and int | unchanged' => [ true, 1, 'bool_and_int', false ],
            'bool and string | changed' => [ true, '0', 'bool_and_string', true ],
            'bool and string | unchanged' => [ true, '1', 'bool_and_string', false ],
            'string and string | changed' => [ 'one', 'two', 'string_and_string', true ],
            'string and string | unchanged' => [ 'three', 'three', 'string_and_string', false ],
            'int and string | changed' => [ 1, '2', 'int_and_string', true ],
            'int and string | unchanged' => [ 1, '1', 'int_and_string', false ],
            'string and int | changed' => [ '1', 2, 'string_and_int', true ],
            'string and int | unchanged' => [ '1', 1, 'string_and_int', false ],
            'object and object | changed' => [ ['four'], ['five'], 'object_and_object', true ],
            'object and object | unchanged' => [ ['six'], ['six'], 'object_and_object', false ],
            'date and date | changed' => [ $d1, $d2, 'publish_start', true ],
            'date and date | unchanged' => [ $d1, $d1, 'publish_start', false ],
            'bool and not boolean string | changed' => [ true, 'whatever', 'enabled', true ],
            'categories | changed' => [ [['name' => 'b', 'label' => 'B']], [['name' => 'a'], ['name' => 'b']], 'categories', true ],
            'categories | unchanged' => [ [['name' => 'a', 'label' => 'A'], ['name' => 'b', 'label' => 'B']], [['name' => 'a'], ['name' => 'b']], 'categories', false ],
        ];
    }

    /**
     * Test `hasFieldChanged` method
     *
     * @param mixed $val1 The first value
     * @param mixed $val2 The second value
     * @param bool $expected The expected result from function hasFieldChanged
     * @covers ::hasFieldChanged()
     * @dataProvider hasFieldChangedProvider()
     * @return void
     */
    public function testHasFieldChanged($val1, $val2, $key, $expected): void
    {
        $this->setupController();
        $actual = $this->invokeMethod($this->AppController, 'hasFieldChanged', [$val1, $val2, $key]);
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
     * @return void
     */
    public function testCheckRequest($expected, $params, $config): void
    {
        $this->setupController($config);

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
                'Cake\Http\Response', // result type
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
            'empty params' => [
                [ // request config
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    '?' => [],
                    'params' => [
                        'object_type' => 'documents',
                    ],
                ],
                'App.filter', // session key
                null, // session value
                null, // expected session value
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
                'Cake\Http\Response', // result type
            ],
        ];
    }

    /**
     * Test `applySessionFilter` method
     *
     * @covers ::applySessionFilter()
     * @dataProvider applySessionFilterProvider()
     * @param array $requestConfig
     * @param string $sessionKey
     * @param mixed|null $sessionValue
     * @param mixed|null $expectedSessionValue
     * @param string|null $expectedHttpStatusCode
     * @param string|null $expectedResultType
     * @return void
     */
    public function testApplySessionFilter($requestConfig, $sessionKey, $sessionValue, $expectedSessionValue, $expectedHttpStatusCode, $expectedResultType): void
    {
        // Setup controller for test
        $this->setupController($requestConfig);

        // get session and write data on it
        $session = $this->AppController->getRequest()->getSession();
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
            static::assertNotNull($expectedResultType);
            static::assertSame(get_class($result), $expectedResultType);
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
     * @covers ::setObjectNav()
     * @dataProvider setObjectNavProvider()
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
        $session = $this->AppController->getRequest()->getSession();
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
     * @covers ::getObjectNav()
     * @dataProvider getObjectNavProvider()
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
        $session = $this->AppController->getRequest()->getSession();
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

    /**
     * Test `getObjectNav`, when empty
     *
     * @return void
     * @covers ::getObjectNav()
     */
    public function testGetObjectNavEmpty(): void
    {
        // Setup controller for test
        $this->setupController();

        // set objectNav data to empty array
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('setObjectNav');
        $method->setAccessible(true);
        $method->invokeArgs($this->AppController, [ [] ]);

        // get session data
        $session = $this->AppController->getRequest()->getSession();
        $expected = $session->read('objectNav');

        // do controller call
        $method = $reflectionClass->getMethod('getObjectNav');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->AppController, [ '' ]);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testRelatedIds` test case.
     */
    public function relatedIdsProvider(): array
    {
        return [
            'empty items' => [
                [],
                [],
            ],
            'string items' => [
                '["1", "2", "3"]',
                ['1', '2', '3'],
            ],
            'array of string items' => [
                ['1', '2', '3'],
                ['1', '2', '3'],
            ],
            'array of array items' => [
                [
                    ['id' => '1', 'name' => 'Item 1'],
                    ['id' => '2', 'name' => 'Item 2'],
                    ['id' => '3', 'name' => 'Item 3'],
                ],
                [
                    ['id' => '1', 'name' => 'Item 1'],
                    ['id' => '2', 'name' => 'Item 2'],
                    ['id' => '3', 'name' => 'Item 3'],
                ],
            ],
            'array of JSON string items' => [
                [
                    '{"id": "1", "name": "Item 1"}',
                    '{"id": "2", "name": "Item 2"}',
                    '{"id": "3", "name": "Item 3"}',
                ],
                [
                    ['id' => '1', 'name' => 'Item 1'],
                    ['id' => '2', 'name' => 'Item 2'],
                    ['id' => '3', 'name' => 'Item 3'],
                ],
            ],
        ];
    }

    /**
     * Test relatedIds method with empty items.
     *
     * @param mixed $items The items to test
     * @param array $expected The expected result
     * @return void
     * @covers ::relatedIds()
     * @dataProvider relatedIdsProvider()
     */
    public function testRelatedIds($items, array $expected): void
    {
        $this->setupController();
        $reflectionClass = new \ReflectionClass($this->AppController);
        $method = $reflectionClass->getMethod('relatedIds');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->AppController, [$items]);
        $this->assertEquals($expected, $actual);
    }
}
