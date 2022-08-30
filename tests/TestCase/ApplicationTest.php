<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase;

use App\Application;
use App\Identifier\ApiIdentifier;
use App\Middleware\ConfigurationMiddleware;
use App\Middleware\ProjectMiddleware;
use App\Middleware\StatusMiddleware;
use Authentication\AuthenticationService;
use Authentication\Authenticator\AuthenticatorInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Identifier\Resolver\ResolverInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use BEdita\I18n\Middleware\I18nMiddleware;
use BEdita\WebTools\Middleware\OAuth2Middleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\TestCase;

/**
 * \App\Application Test Case
 *
 * @coversDefaultClass \App\Application
 */
class ApplicationTest extends TestCase
{
    /**
     * Test `middleware` method
     *
     * @return void
     * @covers ::middleware()
     * @covers ::csrfMiddleware()
     * @covers ::bootstrap()
     * @covers ::bootstrapCli()
     */
    public function testMiddleware(): void
    {
        $app = new Application(CONFIG);
        $app->bootstrap();

        $middleware = new MiddlewareQueue();
        $middleware = $app->middleware($middleware);
        $middleware->rewind();

        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(ProjectMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(StatusMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(ConfigurationMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(AssetMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(I18nMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(RoutingMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(CsrfProtectionMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(AuthenticationMiddleware::class, $middleware->current());
        $middleware->next();
        static::assertInstanceOf(OAuth2Middleware::class, $middleware->current());
    }

    /**
     * Test `bootstrap` method
     *
     * @return void
     * @covers ::bootstrap()
     * @covers ::bootstrapCli()
     */
    public function testBootstrap(): void
    {
        $app = new Application(CONFIG);
        $app->bootstrap();

        static::assertTrue($app->getPlugins()->has('Bake'));
        static::assertTrue($app->getPlugins()->has('IdeHelper'));
        static::assertTrue($app->getPlugins()->has('BEdita/WebTools'));
        static::assertTrue($app->getPlugins()->has('BEdita/I18n'));
        static::assertTrue($app->getPlugins()->has('Authentication'));
    }

    /**
     * Test `loadPluginsFromConfig` method
     *
     * @return void
     * @covers ::loadPluginsFromConfig()
     */
    public function testLoadPlugins(): void
    {
        $app = new Application(CONFIG);
        $app->bootstrap();

        $debug = Configure::read('debug');
        $pluginsConfig = [
            'DebugKit' => ['debugOnly' => true],
            'Bake' => ['debugOnly' => false],
        ];
        $app->getPlugins()->remove('DebugKit');
        $app->getPlugins()->remove('Bake');
        Configure::write('debug', 1);
        Configure::write('Plugins', $pluginsConfig);
        $app->loadPluginsFromConfig();
        $this->assertTrue($app->getPlugins()->has('DebugKit'));
        $this->assertTrue($app->getPlugins()->has('Bake'));
        $app->getPlugins()->remove('DebugKit');
        $app->getPlugins()->remove('Bake');
        Configure::write('debug', 0);
        $app->loadPluginsFromConfig();
        $this->assertFalse($app->getPlugins()->has('DebugKit'));
        $this->assertTrue($app->getPlugins()->has('Bake'));
        Configure::write('debug', $debug);
    }

    /**
     * Test `loadProjectConfig` method
     *
     * @return void
     * @covers ::loadProjectConfig()
     */
    public function testLoadProjectConfig(): void
    {
        Application::loadProjectConfig(null, '');
        static::assertEmpty(Configure::read('Project'));

        $projectsPath = TESTS . 'files' . DS . 'projects' . DS;
        Configure::write('Project.name', null);
        Application::loadProjectConfig('none', $projectsPath);
        static::assertNull(Configure::read('Project.name'));

        Application::loadProjectConfig('test', $projectsPath);
        static::assertEquals('Test', Configure::read('Project.name'));
    }

    /**
     * Test `getAuthenticationService` method.
     *
     * @return void
     * @covers ::getAuthenticationService()
     */
    public function testGetAuthenticationService(): void
    {
        $app = new Application(CONFIG);
        /** @var \Authentication\AuthenticationService $authService */
        $authService = $app->getAuthenticationService(new ServerRequest());
        /** @var \App\Identifier\ApiIdentifier $identifier */
        $identifier = $authService->identifiers()->get(ApiIdentifier::class);
        static::assertInstanceOf(AuthenticationService::class, $authService);
        static::assertInstanceOf(IdentifierInterface::class, $identifier);
        static::assertInstanceOf(ResolverInterface::class, $identifier->getResolver());
        static::assertInstanceOf(AuthenticatorInterface::class, $authService->authenticators()->get('Session'));
    }

    /**
     * Test `getAuthenticationService` method on login requests.
     *
     * @return void
     * @covers ::getAuthenticationService()
     */
    public function testLoginGetAuthenticationService(): void
    {
        $app = new Application(CONFIG);
        $request = new ServerRequest(['url' => '/login']);
        /** @var \Authentication\AuthenticationService $authService */
        $authService = $app->getAuthenticationService($request);

        static::assertFalse($authService->authenticators()->has('BEdita/WebTools.OAuth2'));
        static::assertTrue($authService->authenticators()->has('Form'));
        static::assertInstanceOf(AuthenticatorInterface::class, $authService->authenticators()->get('Form'));
    }

    /**
     * Test `getAuthenticationService` method on login requests.
     *
     * @return void
     * @covers ::getAuthenticationService()
     */
    public function testOAuth2GetAuthenticationService(): void
    {
        $app = new Application(CONFIG);
        $request = new ServerRequest(['url' => '/ext/login/google']);
        /** @var \Authentication\AuthenticationService $authService */
        $authService = $app->getAuthenticationService($request);

        static::assertFalse($authService->authenticators()->has('Form'));
        static::assertTrue($authService->authenticators()->has('OAuth2'));
        static::assertInstanceOf(AuthenticatorInterface::class, $authService->authenticators()->get('OAuth2'));
        static::assertTrue($authService->identifiers()->has('OAuth2'));
        static::assertInstanceOf(IdentifierInterface::class, $authService->identifiers()->get('OAuth2'));
    }
}
