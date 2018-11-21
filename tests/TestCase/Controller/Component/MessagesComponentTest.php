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

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\MessagesComponent;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\MessagesComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\MessagesComponent
 */
class MessagesComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\MessagesComponent
     */
    public $Messages;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $this->Messages = $registry->load(MessagesComponent::class);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() : void
    {
        unset($this->Messages);
        parent::tearDown();
    }

    /**
     * Test `customize()` method.
     *
     * @covers ::customize()
     */
    public function testCustomize() : void
    {
        // test customize a message not present in Message::MESSAGES_MAP
        $expected = $message = 'whatever';
        $_SESSION['Flash']['flash'] = [compact('message')];
        $this->Messages->customize();
        $actual = $_SESSION['Flash']['flash'][0]['message'];
        static::assertEquals($expected, $actual);

        // test customize a message present in Message::MESSAGES_MAP
        $message = 'You are not authorized to access that location.';
        $_SESSION['Flash']['flash'] = [compact('message')];
        $this->Messages->customize();
        $actual = $_SESSION['Flash']['flash'][0]['message'];
        $expected = $this->Messages::MESSAGES_MAP[$message];
        static::assertEquals($expected, $actual);
    }
}
