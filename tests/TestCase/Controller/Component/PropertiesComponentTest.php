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
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
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
    public function tearDown() : void
    {
        unset($this->Properties);

        parent::tearDown();
    }

    /**
     * Create test case component
     *
     * @return void
     */
    protected function createComponent()
    {
        $controller = new Controller();
        $registry = $controller->components();
        $this->Properties = $registry->load(PropertiesComponent::class);
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
        $index = ['legs', 'pet_name'];
        Configure::write('Properties.cats.index', $index);

        $this->createComponent();

        $list = $this->Properties->indexList('cats');
        static::assertEquals($index, $list);
    }

    /**
     * Data provider for `testViewGroups` test case.
     *
     * @return array
     */
    public function viewGroupsProvider() : array
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
                ]
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
                ]
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
                ]
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
                ]
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
    public function testViewGroups($expected, $object, string $type, array $config = []) : void
    {
        if (!empty($config)) {
            Configure::write(sprintf('Properties.%s.view', $type), $config);
        }

        $this->createComponent();

        $result = $this->Properties->viewGroups($object, $type);

        static::assertSame(sort($expected), sort($result));
    }
}
