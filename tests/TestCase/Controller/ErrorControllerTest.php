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
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\ErrorController} Test Case
 *
 * @coversDefaultClass \App\Controller\ErrorController
 * @uses \App\Controller\ErrorController
 */
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
        $this->ErrorController = new ErrorController();
    }

    /**
     * test `initialize` function
     *
     * @covers ::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $this->setupController();

        static::assertNotEmpty($this->ErrorController->{'RequestHandler'});
    }
}
