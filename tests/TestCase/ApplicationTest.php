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
use BEdita\I18n\Middleware\I18nMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
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
     *
     * @covers ::middleware
     * @covers ::csrfMiddleware
     * @covers ::bootstrap
     * @covers ::bootstrapcli
     */
    public function testMiddleware() : void
    {
        $app = new Application(CONFIG);
        $app->bootstrap();

        $middleware = new MiddlewareQueue();
        $middleware = $app->middleware($middleware);

        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->get(0));
        static::assertInstanceOf(AssetMiddleware::class, $middleware->get(1));
        static::assertInstanceOf(I18nMiddleware::class, $middleware->get(2));
        static::assertInstanceOf(RoutingMiddleware::class, $middleware->get(3));
        static::assertInstanceOf(CsrfProtectionMiddleware::class, $middleware->get(4));
    }

    /**
     * Test load from config method
     *
     * @return void
     *
     * @covers ::loadFromConfig()
     */
    public function testLoadConfig()
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
        $app->loadFromConfig();
        $this->assertTrue($app->getPlugins()->has('DebugKit'));
        $this->assertTrue($app->getPlugins()->has('Bake'));
        $app->getPlugins()->remove('DebugKit');
        $app->getPlugins()->remove('Bake');
        Configure::write('debug', 0);
        $app->loadFromConfig();
        $this->assertFalse($app->getPlugins()->has('DebugKit'));
        $this->assertTrue($app->getPlugins()->has('Bake'));
        Configure::write('debug', $debug);
    }
}
