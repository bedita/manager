<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        // setup request, view and helper
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ]);
        $view = new View($request, null, null, []);
        $view->set('objectType', 'dummies');
        $this->Schema = new SchemaHelper($view);
        // set custom control handlers
        $propsConf = (array)Configure::read('Properties');
        $propsConf['dummies'] = [
            'options' => [
                'descr' => [
                    'handler' => 'App\Form\CustomComponentControl',
                    'tag' => 'dummy',
                ],
            ],
        ];
        Configure::write('Properties', $propsConf);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Schema);

        parent::tearDown();
    }

    /**
     * Data provider for `testControlOptions` test case.
     *
     * @return array
     */
    public function controlOptionsSchemaProvider(): array
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
                    'options' => [
                        ['value' => 'on', 'text' => __('On')],
                        ['value' => 'draft', 'text' => __('Draft')],
                        ['value' => 'off', 'text' => __('Off')],
                    ],
                    'templateVars' => [
                        'containerClass' => 'status',
                    ],
                    'type' => 'radio',
                    'value' => 'on',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'status',
                'on',
            ],
            'password' => [
                // expected result
                [
                    'autocomplete' => 'new-password',
                    'class' => 'password',
                    'default' => '',
                    'placeholder' => __('new password'),
                    'value' => '',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'password',
                '',
            ],
            'confirm-password' => [
                // expected result
                [
                    'autocomplete' => 'new-password',
                    'class' => 'confirm-password',
                    'default' => '',
                    'id' => 'confirm_password',
                    'label' => __('Retype password'),
                    'name' => 'confirm-password',
                    'placeholder' => __('confirm password'),
                    'type' => 'password',
                    'value' => '',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'confirm-password',
                '',
            ],
            'json' => [
                // expected result
                [
                    'class' => 'json',
                    'type' => 'textarea',
                    'v-jsoneditor' => 'true',
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
                    'templates' => [
                        'inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>',
                    ],
                    'type' => 'text',
                    'value' => 'test',
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
                    'v-richeditor' => '""',
                    'readonly' => 0,
                    'value' => 'test',
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
                    'v-richeditor' => '""',
                    'readonly' => 0,
                    'value' => 'test',
                ],
                // schema type
                [
                    'type' => 'string',
                    'contentMediaType' => 'text/html',
                ],
                'body',
                'test',
            ],
            'integer' => [
                // expected result
                [
                    'step' => 1,
                    'type' => 'integer',
                    'value' => 123,
                ],
                // schema type
                [
                    'type' => 'integer',
                ],
                'integer',
                123,
            ],
            'number' => [
                // expected result
                [
                    'step' => 'any',
                    'type' => 'number',
                    'value' => 123.45,
                ],
                // schema type
                [
                    'type' => 'number',
                ],
                'number',
                123.45,
            ],
            'publish_start' => [
                // expected result
                [
                    'date' => 'true',
                    'time' => 'true',
                    'type' => 'text',
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                    'v-datepicker' => 'true',
                    'value' => 'test',
                ],
                // schema type
                [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
                'publish_start',
                'test',
            ],
            'enum' => [
                // expected result
                [
                    'options' => [
                        ['value' => 'good', 'text' => 'Good'],
                        ['value' => 'bad', 'text' => 'Bad'],
                    ],
                    'type' => 'select',
                    'value' => 'good',
                ],
                // schema type
                [
                    'type' => 'string',
                    'enum' => [
                        'good',
                        'bad',
                    ],
                ],
                'enum',
                'good',
            ],
            'enum nullable' => [
                // expected result
                [
                    'options' => [
                        ['value' => '', 'text' => ''],
                        ['value' => 'good', 'text' => 'Good'],
                        ['value' => 'bad', 'text' => 'Bad'],
                    ],
                    'type' => 'select',
                    'value' => 'good',
                ],
                // schema type
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
            ],
            'checkbox' => [
                // expected result
                [
                    'checked' => true,
                    'type' => 'checkbox',
                ],
                // schema type
                [
                    'type' => 'boolean',
                ],
                'company',
                'true',
            ],
            'array multiple checkbox' => [
                // expected result
                [
                    'multiple' => 'checkbox',
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
                    'type' => 'select',
                ],
                // schema type
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
                'test_array',
                null,
            ],
            'custom handler' => [
                // expected result
                [
                    'type' => 'string',
                    'html' => '<dummy label="descr" name="descr" value="something" :readonly=false ></dummy>',
                ],
                // schema type
                [
                    'type' => 'string',
                ],
                'descr',
                'something',
            ],
        ];
    }

    /**
     * Test `controlOptions` method.
     *
     * @param array $expected Expected result.
     * @param array $schema The JSON schema
     * @param string $name The field name.
     * @param mixed $value The field value.
     * @return void
     * @dataProvider controlOptionsSchemaProvider()
     * @covers ::controlOptions()
     * @covers ::customControl()
     */
    public function testControlOptions(array $expected, array $schema, string $name, $value): void
    {
        $actual = $this->Schema->controlOptions($name, $value, $schema);

        static::assertEquals(sort($expected), sort($actual));
    }

    /**
     * Test `lang` property
     *
     * @return void
     * @covers ::controlOptions()
     */
    public function testLang(): void
    {
        Configure::write('Project.config.I18n', null);
        $actual = $this->Schema->controlOptions('lang', null, []);
        static::assertEquals('text', Hash::get($actual, 'type'));
        static::assertNull(Hash::get($actual, 'value'));

        $i18n = [
            'languages' => [
                'en' => 'English',
                'de' => 'German',
            ],
        ];
        Configure::write('Project.config.I18n', $i18n);
        $actual = $this->Schema->controlOptions('lang', null, []);

        $expected = [
            'options' => [
                [
                    'value' => '',
                    'text' => '',
                ],
                [
                    'value' => 'en',
                    'text' => 'English',
                ],
                [
                    'value' => 'de',
                    'text' => 'German',
                ],
            ],
            'type' => 'select',
            'value' => null,
            'label' => null,
            'readonly' => false,
            'disabled' => false,
        ];
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testTranslatableFields` test case.
     *
     * @return array
     */
    public function translatableFieldsProvider(): array
    {
        return [
            'empty translatable' => [
                ['translatable' => []],
                [],
            ],
            'translatable list' => [
                ['translatable' => ['subtitle', 'other_field', 'title']],
                ['title', 'subtitle', 'other_field'],
            ],
            'simple' => [
                [
                    'properties' => [
                        'field' => [
                            'oneOf' => [
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/plain',
                                ],
                                [
                                    'type' => 'null',
                                ],
                            ],
                        ],
                    ],
                ],
                ['field'],
            ],
            'not translatable' => [
                [
                    'properties' => [
                        'field1' => [
                            'type' => 'integer',
                        ],
                        'field2' => [
                            'type' => 'string',
                            'contentMediaType' => 'text/css',
                        ],
                    ],
                ],
                [],
            ],
            'properties' => [
                [
                    'properties' => [
                        'dummy' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/plain',
                                ],
                            ],
                        ],
                        'description' => [
                            'type' => 'string',
                            'contentMediaType' => 'text/html',
                        ],
                        'title' => [
                            'type' => 'string',
                            'contentMediaType' => 'text/html',
                        ],
                    ],
                ],
                [
                    'title',
                    'description',
                    'dummy',
                ],
            ],
        ];
    }

    /**
     * Test `translatableFields` method
     *
     * @param array $schema The object schema
     * @param array $expected Expected result
     * @return void
     * @dataProvider translatableFieldsProvider()
     * @covers ::translatableFields()
     * @covers ::translatableType()
     */
    public function testTranslatableFields(array $schema, array $expected): void
    {
        $actual = $this->Schema->translatableFields($schema);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testFormat` test case.
     *
     * @return array
     */
    public function formatProvider(): array
    {
        $d = new FrozenTime('2019-09-08');
        $dateExpected = $d->i18nFormat();
        $d = new FrozenTime('2019-09-08T16:35:15+00');
        $dateTimeExpected = $d->i18nFormat();

        return [
            'dummy' => [
                'dummy',
                'dummy',
                [
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'string',
                            'contentMediaType' => 'text/html',
                        ],
                    ],
                ],
            ],
            'byte' => [
                '1 MB',
                1024 ** 2,
                [
                    'type' => 'byte',
                ],
            ],
            'bool' => [
                'Yes',
                true,
                [
                    'type' => 'boolean',
                ],
            ],
            'date' => [
                $dateExpected,
                '2019-09-08',
                [
                    'type' => 'string',
                    'format' => 'date',
                ],
            ],
            'empty date' => [
                '',
                '',
                [
                    'type' => 'string',
                    'format' => 'date',
                ],
            ],
            'date time' => [
                $dateTimeExpected,
                '2019-09-08T16:35:15+00',
                [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
            ],
            'empty date time' => [
                '',
                '',
                [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
            ],
            'json' => [
                '{"a":1,"b":2}',
                [
                    'a' => 1,
                    'b' => 2,
                ],
                [
                    'type' => 'object',
                ],
            ],
            'type not in string, number, integer, boolean, array, object' => [
                'Dummy',
                'Dummy',
                [
                    'type' => 'dummy',
                ],
            ],
        ];
    }

    /**
     * Test `format` method
     *
     * @param array $expected Expected result
     * @param mixed $value The value
     * @param array $schema The schema
     * @return void
     * @dataProvider formatProvider()
     * @covers ::format()
     * @covers ::formatByte()
     * @covers ::formatBoolean()
     * @covers ::formatDate()
     * @covers ::formatDateTime()
     * @covers ::typeFromSchema()
     */
    public function testFormat($expected, $value, array $schema): void
    {
        $actual = $this->Schema->format($value, $schema);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testSortable` test case.
     *
     * @return array
     */
    public function sortableProvider(): array
    {
        return [
            'no schema, default not sortable' => [
                'dummy_no_schema',
                [],
                false,
            ],
            'string: sortable' => [
                'dummy_string',
                ['type' => 'string'],
                true,
            ],
            'number: sortable' => [
                'dummy_number',
                ['type' => 'number'],
                true,
            ],
            'integer: sortable' => [
                'dummy_integer',
                ['type' => 'integer'],
                true,
            ],
            'boolean: sortable' => [
                'dummy_boolean',
                ['type' => 'boolean'],
                true,
            ],
            'date-time: sortable' => [
                'dummy_date-time',
                ['type' => 'date-time'],
                true,
            ],
            'date: sortable' => [
                'dummy_date',
                ['type' => 'date'],
                true,
            ],
            'array: not sortable' => [
                'dummy_array',
                ['type' => 'array'],
                false,
            ],
            'object: not sortable' => [
                'dummy_object',
                ['type' => 'object'],
                false,
            ],
            'oneOf, string: sortable' => [
                'dummy_oneof_string',
                ['oneOf' => [['type' => 'null'], ['type' => 'string']]],
                true,
            ],
            'oneOf, number: sortable' => [
                'dummy_oneof_number',
                ['oneOf' => [['type' => 'null'], ['type' => 'number']]],
                true,
            ],
            'oneOf, integer: sortable' => [
                'dummy_oneof_integer',
                ['oneOf' => [['type' => 'null'], ['type' => 'integer']]],
                true,
            ],
            'oneOf, boolean: sortable' => [
                'dummy_oneof_boolean',
                ['oneOf' => [['type' => 'null'], ['type' => 'boolean']]],
                true,
            ],
            'oneOf, date-time: sortable' => [
                'dummy_oneof_date-time',
                ['oneOf' => [['type' => 'null'], ['type' => 'date-time']]],
                true,
            ],
            'oneOf, date: sortable' => [
                'dummy_oneof_date',
                ['oneOf' => [['type' => 'null'], ['type' => 'date']]],
                true,
            ],
            'oneOf, array: not sortable' => [
                'dummy_oneof_array',
                ['oneOf' => [['type' => 'null'], ['type' => 'array']]],
                false,
            ],
            'oneOf, object: not sortable' => [
                'dummy_oneof_object',
                ['oneOf' => [['type' => 'null'], ['type' => 'object']]],
                false,
            ],
            'date_ranges' => [
                'date_ranges',
                [],
                true,
            ],
        ];
    }

    /**
     * Test `sortable` method
     *
     * @param string $field The field
     * @param array $schema The property schema
     * @param bool $expected Expected result
     * @return void
     * @dataProvider sortableProvider()
     * @covers ::sortable()
     */
    public function testSortable(string $field, array $schema, bool $expected): void
    {
        $view = $this->Schema->getView();
        $view->set('schema', [
            'properties' => [
                $field => $schema,
            ],
        ]);
        $actual = $this->Schema->sortable($field);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `rightTypes`
     *
     * @return array
     */
    public function rightTypesProvider(): array
    {
        return [
            'empty relationsSchema' => [
                [],
                [],
            ],
            'some right types' => [
                [
                    'dummyRelation1' => [
                        'left' => [
                            'l1dummies',
                        ],
                        'right' => [
                            'r1dummies',
                            'r2dummies',
                            'r3dummies',
                        ],
                    ],
                    'dummyRelation2' => [
                        'left' => [
                            'l2dummies',
                        ],
                        'right' => [
                            'r1dummies',
                            'r4dummies',
                            'r5dummies',
                        ],
                    ],
                ],
                [
                    'r1dummies',
                    'r2dummies',
                    'r3dummies',
                    'r4dummies',
                    'r5dummies',
                ],
            ],
        ];
    }

    /**
     * Test `rightTypes`
     *
     * @return void
     * @dataProvider rightTypesProvider()
     * @covers ::rightTypes()
     */
    public function testRightTypes($relationsSchema, $expected): void
    {
        $view = $this->Schema->getView();
        $view->set('relationsSchema', $relationsSchema);
        /** @phpstan-ignore-next-line */
        $actual = $this->Schema->rightTypes();
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `filterList` test case.
     *
     * @return array
     */
    public function filterListProvider(): array
    {
        return [
            'parent string' => [
                ['parent'],
                null,
                [
                    [
                        'disabled' => false,
                        'label' => 'Parent',
                        'name' => 'parent',
                        'readonly' => false,
                        'type' => 'text',
                        'value' => null,
                    ],
                ],
            ],
            'filter title custom' => [
                [['name' => 'title', 'label' => 'My Title']],
                ['title' => []],
                [
                    [
                        'class' => 'title',
                        'disabled' => false,
                        'label' => 'My Title',
                        'name' => 'title',
                        'readonly' => false,
                        'templates' => ['inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>'],
                        'type' => 'text',
                        'value' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `filterList`.
     *
     * @param array $filters The filters
     * @param array|null $properties The properties
     * @param array $expected The expected result
     * @return void
     * @dataProvider filterListProvider()
     * @covers ::filterList()
     * @covers ::controlOptions()
     */
    public function testFilterList(array $filters, ?array $properties, array $expected): void
    {
        $view = $this->Schema->getView();
        $view->set('schema', [
            'properties' => $properties,
        ]);
        $actual = $this->Schema->filterList($filters, $properties);
        ksort($actual[0]);
        ksort($expected[0]);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `filterListByType` test case.
     *
     * @return array
     */
    public function filterListByTypeProvider(): array
    {
        return [
            'schema null' => [
                [
                    'documents' => [
                        'parent',
                        'status',
                        ['name' => 'title', 'label' => 'My Title'],
                    ],
                ],
                null,
                [
                    'documents' => [
                        [
                            'value' => null,
                            'label' => 'Parent',
                            'readonly' => false,
                            'disabled' => false,
                            'type' => 'text',
                            'name' => 'parent',
                        ],
                        [
                            'options' => [
                                ['value' => 'on', 'text' => 'On'],
                                ['value' => 'draft', 'text' => 'Draft'],
                                ['value' => 'off', 'text' => 'Off'],
                            ],
                            'templateVars' => ['containerClass' => 'status'],
                            'type' => 'radio',
                            'value' => null,
                            'label' => 'Status',
                            'readonly' => false,
                            'disabled' => false,
                            'name' => 'status',
                        ],
                        [
                            'class' => 'title',
                            'templates' => ['inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>'],
                            'type' => 'text',
                            'value' => null,
                            'label' => 'My Title',
                            'readonly' => false,
                            'disabled' => false,
                            'name' => 'title',
                        ],
                    ],
                ],
            ],
            'schema not null' => [
                [
                    'documents' => [
                        'categories',
                        ['name' => 'title', 'label' => 'My Title'],
                    ],
                ],
                [
                    'documents' => [
                        'categories' => [
                            'type' => 'categories',
                        ],
                    ],
                ],
                [
                    'documents' => [
                        [
                            'value' => [],
                            'label' => 'Categories',
                            'readonly' => false,
                            'disabled' => false,
                            'type' => 'select',
                            'options' => [],
                            'multiple' => 'checkbox',
                            'name' => 'categories',
                        ],
                        [
                            'class' => 'title',
                            'templates' => [
                                'inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>',
                            ],
                            'type' => 'text',
                            'value' => null,
                            'label' => 'My Title',
                            'readonly' => false,
                            'disabled' => false,
                            'name' => 'title',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `filterListByType`.
     *
     * @return void
     * @dataProvider filterListByTypeProvider()
     * @covers ::filterListByType()
     * @covers ::filterList()
     * @covers ::controlOptions()
     */
    public function testfilterListByType(array $filtersByType, ?array $schemasByType, array $expected): void
    {
        $view = $this->Schema->getView();
        $view->set('schema', $schemasByType);
        $actual = $this->Schema->filterListByType($filtersByType, $schemasByType);
        static::assertSame($expected, $actual);
    }
}
