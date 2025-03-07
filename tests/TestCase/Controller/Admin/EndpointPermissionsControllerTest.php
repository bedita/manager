<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\EndpointPermissionsController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\EndpointPermissionsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\EndpointPermissionsController
 */
class EndpointPermissionsControllerTest extends TestCase
{
    public EndpointPermissionsController $EndPermsController;

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

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->EndPermsController = new class ($request) extends EndpointPermissionsController
        {
            protected ?string $resourceType = 'endpoint_permissions';
            protected array $properties = ['endpoint_id', 'application_id'];
        };
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
        $this->loadRoutes();
    }

    /**
     * Basic test
     *
     * @return void
     */
    public function testBase(): void
    {
        $this->EndPermsController->index();
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
        $viewVars = (array)$this->EndPermsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('endpoint_permissions', $viewVars['resourceType']);
        static::assertEquals(['endpoint_id', 'application_id'], $viewVars['properties']);
    }

    /**
     * Test `save`
     *
     * @return void
     */
    public function testSave(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'endpoint_id' => 1,
                'application_id' => '-',
                'role_id' => '-',
                'permission' => 15,
            ],
        ];
        $request = new ServerRequest($config);
        $this->EndPermsController = new class ($request) extends EndpointPermissionsController
        {
            protected ?string $resourceType = 'endpoint_permissions';
            protected array $properties = ['endpoint_id', 'application_id'];
        };
        $response = $this->EndPermsController->save();
        static::assertSame(Response::class, get_class($response));
    }
}
