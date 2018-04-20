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

use App\View\Helper\SchemaHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\SchemaHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\SchemaHelper
 */
class SchemaHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\View\Helper\SchemaHelper
     */
    public $Schema;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $view = new View();
        $this->Schema = new SchemaHelper($view);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() : void
    {
        unset($this->Schema);

        parent::tearDown();
    }

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
            'date' => [
                'text', // TODO: replace with "datetime".
                [
                    'type' => 'text',
                        'v-datepicker' => '',
                        'time' => 'true',
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
        $actual = $this->Schema->controlTypeFromSchema($schema);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testControlOptions` test case.
     *
     * @return array
     */
    public function controlOptionsSchemaProvider() : array
    {
        return [
            'text' => [
                // expected result
                [
                    'type' => 'text',
                    'value' => 'test',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'name',
                'test',
            ],
            'status' => [
                // expected result
                [
                    'type' => 'radio',
                    'options' => [
                        ['value' => 'on', 'text' => __('On')],
                        ['value' => 'draft', 'text' => __('Draft')],
                        ['value' => 'off', 'text' => __('Off')],
                    ],
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'status',
                'on'
            ],
            'password' => [
                // expected result
                [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'password',
                ''
            ],
            'confirm-password' => [
                // expected result
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
                // schema type
                [
                    'type' => 'string',
                ],
                'confirm-password',
                ''
            ],
            'json' => [
                // expected result
                [
                    'type' => 'textarea',
                    'v-jsoneditor' => '',
                    'class' => 'json',
                    'value' => json_encode('{ "example": { "this": "is", "an": "example" } }'),
                ],
                // schema type
                [
                    'type' => 'object',
                ],
                'extra',
                '{ "example": { "this": "is", "an": "example" } }',
            ],
            'title' => [
                // expected result
                [
                    'class' => 'title',
                    'type' => 'text',
                ],
                // schema type
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                'title',
                'test',
            ],
            'description' => [
                // expected result
                [
                    'type' => 'textarea',
                    'v-richeditor' => '',
                    'ckconfig' => 'configNormal',
                    'type' => 'textarea',
                ],
                // schema type
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                'description',
                'test',
            ],
            'body' => [
                // expected result
                [
                    'type' => 'textarea',
                    'v-richeditor' => '',
                    'ckconfig' => 'configNormal',
                ],
                // schema type
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                'body',
                'test',
            ],
        ];
    }

    /**
     * Test `controlOptions` method.
     *
     * @param string $expected Expected result.
     * @param array $type The JSON schema type
     * @param string $name The field name.
     * @param string $value The field value.
     * @return void
     *
     * @dataProvider controlOptionsSchemaProvider()
     * @covers ::controlOptions()
     * @covers ::customControlOptions()
     */
    public function testControlOptions(array $expected, $type, $name, $value) : void
    {
        $actual = $this->Schema->controlOptions($name, $value, $type);

        static::assertSame($expected, $actual);
    }
}
