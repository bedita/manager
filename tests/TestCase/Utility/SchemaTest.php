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
     * Test `objectTypesFromRelations` method.
     *
     * @return void
     * @covers ::objectTypesFromRelations()
     */
    public function testObjectTypesFromRelations(): void
    {
        $relationsSchema = [
            'dummy_1' => [
                'left' => ['lions', 'dogs'],
                'right' => ['cats', 'ants'],
            ],
            'dummy_2' => [
                'left' => ['lions', 'horses'],
                'right' => ['cats', 'dogs'],
            ],
            'dummy_3' => [
                'left' => ['lions', 'horses'],
                'right' => ['cats', 'dogs'],
            ],
            'dummy_4' => [
                'left' => ['lions', 'horses'],
                'right' => ['cats', 'dogs'],
            ],
            'dummy_5' => [
                'left' => ['lions', 'horses'],
                'right' => ['cats', 'dogs'],
            ],
        ];
        $actual = Schema::objectTypesFromRelations($relationsSchema, 'left');
        $expected = ['dogs', 'horses', 'lions'];
        $this->assertEquals($expected, $actual);
        $actual = Schema::objectTypesFromRelations($relationsSchema, 'right');
        $expected = ['ants', 'cats', 'dogs'];
        $this->assertEquals($expected, $actual);
    }
}
