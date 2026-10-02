<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AdministrationBaseController;
use App\Controller\Admin\RolesController;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

/**
 * {@see \App\Controller\Admin\AdministrationBaseController} Test Case
 */
#[CoversClass(AdministrationBaseController::class)]
#[CoversMethod(AdministrationBaseController::class, 'activeFilter')]
#[CoversMethod(AdministrationBaseController::class, 'beforeFilter')]
#[CoversMethod(AdministrationBaseController::class, 'endpoint')]
#[CoversMethod(AdministrationBaseController::class, 'index')]
#[CoversMethod(AdministrationBaseController::class, 'initialize')]
#[CoversMethod(AdministrationBaseController::class, 'loadData')]
#[CoversMethod(AdministrationBaseController::class, 'loadPaginatedData')]
#[CoversMethod(AdministrationBaseController::class, 'prepareBody')]
#[CoversMethod(AdministrationBaseController::class, 'remove')]
#[CoversMethod(AdministrationBaseController::class, 'save')]
class AdministrationBaseControllerTest extends TestCase
{
    public RolesController $RlsController;

    /**
     * Test request config
     *
     * @var array
     */
    public array $defaultRequestConfig = [
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
    protected BEditaClient $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
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
            protected ?string $resourceType = 'roles';
            protected array $properties = ['name'];
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
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     */
    public static function beforeFilterProvider(): array
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
     */
    #[DataProvider('beforeFilterProvider')]
    public function testBeforeFilter($expected, array $data): void
    {
        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'applications';
        };

        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $controller->Authentication->setIdentity(new Identity($data));

        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $controller->dispatchEvent('Controller.beforeFilter');
        $controller->beforeFilter($event);
        $result = $event->getResult();

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
     */
    public function testIndex(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'applications';
        };
        $controller->index();
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
        $viewVars = (array)$controller->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'wrongtype';
        };

        $controller->index();
        $viewVars = (array)$controller->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayNotHasKey($expectedKey, $viewVars);
        }
    }

    /**
     * Data provider for `testSave`
     *
     * @return array
     */
    public static function saveProvider(): array
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
     */
    #[DataProvider('saveProvider')]
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
     */
    public function testPrepareBody(): void
    {
        $controller = new class (new ServerRequest()) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'auth_providers';
            protected array $propertiesForceJson = [
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
            ],
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
            ],
        );
        $reflectionClass = new ReflectionClass($this->RlsController);
        $method = $reflectionClass->getMethod('endpoint');
        $actual = $method->invokeArgs($this->RlsController, []);
        static::assertEquals('/roles', $actual);

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'applications';
        };
        $reflectionClass = new ReflectionClass($controller);
        $method = $reflectionClass->getMethod('endpoint');
        $actual = $method->invokeArgs($controller, []);
        static::assertEquals('/admin/applications', $actual);
    }

    /**
     * Test `loadData` method
     *
     * @return void
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
            ],
        );
        $reflectionClass = new ReflectionClass($this->RlsController);
        $method = $reflectionClass->getMethod('loadData');
        $actual = $method->invokeArgs($this->RlsController, []);
        static::assertNotEmpty($actual);
    }

    /**
     * Test `loadData` method with pagination enabled
     *
     * @return void
     */
    public function testLoadPaginatedData(): void
    {
        $config = array_merge($this->defaultRequestConfig, ['query' => ['page' => 1, 'page_size' => 1]]);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'applications';
            protected ?string $sortBy = 'name';
            protected bool $paginated = true;
        };
        $reflectionClass = new ReflectionClass($controller);
        $method = $reflectionClass->getMethod('loadData');
        $actual = $method->invokeArgs($controller, []);
        static::assertLessThanOrEqual(1, count($actual['data']));
        static::assertEquals(1, $actual['meta']['pagination']['page']);
        static::assertEquals(1, $actual['meta']['pagination']['page_size']);
    }

    /**
     * Data provider for `testActiveFilter`
     *
     * @return array
     */
    public static function activeFilterProvider(): array
    {
        return [
            'not paginated' => [false, ['name' => 'foo'], []],
            'no filter' => [true, null, []],
            'not an array' => [true, 'foo', []],
            'allowed only, trimmed, non empty' => [
                true,
                ['name' => ' foo ', 'context' => '', 'content' => 'bar', 'application_id' => ['x']],
                ['name' => 'foo'],
            ],
            'application' => [true, ['application_id' => '2'], ['application_id' => '2']],
        ];
    }

    /**
     * Test `activeFilter` method
     *
     * @param bool $paginated Paginated flag
     * @param mixed $filter Filter query
     * @param array $expected Expected result
     * @return void
     */
    #[DataProvider('activeFilterProvider')]
    public function testActiveFilter(bool $paginated, mixed $filter, array $expected): void
    {
        $query = $filter === null ? [] : compact('filter');
        $request = new ServerRequest(array_merge($this->defaultRequestConfig, compact('query')));
        $controller = new class ($request) extends AdministrationBaseController
        {
            protected ?string $resourceType = 'config';
            protected array $filters = ['name' => 'string', 'context' => 'string', 'application_id' => 'applications'];

            public function setPaginated(bool $paginated): void
            {
                $this->paginated = $paginated;
            }

            public function activeFilter(): array
            {
                return parent::activeFilter();
            }
        };
        $controller->setPaginated($paginated);
        static::assertSame($expected, $controller->activeFilter());
    }
}
