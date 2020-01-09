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

use App\Controller\LoginController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\LoginController} Test Case
 *
 * @coversDefaultClass \App\Controller\LoginController
 */
class LoginControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\LoginController
     */
    public $Login;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'POST',
        ],
        'post' => [
            'username' => '',
            'password' => '',
        ],
    ];

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->Login = new LoginController($request);
    }

    /**
     * Test `login` method, no user timezone set
     *
     * @covers ::login()
     * @covers ::userTimezone()
     * @covers ::setupCurrentProject()
     * @return void
     */
    public function testLogin(): void
    {
        $this->setupController([
            'post' => [
                'username' => env('BEDITA_ADMIN_USR'),
                'password' => env('BEDITA_ADMIN_PWD'),
            ],
        ]);

        $response = $this->Login->login();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/', $response->getHeaderLine('Location'));
    }

    /**
     * Test `login` with HTTP HEAD method
     *
     * @coversNothing
     *
     * @return void
     */
    public function testHeadLogin(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'HEAD',
            ],
        ]);

        $response = $this->Login->login();
        static::assertNull($response);
    }

    /**
     * Test `userTimezone` method
     *
     * @covers ::userTimezone()
     *
     * @return void
     */
    public function testLoginTimezone(): void
    {
        $this->setupController([
            'post' => [
                'username' => env('BEDITA_ADMIN_USR'),
                'password' => env('BEDITA_ADMIN_PWD'),
                'timezone_offset' => '7200 0',
            ],
        ]);

        $response = $this->Login->login();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/', $response->getHeaderLine('Location'));

        $tz = $this->Login->Auth->user('timezone');
        static::assertNotEquals('UTC', $tz);
    }

    /**
     * Test `login` fail method
     *
     * @covers ::login()
     *
     * @return void
     */
    public function testLoginFailed(): void
    {
        $this->setupController([
            'post' => [
                'username' => 'wronguser',
                'password' => 'wrongpwd',
            ],
        ]);

        $response = $this->Login->login();
        static::assertNull($response);
    }

    /**
     * Test `login` method with GET
     *
     * @covers ::login()
     *
     * @return void
     */
    public function testLoginForm(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $response = $this->Login->login();
        static::assertNull($response);
    }

    /**
     * Test `loadAvailableProjects` method with GET
     *
     * @covers ::loadAvailableProjects()
     *
     * @return void
     */
    public function testLoadAvailableProjects(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $this->Login->setConfig('projectsSearch', TESTS . 'files' . DS . 'projects' . DS . '*.php');

        $response = $this->Login->login();
        static::assertNull($response);
        $viewVars = $this->Login->viewVars;
        static::assertArrayHasKey('projects', $viewVars);
        $expected = [
            [
                'value' => 'test',
                'text' => 'Test',
            ],
        ];
        static::assertEquals($expected, $viewVars['projects']);
    }

    /**
     * Test `setupCurrentProject` method
     *
     * @covers ::setupCurrentProject()
     *
     * @return void
     */
    public function testSetupCurrentProject(): void
    {
        $this->setupController([
            'post' => [
                'username' => env('BEDITA_ADMIN_USR'),
                'password' => env('BEDITA_ADMIN_PWD'),
                'project' => 'test',
            ],
        ]);

        $response = $this->Login->login();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('test', $this->Login->request->getSession()->read('_project'));
    }

    /**
     * Test `logout` method
     *
     * @covers ::logout()
     *
     * @return void
     */
    public function testLogout(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $response = $this->Login->logout();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/login', $response->getHeaderLine('Location'));
    }

    /**
     * Test `handleFlashMessages` method
     *
     * @covers ::handleFlashMessages()
     *
     * @return void
     */
    public function testHandleFlashMessages(): void
    {
        // setup controller
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        // case 1: remove message
        $this->Login->request->getSession()->write('Flash', 'something');
        $this->Login->handleFlashMessages([]);
        $message = $this->Login->request->getSession()->read('Flash');
        static::assertEmpty($message);

        // case 2: do not remove message
        $this->Login->request->getSession()->write('Flash', 'something');
        $this->Login->handleFlashMessages(['redirect' => 'dummy']);
        $message = $this->Login->request->getSession()->read('Flash');
        static::assertEquals('something', $message);
    }
}
