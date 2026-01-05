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
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Utility\SchemaTrait} Test Case
 */
#[CoversClass(SchemaTrait::class)]
#[CoversTrait(SchemaTrait::class)]
#[CoversMethod(SchemaTrait::class, 'getMeta')]
class SchemaTraitTest extends TestCase
{
    use SchemaTrait;

    protected $Authentication;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        Cache::clearAll();
        $controller = new Controller(new ServerRequest());
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
     */
    public function testGetMetaException(): void
    {
        $apiClient = new class ('https://api.example.org') extends BEditaClient {
            public function get(string $path, ?array $query = null, ?array $headers = null): ?array
            {
                if ($path === 'home') {
                    throw new BEditaClientException('test');
                }

                return [];
            }
        };
        ApiClientProvider::setApiClient($apiClient);
        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $actual = $this->getMeta($user);
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
