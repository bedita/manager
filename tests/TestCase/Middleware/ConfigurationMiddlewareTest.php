<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\Middleware;

use App\Middleware\ConfigurationMiddleware;
use App\Utility\CacheTools;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * {@see \App\Middleware\ConfigurationMiddleware} Test Case
 *
 * @coversDefaultClass \App\Middleware\ConfigurationMiddleware
 */
class ConfigurationMiddlewareTest extends TestCase
{
    /**
     * Test `process` method.
     *
     * @return void
     * @covers ::process()
     */
    public function testProcess(): void
    {
        Cache::enable();
        $config = [
            'id' => 1,
            'attributes' => ['name' => 'Gustavo', 'content' => '["Supporto"]'],
        ];
        Cache::write(CacheTools::cacheKey('api_config'), [$config]);

        $middleware = new ConfigurationMiddleware();
        $handler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
        $middleware->process(ServerRequestFactory::fromGlobals(), $handler);

        $item = Configure::read('Gustavo');
        static::assertEquals(['Supporto'], $item);
        Cache::disable();
    }
}
