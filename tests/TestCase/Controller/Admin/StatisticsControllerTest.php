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

    /**
     * Test `fetchCount` method
     *
     * @return void
     * @covers ::fetchCount()
     */
    public function testFetchCountZero(): void
    {
        $controller = new class (new ServerRequest()) extends StatisticsController {
            public function fetchCount(string $objectType, string $from, string $to): int
            {
                return parent::fetchCount($objectType, $from, $to);
            }
        };
        $actual = $controller->fetchCount('documents', '2999-01-01', '2999-12-31');
        static::assertSame(0, $actual);
    }

    /**
     * Test `fetchCount` method when exception is thrown
     *
     * @return void
     * @covers ::index()
     * @covers ::fetchCount()
     */
    public function testFetchCountException(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'query' => [
                'objectType' => 'abcdefghi',
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
        $viewVars = (array)$this->StatisticsController->viewBuilder()->getVars();
        foreach ($viewVars['data'] as $num) {
            static::assertSame(0, $num);
        }
    }

    /**
     * Data provider for `testIntervals` test case.
     *
     * @return array
     */
    public function intervalsProvider(): array
    {
        return [
            'case day: return interval with just one day' => [
                [
                    'day' => '30 june 2024',
                    'objectType' => 'documents',
                    'year' => 2024,
                ],
                ['data' => [0]],
            ],
            'case week: return interval with all days of the week, ~ 7 days' => [
                [
                    'week' => 1,
                    'month' => 'june',
                    'objectType' => 'documents',
                    'year' => 2024,
                ],
                ['data' => [0, 0, 0, 0, 0, 0, 0]],
            ],
            'case month: return interval with 4/5 weeks' => [
                [
                    'month' => 'june',
                    'objectType' => 'documents',
                    'year' => 2024,
                ],
                ['data' => [0, 0, 0, 0, 0]],
            ],
            'case year: return interval with 12 months' => [
                [
                    'objectType' => 'documents',
                    'year' => 2024,
                ],
                ['data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
            ],
        ];
    }

    /**
     * Test `intervals` method
     *
     * @return void
     * @covers ::index()
     * @covers ::intervals()
     * @dataProvider intervalsProvider()
     */
    public function testIntervals(array $query, array $expected): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'query' => $query,
        ]);
        $this->StatisticsController = new StatisticsController($request);
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
        $this->StatisticsController->index();
        $actual = (array)$this->StatisticsController->viewBuilder()->getVars();
        static::assertSame($expected, $actual);
    }
}
