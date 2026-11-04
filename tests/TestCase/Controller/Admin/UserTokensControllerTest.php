<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\UserTokensController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \App\Controller\Admin\UserTokensController} Test Case
 */
#[CoversClass(UserTokensController::class)]
class UserTokensControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\UserTokensController
     */
    public $UserTokensController;

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
            'resource_type' => 'user_tokens',
        ],
    ];

    /**
     * API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected BEditaClient $client;

    /**
     * Skip tests flag for API < 6
     *
     * @var bool
     */
    protected bool $skip = false;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->UserTokensController = new class ($request) extends UserTokensController
        {
            protected ?string $resourceType = 'user_tokens';
            protected array $properties = ['client_token'];
        };
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
        $beditaApiVersion = (string)Hash::get((array)$this->client->get('/home'), 'meta.version');
        $apiMajor = substr($beditaApiVersion, 0, strpos($beditaApiVersion, '.'));
        $this->skip = $apiMajor < 6;
    }

    /**
     * Basic test
     *
     * @return void
     */
    public function testBase(): void
    {
        if ($this->skip) {
            $this->markTestSkipped('Requires API v6+');
        }
        $this->UserTokensController->index();
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
            'applications',
            'users',
        ];
        $viewVars = (array)$this->UserTokensController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('user_tokens', $viewVars['resourceType']);
        static::assertEquals(['client_token'], $viewVars['properties']);
    }

    /**
     * Test `index` method on activeFilter.user_id passed.
     *
     * @return void
     */
    public function testIndexActiveFilterUserId(): void
    {
        if ($this->skip) {
            $this->markTestSkipped('Requires API v6+');
        }
        $request = $this->UserTokensController->getRequest()->withQueryParams(['filter' => ['user_id' => 1]]);
        $this->UserTokensController->setRequest($request);
        $this->UserTokensController->index();
        $actual = (array)$this->UserTokensController->viewBuilder()->getVar('users');
        static::assertEquals(['1' => '(admin)'], $actual);
    }

    /**
     * Test `usersLabels` method.
     *
     * @return void
     */
    public function testUsersLabelsEmpty(): void
    {
        if ($this->skip) {
            $this->markTestSkipped('Requires API v6+');
        }
        $controller = new class ($this->UserTokensController->getRequest()) extends UserTokensController
        {
            /**
             * @inheritDoc
             */
            public function usersLabels(array $ids): array
            {
                return parent::usersLabels($ids);
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
        if ($this->skip) {
            $this->markTestSkipped('Requires API v6+');
        }
        $controller = new class ($this->UserTokensController->getRequest()) extends UserTokensController
        {
            /**
             * @inheritDoc
             */
            public function usersLabels(array $ids): array
            {
                return parent::usersLabels($ids);
            }
        };
        $ids = [1];
        $result = $controller->usersLabels($ids);
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
        if ($this->skip) {
            $this->markTestSkipped('Requires API v6+');
        }
        $controller = new class ($this->UserTokensController->getRequest()) extends UserTokensController
        {
            public function setApiClient(BEditaClient $apiClient): void
            {
                $this->apiClient = $apiClient;
            }

            /**
             * @inheritDoc
             */
            public function usersLabels(array $ids): array
            {
                return parent::usersLabels($ids);
            }
        };
        // mock
        $apiClientMock = $this->createMock(BEditaClient::class);
        $apiClientMock->method('get')->willThrowException(new BEditaClientException('API error'));
        $controller->setApiClient($apiClientMock);
        $result = $controller->usersLabels([1]);
        static::assertEmpty($result);
    }
}
