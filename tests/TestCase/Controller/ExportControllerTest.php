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

use App\Controller\ExportController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\ExportController} Test Case
 *
 * @coversDefaultClass \App\Controller\ExportController
 * @uses \App\Controller\ExportController
 */
class ExportControllerTest extends TestCase
{
    /**
     * Test subject.
     *
     * @var \App\Controller\ExportController
     */
    public $Export;

    /**
     * The api client (not mocked).
     *
     * @var BEditaClient
     */
    protected $apiClient = null;

    /**
     * Test data.
     *
     * @var array
     */
    protected $testdata = [
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
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
            ])
        );
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * test 'export'.
     *
     * @covers ::export()
     * @covers ::getFileName()
     *
     * @return void
     */
    public function testExport(): void
    {
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => ['REQUEST_METHOD' => 'POST'],
                'params' => ['objectType' => 'users'],
                'post' => ['ids' => '888,999', 'objectType' => 'users', 'format' => 'csv'],
            ])
        );

        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn([
                'data' => [
                    0 => $this->testdata['input']['gustavo'],
                    1 => $this->testdata['input']['johndoe'],
                ],
                'meta' => [
                    'pagination' => [
                        'page_items' => 2,
                        'page_count' => 1,
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $this->Export->apiClient = $apiClient;

        // expected csv.
        $fields = '"id","name","skills","category","prop"';
        $row1 = '"999","gustavo","[""smart"",""rich"",""beautiful""]","developer","2"';
        $row2 = '"888","john doe","[""humble"",""poor"",""ugly""]","poet",""';
        $expected = sprintf('%s%s%s%s%s%s', $fields, "\n", $row1, "\n", $row2, "\n");

        // call export.
        $response = $this->Export->export();
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
     * @covers ::export()
     * @return void
     */
    public function testExportFormatNotAllowed(): void
    {
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => ['REQUEST_METHOD' => 'POST'],
                'params' => ['objectType' => 'proms'],
                'post' => ['ids' => '655', 'objectType' => 'proms', 'format' => ''],
            ])
        );

        // call export.
        $response = $this->Export->export();
        static::assertEquals(302, $response->getStatusCode());
        $flash = (array)$this->Export->request->getSession()->read('Flash.flash');
        static::assertEquals('Format choosen is not available', Hash::get($flash, '0.message'));
    }

    /**
     * Data provider for `testCsvRows` test case.
     *
     * @return array
     */
    public function rowsProvider(): array
    {
        return [
            'documents, all' => [
                [
                    ['id', 'name', 'skills', 'category', 'prop'],
                    $this->testdata['expected']['gustavo'],
                    $this->testdata['expected']['johndoe'],
                ],
                [
                    'documents',
                ],
                [
                    'data' => [
                        $this->testdata['input']['gustavo'],
                        $this->testdata['input']['johndoe'],
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
                    $this->testdata['expected']['gustavo'],
                ],
                [
                    'documents',
                    '999',
                ],
                [
                    'data' => [
                        $this->testdata['input']['gustavo'],
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
                    $this->testdata['expected']['gustavo'],
                ],
                [
                    'documents',
                ],
                [
                    'data' => [
                        $this->testdata['input']['gustavo'],
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
     *
     * @covers ::rows()
     * @covers ::rowsAll()
     * @covers ::apiPath()
     * @covers ::prepareQuery()
     * @dataProvider rowsProvider()
     */
    public function testRows(array $expected, array $arguments, array $response, array $post = []): void
    {
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

        $this->Export->apiClient = $apiClient;

        $reflectionClass = new \ReflectionClass($this->Export);
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
    public function fillDataFromResponseProvider(): array
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
                            0 => $this->testdata['input']['gustavo'],
                            1 => $this->testdata['input']['johndoe'],
                        ],
                    ],
                ], // input
                [
                    0 => $this->testdata['expected']['gustavo'],
                    1 => $this->testdata['expected']['johndoe'],
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
     * @covers ::fillDataFromResponse()
     * @dataProvider fillDataFromResponseProvider()
     */
    public function testFillDataFromResponse($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('fillDataFromResponse');
        $method->setAccessible(true);
        extract($input); // => $fields, $response
        $data = [];
        $method->invokeArgs($this->Export, [&$data, $response, $fields]);
        static::assertEquals($expected, $data);
    }

    /**
     * Data provider for `testGetFieldNames` test case.
     *
     * @return array
     */
    public function getFieldNamesProvider(): array
    {
        return [
            'full data, default key' => [
                [
                    'data' => [
                        0 => $this->testdata['input']['gustavo'],
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
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     * @return void
     * @covers ::getFieldNames()
     * @dataProvider getFieldNamesProvider()
     */
    public function testGetFields($response, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
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
    public function rowFieldsProvider(): array
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
                    'data' => $this->testdata['input']['gustavo'],
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
     * @covers ::rowFields()
     * @dataProvider rowFieldsProvider()
     */
    public function testRowFields($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('rowFields');
        $method->setAccessible(true);
        extract($input); // => $data, $field
        $row = $method->invokeArgs($this->Export, [&$data, $fields]);
        static::assertEquals($expected, $row);
    }

    /**
     * Data provider for `testGetValue` test case.
     *
     * @return array
     */
    public function getValueProvider(): array
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
     * @covers ::getValue()
     * @dataProvider getValueProvider()
     */
    public function testGetValue($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('getValue');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Export, [ $input ]);
        static::assertEquals($expected, $actual);
    }
}
