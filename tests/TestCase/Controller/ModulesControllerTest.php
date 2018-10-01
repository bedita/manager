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
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
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
     * Public version of parent function (protected) descendants
     *
     * @return void
     */
    public function descendants() : array
    {
        return parent::descendants();
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
            'REQUEST_METHOD' => 'GET',
        ],
        'get' => [],
        'params' => [
            'object_type' => 'documents',
        ],
    ];

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
     * Setup controller to test with request config
     *
     * @param array|null $requestConfig
     * @return void
     */
    protected function setupController(?array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);
        // force modules load
        $this->controller->Auth->setUser(['id' => 'dummy']);
        $this->controller->Modules->startup();
        $this->setupApi();
        $this->createTestObject();
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
        $this->setupController(); // it already calls initialize, internally
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
        $this->setupController();

        // do controller call
        $result = $this->controller->index();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());

        // verify expected vars in view
        $this->assertExpectedViewVars(['objects', 'meta', 'links', 'types', 'properties']);
    }

    /**
     * Test `descendants` method on concrete type
     *
     * @covers ::descendants()
     *
     * @return void
     */
    public function testEmptyDescendants() : void
    {
        // Setup controller for test / on documents
        $this->setupController();

        // do controller call
        $result = $this->controller->descendants();

        // verify response status code and type
        static::assertEmpty($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());
    }

    /**
     * Test `descendants` method on abstract type
     *
     * @covers ::descendants()
     *
     * @return void
     */
    public function testDescendants() : void
    {
        // Setup controller with `objects` as type
        $this->setupController([
            'params' => [
                'object_type' => 'objects',
            ],
        ]);

        $result = $this->controller->descendants();

        // verify response status code and type
        static::assertNotEmpty($result);
        static::assertEquals(200, $this->controller->response->statusCode());
    }

    /**
     * Test `descendants` method on missing type
     *
     * @covers ::descendants()
     *
     * @return void
     */
    public function testWrongDescendants() : void
    {
        // Setup controller with `objects` as type
        $this->setupController([
            'params' => [
                'object_type' => 'gustavos',
            ],
        ]);

        $result = $this->controller->descendants();

        // verify response status code and type
        static::assertEmpty($result);
        static::assertEquals(200, $this->controller->response->statusCode());
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
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->view($id);

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->statusCode());
        static::assertEquals('text/html', $this->controller->response->type());

        // verify expected vars in view
        $this->assertExpectedViewVars(['object', 'included', 'schema', 'properties', 'relations']);
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
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->uname($id);

        // verify response status code and type
        $header = $result->header();
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
        static::assertEquals(sprintf('/%s/view/%s', $this->controller->getObjectType(), $id), $header['Location']);
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
        $exception = new BEditaClientException('Not Found', 404);
        $this->setupController();

        // do controller call
        $result = $this->controller->uname('just-a-wrong-uname');

        // verify response status code and type
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
        $this->setupController();

        // do controller call
        $this->controller->create();

        // verify expected vars in view
        $this->assertExpectedViewVars(['object', 'schema', 'properties']);
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
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'objects',
            ],
        ]);

        // do controller call
        $result = $this->controller->create();

        // verify response status code and type
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
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $o['id'],
                'title' => $o['attributes']['title'],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $result = $this->controller->save();

        // verify response status code and type
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
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $o['id'],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $result = $this->controller->delete();

        // verify response status code and type
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());

        // restore test object
        $this->restoreTestObject($o['id'], 'documents');
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
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $this->controller->relatedJson($id, 'translations');

        // verify expected vars in view
        $this->assertExpectedViewVars(['_serialize', 'data']);
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
        $this->setupController();

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
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $this->controller->relationData($id, 'translations');

        // verify expected vars in view
        $this->assertExpectedViewVars(['_serialize']);
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
            ],
            'parent' => [
                'parent',
                'folders',
            ],
            'parents' => [
                'parents',
                'folders',
            ],
            'document' => [
                'translations',
                'documents',
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
    public function testRelationshipsJson(string $relation, string $objectType) : void
    {
        // Setup controller for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => $objectType,
            ],
        ]);

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $this->controller->relationshipsJson($id, $relation);

        // verify expected vars in view
        $this->assertExpectedViewVars(['_serialize']);
    }

    /**
     * Data provider for `testUpload` test case.
     *
     * @return array
     */
    public function uploadProvider() : array
    {
        return [
            'file.name empty' => [
                [], // params
                '/documents/create', // redir
                new \RuntimeException('Invalid form data: file.name'), // exception
            ],
            'file.name not a string' => [
                [
                    'file' => ['name' => 12345],
                ],
                '/documents/create',
                new \RuntimeException('Invalid form data: file.name'),
            ],
            'file.tmp_name (filepath) empty' => [
                [
                    'file' => ['name' => 'dummy.txt'],
                ],
                '/documents/create',
                new \RuntimeException('Invalid form data: file.tmp_name'),
            ],
            'file.tmp_name (filepath) not a string' => [
                [
                    'file' => [
                        'name' => 'dummy.txt',
                        'tmp_name' => 12345,
                    ],
                ],
                '/documents/create',
                new \RuntimeException('Invalid form data: file.tmp_name'),
            ],
            'model-type empty' => [
                [
                    'file' => [
                        'name' => 'test.png',
                        'tmp_name' => getcwd() . '/tests/files/test.png',
                    ],
                ],
                '/documents/create',
                new \RuntimeException('Invalid form data: model-type'),
            ],
            'model-type not a string' => [
                [
                    'file' => [
                        'name' => 'test.png',
                        'tmp_name' => getcwd() . '/tests/files/test.png',
                    ],
                    'model-type' => '12345',
                ],
                '/documents/create',
                new \RuntimeException('Invalid form data: model-type'),
            ],
            'upload ok' => [
                [
                    'file' => [
                        'name' => 'test.png',
                        'tmp_name' => getcwd() . '/tests/files/test.png',
                    ],
                    'model-type' => 'documents',
                ],
                '/documents/create',
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
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $params,
            'params' => ['object_type' => 'documents'],
        ]);

        try {
            // do controller call
            $result = $this->controller->upload();

            // verify response status code and type
            $header = $result->header();
            static::assertEquals(302, $result->statusCode());
            static::assertEquals('text/html', $result->type());
            static::assertEquals($redir, $header['Location']);
        } catch (\Exception $e) {
            static::assertEquals($exception->getMessage(), $e->getMessage());
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
        $this->setupController([]);

        // get object for test
        $o = $this->getTestObject();

        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $o['id'],
                'status' => $o['attributes']['status'],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->changeStatus();

        // verify response status code and type
        static::assertEquals(302, $result->statusCode());
        static::assertEquals('text/html', $result->type());
    }

    /**
     * Get test object id
     *
     * @return void
     */
    private function getTestId()
    {
        // call index and get first available object, for test view
        $o = $this->getTestObject();

        return $o['id'];
    }

    /**
     * Get an object for test purposes
     *
     * @return array
     */
    private function getTestObject()
    {
        $response = $this->client->getObjects('documents', ['filter' => ['uname' => 'modules-controller-test-document']]);

        if (!empty($response['data'][0])) {
            return $response['data'][0];
        }

        return null;
    }

    /**
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    private function createTestObject()
    {
        $o = $this->getTestObject();
        if ($o == null) {
            $response = $this->client->save('documents', ['title' => 'modules controller test document', 'uname' => 'modules-controller-test-document']);
            $o = $response['data'];
        }

        return $o;
    }

    /**
     * Restore object by id
     *
     * @param string|int $id The object ID
     * @param string $type The object type
     * @return void
     */
    private function restoreTestObject($id, $type)
    {
        $o = $this->getTestObject();
        if ($o == null) {
            $response = $this->client->restoreObject($id, $type);
        }
    }

    /**
     * Verify existence of vars in controller view
     *
     * @param array $expected The expected vars in view
     * @return void
     */
    private function assertExpectedViewVars($expected)
    {
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $this->controller->viewVars);
        }
    }
}
