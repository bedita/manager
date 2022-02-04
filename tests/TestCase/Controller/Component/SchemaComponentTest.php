<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\SchemaComponent;
use App\Utility\CacheTools;
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
     * Test `getSchema`, cache case.
     *
     * @return void
     * @covers ::getSchema()
     */
    public function testGetSchemaFromCache(): void
    {
        $type = 'documents';
        $schema = $this->Schema->getSchema($type);
        $revision = $schema['revision'];

        // from cache
        Cache::enable();
        $reflectionClass = new \ReflectionClass($this->Schema);
        $key = CacheTools::cacheKey($type);
        Cache::write($key, $schema, SchemaComponent::CACHE_CONFIG);

        $method = $reflectionClass->getMethod('getSchema');
        $actual = $method->invokeArgs($this->Schema, [$type, $revision]);
        static::assertEquals($schema, $actual);
        Cache::disable();
    }

    /**
     * Data provider for `testGetSchemasByType`.
     *
     * @return array
     */
    public function getSchemasByTypeProvider(): array
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'documents' => [
                [
                    'documents',
                    'users',
                ],
                [
                    'documents' => [
                        'definitions', '$id', '$schema', 'type', 'properties', 'required', 'associations', 'relations', 'revision',
                    ],
                    'users' => [
                        'definitions', '$id', '$schema', 'type', 'properties', 'required', 'associations', 'relations', 'revision',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `getSchemasByType`.
     *
     * @return void
     * @dataProvider getSchemasByTypeProvider()
     * @covers ::getSchemasByType()
     */
    public function testGetSchemasByType(array $types, array $expected): void
    {
        $schemasByType = $this->Schema->getSchemasByType($types);
        if (empty($expected)) {
            static::assertEquals($expected, $schemasByType);
        } else {
            foreach ($expected as $type => $keys) {
                $actual = array_keys($schemasByType[$type]);
                static::assertEquals($actual, $keys);
            }
        }
    }

    /**
     * Test `loadWithRevision`
     *
     * @return void
     * @covers ::loadWithRevision()
     */
    public function testLoadWithRevision(): void
    {
        $type = 'documents';
        $schema = $this->Schema->getSchema($type);
        $revision = $schema['revision'];

        // false
        $reflectionClass = new \ReflectionClass($this->Schema);
        $method = $reflectionClass->getMethod('loadWithRevision');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Schema, [$type, $revision]);
        static::assertFalse($actual);

        // from cache
        Cache::enable();

        // by type and revision
        $key = CacheTools::cacheKey($type);
        Cache::write($key, $schema, SchemaComponent::CACHE_CONFIG);
        $method = $reflectionClass->getMethod('loadWithRevision');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Schema, [$type, $revision]);
        static::assertEquals($schema, $actual);

        // wrong revision
        $actual = $method->invokeArgs($this->Schema, [$type, '123456789']);
        static::assertFalse($actual);

        // disable cache
        Cache::disable();
    }

    /**
     * Test load internal schema from configuration.
     *
     * @return void
     * @covers ::getSchema()
     * @covers ::loadInternalSchema
     */
    public function testInternalSchema(): void
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
     * @covers ::concreteTypes()
     */
    public function testRelationMethods(): void
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
                        'name' => 'media',
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
    public function testFailRelationsSchema(): void
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

        $message = $this->Schema->getController()->request->getSession()->read('Flash.flash.0.message');
        static::assertEquals('Client Exception', $message);
    }

    /**
     * Data provider for `concreteTypes()`.
     *
     * @return array
     */
    public function concreteTypesProvider(): array
    {
        return [
            'empty' => [
                [],
                [],
                [],
            ],
            'empty descendants' => [
                ['documents', 'events'],
                [],
                ['documents', 'events'],
            ],
            'types + descendants' => [
                ['documents', 'events'],
                ['documents' => ['dummy_docs', 'serious_docs']],
                ['dummy_docs', 'events', 'serious_docs'],
            ],
        ];
    }

    /**
     * Test `concreteTypes`.
     *
     * @param array $types The types
     * @param array $descendants The descendants
     * @param array $expected The expected result
     * @return void
     * @dataProvider concreteTypesProvider()
     * @covers ::concreteTypes()
     */
    public function testConcreteTypes(array $types, array $descendants, array $expected): void
    {
        $reflectionClass = new \ReflectionClass($this->Schema);
        $method = $reflectionClass->getMethod('concreteTypes');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->Schema, [$types, $descendants]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `fetchSchema` for `users`.
     *
     * @return void
     * @covers ::fetchSchema()
     * @covers ::fetchRoles()
     */
    public function testFetchRoles(): void
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
    public function testFetchCategories(): void
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
                'id' => '1',
                'parent_id' => null,
            ],
            [
                'name' => 'cat-2',
                'label' => 'Category 2',
                'id' => '2',
                'parent_id' => null,
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
    public function testFetchCategoriesFail(): void
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
    public function testFetchObjectTypeMeta(): void
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
     * Test `descendants` method on abstract type
     *
     * @covers ::descendants()
     *
     * @return void
     */
    public function testDescendants(): void
    {
        $result = $this->Schema->descendants('non-existent');
        static::assertEmpty($result);

        $result = $this->Schema->descendants('objects');
        static::assertNotEmpty($result);
    }

    /**
     * Test `objectTypesFeatures` method.
     *
     * @return void
     * @covers ::objectTypesFeatures()
     * @covers ::fetchObjectTypesFeatures()
     * @covers ::setDescendant()
     */
    public function testObjectTypesFeatures(): void
    {
        $objectTypes = [
            'data' => [
                [
                    'id' => '1',
                    'attributes' => [
                        'name' => 'objects',
                        'is_abstract' => true,
                        'parent_name' => null,
                    ],
                ],
                [
                    'id' => '2',
                    'attributes' => [
                        'name' => 'media',
                        'is_abstract' => true,
                        'parent_name' => 'objects',
                    ],
                ],
                [
                    'id' => '3',
                    'attributes' => [
                        'name' => 'images',
                        'is_abstract' => false,
                        'parent_name' => 'media',
                        'associations' => ['Streams', 'Categories'],
                    ],
                ],
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($objectTypes);
        ApiClientProvider::setApiClient($apiClient);

        Cache::clearAll();

        $result = $this->Schema->objectTypesFeatures();
        static::assertNotEmpty($result);
        static::assertNotEmpty($result['descendants']);
        static::assertNotEmpty($result['uploadable']);

        $expected = [
            'descendants' => [
                'objects' => [
                    'images',
                ],
                'media' => [
                    'images',
                ],
            ],
            'uploadable' => [
                'images',
            ],
            'categorized' => [
                'images',
            ],
        ];
        static::assertEquals($expected, $result);
    }

    /**
     * Test `objectTypesFeatures` with API error.
     *
     * @return void
     * @covers ::objectTypesFeatures()
     */
    public function testObjectTypesFeaturesFail(): void
    {
        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willThrowException(new BEditaClientException('Error'));

        ApiClientProvider::setApiClient($apiClient);
        Cache::clearAll();
        $result = $this->Schema->objectTypesFeatures();
        static::assertEmpty($result);
    }
}
