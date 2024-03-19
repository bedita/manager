<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\TreeController;
use App\Event\TreeCacheEventHandler;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\TreeController} Test Case
 *
 * @coversDefaultClass \App\Controller\TreeController
 * @uses \App\Controller\TreeController
 */
class TreeControllerTest extends BaseControllerTest
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

    /**
     * Test `get` method
     *
     * @return void
     * @covers ::get()
     * @covers ::treeData()
     * @covers ::fetchTreeData()
     */
    public function testGet(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'query' => ['filter' => ['roots' => true], 'pageSize' => 10],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->get();
        $actual = $tree->viewBuilder()->getVar('tree');
        static::assertNotEmpty($actual);
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertArrayHasKey($var, $actual);
        }
    }

    /**
     * Test `treeData` method on exception
     *
     * @return void
     * @covers ::get()
     * @covers ::treeData()
     */
    public function testDataException(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'query' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new class ($request) extends TreeController {
            public function fetchTreeData(array $query): array
            {
                throw new BEditaClientException('test exception');
            }
        };
        $tree->get();
        $actual = $tree->viewBuilder()->getVar('tree');
        static::assertEmpty($actual);
    }

    /**
     * Test `node` method
     *
     * @return void
     * @covers ::node()
     * @covers ::fetchNodeData()
     * @covers ::minimalData()
     */
    public function testNode(): void
    {
        $this->setupApi();
        $folder = $this->createTestFolder();
        $id = (string)Hash::get($folder, 'id');
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->node($id);
        $actual = $tree->viewBuilder()->getVar('node');
        static::assertNotEmpty($actual);
        static::assertEquals($id, $actual['id']);
        static::assertEquals('folders', $actual['type']);
        static::assertEquals('controller test folder', $actual['attributes']['title']);
        $expectedAttributes = ['status', 'title'];
        $actualAttributes = array_keys($actual['attributes']);
        sort($actualAttributes);
        static::assertEquals($expectedAttributes, $actualAttributes);
    }

    /**
     * Test `node` method
     *
     * @return void
     * @covers ::node()
     * @covers ::fetchNodeData()
     */
    public function testNodeException(): void
    {
        $this->setupApi();
        $id = '99999999';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->node($id);
        $actual = $tree->viewBuilder()->getVar('node');
        static::assertEmpty($actual);
    }

    /**
     * Test `parent` method
     *
     * @return void
     * @covers ::parent()
     * @covers ::fetchParentData()
     * @covers ::minimalDataWithMeta()
     */
    public function testParent(): void
    {
        $this->setupApi();
        $parent = $this->createTestFolder();
        $response = $this->client->save('folders', [
            'title' => 'controller test folder child',
            'parent_id' => (string)Hash::get($parent, 'id'),
        ]);
        $child = $response['data'];
        $id = (string)Hash::get($child, 'id');
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->parent($id);
        $actual = $tree->viewBuilder()->getVar('parent');
        static::assertNotEmpty($actual);
        static::assertEquals('folders', $actual['type']);
        static::assertEquals($parent['id'], $actual['id']);
    }

    /**
     * Test `parent` method
     *
     * @return void
     * @covers ::parent()
     * @covers ::fetchParentData()
     * @covers ::minimalDataWithMeta()
     */
    public function testParentNull(): void
    {
        $this->setupApi();
        $child = $this->createTestFolder();
        $id = (string)Hash::get($child, 'id');
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->parent($id);
        $actual = $tree->viewBuilder()->getVar('parent');
        static::assertNull($actual);
    }

    /**
     * Test `parent` method
     *
     * @return void
     * @covers ::parent()
     * @covers ::fetchParentData()
     */
    public function testParentException(): void
    {
        $this->setupApi();
        $id = '99999999';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->parent($id);
        $actual = $tree->viewBuilder()->getVar('parent');
        static::assertEmpty($actual);
    }

    /**
     * Test `parents` method
     *
     * @return void
     * @covers ::parents()
     * @covers ::fetchParentsData()
     * @covers ::minimalData()
     */
    public function testParents(): void
    {
        $this->setupApi();
        $parent = $this->createTestFolder();
        $docs = [
            'child document 1',
            'child document 2',
            'child document 3',
        ];
        $ids = [];
        $type = 'documents';
        foreach ($docs as $title) {
            $response = $this->client->save($type, [
                'title' => $title,
            ]);
            $this->client->addRelated($parent['id'], 'folders', 'children', [
                [
                    'id' => (string)Hash::get($response, 'data.id'),
                    'type' => $type,
                ],
            ]);
            $ids[] = (string)Hash::get($response, 'data.id');
        }
        foreach ($ids as $id) {
            $config = [
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'get' => [],
                'params' => compact('type', 'id'),
            ];
            $request = new ServerRequest($config);
            $tree = new TreeController($request);
            $tree->parents($type, $id);
            $actual = $tree->viewBuilder()->getVar('parents');
            $actual = $actual[0];
            static::assertNotEmpty($actual);
            static::assertEquals('folders', $actual['type']);
            static::assertEquals($parent['id'], $actual['id']);
        }
    }

    /**
     * Test `parents` method on exception
     *
     * @return void
     * @covers ::parents()
     * @covers ::fetchParentsData()
     */
    public function testParentsException(): void
    {
        $this->setupApi();
        $type = 'documents';
        $id = '99999999999';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('type', 'id'),
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->parents($type, $id);
        $actual = $tree->viewBuilder()->getVar('parents');
        static::assertEmpty($actual);
    }

    /**
     * Test `minimalData` method
     *
     * @return void
     * @covers ::minimalData()
     */
    public function testMinimalDataEmpty(): void
    {
        $this->setupApi();
        $id = 'mytestid';
        $key = CacheTools::cacheKey(sprintf('tree-node-%s', $id));
        Cache::write($key, [], TreeCacheEventHandler::CACHE_CONFIG);

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => compact('id'),
        ];
        $request = new ServerRequest($config);
        $tree = new class ($request) extends TreeController
        {
            public function minData(array $data): array
            {
                return $this->minimalData($data);
            }
        };
        $actual = $tree->minData([]);
        static::assertEmpty($actual);

    }
}
