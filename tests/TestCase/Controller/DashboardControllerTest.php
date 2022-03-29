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
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\WebTools\ApiClientProvider;
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
     * @covers ::recentItems()
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

        $this->setupController($requestConfig);
        $this->Dashboard->index();
        $response = $this->Dashboard->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        // recent items
        static::assertArrayHasKey('recentItems', $this->Dashboard->viewBuilder()->getVars());
        static::assertEmpty($this->Dashboard->viewBuilder()->getVar('recentItems'));
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

    /**
     * Test `recentItems` method
     *
     * @covers ::recentItems()
     * @return void
     */
    public function testRecentItems(): void
    {
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);

        // Mock Authentication component
        $this->Dashboard->setRequest($this->Dashboard->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        // setup api
        $client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $client->authenticate($adminUser, $adminPassword);
        $client->setupTokens($response['meta']);
        // set auth user admin
        $this->Dashboard->Authentication->setIdentity(new Identity(['id' => 1]));
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest();
        $recentItems = $test->invokeMethod($this->Dashboard, 'recentItems', []);
        // at least 1 element (the admin user itself)
        static::assertGreaterThanOrEqual(1, count($recentItems));
    }
}
