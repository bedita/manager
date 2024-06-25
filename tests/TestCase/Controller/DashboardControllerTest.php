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
use Authentication\Identifier\IdentifierInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\WebTools\ApiClientProvider;
use BEdita\WebTools\Identifier\ApiIdentifier;
use Cake\Cache\Cache;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\DashboardController} Test Case
 *
 * @coversDefaultClass \App\Controller\DashboardController
 * @uses \App\Controller\DashboardController
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
    protected function setupController($config = null): void
    {
        $request = null;
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
                IdentifierInterface::CREDENTIAL_USERNAME => 'username',
                IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
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
     * @covers ::initialize()
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
     * @covers ::index()
     * @dataProvider indexProvider()
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

        $this->setupControllerAndLogin($requestConfig);

        $this->Dashboard->set('modules', ['abcde' => [], 'wrong' => [], 'documents' => [], 'images' => [], 'media' => [], 'objects' => [], 'tags' => [], 'trash' => [], 'users' => []]);
        CacheTools::setModuleCount(['meta' => ['pagination' => ['count' => 0]]], 'abcde');
        $this->Dashboard->index();
        $response = $this->Dashboard->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        static::assertArrayHasKey('jobsAllow', $this->Dashboard->viewBuilder()->getVars());
    }

    /**
     * Test `messages` method
     *
     * @covers ::messages()
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
     * @covers ::messages()
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
