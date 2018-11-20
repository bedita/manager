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

namespace App\Test\TestCase\Core\Utility;

use App\Core\Utility\Form;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\Core\Utility\Form} Test Case
 *
 * @coversDefaultClass \App\Core\Utility\Form
 */
class FormTest extends TestCase
{
    /**
     * Data provider for `testControlTypeFromSchema` test case.
     *
     * @return array
     */
    public function controlTypeFromSchemaProvider() : array
    {
        return [
            'string' => [
                'text',
                [
                    'type' => 'string',
                ],
            ],
            'html' => [
                'textarea',
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
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
            'number' => [
                'number',
                [
                    'type' => 'integer',
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
        ];
    }

    /**
     * Test `controlTypeFromSchema()` method.
     *
     * @param string $expected Expected result.
     * @param array|bool $schema Schema.
     * @return void
     *
     * @dataProvider controlTypeFromSchemaProvider()
     * @covers ::controlTypeFromSchema()
     */
    public function testControlTypeFromSchema(string $expected, $schema) : void
    {
        $actual = Form::controlTypeFromSchema($schema);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testCustomControlOptions` test case.
     * Custom control types (@see Form::customControls):
     *
     *     'lang', 'status', 'old_password', 'password', 'confirm-password', 'title'
     *
     * @return array
     */
    public function customControlOptionsProvider() : array
    {
        return [
            'not custom' => [
                'name',
                [],
            ],
            'lang' => [
                'lang',
                [
                    'type' => 'text',
                ],
            ],
            'status' => [
                'status',
                [
                    'type' => 'radio',
                    'options' => [
                        ['value' => 'on', 'text' => __('On')],
                        ['value' => 'draft', 'text' => __('Draft')],
                        ['value' => 'off', 'text' => __('Off')],
                    ],
                    'templateVars' => [
                        'containerClass' => 'status',
                    ],
                ],
            ],
            'password' => [
                'password',
                [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                ],
            ],
            'confirm-password' => [
                'confirm-password',
                [
                    'label' => __('Retype password'),
                    'id' => 'confirm_password',
                    'name' => 'confirm-password',
                    'class' => 'confirm-password',
                    'placeholder' => __('confirm password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                    'type' => 'password',
                ],
            ],
            'title' => [
                'title',
                [
                    'class' => 'title',
                    'type' => 'text',
                ],
            ],
        ];
    }

    /**
     * Test `customControlOptions` method.
     *
     * @param string $name The field name.
     * @param array $expected Expected result.
     * @return void
     *
     * @dataProvider customControlOptionsProvider()
     * @covers ::customControlOptions()
     * @covers ::langOptions
     * @covers ::statusOptions
     * @covers ::oldpasswordOptions
     * @covers ::passwordOptions
     * @covers ::confirmpasswordOptions
     * @covers ::titleOptions
     * @covers ::typeFromString
     * @covers ::typeFromNumber
     * @covers ::typeFromInteger
     * @covers ::typeFromBoolean
     * @covers ::typeFromArray
     * @covers ::typeFromObject
     */
    public function testCustomControlOptions(string $name, array $expected) : void
    {
        $actual = Form::customControlOptions($name);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testControl` test case.
     *
     * @return array
     */
    public function controlProvider() : array
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
                    'v-jsoneditor' => '',
                    'class' => 'json',
                    'value' => json_encode($value),
                ],
            ],
            'textarea' => [
                [],
                'textarea',
                $value,
                [
                    'type' => 'textarea',
                    'v-richeditor' => '',
                    'ckconfig' => 'configNormal',
                    'value' => $value,
                ],
            ],
            'date-time' => [
                [],
                'date-time',
                '2018-01-31 08:45:00',
                [
                    'type' => 'text',
                    'v-datepicker' => '',
                    'time' => 'true',
                    'value' => '2018-01-31 08:45:00',
                ],
            ],
            'date' => [
                [],
                'date',
                '2018-01-31',
                [
                    'type' => 'text',
                    'v-datepicker' => '',
                    'time' => 'false',
                    'value' => '2018-01-31',
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
        ];
    }

    /**
     * Test `control` method
     *
     * @param array $schema Object schema array.
     * @param string $type Control type.
     * @param string|null $value Property value.
     * @param array $expected The expected control.
     * @return void
     *
     * @dataProvider controlProvider()
     * @covers ::control
     * @covers ::jsonControl
     * @covers ::textareaControl
     * @covers ::datetimeControl
     * @covers ::dateControl
     * @covers ::checkboxControl
     * @covers ::enumControl
     */
    public function testControl(array $schema, string $type, ?string $value, array $expected) : void
    {
        $actual = Form::control($schema, $type, $value);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testGetMethod` test case.
     *
     * @return array
     */
    public function getMethodProvider() : array
    {
        return [
            'name with chars to remove' => [
                'old_passwordOptions',
                [Form::class, 'oldPasswordOptions'],
            ],
            'name with chars to remove' => [
                'confirm_passwordOptions',
                [Form::class, 'confirmPasswordOptions'],
            ],
        ];
    }

    /**
     * Test `getMethod` method
     *
     * @param string $methodName The method name
     * @param array $expected The expected method array
     * @return void
     *
     * @dataProvider getMethodProvider()
     * @covers ::getMethod
     */
    public function testGetMethod(string $methodName, array $expected) : void
    {
        $actual = Form::getMethod($methodName);

        static::assertSame($expected, $actual);
    }

    /**
     * Test `getMethod` method exception 'not callable'
     *
     * @return void
     *
     * @covers ::getMethod
     */
    public function testGetMethodNotCallable() : void
    {
        $methodName = 'dummy';
        $expected = new \InvalidArgumentException(sprintf('Method "%s" is not callable', $methodName));
        static::expectException(get_class($expected));
        static::expectExceptionMessage($expected->getMessage());
        Form::getMethod($methodName);
    }
}
