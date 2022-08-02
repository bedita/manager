<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\Middleware;

use App\Authentication\Identifier\ApiIdentifier;
use App\Controller\AppController;
use App\Middleware\RecoveryMiddleware;
use Authentication\AuthenticationService;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Identity;
use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * {@see \App\Middleware\RecoveryMiddleware} Test Case
 *
 * @coversDefaultClass \App\Middleware\RecoveryMiddleware
 */
class RecoveryMiddlewareTest extends TestCase
{
    /**
     * Controller
     *
     * @var \App\Controller\AppController
     */
    protected $AppController;

    /**
     * Test `process` method.
     *
     * @return void
     * @covers ::process()
     * @covers ::check()
     */
    public function testProcessAndCheck(): void
    {
        // empty config Recovery
        $middleware = new RecoveryMiddleware();
        $handler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
        $actual = $middleware->process(ServerRequestFactory::fromGlobals(), $handler);
        static::assertInstanceOf(Response::class, $actual);

        // config recovery + authentication service AuthenticationServiceInterface
        Configure::write('Recovery', true);
        $actual = $middleware->process(ServerRequestFactory::fromGlobals(), $handler);
        static::assertInstanceOf(Response::class, $actual);

        // user admin
        $this->setupControllerAndLogin();
        $actual = $middleware->process($this->AppController->getRequest(), $handler);
        static::assertInstanceOf(Response::class, $actual);

        // user non admin
        $user = new Identity(['id' => 1, 'roles' => ['guest']]);
        $this->AppController->Authentication->setIdentity($user);
        $this->expectException(\Authentication\Authenticator\UnauthenticatedException::class);
        $middleware->process($this->AppController->getRequest()->withAttribute('identity', $user), $handler);
    }

    /**
     * Setup controller
     *
     * @param array $config configuration for controller setup
     * @return void
     */
    protected function setupController($config = null): void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
            if (!empty($config['?'])) {
                $request = $request->withQueryParams($config['?']);
            }
        }
        $this->AppController = new AppController($request);
    }

    /**
     * Setup controller and manually login user
     *
     * @return array|null
     */
    protected function setupControllerAndLogin(): ?array
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
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
        $this->AppController->setRequest($this->AppController->getRequest()->withAttribute('authentication', $service));
        $result = $this->AppController->Authentication->getAuthenticationService()->authenticate($this->AppController->getRequest());
        $identity = new Identity($result->getData());
        $request = $this->AppController->getRequest()->withAttribute('identity', $identity);
        $this->AppController->setRequest($request);
        $user = $this->AppController->Authentication->getIdentity() ?: new Identity([]);
        $this->AppController->Authentication->setIdentity($user);

        return $user->getOriginalData();
    }
}
