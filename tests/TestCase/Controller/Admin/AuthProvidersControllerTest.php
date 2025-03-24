<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AuthProvidersController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\AuthProvidersController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\AuthProvidersController
 */
class AuthProvidersControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\AuthProvidersController
     */
    public $AuthProvidersController;

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
            'resource_type' => 'auth_providers',
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
        $this->AuthProvidersController = new class ($request) extends AuthProvidersController
        {
            protected $resourceType = 'auth_providers';
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
     */
    public function testBase(): void
    {
        $this->AuthProvidersController->index();
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
        $viewVars = (array)$this->AuthProvidersController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('auth_providers', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }
}
