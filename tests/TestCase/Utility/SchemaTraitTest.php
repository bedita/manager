<?php

declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Utility;

use App\Utility\SchemaTrait;
use Authentication\AuthenticationServiceInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Utility\SchemaTrait} Test Case
 *
 * @coversDefaultClass \App\Utility\SchemaTrait
 */
class SchemaTraitTest extends TestCase
{
    use SchemaTrait;

    protected $Authentication;

    protected $logger = [];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        Cache::clearAll();
        $controller = new Controller();
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $registry = $controller->components();
        $registry->load('Authentication.Authentication');
        /** @var \Authentication\Controller\Component\AuthenticationComponent $authenticationComponent */
        $authenticationComponent = $registry->load(AuthenticationComponent::class);
        $this->Authentication = $authenticationComponent;
        $user = ['id' => 123, 'attributes' => ['name' => 'Gustavo', 'surname' => 'Support']];
        $this->Authentication->setIdentity(new Identity($user));

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        ApiClientProvider::setApiClient(null);
        parent::tearDown();
    }

    /**
     * Test `getMeta`.
     *
     * @return void
     * @covers ::getMeta()
     */
    public function testGetMeta(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/home')
            ->willReturn([
                'meta' => [
                    'cats' => [],
                    'dogs' => [],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $actual = $this->getMeta($user);
        $expected = [
            'cats' => [],
            'dogs' => [],
        ];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getMeta` with exception.
     *
     * @return void
     * @covers ::getMeta()
     */
    public function testGetMetaException(): void
    {
        $this->logger = [];
        $expectedException = new BEditaClientException('test');
        $expectedLogger = [
            [
                'message' => $expectedException->getMessage(),
                'level' => 'error',
            ],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/home')
            ->willThrowException($expectedException);
        ApiClientProvider::setApiClient($apiClient);
        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $actual = $this->getMeta($user);
        $expected = [];
        static::assertEquals($expected, $actual);
        static::assertEquals($expectedLogger, $this->logger);
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

    private function log(string $message, string $level): void
    {
        $this->logger[] = compact('message', 'level');
    }
}
