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

use App\Controller\Component\SchemaComponent;
use App\Controller\ModulesController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * Sample controller wrapper, to add useful methods for test
 */
class ModulesControllerSample extends ModulesController
{
    /**
     * Getter for objectType protected var
     *
     * @return string
     */
    public function getObjectType() : string
    {
        return $this->objectType;
    }

    /**
     * Update media urls, public for testing
     *
     * @param array $response The response
     * @return void
     */
    public function updateMediaUrls(array &$response) : void
    {
        parent::updateMediaUrls($response);
    }
}

/**
 * {@see \App\Controller\ModulesController} Test Case
 *
 * @coversDefaultClass \App\Controller\ModulesController
 */
class ModulesControllerTest extends TestCase
{
    /**
     * Test Modules controller
     *
     * @var App\Test\TestCase\Controller\ModulesControllerSample
     */
    public $controller;

    /**
     * Test api client
     *
     * @var BEdita\SDK\BEditaClient
     */
    public $client;

    /**
    * Test request config
    *
    * @var array
    */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'POST',
        ],
        'post' => [
            'username' => '',
            'password' => '',
        ],
    ];

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);
    }

    /**
     * Test `initialize` method
     *
     * @covers ::initialize()
     *
     * @return void
     */
    public function testInitialize() : void
    {
        // Setup controller for test
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request); // it already calls initialize, internally
        static::assertEquals('documents', $this->controller->getObjectType());
        static::assertEquals('documents', $this->controller->Modules->getConfig('currentModuleName'));
        static::assertEquals('documents', $this->controller->Schema->getConfig('type'));
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     *
     * @return void
     */
    public function testIndex() : void
    {
        // Setup controller for test
        $method = 'getObjects';
        $response = [
            'data' => [
                [
                    'id' => 1,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'a sample doc',
                    ],
                ],
                [
                    'id' => 2,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'another doc',
                    ],
                ],
            ],
            'meta' => [],
            'links' => [],
        ];
        $this->setupController();
        // do controller call
        $result = $this->controller->index();
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     *
     * @return void
     */
    public function testView() : void
    {
        // Setup controller for test
        $method = 'getObject';
        $response = [
            'data' => [
                'id' => 1,
                'type' => 'documents',
                'attributes' => [
                    'title' => 'a sample doc',
                ],
                'relationships' => [],
            ],
        ];
        $this->setupController();
        // do controller call
        $result = $this->controller->view(1);
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());
    }

    /**
     * Test `uname` method
     *
     * @covers ::uname()
     *
     * @return void
     */
    public function testUname() : void
    {
        // Setup controller for test
        $method = 'get';
        $response = [
            'data' => [
                'id' => 1,
                'type' => 'documents',
            ],
        ];
        $this->setupController();
        // do controller call
        $result = $this->controller->uname(1);
        $header = $result->header();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
        static::assertEquals('/documents/view/1', $header['Location']);
    }

    /**
     * Test `uname` method, case 404 Not Found
     *
     * @covers ::uname()
     *
     * @return void
     */
    public function testUname404() : void
    {
        // Setup controller for test
        $method = 'get';
        $exception = new BEditaClientException('Not Found', 404);
        $this->setupController();
        // do controller call
        $result = $this->controller->uname('just-a-wrong-uname');
        $header = $result->header();
        static::assertEquals(302, $result->statusCode()); // redir to referer
        static::assertEquals('/', $header['Location']);
    }

    /**
     * Test `uname` method, case 500 Internal Error (or just an error different from 404)
     *
     * @covers ::uname()
     *
     * @return void
     */
    public function testUname500() : void
    {
        // Setup controller for test
        $method = 'get';
        $exception = new BEditaClientException('Internal Error', 500);
        $this->setupController();
        // do controller call
        $result = $this->controller->uname('just-a-wrong-uname');
        $header = $result->header();
        static::assertEquals(302, $result->statusCode()); // redir to referer
        static::assertEquals('/', $header['Location']);
    }

    /**
     * Test `create` method
     *
     * @covers ::create()
     *
     * @return void
     */
    public function testCreate() : void
    {
        // Setup controller for test
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // mock Schema->getSchema();
        $testSchema = [
            'definitions' => [],
            '$id' => 'https://example.com/model/schema/documents',
            '"$schema' => 'http://json-schema.org/draft-06/schema#',
            'type' => 'object',
            'properties' => [
                'title' => [
                    'oneOf' => [
                        [
                            'type' => null,
                        ],
                        [
                            'type' => 'string',
                            'contentMediaType' => 'text/html',
                        ],
                    ],
                ],
                '$id' => '/properties/title',
                'title' => 'Title',
                'description' => ''
            ],
            'required' => [],
            'revision' => '3954685133',
        ];
        $schema = $this->getMockBuilder(SchemaComponent::class)
            ->setConstructorArgs([$this->controller->components()])
            ->getMock();
        $schema->method('getSchema')->willReturn($testSchema);
        $this->controller->Schema = $this->controller
            ->components()
            ->set('Schema', $schema)->get('Schema');
        // do controller call
        $this->controller->create();
        foreach (['object', 'schema', 'properties'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
    }

    /**
     * Test `create` method
     *
     * @covers ::create()
     *
     * @return void
     */
    public function testCreate302() : void
    {
        // Setup controller for test
        $this->setupController();
        // do controller call
        $result = $this->controller->create();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `save` method
     *
     * @covers ::save()
     *
     * @return void
     */
    public function testSave() : void
    {
        // Setup controller for test
        $method = 'save';
        $response = [
            'data' => [
                'id' => 1,
                'type' => 'documents',
            ],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'title' => 'sample',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $result = $this->controller->save();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     *
     * @return void
     */
    public function testDelete() : void
    {
        // Setup controller for test
        $method = 'deleteObject';
        $response = [];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => 1,
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $result = $this->controller->delete();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Test `relatedJson` method
     *
     * @covers ::relatedJson()
     *
     * @return void
     */
    public function testRelatedJson() : void
    {
        // Setup controller for test
        $method = 'getRelated';
        $response = [
            'data' => [
                [
                    'id' => 9991,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'another doc',
                    ],
                ],
                [
                    'id' => 9992,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'again a doc',
                    ],
                ],
            ],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $this->controller->relatedJson(1, 'something');
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($response['data'], $this->controller->viewVars['data']);
    }

    /**
     * Test `updateMediaUrls` method
     *
     * @covers ::updateMediaUrls()
     *
     * @return void
     */
    public function testUpdateMediaUrls() : void
    {
        // Setup controller for test
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $response = [
            'data' => [
                [
                    'id' => 99911,
                    'type' => 'images',
                    'attributes' => [
                        'provider_thumbnail' => 'https://thumb/99911',
                    ],
                ],
                [
                    'id' => 99922,
                    'type' => 'images',
                    'relationships' => [
                        'streams' => [
                            'data' => [
                                [
                                    'id' => '99922999999999',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                '9991' => [
                    'id' => '9991',
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'first doc',
                    ],
                ],
                '9992' => [
                    'id' => '9992',
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'second doc',
                    ],
                ],
                '99922999999999' => [
                    'id' => '99922999999999',
                    'type' => 'streams',
                    'meta' => [
                        'url' => 'https://thumb/99922',
                    ]
                ],
            ],
        ];
        $this->controller->updateMediaUrls($response);
        foreach ($response['data'] as $item) {
            static::assertNotEmpty($item['meta']['url']);
            static::assertEquals(sprintf('https://thumb/%s', $item['id']), $item['meta']['url']);
        }
    }

    /**
     * Test `relationData` method
     *
     * @covers ::relationData()
     *
     * @return void
     */
    public function testRelationData() : void
    {
        // Setup controller for test
        $method = 'relationData';
        $response = [
            'data' => [
                [
                    'id' => 9991,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'another doc',
                    ],
                ],
                [
                    'id' => 9992,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'again a doc',
                    ],
                ],
            ],
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $this->controller->relationData(1, 'something');
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($response['data'], $this->controller->viewVars['data']);
    }

    /**
     * Data provider for `testRelationshipsJson` test case.
     *
     * @return array
     */
    public function relationshipsJsonProvider() : array
    {
        return [
            'children' => [
                'children',
                'objects',
                [
                    'data' => [
                        [
                            'id' => 991,
                            'type' => 'documents',
                            'attributes' => [
                                'title' => 'a doc',
                            ],
                        ],
                        [
                            'id' => 992,
                            'type' => 'documents',
                            'attributes' => [
                                'title' => 'another doc',
                            ],
                        ],
                    ],
                ],
            ],
            'parent' => [ // folders
                'parent',
                'folders',
                [
                    'data' => [
                        [
                            'id' => 9991,
                            'type' => 'folders',
                            'attributes' => [
                                'title' => 'a folder',
                            ],
                        ],
                        [
                            'id' => 9992,
                            'type' => 'folders',
                            'attributes' => [
                                'title' => 'again a folder',
                            ],
                        ],
                    ],
                ],
            ],
            'parents' => [ // folders
                'parents',
                'folders',
                [
                    'data' => [
                        [
                            'id' => 9991,
                            'type' => 'folders',
                            'attributes' => [
                                'title' => 'a folder',
                            ],
                        ],
                        [
                            'id' => 9992,
                            'type' => 'folders',
                            'attributes' => [
                                'title' => 'again a folder',
                            ],
                        ],
                    ],
                ],
            ],
            'dummy' => [
                'dummy',
                'dummies',
                [
                    'data' => [
                        [
                            'id' => 99991,
                            'type' => 'dummies',
                            'attributes' => [
                                'title' => 'a dummy',
                            ],
                        ],
                        [
                            'id' => 99992,
                            'type' => 'dummies',
                            'attributes' => [
                                'title' => 'again a dummy',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `relationshipsJson` method
     *
     * @param string $relation The relation to test
     * @param string $objectType The object type / endpoint
     * @param array $expected The expected data
     *
     * @covers ::relationshipsJson()
     * @dataProvider relationshipsJsonProvider()
     * @return void
     */
    public function testRelationshipsJson(string $relation, string $objectType, array $expected) : void
    {
        // Setup controller for test
        $method = 'get';
        $expected = [
            'data' => [
                [
                    'id' => 9991,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'another doc',
                    ],
                ],
                [
                    'id' => 9992,
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'again a doc',
                    ],
                ],
            ],
        ];
        $id = '123456789';
        $query = [];
        $headers = null;
        $map = [
            [sprintf('/%s', $objectType), $query, $headers, $expected],
        ];
        if ($relation === 'dummy') {
            $map[] = [
                sprintf('/%s/%s/%s', $objectType, $id, $relation),
                ['page_size' => 1],
                $headers,
                [
                    'links' => [
                        'available' => sprintf('/%s', $objectType)
                    ]
                ],
            ];
        }
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($request);
        // do controller call
        $this->controller->relationshipsJson($id, $relation);
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($expected['data'], $this->controller->viewVars['data']);
    }

    /**
     * Data provider for `testUpload` test case.
     *
     * @return array
     */
    public function uploadProvider() : array
    {
        $redir = [
            'unexpected-error' => '/documents/create',
            'validation-error' => '/documents/view/999',
            'ok' => '/documents/view/999',
        ];

        return [
            'file.name empty' => [
                [], // params
                $redir['validation-error'], // redir
                new \RuntimeException('Invalid form data: file.name'), // exception
            ],
            'file.name not a string' => [
                [
                    'file' => ['name' => 12345],
                ],
                $redir['validation-error'],
                new \RuntimeException('Invalid form data: file.name'),
            ],
            'file.tmp_name (filepath) empty' => [
                [
                    'file' => ['name' => 'dummy.txt'],
                ],
                $redir['validation-error'],
                new \RuntimeException('Invalid form data: file.tmp_name'),
            ],
            'file.tmp_name (filepath) not a string' => [
                [
                    'file' => [
                        'name' => 'dummy.txt',
                        'tmp_name' => 12345,
                    ],
                ],
                $redir['validation-error'],
                new \RuntimeException('Invalid form data: file.tmp_name'),
            ],
            'model-type empty' => [
                [
                    'file' => [
                        'name' => 'dummy.txt',
                        'tmp_name' => '/tmp/dummy.txt',
                    ],
                ],
                $redir['validation-error'],
                new \RuntimeException('Invalid form data: model-type'),
            ],
            'model-type not a string' => [
                [
                    'file' => [
                        'name' => 'dummy.txt',
                        'tmp_name' => '/tmp/dummy.txt',
                        'model-type' => 12345,
                    ],
                ],
                $redir['validation-error'],
                new \RuntimeException('Invalid form data: model-type'),
            ],
            'upload ok' => [
                [
                    'file' => [
                        'name' => 'dummy.txt',
                        'tmp_name' => '/tmp/dummy.txt',
                    ],
                    'model-type' => 'dummies',
                ],
                $redir['ok'],
                null,
            ],
        ];
    }

    /**
     * Test `upload` method
     *
     * @param array $params The form params
     * @param string $redir The redir url
     * @param \Exception|null The exception expected on upload test
     *
     * @covers ::upload()
     * @dataProvider uploadProvider()
     * @return void
     */
    public function testUpload(array $params, string $redir, $exception) : void
    {
        // Setup controller for test
        $methods = ['upload', 'createMediaFromStream'];
        $response = ['data' => ['id' => 999]];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $params,
            'params' => ['object_type' => 'documents'],
        ];
        $this->setupController($request);
        // do controller call
        try {
            $result = $this->controller->upload();
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals($redir, $header['Location']);
        } catch (\Exception $e) {
            if ($exception instanceof \Exception) {
                static::assertEquals($exception->getMessage(), $e->getMessage());
            }
        }
    }

    /**
     * Test `changeStatus` method
     *
     * @covers ::changeStatus()
     *
     * @return void
     */
    public function testChangeStatus() : void
    {
        // Setup controller for test
        $method = 'save';
        $response = [
            'data' => [
                'id' => 1,
                'type' => 'documents',
                'attributes' => [
                    'title' => 'test',
                    'status' => 'off',
                ],
            ]
        ];
        $request = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => 1,
                'status' => 'off',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($request);
        // do controller call
        $result = $this->controller->changeStatus();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }
}
