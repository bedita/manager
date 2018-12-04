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

use App\Controller\ModelController;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\UnauthorizedException;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\ModelController} Test Case
 *
 * @coversDefaultClass \App\Controller\ModelController
 */
class ModelControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\ModelController
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
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig = []) : void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->ModelController = new ModelController($request);
    }

    /**
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     *
     */
    public function beforeFilterProvider() : array
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
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     *
     * @return void
     */
    public function testBeforeFilter($expected, $data, $exception) : void
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
    public function testBeforeRender() : void
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
    public function testIndex() : void
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
    public function testIndexFail() : void
    {
        $this->setupController([
            'params' => [
                'resource_type' => 'documents',
            ]
        ]);
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
    public function testView() : void
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
    public function testViewFail() : void
    {
        $this->setupController();
        $result = $this->ModelController->view(0);
        static::assertTrue(($result instanceof Response));
    }
}
