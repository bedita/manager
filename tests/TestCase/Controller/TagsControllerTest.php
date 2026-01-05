<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\TagsController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\TagsController} Test Case
 */
#[CoversClass(TagsController::class)]
#[CoversMethod(TagsController::class, 'beforeRender')]
#[CoversMethod(TagsController::class, 'create')]
#[CoversMethod(TagsController::class, 'delete')]
#[CoversMethod(TagsController::class, 'index')]
#[CoversMethod(TagsController::class, 'initialize')]
#[CoversMethod(TagsController::class, 'patch')]
#[CoversMethod(TagsController::class, 'search')]
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
        $actual = $this->controller->FormProtection->getConfig('unlockedActions');
        $expected = ['create', 'patch', 'delete'];
        static::assertEquals($expected, $actual);
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

    /**
     * Test `create`, `patch`, `delete`, `search` methods
     *
     * @return void
     */
    public function testMulti(): void
    {
        // create with error
        $this->setupController(['environment' => ['REQUEST_METHOD' => 'POST']]);
        $this->controller->create();
        static::assertNotEmpty($this->controller->viewBuilder()->getVar('error'));

        // create ok
        $data = [
            'type' => 'tags',
            'attributes' => [
                'name' => 'my-dummy-test-tag',
                'label' => 'My Dummy Test Tag',
                'labels' => [
                    'default' => 'My Dummy Test Tag',
                ],
                'enabled' => false,
            ],
        ];
        $request = $this->controller->getRequest()->withData('data', $data);
        $this->controller->setRequest($request);
        $this->controller->create();
        static::assertEmpty($this->controller->viewBuilder()->getVar('error'));
        $response = $this->controller->viewBuilder()->getVar('response');
        static::assertNotEmpty($response);
        $id = $response['data']['id'];

        // patch with error
        $this->setupController(['environment' => ['REQUEST_METHOD' => 'PATCH']]);
        $this->controller->getRequest()->withData('name', 'test');
        $this->controller->patch('test');
        static::assertNotEmpty($this->controller->viewBuilder()->getVar('error'));
        static::assertNotEquals('ok', $this->controller->viewBuilder()->getVar('response'));

        // patch ok
        $data = [
            'id' => $id,
            'type' => 'tags',
            'attributes' => [
                'name' => 'my-dummy-test-tag',
                'label' => 'My Dummy Test Tag',
                'labels' => [
                    'default' => 'My Dummy Test Tag',
                ],
                'enabled' => true,
            ],
        ];
        $request = $this->controller->getRequest()->withData('data', $data);
        $this->controller->setRequest($request);
        $this->controller->patch($id);
        static::assertEquals('ok', $this->controller->viewBuilder()->getVar('response'));
        static::assertEmpty($this->controller->viewBuilder()->getVar('error'));

        // search with error
        $this->setupController(['environment' => ['REQUEST_METHOD' => 'GET']]);
        $request = $this->controller->getRequest()->withQueryParams(['filter' => 'wrong']);
        $this->controller->setRequest($request);
        $this->controller->search();
        static::assertNotEmpty($this->controller->viewBuilder()->getVar('error'));

        // search ok
        $request = $this->controller->getRequest()->withQueryParams(['filter' => ['name' => 'my-dummy-test-tag']]);
        $this->controller->setRequest($request);
        $this->controller->search();
        static::assertEmpty($this->controller->viewBuilder()->getVar('error'));
        $response = $this->controller->viewBuilder()->getVar('data');
        static::assertNotEmpty($response);
        $actual = (array)Hash::get($response, '0');
        static::assertNotEmpty($actual);
        static::assertEquals('my-dummy-test-tag', $actual['attributes']['name']);
        static::assertEquals('My Dummy Test Tag', $actual['attributes']['label']);
        static::assertEquals('My Dummy Test Tag', $actual['attributes']['labels']['default']);
        static::assertEquals(true, $actual['attributes']['enabled']);
        static::assertEquals($id, $actual['id']);

        // delete with error
        $this->setupController(['environment' => ['REQUEST_METHOD' => 'POST']]);
        $this->controller->delete('test');
        static::assertNotEmpty($this->controller->viewBuilder()->getVar('error'));
        static::assertNotEquals('ok', $this->controller->viewBuilder()->getVar('response'));

        // delete ok
        $this->setupController(['environment' => ['REQUEST_METHOD' => 'POST']]);
        $this->controller->delete($id);
        static::assertEquals('ok', $this->controller->viewBuilder()->getVar('response'));
        static::assertEmpty($this->controller->viewBuilder()->getVar('error'));
    }
}
