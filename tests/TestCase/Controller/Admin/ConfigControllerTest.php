<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ConfigController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\ConfigController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\ConfigController
 */
class ConfigControllerTest extends TestCase
{
    public $CfgController;

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
            'resource_type' => 'config',
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
        $this->CfgController = new class ($request) extends ConfigController
        {
            protected $resourceType = 'config';
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
        $this->CfgController->index();
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
        $viewVars = (array)$this->CfgController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('config', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }

    /**
     * Test `beforeFilter`
     *
     * @return void
     * @covers ::beforeFilter()
     */
    public function testBeforeFilter(): void
    {
        $event = $this->CfgController->dispatchEvent('Controller.beforeFilter');
        $this->CfgController->beforeFilter($event);
        $viewVars = (array)$this->CfgController->viewBuilder()->getVars();
        static::assertEquals(['' => __('No application'), 1 => 'default-app', 2 => 'manager'], $viewVars['applications']);
    }
}
