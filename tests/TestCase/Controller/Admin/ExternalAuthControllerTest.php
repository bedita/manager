<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\ExternalAuthController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\ExternalAuthController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\ExternalAuthController
 */
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
}
