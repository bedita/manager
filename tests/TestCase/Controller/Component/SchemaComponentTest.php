<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\SchemaComponent;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\SchemaComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\SchemaComponent
 */
class SchemaComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\SchemaComponent
     */
    public $Schema;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Auth');
        $this->Schema = $registry->load(SchemaComponent::class);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Schema);

        // reset client, force new client creation
        ApiClientProvider::setApiClient(null);
        parent::tearDown();
    }

    /**
     * Data provider for `testGetSchema` test case.
     *
     * @return array
     */
    public function getSchemaProvider(): array
    {
        return [
            'type as argument' => [
                [
                    'type' => 'object',
                ],
                [
                    'type' => 'object',
                ],
                'you-are-not-my-type',
            ],
            'type from config' => [
                [
                    'type' => 'object',
                ],
                [
                    'type' => 'object',
                ],
                null,
                [
                    'type' => 'objects',
                ],
            ],
            'client exception' => [
                false,
                new BEditaClientException('I am a client exception'),
                'you-are-not-my-type',
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
                'you-are-not-my-type',
            ],
        ];
    }

    /**
     * Test `getSchema()` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param array|\Exception $schema Response to `/schema/:type` endpoint.
     * @param string|null $type Type to get schema for.
     * @param array $config Component configuration.
     * @return void
     *
     * @dataProvider getSchemaProvider()
     * @covers ::fetchSchema()
     * @covers ::getSchema()
     * @covers ::loadWithRevision()
     */
    public function testGetSchema($expected, $schema, ?string $type, array $config = []): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($schema instanceof \Exception) {
            $apiClient->method('schema')
                ->with($type ?: $config['type'])
                ->willThrowException($schema);
        } else {
            $apiClient->method('schema')
                ->with($type ?: $config['type'])
                ->willReturn($schema);
        }

        ApiClientProvider::setApiClient($apiClient);
        $this->Schema->setConfig($config);

        $actual = $this->Schema->getSchema($type);

        static::assertSame($expected, $actual);
    }

    /**
     * Test load internal schema from configuration.
     *
     * @return void
     * @covers ::getSchema()
     * @covers ::loadInternalSchema
     */
    public function testInternalSchema()
    {
        $this->Schema->setConfig([
            'type' => 'object_types',
            'internalSchema' => true,
        ]);

        $result = $this->Schema->getSchema();
        static::assertNotEmpty($result);
        static::assertNotEmpty($result['properties']);
    }

    /**
     * Test load relations schema.
     *
     * @return void
     * @covers ::getRelationsSchema()
     * @covers ::fetchRelationData()
     */
    public function testRelationsSchema()
    {
        $relations = [
            'data' => [
                [
                    'id' => '24',
                    'type' => 'relations',
                    'attributes' => [
                        'name' => 'poster',
                        'inverse_name' => 'poster_of',
                    ],
                    'relationships' => [
                        'left_object_types' => [
                            'data' => [
                                [
                                    'id' => '3',
                                    'type' => 'object_types',
                                ],
                            ],
                        ],
                        'right_object_types' => [
                            'data' => [
                                [
                                    'id' => '9',
                                    'type' => 'object_types',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '3',
                    'type' => 'object_types',
                    'attributes' => [
                        'name' => 'users',
                    ],
                ],
                [
                    'id' => '9',
                    'type' => 'object_types',
                    'attributes' => [
                        'name' => 'images',
                    ],
                ],
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
                ->willReturn($relations);

        ApiClientProvider::setApiClient($apiClient);
        $result = $this->Schema->getRelationsSchema();
        static::assertNotEmpty($result);
        // count is 3 because `children` relation is automatically added
        static::assertEquals(3, count($result));
        static::assertArrayHasKey('poster', $result);
        static::assertArrayHasKey('poster_of', $result);
        static::assertArrayHasKey('children', $result);
    }

    /**
     * Test load internal schema from configuration.
     *
     * @return void
     * @covers ::getRelationsSchema()
     * @covers ::fetchRelationData()
     */
    public function testFailRelationsSchema()
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
                ->willThrowException(new BEditaClientException('Client Exception'));

        ApiClientProvider::setApiClient($apiClient);
        Cache::clearAll();
        $result = $this->Schema->getRelationsSchema();
        static::assertEmpty($result);

        $message = $this->Schema->request->getSession()->read('Flash.flash.0.message');
        static::assertEquals('Client Exception', $message);
    }
}
