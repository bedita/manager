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

namespace App\Test\TestCase\Core\Utility\Form;

use App\Core\Utility\Form\Control;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Core\Utility\Form\Control} Test Case
 *
 * @coversDefaultClass \App\Core\Utility\Form\Control
 */
class ControlTest extends TestCase
{
    /**
     * Data provider for `testControl` test case.
     *
     * @return array
     */
    public function controlProvider(): array
    {
        $value = 'something';

        return [
            'other' => [
                [], // schema
                'other', // type
                $value, // value
                ['type' => 'other'] + compact('value'), // expected
            ],
            'json' => [
                [],
                'json',
                $value,
                [
                    'type' => 'textarea',
                    'v-jsoneditor' => 'true',
                    'class' => 'json',
                    'value' => json_encode($value),
                ],
            ],
            'richtext' => [
                [],
                'richtext',
                $value,
                [
                    'type' => 'textarea',
                    'v-richeditor' => '""',
                    'value' => $value,
                ],
            ],
            'plaintext' => [
                [],
                'plaintext',
                $value,
                [
                    'type' => 'textarea',
                    'value' => $value,
                ],
            ],
            'date-time' => [
                [],
                'date-time',
                '2018-01-31 08:45:00',
                [
                    'type' => 'text',
                    'v-datepicker' => 'true',
                    'date' => 'true',
                    'time' => 'true',
                    'value' => '2018-01-31 08:45:00',
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                ],
            ],
            'date' => [
                [],
                'date',
                '2018-01-31',
                [
                    'type' => 'text',
                    'v-datepicker' => 'true',
                    'date' => 'true',
                    'value' => '2018-01-31',
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                ],
            ],
            'checkbox' => [
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
                'checkbox',
                null,
                [
                    'type' => 'select',
                    'options' => [
                        [
                            'value' => 'a',
                            'text' => 'A',
                        ],
                        [
                            'value' => 'b',
                            'text' => 'B',
                        ],
                        [
                            'value' => 'c',
                            'text' => 'C',
                        ],
                        [
                            'value' => 'd',
                            'text' => 'D',
                        ],
                    ],
                    'multiple' => 'checkbox',
                ],
            ],
            'enum' => [
                [
                    'type' => 'string',
                    'enum' => [
                        'good',
                        'bad',
                    ],
                ],
                'enum',
                'good',
                [
                    'type' => 'select',
                    'options' => [
                        ['value' => 'good', 'text' => 'Good'],
                        ['value' => 'bad', 'text' => 'Bad'],
                    ],
                ],
            ],
            'enum nullable' => [
                [
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'string',
                            'enum' => [
                                'good',
                                'bad',
                            ],
                        ],
                    ],
                ],
                'enum',
                'good',
                [
                    'type' => 'select',
                    'options' => [
                        ['value' => '', 'text' => ''],
                        ['value' => 'good', 'text' => 'Good'],
                        ['value' => 'bad', 'text' => 'Bad'],
                    ],
                ],
            ],
            'categories' => [
                [
                    'categories' => [
                        ['name' => 'animals', 'label' => 'Animals'],
                        ['name' => 'furnitures', 'label' => 'Furnitures'],
                        ['name' => 'houses', 'label' => 'Houses'],
                    ],
                ], // schema
                'categories', // type
                [['name' => 'animals'], ['name' => 'houses']], // value
                [
                    'type' => 'select',
                    'options' => [
                        ['value' => 'animals', 'text' => 'Animals'],
                        ['value' => 'furnitures', 'text' => 'Furnitures'],
                        ['value' => 'houses', 'text' => 'Houses'],
                    ],
                    'multiple' => 'checkbox',
                    'value' => ['animals', 'houses'],
                ], // expected
            ],
        ];
    }

    /**
     * Test `control` method
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param mixed|null $value Property value.
     * @param array $expected The expected control.
     * @return void
     *
     * @dataProvider controlProvider()
     * @covers ::control
     * @covers ::json
     * @covers ::richtext
     * @covers ::plaintext
     * @covers ::datetime
     * @covers ::date
     * @covers ::checkbox
     * @covers ::enum
     * @covers ::categories
     */
    public function testControl(array $schema, string $type, $value, array $expected): void
    {
        $actual = Control::control($schema, $type, $value);

        static::assertSame($expected, $actual);
    }
}
