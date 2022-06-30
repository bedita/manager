<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ConfigComponent;
use App\Controller\Component\FlashComponent;
use App\Test\TestCase\Controller\BaseControllerTest;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use Cake\Cache\Cache;
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Config);
        unset($this->Flash);
        unset($this->controller);
        Cache::disable();

        parent::tearDown();
    }

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
     * Data provider for testRead.
     *
     * @return array
     */
    public function readProvider(): array
    {
        return [
            'key not available' => [
                'whatever-not-available',
                false,
            ],
            'modules' => [
                'Modules',
                true,
            ],
        ];
    }

    /**
     * Test `read`.
     *
     * @param string $key The config key
     * @param bool $found Data found per config
     * @return void
     * @covers ::read()
     * @covers ::fetchConfig()
     * @dataProvider readProvider()
     */
    public function testRead(string $key, bool $found): void
    {
        if ($found) {
            Cache::write(CacheTools::cacheKey(sprintf('config.%s', $key)), ['attributes' => ['content' => '{"documents":{"color":"#D95700"},"folders":{"color":"#6FB78B"}}']]);
        }
        $this->prepareConfig();
        $actual = $this->Config->read($key);
        if ($found) {
            static::assertNotEmpty($actual);
        } else {
            static::assertEmpty($actual);
        }
    }

    /**
     * Test `read`, exception case.
     *
     * @return void
     * @covers ::read()
     * @covers ::fetchConfig()
     */
    public function testReadException(): void
    {
        $this->prepareConfig();
        $expected = 'Something went wrong';
        $expectedException = new BEditaClientException($expected);
        // mock GET /config to throw BEditaClientException
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willThrowException($expectedException);

        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $this->Config->read('whatever');
        $actual = $this->Config->getController()->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `managerApplicationId`.
     *
     * @return void
     * @covers ::managerApplicationId()
     */
    public function testManagerApplicationId(): void
    {
        $this->prepareConfig();

        // mock GET /admin/applications
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/admin/applications')
            ->willReturn([
                'data' => [
                    ['id' => 456, 'attributes' => ['name' => 'manager']],
                ],
            ]);
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals(456, $actual);
    }

    /**
     * Test `save`.
     *
     * @return void
     * @covers ::save()
     * @covers ::fetchConfig()
     * @covers ::managerApplicationId()
     */
    public function testSave(): void
    {
        $this->prepareConfig();

        // post
        $key = 'Modules';
        $data = $this->Config->read($key);
        $this->Config->save($key, $data);
        $actual = Cache::read(CacheTools::cacheKey(sprintf('config.%s', $key)));
        static::assertEmpty($actual);

        // patch
        // mock GET /config
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willReturn([
                'data' => [
                    ['id' => 123, 'attributes' => ['name' => 'Modules', 'context' => 'app', 'content' => '{}', 'application_id' => 1]],
                ],
            ]);
        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
        $data = $this->Config->read($key);
        $this->Config->save($key, $data);
        $actual = Cache::read(CacheTools::cacheKey(sprintf('config.%s', $key)));
        static::assertEmpty($actual);
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

        // mock GET /config and /admin/applications.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->will(
                $this->returnCallback(
                    function ($param) {
                        if ($param === '/config') {
                            return [
                                'data' => [
                                    ['id' => 1, 'attributes' => ['name' => 'Gustavo']],
                                    ['id' => 123, 'attributes' => ['name' => 'Modules', 'content' => '{}']],
                                    ['id' => 456, 'attributes' => ['name' => 'Whatever']],
                                ],
                            ];
                        }
                        if ($param === '/admin/applications') {
                            return [
                                'data' => [
                                    ['id' => 1, 'attributes' => ['name' => 'gustavo']],
                                    ['id' => 123456789, 'attributes' => ['name' => 'manager']],
                                    ['id' => 999999999, 'attributes' => ['name' => 'whatever']],
                                ],
                            ];
                        }
                    }
                )
            );

        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
    }
}
