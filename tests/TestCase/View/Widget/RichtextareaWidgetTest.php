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

namespace App\Test\TestCase\View;

use App\View\AppView;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\View\Widget\RichtestareaWidget} Test Case
 *
 * @coversDefaultClass \App\View\Widget\RichtestareaWidget
 */
class AppViewTest extends TestCase
{
    /**
     * Test `render`.
     *
     * @return void
     * @covers ::render()
     */
    public function testRender(): void
    {
        $View = new AppView();
        $actual = $View->Form->control('test', ['type' => 'richtext']);
        $expected = '"<div class="input richtext "><label for="test">Test</label><div name="test" contenteditable type="richtext" id="test"></div></div>"';
        static::assertSame($expected, $actual);
    }
}
