<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\TreeController;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\TreeController} Test Case
 *
 * @coversDefaultClass \App\Controller\TreeController
 * @uses \App\Controller\TreeController
 */
class TreeControllerTest extends TestCase
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
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

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
     * Test `get` method
     *
     * @return void
     * @covers ::get()
     * @covers ::data()
     * @covers ::fetchData()
     */
    public function testGet(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'query' => ['filter' => ['roots' => true], 'pageSize' => 10],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->get();
        $actual = $tree->viewBuilder()->getVar('tree');
        static::assertNotEmpty($actual);
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertArrayHasKey($var, $actual);
        }
    }

    /**
     * Test `data` method on exception
     *
     * @return void
     * @covers ::get()
     * @covers ::data()
     */
    public function testDataException(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'query' => ['filter' => ['parent' => 999], 'pageSize' => 10],
        ];
        $request = new ServerRequest($config);
        $tree = new class ($request) extends TreeController {
            public function fetchData(array $query): array
            {
                throw new BEditaClientException('test exception');
            }
        };
        $tree->get();
        $actual = $tree->viewBuilder()->getVar('tree');
        static::assertEmpty($actual);
    }
}
