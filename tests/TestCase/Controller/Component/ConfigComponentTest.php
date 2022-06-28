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
     * The Flash component
     *
     * @var \App\Controller\Component\FlashComponent
     */
    protected $Flash;
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
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        static::assertInstanceOf(BEditaClient::class, $property->getValue($this->Config));
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
     * Test modulesConfigId
     *
     * @return void
     * @covers ::modulesConfigId()
     */
    public function testModulesConfigId(): void
    {
        $this->prepareConfig();

        // configId not empty
        $expected = 123456789;
        // set $this->Config->configId
        $property = new \ReflectionProperty(ConfigComponent::class, 'configId');
        $property->setAccessible(true);
        $property->setValue($this->Config, $expected);
        $actual = $this->Config->modulesConfigId();
        static::assertEquals($expected, $actual);

        // configId empty
        $property->setValue($this->Config, null);
        $actual = $this->Config->modulesConfigId();
        static::assertEquals(123, $actual);

        // exception
        $property->setValue($this->Config, null);
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
        $actual = $this->Config->modulesConfigId();
        static::assertNull($actual);
    }

    /**
     * Test managerApplicationId
     *
     * @return void
     * @covers ::managerApplicationId()
     */
    public function testManagerApplicationId(): void
    {
        $this->prepareConfig();

        // managerApplicationId not empty
        $expected = 123456789;
        // set $this->Config->managerApplicationId
        $property = new \ReflectionProperty(ConfigComponent::class, 'managerApplicationId');
        $property->setAccessible(true);
        $property->setValue($this->Config, $expected);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals($expected, $actual);

        // managerApplicationId empty
        $property->setValue($this->Config, null);
        // mock GET /config.
        $exception = new BEditaClientException('testModulesException');
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/admin/applications')
            ->willReturn([
                'data' => [
                    ['id' => 123, 'attributes' => ['name' => 'manager']],
                    ['id' => 456, 'attributes' => ['name' => 'whatever']],
                ],
            ]);

        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals(123, $actual);

        // exception
        $property = new \ReflectionProperty(ConfigComponent::class, 'managerApplicationId');
        $property->setAccessible(true);
        $property->setValue($this->Config, 999);
        // mock GET /config.
        $exception = new BEditaClientException('testModulesException');
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/admin/applications')
            ->willThrowException($exception);
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals(999, $actual);
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
