<?php
namespace App\Test\TestCase\Controller\Administration;

use App\Controller\Administration\AdministrationBaseController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * Test class
 *
 * @uses \App\Controller\Administration\AdministrationBaseController
 */
class AdminBaseController extends AdministrationBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'applications';
}

/**
 * Test class
 */
class WrongAdminBaseController extends AdministrationBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'wrongtype';
}

/**
 * {@see \App\Controller\Administration\AdministrationBaseController} Test Case
 *
 * @coversDefaultClass \App\Controller\Administration\AdministrationBaseController
 */
class AdministrationBaseControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Test\TestCase\Controller\AdminBaseController
     */
    public $AdministrationBaseController;

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
        $this->AdministrationBaseController = new AdminBaseController($request);
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Test `initialize` method
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $this->AdministrationBaseController->initialize();
        static::assertNotEmpty($this->AdministrationBaseController->Properties);
    }

    /**
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     */
    public function beforeFilterProvider(): array
    {
        return [
            'not authorized' => [
                new UnauthorizedException(__('Module access not authorized')),
                [
                    'username' => 'dummy',
                    'roles' => [ 'useless' ],
                    'tokens' => [],
                ],
            ],
            'authorized' => [
                null,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                    'tokens' => [],
                ],
            ],
            'expired' => [
                Response::class,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                ],
            ],
        ];
    }

    /**
     * Test `beforeFilter` method
     *
     * @param \Exception|string|null $expected Expected result
     * @param array $data setup data for test
     * @return void
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     */
    public function testBeforeFilter($expected, array $data): void
    {
        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }
        $this->AdministrationBaseController->Auth->setUser($data);

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $this->AdministrationBaseController->dispatchEvent('Controller.beforeFilter');
        $result = $this->AdministrationBaseController->beforeFilter($event);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        } else {
            static::assertNull($result);
        }
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->AdministrationBaseController->index();
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
        ];
        $viewVars = (array)$this->AdministrationBaseController->viewVars;
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->AdministrationBaseController = new WrongAdminBaseController($request);
        $this->AdministrationBaseController->index();
        $viewVars = (array)$this->AdministrationBaseController->viewVars;
        foreach ($keys as $expectedKey) {
            static::assertArrayNotHasKey($expectedKey, $viewVars);
        }
    }
}
