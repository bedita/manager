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
use App\Identifier\ApiIdentifier;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\LoginController} Test Case
 *
 * @coversDefaultClass \App\Controller\LoginController
 * @uses \App\Controller\LoginController
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

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

        // Mock Authentication component and prepare for "real" login
        $service = new AuthenticationService();
        $service->loadIdentifier(ApiIdentifier::class);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                IdentifierInterface::CREDENTIAL_USERNAME => 'username',
                IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
            ],
        ]);
        $this->Login->setRequest($this->Login->getRequest()->withAttribute('authentication', $service));
        $result = $this->Login->Authentication->getAuthenticationService()->authenticate($this->Login->getRequest());
        $identity = new Identity($result->getData() ?: []);
        $this->Login->setRequest($this->Login->getRequest()->withAttribute('identity', $identity));
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }

    /**
     * Test `initialize` method.
     *
     * @covers ::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        static::assertEquals(['login'], $this->Login->Authentication->getUnauthenticatedActions());
    }

    /**
     * Test `authRequest` method, no user timezone set
     *
     * @covers ::authRequest()
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
     * Test `login` fail method
     *
     * @covers ::authRequest()
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
     * @return void
     */
    public function testLoadAvailableProjects(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        $this->Login->setConfig('projectsPath', TESTS . 'files' . DS . 'projects' . DS);

        $response = $this->Login->login();
        static::assertNull($response);
        $viewVars = $this->Login->viewBuilder()->getVars();
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
        static::assertEquals('test', $this->Login->getRequest()->getSession()->read('_project'));
    }

    /**
     * Test `logout` method
     *
     * @covers ::logout()
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
        static::assertNull($this->Login->getRequest()->getSession()->read('Auth'));
        static::assertNull($this->Login->getRequest()->getSession()->read('_project'));
    }

    /**
     * Test `handleFlashMessages` method
     *
     * @covers ::handleFlashMessages()
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
        $this->Login->getRequest()->getSession()->write('Flash', 'something');
        $this->Login->handleFlashMessages([]);
        $message = $this->Login->getRequest()->getSession()->read('Flash');
        static::assertEmpty($message);

        // case 2: do not remove message
        $this->Login->getRequest()->getSession()->write('Flash', 'something');
        $this->Login->handleFlashMessages(['redirect' => 'dummy']);
        $message = $this->Login->getRequest()->getSession()->read('Flash');
        static::assertEquals('something', $message);
    }
}
