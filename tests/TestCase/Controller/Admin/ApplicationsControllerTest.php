<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ApplicationsController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\ApplicationsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\ApplicationsController
 */
class ApplicationsControllerTest extends TestCase
{
    public $AppsController;

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
        $this->AppsController = new class ($request) extends ApplicationsController
        {
            protected $resourceType = 'applications';
            protected $properties = ['name'];
            protected $propertiesSecrets = ['api_key', 'client_secret'];
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
        $this->AppsController->index();
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
        $viewVars = (array)$this->AppsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('applications', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }
}
