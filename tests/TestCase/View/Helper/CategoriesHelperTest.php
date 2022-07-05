<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\CategoriesHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\CategoriesHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\CategoriesHelper
 */
class CategoriesHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\CategoriesHelper
     */
    public $Categories;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Categories = new CategoriesHelper(new View(null, null, null, []));
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Categories);

        parent::tearDown();
    }

    /**
     * Data provider for `testControl` method.
     *
     * @return array
     */
    public function controlProvider(): array
    {
        return [
            'is not tree' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'label' => 'Dummy 1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'label' => 'Dummy 2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'label' => 'Dummy 3', 'parent_id' => null],
                        ['id' => 4, 'name' => 'dummy4', 'label' => 'Dummy 4', 'parent_id' => null],
                        ['id' => 5, 'name' => 'dummy5', 'label' => 'Dummy 5', 'parent_id' => null],
                    ],
                ],
                'categories',
                [
                    ['name' => 'dummy1', 'label' => 'Dummy 1'],
                    ['name' => 'dummy3', 'label' => 'Dummy 3'],
                ],
                '<div class="categories"><h3>Global</h3><div class="input select"><input type="hidden" name="categories" id="categories" value=""/><div class="checkbox"><label for="categories-dummy1" class="selected"><input type="checkbox" name="categories[]" value="dummy1" checked="checked" id="categories-dummy1">Dummy 1</label></div><div class="checkbox"><label for="categories-dummy2"><input type="checkbox" name="categories[]" value="dummy2" id="categories-dummy2">Dummy 2</label></div><div class="checkbox"><label for="categories-dummy3" class="selected"><input type="checkbox" name="categories[]" value="dummy3" checked="checked" id="categories-dummy3">Dummy 3</label></div><div class="checkbox"><label for="categories-dummy4"><input type="checkbox" name="categories[]" value="dummy4" id="categories-dummy4">Dummy 4</label></div><div class="checkbox"><label for="categories-dummy5"><input type="checkbox" name="categories[]" value="dummy5" id="categories-dummy5">Dummy 5</label></div></div></div>',
            ],
            'is tree' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'label' => 'Dummy 1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'label' => 'Dummy 2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'label' => 'Dummy 3', 'parent_id' => 2],
                        ['id' => 4, 'name' => 'dummy4', 'label' => 'Dummy 4', 'parent_id' => 2],
                        ['id' => 5, 'name' => 'dummy5', 'label' => 'Dummy 5', 'parent_id' => 2],
                    ],
                ],
                'categories',
                [
                    ['name' => 'dummy1', 'label' => 'Dummy 1'],
                    ['name' => 'dummy3', 'label' => 'Dummy 3'],
                ],
                '<div class="categories"><h3>Global</h3><div class="input select"><input type="hidden" name="categories" id="categories" value=""/><div class="checkbox"><label for="categories-dummy1" class="selected"><input type="checkbox" name="categories[]" value="dummy1" checked="checked" id="categories-dummy1">Dummy 1</label></div></div></div><div class="categories"><h3>Dummy 2</h3><div class="input select"><div class="checkbox"><label for="categories-dummy3" class="selected"><input type="checkbox" name="categories[]" value="dummy3" checked="checked" id="categories-dummy3">Dummy 3</label></div><div class="checkbox"><label for="categories-dummy4"><input type="checkbox" name="categories[]" value="dummy4" id="categories-dummy4">Dummy 4</label></div><div class="checkbox"><label for="categories-dummy5"><input type="checkbox" name="categories[]" value="dummy5" id="categories-dummy5">Dummy 5</label></div></div></div>',
            ],
        ];
    }

    /**
     * Test `control` method
     *
     * @param array $schema The schema
     * @param string $name The name
     * @param mixed|null $value The value
     * @param string $expected The expected result
     * @return void
     * @dataProvider controlProvider()
     * @covers ::control()
     */
    public function testControl(array $schema, string $name, $value, string $expected): void
    {
        $view = new View(null, null, null, []);
        $view->set('schema', $schema);
        $this->Categories = new CategoriesHelper($view);
        $actual = $this->Categories->control($name, $value);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testHtml` method.
     *
     * @return array
     */
    public function htmlProvider(): array
    {
        return [
            'multiple nodes' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'label' => 'Dummy 1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'label' => 'Dummy 2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'label' => 'Dummy 3', 'parent_id' => 2],
                        ['id' => 4, 'name' => 'dummy4', 'label' => 'Dummy 4', 'parent_id' => 2],
                        ['id' => 5, 'name' => 'dummy5', 'label' => 'Dummy 5', 'parent_id' => 2],
                    ],
                ],
                'categories',
                [
                    ['name' => 'dummy1', 'label' => 'Dummy 1'],
                    ['name' => 'dummy3', 'label' => 'Dummy 3'],
                ],
                [],
                '<div class="categories"><h3>Global</h3><div class="input select"><label for="categories">Categories</label><input type="hidden" name="categories" id="categories" value=""/><div class="checkbox"><label for="categories-dummy1" class="selected"><input type="checkbox" name="categories[]" value="dummy1" checked="checked" id="categories-dummy1">Dummy 1</label></div></div></div><div class="categories"><h3>Dummy 2</h3><div class="input select"><label for="categories">Categories</label><div class="checkbox"><label for="categories-dummy3" class="selected"><input type="checkbox" name="categories[]" value="dummy3" checked="checked" id="categories-dummy3">Dummy 3</label></div><div class="checkbox"><label for="categories-dummy4"><input type="checkbox" name="categories[]" value="dummy4" id="categories-dummy4">Dummy 4</label></div><div class="checkbox"><label for="categories-dummy5"><input type="checkbox" name="categories[]" value="dummy5" id="categories-dummy5">Dummy 5</label></div></div></div>',
            ],
        ];
    }

    /**
     * Test `html` method
     *
     * @param array $schema The schema
     * @param string $name The name
     * @param mixed|null $value The value
     * @param array $options The options
     * @param string $expected The expected result
     * @return void
     * @dataProvider htmlProvider()
     * @covers ::html()
     */
    public function testHtml(array $schema, string $name, $value, array $options, string $expected): void
    {
        $view = new View(null, null, null, []);
        $view->set('schema', $schema);
        $this->Categories = new CategoriesHelper($view);
        $actual = $this->Categories->html($name, $value, $options);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testNode` method.
     *
     * @return array
     */
    public function nodeProvider(): array
    {
        return [
            'no children' => [
                [
                    'name' => 'dummy1',
                    'label' => 'Dummy 1',
                ],
                'categories',
                [['name' => 'dummy1', 'label' => 'Dummy 1'], ['name' => 'dummy2', 'label' => 'Dummy 2']],
                [],
                '<div class="categories"><h3>Dummy 1</h3><div class="input select"><label for="categories">Categories</label><div class="checkbox"><label for="categories-dummy1" class="selected"><input type="checkbox" name="categories[]" value="dummy1" checked="checked" id="categories-dummy1">Dummy 1</label></div></div></div>',
            ],
            'children' => [
                [
                    'name' => 'dummy1',
                    'label' => 'Dummy 1',
                    'children' => [
                        ['name' => 'son1', 'label' => 'Son 1'],
                        ['name' => 'son2', 'label' => 'Son 2'],
                        ['name' => 'son3', 'label' => 'Son 3'],
                    ],
                ],
                'categories',
                [['name' => 'dummy1', 'label' => 'Dummy 1'], ['name' => 'dummy2', 'label' => 'Dummy 2']],
                [],
                '<div class="categories"><h3>Dummy 1</h3><div class="input select"><label for="categories">Categories</label><div class="checkbox"><label for="categories-son1"><input type="checkbox" name="categories[]" value="son1" id="categories-son1">Son 1</label></div><div class="checkbox"><label for="categories-son2"><input type="checkbox" name="categories[]" value="son2" id="categories-son2">Son 2</label></div><div class="checkbox"><label for="categories-son3"><input type="checkbox" name="categories[]" value="son3" id="categories-son3">Son 3</label></div></div></div>',
            ],
        ];
    }

    /**
     * Test `node` method
     *
     * @param array $node The node
     * @param string $name The name
     * @param mixed|null $value The value
     * @param array $options The options
     * @param string $expected The expected result
     * @return void
     * @dataProvider nodeProvider()
     * @covers ::node()
     */
    public function testNode(array $node, string $name, $value, array $options, string $expected): void
    {
        $hiddenField = false;
        $actual = $this->Categories->node(
            $node,
            $name,
            $value,
            $options,
            $hiddenField
        );

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testControlOptions` method.
     *
     * @return array
     */
    public function controlOptionsProvider(): array
    {
        return [
            'empty node children, empty categories' => [
                ['name' => 'dummy', 'label' => 'Dummy'],
                null,
                ['class' => 'testClass'],
                [
                    'class' => 'testClass',
                    'type' => 'select',
                    'multiple' => 'checkbox',
                    'value' => [],
                    'hiddenField' => false,
                    'options' => [['value' => 'dummy', 'text' => 'Dummy']],
                ],
            ],
            'empty node children, not empty categories' => [
                ['name' => 'dummy1', 'label' => 'Dummy 1'],
                [['name' => 'dummy1', 'label' => 'Dummy 1'], ['name' => 'dummy2', 'label' => 'Dummy 2']],
                ['class' => 'testClass'],
                [
                    'class' => 'testClass',
                    'type' => 'select',
                    'multiple' => 'checkbox',
                    'value' => ['dummy1', 'dummy2'],
                    'hiddenField' => false,
                    'options' => [
                        ['value' => 'dummy1', 'text' => 'Dummy 1'],
                    ],
                ],
            ],
            'not empty node children, not empty categories' => [
                [
                    'name' => 'dummy1',
                    'label' => 'Dummy 1',
                    'children' => [
                        ['name' => 'son1', 'label' => 'Son 1'],
                        ['name' => 'son2', 'label' => 'Son 2'],
                        ['name' => 'son3', 'label' => 'Son 3'],
                    ],
                ],
                [['name' => 'dummy1', 'label' => 'Dummy 1'], ['name' => 'dummy2', 'label' => 'Dummy 2']],
                ['class' => 'testClass'],
                [
                    'class' => 'testClass',
                    'type' => 'select',
                    'multiple' => 'checkbox',
                    'value' => ['dummy1', 'dummy2'],
                    'hiddenField' => false,
                    'options' => [
                        ['value' => 'son1', 'text' => 'Son 1'],
                        ['value' => 'son2', 'text' => 'Son 2'],
                        ['value' => 'son3', 'text' => 'Son 3'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `controlOptions` method
     *
     * @param array $node The node
     * @param mixed|null $value The value
     * @param array $options The options
     * @param array $expected The expected result
     * @return void
     * @dataProvider controlOptionsProvider()
     * @covers ::controlOptions()
     */
    public function testControlOptions(array $node, $value, array $options, array $expected): void
    {
        $hiddenField = false;
        $actual = $this->Categories->controlOptions(
            $node,
            $value,
            $options,
            $hiddenField
        );

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testTree` method.
     *
     * @return array
     */
    public function treeProvider(): array
    {
        return [
            'empty schema' => [
                [],
                false,
                [],
            ],
            'order 1' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'parent_id' => 10],
                        ['id' => 4, 'name' => 'dummy4', 'parent_id' => 1],
                        ['id' => 5, 'name' => 'dummy5', 'parent_id' => 1],
                        ['id' => 6, 'name' => 'dummy6', 'parent_id' => null],
                        ['id' => 7, 'name' => 'dummy7', 'parent_id' => 10],
                        ['id' => 8, 'name' => 'dummy8', 'parent_id' => 10],
                        ['id' => 9, 'name' => 'dummy9', 'parent_id' => 1],
                        ['id' => 10, 'name' => 'dummy10', 'parent_id' => null],
                    ],
                ],
                true,
                [
                    [
                        'id' => '0', 'name' => '_', 'label' => 'Global', 'parent_id' => null,
                        'children' => [
                            ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                            ['id' => 6, 'name' => 'dummy6', 'parent_id' => null],
                        ],
                    ],
                    [
                        'id' => 1, 'name' => 'dummy1', 'parent_id' => null,
                        'children' => [
                            ['id' => 4, 'name' => 'dummy4', 'parent_id' => 1],
                            ['id' => 5, 'name' => 'dummy5', 'parent_id' => 1],
                            ['id' => 9, 'name' => 'dummy9', 'parent_id' => 1],
                        ],
                    ],
                    [
                        'id' => 10, 'name' => 'dummy10', 'parent_id' => null,
                        'children' => [
                            ['id' => 3, 'name' => 'dummy3', 'parent_id' => 10],
                            ['id' => 7, 'name' => 'dummy7', 'parent_id' => 10],
                            ['id' => 8, 'name' => 'dummy8', 'parent_id' => 10],
                        ],
                    ],
                ],
            ],
            'schema categories with all parent_id null' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'parent_id' => null],
                    ],
                ],
                false,
                [
                    [
                        'id' => '0',
                        'name' => '_',
                        'label' => 'Global',
                        'children' => [
                            ['id' => 1, 'name' => 'dummy1', 'parent_id' => null],
                            ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                            ['id' => 3, 'name' => 'dummy3', 'parent_id' => null],
                        ],
                        'parent_id' => null,
                    ],
                ],
            ],
            'schema categories with a parent_id not null' => [
                [
                    'categories' => [
                        ['id' => 1, 'name' => 'dummy1', 'parent_id' => null],
                        ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                        ['id' => 3, 'name' => 'dummy3', 'parent_id' => 1],
                    ],
                ],
                true,
                [
                    [
                        'id' => '0',
                        'name' => '_',
                        'label' => 'Global',
                        'children' => [
                            ['id' => 2, 'name' => 'dummy2', 'parent_id' => null],
                        ],
                        'parent_id' => null,
                    ],
                    [
                        'id' => 1,
                        'name' => 'dummy1',
                        'children' => [
                            ['id' => 3, 'name' => 'dummy3', 'parent_id' => 1],
                        ],
                        'parent_id' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `isTree` method
     *
     * @param array $schema The schema
     * @param bool $expectedIsTree The expected for isTree
     * @param array $expectedTree The expected for Tree
     * @return void
     * @dataProvider treeProvider()
     * @covers ::sortRoots()
     * @covers ::tree()
     * @covers ::isTree()
     */
    public function testTree(array $schema, bool $expectedIsTree, array $expectedTree): void
    {
        $view = new View(null, null, null, []);
        $view->set('schema', $schema);
        $this->Categories = new CategoriesHelper($view);
        $actual = $this->Categories->isTree();
        static::assertEquals($expectedIsTree, $actual);

        $actual = $this->Categories->tree();
        static::assertEquals($expectedTree, $actual);
    }
}
