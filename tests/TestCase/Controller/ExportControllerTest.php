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

namespace App\Test\TestCase\Controller;

use App\Controller\Component\ExportComponent;
use App\Controller\ExportController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionProperty;

/**
 * {@see \App\Controller\ExportController} Test Case
 */
#[CoversClass(ExportController::class)]
#[CoversMethod(ExportController::class, 'apiPath')]
#[CoversMethod(ExportController::class, 'export')]
#[CoversMethod(ExportController::class, 'fillDataFromResponse')]
#[CoversMethod(ExportController::class, 'getFieldNames')]
#[CoversMethod(ExportController::class, 'getFileName')]
#[CoversMethod(ExportController::class, 'getRelatedFileName')]
#[CoversMethod(ExportController::class, 'getValue')]
#[CoversMethod(ExportController::class, 'limit')]
#[CoversMethod(ExportController::class, 'prepareQuery')]
#[CoversMethod(ExportController::class, 'related')]
#[CoversMethod(ExportController::class, 'relatedFiltered')]
#[CoversMethod(ExportController::class, 'rows')]
#[CoversMethod(ExportController::class, 'rowsAll')]
#[CoversMethod(ExportController::class, 'rowsAllRelated')]
#[CoversMethod(ExportController::class, 'rowFields')]
class ExportControllerTest extends TestCase
{
    /**
     * Test subject.
     *
     * @var \App\Controller\ExportController
     */
    public ExportController $Export;

    /**
     * The api client (not mocked).
     *
     * @var BEditaClient|null
     */
    protected ?BEditaClient $apiClient = null;

    /**
     * Test data.
     *
     * @var array
     */
    public static array $testdata = [
        'input' => [
            'gustavo' => [
                'id' => 999,
                'attributes' => ['name' => 'gustavo', 'skills' => ['smart', 'rich', 'beautiful']],
                'meta' => ['category' => 'developer', 'extra' => ['prop' => 2]],
            ],
            'johndoe' => [
                'id' => 888,
                'attributes' => ['name' => 'john doe', 'skills' => ['humble', 'poor', 'ugly']],
                'meta' => ['category' => 'poet', 'extra' => ['prop' => null]],
            ],
        ],
        'expected' => [
            'gustavo' => [
                'id' => 999,
                'name' => 'gustavo',
                'skills' => '["smart","rich","beautiful"]',
                'category' => 'developer',
                'prop' => 2,
            ],
            'johndoe' => [
                'id' => 888,
                'name' => 'john doe',
                'skills' => '["humble","poor","ugly"]',
                'category' => 'poet',
                'prop' => null,
            ],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
            ])
        );

        $this->apiClient = ApiClientProvider::getApiClient();
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        ApiClientProvider::setApiClient($this->apiClient);
        parent::tearDown();
    }

    /**
     * test 'export'.
     *
     * @return void
     */
    public function testExport(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'params' => ['objectType' => 'users'],
            'post' => ['ids' => '888,999', 'objectType' => 'users', 'format' => 'csv'],
        ]);
        $controller = new class ($request) extends ExportController {
            public ?BEditaClient $apiClient;
            public function initialize(): void
            {
                parent::initialize();
                $this->loadComponent(ExportComponent::class);
            }
        };

        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn([
                'data' => [
                    0 => self::$testdata['input']['gustavo'],
                    1 => self::$testdata['input']['johndoe'],
                ],
                'meta' => [
                    'pagination' => [
                        'page_items' => 2,
                        'page_count' => 1,
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $controller->apiClient = $apiClient;

        // expected csv.
        $fields = '"id","name","skills","category","prop"';
        $row1 = '"999","gustavo","[""smart"",""rich"",""beautiful""]","developer","2"';
        $row2 = '"888","john doe","[""humble"",""poor"",""ugly""]","poet",""';
        $expected = sprintf('%s%s%s%s%s%s', $fields, "\n", $row1, "\n", $row2, "\n");

        // call export.
        $response = $controller->export();
        $content = $response->getBody()->__toString();
        static::assertInstanceOf('Cake\Http\Response', $response);
        static::assertEquals($expected, $content);

        // check 'Content-Disposition' header containing filename
        $download = $response->getHeader('Content-Disposition');
        $download = (string)Hash::get($download, '0');
        static::assertEquals('attachment; filename="users_', substr($download, 0, 28));
        static::assertEquals('.csv"', substr($download, strlen($download) - 5));
    }

    /**
     * Test `related`.
     *
     * @return void
     */
    public function testRelated(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'GET'],
            'params' => ['object_type' => 'users'],
            'get' => ['id' => '888', 'objectType' => 'users', 'format' => 'csv'],
        ]);
        $controller = new class ($request) extends ExportController {
            public ExportComponent $Export;
            public ?BEditaClient $apiClient;
            public function initialize(): void
            {
                parent::initialize();
                /** @var \App\Controller\Component\ExportComponent $component */
                $component = $this->loadComponent(ExportComponent::class);
                $this->Export = $component;
            }
        };

        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn([
                'data' => [
                    0 => self::$testdata['input']['gustavo'],
                ],
                'meta' => [
                    'pagination' => [
                        'page_items' => 1,
                        'page_count' => 1,
                    ],
                ],
            ]);
        $controller->apiClient = $apiClient;

        // expected csv.
        $fields = '"id","name","skills","category","prop"';
        $row1 = '"999","gustavo","[""smart"",""rich"",""beautiful""]","developer","2"';
        $expected = sprintf('%s%s%s%s', $fields, "\n", $row1, "\n");

        // call export.
        $this->setLimit(500);
        $response = $controller->related('999', 'seealso', 'csv');
        $content = $response->getBody()->__toString();
        static::assertInstanceOf('Cake\Http\Response', $response);
        static::assertEquals($expected, $content);

        // check 'Content-Disposition' header containing filename
        $download = $response->getHeader('Content-Disposition');
        $download = (string)Hash::get($download, '0');
        static::assertEquals('attachment; filename="users_', substr($download, 0, 28));
        static::assertEquals('.csv"', substr($download, strlen($download) - 5));
    }

    /**
     * Test case of export of format not allowed FAIL METHOD
     *
     * @return void
     */
    public function testExportFormatNotAllowed(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'params' => ['objectType' => 'proms'],
            'post' => ['ids' => '655', 'objectType' => 'proms', 'format' => ''],
        ]);
        $controller = new class ($request) extends ExportController {
            public ExportComponent $Export;
            public ?BEditaClient $apiClient;
            public function initialize(): void
            {
                parent::initialize();
                /** @var \App\Controller\Component\ExportComponent $component */
                $component = $this->loadComponent(ExportComponent::class);
                $this->Export = $component;
            }
        };

        // call export.
        $response = $controller->export();
        static::assertEquals(302, $response->getStatusCode());
        $flash = (array)$controller->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('Format choosen is not available', Hash::get($flash, '0.message'));
    }

    /**
     * Test case of related of format not allowed FAIL METHOD
     *
     * @return void
     */
    public function testRelatedFormatNotAllowed(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'GET'],
            'params' => ['objectType' => 'proms'],
            'get' => ['id' => '655', 'relation' => 'dummy', 'format' => 'abcde'],
        ]);
        $controller = new class ($request) extends ExportController {
            public ExportComponent $Export;
            public ?BEditaClient $apiClient;
            public function initialize(): void
            {
                parent::initialize();
                /** @var \App\Controller\Component\ExportComponent $component */
                $component = $this->loadComponent(ExportComponent::class);
                $this->Export = $component;
            }
        };

        // call export.
        $response = $controller->related('655', 'proms', '');
        static::assertEquals(302, $response->getStatusCode());
        $flash = (array)$controller->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('Format choosen is not available', Hash::get($flash, '0.message'));
    }

    /**
     * Data provider for `testCsvRows` test case.
     *
     * @return array
     */
    public static function rowsProvider(): array
    {
        return [
            'documents, all' => [
                [
                    ['id', 'name', 'skills', 'category', 'prop'],
                    ExportControllerTest::$testdata['expected']['gustavo'],
                    ExportControllerTest::$testdata['expected']['johndoe'],
                ],
                [
                    'documents',
                ],
                [
                    'data' => [
                        ExportControllerTest::$testdata['input']['gustavo'],
                        ExportControllerTest::$testdata['input']['johndoe'],
                    ],
                    'meta' => [
                        'pagination' => [
                            'page_items' => 2,
                            'page_count' => 1,
                        ],
                    ],
                ],
            ],
            'documents with ids' => [
                [
                    ['id', 'name', 'skills', 'category', 'prop'],
                    ExportControllerTest::$testdata['expected']['gustavo'],
                ],
                [
                    'documents',
                    '999',
                ],
                [
                    'data' => [
                        ExportControllerTest::$testdata['input']['gustavo'],
                    ],
                    'meta' => [
                        'pagination' => [
                            'page_items' => 1,
                            'page_count' => 1,
                        ],
                    ],
                ],
            ],
            'query' => [
                [
                    ['id', 'name', 'skills', 'category', 'prop'],
                    ExportControllerTest::$testdata['expected']['gustavo'],
                ],
                [
                    'documents',
                ],
                [
                    'data' => [
                        ExportControllerTest::$testdata['input']['gustavo'],
                    ],
                    'meta' => [
                        'pagination' => [
                            'page_items' => 1,
                            'page_count' => 1,
                        ],
                    ],
                ],
                [
                    'filter' => ['status' => 'on'],
                    'q' => 'gustavo',
                ],
            ],
        ];
    }

    /**
     * Test `rows` method.
     *
     * @param array $expected The expected result.
     * @param array $arguments Method arguments.
     * @param array $response API response.
     * @param array $post Post data.
     * @return void
     */
    #[DataProvider('rowsProvider')]
    public function testRows(array $expected, array $arguments, array $response, array $post = []): void
    {
        $this->setLimit(500);

        // mock api get.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($response);
        ApiClientProvider::setApiClient($apiClient);

        if (!empty($post)) {
            $this->Export = new ExportController(
                new ServerRequest(compact('post') + [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ])
            );
        }

        // set $this->Export->apiClient
        $property = new ReflectionProperty(ExportController::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Export, $apiClient);

        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('rows');
        $method->setAccessible(true);

        $actual = $method->invokeArgs($this->Export, $arguments);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testFillDataFromResponse` test case.
     *
     * @return array
     */
    public static function fillDataFromResponseProvider(): array
    {
        return [
            'empty data' => [
                [
                    'fields' => [],
                    'response' => [
                        'data' => [],
                    ],
                ], // input
                [], // expected
            ],
            'some data' => [
                [
                    'fields' => ['id', 'name', 'skills', 'category', 'prop'],
                    'response' => [
                        'data' => [
                            0 => ExportControllerTest::$testdata['input']['gustavo'],
                            1 => ExportControllerTest::$testdata['input']['johndoe'],
                        ],
                    ],
                ], // input
                [
                    0 => ExportControllerTest::$testdata['expected']['gustavo'],
                    1 => ExportControllerTest::$testdata['expected']['johndoe'],
                ], // expected
            ],
        ];
    }

    /**
     * Test `fillDataFromResponse`.
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     * @return void
     */
    #[DataProvider('fillDataFromResponseProvider')]
    public function testFillDataFromResponse($input, $expected): void
    {
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('fillDataFromResponse');
        $method->setAccessible(true);
        $data = [];
        $response = $input['response'];
        $fields = $input['fields'];
        $method->invokeArgs($this->Export, [&$data, $response, $fields]);
        static::assertEquals($expected, $data);
    }

    /**
     * Data provider for `testGetFieldNames` test case.
     *
     * @return array
     */
    public static function getFieldNamesProvider(): array
    {
        return [
            'full data, default key' => [
                [
                    'data' => [
                        0 => ExportControllerTest::$testdata['input']['gustavo'],
                    ],
                ], // input
                [
                    'id',
                    'name',
                    'skills',
                    'category',
                    'prop',
                ], // expected
            ],
            'full data by custom key' => [
                [
                    'data' => [
                        0 => [
                            'attributes' => [
                                'name' => 'gustavo',
                                'category' => 'developer',
                            ],
                        ],
                    ],
                ], // input
                [
                    'id',
                    'name',
                    'category',
                ], // expected
            ],
        ];
    }

    /**
     * Test `getFieldNames` method.
     *
     * @param string|array $response The response.
     * @param string|array $expected The expected value.
     * @return void
     */
    #[DataProvider('getFieldNamesProvider')]
    public function testGetFields($response, $expected): void
    {
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('getFieldNames');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, [$response]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testRowFields` test case.
     *
     * @return array
     */
    public static function rowFieldsProvider(): array
    {
        return [
            'empty data' => [
                [
                    'data' => [],
                    'fields' => [
                        'id',
                        'name',
                        'category',
                        'skills',
                    ],
                ], // input
                [
                    'id' => '',
                    'name' => '',
                    'category' => '',
                    'skills' => '',
                ], // expected
            ],
            'full data' => [
                [
                    'data' => ExportControllerTest::$testdata['input']['gustavo'],
                    'fields' => [
                        'id',
                        'name',
                        'category',
                        'skills',
                        'prop',
                    ],
                ], // input
                [
                    'id' => 999,
                    'name' => 'gustavo',
                    'category' => 'developer',
                    'skills' => '["smart","rich","beautiful"]',
                    'prop' => 2,
                ], // expected
            ],
        ];
    }

    /**
     * Test `rowFields` method.
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     * @return void
     */
    #[DataProvider('rowFieldsProvider')]
    public function testRowFields($input, $expected): void
    {
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('rowFields');
        $method->setAccessible(true);
        $data = $input['data'];
        $fields = $input['fields'];
        $row = $method->invokeArgs($this->Export, [&$data, $fields]);
        static::assertEquals($expected, $row);
    }

    /**
     * Data provider for `testGetValue` test case.
     *
     * @return array
     */
    public static function getValueProvider(): array
    {
        return [
            'value array' => [
                [ 'dummy' ], // input
                json_encode([ 'dummy' ]), // expected
            ],
            'value string' => [
                'dummy', // input
                'dummy', // expected
            ],
        ];
    }

    /**
     * Test `getValue` method.
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     * @return void
     */
    #[DataProvider('getValueProvider')]
    public function testGetValue($input, $expected): void
    {
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('getValue');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, [ $input ]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `limit`.
     *
     * @return void
     */
    public function testLimit(): void
    {
        $expected = 123;
        $this->setLimit($expected);
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('limit');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, []);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `relatedFiltered` method.
     *
     * @return void
     */
    public function testRelatedFiltered(): void
    {
        $request = new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'GET'],
            'params' => ['object_type' => 'users'],
            'get' => ['id' => '888', 'object_type' => 'users', 'format' => 'csv'],
        ]);

        $controller = new class ($request) extends ExportController {
            public ExportComponent $Export;
            public ?BEditaClient $apiClient;
            public function initialize(): void
            {
                parent::initialize();
                /** @var \App\Controller\Component\ExportComponent $component */
                $component = $this->loadComponent(ExportComponent::class);
                $this->Export = $component;
            }
        };
        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn([
                'data' => [
                    0 => self::$testdata['input']['gustavo'],
                ],
                'meta' => [
                    'pagination' => [
                        'page_items' => 1,
                        'page_count' => 1,
                    ],
                ],
            ]);
        $controller->apiClient = $apiClient;
        $controller->relatedFiltered('888', 'seealso', 'csv', 'q=test&filter[type]=documents');
        static::assertEquals(['q' => 'test', 'filter' => ['type' => 'documents']], $controller->filter);
    }

    /**
     * Set export limit in cache.
     *
     * @param int $limit The limit
     * @return void
     */
    private function setLimit(int $limit): void
    {
        Configure::write('Export.limit', $limit);
    }

    /**
     * Data provider for `testGetFileName` test case.
     *
     * @return array
     */
    public static function getRelatedFileNameProvider(): array
    {
        return [
            'empty filter' => [
                [],
                '123',
                'folders',
                'children',
                'csv',
                'folders_123_children_',
                3,
                'csv',
            ],
            'q + filter' => [
                ['q' => 'needle', 'filter' => ['type' => 'documents']],
                '123',
                'folders',
                'children',
                'csv',
                'folders_123_children_needle_documents_',
                5,
                'csv',
            ],
        ];
    }

    /**
     * Test `getRelatedFileName` method.
     *
     * @param array $filter The filter.
     * @param string $id The object id.
     * @param string $type The object type.
     * @param string $relation The relation.
     * @param string $format The format.
     * @param string $expectedPrefix The expected prefix.
     * @param string $expectedExtension The expected extension.
     * @return void
     */
    #[DataProvider('getRelatedFileNameProvider')]
    public function testGetRelatedFileName(array $filter, string $id, string $type, string $relation, string $format, string $expectedPrefix, int $expectedDash, string $expectedExtension): void
    {
        $this->Export->filter = $filter;
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('getRelatedFileName');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, [$id, $type, $relation, $format]);
        static::assertStringContainsString($expectedPrefix, $actual);
        static::assertStringContainsString($expectedExtension, $actual);
        static::assertTrue(strpos($actual, $expectedPrefix) === 0);
        $tmp = explode('_', $actual);
        static::assertEquals($expectedDash, count($tmp) - 1);
    }

    /**
     * Test `prepareQuery` method.
     *
     * @return void
     */
    public function testPrepareQuery(): void
    {
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => ['REQUEST_METHOD' => 'GET'],
                'params' => [
                    'objectType' => 'users',
                ],
                'post' => [
                    'filter' => ['{"status":"on"}'],
                    'q' => 'gustavo',
                ],
                'get' => ['id' => '888', 'objectType' => 'users', 'format' => 'csv'],
            ])
        );
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('prepareQuery');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, []);
        static::assertEquals(['filter' => ['status' => 'on'], 'q' => 'gustavo'], $actual);

        // use $this->filter
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => ['REQUEST_METHOD' => 'GET'],
                'params' => [
                    'objectType' => 'users',
                ],
                'post' => [
                    'q' => 'gustavo',
                ],
                'get' => ['id' => '888', 'objectType' => 'users', 'format' => 'csv'],
            ])
        );
        $this->Export->filter = ['filter' => ['type' => 'documents'], 'q' => 'needle'];
        $reflectionClass = new ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('prepareQuery');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, []);
        static::assertEquals(['filter' => ['type' => 'documents'], 'q' => 'needle'], $actual);
    }
}
