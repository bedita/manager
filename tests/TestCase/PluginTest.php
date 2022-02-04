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
namespace App\Test\TestCase;

use App\Plugin;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * \App\Plugin Test Case
 *
 * @coversDefaultClass \App\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Test loaded app plugins
     *
     * @return void
     *
     * @covers ::loadedAppPlugins()
     */
    public function testLoadedAppPlugins(): void
    {
        $expected = Configure::read('Plugins', []);
        $expected = array_keys($expected) + ['IdeHelper'];
        sort($expected);
        $loaded = Plugin::loadedAppPlugins();
        static::assertEquals($expected, $loaded);
    }
}
