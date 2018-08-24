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

use App\Controller\TrashController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

class TrashControllerSample extends TrashController
{
    /**
     * Set api client for test (using mock)
     *
     * @param \BEdita\SDK\BEditaClient $apiClient The api client
     * @return void
     */
    public function setApiClient($apiClient) : void
    {
        $this->apiClient = $apiClient;
    }

    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        // do nothing
    }

    /**
     * {@inheritDoc}
     */
    public function redirect($url, $status = 302) : Response
    {
        return new Response();
    }
}

/**
 * {@see \App\Controller\TrashController} Test Case
 *
 * @coversDefaultClass \App\Controller\TrashController
 */
class TrashControllerTest extends TestCase
{
    /**
     * Test controller
     *
     * @var App\Test\TestCase\Controller\TrashControllerSample
     */
    public $controller;

    /**
     * Test api client
     *
     * @var BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Test `restore` method
     *
     * @covers ::restore()
     *
     * @return void
     */
    public function testRestore() : void
    {
        // Setup controller for test
        $method = 'restoreObject';
        $response = [
            'data' => [
                'id' => 999
            ],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'response'), $request);
        // do controller call
        $result = $this->controller->restore();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `restore` method when unauthorized
     *
     * @covers ::restore()
     *
     * @return void
     */
    public function testRestoreUnauthorized() : void
    {
        // Setup controller for test
        $method = 'restoreObject';
        $exception = new BEditaClientException('Unauthorized', 401);
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'exception'), $request);
        // do controller call
        $result = $this->controller->restore();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     *
     * @return void
     */
    public function testDelete() : void
    {
        // Setup controller for test
        $method = 'remove';
        $response = [
            'data' => [
                'id' => 999
            ],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'response'), $request);
        // do controller call
        $result = $this->controller->delete();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `delete` method when unauthorized
     *
     * @covers ::delete()
     *
     * @return void
     */
    public function testDeleteUnauthorized() : void
    {
        // Setup controller for test
        $method = 'remove';
        $exception = new BEditaClientException('Unauthorized', 401);
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'exception'), $request);
        // do controller call
        $result = $this->controller->delete();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `empty` method
     *
     * @covers ::empty()
     *
     * @return void
     */
    public function testEmpty() : void
    {
        // Setup controller for test
        $method = 'getObjects';
        $response = [
            'data' => [],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'response'), $request);
        // do controller call
        $result = $this->controller->empty();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `empty` method, passing query filter
     *
     * @covers ::empty()
     *
     * @return void
     */
    public function testEmptyWithFilter() : void
    {
        // Setup controller for test
        $method = 'getObjects';
        $response = [
            'data' => [],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
                'filter' => [
                    'object_type' => 'documents',
                ],
            ],
        ];
        $this->initController(compact('method', 'response'), $request);
        // do controller call
        $result = $this->controller->empty();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `empty` method when unauthorized
     *
     * @covers ::empty()
     *
     * @return void
     */
    public function testEmptyUnauthorized() : void
    {
        // Setup controller for test
        $method = 'remove';
        $exception = new BEditaClientException('Unauthorized', 401);
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->initController(compact('method', 'exception'), $request);
        // do controller call
        $result = $this->controller->empty();
        static::assertEquals(200, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Setup api client mock, create the controller for test
     *
     * @param array $mockParams The mock params: 'method', 'response', 'exception'
     * @param Exception|null $mockException The exception that mock method should raise
     * @param array|null $request The request parameters
     * @return void
     */
    private function initController(array $mockParams, ?array $request = null) : void
    {
        if (!empty($mockParams)) {
            foreach (['methods', 'method', 'response', 'exception', 'map'] as $param) {
                ${$param} = (!empty($mockParams[$param])) ? $mockParams[$param] : null;
            }
        }
        $apiClient = $this->getMockBuilder(BEditaClient::class)->setConstructorArgs(['https://api.example.org'])->getMock();
        if (!empty($methods)) {
            foreach ($methods as $m) {
                if (!empty($exception)) {
                    $apiClient->method($m)->will($this->throwException($exception));
                } elseif (!empty($response)) {
                    $apiClient->method($m)->willReturn($response);
                } elseif (!empty($map)) {
                    $apiClient->method($m)->will($this->returnValueMap($map));
                }
            }
        }
        if (!empty($method)) {
            if (!empty($exception)) {
                $apiClient->method($method)->will($this->throwException($exception));
            } elseif (!empty($response)) {
                $apiClient->method($method)->willReturn($response);
            } elseif (!empty($map)) {
                $apiClient->method($method)->will($this->returnValueMap($map));
            }
        }
        ApiClientProvider::setApiClient($apiClient);
        if (empty($request)) {
            $request = [
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'get' => [],
            ];
        }
        $this->controller = new TrashControllerSample(new ServerRequest($request));
        $this->controller->setApiClient($apiClient);
        $this->controller->objectType = (!empty($request['params']['object_type'])) ? $request['params']['object_type'] : 'documents';
    }
}
