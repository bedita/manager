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

namespace App\Test\TestCase\View;

use App\View\AppView;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\View\AppView} Test Case
 *
 * @coversDefaultClass \App\View\AppView
 */
class AppViewTest extends TestCase
{
    /**
     * Test `initialize` method
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $View = new AppView();
        $extensions = $View->getTwig()->getExtensions();
        static::assertNotEmpty($extensions);
        static::assertArrayHasKey('BEdita\WebTools\View\Twig\BeditaTwigExtension', $extensions);
    }

    /**
     * Test `_getElementFileName` method
     *
     * @return void
     * @covers ::_getElementFileName()
     */
    public function testCustomElement(): void
    {
        $View = new AppView();
        $View->set('currentModule', ['name' => 'cats']);
        $result = $View->elementExists('Form/meta');
        static::assertTrue($result);

        $custom = [
            'cats' => [
                'Form/meta' => 'MyPlugin',
            ],
        ];
        Configure::write('Elements', $custom);
        $result = $View->elementExists('Form/meta');
        static::assertFalse($result);
    }
}
