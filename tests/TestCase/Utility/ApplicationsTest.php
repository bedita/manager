<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase;

use App\Utility\Applications;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\Applications Test Case
 *
 * @coversDefaultClass App\Utility\Applications
 */
class ApplicationsTest extends TestCase
{
    /**
     * Test `list` method.
     * No applications, for test data.
     *
     * @return void
     * @covers ::list()
     */
    public function testList(): void
    {
        $expected = [];
        $actual = Applications::list();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getName` method
     * No applications, for test data.
     *
     * @return void
     * @covers ::getName()
     */
    public function testGetName(): void
    {
        $expected = "";
        $actual = Applications::getName('1');
        static::assertEquals($expected, $actual);
    }
}
