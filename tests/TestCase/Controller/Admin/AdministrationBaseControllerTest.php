<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AdministrationBaseController;
use App\Controller\Admin\RolesController;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\Admin\AdministrationBaseController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\AdministrationBaseController
 */
class AdministrationBaseControllerTest extends TestCase
{
    public $AdministrationBaseController;

    public $RlsController;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'applications',
        ],
    ];

    /**
     * API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->AdministrationBaseController = new class ($request) extends AdministrationBaseController
        {
            protected $resourceType = 'applications';
        };
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Init data.
     *
     * @param array $cfg The config array for request
     * @return void
     */
    private function initRolesController(array $cfg): void
    {
        $config = array_merge($this->defaultRequestConfig, $cfg);
        $request = new ServerRequest($config);
        $this->RlsController = new class ($request) extends RolesController
        {
            protected $resourceType = 'roles';
            protected $properties = ['name'];
        };
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
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
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $this->AdministrationBaseController->initialize();
        static::assertNotEmpty($this->AdministrationBaseController->Properties);
    }

    /**
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     */
    public function beforeFilterProvider(): array
    {
        return [
            'not authorized' => [
                new UnauthorizedException(__('Module access not authorized')),
                [
                    'username' => 'dummy',
                    'roles' => [ 'useless' ],
                    'tokens' => [],
                ],
            ],
            'authorized' => [
                null,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                    'tokens' => [],
                ],
            ],
            'expired' => [
                Response::class,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                ],
            ],
        ];
    }

    /**
     * Test `beforeFilter` method
     *
     * @param \Exception|string|null $expected Expected result
     * @param array $data setup data for test
     * @return void
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     */
    public function testBeforeFilter($expected, array $data): void
    {
        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }

        // Mock Authentication component
        $this->AdministrationBaseController->setRequest($this->AdministrationBaseController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->AdministrationBaseController->Authentication->setIdentity(new Identity($data));

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $this->AdministrationBaseController->dispatchEvent('Controller.beforeFilter');
        $result = $this->AdministrationBaseController->beforeFilter($event);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        } else {
            static::assertNull($result);
        }
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->AdministrationBaseController->index();
        $keys = [
            'resources',
            'meta',
            'links',
            'resourceType',
            'properties',
            'metaColumns',
            'filter',
            'schema',
            'readonly',
            'deleteonly',
        ];
        $viewVars = (array)$this->AdministrationBaseController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->AdministrationBaseController = new class ($request) extends AdministrationBaseController
        {
            protected $resourceType = 'wrongtype';
        };

        $this->AdministrationBaseController->index();
        $viewVars = (array)$this->AdministrationBaseController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayNotHasKey($expectedKey, $viewVars);
        }
    }

    /**
     * Data provider for `testSave`
     *
     * @return array
     */
    public function saveProvider(): array
    {
        return [
            'post 400' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'params' => [
                        'resource_type' => 'roles',
                    ],
                ],
                '[400]',
            ],
            'patch 404' => [
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'params' => [
                        'resource_type' => 'roles',
                    ],
                    'post' => [
                        'id' => 999,
                    ],
                ],
                '[404] Not Found',
            ],
        ];
    }

    /**
     * Test `save` method
     *
     * @return void
     * @covers ::prepareBody()
     * @covers ::save()
     * @dataProvider saveProvider()
     */
    public function testSave(array $config, string $expected): void
    {
        $this->initRolesController($config);
        $response = $this->RlsController->save();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/admin/roles', $response->getHeader('Location')[0]);
        $flash = $this->RlsController->getRequest()->getSession()->read('Flash');
        $actual = $flash['flash'][0]['message'];
        static::assertStringContainsString($expected, $actual);
    }

    /**
     * Test `prepareBody` method
     *
     * @return void
     * @covers ::prepareBody()
     */
    public function testPrepareBody(): void
    {
        $controller = new class () extends AdministrationBaseController
        {
            protected $resourceType = 'auth_providers';
            protected $propertiesForceJson = [
                'params',
            ];

            public function prepareBody(array $data): array
            {
                return parent::prepareBody($data);
            }
        };
        $data = [
            'id' => 1,
            'name' => 'test',
            'params' => json_encode([
                'key' => 'value',
            ]),
        ];
        $expected = [
            'data' => [
                'type' => 'auth_providers',
                'attributes' => [
                    'name' => 'test',
                    'params' => ['key' => 'value'],
                ],
            ],
        ];
        $actual = $controller->prepareBody($data);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `remove` method
     *
     * @return void
     * @covers ::remove()
     */
    public function testRemove(): void
    {
        $this->initRolesController(
            [
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'params' => [
                    'resource_type' => 'roles',
                ],
            ]
        );
        $response = $this->RlsController->remove('9999999999');
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/admin/roles', $response->getHeader('Location')[0]);
        $flash = $this->RlsController->getRequest()->getSession()->read('Flash');
        $expected = __('[404] Not Found');
        $message = $flash['flash'][0]['message'];
        static::assertEquals($expected, $message);
    }

    /**
     * Test `endpoint` method
     *
     * @return void
     * @covers ::endpoint()
     */
    public function testEndpoint(): void
    {
        $this->initRolesController(
            [
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'params' => [
                    'resource_type' => 'roles',
                ],
            ]
        );
        $reflectionClass = new \ReflectionClass($this->RlsController);
        $method = $reflectionClass->getMethod('endpoint');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->RlsController, []);
        static::assertEquals('/roles', $actual);

        $reflectionClass = new \ReflectionClass($this->AdministrationBaseController);
        $method = $reflectionClass->getMethod('endpoint');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->AdministrationBaseController, []);
        static::assertEquals('/admin/applications', $actual);
    }

    /**
     * Test `loadData` method
     *
     * @return void
     * @covers ::loadData()
     */
    public function testLoadData(): void
    {
        $this->initRolesController(
            [
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'resource_type' => 'roles',
                ],
                'query' => [
                    'page' => 1,
                    'page_size' => 1,
                ],
            ]
        );
        $reflectionClass = new \ReflectionClass($this->RlsController);
        $method = $reflectionClass->getMethod('loadData');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->RlsController, []);
        static::assertNotEmpty($actual);
    }
}
