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

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\LinkHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\Viinklper\LinkHelper} Test Case
 *
 * @coversDefaulinks \App\View\Helper\LinkHelper
 */
class LinkHelperTest extends TestCase
{
    /**
     * Data provider for `testObjectNav` test case.
     *
     * @return array
     */
    public function objectNavProvider() : array
    {
        return [
            'empty' => [
                [], // $data
                '<div class="listobjnav">‹›<div>0 / 0</div></div>', // $expected
            ],
            'only next' => [
                [
                    'next' => 2,
                    'index' => 1,
                    'total' => 10,
                ], // $data
                '<div class="listobjnav">‹<a href="/view/2?title=next">›</a><div>1 / 10</div></div>', // $expected
            ],
            'only prev' => [
                [
                    'prev' => 9,
                    'index' => 10,
                    'total' => 10,
                ], // $data
                '<div class="listobjnav"><a href="/view/9?title=prev">‹</a>›<div>10 / 10</div></div>', // $expected
            ],
            'full' => [
                [
                    'prev' => 4,
                    'next' => 6,
                    'index' => 5,
                    'total' => 10,
                ], // $data
                '<div class="listobjnav"><a href="/view/4?title=prev">‹</a><a href="/view/6?title=next">›</a><div>5 / 10</div></div>', // $expected
            ],
        ];
    }

    /**
     * Test `objectNav`
     *
     * @dataProvider objectNavProvider()
     * @covers ::objectNav()
     */
    public function testObjectNav($data, $expected) : void
    {
        $request = $response = $events = null;
        $layout = new LinkHelper(new View($request, $response, $events, $data));
        $result = $layout->objectNav($data);
        static::assertSame($result, $expected);
    }
}
