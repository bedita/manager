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
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\LoginController} Test Case
 */
#[CoversClass(LoginController::class)]
#[CoversMethod(LoginController::class, 'authRequest')]
#[CoversMethod(LoginController::class, 'handleFlashMessages')]
#[CoversMethod(LoginController::class, 'initialize')]
#[CoversMethod(LoginController::class, 'loadAvailableProjects')]
#[CoversMethod(LoginController::class, 'login')]
#[CoversMethod(LoginController::class, 'logout')]
#[CoversMethod(LoginController::class, 'setupCurrentProject')]
class LoginControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\LoginController
     */
    public LoginController $Login;

    /**
     * Test request config
     *
     * @var array
     */
    public array $defaultRequestConfig = [
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
        $service->loadAuthenticator('Authentication.Form', [
            'identifier' => ApiIdentifier::class,
            'fields' => [
                AbstractIdentifier::CREDENTIAL_USERNAME => 'username',
                AbstractIdentifier::CREDENTIAL_PASSWORD => 'password',
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
     * @return void
     */
    #[CoversNothing()]
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
     * Test `login` method with POST
     *
     * @return void
     */
    public function testLoginPost(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
        ]);
        static::assertNull($this->Login->login());
        static::assertSame('Invalid username or password', $this->Login->getRequest()->getSession()->read('Flash')['flash'][0]['message']);
    }

    /**
     * Test `loadAvailableProjects` method with GET
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
