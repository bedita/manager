<?php
declare(strict_types=1);

namespace App\Test\TestCase\Utility;

use App\Utility\ApiConfigTrait;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Utility\ApiConfigTrait} Test Case
 *
 * @coversDefaultClass \App\Utility\ApiConfigTrait
 */
class ApiConfigTraitTest extends TestCase
{
    use ApiConfigTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        Cache::clearAll();

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        ApiClientProvider::setApiClient(null);
        parent::tearDown();
    }

    /**
     * Data provider for `testReadApiConfig`.
     *
     * @return array
     */
    public function readApiConfigProvider(): array
    {
        return [
            'ok' => [
                ['Supporto'],
                [
                    [
                        'id' => 1,
                        'attributes' => ['name' => 'Gustavo', 'content' => '["Supporto"]'],
                    ],
                ],
            ],
            'nothing' => [
                null,
                [
                    [],
                ],
            ],
        ];
    }

    /**
     * Test `readApiConfig`.
     *
     * @param string $key The config key
     * @param bool $found Data found per config
     * @return void
     * @covers ::readApiConfig()
     * @covers ::fetchConfig()
     * @dataProvider readApiConfigProvider()
     */
    public function testReadApiCache($expected, array $content): void
    {
        Configure::delete('Gustavo');
        Cache::write(CacheTools::cacheKey(static::$cacheKey), $content);
        $this->readApiConfig();
        $result = Configure::read('Gustavo');
        static::assertEquals($expected, $result);
    }

    public function testReadApiConfig(): void
    {
        $this->prepareClient();
        Configure::delete('Gustavo');
        $this->readApiConfig();
        $result = Configure::read('Gustavo');
        static::assertEquals([], $result);
    }

    /**
     * Test `readApiConfig`, exception case.
     *
     * @return void
     * @covers ::readApiConfig()
     * @covers ::fetchConfig()
     */
    public function testReadException(): void
    {
        $expectedException = new BEditaClientException();
        // mock GET /config to throw BEditaClientException
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/config')
            ->willThrowException($expectedException);
        ApiClientProvider::setApiClient($apiClient);

        $this->readApiConfig();

        static::assertNull(Cache::read(CacheTools::cacheKey(static::$cacheKey)));
    }

    /**
     * Test `managerApplicationId`.
     *
     * @return void
     * @covers ::managerApplicationId()
     */
    public function testManagerApplicationId(): void
    {
        $this->prepareClient();

        $actual = $this->managerApplicationId();
        static::assertEquals(456, $actual);
    }

    /**
     * Test `save`.
     *
     * @return void
     * @covers ::saveApiConfig()
     * @covers ::fetchConfig()
     * @covers ::isAppConfig()
     */
    public function testSave(): void
    {
        $this->prepareClient();

        // PATCH
        $this->saveApiConfig('Gustavo', ['Supporto']);
        $actual = Cache::read(CacheTools::cacheKey(static::$cacheKey));
        static::assertEmpty($actual);
        $actual = Cache::read(CacheTools::cacheKey('properties'));
        static::assertEmpty($actual);

        // POST
        $this->saveApiConfig('GustavoTest', ['SupportoTest']);
        $actual = Cache::read(CacheTools::cacheKey(static::$cacheKey));
        static::assertEmpty($actual);
    }

    /**
     * Prepare API client mock
     *
     * @return void
     */
    private function prepareClient(): void
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
                                    [
                                        'id' => 123,
                                        'attributes' => [
                                            'name' => 'Gustavo',
                                            'content' => '{}',
                                            'context' => 'app',
                                            'application_id' => 456,
                                        ],
                                    ],
                                    [
                                        'id' => 666,
                                        'attributes' => [
                                            'name' => 'Supporto',
                                        ],
                                    ],
                                ],
                            ];
                        }
                        if ($param === '/admin/applications') {
                            return [
                                'data' => [
                                    ['id' => 456, 'attributes' => ['name' => 'manager']],
                                ],
                            ];
                        }
                    }
                )
            );
        $apiClient->method('post')->willReturn([]);
        $apiClient->method('patch')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
    }
}
