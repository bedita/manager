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

use App\Form\Control;
use App\View\Helper\PropertyHelper;
use Cake\Core\Configure;
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        Configure::write(
            'Control.handlers',
            array_merge(
                (array)\Cake\Core\Configure::read('Control.handlers'),
                [
                    'dummies' => [ // an object type
                        'descr' => [ // a field
                            'class' => 'App\Test\TestCase\View\Helper\PropertyHelperTest',
                            'method' => 'dummy',
                        ],
                    ],
                ]
            )
        );
    }

    /**
     * Data provider for `testControl` test case.
     *
     * @return array
     */
    public function controlProvider(): array
    {
        return [
            'text' => [
                'title',
                'something',
                [],
                [
                    'oneOf' => [
                        ['type' => null],
                        ['type' => 'string', 'contentMediaType' => 'text/html'],
                    ],
                    '$id' => '/properties/title',
                    'title' => 'Title',
                    'description' => null,
                ],
                '<div class="input title text"><label for="title">Title</label><input type="text" name="title" class="title" id="title" value="something"/></div>',
            ],
            'date' => [
                'created',
                '2020-06-15 12:35:00',
                [],
                [
                    'type' => 'string',
                    'format' => 'date-time',
                    '$id' => '/properties/created',
                    'title' => 'Created',
                    'description' => 'creation date',
                    'readonly' => true,
                ],
                '<div class="input datepicker text"><label for="created">Created</label><input type="text" name="created" v-datepicker="true" date="true" time="true" id="created" value="2020-06-15 12:35:00"/></div>',
            ],
            'status' => [
                'status',
                'en',
                ['class' => 'test'],
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
                '<div class="input radio"><label>Status</label><input type="hidden" name="status" id="status" value=""/><label for="status-on"><input type="radio" name="status" value="on" id="status-on" class="test">On</label><label for="status-draft"><input type="radio" name="status" value="draft" id="status-draft" class="test">Draft</label><label for="status-off"><input type="radio" name="status" value="off" id="status-off" class="test">Off</label></div>',
            ],
            'categories' => [
                'categories',
                [['name' => 'cat-1'], ['name' => 'cat-3']],
                ['class' => 'test'],
                [
                    ['name' => 'cat-1', 'label' => 'category 1'],
                    ['name' => 'cat-2', 'label' => 'category 2'],
                    ['name' => 'cat-3', 'label' => 'category 3'],
                    ['name' => 'cat-4', 'label' => 'category 4'],
                    ['name' => 'cat-5', 'label' => 'category 5'],
                ],
                '<div class="input select"><label for="categories">Categories</label><input type="hidden" name="categories" id="categories" value=""/><div class="checkbox"><label for="categories-cat-1" class="selected"><input type="checkbox" name="categories[]" value="cat-1" checked="checked" id="categories-cat-1" class="test">category 1</label></div><div class="checkbox"><label for="categories-cat-2"><input type="checkbox" name="categories[]" value="cat-2" id="categories-cat-2" class="test">category 2</label></div><div class="checkbox"><label for="categories-cat-3" class="selected"><input type="checkbox" name="categories[]" value="cat-3" checked="checked" id="categories-cat-3" class="test">category 3</label></div><div class="checkbox"><label for="categories-cat-4"><input type="checkbox" name="categories[]" value="cat-4" id="categories-cat-4" class="test">category 4</label></div><div class="checkbox"><label for="categories-cat-5"><input type="checkbox" name="categories[]" value="cat-5" id="categories-cat-5" class="test">category 5</label></div></div>',
            ],
            'object' => [
                'an object',
                ['an' => 'object'],
                [],
                [
                    'type' => 'object',
                    '$id' => '/properties/object',
                ],
                '<div class="input textarea"><label for="an-object">An Object</label><textarea name="an object" v-jsoneditor="true" class="json" id="an-object" rows="5">{&quot;an&quot;:&quot;object&quot;}</textarea></div>',
            ],
            'html' => [
                'descr',
                'something',
                [],
                [
                    'name' => 'descr',
                ],
                '<dummy>something</dummy>',
            ],
        ];
    }

    /**
     * Test `control`
     *
     * @param string $key The key
     * @param mixed|null $value The value
     * @param array $options The options
     * @param array $schema The schema
     * @param string $expected The expected result
     * @return void
     * @dataProvider controlProvider()
     * @covers ::control()
     * @covers ::schema()
     */
    public function testControl(string $key, $value, array $options = [], array $schema = [], string $expected = ''): void
    {
        $view = new View(null, null, null, []);
        if ($key === 'categories') {
            $schema = ['categories' => $schema];
        } else {
            $schema = ['properties' => [$key => $schema]];
        }
        $view->set('schema', $schema);
        $view->set('objectType', 'dummies');
        $property = new PropertyHelper($view);
        $actual = $property->control($key, $value, $options);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `control` with parameter type, for "other" types schema controls
     *
     * @return void
     * @covers ::control()
     * @covers ::schema()
     */
    public function testControlOtherType(): void
    {
        $key = 'title';
        $value = 'something';
        $options = [];
        $type = 'documents';
        $schema = [];
        $schemasByType = [
            'documents' => [
                'oneOf' => [
                    ['type' => null],
                    ['type' => 'string', 'contentMediaType' => 'text/html'],
                ],
                '$id' => '/properties/title',
                'title' => 'Title',
                'description' => null,
            ],
        ];
        $expected = '<div class="input title text"><label for="title">Title</label><input type="text" name="title" class="title" id="title" value="something"/></div>';
        $view = new View(null, null, null, []);
        $schema = ['properties' => [$key => $schema]];
        $view->set('schema', $schema);
        $view->set('schemasByType', $schemasByType);
        $view->set('objectType', 'dummies');
        $property = new PropertyHelper($view);
        $actual = $property->control($key, $value, $options, $type);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testValue` test case.
     *
     * @return array
     */
    public function valueProvider(): array
    {
        return [
            'object attribute' => [
                [
                    'attributes' => [
                        'dummy' => 'gustavo',
                    ],
                ], // object
                'dummy', // property
                'gustavo', // expected
            ],
            'object meta' => [
                [
                    'meta' => [
                        'dummy' => 'gustavo',
                    ],
                ], // object
                'dummy', // property
                'gustavo', // expected
            ],
            'file_name' => [
                [
                    'relationships' => [
                        'streams' => [
                            'data' => [
                                ['attributes' => ['file_name' => 'sample.txt']],
                            ],
                        ],
                    ],
                ], // object
                'file_name', // property
                'sample.txt', // expected
            ],
            'object id' => [
                [
                    'id' => '123',
                    'attributes' => [],
                ], // object
                'id', // property
                '123', // expected
            ],
            'not found' => [
                [
                    'attributes' => [
                        'dummy' => 'gustavo',
                    ],
                ], // object
                'dddummy', // property
                '', // expected
            ],

        ];
    }

    /**
     * Test `value`
     *
     * @param array $object The object
     * @param string $property The property
     * @param string $expected The expected value
     * @return void
     * @dataProvider valueProvider()
     * @covers ::value()
     */
    public function testValue(array $object, string $property, string $expected): void
    {
        $view = new View(null, null, null, []);
        $helper = new PropertyHelper($view);
        $actual = $helper->value($object, $property);
        static::assertEquals($expected, $actual);
    }

    /**
     * Dummy function to test custom control.
     *
     * @param mixed|null $value The value
     * @return array
     */
    public static function dummy($value): array
    {
        return ['html' => sprintf('<dummy>%s</dummy>', $value)];
    }

    /**
     * Test `fieldLabel`.
     *
     * @return void
     * @covers ::fieldLabel()
     */
    public function testFieldLabel(): void
    {
        // no type
        $view = new View(null, null, null, []);
        $helper = new PropertyHelper($view);
        $actual = $helper->fieldLabel('dummy');
        $expected = 'Dummy';
        static::assertEquals($expected, $actual);

        // type without config
        $actual = $helper->fieldLabel('info', 'dummies');
        $expected = 'Info';
        static::assertEquals($expected, $actual);

        // type with config
        $expected = 'A B O U T';
        Configure::write(
            'Properties.dummies',
            array_merge(
                (array)\Cake\Core\Configure::read('Properties.dummies'),
                ['labels' => ['fields' => ['about' => $expected]]]
            )
        );
        $actual = $helper->fieldLabel('about', 'dummies');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `fastCreateFields`.

     * @return void
     * @covers ::fastCreateFields()
     */
    public function testFastCreateFields(): void
    {
        $view = new View(null, null, null, []);
        $helper = new PropertyHelper($view);
        Configure::write('Properties.documents.fastCreate', [
            'required' => ['status'],
            'all' => ['status', 'title', 'description' => 'plaintext', 'body' => 'richtext', 'extra' => 'json', 'date_ranges'],
        ]);
        $actual = $helper->fastCreateFields('documents', 'fast_create_docs_');
        $expected = '<div class="input radio"><label>Status</label><input type="hidden" name="status" id="fast_create_docs_status" value=""/><label for="fast_create_docs_status-on"><input type="radio" name="status" value="on" id="fast_create_docs_status-on" class="fastCreateField required" data-name="status" key="documents-status" v-model="object.attributes.status">On</label><label for="fast_create_docs_status-draft"><input type="radio" name="status" value="draft" id="fast_create_docs_status-draft" class="fastCreateField required" data-name="status" key="documents-status" v-model="object.attributes.status">Draft</label><label for="fast_create_docs_status-off"><input type="radio" name="status" value="off" id="fast_create_docs_status-off" class="fastCreateField required" data-name="status" key="documents-status" v-model="object.attributes.status">Off</label></div><div class="input title text"><label for="fast_create_docs_title">Title</label><input type="text" name="title" class="fastCreateField" id="fast_create_docs_title" data-name="title" key="documents-title" value=""/></div><div class="input textarea"><label for="fast_create_docs_description">Description</label><textarea name="description" id="fast_create_docs_description" class="fastCreateField fastCreateField" data-name="description" key="documents-description" rows="5"></textarea></div><div class="input textarea"><label for="fast_create_docs_body">Body</label><textarea name="body" id="fast_create_docs_body" class="fastCreateField fastCreateField" data-name="body" key="documents-body" v-richeditor="&quot;&quot;" rows="5"></textarea></div><div class="input textarea"><label for="fast_create_docs_extra">Extra</label><textarea name="extra" id="fast_create_docs_extra" class="json fastCreateField" data-name="extra" key="documents-extra" v-jsoneditor="true" rows="5">null</textarea></div><div class="date-ranges-item mb-1"><div><div class="input text"><label for="start_date_0">From</label><input type="text" name="date_ranges[0][start_date]" id="start_date_0" class="fastCreateField" data-name="date_ranges" key="documents-date_ranges" v-datepicker="true" date="true" time="true" daterange="true" value=""/></div><div class="input text"><label for="end_date_0">To</label><input type="text" name="date_ranges[0][end_date]" id="end_date_0" class="fastCreateField" data-name="date_ranges" key="documents-date_ranges" v-datepicker="true" date="true" time="true" daterange="true" value=""/></div><div class="input checkbox"><input type="hidden" name="date_ranges[0][params][all_day]" value="0"/><label for="all_day_0"><input type="checkbox" name="date_ranges[0][params][all_day]" value="" id="all_day_0" class="fastCreateField" data-name="date_ranges" key="documents-date_ranges" checked="checked">All Day</label></div></div></div><input type="hidden" name="_jsonKeys" id="jsonkeys" value="extra"/>';
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `prepareFieldOptions`.

     * @return void
     * @covers ::prepareFieldOptions()
     */
    public function testPrepareFieldOptions(): void
    {
        $view = new View(null, null, null, []);
        $helper = new PropertyHelper($view);
        // json
        $actual = ['something' => true];
        $helper->prepareFieldOptions('extra', 'json', $actual);
        $expected = array_merge(['something' => true], Control::json([]));
        static::assertEquals($expected, $actual);

        // status
        $actual = ['whatever' => true];
        $helper->prepareFieldOptions('status', null, $actual);
        $expected = ['v-model' => 'object.attributes.status', 'whatever' => true];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `dateRange`.

     * @return void
     * @covers ::dateRange()
     */
    public function testDateRange(): void
    {
        $view = new View(null, null, null, []);
        $helper = new PropertyHelper($view);
        $actual = $helper->dateRange('dummy', []);
        $expected = '<div class="date-ranges-item mb-1"><div><div class="input text"><label for="start_date_0">From</label><input type="text" name="date_ranges[0][start_date]" id="start_date_0" v-datepicker="true" date="true" time="true" daterange="true" value=""/></div><div class="input text"><label for="end_date_0">To</label><input type="text" name="date_ranges[0][end_date]" id="end_date_0" v-datepicker="true" date="true" time="true" daterange="true" value=""/></div><div class="input checkbox"><input type="hidden" name="date_ranges[0][params][all_day]" value="0"/><label for="all_day_0"><input type="checkbox" name="date_ranges[0][params][all_day]" value="" id="all_day_0" checked="checked">All Day</label></div></div></div>';
        static::assertEquals($expected, $actual);
    }
}
