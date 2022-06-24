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
namespace App\Test\TestCase\Controller\Component;

use App\Controller\AppController;
use App\Controller\Component\FlashComponent;
use BEdita\SDK\BEditaClientException;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\FlashComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\FlashComponent
 */
class FlashComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\FlashComponent
     */
    public $Flash;

    /**
     * Test controller
     *
     * @var \App\Controller\AppController
     */
    public $controller;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->controller = new AppController();
        $registry = $this->controller->components();
        /** @var \App\Controller\Component\FlashComponent $component */
        $component = $registry->load(FlashComponent::class);
        $this->Flash = $component;

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Flash);

        parent::tearDown();
    }

    /**
     * Test `set()` method.
     *
     * @return void
     * @covers ::set()
     */
    public function testSet(): void
    {
        // message without params
        $expected = 'debug';
        $this->Flash->set($expected, []);
        $flash = $this->controller->getRequest()->getSession()->read('Flash');
        static::assertEquals($expected, $flash['flash'][0]['message']);

        // message with params exception
        $expected = 'debug';
        $expectedExceptionMessage = 'something went wrong';
        $this->Flash->set('debug', ['params' => new BEditaClientException($expectedExceptionMessage)]);
        $flash = $this->controller->getRequest()->getSession()->read('Flash');
        static::assertEquals($expected, $flash['flash'][0]['message']);
        static::assertEquals($expectedExceptionMessage, $flash['flash'][0]['params']['title']);
        static::assertEquals(503, $flash['flash'][0]['params']['status']);
    }
}
