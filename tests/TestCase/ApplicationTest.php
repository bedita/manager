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
use App\Middleware\ProjectMiddleware;
use BEdita\I18n\Middleware\I18nMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
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

        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->get(0));
        static::assertInstanceOf(ProjectMiddleware::class, $middleware->get(1));
        static::assertInstanceOf(AssetMiddleware::class, $middleware->get(2));
        static::assertInstanceOf(I18nMiddleware::class, $middleware->get(3));
        static::assertInstanceOf(RoutingMiddleware::class, $middleware->get(4));
        static::assertInstanceOf(CsrfProtectionMiddleware::class, $middleware->get(5));
    }

    /**
     * Test `addPluginDev` method
     *
     * @return void
     * @covers ::addPluginDev()
     */
    public function testAddPluginDev(): void
    {
        $app = new Application(CONFIG);
        static::assertEquals(true, $app->addPluginDev('IdeHelper'));
        static::assertEquals(false, $app->addPluginDev('NotExisting'));
    }

    /**
     * Test `loadPluginsFromConfig` method
     *
     * @return void
     *
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
     *
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
}
