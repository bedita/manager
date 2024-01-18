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
        /** @phpstan-ignore-next-line */
        $actual = Schema::rightTypes($schema);
        static::assertEquals($expected, $actual);
    }

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
