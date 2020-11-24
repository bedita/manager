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

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\PropertyHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\PropertyHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\PropertyHelper
 */
class PropertyHelperTest extends TestCase
{

    /**
     * Data provider for `testView` test case.
     *
     * @return array
     */
    public function viewProvider(): array
    {
        return [
            'text' => [
                'title',
                'something',
                [
                    'oneOf' => [
                        ['type' => null],
                        ['type' => 'string', 'contentMediaType' => 'text/html'],
                    ],
                    '$id' => '/properties/title',
                    'title' => 'Title',
                    'description' => null,
                ],
                [],
                '<div class="input title text"><label for="title">Title</label><input type="text" name="title" class="title" id="title" value="something"/></div>',
            ],
            'date' => [
                'created',
                '2020-06-15 12:35:00',
                [
                    'type' => 'string',
                    'format' => 'date-time',
                    '$id' => '/properties/created',
                    'title' => 'Created',
                    'description' => 'creation date',
                    'readonly' => true,
                ],
                [],
                '<div class="input datepicker text"><label for="created">Created</label><input type="text" name="created" v-datepicker="true" date="true" time="true" id="created" value="2020-06-15 12:35:00"/></div>',
            ],
            'status' => [
                'status',
                'en',
                [
                    'type' => 'string',
                    'enum' => [
                        'on',
                        'off',
                        'draft',
                    ],
                    '$id' => '/properties/status',
                    'title' => 'Status',
                    'description' => 'object status: on, draft, off',
                    'default' => 'draft',
                ],
                ['class' => 'test'],
                '<div class="input radio"><label>Status</label><input type="hidden" name="status" value=""/><label for="status-on"><input type="radio" name="status" value="on" id="status-on" class="test">On</label><label for="status-draft"><input type="radio" name="status" value="draft" id="status-draft" class="test">Draft</label><label for="status-off"><input type="radio" name="status" value="off" id="status-off" class="test">Off</label></div>',
            ],
        ];
    }

    /**
     * Test `view`
     *
     * @param string $key The key
     * @param mixed|null $value The value
     * @param array $schema The schema
     * @param array $options The options
     * @param string $expected The expected result
     * @return void
     *
     * @dataProvider viewProvider()
     * @covers ::view()
     */
    public function testView(string $key, $value, array $schema, array $options = [], string $expected): void
    {
        $property = new PropertyHelper(new View(null, null, null, []));
        $actual = $property->view($key, $value, $schema, $options);
        static::assertEquals($expected, $actual);
    }
}
