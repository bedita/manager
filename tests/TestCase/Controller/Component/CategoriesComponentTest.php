<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase\Controller\Component;

use App\Controller\CategoriesController;
use App\Controller\Component\CategoriesComponent;
use App\Controller\Component\SchemaComponent;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\Controller\Component\CategoriesComponent} Test Case
 */
#[CoversClass(CategoriesComponent::class)]
#[CoversMethod(CategoriesComponent::class, 'delete')]
#[CoversMethod(CategoriesComponent::class, 'fillRoots')]
#[CoversMethod(CategoriesComponent::class, 'getAllAvailableRoots')]
#[CoversMethod(CategoriesComponent::class, 'getAvailableRoots')]
#[CoversMethod(CategoriesComponent::class, 'hasChanged')]
#[CoversMethod(CategoriesComponent::class, 'index')]
#[CoversMethod(CategoriesComponent::class, 'invalidateSchemaCache')]
#[CoversMethod(CategoriesComponent::class, 'map')]
#[CoversMethod(CategoriesComponent::class, 'names')]
#[CoversMethod(CategoriesComponent::class, 'save')]
#[CoversMethod(CategoriesComponent::class, 'tree')]
class CategoriesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\CategoriesComponent
     */
    public CategoriesComponent $Categories;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $controller = new CategoriesController(new ServerRequest());
        $registry = new ComponentRegistry($controller);
        $this->Categories = new CategoriesComponent($registry);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Categories);

        parent::tearDown();
    }

    /**
     * Test `index`
     *
     * @return void
     */
    public function testIndex(): void
    {
        $safeClient = ApiClientProvider::getApiClient();

        // mock api /model/categories
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/model/categories')
            ->willReturnCallback(function () {
                $args = func_get_args();

                return $args[1]; // options
            });
        ApiClientProvider::setApiClient($apiClient);

        // test, default options, no type
        $expected = ['page_size' => 100];
        $actual = $this->Categories->index();
        static::assertEquals($expected, $actual);

        // test, type + options
        $options = ['filter' => ['status' => 'draft', 'type' => 'documents']];
        $expected = ['page_size' => 100] + $options;
        $actual = $this->Categories->index('documents', $options);
        static::assertEquals($expected, $actual);

        // restore api client
        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test `names`
     *
     * @return void
     */
    public function testNames(): void
    {
        $actual = $this->Categories->names('documents');
        static::assertEmpty($actual);
    }

    /**
     * Test `map`
     *
     * @return void
     */
    public function testMap(): void
    {
        // empty
        $actual = $this->Categories->map([]);
        static::assertEmpty($actual);

        // response
        $response = [
            'data' => [
                ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123']],
                ['id' => 456, 'attributes' => ['label' => 'Dummy 456', 'name' => 'dummy-456']],
                ['id' => 789, 'attributes' => ['label' => 'Dummy 789', 'name' => 'dummy-789']],
            ],
        ];
        $expected = [
            123 => ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123']],
            456 => ['id' => 456, 'attributes' => ['label' => 'Dummy 456', 'name' => 'dummy-456']],
            789 => ['id' => 789, 'attributes' => ['label' => 'Dummy 789', 'name' => 'dummy-789']],
        ];
        $actual = $this->Categories->map($response);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `tree`.
     *
     * @return void
     */
    public function testTree(): void
    {
        // empty map
        $expected = ['_' => []];
        $actual = $this->Categories->tree([]);
        static::assertEquals($expected, $actual);

        // not empty map
        $map = [
            ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123']],
            ['id' => 456, 'attributes' => ['label' => 'Dummy 456', 'name' => 'dummy-456', 'parent_id' => 123]],
            ['id' => 789, 'attributes' => ['label' => 'Dummy 789', 'name' => 'dummy-789', 'parent_id' => 456]],
            ['id' => 999, 'attributes' => ['label' => 'Dummy 0', 'name' => 'dummy-0', 'parent_id' => 456]],
        ];
        $expected = [
            '_' => [123],
            123 => [456],
            456 => [999, 789],
        ];
        $actual = $this->Categories->tree($map);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getAvailableRoots`.
     *
     * @return void
     */
    public function testGetAvailableRoots(): void
    {
        // empty map
        $map = [];
        $expected = ['' => ['id' => 0, 'label' => '-', 'name' => '', 'object_type_name' => '']];
        $actual = $this->Categories->getAvailableRoots($map);
        static::assertEquals($expected, $actual);

        // not empty map
        $map = [
            ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123', 'object_type_name' => 'documents']],
            ['id' => 456, 'attributes' => ['name' => 'dummy-456', 'object_type_name' => 'documents']],
            ['id' => 789, 'attributes' => ['label' => 'Dummy 789', 'name' => 'dummy-789', 'object_type_name' => 'documents', 'parent_id' => 456]],
        ];
        $expected = [
            '' => ['id' => 0, 'label' => '-', 'name' => '', 'object_type_name' => ''],
            123 => ['id' => 123, 'label' => 'Dummy 123', 'name' => 'dummy-123', 'object_type_name' => 'documents'],
            456 => ['id' => 456, 'label' => 'dummy-456', 'name' => 'dummy-456', 'object_type_name' => 'documents'],
        ];
        $actual = $this->Categories->getAvailableRoots($map);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getAllAvailableRoots`.
     *
     * @return void
     */
    public function testGetAllAvailableRoots(): void
    {
        $beditaApiVersion = $this->getBEditaAPIVersion();
        $response = [
            'data' => [
                ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123', 'object_type_name' => 'documents', 'parent_id' => null]],
                ['id' => 456, 'attributes' => ['name' => 'dummy-456', 'object_type_name' => 'documents', 'parent_id' => null]],
            ],
        ];
        $safeClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($response);
        ApiClientProvider::setApiClient($apiClient);
        $expected = [
            '' => ['id' => 0, 'label' => '-', 'name' => '', 'object_type_name' => ''],
            123 => ['id' => 123, 'label' => 'Dummy 123', 'name' => 'dummy-123', 'object_type_name' => 'documents'],
            456 => ['id' => 456, 'label' => 'dummy-456', 'name' => 'dummy-456', 'object_type_name' => 'documents'],
        ];
        $this->Categories->getController()->viewBuilder()->setVar('project', ['version' => $beditaApiVersion]);
        $actual = $this->Categories->getAllAvailableRoots();
        ApiClientProvider::setApiClient($safeClient);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `save`.
     *
     * @return void
     */
    public function testSave(): void
    {
        $safeClient = ApiClientProvider::getApiClient();

        Cache::enable();
        Cache::clearAll();

        // test invalidate schema too
        $key = CacheTools::cacheKey('documents');

        // mock api /model/categories
        $expected = ['data' => ['id' => 999, 'type' => 'documents', 'attributes' => ['title' => 'Fake data for save']]];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('post')
            ->with('/model/categories')
            ->willReturn($expected);
        ApiClientProvider::setApiClient($apiClient);

        // no id, use post
        $actual = $this->Categories->save(['object_type_name' => 'documents']);
        static::assertEquals($expected, $actual);
        $cached = Cache::read($key, SchemaComponent::CACHE_CONFIG);
        static::assertEmpty($cached);

        $expected = ['data' => ['id' => 999, 'type' => 'documents', 'attributes' => ['title' => 'Fake data for patch']]];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('patch')
            ->with('/model/categories/999')
            ->willReturn($expected);
        ApiClientProvider::setApiClient($apiClient);

        // id, use patch
        $actual = $this->Categories->save(['id' => 999, 'object_type_name' => 'documents']);
        static::assertEquals($expected, $actual);
        $cached = Cache::read($key, SchemaComponent::CACHE_CONFIG);
        static::assertEmpty($cached);

        Cache::disable();

        // restore api client
        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test `delete`.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $safeClient = ApiClientProvider::getApiClient();

        Cache::enable();

        // test invalidate schema too
        $key = CacheTools::cacheKey('documents');

        // mock api /model/categories
        $expected = ['data' => ['id' => 999, 'type' => 'documents', 'attributes' => ['title' => 'Fake data for delete']]];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('delete')
            ->with('/model/categories/999')
            ->willReturn($expected);
        ApiClientProvider::setApiClient($apiClient);

        $actual = $this->Categories->delete('999', 'documents');
        static::assertEquals($expected, $actual);
        $cached = Cache::read($key, SchemaComponent::CACHE_CONFIG);
        static::assertEmpty($cached);

        Cache::disable();

        // restore api client
        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Data provider for `hasChanged` test case.
     *
     * @return array
     */
    public static function hasChangedProvider(): array
    {
        return [
            'empty' => [
                [],
                [],
                false,
            ],
            'same' => [
                [['name' => 'a', 'label' => 'A'], ['name' => 'b', 'label' => 'B']],
                [['name' => 'a'], ['name' => 'b']],
                false,
            ],
            'different' => [
                [['name' => 'a', 'label' => 'A'], ['name' => 'b', 'label' => 'B']],
                [['name' => 'a'], ['name' => 'c']],
                true,
            ],
        ];
    }

    /**
     * Test `hasChanged`.
     *
     * @return void
     */
    #[DataProvider('hasChangedProvider')]
    public function testHasChanged(array $oldValue, array $newValue, bool $expected): void
    {
        $actual = $this->Categories->hasChanged($oldValue, $newValue);
        static::assertSame($expected, $actual);
    }

    /**
     * Get BEdita API version
     *
     * @return string
     */
    protected function getBEditaAPIVersion(): string
    {
        return (string)Hash::get((array)ApiClientProvider::getApiClient()->get('/home'), 'meta.version');
    }
}
