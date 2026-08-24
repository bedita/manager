<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\SystemInfoController;
use App\Test\TestCase\Controller\AppControllerTest;
use Authentication\Identity;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Admin\SystemInfoController} Test Case
 */
#[CoversClass(SystemInfoController::class)]
#[CoversMethod(SystemInfoController::class, 'getApiInfo')]
#[CoversMethod(SystemInfoController::class, 'getSystemInfo')]
#[CoversMethod(SystemInfoController::class, 'index')]
class SystemInfoControllerTest extends AppControllerTest
{
    public SystemInfoController $SystemInfoController;

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
     */
    public function testIndex(): void
    {
        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => ['readers'],
        ]);
        $this->SystemInfoController->setRequest($this->SystemInfoController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->SystemInfoController->Authentication->setIdentity($user);
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
     */
    public function testGetApiInfo(): void
    {
        $expectedKeys = [
            'Url',
            'Version',
            'GET /home',
        ];
        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => ['readers'],
        ]);
        $this->SystemInfoController->setRequest($this->SystemInfoController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->SystemInfoController->Authentication->setIdentity($user);
        $actual = $this->SystemInfoController->getApiInfo();
        foreach ($expectedKeys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $actual);
        }
    }

    /**
     * Test `getApiInfo` method with exception
     *
     * @return void
     */
    public function testGetApiInfoException(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->will($this->throwException(new BEditaClientException('test')));
        $expected = [
            'Url' => getenv('BEDITA_API'),
            'Version' => '',
        ];
        $controller = new class (new ServerRequest()) extends SystemInfoController {
            public function setApiClient($client): void
            {
                $this->apiClient = $client;
            }
        };
        $controller->setApiClient($apiClient);

        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => ['readers'],
        ]);
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $controller->Authentication->setIdentity($user);
        $actual = $controller->getApiInfo();
        static::assertEquals($expected, $actual);
    }
}
