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

namespace App\Test\TestCase\Controller\Component;

use App\Controller\AppController;
use App\Controller\Component\ModulesComponent;
use App\Core\Exception\UploadException;
use App\Test\TestCase\Controller\AppControllerTest;
use Authentication\AuthenticationServiceInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\Component\ModulesComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ModulesComponent
 */
class ModulesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ModulesComponent
     */
    public $Modules;

    /**
     * Authentication component
     *
     * @var \Authentication\Controller\Component\AuthenticationComponent;
     */
    public $Authentication;

    public $MyModules;

    /**
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new AppController();
        $registry = $controller->components();
        $registry->load('Authentication.Authentication');
        /** @var \App\Controller\Component\ModulesComponent $modulesComponent */
        $modulesComponent = $registry->load(ModulesComponent::class);
        $this->Modules = $modulesComponent;
        /** @var \Authentication\Controller\Component\AuthenticationComponent $authenticationComponent */
        $authenticationComponent = $registry->load(AuthenticationComponent::class);
        $this->Authentication = $authenticationComponent;
        $this->MyModules = new class ($registry) extends ModulesComponent
        {
            public $meta = [];

            protected function oEmbedMeta(string $url): ?array
            {
                return $this->meta;
            }

            public function objectTypes(?bool $abstract = null): array
            {
                return ['mices', 'elefants', 'cats', 'dogs'];
            }
        };
        $controller->loadComponent('Authentication');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Modules);
        // reset client, force new client creation
        ApiClientProvider::setApiClient(null);
        parent::tearDown();
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
     * Data provider for `testGetProject` test case.
     *
     * @return array
     */
    public function getProjectProvider(): array
    {
        return [
            'ok' => [
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
            ],
            'empty' => [
                [
                    'name' => '',
                    'version' => '',
                ],
                [],
            ],
            'client exception' => [
                [
                    'name' => '',
                    'version' => '',
                ],
                new BEditaClientException('I am a client exception'),
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
            ],
            'config' => [
                [
                    'name' => 'Gustavo',
                    'version' => '4.1.2',
                ],
                [
                    'version' => '4.1.2',
                ],
                [
                    'name' => 'Gustavo',
                ],
            ],
        ];
    }

    /**
     * Test `getProject()` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param array|\Exception $meta Response to `/home` endpoint.
     * @param array $config Project config to set.
     * @return void
     * @dataProvider getProjectProvider()
     * @covers ::getProject()
     */
    public function testGetProject($expected, $meta, $config = []): void
    {
        // Mock Authentication component
        $this->Modules->getController()->setRequest($this->Modules->getController()->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->Modules->Authentication->setIdentity(new Identity([]));

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($meta instanceof \Exception) {
            $apiClient->method('get')
                ->with('/home')
                ->willThrowException($meta);
        } else {
            $apiClient->method('get')
                ->with('/home')
                ->willReturn(compact('meta'));
        }
        ApiClientProvider::setApiClient($apiClient);
        Configure::write('Project', $config);
        Cache::delete('home_0'); // otherwise mock is applied only on first round of test from data provider
        $actual = $this->Modules->getProject();

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testIsAbstract` test case.
     *
     * @return array
     */
    public function isAbstractProvider(): array
    {
        return [
            'isAbstractTrue' => [
                true,
                'objects',
            ],
            'isAbstractFalse' => [
                false,
                'documents',
            ],
        ];
    }

    /**
     * Test `isAbstract()` method.
     *
     * @param bool $expected expected results from test
     * @param string $data setup data for test, object type
     * @dataProvider isAbstractProvider()
     * @covers ::isAbstract()
     * @return void
     */
    public function testIsAbstract($expected, $data): void
    {
        /** @var \App\Controller\ModulesController $controller */
        $controller = $this->Modules->getController();
        // mock GET /config.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([]);
        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->Modules->Authentication->setIdentity(new Identity(['id' => 1, 'roles' => ['guest']]));
        $this->Modules->beforeFilter(new Event('Module.beforeFilter'));
        $this->Modules->startup();
        $actual = $this->Modules->isAbstract($data);

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testObjectTypes` test case.
     *
     * @return array
     */
    public function objectTypesProvider(): array
    {
        return [
            'empty' => [
                [],
                null,
            ],
            'abstractList' => [
                [
                    'objects',
                    'media',
                ],
                true,
            ],
            'concreteList' => [
                [
                    'folders',
                    'documents',
                    'events',
                    'news',
                    'links',
                    'locations',
                    'images',
                    'videos',
                    'audio',
                    'files',
                    'users',
                    'profiles',
                    'publications',
                ],
                false,
            ],
        ];
    }

    /**
     * Test `objectTypes()` method.
     *
     * @param array $expected expected results from test
     * @param bool|null $data setup data for test
     * @dataProvider objectTypesProvider()
     * @covers ::objectTypes()
     * @return void
     */
    public function testObjectTypes($expected, $data): void
    {
        /** @var \App\Controller\ModulesController $controller */
        $controller = $this->Modules->getController();
        // mock GET /config.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([]);

        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->Modules->Authentication->setIdentity(new Identity(['id' => 1, 'roles' => ['guest']]));

        if (!empty($expected)) {
            $this->Modules->beforeFilter(new Event('Module.beforeFilter'));
            $this->Modules->startup();
        }
        $actual = $this->Modules->objectTypes($data);
        sort($actual);
        sort($expected);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testGetModules` test case.
     *
     * @return array
     */
    public function getModulesProvider(): array
    {
        return [
            'ok' => [
                [
                    'bedita',
                    'supporto',
                    'gustavo',
                    'trash',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'bedita',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'trash',
                        ],
                    ],
                ],
                [
                    'bedita' => [],
                    'supporto' => [],
                ],
            ],
            'ok (trash first)' => [
                [
                    'trash',
                    'supporto',
                    'gustavo',
                    'bedita',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'bedita',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'trash',
                        ],
                    ],
                ],
                [
                    'trash' => [],
                    'supporto' => [],
                ],
            ],
            'ok translations' => [
                [
                    'bedita',
                    'supporto',
                    'gustavo',
                    'translations',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'bedita',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'translations',
                        ],
                    ],
                ],
                [
                    'bedita' => [],
                    'supporto' => [],
                ],
            ],
            'client exception' => [
                [],
                new BEditaClientException('I am a client exception'),
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
            ],
        ];
    }

    /**
     * Test `getModules()` method.
     *
     * @param string[]|\Exception $expected Expected result.
     * @param array|\Exception $meta Response to `/home` endpoint.
     * @param array $modules Modules configuration.
     * @return void
     * @dataProvider getModulesProvider()
     * @covers ::modulesFromMeta()
     * @covers ::getModules()
     */
    public function testGetModules($expected, $meta, array $modules = []): void
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([]);
        /** @var \App\Controller\ModulesController $appController */
        $appController = $this->Modules->getController();
        // Mock Authentication component
        $request = $appController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock());
        $appController->setRequest($request);
        $this->Modules->Authentication->setIdentity(new Identity(['id' => 1, 'roles' => ['guest']]));

        Configure::write('Modules', $modules);

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($meta instanceof \Exception) {
            $apiClient->method('get')
                ->willThrowException($meta);
        } else {
            $apiClient->method('get')
                ->will($this->returnCallback(
                    function ($param) use ($meta, $modules) {
                        $args = func_get_args();
                        if ($args[0] === '/model/object_types') {
                            return $modules;
                        }

                        return compact('meta');
                    }
                ));
        }
        ApiClientProvider::setApiClient($apiClient);

        $actual = Hash::extract($this->Modules->getModules(), '{*}.name');

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testModulesByAccessControl`.
     *
     * @return array
     */
    public function modulesByAccessControlProvider(): array
    {
        return [
            'empty access control' => [
                ['documents' => [], 'events' => [], 'news' => []],
                [],
                [],
                ['documents' => [], 'events' => [], 'news' => []],
            ],
            'no user' => [
                ['documents' => [], 'events' => [], 'news' => []],
                ['guest'],
                [],
                ['documents' => [], 'events' => [], 'news' => []],
            ],
            'empty roles' => [
                ['documents' => [], 'events' => [], 'news' => []],
                ['guest'],
                ['id' => 1, 'roles' => []],
                ['documents' => [], 'events' => [], 'news' => []],
            ],
            'empty hidden, empty readonly' => [
                ['documents' => [], 'events' => [], 'news' => []],
                [
                    'somerole' => [
                        'hidden' => [],
                        'readonly' => [],
                    ],
                ],
                ['id' => 1, 'roles' => ['somerole']],
                ['documents' => [], 'events' => [], 'news' => []],
            ],
            'hidden + readonly' => [
                ['documents' => [], 'events' => [], 'news' => []],
                [
                    'somerole' => [
                        'hidden' => ['documents'],
                        'readonly' => ['events'],
                    ],
                ],
                ['id' => 1, 'roles' => ['somerole']],
                ['events' => ['hints' => ['allow' => []]], 'news' => []],
            ],
            'multi roles' => [
                ['documents' => [], 'events' => [], 'news' => [], 'articles' => [], 'festivals' => []],
                [
                    'role1' => [
                        'hidden' => ['articles', 'documents', 'events', 'festivals', 'news'],
                    ],
                    'role2' => [
                        'hidden' => ['articles', 'events', 'festivals'],
                        'readonly' => ['documents', 'news'],
                    ],
                    'role3' => [
                        'hidden' => ['articles', 'festivals'],
                        'readonly' => ['documents'],
                    ],
                    'role4' => [
                        'hidden' => ['articles', 'festivals', 'news'],
                        'readonly' => ['documents', 'events'],
                    ],
                ],
                ['id' => 1, 'roles' => ['role1', 'role2', 'role3', 'role4']],
                [
                    // 'articles' hidden
                    'documents' => ['hints' => ['allow' => []]], // readonly
                    'events' => [], // write
                    // 'festivals' hidden
                    'news' => [], // write
                ],
            ],
        ];
    }

    /**
     * Test `modulesByAccessControl` method
     *
     * @param array $modules The modules
     * @param array $accessControl The AccessControl config
     * @param array $user The user
     * @param array $expected The expected modules
     * @return void
     * @dataProvider modulesByAccessControlProvider()
     * @cover ::modulesByAccessControl()
     */
    public function testModulesByAccessControl(array $modules, array $accessControl, array $user, array $expected): void
    {
        // Mock Authentication component
        $this->Modules->getController()->setRequest($this->Modules->getController()->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        // set $this->Modules->modules
        $property = new \ReflectionProperty(ModulesComponent::class, 'modules');
        $property->setAccessible(true);
        $property->setValue($this->Modules, $modules);
        // set AccessControl
        Configure::write('AccessControl', $accessControl);
        // call modulesByAccessControl
        $reflectionClass = new \ReflectionClass($this->Modules);
        $method = $reflectionClass->getMethod('modulesByAccessControl');
        $method->setAccessible(true);
        $this->Modules->Authentication->setIdentity(new Identity($user));
        $method->invokeArgs($this->Modules, []);

        // get $this->Modules->modules
        $property = new \ReflectionProperty(ModulesComponent::class, 'modules');
        $property->setAccessible(true);
        $actual = $property->getValue($this->Modules);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testBeforeRender` test case.
     *
     * @return array
     */
    public function startupProvider(): array
    {
        return [
            'without current module' => [
                1,
                [
                    'gustavo',
                    'supporto',
                ],
                null,
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                    ],
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'gustavo' => [],
                ],
            ],
            'with current module' => [
                1,
                [
                    'gustavo',
                    'supporto',
                ],
                'supporto',
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                    ],
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'gustavo' => [],
                ],
                'supporto',
            ],
            'no user' => [
                null,
                [],
                null,
                [],
                [],
                [],
                null,
            ],
        ];
    }

    /**
     * Test `startup()` method.
     *
     * @param int|null $userId User id.
     * @param string[] $modules Expected module names.
     * @param string|null $currentModule Expected current module name.
     * @param array $project Expected project info.
     * @param array $meta Response to `/home` endpoint.
     * @param string[] $config Modules configuration.
     * @param string|null $currentModuleName Current module.
     * @return void
     * @dataProvider startupProvider()
     * @covers ::startup()
     * @covers ::beforeFilter()
     */
    public function testBeforeRender($userId, $modules, ?string $currentModule, array $project, array $meta, array $config = [], ?string $currentModuleName = null): void
    {
        /** @var \App\Controller\ModulesController $controller */
        $controller = $this->Modules->getController();
        // mock GET /config.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([]);
        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        Configure::write('Modules', $config);

        if ($userId) {
            $this->Authentication->setIdentity(new Identity(['id' => $userId, 'roles' => ['guest']]));
        } else {
            $this->Authentication->setIdentity(new Identity([]));
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn(compact('meta'));
        ApiClientProvider::setApiClient($apiClient);

        $clearHomeCache = true;
        $this->Modules->setConfig(compact('apiClient', 'currentModuleName', 'clearHomeCache'));
        $this->Modules->beforeFilter(new Event('Module.beforeFilter'));
        $this->Modules->startup();

        $viewVars = $controller->viewBuilder()->getVars();
        static::assertArrayHasKey('project', $viewVars);
        static::assertEquals($project, $viewVars['project']);
        static::assertArrayHasKey('modules', $viewVars);
        static::assertSame($modules, Hash::extract($viewVars['modules'], '{*}.name'));
        if ($currentModule !== null) {
            static::assertArrayHasKey('currentModule', $viewVars);
            static::assertSame($currentModule, Hash::get($viewVars['currentModule'], 'name'));
        } else {
            static::assertArrayNotHasKey('currentModule', $viewVars);
        }
    }

    /**
     * Data provider for `testUpload` test case.
     *
     * @return array
     */
    public function uploadProvider(): array
    {
        $filename = sprintf('%s/tests/files/%s', getcwd(), 'test.png');
        $file = new UploadedFile($filename, filesize($filename), 0, $filename);
        $fileErr = new UploadedFile($filename, filesize($filename), 1, $filename);
        $fileEmpty = new UploadedFile($filename, filesize($filename), 4, $filename);

        return [
            'no file' => [
                [
                    'file' => null,
                    'upload_behavior' => 'file',
                ],
                null,
                false,
            ],
            'model-type empty' => [
                [
                    'file' => $file,
                    'upload_behavior' => 'file',
                ],
                new InternalErrorException('Invalid form data: model-type'),
                false,
            ],
            'model-type not a string' => [
                [
                    'file' => $file,
                    'model-type' => 12345,
                    'upload_behavior' => 'file',
                ],
                new InternalErrorException('Invalid form data: model-type'),
                false,
            ],
            'upload ok' => [
                [
                    'file' => $file,
                    'model-type' => 'images',
                    'upload_behavior' => 'file',
                ],
                null,
                true,
            ],
            'generic upload error' => [
                [
                    'file' => $fileErr,
                    'upload_behavior' => 'file',
                    'model-type' => 'images',
                ],
                new UploadException(null, 1), // !UPLOAD_ERR_OK
                true,
            ],
            'save with empty file' => [
                [
                    'file' => $fileEmpty,
                    'upload_behavior' => 'file',
                    'model-type' => 'images',
                ],
                null,
                false,
            ],
            'upload remote url' => [
                [
                    'remote_url' => 'https://www.youtube.com/watch?v=fE50xrnJnR8',
                    'model-type' => 'videos',
                    'upload_behavior' => 'embed',
                ],
                null,
                [
                    'provider' => 'YouTube',
                    'provider_uid' => 'v=fE50xrnJnR8',
                ],
            ],
        ];
    }

    /**
     * Test `upload` method
     *
     * @param array $requestData The request data
     * @param \Exception|null $expectedException The exception expected
     * @param array|bool $uploaded The upload result (boolean or expected requestdata)
     * @return void
     * @covers ::upload()
     * @covers ::removeStream()
     * @covers ::assocStreamToMedia()
     * @covers ::checkRequestForUpload()
     * @dataProvider uploadProvider()
     */
    public function testUpload(array $requestData, $expectedException, $uploaded): void
    {
        // if upload failed, verify exception
        if ($expectedException != null) {
            $this->expectException(get_class($expectedException));
            $this->expectExceptionCode($expectedException->getCode());
            $this->expectExceptionMessage($expectedException->getMessage());
        }

        // get api client (+auth)
        $this->setupApi();

        if ($requestData['upload_behavior'] === 'file') {
            // do component call
            $this->Modules->upload($requestData);
        } else {
            // mock for ModulesComponent
            $controller = new Controller();
            $registry = $controller->components();
            $myModules = new class ($registry) extends ModulesComponent
            {
                public $meta = [];

                protected function oEmbedMeta(string $url): ?array
                {
                    return $this->meta;
                }

                public function objectTypes(?bool $abstract = null): array
                {
                    return ['mices', 'elefants', 'cats', 'dogs'];
                }
            };
            $myModules->meta = $uploaded;

            $myModules->upload($requestData);
            $result = array_intersect_key($requestData, (array)$uploaded);
            static::assertEquals($uploaded, $result);

            return;
        }

        // if upload ok, verify ID is not null
        if ($uploaded) {
            static::assertArrayHasKey('id', $requestData);

            // test upload of another file to change stream
            $filename = sprintf('%s/tests/files/%s', getcwd(), 'test2.png');
            $file = new UploadedFile($filename, filesize($filename), 0, $filename);
            $requestData = [
                'file' => $file,
                'model-type' => 'images',
                'id' => $requestData['id'],
                'upload_behavior' => 'file',
            ];
            $this->Modules->upload($requestData);
            static::assertArrayHasKey('id', $requestData);
        } else {
            static::assertFalse(isset($requestData['id']));
        }
    }

    /**
     * Test `upload` method for InternalErrorException 'Invalid form data: file.name'
     *
     * @return void
     * @covers ::upload()
     * @covers ::checkRequestForUpload()
     */
    public function testUploadInvalidFormDataFileName(): void
    {
        $expectedException = new InternalErrorException('Invalid form data: file.name');
        $this->expectException(get_class($expectedException));
        $this->expectExceptionCode($expectedException->getCode());
        $this->expectExceptionMessage($expectedException->getMessage());
        $filename = sprintf('%s/tests/files/%s', getcwd(), 'test2.png');
        $uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$filename, filesize($filename), 0, $filename])
            ->getMock();
        $uploadedFile->method('getClientFileName')
            ->willReturn(null);
        $requestData = [
            'file' => $uploadedFile,
            'model-type' => 'images',
            'upload_behavior' => 'file',
        ];
        $this->Modules->upload($requestData);
    }

    /**
     * Test `upload` method for InternalErrorException 'Invalid form data: file.tmp_name'
     *
     * @return void
     * @covers ::upload()
     * @covers ::checkRequestForUpload()
     */
    public function testUploadInvalidFormDataFileTmpName(): void
    {
        $expectedException = new InternalErrorException('Invalid form data: file.tmp_name');
        $this->expectException(get_class($expectedException));
        $this->expectExceptionCode($expectedException->getCode());
        $this->expectExceptionMessage($expectedException->getMessage());
        $filename = sprintf('%s/tests/files/%s', getcwd(), 'test2.png');
        $stream = $this->getMockBuilder(Stream::class)
            ->setConstructorArgs([$filename])
            ->getMock();
        $stream->method('getMetadata')
            ->with('uri')
            ->willReturn(null);
        $uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$filename, filesize($filename), 0, $filename])
            ->getMock();
        $uploadedFile->method('getClientFileName')
            ->willReturn($filename);
        $uploadedFile->method('getStream')
            ->willReturn($stream);
        $requestData = [
            'file' => $uploadedFile,
            'model-type' => 'images',
            'upload_behavior' => 'file',
        ];
        $this->Modules->upload($requestData);
    }

    /**
     * Test `removeStream` method
     *
     * @return void
     * @covers ::removeStream()
     */
    public function testRemoveStreamWhenThereIsNoStream(): void
    {
        $mockId = '99';
        $requestData = [
            'id' => $mockId,
            'model-type' => 'images',
        ];

        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();

        $apiClient->method('get')
            ->with(sprintf('/images/%s/streams', $mockId))
            ->willReturn([]);

        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Modules->removeStream($requestData);
        static::assertFalse($actual);
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Data provider for `testSetupRelationsMeta`
     *
     * @return array
     */
    public function setupRelationsProvider(): array
    {
        return [
            'simple' => [
                [
                    'relationsSchema' => [
                        'has_media' => [
                            'attributes' => [
                                'name' => 'has_media',
                                'label' => 'Has Media',
                                'inverse_name' => 'media_of',
                                'inverse_label' => 'Media Of',
                            ],
                        ],
                    ],
                    'resourceRelations' => [],
                    'objectRelations' => [
                        'main' => [
                            'has_media' => 'Has Media',
                        ],
                        'aside' => [],
                    ],
                ],
                [
                    'has_media' => [
                        'attributes' => [
                            'name' => 'has_media',
                            'label' => 'Has Media',
                            'inverse_name' => 'media_of',
                            'inverse_label' => 'Media Of',
                        ],
                    ],
                ],
                [
                    'has_media' => [],
                ],
            ],
            'inverse' => [
                [
                    'relationsSchema' => [
                        'media_of' => [
                            'attributes' => [
                                'name' => 'has_media',
                                'label' => 'Has Media',
                                'inverse_name' => 'media_of',
                                'inverse_label' => 'Media Of',
                            ],
                        ],
                    ],
                    'resourceRelations' => [],
                    'objectRelations' => [
                        'main' => [
                            'media_of' => 'Media Of',
                        ],
                        'aside' => [],
                    ],
                ],
                [
                    'media_of' => [
                        'attributes' => [
                            'name' => 'has_media',
                            'label' => 'Has Media',
                            'inverse_name' => 'media_of',
                            'inverse_label' => 'Media Of',
                        ],
                    ],
                ],
                [
                    'media_of' => [],
                ],
            ],
            'ordered' => [
                [
                    'relationsSchema' => [
                        'has_media' => [
                            'attributes' => [
                                'name' => 'has_media',
                                'label' => 'Has Media',
                                'inverse_name' => 'media_of',
                                'inverse_label' => 'Media Of',
                            ],
                        ],
                        'attach' => [
                            'attributes' => [
                                'name' => 'attach',
                                'label' => 'Attach',
                                'inverse_name' => 'attached_to',
                                'inverse_label' => 'Attached To',
                            ],
                        ],
                    ],
                    'resourceRelations' => [],
                    'objectRelations' => [
                        'main' => [
                            'attach' => 'Attach',
                            'has_media' => 'Has Media',
                        ],
                        'aside' => [
                        ],
                    ],
                ],
                [
                    'has_media' => [
                        'attributes' => [
                            'name' => 'has_media',
                            'label' => 'Has Media',
                            'inverse_name' => 'media_of',
                            'inverse_label' => 'Media Of',
                        ],
                    ],
                    'attach' => [
                        'attributes' => [
                            'name' => 'attach',
                            'label' => 'Attach',
                            'inverse_name' => 'attached_to',
                            'inverse_label' => 'Attached To',
                        ],
                    ],
                ],
                [
                    'has_media' => [],
                    'attach' => [],
                ],
                [
                    'main' => [
                        'attach',
                    ],
                    'aside' => [
                    ],
                ],
            ],
            'hidden' => [
                [
                    'relationsSchema' => [
                        'has_media' => [
                            'attributes' => [
                                'name' => 'has_media',
                                'label' => 'Has Media',
                                'inverse_name' => 'media_of',
                                'inverse_label' => 'Media Of',
                            ],
                        ],
                    ],
                    'resourceRelations' => [],
                    'objectRelations' => [
                        'main' => [
                            'has_media' => 'Has Media',
                        ],
                        'aside' => [
                        ],
                    ],
                ],
                [
                    'has_media' => [
                        'attributes' => [
                            'name' => 'has_media',
                            'label' => 'Has Media',
                            'inverse_name' => 'media_of',
                            'inverse_label' => 'Media Of',
                        ],
                    ],
                    'attach' => [
                        'attributes' => [
                            'name' => 'attach',
                            'label' => 'Attach',
                            'inverse_name' => 'attached_to',
                            'inverse_label' => 'Attached To',
                        ],
                    ],
                ],
                [
                    'has_media' => [],
                    'attach' => [],
                ],
                [
                    'main' => [
                        'attach',
                    ],
                    'aside' => [
                    ],
                ],
                ['attach'],
            ],
            'readonly' => [
                [
                    'relationsSchema' => [
                        'has_media' => [
                            'attributes' => [
                                'name' => 'has_media',
                                'label' => 'Has Media',
                                'inverse_name' => 'media_of',
                                'inverse_label' => 'Media Of',
                            ],
                            'readonly' => true,
                        ],
                    ],
                    'resourceRelations' => [],
                    'objectRelations' => [
                        'main' => [
                            'has_media' => 'Has Media',
                        ],
                        'aside' => [],
                    ],
                ],
                [
                    'has_media' => [
                        'attributes' => [
                            'name' => 'has_media',
                            'label' => 'Has Media',
                            'inverse_name' => 'media_of',
                            'inverse_label' => 'Media Of',
                        ],
                    ],
                ],
                [
                    'has_media' => [],
                ],
                [],
                [],
                ['has_media'],
            ],
        ];
    }

    /**
     * Test `setupRelationsMeta` method
     *
     * @dataProvider setupRelationsProvider
     * @covers ::setupRelationsMeta()
     * @covers ::relationLabels()
     * @param array $expected Expected result.
     * @param array $schema Schema array.
     * @param array $relationships Relationships array.
     * @param array $order Order array.
     * @param array $hidden Hidden array.
     * @param array $readonly Readonly array.
     * @return void
     */
    public function testSetupRelationsMeta(array $expected, array $schema, array $relationships, array $order = [], array $hidden = [], array $readonly = []): void
    {
        $this->Modules->setupRelationsMeta($schema, $relationships, $order, $hidden, $readonly);

        $viewVars = $this->Modules->getController()->viewBuilder()->getVars();

        static::assertEquals(array_keys($expected), array_keys($viewVars));

        foreach ($expected as $key => $value) {
            static::assertEquals($value, $viewVars[$key]);
        }
    }

    /**
     * Test `relatedTypes` method
     *
     * @return void
     * @covers ::relatedTypes()
     */
    public function testRelatedTypes(): void
    {
        $schema = [
            'has_media' => [
                'attributes' => [
                    'name' => 'has_media',
                    'inverse_name' => 'media_of',
                ],
                'left' => ['documents'],
                'right' => ['media'],
            ],
            'media_of' => [
                'attributes' => [
                    'name' => 'has_media',
                    'inverse_name' => 'media_of',
                ],
                'left' => ['media'],
                'right' => ['documents'],
            ],
        ];

        $types = $this->Modules->relatedTypes($schema, 'has_media');
        static::assertEquals(['media'], $types);
        $types = $this->Modules->relatedTypes($schema, 'media_of');
        static::assertEquals(['documents'], $types);
    }

    /**
     * Provider for `testRelationsSchema`.
     *
     * @return array
     */
    public function relationsSchemaProvider(): array
    {
        return [
            'empty data' => [
                [], // schema
                [], // relationships
                [], // expected
            ],
            'no right data' => [
                [
                    'hates' => [
                        'left' => ['elefants'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                    ],
                ], // schema
                [
                    'hates' => [],
                    'loves' => [],
                ], // relationships
                [
                    'hates' => [
                        'left' => ['elefants'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                    ],
                ], // expected
            ],
            'full example' => [
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['objects'],
                    ],
                ], // schema
                [
                    'hates' => [],
                    'loves' => [],
                ], // relationships
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['cats', 'dogs', 'elefants', 'mices'],
                    ],
                ], // expected
            ],
            'readonly' => [
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['objects'],
                    ],
                ], // schema
                [
                    'hates' => [
                        'readonly' => true,
                    ],
                    'loves' => [],
                ], // relationships
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                        'readonly' => true,
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['cats', 'dogs', 'elefants', 'mices'],
                    ],
                ], // expected
            ],
        ];
    }

    /**
     * Test `relationsSchema` method
     *
     * @param array $schema The schema
     * @param array $relationships The relationships
     * @param array $expected The expected result
     * @return void
     * @dataProvider relationsSchemaProvider()
     * @covers ::relationsSchema()
     */
    public function testRelationsSchema(array $schema, array $relationships, array $expected): void
    {
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest();
        $actual = $test->invokeMethod($this->MyModules, 'relationsSchema', [$schema, $relationships]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testSaveRelated`.
     *
     * @return array
     */
    public function saveRelatedProvider(): array
    {
        $dummy = ['id' => 123, 'type' => 'dummies'];

        return [
            'bad request exception' => [
                111, // id
                'dummies', // type
                [
                    [
                        'method' => 'wrongMethod',
                        'relation' => 'see_also',
                        'relatedIds' => [$dummy],
                    ],
                ], // relatedData
                new BadRequestException(__('Bad related data method')), // expected
            ],
            'addRelated see_also' => [
                111, // id
                'dummies', // type
                [
                    [
                        'method' => 'addRelated',
                        'relation' => 'see_also',
                        'relatedIds' => [$dummy],
                    ],
                ], // relatedData
                'addRelated', // expected
            ],
            'removeRelated see_also' => [
                111, // id
                'dummies', // type
                [
                    [
                        'method' => 'removeRelated',
                        'relation' => 'see_also',
                        'relatedIds' => [$dummy],
                    ],
                ], // relatedData
                'removeRelated', // expected
            ],
            'replaceRelated see_also' => [
                111, // id
                'dummies', // type
                [
                    [
                        'method' => 'replaceRelated',
                        'relation' => 'see_also',
                        'relatedIds' => [$dummy],
                    ],
                ], // relatedData
                'replaceRelated', // expected
            ],
            'folders children not folders' => [
                111, // id
                'folders', // type
                [
                    [
                        'method' => 'addRelated',
                        'relation' => 'children',
                        'relatedIds' => [$dummy],
                    ],
                ], // relatedData
                'addRelated', // expected
            ],
            'folders children position' => [
                111, // id
                'folders', // type
                [
                    [
                        'method' => 'addRelated',
                        'relation' => 'children',
                        'relatedIds' => [
                            [
                                'id' => 123,
                                'type' => 'folders',
                                'meta' => ['relation' => ['position' => 1]],
                            ],
                            [
                                'id' => 124,
                                'type' => 'folders',
                                'meta' => ['relation' => ['position' => 2]],
                            ],
                        ],
                    ],
                ], // relatedData
                'addRelated', // expected
            ],
            'folders children folders' => [
                222, // id
                'folders', // type
                [
                    [
                        'method' => 'addRelated',
                        'relation' => 'children',
                        'relatedIds' => [['id' => 123, 'type' => 'folders']],
                    ],
                ], // relatedData
                'addRelated', // expected
            ],
            'folders children mixed' => [
                333, // id
                'folders', // type
                [
                    [
                        'method' => 'removeRelated',
                        'relation' => 'children',
                        'relatedIds' => [['id' => 123, 'type' => 'folders'], ['id' => 456, 'type' => 'dummies']],
                    ],
                ], // relatedData
                'removeRelated', // expected
            ],
            'folders parent' => [
                333, // id
                'folders', // type
                [
                    [
                        'method' => 'addRelated',
                        'relation' => 'parent',
                        'relatedIds' => [['id' => 123, 'type' => 'folders'], ['id' => 456, 'type' => 'folders']],
                    ],
                ], // relatedData
                'replaceRelated', // expected
            ],
        ];
    }

    /**
     * Test `saveRelated`
     *
     * @param int $id Object ID
     * @param string $type Object type
     * @param array $relatedData Related objects data
     * @param mixed $expected The expected result
     * @return void
     * @dataProvider saveRelatedProvider
     * @covers ::saveRelated()
     * @covers ::saveRelatedObjects()
     */
    public function testSaveRelated(int $id, string $type, array $relatedData, $expected): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }
        $actual = 'none';
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.org'])
            ->getMock();
        $apiClient->method('addRelated')
            ->will($this->returnCallback(function () use (&$actual) {
                $actual = 'addRelated';

                return ['response addRelated'];
            }));
        $apiClient->method('removeRelated')
            ->will($this->returnCallback(function () use (&$actual) {
                $actual = 'removeRelated';

                return ['response removeRelated'];
            }));
        $apiClient->method('replaceRelated')
            ->will($this->returnCallback(function () use (&$actual) {
                $actual = 'replaceRelated';

                return ['response replaceRelated'];
            }));
        ApiClientProvider::setApiClient($apiClient);
        $this->Modules->saveRelated((string)$id, $type, $relatedData);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getRelated` method on empty relatedIds.
     *
     * @return void
     * @covers ::getRelated()
     */
    public function testGetRelatedEmpty(): void
    {
        // empty relatedIds
        $data = ['relatedIds' => []];
        static::assertSame([], $this->Modules->getRelated($data));
    }

    /**
     * Test `getRelated` method on non-empty relatedIds.
     *
     * @return void
     * @covers ::getRelated()
     */
    public function testGetRelated(): void
    {
        $apiClient = ApiClientProvider::getApiClient();
        $response = $apiClient->authenticate(getenv('BEDITA_ADMIN_USR'), getenv('BEDITA_ADMIN_PWD'));
        $apiClient->setupTokens($response['meta']);
        $response = $apiClient->save('documents', ['title' => 'a dummy doc one']);
        $o = $response['data'];
        $data = ['relatedIds' => [
            $o,
            ['type' => 'documents', 'attributes' => ['title' => 'a dummy doc two']],
            ['type' => 'documents', 'attributes' => ['title' => 'a dummy doc three']],
        ]];
        $actual = $this->Modules->getRelated($data);
        static::assertIsArray($actual);
        static::assertCount(3, $actual);
        $objs = [];
        foreach ($actual as $obj) {
            $objs[] = $apiClient->getObject($obj['id']);
        }
        $titles = Hash::extract($objs, '{n}.data.attributes.title');
        $expected = ['a dummy doc one', 'a dummy doc two', 'a dummy doc three'];
        static::assertSame($expected, $titles);
    }

    /**
     * Test `setupAttributes` method
     *
     * @return void
     * @covers ::setupAttributes()
     */
    public function testSetupAttributes(): void
    {
        // check that object attributes is set into controller currentAttributes var as json string
        $obj = ['attributes' => ['title' => 'test']];
        $this->Modules->setupAttributes($obj);
        $viewVars = $this->Modules->getController()->viewBuilder()->getVars();
        static::assertEquals(json_encode(['title' => 'test']), $viewVars['currentAttributes']);
    }

    /**
     * Test `skipSaveObject` method
     *
     * @return void
     * @covers ::skipSaveObject()
     */
    public function testSkipSaveObject(): void
    {
        // empty id
        $requestData = [];
        $actual = $this->Modules->skipSaveObject('', $requestData);
        static::assertFalse($actual);

        // not empty id, empty request data
        $requestData = [];
        $actual = $this->Modules->skipSaveObject('123', $requestData);
        static::assertTrue($actual);

        // not empty id, not empty request data
        $requestData = ['id' => '123', 'title' => 'test', 'permissions' => []];
        $actual = $this->Modules->skipSaveObject('123', $requestData);
        static::assertFalse($actual);

        // not empty id, empty request data
        $requestData = ['id' => '123', 'permissions' => []];
        $actual = $this->Modules->skipSaveObject('123', $requestData);
        static::assertTrue($actual);

        // not empty id, date ranges unchanged
        $request = $this->Modules->getController()->getRequest()->withParam('object_type', 'events');
        $this->Modules->getController()->setRequest($request);
        // mock apiClient getObject
        $apiClient = new class ('https://api.example.com') extends BEditaClient {
            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                return [
                    'data' => [
                        'id' => '123',
                        'type' => 'events',
                        'attributes' => [
                            'date_ranges' => [],
                        ],
                    ],
                ];
            }
        };
        $safeClient = ApiClientProvider::getApiClient();
        ApiClientProvider::setApiClient($apiClient);
        $requestData = ['id' => '123', 'date_ranges' => []];
        $actual = $this->Modules->skipSaveObject('123', $requestData);
        static::assertTrue($actual);

        // not empty id, date ranges changed
        $request = $this->Modules->getController()->getRequest()->withParam('object_type', 'events');
        $this->Modules->getController()->setRequest($request);
        // mock apiClient getObject
        $apiClient = new class ('https://api.example.com') extends BEditaClient {
            public function getObject(string|int $id, string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                return [
                    'data' => [
                        'id' => '123',
                        'type' => 'events',
                        'attributes' => [
                            'date_ranges' => [
                                [
                                    'start' => '2023-01-01',
                                    'end' => '2023-01-31',
                                ],
                            ],
                        ],
                    ],
                ];
            }
        };
        ApiClientProvider::setApiClient($apiClient);
        $requestData = [
            'id' => '123',
            'date_ranges' => [
                [
                    'start' => '2023-01-01',
                    'end' => '2023-01-31',
                ],
                [
                    'start' => '2023-02-01',
                    'end' => '2023-02-28',
                ],
            ],
        ];
        $actual = $this->Modules->skipSaveObject('123', $requestData);
        static::assertFalse($actual);

        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test `skipSaveRelated` method
     *
     * @return void
     * @covers ::skipSaveRelated()
     */
    public function testSkipSaveRelated(): void
    {
        $request = $this->Modules->getController()->getRequest()->withParam('object_type', 'dummies');
        $this->Modules->getController()->setRequest($request);

        // empty related data => true
        $relatedData = [];
        $actual = $this->Modules->skipSaveRelated('123', $relatedData);
        static::assertTrue($actual);

        // addRelated or removeRelated => false
        $relatedData = [
            [
                'method' => 'addRelated',
                'relation' => 'see_also',
                'relatedIds' => [['id' => '456', 'type' => 'dummies']],
            ],
        ];
        $actual = $this->Modules->skipSaveRelated('123', $relatedData);
        static::assertFalse($actual);
        $relatedData = [
            [
                'method' => 'removeRelated',
                'relation' => 'see_also',
                'relatedIds' => [['id' => '456', 'type' => 'dummies']],
            ],
        ];
        $actual = $this->Modules->skipSaveRelated('123', $relatedData);
        static::assertFalse($actual);

        // replaceRelated with a change => false
        $relatedData = [
            [
                'method' => 'replaceRelated',
                'relation' => 'see_also',
                'relatedIds' => [['id' => '1001', 'type' => 'dummies', 'meta' => ['relation' => ['priority' => 1]]]],
            ],
        ];
        // mock apiClient getRelated
        $apiClient = new class ('https://api.example.com') extends BEditaClient {
            public function getRelated(string|int $id, string $type, string $relation, ?array $query = null, ?array $headers = null): ?array
            {
                return [
                    'data' => [
                        ['id' => '1001', 'type' => 'dummies', 'meta' => ['relation' => ['priority' => 1]]],
                        ['id' => '1002', 'type' => 'dummies', 'meta' => ['relation' => ['priority' => 2]]],
                    ],
                ];
            }
        };
        $safeClient = ApiClientProvider::getApiClient();
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Modules->skipSaveRelated('123', $relatedData);
        static::assertFalse($actual);

        // replaceRelated without a change => true
        $relatedData = [
            [
                'method' => 'replaceRelated',
                'relation' => 'see_also',
                'relatedIds' => [
                    ['id' => '1001', 'type' => 'dummies', 'meta' => ['relation' => ['priority' => 1]]],
                    ['id' => '1002', 'type' => 'dummies', 'meta' => ['relation' => ['priority' => 2]]],
                ],
            ],
        ];
        $actual = $this->Modules->skipSaveRelated('123', $relatedData);
        static::assertTrue($actual);

        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test `skipSavePermissions` method
     *
     * @return void
     * @covers ::skipSavePermissions()
     */
    public function testSkipSavePermissions(): void
    {
        $requestData = [
            'id' => '123',
            'permissions' => ['1', '2'],
        ];
        $apiClient = new class ('https://api.example.com') extends BEditaClient {
            public function getObjects(string $type = 'objects', ?array $query = null, ?array $headers = null): ?array
            {
                return [
                    'data' => [
                        ['id' => '1', 'attributes' => ['role_id' => 1]],
                        ['id' => '2', 'attributes' => ['role_id' => 2]],
                    ],
                ];
            }
        };
        $safeClient = ApiClientProvider::getApiClient();
        ApiClientProvider::setApiClient($apiClient);
        $controller = new AppController();
        $registry = $controller->components();
        $registry->load('Authentication.Authentication');
        /** @var \App\Controller\Component\ModulesComponent $modulesComponent */
        $modulesComponent = $registry->load(ModulesComponent::class);
        $this->Modules = $modulesComponent;
        $schema = ['associations' => []];
        $actual = $this->Modules->skipSavePermissions('123', $requestData['permissions'], $schema);
        static::assertTrue($actual);
        $schema = ['associations' => ['Permissions']];
        $actual = $this->Modules->skipSavePermissions('123', $requestData['permissions'], $schema);
        static::assertTrue($actual);
        ApiClientProvider::setApiClient($safeClient);
    }
}
