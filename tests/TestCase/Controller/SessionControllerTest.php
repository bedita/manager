<?php
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
namespace App\Test\TestCase\Controller;

use App\Controller\SessionController;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\SessionController} test case.
 */
#[CoversClass(SessionController::class)]
#[CoversMethod(SessionController::class, 'delete')]
#[CoversMethod(SessionController::class, 'save')]
#[CoversMethod(SessionController::class, 'view')]
class SessionControllerTest extends TestCase
{
    /**
     * Test `view` method.
     *
     * @return void
     */
    public function testView(): void
    {
        $session = new Session();
        $session->write('test', 'tost');
        $controller = new SessionController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'url' => '/session/test',
                'session' => $session,
            ])
        );
        $controller->view('test');
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals([
            'value' => 'tost',
        ], $controller->viewBuilder()->getVars());

        $controller->setRequest(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'url' => '/session/tast',
                'session' => $session,
            ])
        );
        $controller->view('tast');
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals([
            'value' => null,
        ], $controller->viewBuilder()->getVars());
    }

    /**
     * Test `save` method.
     *
     * @return void
     */
    public function testSave(): void
    {
        $session = new Session();
        $controller = new SessionController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'post' => [
                    'name' => 'test',
                    'value' => 'tost',
                ],
                'url' => '/session',
                'session' => $session,
            ])
        );
        $controller->save();
        static::assertEquals('tost', $session->read('test'));
        static::assertEquals(201, $controller->getResponse()->getStatusCode());
        static::assertEquals([
            'name' => 'test',
            'value' => 'tost',
        ], $controller->viewBuilder()->getVars());

        $controller->setRequest(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'post' => [
                    'name' => 'test',
                    'value' => 'tast',
                ],
                'url' => '/session',
                'session' => $session,
            ])
        );
        $controller->save();
        static::assertEquals('tast', $session->read('test'));
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals([
            'name' => 'test',
            'value' => 'tast',
        ], $controller->viewBuilder()->getVars());
    }

    /**
     * Test `delete` method.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $session = new Session();
        $session->write('test', 'tost');
        $controller = new SessionController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'DELETE',
                ],
                'url' => '/session/test',
                'session' => $session,
            ])
        );
        $controller->delete('test');
        static::assertNull($session->read('test'));
        static::assertEquals(204, $controller->getResponse()->getStatusCode());

        $controller->setRequest(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'DELETE',
                ],
                'url' => '/session/tast',
                'session' => $session,
            ])
        );
        $controller->delete('tast');
        static::assertNull($session->read('tast'));
        static::assertEquals(204, $controller->getResponse()->getStatusCode());
    }
}
