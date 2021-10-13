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

use App\Utility\Schema;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\Schema Test Case
 *
 * @coversDefaultClass App\Utility\Schema
 */
class SchemaTest extends TestCase
{
    /**
     * Test `rightTypes` method
     *
     * @return void
     * @covers ::rightTypes()
     */
    public function testRightTypes(): void
    {
        $schema = [
            'dummy_relation_1' => [
                'right' => [
                    'dummy_type_1',
                ],
            ],
            'dummy_relation_2' => [
                'right' => [
                    'dummy_type_2',
                    'dummy_type_3',
                ],
            ],
            'dummy_relation_3' => [
                'right' => [
                    'dummy_type_1',
                    'dummy_type_4',
                    'dummy_type_5',
                ],
            ],
        ];
        $expected = [
            'dummy_type_1',
            'dummy_type_2',
            'dummy_type_3',
            'dummy_type_4',
            'dummy_type_5',
        ];
        $actual = Schema::rightTypes($schema);
        static::assertEquals($expected, $actual);
    }
}
