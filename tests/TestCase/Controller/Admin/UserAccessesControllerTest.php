<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\UserAccessesController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\UserAccessesController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\UserAccessesController
 */
class UserAccessesControllerTest extends TestCase
{
    public UserAccessesController $UserAccessesController;

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
            'resource_type' => 'whatever',
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
        $this->UserAccessesController = new UserAccessesController($request);
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
        static::assertNull($this->UserAccessesController->index());
    }
}
