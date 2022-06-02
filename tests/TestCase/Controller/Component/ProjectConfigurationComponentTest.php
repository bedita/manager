<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ProjectConfigurationComponent;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\ProjectConfigurationComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ProjectConfigurationComponent
 */
class ProjectConfigurationComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ProjectConfigurationComponent
     */
    public $ProjectConfiguration;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Auth');
        /** @var \App\Controller\Component\ProjectConfigurationComponent $projectConfigurationComponent */
        $projectConfigurationComponent = $registry->load(ProjectConfigurationComponent::class);
        $this->ProjectConfiguration = $projectConfigurationComponent;
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->ProjectConfiguration);

        // reset client, force new client creation
        ApiClientProvider::setApiClient(null);
        parent::tearDown();
    }

    /**
     * Data provider for `testRead` test case.
     *
     * @return array
     */
    public function readProvider(): array
    {
        return [
            'simple conf' => [
                [
                    'Simple' => ['a' => 2],
                ],
                [
                    'id' => '13',
                    'type' => 'config',
                    'attributes' => [
                        'name' => 'Simple',
                        'content' => '{"a":2}',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `read()` method.
     *
     * @param array $expected Expected result.
     * @param array $config Response from `/config` endpoint.
     * @return void
     * @dataProvider readProvider()
     * @covers ::read()
     * @covers ::fetchConfig()
     */
    public function testRead($expected, $config): void
    {
        Configure::write('Project.config', null);
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('config')
            ->willReturn([
                'data' => [$config],
            ]);
        ApiClientProvider::setApiClient($apiClient);

        $project = $this->ProjectConfiguration->read();
        static::assertSame($expected, $project);
        static::assertSame($expected, Configure::read('Project.config'));
    }

    /**
     * Test `read()` method with configured data
     *
     * @covers ::read()
     * @return void
     */
    public function testReadFromConf(): void
    {
        $data = ['Conf' => true];
        Configure::write('Project.config', $data);
        $project = $this->ProjectConfiguration->read();
        static::assertSame($data, $project);
    }

    /**
     * Test `read()` method with API Error
     *
     * @covers ::read()
     * @return void
     */
    public function testReadError(): void
    {
        Configure::write('Project.config', null);
        Cache::clear(ProjectConfigurationComponent::CACHE_CONFIG);
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('config')
            ->willThrowException(new BEditaClientException('Test'));

        ApiClientProvider::setApiClient($apiClient);

        $project = $this->ProjectConfiguration->read();
        static::assertSame([], $project);
    }
}
