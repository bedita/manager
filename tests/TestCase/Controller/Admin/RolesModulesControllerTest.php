<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\RolesModulesController;
use App\Utility\ApiConfigTrait;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\RolesModulesController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\RolesModulesController
 */
class RolesModulesControllerTest extends TestCase
{
    use ApiConfigTrait;

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
            'resource_type' => 'roles',
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
        Cache::enable();
        Cache::clearAll();
        parent::setUp();

        $this->init([]);
        $this->loadRoutes();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Init request, controller, etc.
     *
     * @param array $requestConfig The request config
     * @return void
     */
    private function init(array $requestConfig): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->RlsController = new class ($request) extends RolesModulesController
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
     * Basic test
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->RlsController->index();
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
            'access_control',
        ];
        $viewVars = (array)$this->RlsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('roles', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }

    /**
     * Test save
     *
     * @return void
     * @covers ::save()
     */
    public function testSave(): void
    {
        $roles = [
            'manager' => [
                'admin' => 'hidden',
                'objects' => 'write',
                'users' => 'read',
                'documents' => 'write',
            ],
            'guest' => [
                'admin' => 'hidden',
                'objects' => 'read',
                'users' => 'read',
                'documents' => 'write',
            ],
        ];
        $this->RlsController = new RolesModulesController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => [
                        'roles' => $roles,
                    ],
                ]
            )
        );
        $response = $this->RlsController->save();
        static::assertSame(Response::class, get_class($response));
    }

    /**
     * Test `allowedRoles`
     *
     * @return void
     * @covers ::allowedRoles()
     */
    public function testAllowedRoles(): void
    {
        $reflection = new \ReflectionClass(get_class($this->RlsController));
        $method = $reflection->getMethod('allowedRoles');
        $method->setAccessible(true);

        // empty endpoint permissions
        $expected = [['id' => 2, 'attributes' => ['name' => 'manager']], ['id' => 3, 'attributes' => ['name' => 'guest']]];
        $actual = $method->invokeArgs($this->RlsController, [
            [
                ['id' => 1, 'attributes' => ['name' => 'admin']],
                ['id' => 2, 'attributes' => ['name' => 'manager']],
                ['id' => 3, 'attributes' => ['name' => 'guest']],
            ], // roles
            [], // endpoint permissions
        ]);
        static::assertSame($expected, array_values($actual));

        // non empty endpoint permission
        $expected = [['id' => 2, 'attributes' => ['name' => 'manager']]];
        $actual = $method->invokeArgs($this->RlsController, [
            [
                ['id' => 1, 'attributes' => ['name' => 'admin']],
                ['id' => 2, 'attributes' => ['name' => 'manager']],
                ['id' => 3, 'attributes' => ['name' => 'guest']],
            ], // roles
            [
                ['id' => 11, 'attributes' => ['role_id' => 3, 'write' => false]], // guest blocked
                ['id' => 12, 'attributes' => ['role_id' => 2, 'write' => true]], // manager allowed
            ], // endpoint permissions
        ]);
        static::assertSame($expected, array_values($actual));
    }
}
