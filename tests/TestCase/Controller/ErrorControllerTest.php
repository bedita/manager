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

namespace App\Test\TestCase\Controller;

use App\Controller\ErrorController;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\ErrorController} Test Case
 *
 * @uses \App\Controller\ErrorController
 */
#[CoversClass(ErrorController::class)]
#[CoversMethod('beforeFilter')]
#[CoversMethod('beforeRender')]
class ErrorControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\ErrorController
     */
    protected $ErrorController;

    /**
     * Setup controller to test with request config
     *
     * @return void
     */
    protected function setupController(): void
    {
        $this->ErrorController = new ErrorController(new ServerRequest());
    }

    /**
     * Test `beforeFiler` method
     *
     * @return void
     */
    public function testBeforeFilter(): void
    {
        $this->setupController();
        $event = new Event('Controller.beforeFilter');
        $this->assertNull($this->ErrorController->beforeFilter($event));
    }

    /**
     * Test `beforeRender` method
     *
     * @return void
     */
    public function testBeforeRender(): void
    {
        $this->setupController();
        $event = new Event('Controller.beforeRender');
        $this->assertNull($this->ErrorController->beforeRender($event));
        $this->assertSame('App\View\AppView', $this->ErrorController->viewBuilder()->getClassName());
        $this->assertSame('Error', $this->ErrorController->viewBuilder()->getTemplatePath());
    }
}
