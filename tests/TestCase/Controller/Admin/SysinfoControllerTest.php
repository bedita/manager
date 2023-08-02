<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\SysinfoController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\SysinfoController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\SysinfoController
 */
class SysinfoControllerTest extends TestCase
{
    public $SysinfoController;

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
        $this->SysinfoController = new SysinfoController($request);
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
        $this->SysinfoController->index();
        $keys = [
            'sysinfo',
            'apiinfo',
        ];
        $viewVars = (array)$this->SysinfoController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
    }

    /**
     * Test `getApiInfo` method
     *
     * @return void
     * @covers ::getApiInfo()
     */
    public function testGetSysInfo(): void
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
        $actual = $this->SysinfoController->getSysInfo();
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
            'url',
            'version',
        ];
        $actual = $this->SysinfoController->getApiInfo();
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $actual);
        }
    }
}
