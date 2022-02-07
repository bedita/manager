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
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\PropertiesComponent;
use App\Utility\CacheTools;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\PropertiesComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\PropertiesComponent
 */
class PropertiesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\PropertiesComponent
     */
    public $Properties;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        Cache::enable();

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        Cache::disable();
        unset($this->Properties);

        parent::tearDown();
    }

    /**
     * Create test case component
     *
     * @return void
     */
    protected function createComponent(): void
    {
        $controller = new Controller();
        $registry = $controller->components();
        $this->Properties = $registry->load(PropertiesComponent::class);
        $this->Properties->startup();
    }

    /**
     * Test `startup()` method.
     *
     * @return void
     * @covers ::startup()
     */
    public function testStartup(): void
    {
        Cache::clear();

        // test 1: read properties and write to cache
        Configure::write('Properties.users.fastCreate', [
            'required' => ['status', 'username'],
            'all' => ['status', 'username', 'email'],
        ]);
        $this->createComponent();
        $cacheKey = CacheTools::cacheKey('properties');
        $config = Cache::read($cacheKey, 'default');
        foreach (['index', 'view', 'filter', 'fastCreate'] as $key) {
            static::assertArrayHasKey($key, $config['users']);
        }

        // test 2: changing config won't change properties, because it's cached from previous call
        Configure::write('Properties.users.dummy', ['whatever']);
        $this->createComponent();
        $config = Cache::read($cacheKey, 'default');
        foreach (['index', 'view', 'filter', 'fastCreate'] as $key) {
            static::assertArrayHasKey($key, $config['users']);
        }
        static::assertArrayNotHasKey('whatever', $config['users']);
    }

    /**
     * Test `indexList()` method.
     *
     * @return void
     *
     * @covers ::indexList()
     */
    public function testIndexList(): void
    {
        Cache::clear();

        $index = ['legs', 'pet_name'];
        Configure::write('Properties.cats.index', $index);

        $this->createComponent();

        $list = $this->Properties->indexList('cats');
        static::assertEquals($index, $list);
    }

    /**
     * Test `filterList()` method.
     *
     * @return void
     *
     * @covers ::filterList()
     */
    public function testFilterList(): void
    {
        Cache::clear();

        $filter = ['modified'];
        Configure::write('Properties.documents.filter', $filter);

        $this->createComponent();

        $expectedFilter = $filter;

        $filterList = $this->Properties->filterList('documents');
        static::assertEquals($expectedFilter, $filterList);
    }

    /**
     * Test `filtersByType()` method.
     *
     * @return void
     *
     * @covers ::filtersByType()
     */
    public function testFiltersByType(): void
    {
        Cache::clear();

        $documentsFilters = ['lang', 'categories'];
        $profilesFilters = ['modified', 'status'];
        Configure::write('Properties.documents.filter', $documentsFilters);
        Configure::write('Properties.profiles.filter', $profilesFilters);

        $this->createComponent();

        $expected = [
            'documents' => $documentsFilters,
            'profiles' => $profilesFilters,
        ];

        $filters = $this->Properties->filtersByType([]);
        static::assertEquals([], $filters);

        $filters = $this->Properties->filtersByType(['documents', 'profiles']);
        static::assertEquals($expected, $filters);
    }

    /**
     * Test `relationsList()` method.
     *
     * @return void
     *
     * @covers ::relationsList()
     */
    public function testRelationsList(): void
    {
        Cache::clear();

        $index = ['has_food', 'is_tired', 'sleeps_with'];
        Configure::write('Properties.cats.relations', $index);

        $this->createComponent();

        $list = $this->Properties->relationsList('cats');
        static::assertEquals($index, $list);
    }

    /**
     * Data provider for `testViewGroups` test case.
     *
     * @return array
     */
    public function viewGroupsProvider(): array
    {
        return [
            'minimal' => [
                [
                    'core' => [
                        'title' => 'A',
                    ],
                    'publish' => [
                        'uname' => 'an-object',
                        'status' => 'on',
                        'publish_start' => null,
                    ],
                    'advanced' => [
                    ],
                    'other' => [
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'A',
                        'status' => 'on',
                        'uname' => 'an-object',
                        'publish_start' => null,
                    ],
                ],
                'foos',
            ],
            'keep' => [
                [
                    'core' => [
                        'something' => '',
                    ],
                    'publish' => [
                        'uname' => 'test',
                        'status' => 'on',
                    ],
                    'advanced' => [
                    ],
                    'other' => [
                    ],
                ],
                [
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'test',
                    ],
                ],
                'gustavos',
                [
                    '_keep' => [
                        'something',
                    ],
                    'core' => [
                        'something',
                    ],
                ],
            ],
            'other' => [
                [
                    'core' => [
                        'title' => 'A',
                    ],
                    'advanced' => [
                        'json_field' => 'json',
                    ],
                    'publish' => [
                        'uname' => 'test',
                        'status' => 'draft',
                    ],
                    'other' => [
                        'description' => 'desc',
                        'other_field' => null,
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'A',
                        'description' => 'desc',
                        'status' => 'draft',
                        'json_field' => 'json',
                        'uname' => 'test',
                        'other_field' => null,
                    ],
                ],
                'geeks',
                [
                    'core' => [
                        'title',
                    ],
                    'advanced' => [
                        'json_field',
                    ],
                ],
            ],
            'new order' => [
                [
                    'core' => [
                        'description' => null,
                        'title' => 'A',
                    ],
                    'publish' => [
                        'uname' => 'test',
                        'status' => 'on',
                    ],
                    'advanced' => [
                    ],
                    'other' => [
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'A',
                        'description' => null,
                        'status' => 'on',
                        'uname' => 'test',
                    ],
                ],
                'gifts',
                [
                    'core' => [
                        'description',
                        'title',
                    ],
                    'publish' => [
                        'uname',
                        'status',
                    ],
                ],
            ],
            'custom groups' => [
                [
                    'core' => [
                        'title' => 'A',
                    ],
                    'publish' => [
                        'status' => 'on',
                    ],
                    'advanced' => [
                    ],
                    'custom' => [
                        'model' => 'bianchi',
                    ],
                    'other' => [
                        'color' => 'blue',
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'A',
                        'status' => 'on',
                        'color' => 'blue',
                        'model' => 'bianchi',
                    ],
                ],
                'bikes',
                [
                    'core' => [
                        'title',
                    ],
                    'publish' => [
                        'status',
                    ],
                    'custom' => [
                        'model',
                    ],
                ],
            ],
            'other body' => [
                [
                    'core' => [
                        'title' => 'Example',
                        'body' => 'some text',
                    ],
                    'publish' => [
                    ],
                    'advanced' => [
                    ],
                    'other' => [
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'Example',
                        'body' => 'some text',
                    ],
                ],
                'foos',
                [
                    'core' => [
                        'title',
                        'body',
                    ],
                ],
            ],
            'other defaults' => [
                [
                    'core' => [
                        'title' => 'Example',
                    ],
                    'publish' => [
                    ],
                    'advanced' => [
                    ],
                    'other' => [
                        'body' => 'some text',
                    ],
                ],
                [
                    'attributes' => [
                        'title' => 'Example',
                        'body' => 'some text',
                        'date_ranges' => [],
                    ],
                ],
                'foos',
            ],

        ];
    }

    /**
     * Test `viewGroups()` method.
     *
     * @param array $expected Expected result.
     * @param array $object Object data.
     * @param string $type Object type.
     * @param array $config Properties configuration to write for $type
     * @return void
     *
     * @dataProvider viewGroupsProvider()
     * @covers ::viewGroups()
     * @covers ::initialize()
     */
    public function testViewGroups($expected, $object, string $type, array $config = []): void
    {
        Cache::clear();

        if (!empty($config)) {
            Configure::write(sprintf('Properties.%s.view', $type), $config);
        }

        $this->createComponent();

        $result = $this->Properties->viewGroups($object, $type);
        ksort($expected);
        ksort($result);

        static::assertEquals($expected, $result);
        foreach ($expected as $k => $v) {
            static::assertEquals($v, $result[$k]);
        }
    }
}
