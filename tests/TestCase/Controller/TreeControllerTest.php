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
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\TreeController} Test Case
 */
#[CoversClass(TreeController::class)]
#[CoversMethod(TreeController::class, 'children()')]
#[CoversMethod(TreeController::class, 'compactTreeData()')]
#[CoversMethod(TreeController::class, 'fetchCompactTreeData()')]
#[CoversMethod(TreeController::class, 'fetchNodeData')]
#[CoversMethod(TreeController::class, 'fetchParentData')]
#[CoversMethod(TreeController::class, 'fetchParentsData')]
#[CoversMethod(TreeController::class, 'fetchTreeData')]
#[CoversMethod(TreeController::class, 'get')]
#[CoversMethod(TreeController::class, 'initialize')]
#[CoversMethod(TreeController::class, 'loadAll()')]
#[CoversMethod(TreeController::class, 'minimalData')]
#[CoversMethod(TreeController::class, 'minimalDataWithMeta')]
#[CoversMethod(TreeController::class, 'node')]
#[CoversMethod(TreeController::class, 'parent')]
#[CoversMethod(TreeController::class, 'parents')]
#[CoversMethod(TreeController::class, 'pushIntoTree()')]
#[CoversMethod(TreeController::class, 'slug')]
#[CoversMethod(TreeController::class, 'slugPathCompact')]
#[CoversMethod(TreeController::class, 'treeData')]
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
     * Test `loadAll` method
     *
     * @return void
     */
    public function testLoadAll(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->loadAll();
        $actual = $tree->viewBuilder()->getVar('data');
        static::assertNotEmpty($actual);
        $vars = ['tree', 'folders'];
        foreach ($vars as $var) {
            static::assertArrayHasKey($var, $actual);
        }
    }

    /**
     * Test `children` method
     *
     * @return void
     */
    public function testChildren(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $folder = $this->createTestFolder();
        $child = $this->createTestObject();
        $this->client->addRelated($folder['id'], 'folders', 'children', [
            [
                'id' => (string)Hash::get($child, 'id'),
                'type' => (string)Hash::get($child, 'type'),
            ],
        ]);
        $tree->children($folder['id']);
        $actual = $tree->viewBuilder()->getVar('data');
        static::assertNotEmpty($actual);
        $actual = $tree->viewBuilder()->getVar('meta');
        static::assertNotEmpty($actual);
    }

    /**
     * Test `children` method on exception
     *
     * @return void
     */
    public function testChildrenException(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $id = '99999999';
        $tree->children($id);
        $actual = $tree->viewBuilder()->getVar('data');
        static::assertEmpty($actual);
    }

    /**
     * Test `compactTreeData` method with query parameter `no_cache` set to true
     *
     * @return void
     */
    public function testCompactTreeDataNoCache(): void
    {
        Cache::enable();
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'query' => ['no_cache' => true],
        ];
        $request = new ServerRequest($config);
        $tree = new class ($request) extends TreeController {
            public bool $usingCache = true;
            public function fetchCompactTreeData(): array
            {
                $this->usingCache = false;

                return parent::fetchCompactTreeData();
            }
        };
        $tree->loadAll();
        static::assertFalse($tree->usingCache);
        Cache::disable();
    }

    /**
     * Test `compactTreeData` method on exception
     *
     * @return void
     */
    public function testCompactTreeDataException(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new class ($request) extends TreeController {
            public function fetchCompactTreeData(): array
            {
                throw new BEditaClientException('test exception');
            }
        };
        $tree->loadAll();
        $actual = $tree->viewBuilder()->getVar('data');
        static::assertEmpty($actual);
    }

    /**
     * Test `fetchCompactTreeData` method
     *
     * @return void
     */
    public function testFetchCompactTreeData(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree = new class ($request) extends TreeController {
            public function fetchCompactTreeData(): array
            {
                return parent::fetchCompactTreeData();
            }
        };
        $mockResponse = [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'folders',
                    'attributes' => [
                        'title' => 'Folder 1',
                        'status' => 'published',
                        'description' => 'This is folder 1',
                        'body' => 'This is the body of folder 1',
                    ],
                    'meta' => [
                        'path' => '/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'folders',
                    'attributes' => [
                        'title' => 'Folder 2',
                        'status' => 'draft',
                        'description' => 'This is folder 2',
                        'body' => 'This is the body of folder 2',
                    ],
                    'meta' => [
                        'path' => '/1/2',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'folders',
                    'attributes' => [
                        'title' => 'Folder 3',
                        'status' => 'draft',
                        'description' => 'This is folder 3',
                        'body' => 'This is the body of folder 3',
                    ],
                    'meta' => [
                        'path' => '/3',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'page' => 1,
                    'page_size' => 10,
                    'page_count' => 1,
                    'total' => 2,
                    'count' => 2,
                ],
            ],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/folders')
            ->willReturn($mockResponse);
        $safeClient = ApiClientProvider::getApiClient();
        ApiClientProvider::setApiClient($apiClient);
        $data = $tree->fetchCompactTreeData();
        static::assertNotEmpty($data);
        static::assertArrayHasKey('tree', $data);
        static::assertArrayHasKey('folders', $data);
        $expectedTree = [
            '1' => [
                'id' => '1',
                'subfolders' => [
                    '2' => [
                        'id' => '2',
                    ],
                ],
            ],
            '3' => [
                'id' => '3',
            ],
        ];
        static::assertEquals($expectedTree, $data['tree']);
        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test `pushIntoTree` method
     *
     * @return void
     */
    public function testPushIntoTree(): void
    {
        $this->setupApi();
        $tree = new TreeController(new ServerRequest());
        $treeData = [
            '1' => [
                'id' => '1',
                'subfolders' => [
                    '3' => [
                        'id' => '3',
                        'subfolders' => [
                            '4' => [
                                'id' => '4',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $actual = $tree->pushIntoTree($treeData, '1', '2', 'subfolders');
        static::assertTrue($actual);
        $actual = $tree->pushIntoTree($treeData, '3', '5', 'subfolders');
        static::assertTrue($actual);
        $actual = $tree->pushIntoTree($treeData, '7', '6', 'subfolders');
        static::assertFalse($actual);
        $expected = [
            '1' => [
                'id' => '1',
                'subfolders' => [
                    '2' => [
                        'id' => '2',
                    ],
                    '3' => [
                        'id' => '3',
                        'subfolders' => [
                            '4' => [
                                'id' => '4',
                            ],
                            '5' => [
                                'id' => '5',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        static::assertEquals($expected, $treeData);
    }

    /**
     * Test `treeData` method on exception
     *
     * @return void
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
     */
    public function testParent(): void
    {
        $this->setupApi();
        $parent = $this->createTestFolder();
        $response = $this->client->save('folders', [
            'title' => 'controller test folder child',
            'parent_id' => (int)Hash::get($parent, 'id'),
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

    /**
     * Test `slug` method
     *
     * @return void
     */
    public function testSlug(): void
    {
        $this->setupApi();
        $parent = $this->createTestFolder();
        $response = $this->client->save('folders', [
            'title' => 'controller test folder child',
            'parent_id' => (int)Hash::get($parent, 'id'),
        ]);
        $child = $response['data'];
        $parentId = (string)Hash::get($parent, 'id');
        $childId = (string)Hash::get($child, 'id');
        $type = (string)Hash::get($child, 'type');
        $slug = 'test_slug';
        $data = [
            'parent' => $parentId,
            'slug' => $slug,
            'type' => $type,
            'id' => $childId,
        ];
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->slug();
        $actualResponse = $tree->viewBuilder()->getVar('response');
        static::assertNotEmpty($actualResponse);
        $actualError = $tree->viewBuilder()->getVar('error');
        static::assertNull($actualError);
        static::assertArrayHasKey('links', $actualResponse);
        static::assertArrayHasKey('self', $actualResponse['links']);
        static::assertArrayHasKey('home', $actualResponse['links']);
        $this->assertNotEmpty($actualResponse['links']['self']);
        $this->assertNotEmpty($actualResponse['links']['home']);
    }

    /**
     * Test `slug` method on exception
     *
     * @return void
     */
    public function testSlugException(): void
    {
        $this->setupApi();
        $parent = $this->createTestFolder();
        $response = $this->client->save('folders', [
            'title' => 'controller test folder child',
            'parent_id' => (int)Hash::get($parent, 'id'),
        ]);
        $child = $response['data'];
        $childId = (string)Hash::get($child, 'id');
        $type = (string)Hash::get($child, 'type');
        $slug = 'test_slug';
        $data = [
            'parent' => null,
            'slug' => $slug,
            'type' => $type,
            'id' => $childId,
        ];
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data,
        ];
        $request = new ServerRequest($config);
        $tree = new TreeController($request);
        $tree->slug();
        $actualResponse = $tree->viewBuilder()->getVar('response');
        $actualError = $tree->viewBuilder()->getVar('error');
        static::assertEmpty($actualResponse);
        static::assertNotEmpty($actualError);
    }
}
