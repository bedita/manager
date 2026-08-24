<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\RolesController;
use App\Test\TestCase\Controller\BaseControllerTest;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Admin\RolesController} Test Case
 */
#[CoversClass(RolesController::class)]
#[CoversMethod(RolesController::class, 'save')]
#[CoversMethod(RolesController::class, 'remove')]
class RolesControllerTest extends BaseControllerTest
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
            'resource_type' => 'roles',
        ],
    ];

    /**
     * API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadRoutes();
        $this->init([]);
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Cache::disable();
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
     * Basic test
     *
     * @return void
     */
    public function testBase(): void
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
        ];
        $viewVars = (array)$this->RlsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('roles', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }

    /**
     * Test save role and check cached val
     *
     * @return void
     */
    public function testSave(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'name' => 'dummy',
            ],
        ];
        $this->init($config);
        Cache::clearAll();
        $this->RlsController->save();
        static::assertEmpty(Cache::read(RolesController::CACHE_KEY_ROLES));
    }

    /**
     * Test `remove` method
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'name' => 'dummy',
            ],
        ];
        $this->init($config);
        $response = ApiClientProvider::getApiClient()->get('roles', ['filter' => ['name' => 'dummy']]);
        $this->RlsController->remove($response['data'][0]['id']);
        $roles = ApiClientProvider::getApiClient()->get('roles');
        $expected = false;
        $actual = false;
        foreach ($roles['data'] as $role) {
            if ($role['attributes']['name'] === 'dummy') {
                $actual = true;
            }
        }
        static::assertSame($expected, $actual);
        static::assertEmpty(Cache::read(RolesController::CACHE_KEY_ROLES));
    }
}
