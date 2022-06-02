<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Form;

use App\Form\ControlType;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Form\ControlType} Test Case
 *
 * @coversDefaultClass \App\Form\ControlType
 */
class ControlTypeTest extends TestCase
{
    /**
     * Data provider for `testFromSchema` test case.
     *
     * @return array
     */
    public function fromSchemaProvider(): array
    {
        return [
            'string' => [
                'text',
                [
                    'type' => 'string',
                ],
            ],
            'html' => [
                'richtext',
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
            ],
            'txt' => [
                'plaintext',
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/plain',
                ],
            ],
            'date-time' => [
                'date-time',
                [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
            ],
            'date' => [
                'date',
                [
                    'type' => 'string',
                    'format' => 'date',
                ],
            ],
            'integer' => [
                'number',
                [
                    'type' => 'integer',
                ],
            ],
            'number' => [
                'number',
                [
                    'type' => 'number',
                ],
            ],
            'checkbox' => [
                'checkbox',
                [
                    'type' => 'boolean',
                ],
            ],
            'one of' => [
                'checkbox',
                [
                    'type' => 'string',
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'boolean',
                        ],
                    ],
                ],
            ],
            'not an array' => [
                'text',
                false,
            ],
            'no type' => [
                'text',
                [
                    'const' => 13,
                ],
            ],
            'json' => [
                'json',
                [
                    'type' => 'object',
                ],
            ],
            'unknown' => [
                'text',
                [
                    'type' => 'unknown',
                ],
            ],
            'enum' => [
                'enum',
                [
                    'type' => 'string',
                    'enum' => ['a', 'b', 'c'],
                ],
            ],
            'array' => [
                'checkbox',
                [
                    'type' => 'array',
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'array',
                            'uniqueItems' => true,
                            'items' => [
                                'type' => 'string',
                                'enum' => ['a', 'b', 'c', 'd'],
                            ],
                        ],
                    ],
                ],
            ],
            'categories' => [
                'categories',
                [
                    'type' => 'categories',
                ],
            ],
        ];
    }

    /**
     * Test `fromSchema()` method.
     *
     * @param string $expected Expected result.
     * @param array|bool $schema Schema.
     * @return void
     * @dataProvider fromSchemaProvider()
     * @covers ::fromSchema()
     * @covers ::fromString()
     * @covers ::fromNumber()
     * @covers ::fromInteger()
     * @covers ::fromBoolean()
     * @covers ::fromArray()
     * @covers ::fromObject()
     */
    public function testFromSchema(string $expected, $schema): void
    {
        $actual = ControlType::fromSchema($schema);

        static::assertSame($expected, $actual);
    }
}
