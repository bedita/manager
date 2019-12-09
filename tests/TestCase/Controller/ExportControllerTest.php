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
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\ExportController} Test Case
 *
 * @coversDefaultClass \App\Controller\ExportController
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
                'meta' => ['category' => 'developer'],
            ],
            'johndoe' => [
                'id' => 888,
                'attributes' => ['name' => 'john doe', 'skills' => ['humble', 'poor', 'ugly']],
                'meta' => ['category' => 'poet'],
            ],
        ],
        'expected' => [
            'gustavo' => [
                'id' => 999,
                'name' => 'gustavo',
                'skills' => '["smart","rich","beautiful"]',
                'category' => 'developer',
            ],
            'johndoe' => [
                'id' => 888,
                'name' => 'john doe',
                'skills' => '["humble","poor","ugly"]',
                'category' => 'poet',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
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
    public function tearDown()
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * test 'export'.
     *
     * @covers ::export()
     *
     * @return void
     */
    public function testExport(): void
    {
        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => ['REQUEST_METHOD' => 'POST'],
                'params' => ['objectType' => 'users'],
                'post' => ['ids' => '888,999', 'objectType' => 'users'],
            ])
        );

        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('getObjects')
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
        $fields = 'id,name,skills,category';
        $row1 = '999,gustavo,"[""smart"",""rich"",""beautiful""]",developer';
        $row2 = '888,"john doe","[""humble"",""poor"",""ugly""]",poet';
        $expected = sprintf('%s%s%s%s%s%s', $fields, "\n", $row1, "\n", $row2, "\n");

        // call export.
        $response = $this->Export->export();
        $content = $response->getBody()->__toString();
        static::assertInstanceOf('Cake\Http\Response', $response);
        static::assertEquals($expected, $content);
    }

    /**
     * Data provider for `testCsvRows` test case.
     *
     * @return array
     */
    public function csvRowsProvider(): array
    {
        return [
            'documents, all' => [
                [
                    'objectType' => 'documents',
                ], // input
                [
                    'response' => [
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
                    ],
                    'data' => [
                        0 => ['id', 'name', 'skills', 'category' => 'category'],
                        1 => $this->testdata['expected']['gustavo'],
                        2 => $this->testdata['expected']['johndoe'],
                    ],
                ], // expected
            ],
            'documents with ids' => [
                [
                    'objectType' => 'documents',
                    'ids' => '999',
                ], // input
                [
                    'response' => [
                        'data' => [
                            0 => $this->testdata['input']['gustavo'],
                        ],
                        'meta' => [
                            'pagination' => [
                                'page_items' => 1,
                                'page_count' => 1,
                            ],
                        ],
                    ],
                    'data' => [
                        0 => ['id', 'name', 'skills', 'category' => 'category'],
                        1 => $this->testdata['expected']['gustavo'],
                    ],
                ], // expected
            ],
        ];
    }

    /**
     * test 'csvRows'.
     *
     * @covers ::csvRows()
     * @dataProvider csvRowsProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
     */
    public function testCsvRows($input, $expected): void
    {
        // mock api getObjects.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('getObjects')
            ->willReturn($expected['response']);
        ApiClientProvider::setApiClient($apiClient);
        $this->Export->apiClient = $apiClient;

        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('csvRows');
        $method->setAccessible(true);
        extract($input); // => $objectType, $ids
        $parameters = (empty($ids)) ? [ $objectType ] : [ $objectType, $ids ];
        $actual = $method->invokeArgs($this->Export, $parameters);
        static::assertEquals($expected['data'], $actual);
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
                    'data' => [],
                    'response' => [
                        'data' => [],
                    ],
                ], // input
                [
                    'data' => [],
                    'fields' => [],
                ], // expected
            ],
            'some data' => [
                [
                    'data' => [],
                    'response' => [
                        'data' => [
                            0 => $this->testdata['input']['gustavo'],
                            1 => $this->testdata['input']['johndoe'],
                        ],
                    ],
                ], // input
                [
                    'data' => [
                        0 => $this->testdata['expected']['gustavo'],
                        1 => $this->testdata['expected']['johndoe'],
                    ],
                    'fields' => [
                        'id',
                        'name',
                        'skills',
                        'category' => 'category',
                    ],
                ], // expected
            ],
        ];
    }

    /**
     * test 'fillDataFromResponse'.
     *
     * @covers ::fillDataFromResponse()
     * @dataProvider fillDataFromResponseProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
     */
    public function testFillDataFromResponse($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('fillDataFromResponse');
        $method->setAccessible(true);
        extract($input); // => $data, $response
        $actual = $method->invokeArgs($this->Export, [ &$data, $response ]);
        static::assertEquals($expected['fields'], $actual);
        static::assertEquals($expected['data'], $data);
    }

    /**
     * Data provider for `testCsv` test case.
     *
     * @return array
     */
    public function csvProvider(): array
    {
        return [
            'basic csv' => [
                [
                    'rows' => [
                        ['id', 'name', 'category', 'skills'],
                        [999, 'gustavo', 'developer', '["smart","rich","beautiful"]'],
                        [888, 'john doe', 'poet', '["humble","poor","ugly"]'],
                    ],
                    'objectType' => 'documents',
                ], // input
            ],
        ];
    }

    /**
     * test 'csv'.
     *
     * @covers ::csv()
     * @dataProvider csvProvider()
     *
     * @param string|array $input The input for the function.
     *
     * @return void
     */
    public function testCsv($input): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('csv');
        $method->setAccessible(true);
        extract($input); // => $rows, $objectType
        $response = $method->invokeArgs($this->Export, [ $rows, $objectType ]);
        static::assertInstanceOf('Cake\Http\Response', $response);
    }

    /**
     * test 'outputCsv'.
     *
     * @covers ::outputCsv()
     *
     * @return void
     */
    public function testOutputCsv(): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('outputCsv');
        $method->setAccessible(true);
        $response = $method->invokeArgs($this->Export, [ 'test', '' ]);
        static::assertInstanceOf('Cake\Http\Response', $response);
    }

    /**
     * Data provider for `testProcessCsv` test case.
     *
     * @return array
     */
    public function processCsvProvider(): array
    {
        return [
            'basic csv' => [
                [
                    'rows' => [
                        ['id', 'name', 'category', 'skills'],
                        [999, 'gustavo', 'developer', '["smart","rich","beautiful"]'],
                        [888, 'john doe', 'poet', '["humble","poor","ugly"]'],
                    ],
                    'objectType' => 'documents',
                ], // input
                sprintf('id,name,category,skills%s999,gustavo,developer,"[""smart"",""rich"",""beautiful""]"%s888,"john doe",poet,"[""humble"",""poor"",""ugly""]"%s', "\n", "\n", "\n"), // expected
            ],
        ];
    }

    /**
     * test 'processCsv'.
     *
     * @covers ::processCsv()
     * @dataProvider processCsvProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
     */
    public function testProcessCsv($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('processCsv');
        $method->setAccessible(true);
        extract($input); // => $rows, $objectType
        $actual = $method->invokeArgs($this->Export, [ $rows, $objectType ]);
        static::assertEquals($expected, $actual['csv']);
        static::assertNotEmpty($actual['filename']);
    }

    /**
     * Data provider for `testGetFields` test case.
     *
     * @return array
     */
    public function getFieldsProvider(): array
    {
        return [
            'full data, default key' => [
                [
                    'response' => [
                        'data' => [
                            0 => $this->testdata['input']['gustavo'],
                        ],
                    ],
                ], // input
                [
                    'name',
                    'skills',
                ], // expected
            ],
            'full data by custom key' => [
                [
                    'response' => [
                        'data' => [
                            0 => [
                                'user' => [
                                    'name' => 'gustavo',
                                    'category' => 'developer',
                                ],
                            ],
                        ],
                    ],
                    'key' => 'user',
                ], // input
                [
                    'name',
                    'category',
                ], // expected
            ],
        ];
    }

    /**
     * test 'getFields'.
     *
     * @covers ::getFields()
     * @dataProvider getFieldsProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
     */
    public function testGetFields($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('getFields');
        $method->setAccessible(true);
        extract($input); // => $response, $key
        $parameters = (empty($key)) ? [ $response ] : [ $response, $key ];
        $actual = $method->invokeArgs($this->Export, $parameters);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testFillRowFields` test case.
     *
     * @return array
     */
    public function fillRowFieldsProvider(): array
    {
        return [
            'empty data' => [
                [
                    'row' => [],
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
                    'row' => [],
                    'data' => $this->testdata['input']['gustavo'],
                    'fields' => [
                        'id',
                        'name',
                        'category',
                        'skills',
                    ],
                ], // input
                [
                    'id' => 999,
                    'name' => 'gustavo',
                    'category' => 'developer',
                    'skills' => '["smart","rich","beautiful"]',
                ], // expected
            ],
        ];
    }

    /**
     * test 'fillRowFields'.
     *
     * @covers ::fillRowFields()
     * @dataProvider fillRowFieldsProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
     */
    public function testFillRowFields($input, $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Export);
        $method = $reflectionClass->getMethod('fillRowFields');
        $method->setAccessible(true);
        extract($input); // => $row, $data, $field
        $method->invokeArgs($this->Export, [&$row, $data, $fields]);
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
     * test 'getValue'.
     *
     * @covers ::getValue()
     * @dataProvider getValueProvider()
     *
     * @param string|array $input The input for the function.
     * @param string|array $expected The expected value.
     *
     * @return void
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
