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
     * Data provider for `testGetControlTypeFromSchema` test case.
     *
     * @return array
     */
    public function getControlTypeFromSchemaProvider() : array
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
                    'type' => 'string',
                    'format' => 'date-time',
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
            'unknown type' => [
                'text',
                [
                    'type' => 'object',
                ],
            ],
        ];
    }

    /**
     * Test `getControlTypeFromSchema()` method.
     *
     * @param string $expected Expected result.
     * @param array|bool $schema Schema.
     * @return void
     *
     * @dataProvider getControlTypeFromSchemaProvider()
     * @covers ::getControlTypeFromSchema()
     */
    public function testGetControlTypeFromSchema(string $expected, $schema) : void
    {
        $actual = $this->Schema->getControlTypeFromSchema($schema);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testGetControlOptionFromTypeAndName` test case.
     *
     * @return array
     */
    public function getControlOptionFromTypeAndNameSchemaProvider() : array
    {
        return [
            'text' => [
                // expected result
                [
                    'type' => 'text',
                ],
                // params (type, name, value)
                'text', 'test', '',
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
                // params (type, name, value)
                'radio', 'status', '',
            ],
            'password' => [
                // expected result
                [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                ],
                // params (type, name, value)
                'text', 'password', '',
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
                // params (type, name, value)
                'text', 'confirm-password', '',
            ],
            'json' => [
                // expected result
                [
                    'type' => 'textarea',
                    'class' => 'json',
                    'value' => json_encode('{ "example": { "this": "is", "an": "example" } }'),
                ],
                // params (type, name, value)
                'json', '', '{ "example": { "this": "is", "an": "example" } }',
            ],
        ];
    }

    /**
     * Test `getControlOptionFromTypeAndName()` method.
     *
     * @param string $expected Expected result.
     * @param string $type The type (i.e. 'radio', 'text', 'textarea', 'json', etc.)
     * @param string $name The field name.
     * @param string $value The field value.
     * @return void
     *
     * @dataProvider getControlOptionFromTypeAndNameSchemaProvider()
     * @covers ::getControlOptionFromTypeAndName()
     */
    public function testGetControlOptionFromTypeAndName(array $expected, $type, $name, $value) : void
    {
        $actual = $this->Schema->getControlOptionFromTypeAndName($type, $name, $value);

        static::assertSame($expected, $actual);
    }
}
