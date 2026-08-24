<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AsyncJobsController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \App\Controller\Admin\AsyncJobsController} Test Case
 */
#[CoversClass(AsyncJobsController::class)]
class AsyncJobsControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\AsyncJobsController
     */
    public AsyncJobsController $AsyncJobsController;

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
            'resource_type' => 'async_jobs',
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
        $this->AsyncJobsController = new AsyncJobsController($request);
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $result = $this->AsyncJobsController->index();
        static::assertNull($result);
        $vars = $this->AsyncJobsController->viewBuilder()->getVars();
        static::assertIsArray($vars['services']);
    }

    /**
     * Test jobs method
     *
     * @return void
     */
    public function testJobs(): void
    {
        $this->AsyncJobsController->jobs();
        $vars = $this->AsyncJobsController->viewBuilder()->getVars();
        static::assertIsArray($vars['pagination']);
        static::assertIsArray($vars['jobs']);
        static::assertIsArray($vars['services']);
    }

    /**
     * Test loadAsyncJobs method
     *
     * @return void
     */
    public function testLoadAsyncJobs(): void
    {
        $request = $this->AsyncJobsController->getRequest()->withQueryParams([
            'page' => 2,
            'page_size' => 50,
            'sort' => 'created',
            'service' => 'mail',
        ]);
        $this->AsyncJobsController = new class ($request) extends AsyncJobsController {
            public function loadAsyncJobs(): void
            {
                parent::loadAsyncJobs();
            }
        };
        $this->AsyncJobsController->setRequest($request);
        $this->AsyncJobsController->loadAsyncJobs();
        $vars = $this->AsyncJobsController->viewBuilder()->getVars();
        static::assertIsArray($vars['pagination']);
        static::assertIsArray($vars['jobs']);
    }
}
