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

use App\Form\Control;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Form\Control} Test Case
 *
 * @coversDefaultClass \App\Form\Control
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
            'checkbox true' => [
                [
                    'type' => 'array',
                ],
                'checkbox',
                'true',
                [
                    'type' => 'checkbox',
                    'checked' => true,
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
            'checkbox no options' => [
                [
                    'type' => 'array',
                    'oneOf' => [
                        [],
                    ],
                ],
                'checkbox',
                'false',
                [
                    'type' => 'checkbox',
                    'checked' => false,
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
                        ['text' => 'Good', 'value' => 'good'],
                        ['text' => 'Bad', 'value' => 'bad'],
                    ],
                    'value' => 'good',
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
                        ['text' => '', 'value' => ''],
                        ['text' => 'Good', 'value' => 'good'],
                        ['text' => 'Bad', 'value' => 'bad'],
                    ],
                    'value' => 'good',
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
            'email' => [
                [
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'string',
                            'format' => 'email',
                        ],
                    ],
                ],
                'text',
                'gustavo@support.com',
                [
                    'type' => 'text',
                    'v-email' => 'true',
                    'class' => 'email',
                    'value' => 'gustavo@support.com',
                ],
            ],
            'uri' => [
                [
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'string',
                            'format' => 'uri',
                        ],
                    ],
                ],
                'text',
                'www.gustavosupport.com',
                [
                    'type' => 'text',
                    'v-uri' => 'true',
                    'class' => 'uri',
                    'value' => 'www.gustavosupport.com',
                ],
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
     * @dataProvider controlProvider()
     * @covers ::control()
     * @covers ::json()
     * @covers ::richtext()
     * @covers ::plaintext()
     * @covers ::datetime()
     * @covers ::date()
     * @covers ::checkbox()
     * @covers ::enum()
     * @covers ::categories()
     * @covers ::oneOptions()
     * @covers ::email()
     * @covers ::uri()
     */
    public function testControl(array $schema, string $type, $value, array $expected): void
    {
        $options = [
            'objectType' => 'documents',
            'property' => 'dummy',
            'value' => $value,
            'schema' => (array)$schema,
            'propertyType' => $type,
        ];
        $actual = Control::control($options);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testFormat` test case.
     *
     * @return array
     */
    public function formatProvider(): array
    {
        return [
            'empty schema' => [
                [],
                '',
            ],
            'no format oneOf' => [
                [
                    'oneOf' => [
                        ['type' => null],
                        ['type' => 'text'],
                    ],
                ],
                '',
            ],
            'email' => [
                [
                    'oneOf' => [
                        [],
                        ['format' => 'email'],
                    ],
                ],
                'email',
            ],
            'uri' => [
                [
                    'oneOf' => [
                        [],
                        ['format' => 'uri'],
                    ],
                ],
                'uri',
            ],
            'other' => [
                [
                    'oneOf' => [
                        [],
                        ['format' => 'whatever'],
                    ],
                ],
                'whatever',
            ],
        ];
    }

    /**
     * Test `format` method
     *
     * @param array $schema Object schema array.
     * @param string $expected The expected format.
     * @return void
     * @dataProvider formatProvider()
     * @covers ::format()
     */
    public function testFormat(array $schema, string $expected): void
    {
        $actual = Control::format($schema);

        static::assertSame($expected, $actual);
    }

    /**
     * Provider for testLabel
     *
     * @return array
     */
    public function labelProvider(): array
    {
        return [
            'no custom config' => [
                'dummies',
                'status',
                'on',
                null,
                'On',
            ],
            'other value not in config' => [
                'dummies',
                'status',
                'other',
                [
                    'on' => 'OOONNN',
                    'off' => 'OOOFFF',
                    'draft' => 'DDDRRRAAAFFFTTT',
                ],
                'Other',
            ],
            'custom config property value' => [
                'dummies',
                'status',
                'draft',
                [
                    'on' => 'OOONNN',
                    'off' => 'OOOFFF',
                    'draft' => 'DDDRRRAAAFFFTTT',
                ],
                'DDDRRRAAAFFFTTT',
            ],
        ];
    }

    /**
     * Test `label`
     *
     * @param string $type The type
     * @param string $property The property
     * @param mixed|null $customConfig The custom configuration
     * @param string $expected The expected label
     * @return void
     * @dataProvider labelProvider()
     * @covers ::label()
     */
    public function testLabel(string $type, string $property, string $value, $customConfig, string $expected): void
    {
        if (!empty($customConfig)) {
            Configure::write(
                sprintf('Properties.%s', $type),
                array_merge(
                    (array)\Cake\Core\Configure::read(sprintf('Properties.%s', $type)),
                    ['labels' => ['options' => [$property => $customConfig]]]
                )
            );
        }
        $actual = Control::label($type, $property, $value);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `oneOptions`
     *
     * @return void
     * @dataProvider labelProvider()
     * @covers ::oneOptions()
     */
    public function testOneOptions(): void
    {
        // empty one
        $expected = [];
        $actual = [];
        Control::oneOptions($actual, []);
        static::assertSame($expected, $actual);

        // empty one
        $expected = [
            ['value' => 'on', 'text' => 'On'],
            ['value' => 'off', 'text' => 'Off'],
            ['value' => 'draft', 'text' => 'Draft'],
        ];
        $actual = [];
        $one = ['type' => 'array', 'items' => ['enum' => ['on', 'off', 'draft']]];
        Control::oneOptions($actual, $one);
        static::assertSame($expected, $actual);
    }
}
