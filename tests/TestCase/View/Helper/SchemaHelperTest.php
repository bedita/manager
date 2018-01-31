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
    public function setUp()
    {
        parent::setUp();

        $view = new View();
        $this->Schema = new SchemaHelper($view);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Schema);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetControlTypeFromSchema` test case.
     *
     * @return array
     */
    public function getControlTypeFromSchemaProvider()
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
                'datetime',
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
    public function testGetControlTypeFromSchema($expected, $schema)
    {
        $actual = $this->Schema->getControlTypeFromSchema($schema);

        static::assertSame($expected, $actual);
    }
}
