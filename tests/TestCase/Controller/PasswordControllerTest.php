<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\PasswordController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * {@see \App\Controller\PasswordController} Test Case
 *
 * @coversDefaultClass \App\Controller\PasswordController
 * @uses \App\Controller\PasswordController
 */
class PasswordControllerTest extends TestCase
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
     * @var \App\Controller\PasswordController
     */
    public $Password;

    /**
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Setup controller to test with request config
     *
     * @param array $config The request config
     * @param array $mock The parameters to mock api client
     * @return void
     */
    private function setupController(array $config, array $mock): void
    {
        $request = new ServerRequest($config);
        $this->Password = new PasswordController($request);
        if (empty($mock)) {
            return;
        }
        $this->client = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $method = Hash::get($mock, 'method');
        $with = Hash::get($mock, 'with');
        $exception = Hash::get($mock, 'exception');
        if ($exception === null) {
            $this->client->method($method)->with($with[0], $with[1], $with[2])->willReturn(Hash::get($mock, 'return'));
        } else {
            $this->client->method($method)->with($with[0], $with[1], $with[2])->willThrowException($exception);
        }
        // set $this->Password->apiClient
        $property = new \ReflectionProperty(PasswordController::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Password, $this->client);
    }

    /**
     * Data provider for `testReset` test case.
     *
     * @return array
     */
    public function resetProvider(): array
    {
        $email = 'gustavo@bedita.net';

        return [
            'get' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    'get' => [],
                ], // config
                [], // mock
            ],
            'post' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => compact('email'),
                ], // config
                [
                    'method' => 'post',
                    'with' => [
                        '/auth/change',
                        json_encode([
                            'contact' => 'gustavo@bedita.net',
                            'change_url' => '/login/change',
                        ]),
                        ['Content-Type' => 'application/json'],
                    ],
                    'return' => ['meta' => []],
                ], // mock
            ],
        ];
    }

    /**
     * Test `reset` method
     *
     * @param array $config The config for controller setup
     * @param array $mock The parameters for api client mock
     * @return void
     * @dataProvider resetProvider()
     * @covers ::reset()
     */
    public function testReset(array $config, array $mock): void
    {
        $this->setupController($config, $mock);
        $actual = $this->Password->reset();
        static::assertNull($actual);
    }

    /**
     * Test `reset` method, exception case
     *
     * @return void
     * @covers ::reset()
     */
    public function testResetException(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => ['email' => 'gustavo@bedita.net'],
        ];
        $mock = [
            'method' => 'post',
            'with' => [
                '/auth/change',
                json_encode([
                    'contact' => 'gustavo@bedita.net',
                    'change_url' => '/login/change',
                ]),
                ['Content-Type' => 'application/json'],
            ],
            'return' => [],
            'exception' => new BEditaClientException(),
        ];
        $this->setupController($config, $mock);
        $actual = $this->Password->reset();
        static::assertEquals(302, $actual->getStatusCode());
        static::assertEquals('/login/reset', $actual->getHeaderLine('Location'));
    }

    /**
     * Data provider for `testChange` test case.
     *
     * @return array
     */
    public function changeProvider(): array
    {
        $uuid = Text::uuid();

        return [
            'get' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                    'get' => compact('uuid'),
                ], // config
                [], // mock
                null, // expected
            ],
            'post' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => compact('uuid') + ['login' => 'gustavo@bedita.net', 'password' => '123456789'],
                ], // config
                [
                    'method' => 'patch',
                    'with' => [
                        '/auth/change',
                        json_encode([
                            'uuid' => $uuid,
                            'password' => '123456789',
                            'login' => true,
                        ]),
                        ['Content-Type' => 'application/json'],
                    ],
                    'return' => ['meta' => []],
                ], // mock
                new Response(), // expected
            ],
        ];
    }

    /**
     * Test `change` method
     *
     * @param array $config The config for controller setup
     * @param array $mock The parameters to mock api client
     * @param Response|null $expected The expected result
     * @return void
     * @dataProvider changeProvider()
     * @covers ::change()
     */
    public function testChange(array $config, array $mock, ?Response $expected): void
    {
        $this->setupController($config, $mock);
        $actual = $this->Password->change();
        if ($expected === null) {
            static::assertEquals($expected, $actual);
        } else {
            static::assertEquals(302, $actual->getStatusCode());
            static::assertEquals('/', $actual->getHeaderLine('Location'));
        }
    }

    /**
     * Test `change` method, exception case
     *
     * @return void
     * @covers ::change()
     */
    public function testChangeException(): void
    {
        $uuid = Text::uuid();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => compact('uuid') + ['login' => 'gustavo@bedita.net', 'password' => '123456789'],
        ];
        $mock = [
            'method' => 'patch',
            'with' => [
                '/auth/change',
                json_encode([
                    'uuid' => $uuid,
                    'password' => '123456789',
                    'login' => true,
                ]),
                ['Content-Type' => 'application/json'],
            ],
            'return' => [],
            'exception' => new BEditaClientException(),
        ];
        $this->setupController($config, $mock);
        $actual = $this->Password->change();
        static::assertEquals(302, $actual->getStatusCode());
        static::assertEquals('/login/reset', $actual->getHeaderLine('Location'));
    }
}
