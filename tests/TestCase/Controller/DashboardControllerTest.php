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

use App\Controller\DashboardController;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\DashboardController} Test Case
 *
 * @coversDefaultClass \App\Controller\DashboardController
 */
class DashboardControllerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\DashboardController
     */
    public $Dashboard;

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController($config = null): void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
        }
        $this->Dashboard = new DashboardController($request);
    }

    /**
     * Test `initialize` method
     *
     * @covers ::initialize()
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->setupController();
        $forceHome = $this->Dashboard->Modules->getConfig('clearHomeCache');

        static::assertEquals(true, $forceHome);
    }

    /**
     * Data provider for `testIndex` test case.
     *
     * @return array
     */
    public function indexProvider(): array
    {
        return [
            'post' => [
                new MethodNotAllowedException(),
                'POST',
            ],
            'delete' => [
                new MethodNotAllowedException(),
                'DELETE',
            ],
            'patch' => [
                new MethodNotAllowedException(),
                'PATCH',
            ],
            'get' => [
                null,
                'GET',
            ],
        ];
    }

    /**
     * Test `index` method
     *
     * @param MethodNotAllowedException|null $expected The expected exception or null
     * @param string $method The request method, can be 'GET', 'PATCH', 'POST', 'DELETE'
     *
     * @covers ::index()
     * @dataProvider indexProvider()
     *
     * @return void
     */
    public function testIndex($expected, $method): void
    {
        $requestConfig = [
            'environment' => [
                'REQUEST_METHOD' => $method,
            ],
        ];

        if ($expected != null) {
            static::expectException(get_class($expected));
        }

        $this->setupController($requestConfig);
        $this->Dashboard->index();
        $response = $this->Dashboard->response;

        static::assertEquals(200, $response->getStatusCode());
    }
}
