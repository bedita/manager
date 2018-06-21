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

namespace App\Test\TestCase\Core;

use Cake\TestSuite\TestCase;
use OpenCorporation\Core\Countries;

/**
 * {@see \App\Core\Countries} Test Case
 *
 * @coversDefaultClass \App\Core\Countries
 */
class CountriesTest extends TestCase
{
    /**
     * Data provider for `testList` test case.
     *
     * @return array
     */
    public function listProvider() : array
    {
        return [
            'alpha2' => [
                'alpha2',
                249,
            ],
            'alpha3' => [
                'alpha3',
                249,
            ],
            'numeric' => [
                'numeric',
                249,
            ],
            'name' => [
                'name',
                249,
            ],
        ];
    }

    /**
     * Test `list` method
     *
     * @param string $code The code, can be alpha2, alpha3, numeric, name
     * @param int $size Expected number of countries
     * @return void
     * @dataProvider listProvider()
     * @covers ::list()
     */
    public function testList($code, $size) : void
    {
        $countries = new Countries();
        $result = $countries->list($code);
        static::assertNotEmpty($result);
        static::assertEquals(count($result), $size);
    }
}
