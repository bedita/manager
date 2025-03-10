<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\TagsController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\TagsController} Test Case
 */
#[CoversClass(TagsController::class)]
#[CoversMethod(TagsController::class, 'beforeRender')]
#[CoversMethod(TagsController::class, 'index')]
#[CoversMethod(TagsController::class, 'initialize')]
class TagsControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\TagsController
     */
    public TagsController $controller;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

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
            'resource_type' => 'tags',
        ],
    ];

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->controller = new TagsController($request);
        $this->setupApi();
    }

    /**
     * Test `initialize` method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->setupController();
        $this->controller->index();
        static::assertInstanceOf('App\Controller\Component\ProjectConfigurationComponent', $this->controller->ProjectConfiguration);
    }

    /**
     * Test `index` method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->setupController();
        $this->controller->index();
        static::assertTrue($this->controller->viewBuilder()->getVar('hideSidebar'));
        static::assertSame(['_name' => 'tags:index'], $this->controller->viewBuilder()->getVar('redirTo'));
    }

    /**
     * Test `beforeRender` method
     *
     * @return void
     */
    public function testBeforeRender(): void
    {
        $this->setupController();
        $this->controller->dispatchEvent('Controller.beforeRender');
        static::assertSame(['_name' => 'tags:index'], $this->controller->viewBuilder()->getVar('moduleLink'));
    }
}
