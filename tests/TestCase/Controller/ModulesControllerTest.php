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

use App\Controller\ModulesController;
use BEdita\SDK\BEditaClient;
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
     * The api client
     *
     * @var \BEdita\SDK\BEditaClient The api client
     */
    protected $safeApiClient = null;

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
     * Set api client for test (using mock)
     *
     * @param \BEdita\SDK\BEditaClient $apiClient The api client
     * @return void
     */
    public function setApiClient($apiClient) : void
    {
        $this->apiClient = $apiClient;
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
     * Setup modules controller for test
     *
     * @param array|null $config The config for request
     * @return void
     */
    private function setupController(array $config = []) : void
    {
        $this->setupApi();
        if (empty($config)) {
            $config = [
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'get' => [],
            ];
        }
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi() : void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
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
        $objectType = 'documents';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
        static::assertEquals($objectType, $this->controller->getObjectType());
        static::assertEquals($objectType, $this->controller->Modules->getConfig('currentModuleName'));
        static::assertEquals($objectType, $this->controller->Schema->getConfig('type'));
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
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('getObjects')
            ->willReturn([
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
            ]);
        ApiClientProvider::setApiClient($apiClient);
        // create controller with mock api client
        $this->controller = new ModulesControllerSample(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'get' => [],
            ])
        );
        $this->controller->setApiClient($apiClient);
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
        $this->setupController();
        $response = $this->client->get('/documents', ['page' => 1, 'page_size' => 5]);
        foreach ($response['data'] as $object) {
            // test 200 OK
            $this->setupController(); // renew controller
            $this->controller->objectType = $object['type'];
            $result = $this->controller->view($object['id']);
            static::assertNull($result);
            static::assertEquals(200, $this->controller->response->statusCode());
            static::assertEquals('text/html', $this->controller->response->type());
        }
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
        $this->setupController();
        $response = $this->client->get('/objects', ['page' => 1, 'page_size' => 5]);
        foreach ($response['data'] as $object) {
            $this->setupController(); // renew controller
            // by id
            $result = $this->controller->uname($object['id']);
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals(sprintf('/%s/view/%s', $object['type'], $object['id']), $header['Location']);
            // by uname
            $result = $this->controller->uname($object['attributes']['uname']);
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals(sprintf('/%s/view/%s', $object['type'], $object['id']), $header['Location']);
        }
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
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'GET']]);
        $this->controller = new ModulesController();
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
        $objectType = 'documents';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
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
        $this->setupController();
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
        $objectType = 'documents';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'title' => 'sample',
            ],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
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
        $id = $this->createDocument();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => $id,
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($config);
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
        $objectType = 'documents';
        $id = $this->createDocument();
        $relation = 'download';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
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
        // Setup mock API client.
        $this->safeApiClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('getRelated')
            ->willReturn($expected);
        ApiClientProvider::setApiClient($apiClient);
        $this->controller->setApiClient($apiClient);
        // do controller call
        $this->controller->relatedJson($id, $relation);
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($expected['data'], $this->controller->viewVars['data']);
        ApiClientProvider::setApiClient($this->safeApiClient);
        $this->deleteDocument($id);
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
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($config);
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
        $objectType = 'documents';
        $id = $this->createDocument();
        $relation = 'download';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
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
        // Setup mock API client.
        $this->safeApiClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('relationData')
            ->willReturn($expected);
        ApiClientProvider::setApiClient($apiClient);
        $this->controller->setApiClient($apiClient);
        // do controller call
        $this->controller->relationData($id, $relation);
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($expected['data'], $this->controller->viewVars['data']);
        ApiClientProvider::setApiClient($this->safeApiClient);
        $this->deleteDocument($id);
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
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
        // Setup mock API client.
        $this->safeApiClient = ApiClientProvider::getApiClient();
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
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->will($this->returnValueMap($map));
        ApiClientProvider::setApiClient($apiClient);
        $this->controller->setApiClient($apiClient);
        // do controller call
        $this->controller->relationshipsJson($id, $relation);
        foreach (['_serialize', 'data'] as $var) {
            static::assertNotEmpty($this->controller->viewVars[$var]);
        }
        static::assertEquals($expected['data'], $this->controller->viewVars['data']);
        ApiClientProvider::setApiClient($this->safeApiClient);
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
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $params,
            'params' => ['object_type' => 'documents'],
        ];
        $this->setupController($config);
        // Setup mock API client.
        $this->safeApiClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('upload')
            ->willReturn(['data' => ['id' => 999]]);
        $apiClient->method('createMediaFromStream')
            ->willReturn(['data' => ['id' => 999]]);
        ApiClientProvider::setApiClient($apiClient);
        $this->controller->setApiClient($apiClient);
        try {
            // do controller call
            $result = $this->controller->upload();
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals($redir, $header['Location']);
            ApiClientProvider::setApiClient($this->safeApiClient);
        } catch (\Exception $e) {
            ApiClientProvider::setApiClient($this->safeApiClient);
            $this->controller->setApiClient($this->safeApiClient);
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
        $this->setupController();
        $id = $this->createDocument();
        $expectedStatus = 'off';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $id,
                'status' => $expectedStatus,
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $this->setupController($config);
        $result = $this->controller->save();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
        $response = $this->client->get(sprintf('/documents/%s', $id));
        $status = $response['data']['attributes']['status'];
        static::assertEquals($status, $expectedStatus);
        $this->deleteDocument($id);
    }

    /**
     * Create a new document and return ID
     *
     * @return string The new document ID
     */
    private function createDocument() : string
    {
        $objectType = 'documents';
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ];
        $this->setupController($config);
        $response = $this->client->save('documents', [
            'title' => 'sample DOC',
            'status' => 'draft',
        ]);

        return $response['data']['id'];
    }

    /**
     * Delete the document by ID
     *
     * @param string|int $id The document ID
     * @return void
     */
    private function deleteDocument($id) : void
    {
        $this->setupController();
        $this->client->delete(sprintf('/documents/%s', $id));
        $this->client->delete(sprintf('/trash/%s', $id));
    }
}
