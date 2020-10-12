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
                    'associations' => [],
                    'relations' => [],
                ],
                [
                    'type' => 'object',
                ],
                'you-are-not-my-type',
            ],
            'type from config' => [
                [
                    'type' => 'object',
                    'associations' => [],
                    'relations' => [],
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
            'no schema' => [
                false,
                [],
                'some-type',
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
     * @covers ::cacheKey()
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
    public function testRelationMethods()
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

    /**
     * Test `fetchSchema` for `users`.
     *
     * @return void
     * @covers ::fetchSchema()
     * @covers ::fetchRoles()
     */
    public function testFetchRoles()
    {
        $roles = [
            'data' => [
                [
                    'attributes' => [
                        'name' => 'admin',
                    ],
                ],
                [
                    'attributes' => [
                        'name' => 'manager',
                    ],
                ],
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
                ->willReturn($roles);
        $apiClient->method('schema')
                ->willReturn(['type' => 'object']);

        ApiClientProvider::setApiClient($apiClient);
        Cache::clearAll();
        $result = $this->Schema->getSchema('users');
        static::assertNotEmpty($result);
        static::assertNotEmpty($result['properties']['roles']);

        $expected = [
            'type' => 'string',
            'enum' => ['admin', 'manager'],
        ];
        static::assertEquals($expected, $result['properties']['roles']);
    }

    /**
     * Test `fetchSchema` for `categories`.
     *
     * @return void
     * @covers ::fetchSchema()
     * @covers ::fetchCategories()
     */
    public function testFetchCategories()
    {
        $categories = [
            'data' => [
                [
                    'id' => '1',
                    'attributes' => [
                        'name' => 'cat-1',
                        'label' => 'Category 1',
                    ],
                ],
                [
                    'id' => '2',
                    'attributes' => [
                        'name' => 'cat-2',
                        'label' => 'Category 2',
                    ],
                ],
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($categories);
        $apiClient->method('schema')
            ->willReturn(['type' => 'object']);

        ApiClientProvider::setApiClient($apiClient);
        Cache::clearAll();
        $result = $this->Schema->getSchema('documents');
        static::assertNotEmpty($result);
        static::assertNotEmpty($result['categories']);

        $expected = [
            [
                'name' => 'cat-1',
                'label' => 'Category 1',
            ],
            [
                'name' => 'cat-2',
                'label' => 'Category 2',
            ],
        ];
        static::assertEquals($expected, $result['categories']);
    }

    /**
     * Test `fetchCategories` with API error.
     *
     * @return void
     * @covers ::fetchCategories()
     */
    public function testFetchCategoriesFail()
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->will($this->returnCallback([$this, 'mockApiCallback']));
        $apiClient->method('schema')
            ->willReturn(['type' => 'object']);

        ApiClientProvider::setApiClient($apiClient);
        Cache::clearAll();
        $result = $this->Schema->getSchema('documents');
        $expected = [
            'type' => 'object',
            'associations' => [],
            'relations' => [],
        ];
        static::assertEquals($expected, $result);
    }

    /**
     * Mock API callback
     *
     * @return array
     */
    public function mockApiCallback(): array
    {
        $args = func_get_args();
        if ($args[0] === '/model/categories?filter[type]=documents') {
            throw new BEditaClientException('');
        }

        return [];
    }

    /**
     * Test `fetchObjectTypeMeta` method.
     *
     * @return void
     * @covers ::fetchObjectTypeMeta()
     */
    public function testFetchObjectTypeMeta()
    {
        $objectType = [
            'data' => [
                'attributes' => [
                    'associations' => ['Categories'],
                ],
                'meta' => [
                    'relations' => ['has_media'],
                ],
            ],
        ];

        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($objectType);
        $apiClient->method('schema')
            ->willReturn(['type' => 'object']);

        ApiClientProvider::setApiClient($apiClient);

        $result = $this->Schema->getSchema('documents');
        static::assertEquals(['Categories'], $result['associations']);
        static::assertEquals(['has_media' => 0], $result['relations']);
    }

    /**
     * Provider for `testRelationsSchema`.
     *
     * @return array
     */
    public function relationsSchemaProvider(): array
    {
        return [
            'empty data' => [
                [], // schema
                [], // relationships
                [], // types
                [], // expected
            ],
            'full example' => [
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['objects'],
                    ],
                ], // schema
                [
                    'hates' => [],
                    'loves' => [],
                ], // relationships
                ['mices', 'elefants', 'cats', 'dogs'], // types
                [
                    'hates' => [
                        'left' => ['elefants'],
                        'right' => ['mices'],
                    ],
                    'loves' => [
                        'left' => ['robots'],
                        'right' => ['mices', 'elefants', 'cats', 'dogs'],
                    ],
                ], // expected
            ],
        ];
    }

    /**
     * Test `relationsSchema` method
     *
     * @param array $schema The schema
     * @param array $relationships The relationships
     * @param array $types The types
     * @param array $expected The expected result
     * @return void
     * @dataProvider relationsSchemaProvider()
     * @covers ::relationsSchema()
     */
    public function testRelationsSchema(array $schema, array $relationships, array $types, array $expected): void
    {
        $actual = $this->Schema->relationsSchema($schema, $relationships, $types);
        static::assertEquals($expected, $actual);
    }
}
