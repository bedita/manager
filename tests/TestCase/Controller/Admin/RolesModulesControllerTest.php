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
}
