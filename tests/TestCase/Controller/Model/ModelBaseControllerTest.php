<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\ModelBaseController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

class ModelController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'object_types';

    /**
     * Set resource type
     *
     * @param string $type Resource type
     * @return void
     */
    public function setResourceType(string $type): void
    {
        $this->resourceType = $type;
    }
}

/**
 * {@see \App\Controller\Model\ModelBaseController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\ModelBaseController
 */
class ModelBaseControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Test\TestCase\Controller\ModelController
     */
    public $ModelController;

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
            'resource_type' => 'object_types',
        ],
    ];

    /**
     * API client
     *
     * @var BEditaClient
     */
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->ModelController = new ModelController($request);

        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     */
    public function beforeFilterProvider(): array
    {
        return [
            'not authorized' => [
                new UnauthorizedException(__('Module access not authorized')),
                [
                    'username' => 'dummy',
                    'roles' => [ 'useless' ],
                    'tokens' => [],
                ],
            ],
            'authorized' => [
                null,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                    'tokens' => [],
                ],
            ],
            'expired' => [
                Response::class,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                ],
            ],
        ];
    }

    /**
     * Test `beforeFilter` method
     *
     * @param \Exception|string|null $expected Expected result
     * @param array $data setup data for test
     *
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     *
     * @return void
     */
    public function testBeforeFilter($expected, array $data): void
    {
        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }
        $this->ModelController->Auth->setUser($data);

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $this->ModelController->dispatchEvent('Controller.beforeFilter');
        $result = $this->ModelController->beforeFilter($event);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        } else {
            static::assertNull($result);
        }
    }

    /**
     * Test `beforeRender` method
     *
     * @covers ::beforeRender()
     *
     * @return void
     */
    public function testBeforeRender(): void
    {
        $this->ModelController->dispatchEvent('Controller.beforeRender');

        static::assertNotEmpty($this->ModelController->viewVars['resourceType']);
        static::assertNotEmpty($this->ModelController->viewVars['moduleLink']);
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::beforeFilter()
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->ModelController->index();
        $vars = ['resources', 'meta', 'links', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewVars[$var]);
        }
    }

    /**
     * Test `index` failure method
     *
     * @covers ::index()
     *
     * @return void
     */
    public function testIndexFail(): void
    {
        $this->ModelController->setResourceType('elements');
        $result = $this->ModelController->index();
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     *
     * @return void
     */
    public function testView(): void
    {
        $this->ModelController->view(1);
        $vars = ['resource', 'schema', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewVars[$var]);
        }
    }

    /**
     * Test `view` failure method
     *
     * @covers ::view()
     *
     * @return void
     */
    public function testViewFail(): void
    {
        $result = $this->ModelController->view(0);
        static::assertTrue(($result instanceof Response));
    }
}
