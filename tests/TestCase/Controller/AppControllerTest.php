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
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\TestSuite\TestCase;
use Composer\Script\Event;
use Symfony\Component\Config\Definition\Exception\Exception;

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
    public $App;

    /**
     * test init function
     *
     * @covers ::initialize()
     *
     * @return void
     */
    public function testInitialize() : void
    {
        $this->setupController();

        static::assertNotEmpty($this->App->{'RequestHandler'});
        static::assertNotEmpty($this->App->{'Flash'});
        static::assertNotEmpty($this->App->{'Security'});
        static::assertNotEmpty($this->App->{'Csrf'});
        static::assertNotEmpty($this->App->{'Auth'});
        static::assertNotEmpty($this->App->{'Modules'});
        static::assertNotEmpty($this->App->{'Schema'});
    }

    /**
     * test 'beforeFilter' not logged error
     *
     * @covers ::beforeFilter()
     *
     * @return void
     */
    public function testBeforeFilterLoginError() : void
    {
        $this->setupController($config);

        $event = $this->App->dispatchEvent('Controller.initialize');
        $this->App->beforeFilter($event);
        $flash = $this->App->request->getSession()->read('Flash');

        $expected = __('You are not logged or your session has expired, please provide login credentials');
        $message = $flash['flash'][0]['message'];

        static::assertEquals($expected, $message);
    }

    /**
     * test 'beforeFilter' correct apiClient token setup
     *
     * @covers ::beforeFilter()
     *
     * @return void
     */
    public function testBeforeFilterCorrectTokens() : void
    {
        $expectedtokens = [];

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

        $user = $this->App->Auth->identify();
        $this->App->Auth->setUser($user);
        $expectedtokens = $this->App->Auth->user('tokens');

        $event = $this->App->dispatchEvent('Controller.initialize');
        $this->App->beforeFilter($event);

        $apiClient = $this->accessProperty($this->App, 'apiClient');
        $apiClientTokens = $this->accessProperty($apiClient, 'tokens');

        static::assertEquals($expectedtokens, $apiClientTokens);
    }

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController($config = null) : void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
        }
        $this->App = new AppController($request);
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

        $results = $this->invokeMethod($this->App, 'prepareRequest', [$objectType]);

        static::assertEquals($expected, $results);
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
                null,
                [],
                null,
            ],
            'methodNotAllowed' => [
                new MethodNotAllowedException(),
                new ServerRequest(),
                ['allowedMethods' => 'GET'],
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ],
            ],
            'requiredParameters' => [
                new BadRequestException('Empty password'),
                new ServerRequest(),
                ['requiredParameters' => ['password']],
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
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
    public function testCheckRequest($expected, $request, $params, $config) : void
    {
        $this->setupController($config);

        $this->App->request = $request;

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $results = $this->invokeMethod($this->App, 'checkRequest', [$params]);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
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

    public function accessProperty(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
