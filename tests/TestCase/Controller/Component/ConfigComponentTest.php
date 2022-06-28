<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ConfigComponent;
use App\Controller\Component\FlashComponent;
use App\Test\TestCase\Controller\BaseControllerTest;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;

/**
 * {@see \App\Controller\Component\ConfigComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ConfigComponent
 */
class ConfigComponentTest extends BaseControllerTest
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ConfigComponent
     */
    protected $Config;

    /**
     * Controller for test
     *
     * @var \Cake\Controller\Controller
     */
    protected $controller;

    /**
     * Test startup
     *
     * @return void
     * @covers ::startup()
     */
    public function testStartup(): void
    {
        $this->prepareConfig();
        $this->Config->startup();
        static::assertInstanceOf(BEditaClient::class, $this->Config->apiClient);
    }

    /**
     * Test modules
     *
     * @return void
     * @covers ::modules()
     */
    public function testModules(): void
    {
        $this->prepareConfig();
        $actual = $this->Config->modules();
        static::assertIsArray($actual);
        static::assertEmpty($actual);
    }

    /**
     * Test modules
     *
     * @return void
     * @covers ::modules()
     */
    public function testModulesException(): void
    {
        $this->prepareConfig();
        // mock GET /config.
        $exception = new BEditaClientException('testModulesException');
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willThrowException($exception);
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $actual = $this->Config->modules();
        static::assertIsArray($actual);
        static::assertNotEmpty($actual);
    }

    /**
     * Prepare config component for test
     *
     * @return void
     */
    private function prepareConfig(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new Controller($request);
        $registry = $this->controller->components();
        /** @var \App\Controller\Component\ConfigComponent $configComponent */
        $configComponent = $registry->load(ConfigComponent::class);
        $this->Config = $configComponent;
        /** @var \App\Controller\Component\FlashComponent $flashComponent */
        $flashComponent = $registry->load(FlashComponent::class);
        $this->Flash = $flashComponent;
        $this->controller->Flash = $this->Flash;

        // mock GET /config.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([
                'data' => [
                    ['id' => 123, 'attributes' => ['name' => 'Modules', 'content' => '{}']],
                    ['id' => 456, 'attributes' => ['name' => 'Whatever']],
                ],
            ]);
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
    }
}
