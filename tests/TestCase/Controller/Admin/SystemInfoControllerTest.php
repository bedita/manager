<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\SystemInfoController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\SystemInfoController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\SystemInfoController
 */
class SystemInfoControllerTest extends TestCase
{
    public $SystemInfoController;

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
        $this->SystemInfoController = new SystemInfoController($request);
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->SystemInfoController->index();
        $keys = [
            'system_info',
            'api_info',
        ];
        $viewVars = (array)$this->SystemInfoController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
    }

    /**
     * Test `getSystemInfo` method
     *
     * @return void
     * @covers ::getSystemInfo()
     */
    public function testGetSystemInfo(): void
    {
        $expectedKeys = [
            'Version',
            'CakePHP',
            'PHP',
            'Twig',
            'Vuejs',
            'Operating System',
            'PHP Server API',
            'Extensions',
            'Extensions info',
            'Memory limit',
            'Post max size',
            'Upload max size',
        ];
        $actual = $this->SystemInfoController->getSystemInfo();
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $actual);
        }
    }

    /**
     * Test `getApiInfo` method
     *
     * @return void
     * @covers ::getApiInfo()
     */
    public function testGetApiInfo(): void
    {
        $expectedKeys = [
            'Url',
            'Version',
        ];
        $actual = $this->SystemInfoController->getApiInfo();
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $actual);
        }
    }
}
