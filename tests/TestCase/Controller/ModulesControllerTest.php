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

use App\Controller\Component\ModulesComponent;
use App\Controller\Component\SchemaComponent;
use App\Test\Utils\ModulesControllerSample;
use App\Utility\CacheTools;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\ModulesController} Test Case
 *
 * @coversDefaultClass \App\Controller\ModulesController
 */
class ModulesControllerTest extends BaseControllerTest
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
    }

    /**
     * Test Modules controller
     *
     * @var \App\Test\Utils\ModulesControllerSample
     */
    public $controller;

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
        // Mock Authentication component
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity(new Identity(['id' => 'dummy']));
        // Mock GET /config using cache
        Cache::write(CacheTools::cacheKey('config.Modules'), []);
        $this->controller->Modules->startup();
        $this->setupApi();
        $this->createTestObject();
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }

    /**
     * Test `initialize` method
     *
     * @covers ::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        // Mock GET /config using cache
        Cache::write(CacheTools::cacheKey('config.AlertMessage'), []);
        Cache::write(CacheTools::cacheKey('config.Project'), []);
        Cache::write(CacheTools::cacheKey('config.Pagination'), []);

        // Setup controller for test
        $this->setupController(); // it already calls initialize, internally
        static::assertEquals('documents', $this->controller->getObjectType());
        static::assertEquals('documents', $this->controller->Modules->getConfig('currentModuleName'));
        static::assertEquals('documents', $this->controller->Schema->getConfig('type'));
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $result = $this->controller->index();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->controller->getResponse()->getType());

        // verify expected vars in view
        $this->assertExpectedViewVars(['objects', 'meta', 'links', 'types', 'properties']);
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndexResetRequest(): void
    {
        // Setup controller for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $request = $request->withQueryParams(['reset' => '1']);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $result = $this->controller->index();

        // verify response status code and type
        static::assertNotNull($result);
        static::assertEquals(302, $this->controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->controller->getResponse()->getType());
    }

    /**
     * Test `index` method with query string error
     * Session filter data must be empty
     *
     * @covers ::index()
     * @return void
     */
    public function testQueryErrorSession(): void
    {
        // Setup controller for test with query string error
        $this->setupController([
            'query' => ['sort' => 'bad_attribute'],
        ]);

        // do controller call
        $result = $this->controller->index();

        // verify response status code and type
        static::assertNotNull($result);
        static::assertEquals(302, $this->controller->getResponse()->getStatusCode());

        // verify session filter is empty
        $filter = $this->controller->getRequest()->getSession()->read('documents.filter');
        static::assertNull($filter);
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     * @covers ::setupViewRelations()
     * @return void
     */
    public function testView(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->view($id);

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->controller->getResponse()->getType());

        // verify expected vars in view
        $this->assertExpectedViewVars(['object', 'included', 'schema', 'properties', 'objectRelations']);
    }

    /**
     * Test `view` method on error
     *
     * @covers ::view()
     * @return void
     */
    public function testViewError(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $response = $this->controller->view(123456789);

        // verify response status code and type
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('text/html', $response->getType());
    }

    /**
     * Test `uname` method
     *
     * @covers ::uname()
     * @return void
     */
    public function testUname(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->uname($id);

        // verify response status code and type
        $locationHeader = $result->getHeaderLine('Location');
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
        static::assertEquals(sprintf('/%s/view/%s', $this->controller->getObjectType(), $id), $locationHeader);
    }

    /**
     * Test `uname` method, case 404 Not Found
     *
     * @covers ::uname()
     * @return void
     */
    public function testUname404(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $result = $this->controller->uname('just-a-wrong-uname');

        // verify response status code and type
        $locationHeader = $result->getHeaderLine('Location');
        static::assertEquals(302, $result->getStatusCode()); // redir to referer
        static::assertEquals('/', $locationHeader);
    }

    /**
     * Test `create` method
     *
     * @covers ::create()
     * @covers ::setupViewRelations()
     * @return void
     */
    public function testCreate(): void
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
     * @return void
     */
    public function testCreate302(): void
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
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `clone` method
     *
     * @covers ::clone()
     * @return void
     */
    public function testClone(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->clone($id);

        // verify response status code and type
        static::assertNotNull($result);
        static::assertEquals(302, $this->controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->controller->getResponse()->getType());
    }

    /**
     * Test `clone` method
     *
     * @covers ::clone()
     * @return void
     */
    public function testClone302(): void
    {
        // test 1 clone abstract type

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

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->clone($id);

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // test 2 clone object by ID that doesn't exist
        $id = 999999999999;

        // do controller call
        $result = $this->controller->clone($id);

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `clone` method, on error
     *
     * @covers ::clone()
     * @return void
     */
    public function testCloneError(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $response = $this->controller->clone(123456789);

        // verify response status code and type
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('text/html', $response->getType());
    }

    /**
     * Test `save` method, on error
     *
     * @covers ::save()
     * @return void
     */
    public function testSaveErrorNoPost(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $this->controller->save();

        // verify page has error key
        static::assertArrayHasKey('error', $this->controller->viewBuilder()->getVars());
    }

    /**
     * Test `save` method, on error
     *
     * @covers ::save()
     * @return void
     */
    public function testSaveErrorPostId(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => 123456789,
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $this->controller->save();

        // verify page has error key
        static::assertArrayHasKey('error', $this->controller->viewBuilder()->getVars());
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider(): array
    {
        return [
            'save' => [
                [
                    'code' => 200,
                    'message' => 'OK',
                ],
                [
                    'params' => [
                        'object_type' => 'images',
                    ],
                    'body' => [
                        'title' => 'bibo',
                    ],
                ],
            ],
            'exception' => [
                [
                    'code' => 404,
                    'message' => 'Not Found',
                ],
                [
                    'params' => [
                        'object_type' => 'drago',
                    ],
                    'body' => [
                        'title' => 'bibo',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `save` method
     *
     * @dataProvider saveProvider()
     * @covers ::save()
     * @return void
     */
    public function testSave($expected, $data): void
    {
        // Setup controller for test
        $this->setupController();

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $data['body'],
            'params' => $data['params'],
        ];

        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $this->controller->save();

        // verify response status code and type
        $result = $this->controller->getApiClient();
        static::assertEquals($expected['code'], $result->getStatusCode());
        static::assertEquals($expected['message'], $result->getStatusMessage());
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     * @return void
     */
    public function testDelete(): void
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
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // restore test object
        $this->restoreTestObject($o['id'], 'documents');
    }

    /**
     * Test `delete` method, ids
     *
     * @covers ::delete()
     * @return void
     */
    public function testDeleteIds(): void
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
                'ids' => (string)Hash::get($o, 'id'),
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
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // restore test object
        $this->restoreTestObject($o['id'], 'documents');
    }

    /**
     * Test `delete` method, on error
     *
     * @covers ::delete()
     * @return void
     */
    public function testDeleteError(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => '123456789',
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
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // test by ids
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => '123456789',
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
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `related` method
     *
     * @covers ::related()
     * @return void
     */
    public function testRelated(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $this->controller->related($id, 'translations');

        // verify expected vars in view
        $this->assertExpectedViewVars(['data']);
        static::assertNotEmpty($this->controller->viewBuilder()->getOption('serialize'));
    }

    /**
     * Test `related` method on `new` object
     *
     * @covers ::related()
     * @return void
     */
    public function testRelatedNew(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $this->controller->related('new', 'has_media');

        static::assertEquals([], $this->controller->viewBuilder()->getVar('data'));
    }

    /**
     * Test `related` method, on error
     *
     * @covers ::related()
     * @return void
     */
    public function testRelatedError(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $this->controller->related(12346789, 'translations');

        // verify expected vars in view
        $this->assertExpectedViewVars(['error']);
        static::assertNotEmpty($this->controller->viewBuilder()->getOption('serialize'));
    }

    /**
     * Test `resources` method
     *
     * @covers ::resources()
     * @return void
     */
    public function testResources(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $this->controller->resources($id, 'documents');

        // verify expected vars in view
        $this->assertExpectedViewVars(['data']);
        static::assertNotEmpty($this->controller->viewBuilder()->getOption('serialize'));
    }

    /**
     * Test `resources` method
     *
     * @covers ::resources()
     * @return void
     */
    public function testResourcesError(): void
    {
        // Setup controller for test
        $this->setupController();

        // do controller call
        $this->controller->resources(123456789, 'dummies');

        // verify expected vars in view
        $this->assertExpectedViewVars(['error']);
        static::assertNotEmpty($this->controller->viewBuilder()->getOption('serialize'));
    }

    /**
     * Data provider for `testRelationships` test case.
     *
     * @return array
     */
    public function relationshipsProvider(): array
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
     * Test `relationships` method
     *
     * @param string $relation The relation to test
     * @param string $objectType The object type / endpoint
     * @covers ::relationships()
     * @dataProvider relationshipsProvider()
     * @return void
     */
    public function testRelationships(string $relation, string $objectType): void
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
        $this->controller->relationships($id, $relation);

        // verify expected vars in view
        static::assertNotEmpty($this->controller->viewBuilder()->getOption('serialize'));
    }

    /**
     * Test `getSchemaForIndex` method with errors
     *
     * @covers ::getSchemaForIndex()
     * @return void
     */
    public function testGetSchemaForIndex(): void
    {
        $type = 'documents';
        $mockResponse = [
            'properties' => [
                'enum_prop' => [
                    'type' => 'string',
                    'enum' => [
                        'enum1',
                        'enum2',
                        'enum3',
                    ],
                ],
            ],
        ];
        $expected = [
            'properties' => [
                'enum_prop' => [
                    'type' => 'string',
                    'enum' => [
                        '',
                        'enum1',
                        'enum2',
                        'enum3',
                    ],
                ],
            ],
        ];

        $this->setupController();
        $this->controller->Schema = $this->createMock(SchemaComponent::class);
        $this->controller->Schema->method('getSchema')
            ->with($type)
            ->willReturn($mockResponse);

        $actual = $this->controller->getSchemaForIndex($type);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test `availableRelationshipsUrl` method
     *
     * @covers ::availableRelationshipsUrl()
     * @return void
     */
    public function testAvailableRelationshipsUrl(): void
    {
        $this->setupController();
        $url = $this->controller->availableRelationshipsUrl('children');
        static::assertEquals('/objects', $url);

        $this->controller->Modules = $this->createMock(ModulesComponent::class);
        $this->controller->Modules->method('relatedTypes')
            ->willReturn(['documents']);

        $url = $this->controller->availableRelationshipsUrl('test_relation');
        static::assertEquals('/documents', $url);

        $this->controller->Modules = $this->createMock(ModulesComponent::class);
        $this->controller->Modules->method('relatedTypes')
            ->willReturn(['images', 'profiles']);

        $url = $this->controller->availableRelationshipsUrl('test_relation');
        static::assertEquals('/objects?filter[type][]=images&filter[type][]=profiles', $url);
    }

    /**
     * Verify existence of vars in controller view
     *
     * @param array $expected The expected vars in view
     * @return void
     */
    private function assertExpectedViewVars($expected): void
    {
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $this->controller->viewBuilder()->getVars());
        }
    }

    /**
     * Test `getObjectType` and `setObjectType`.
     *
     * @return void
     * @covers ::getObjectType()
     * @covers ::setObjectType()
     */
    public function testGetSetObjectType(): void
    {
        // Setup controller for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $expected = 'dummies';
        $this->controller->setObjectType($expected);
        $actual = $this->controller->getObjectType();
        static::assertSame($expected, $actual);
    }
}
