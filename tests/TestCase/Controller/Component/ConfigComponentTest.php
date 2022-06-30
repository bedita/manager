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
     * Test managerApplicationId
     *
     * @return void
     * @covers ::managerApplicationId()
     */
    public function testManagerApplicationId(): void
    {
        $this->prepareConfig();

        // managerApplicationId not empty
        $expected = 987654321;
        // set $this->Config->managerApplicationId
        $property = new \ReflectionProperty(ConfigComponent::class, 'managerApplicationId');
        $property->setAccessible(true);
        $property->setValue($this->Config, $expected);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals($expected, $actual);

        // managerApplicationId empty
        $property->setValue($this->Config, null);
        $actual = $this->Config->managerApplicationId();
        static::assertEquals(123456789, $actual);

        // exception
        $property = new \ReflectionProperty(ConfigComponent::class, 'managerApplicationId');
        $property->setAccessible(true);
        $property->setValue($this->Config, null);
        // mock GET /admin/applications.
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
        static::assertEquals(-1, $actual);
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

        $this->setMock([]);
    }

    /**
     * Set api mock
     *
     * @param array $options The options
     * @return void
     */
    private function setMock(array $options): void
    {
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
        if (!empty($options['exception'])) {
            $apiClient->method('patch')
                ->with('/admin/config/123')
                ->willThrowException($options['exception']);
        }

        // set $this->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Config, $apiClient);
    }
}
