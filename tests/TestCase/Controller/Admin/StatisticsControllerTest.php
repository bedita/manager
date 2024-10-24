<?php

namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\StatisticsController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\StatisticsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\StatisticsController
 */
class StatisticsControllerTest extends TestCase
{
    public $StatisticsController;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
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
        $this->StatisticsController = new StatisticsController($request);
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
     * @covers ::fetch()
     * @covers ::intervals()
     * @covers ::fetchCount()
     */
    public function testIndex(): void
    {
        $this->StatisticsController->index();
        $keys = ['resources'];
        $viewVars = (array)$this->StatisticsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }

        // test with year query
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'query' => [
                'objectType' => 'documents',
                'year' => 2024,
            ],
        ]);
        $this->StatisticsController = new StatisticsController($request);
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
        $this->StatisticsController->index();
        $keys = ['data'];
        $viewVars = (array)$this->StatisticsController->viewBuilder()->getVars();
        foreach ($keys as $expectedKey) {
            static::assertArrayHasKey($expectedKey, $viewVars);
        }
        foreach ($viewVars['data'] as $num) {
            static::assertSame(0, $num);
        }
    }
}
