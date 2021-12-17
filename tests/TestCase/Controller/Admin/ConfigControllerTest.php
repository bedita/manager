<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ConfigController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\ConfigController
 */
class CfgController extends ConfigController
{
    protected $resourceType = 'config';
    protected $properties = ['name'];
}

/**
 * {@see \App\Controller\Admin\ConfigController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\ConfigController
 */
class ConfigControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Test\TestCase\Controller\Admin\CfgController
     */
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
     * @var BEditaClient
     */
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->CfgController = new CfgController($request);
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
        $viewVars = (array)$this->CfgController->viewVars;
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
        $viewVars = (array)$this->CfgController->viewVars;
        static::assertEquals(['' => __('No application'), 1 => 'default-app', 2 => 'manager'], $viewVars['applications']);
    }
}
