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
use Cake\Http\MiddlewareQueue;
use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Middleware\ProjectMiddleware} Test Case
 *
 * @coversDefaultClass \App\Middleware\ProjectMiddleware
 */
class ProjectMiddlewareTest extends TestCase
{
    /**
     * Fake next middleware
     *
     * @var callable
     */
    protected $nextMiddleware;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->nextMiddleware = function ($req, $res) {
            return $res;
        };
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->nextMiddleware = null;
    }

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
     * Test `__invoke` method.
     *
     * @param int $expected The HTTP status code expected
     * @param array $data Request session data
     * @param array $server The server vars
     * @return void
     * @dataProvider invokeProvider
     * @covers ::__construct()
     * @covers ::__invoke()
     * @covers ::detectProject()
     */
    public function testInvoke($expected, $data): void
    {
        $request = ServerRequestFactory::fromGlobals();
        $request->getSession()->write($data);
        foreach ($data as $key => $value) {
            $request = $request->withData($key, $value);
        }
        $app = new Application(CONFIG);
        $middleware = new ProjectMiddleware($app, TESTS . 'files' . DS . 'projects' . DS);
        $runner = new Runner();
        $runner->run(new MiddlewareQueue([$middleware]), $request);

        $project = Configure::read('Project');
        static::assertEquals($expected, $project);
        Configure::write('Project', null);
    }
}
