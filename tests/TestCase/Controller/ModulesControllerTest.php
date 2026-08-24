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
use App\Controller\ModulesController;
use App\Test\Utils\ModulesControllerSample;
use App\Utility\CacheTools;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\ModulesController} Test Case
 */
#[CoversClass(ModulesController::class)]
#[CoversMethod(ModulesController::class, 'availableRelationshipsUrl')]
#[CoversMethod(ModulesController::class, 'clone')]
#[CoversMethod(ModulesController::class, 'create')]
#[CoversMethod(ModulesController::class, 'delete')]
#[CoversMethod(ModulesController::class, 'get')]
#[CoversMethod(ModulesController::class, 'getObjectType')]
#[CoversMethod(ModulesController::class, 'getSchemaForIndex')]
#[CoversMethod(ModulesController::class, 'handleError')]
#[CoversMethod(ModulesController::class, 'index')]
#[CoversMethod(ModulesController::class, 'initialize')]
#[CoversMethod(ModulesController::class, 'relationships')]
#[CoversMethod(ModulesController::class, 'related')]
#[CoversMethod(ModulesController::class, 'resources')]
#[CoversMethod(ModulesController::class, 'save')]
#[CoversMethod(ModulesController::class, 'setObjectType')]
#[CoversMethod(ModulesController::class, 'setup')]
#[CoversMethod(ModulesController::class, 'setupViewRelations')]
#[CoversMethod(ModulesController::class, 'uname')]
#[CoversMethod(ModulesController::class, 'users')]
#[CoversMethod(ModulesController::class, 'view')]
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
    public ModulesControllerSample $controller;

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
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }
        };
        // Mock Authentication component
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity(new Identity(['id' => 'dummy']));
        // Mock GET /config using cache
        Cache::write(CacheTools::cacheKey('config.Modules'), []);
        $this->controller->Modules->beforeFilter(new Event('Module.beforeFilter'));
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
     * @return void
     */
    public function testClone(): void
    {
        // Setup controller for test
        $this->setupController([
            'query' => ['relationships' => 'true', 'translations' => 'true'],
        ]);
        Configure::write('Clone.documents.reset', ['body']);

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
     * @return void
     */
    public function testCloneMedia(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $media = $this->createTestMedia();
        $id = (string)Hash::get($media, 'id');

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
     * Test `save` method when uname is numeric
     *
     * @return void
     */
    public function testSaveUnameNumeric(): void
    {
        // Setup controller for test
        $this->setupController();

        $o = $this->getTestObject();
        $id = (string)Hash::get($o, 'id');
        $type = (string)Hash::get($o, 'type');

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $id,
                'uname' => '123456789',
            ],
            'params' => [
                'object_type' => $type,
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $this->controller->save();

        // verify page has data, meta and links keys
        $vars = $this->controller->viewBuilder()->getVars();
        static::assertArrayHasKey('error', $vars);
        static::assertEquals('Invalid numeric uname. Change it to a valid string', $vars['error']);
    }

    /**
     * Test `save` method when there's only 'id' in post data
     *
     * @return void
     */
    public function testSkipSave(): void
    {
        // Setup controller for test
        $this->setupController();

        $o = $this->getTestObject();
        $id = (string)Hash::get($o, 'id');
        $type = (string)Hash::get($o, 'type');

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $id,
            ],
            'params' => [
                'object_type' => $type,
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new ModulesControllerSample($request);

        // do controller call
        $this->controller->save();

        // verify page has data, meta and links keys
        $vars = $this->controller->viewBuilder()->getVars();
        static::assertArrayHasKey('data', $vars);
        static::assertArrayHasKey('meta', $vars);
        static::assertArrayHasKey('links', $vars);
    }

    /**
     * Test `save` method, on error
     *
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
    public static function saveProvider(): array
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
     * @return void
     */
    #[DataProvider('saveProvider')]
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
        $result = $this->controller->apiClient;
        static::assertEquals($expected['code'], $result->getStatusCode());
        static::assertEquals($expected['message'], $result->getStatusMessage());
    }

    /**
     * Test `delete` method
     *
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

        $this->controller->apiClient = new class ('https://api.example.com') extends BEditaClient {
            public function delete(string $path, ?string $body = null, ?array $headers = null): ?array
            {
                throw new BEditaClientException('Error');
            }
        };

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
    public static function relationshipsProvider(): array
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
     * @return void
     */
    #[DataProvider('relationshipsProvider')]
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
     * @return void
     */
    public function testGetSchemaForIndex(): void
    {
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
        $controller = new class ($this->controller->getRequest()) extends ModulesController {
            public object $Schema;
            public function initialize(): void
            {
                $this->Schema = new class (new ComponentRegistry($this)) extends SchemaComponent {
                    public function getSchema(?string $type = null, ?string $revision = null): array|bool
                    {
                        return [
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
                    }
                };
                parent::initialize();
            }
        };
        $actual = $controller->getSchemaForIndex('documents');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `availableRelationshipsUrl` method
     *
     * @return void
     */
    public function testAvailableRelationshipsUrl(): void
    {
        $this->setupController();
        $url = $this->controller->availableRelationshipsUrl('children');
        static::assertEquals('/objects', $url);
        $controller = new class ($this->controller->getRequest()) extends ModulesController {
            public object $Modules;

            public function initialize(): void
            {
                $this->Modules = new class (new ComponentRegistry($this)) extends ModulesComponent {
                    public function relatedTypes(array $schema, string $relation): array
                    {
                        return ['documents'];
                    }
                };
                parent::initialize();
            }

            public function availableRelationshipsUrl(string $relation): string
            {
                return parent::availableRelationshipsUrl($relation);
            }
        };

        $url = $controller->availableRelationshipsUrl('test_relation');
        static::assertEquals('/documents', $url);
    }

    /**
     * Test `availableRelationshipsUrl` method
     *
     * @return void
     */
    public function testAvailableRelationshipsUrlMulti(): void
    {
        $this->setupController();
        $controller = new class ($this->controller->getRequest()) extends ModulesController {
            public object $Modules;

            public function initialize(): void
            {
                $this->Modules = new class (new ComponentRegistry($this)) extends ModulesComponent {
                    public function relatedTypes(array $schema, string $relation): array
                    {
                        return ['images', 'profiles'];
                    }
                };
                parent::initialize();
            }

            public function availableRelationshipsUrl(string $relation): string
            {
                return parent::availableRelationshipsUrl($relation);
            }
        };
        $url = $controller->availableRelationshipsUrl('test_relation');
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

    /**
     * Test list users
     *
     * @return void
     */
    public function testListUsers(): void
    {
        $this->setupController();
        // Get list of users / no email, no relationships, no links, no schema, no included.
        $this->controller->users();
        $actual = $this->controller->viewBuilder()->getVars();
        // check data has only id,title,username,name,surname
        $data = $actual['data'];
        foreach ($data as $item) {
            $itemKeys = array_keys($item);
            sort($itemKeys);
            static::assertSame(['attributes', 'id', 'meta', 'type'], $itemKeys);
            $keys = array_keys($item['attributes']);
            sort($keys);
            static::assertSame($keys, ['name', 'surname', 'title', 'username']);
        }
    }

    /**
     * Test get single resource minimal data
     *
     * @return void
     */
    public function testResourceGet(): void
    {
        $this->setupController();
        // get object ID for test
        $id = $this->getTestId();
        // Get single resource, minimal data / no relationships, no links, no schema, no included.
        $this->controller->get($id);
        $actual = $this->controller->viewBuilder()->getVars();
        // check data has only id,title,description,uname,status
        $item = $actual['data'];
        $itemKeys = array_keys($item);
        sort($itemKeys);
        static::assertSame(['attributes', 'id', 'type'], $itemKeys);
        $keys = array_keys($item['attributes']);
        sort($keys);
        static::assertSame($keys, ['description', 'status', 'title', 'uname']);
    }

    /**
     * Test setup method with an unauthorized user
     *
     * @return void
     */
    public function testSetupUnauthorized(): void
    {
        $expected = new UnauthorizedException(__('You are not authorized to access here'));
        $this->expectException(get_class($expected));
        $this->expectExceptionCode($expected->getCode());
        $this->expectExceptionMessage($expected->getMessage());
        $this->setupController();
        $this->controller->setup();
    }

    /**
     * Test setup method with an authorized user
     *
     * @return void
     */
    public function testSetupAuthorized(): void
    {
        $this->setupController();
        $user = new Identity([
            'id' => 1,
            'username' => 'admin',
            'roles' => ['admin'],
        ]);
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity($user);
        $actual = $this->controller->setup();
        static::assertNull($actual);
    }

    /**
     * Test setup method with an authorized user and save
     *
     * @return void
     */
    public function testSetupSave(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'configurationKey' => 'Modules.dummies',
                'shortLabel' => 'dum',
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ];
        $this->setupController($config);
        $user = new Identity([
            'id' => 1,
            'username' => 'admin',
            'roles' => ['admin'],
        ]);
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity($user);
        $actual = $this->controller->setup();
        static::assertNull($actual);
        $actual = $this->controller->viewBuilder()->getVar('response');
        static::assertSame('Configuration saved', $actual);
    }

    /**
     * Test setup method with an authorized user and save error
     *
     * @return void
     */
    public function testSetupSaveError(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'configurationKey' => 'WrongKey.dummies',
                'shortLabel' => 'dum',
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ];
        $this->setupController($config);
        $user = new Identity([
            'id' => 1,
            'username' => 'admin',
            'roles' => ['admin'],
        ]);
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity($user);
        $actual = $this->controller->setup();
        static::assertNull($actual);
        $actual = $this->controller->viewBuilder()->getVar('error');
        static::assertSame('Bad configuration key "WrongKey"', $actual);
    }

    /**
     * Test `save` method, skip save when no post data is provided
     *
     * @return void
     */
    public function testSkipSaveObject(): void
    {
        // Setup controller for test
        $this->setupController();

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
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }
        };

        // mock api client save... and check it's not called
        $apiClient = new class ('https://api.example.com') extends BEditaClient
        {
            protected bool $load = false;
            protected bool $save = false;

            public function getLoad(): bool
            {
                return $this->load;
            }

            public function getSave(): bool
            {
                return $this->save;
            }

            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                $this->load = true;

                return [];
            }

            public function save(string $type, array $data, ?array $headers = null): ?array
            {
                $this->save = true;

                return null;
            }
        };
        $this->controller->setApiClient($apiClient);

        // do controller call
        $this->controller->save();

        static::assertFalse($apiClient->getSave(), 'ApiClient save method should not be called when no post data is provided');
        static::assertTrue($apiClient->getLoad(), 'ApiClient load method should be called to load object');
    }

    /**
     * Test `save` method when permissions are provided
     *
     * @return void
     */
    public function testSavePermissions(): void
    {
        // Setup controller for test
        $this->setupController();

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => 123456789,
                'permissions' => ['1', '2', '3'],
            ],
            'params' => [
                'object_type' => 'folders',
            ],
        ];

        $request = new ServerRequest($config);
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public bool $savePerms = false;

            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }

            public function savePermissions(array $response, array $schema, array $newPermissions): bool
            {
                $this->savePerms = true;

                return true;
            }
        };

        // mock api client save... and check it's not called
        $apiClient = new class ('https://api.example.com') extends BEditaClient
        {
            protected bool $load = false;
            protected bool $save = false;

            public function getLoad(): bool
            {
                return $this->load;
            }

            public function getSave(): bool
            {
                return $this->save;
            }

            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                $this->load = true;

                return ['data' => ['id' => 123456789]];
            }

            public function save(string $type, array $data, ?array $headers = null): ?array
            {
                $this->save = true;

                return [];
            }
        };
        $this->controller->setApiClient($apiClient);

        // mock schema getSchema to return ['associations' => ['Permissions']]
        $registry = $this->controller->components();
        $schemaComponent = new class ($registry) extends SchemaComponent
        {
            public function getSchema(?string $type = null, ?string $revision = null): array|bool
            {
                return ['associations' => ['Permissions']];
            }
        };
        $this->controller->Schema = $schemaComponent;

        // do controller call
        $this->controller->save();

        static::assertTrue($this->controller->savePerms, 'Controller save permissions should be called');
        static::assertFalse($apiClient->getSave(), 'ApiClient save method should not be called when no post data is provided');
        static::assertTrue($apiClient->getLoad(), 'ApiClient load method should be called to load object');
    }

    /**
     * Test `save` method, when related data is provided
     *
     * @return void
     */
    public function testSaveRelated(): void
    {
        // Setup controller for test
        $this->setupController();

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => 123456789,
                '_api' => [['method' => 'addRelated', 'type' => 'dummies', 'relatedIds' => [['id' => 1, 'type' => 'dummies'], ['id' => 2, 'type' => 'dummies']]]],
            ],
            'params' => [
                'object_type' => 'folders',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }
        };

        $registry = $this->controller->components();
        $component = new class ($registry) extends ModulesComponent
        {
            public bool $saveRelated = false;

            public function getSaveRelated(): bool
            {
                return $this->saveRelated;
            }

            public function saveRelated(string $id, string $type, array $relatedData): void
            {
                $this->saveRelated = true;
            }
        };
        $this->controller->Modules = $component;

        // mock api client save... and check it's not called
        $apiClient = new class ('https://api.example.com') extends BEditaClient
        {
            protected bool $load = false;
            protected bool $save = false;

            public function getLoad(): bool
            {
                return $this->load;
            }

            public function getSave(): bool
            {
                return $this->save;
            }

            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                $this->load = true;

                return ['data' => ['id' => 123456789]];
            }

            public function save(string $type, array $data, ?array $headers = null): ?array
            {
                $this->save = true;

                return [];
            }
        };
        $this->controller->setApiClient($apiClient);

        // do controller call
        $this->controller->save();

        static::assertTrue($component->getSaveRelated(), 'Component save related should be called');
        static::assertFalse($apiClient->getSave(), 'ApiClient save method should not be called when no post data is provided');
        static::assertTrue($apiClient->getLoad(), 'ApiClient load method should be called to load object');
    }

    /**
     * Test `save` method, skip save when no post 'permissions' data is provided
     *
     * @return void
     */
    public function testSkipSavePermissions(): void
    {
        // Modules component savePermissions method should not be called when no post 'permissions' data is provided
        // Setup controller for test
        $this->setupController();

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => 123456789,
                'title' => 'Test Dummy',
            ],
            'params' => [
                'object_type' => 'dummies',
            ],
        ];

        $request = new ServerRequest($config);
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public bool $savePerms = false;

            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }

            public function savePermissions(array $response, array $schema, array $newPermissions): bool
            {
                $this->savePerms = true;

                return true;
            }
        };

        // mock api client save... and check it's not called
        $apiClient = new class ('https://api.example.com') extends BEditaClient
        {
            protected bool $load = false;
            protected bool $save = false;

            public function getLoad(): bool
            {
                return $this->load;
            }

            public function getSave(): bool
            {
                return $this->save;
            }

            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                $this->load = true;

                return [];
            }

            public function save(string $type, array $data, ?array $headers = null): ?array
            {
                $this->save = true;

                return ['data' => ['id' => 123456789]];
            }
        };
        $this->controller->setApiClient($apiClient);

        // do controller call
        $this->controller->save();

        static::assertFalse($this->controller->savePerms, 'Controller save permissions should not be called');
        static::assertTrue($apiClient->getSave(), 'ApiClient save method should be called when no post data is provided');
        static::assertFalse($apiClient->getLoad(), 'ApiClient load method should not be called to load object');
    }

    /**
     * Test errors when saving permissions and related data
     *
     * @return void
     */
    public function testSaveErrorPermissionsAndRelatedData(): void
    {
        $this->setupController();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'title' => 'Test Dummy',
                '_api' => [['method' => 'addRelated', 'type' => 'dummies', 'relatedIds' => [['id' => 1, 'type' => 'dummies'], ['id' => 2, 'type' => 'dummies']]]],
                'permissions' => ['1', '2', '3'],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new class ($request) extends ModulesControllerSample
        {
            public function savePermissions(array $response, array $schema, array $newPermissions): bool
            {
                throw new BEditaClientException('save permission debug error');
            }
        };
        $registry = $this->controller->components();
        $component = new class ($registry) extends ModulesComponent
        {
            public function skipSavePermissions(string $id, array $requestPermissions, array $schema): bool
            {
                return false; // do not skip save permissions
            }

            public function skipSaveRelated(string $id, array &$relatedData): bool
            {
                return false; // do not skip save related
            }

            public function saveRelated(string $id, string $type, array $relatedData): void
            {
                throw new BEditaClientException('save related debug error');
            }
        };
        $this->controller->Modules = $component;
        $this->controller->save();
        $actual = $this->controller->viewBuilder()->getVar('error');
        static::assertIsArray($actual);
        static::assertCount(2, $actual);
        static::assertStringContainsString('save permission debug error', $actual[0]);
        static::assertStringContainsString('save related debug error', $actual[1]);
    }
}
