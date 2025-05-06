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
use App\Utility\CacheTools;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\WebTools\ApiClientProvider;
use BEdita\WebTools\Identifier\ApiIdentifier;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\DashboardController} Test Case
 */
#[CoversClass(DashboardController::class)]
#[CoversMethod(DashboardController::class, 'index')]
#[CoversMethod(DashboardController::class, 'initialize')]
#[CoversMethod(DashboardController::class, 'messages')]
class DashboardControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\DashboardController
     */
    public DashboardController $Dashboard;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::enable();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Cache::disable();
    }

    /**
     * Setup controller to test with request config
     *
     * @param ?array $config The config
     * @return void
     */
    protected function setupController(?array $config = null): void
    {
        $request = new ServerRequest();
        if ($config != null) {
            $request = new ServerRequest($config);
        }
        $this->Dashboard = new DashboardController($request);
    }

    /**
     * Setup controller and manually login user
     *
     * @return array|null
     */
    protected function setupControllerAndLogin(array $requestConfig): ?array
    {
        $config = $requestConfig + [
            'post' => [
                'username' => env('BEDITA_ADMIN_USR'),
                'password' => env('BEDITA_ADMIN_PWD'),
            ],
        ];
        $this->setupController($config);

        // Mock Authentication component
        ApiClientProvider::getApiClient()->setupTokens([]); // reset client
        $service = new AuthenticationService();
        $service->loadIdentifier(ApiIdentifier::class);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                AbstractIdentifier::CREDENTIAL_USERNAME => 'username',
                AbstractIdentifier::CREDENTIAL_PASSWORD => 'password',
            ],
        ]);
        $this->Dashboard->setRequest($this->Dashboard->getRequest()->withAttribute('authentication', $service));
        $result = $this->Dashboard->Authentication->getAuthenticationService()->authenticate($this->Dashboard->getRequest());
        $identity = new Identity($result->getData());
        $request = $this->Dashboard->getRequest()->withAttribute('identity', $identity);
        $this->Dashboard->setRequest($request);
        $user = $this->Dashboard->Authentication->getIdentity() ?: new Identity([]);
        $this->Dashboard->Authentication->setIdentity($user);

        return $user->getOriginalData();
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
     * Test `initialize` method
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
    public static function indexProvider(): array
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
     * @return void
     */
    #[DataProvider('indexProvider')]
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

        $this->setupControllerAndLogin($requestConfig);

        Cache::clearAll();
        Configure::delete('UI.modules.counters'); // default ['trash']
        $this->Dashboard->set('modules', ['wrong' => [], 'documents' => [], 'images' => [], 'media' => [], 'objects' => [], 'tags' => [], 'trash' => [], 'users' => []]);
        CacheTools::setModuleCount(['meta' => ['pagination' => ['count' => 0]]], 'trash');
        $this->Dashboard->index();
        $response = $this->Dashboard->getResponse();
        $vars = $this->Dashboard->viewBuilder()->getVars();
        $modules = array_keys((array)$vars['modules']);
        foreach ($modules as $name) {
            $count = CacheTools::getModuleCount($name);
            if ($name === 'trash') {
                static::assertTrue(is_numeric($count));
            } else {
                static::assertEquals('-', $count);
            }
        }

        static::assertEquals(200, $response->getStatusCode());
        static::assertArrayHasKey('jobsAllow', $this->Dashboard->viewBuilder()->getVars());

        Cache::clearAll();
        Configure::write('UI.modules.counters', 'none');
        $this->Dashboard->index();
        $vars = $this->Dashboard->viewBuilder()->getVars();
        $modules = array_keys((array)$vars['modules']);
        foreach ($modules as $name) {
            static::assertEquals('-', CacheTools::getModuleCount($name));
        }

        Cache::clearAll();
        Configure::write('UI.modules.counters', 'all');
        $this->Dashboard->index();
        $vars = $this->Dashboard->viewBuilder()->getVars();
        $modules = array_keys((array)$vars['modules']);
        foreach ($modules as $name) {
            $count = CacheTools::getModuleCount($name);
            if ($name === 'wrong') {
                static::assertEquals('-', $count);
            } else {
                static::assertTrue(is_numeric($count));
            }
        }
    }

    /**
     * Test `messages` method
     *
     * @return void
     */
    public function testMessages(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);
        $this->Dashboard->messages();
        $response = $this->Dashboard->getResponse();
        static::assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test `messages` method for "MethodNotAllowed" case
     *
     * @return void
     */
    public function testMessagesMethodNotAllowed(): void
    {
        $notallowed = ['POST', 'DELETE', 'PATCH'];
        foreach ($notallowed as $method) {
            static::expectException('Cake\Http\Exception\MethodNotAllowedException');
            $this->setupController([
                'environment' => [
                    'REQUEST_METHOD' => $method,
                ],
            ]);
            $this->Dashboard->messages();
        }
    }
}
