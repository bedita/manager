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
namespace App\Test\Middleware;

use App\Application;
use App\Middleware\ProjectMiddleware;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * {@see \App\Middleware\ProjectMiddleware} Test Case
 *
 * @coversDefaultClass \App\Middleware\ProjectMiddleware
 */
class ProjectMiddlewareTest extends TestCase
{
    /**
     * Data provider for `testInvoke()`
     *
     * @return array
     */
    public function invokeProvider(): array
    {
        return [
            'test session' => [
                [
                    'name' => 'Test',
                ],
                [
                    '_project' => 'test',
                ],
            ],
            'test request' => [
                [
                    'name' => 'Test',
                ],
                [
                    'project' => 'test',
                ],
            ],
            'no project' => [
                null,
                [
                    'gustavo' => 'supporto',
                ],
            ],
        ];
    }

    /**
     * Test `process` method.
     *
     * @param int $expected The HTTP status code expected
     * @param array $data Request session data
     * @return void
     * @dataProvider invokeProvider
     * @covers ::__construct()
     * @covers ::process()
     * @covers ::detectProject()
     */
    public function testInvoke($expected, $data): void
    {
        Configure::write('Project', null);
        $request = ServerRequestFactory::fromGlobals();
        $request->getSession()->write($data);
        foreach ($data as $key => $value) {
            $request = $request->withData($key, $value);
        }
        $app = new Application(CONFIG);
        $middleware = new ProjectMiddleware($app, TESTS . 'files' . DS . 'projects' . DS);
        $handler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
        $middleware->process($request, $handler);

        $project = Configure::read('Project');
        static::assertEquals($expected, $project);
        Configure::write('Project', null);
    }
}
