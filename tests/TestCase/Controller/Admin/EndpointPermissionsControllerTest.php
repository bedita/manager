<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\EndpointPermissionsController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\EndpointPermissionsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\EndpointPermissionsController
 */
class EndpointPermissionsControllerTest extends TestCase
{
    public $EndPermsController;

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

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->EndPermsController = new class ($request) extends EndpointPermissionsController
        {
            protected $resourceType = 'endpoint_permissions';
            protected $properties = ['endpoint_id', 'application_id'];
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
}
