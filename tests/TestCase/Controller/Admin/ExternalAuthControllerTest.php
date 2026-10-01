<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ExternalAuthController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \App\Controller\Admin\ExternalAuthController} Test Case
 */
#[CoversClass(ExternalAuthController::class)]
class ExternalAuthControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\ExternalAuthController
     */
    public $ExternalAuthController;

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
            'resource_type' => 'external_auth',
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
        $this->ExternalAuthController = new class ($request) extends ExternalAuthController
        {
            protected ?string $resourceType = 'external_auth';
            protected array $properties = ['name'];
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
        $this->ExternalAuthController->index();
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
        $viewVars = (array)$this->ExternalAuthController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('external_auth', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
        $flash = $this->ExternalAuthController->getRequest()->getSession()->read('Flash');
        $expected = 'No auth providers found: you cannot create external auth entries. Create at least one auth provider first';
        static::assertEquals($expected, $flash['flash'][0]['message']);
    }

    /**
     * Test `usersLabels` method.
     *
     * @return void
     */
    public function testUsersLabelsEmpty(): void
    {
        $controller = new class ($this->ExternalAuthController->getRequest()) extends ExternalAuthController
        {
            public function usersLabels(array $resources): array
            {
                return parent::usersLabels($resources);
            }
        };
        $result = $controller->usersLabels([]);
        static::assertEmpty($result);
    }

    /**
     * Test `usersLabels` method with non-empty resources.
     *
     * @return void
     */
    public function testUsersLabelsNonEmpty(): void
    {
        $controller = new class ($this->ExternalAuthController->getRequest()) extends ExternalAuthController
        {
            public function usersLabels(array $resources): array
            {
                return parent::usersLabels($resources);
            }
        };
        $resources = [['attributes' => ['user_id' => 1]]];
        $result = $controller->usersLabels($resources);
        static::assertNotEmpty($result);
        static::assertSame([1 => '(admin)'], $result);
    }

    /**
     * Test `usersLabels` method when BEditaClient throws an exception.
     *
     * @return void
     */
    public function testUsersLabelsBEditaClientException(): void
    {
        $controller = new class ($this->ExternalAuthController->getRequest()) extends ExternalAuthController
        {
            public function setApiClient(BEditaClient $apiClient): void
            {
                $this->apiClient = $apiClient;
            }

            public function usersLabels(array $resources): array
            {
                return parent::usersLabels($resources);
            }
        };
        // mock
        $apiClientMock = $this->createMock(BEditaClient::class);
        $apiClientMock->method('get')->willThrowException(new BEditaClientException('API error'));
        $controller->setApiClient($apiClientMock);
        $result = $controller->usersLabels([['attributes' => ['user_id' => 1]]]);
        static::assertEmpty($result);
    }
}
