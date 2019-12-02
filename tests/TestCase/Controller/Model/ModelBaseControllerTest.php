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
 * {@see \App\Controller\ModelBaseController} Test Case
 *
 * @coversDefaultClass \App\Controller\ModelBaseController
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
        ]
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
        $this->ModelController = new ModelController($request);
        $this->setupApi();
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
                false,
                [
                    'username' => 'dummy',
                    'password' => 'dummy',
                    'roles' => [ 'useless' ],
                ],
                new UnauthorizedException(__('Module access not authorized')),
            ],
            'authorized' => [
                true,
                [
                    'username' => 'bedita',
                    'password' => 'bedita',
                    'roles' => [ 'admin' ],
                ],
                null,
            ]
        ];
    }

    /**
     * Test `beforeFilter` method
     *
     * @param array $expected expected results from test
     * @param boolean|null $data setup data for test
     * @param \Exception|null $expection expected exception from test
     *
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     *
     * @return void
     */
    public function testBeforeFilter($expected, $data, $exception): void
    {
        $this->setupController();

        $this->ModelController->Auth->setUser($data);

        if (!empty($exception)) {
            $this->expectException(get_class($exception));
        }

        $event = $this->ModelController->dispatchEvent('Controller.beforeFilter');
        $this->ModelController->beforeFilter($event);

        static::assertTrue($expected);
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
        $this->setupController();
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
        $this->setupController();
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
        $this->setupController();
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
        $this->setupController();
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
        $this->setupController();
        $result = $this->ModelController->view(0);
        static::assertTrue(($result instanceof Response));
    }
}
