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

use App\Controller\ModulesController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\ModulesController} Test Case
 *
 * @coversDefaultClass \App\Controller\ModulesController
 */
class ModulesControllerTest extends TestCase
{
    /**
     * Test Modules controller
     *
     * @var App\Controller\ModulesController
     */
    public $controller;

    /**
     * Test api client
     *
     * @var BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Setup modules controller for test
     *
     * @return void
     */
    private function setupController() : void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesController($request);
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi() : void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     *
     * @return void
     */
    public function testIndex() : void
    {
        $this->setupController();
        $result = $this->controller->index();
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     *
     * @return void
     */
    public function testView() : void
    {
        $this->setupController();
        $response = $this->client->get('/documents', ['page' => 1, 'page_size' => 5]);
        foreach ($response['data'] as $object) {
            // test 200 OK
            $this->setupController(); // renew controller
            $this->controller->objectType = $object['type'];
            $result = $this->controller->view($object['id']);
            static::assertNull($result);
            static::assertEquals(200, $this->controller->response->statusCode());
            static::assertEquals('text/html', $this->controller->response->type());
        }
    }

    /**
     * Test `uname` method
     *
     * @covers ::uname()
     *
     * @return void
     */
    public function testUname() : void
    {
        $this->setupController();
        $response = $this->client->get('/objects', ['page' => 1, 'page_size' => 5]);
        foreach ($response['data'] as $object) {
            $this->setupController(); // renew controller
            // by id
            $result = $this->controller->uname($object['id']);
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals(sprintf('/%s/view/%s', $object['type'], $object['id']), $header['Location']);
            // by uname
            $result = $this->controller->uname($object['attributes']['uname']);
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals(sprintf('/%s/view/%s', $object['type'], $object['id']), $header['Location']);
        }
    }

    /**
     * Test `uname` method, case 404 Not Found
     *
     * @covers ::uname()
     *
     * @return void
     */
    public function testUname404() : void
    {
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'GET']]);
        $this->controller = new ModulesController();
        $result = $this->controller->uname('just-a-wrong-uname');
        $header = $result->header();
        static::assertEquals(302, $result->statusCode()); // redir to referer
        static::assertEquals('/', $header['Location']);
    }
}
