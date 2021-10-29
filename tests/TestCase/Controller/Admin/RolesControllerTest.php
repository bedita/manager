<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\RolesController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\RolesController
 */
class RlsController extends RolesController
{
    protected $resourceType = 'roles';
    protected $properties = ['name'];
}

/**
 * {@see \App\Controller\Admin\RolesController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\RolesController
 */
class RolesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Test\TestCase\Controller\Admin\RlsController
     */
    public $RlsController;

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
            'resource_type' => 'roles',
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

        $this->init([]);
    }

    /**
     * Init request, controller, etc.
     *
     * @param array $requestConfig The request config
     * @return void
     */
    private function init(array $requestConfig): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->RlsController = new RlsController($request);
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
        $this->RlsController->index();
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
        $viewVars = (array)$this->RlsController->viewVars;
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        static::assertEquals('roles', $viewVars['resourceType']);
        static::assertEquals(['name'], $viewVars['properties']);
    }

    /**
     * Test `save` method
     *
     * @return void
     * @covers ::save()
     */
    public function testSave(): void
    {
        $this->init([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'roles',
            ],
        ]);
        $response = $this->RlsController->save();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/admin/roles', $response->getHeader('Location')[0]);
        $flash = $this->RlsController->request->getSession()->read('Flash');
        $expected = __('[400] Invalid data');
        $message = $flash['flash'][0]['message'];
        static::assertEquals($expected, $message);
    }

    /**
     * Test `save` method
     *
     * @return void
     * @covers ::remove()
     */
    public function testRemove(): void
    {
        $this->init([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'params' => [
                'resource_type' => 'roles',
            ],
        ]);
        $response = $this->RlsController->remove('9999999999');
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/admin/roles', $response->getHeader('Location')[0]);
        $flash = $this->RlsController->request->getSession()->read('Flash');
        $expected = __('[404] Not Found');
        $message = $flash['flash'][0]['message'];
        static::assertEquals($expected, $message);
    }
}
