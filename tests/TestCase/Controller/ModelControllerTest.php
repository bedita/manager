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
    protected function setupController(array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->ModelController = new ModelController($request);
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::beforeFilter()
     * @covers ::beforeRender()
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
