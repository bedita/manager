<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\RolesController;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\RolesController} test Case
 */
#[CoversClass(RolesController::class)]
#[CoversMethod(RolesController::class, 'allowed')]
#[CoversMethod(RolesController::class, 'beforeFilter')]
#[CoversMethod(RolesController::class, 'list')]
class RolesControllerTest extends TestCase
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
     * Test subject
     *
     * @var \App\Controller\RolesController
     */
    protected $RolesController;

    /**
     * Setup controller to test with request config
     *
     * @param array $config configuration for controller setup
     * @return void
     */
    protected function setupController($config = null): void
    {
        $request = null;
        if ($config != null) {
            $headers = (array)Hash::get($config, 'headers', []);
            unset($config['headers']);
            $request = new ServerRequest($config);
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }
        $this->RolesController = new RolesController($request);
    }

    /**
     * Data provider for `testUnauthorizedException` test case.
     *
     * @return array
     */
    public static function unauthorizedExceptionProvider(): array
    {
        return [
            'no same origin' => [
                [
                    'headers' => [
                        'Referer' => 'http://example.com',
                    ],
                ],
            ],
            'no referer' => [
                [
                    'headers' => [
                        'Sec-Fetch-Site' => 'same-origin',
                    ],
                ],
            ],
            'navigate' => [
                [
                    'headers' => [
                        'Sec-Fetch-Site' => 'same-origin',
                        'Referer' => 'http://example.com',
                        'Sec-Fetch-Mode' => 'navigate',
                    ],
                ],
            ],
            'user empty roles' => [
                [
                    'headers' => [
                        'Sec-Fetch-Site' => 'same-origin',
                        'Referer' => 'http://example.com',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for unauthorized exception
     *
     * @param array $config Request configuration.
     * @return void
     */
    #[DataProvider('unauthorizedExceptionProvider')]
    public function testUnauthorizedException(array $config): void
    {
        $expected = new UnauthorizedException(__('You are not authorized to access this resource'));
        $this->expectException(get_class($expected));
        $this->expectExceptionCode($expected->getCode());
        $this->expectExceptionMessage($expected->getMessage());
        $this->setupController($config);
        $this->RolesController->setRequest($this->RolesController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->RolesController->dispatchEvent('Controller.initialize');
    }

    /**
     * Test unauthorized role
     *
     * @return void
     */
    public function testUnauthorizedRole(): void
    {
        $expected = new UnauthorizedException(__('You are not authorized to access this resource'));
        $this->expectException(get_class($expected));
        $this->expectExceptionCode($expected->getCode());
        $this->expectExceptionMessage($expected->getMessage());
        $this->setupController([
            'headers' => [
                'Sec-Fetch-Site' => 'same-origin',
                'Referer' => 'http://example.com',
            ],
        ]);
        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => [],
        ]);
        $this->RolesController->setRequest($this->RolesController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->RolesController->Authentication->setIdentity($user);
        $this->RolesController->dispatchEvent('Controller.initialize');
    }

    /**
     * Test for authorized admin
     *
     * @return void
     */
    public function testAuthorizeAdmin(): void
    {
        $this->setupController([
            'headers' => [
                'Sec-Fetch-Site' => 'same-origin',
                'Referer' => 'http://example.com',
            ],
        ]);
        $user = new Identity([
            'id' => 1,
            'username' => 'admin',
            'roles' => ['admin'],
        ]);
        $this->RolesController->setRequest($this->RolesController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->RolesController->Authentication->setIdentity($user);
        $this->RolesController->dispatchEvent('Controller.initialize');
        $actual = $this->RolesController->Security->getConfig('unlockedActions');
        $expected = [];
        static::assertEquals($expected, $actual);
        $response = $this->RolesController->list();
        static::assertNull($response);
        $data = $this->RolesController->viewBuilder()->getVars();
        $roles = (array)Hash::extract($data, 'data.{n}.attributes.name');
        static::assertNotEmpty($roles);
        static::assertTrue(in_array('admin', $roles), 'Admin role should be present in the list of roles');

        // test exception when API client fails
        $safeClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willThrowException(new BEditaClientException('API error'));
        $controller = new class (new ServerRequest()) extends RolesController {
            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }

            protected function allowed(): bool
            {
                // Allow the request to proceed for testing purposes
                return true;
            }
        };
        $controller->setApiClient($apiClient);
        $controller->list();
        $data = $controller->viewBuilder()->getVars();
        $error = Hash::get($data, 'error');
        static::assertNotEmpty($error);
        static::assertEquals('API error', $error->getMessage(), 'Error message should match the exception thrown by the API client');

        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test for authorized user
     *
     * @return void
     */
    public function testUserAllowed(): void
    {
        $this->setupController([
            'params' => ['pass' => ['events']],
            'headers' => [
                'Sec-Fetch-Site' => 'same-origin',
                'Referer' => 'http://example.com',
            ],
        ]);
        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => ['manager'],
        ]);
        $this->RolesController->setRequest($this->RolesController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->RolesController->Authentication->setIdentity($user);
        $this->RolesController->dispatchEvent('Controller.initialize');
        $actual = $this->RolesController->Security->getConfig('unlockedActions');
        $expected = [];
        static::assertEquals($expected, $actual);
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
}
