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

use App\Controller\Component\CategoriesComponent;
use App\Controller\Component\SchemaComponent;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\CategoriesComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\CategoriesComponent
 * @uses \App\Controller\Component\CategoriesComponent
 */
class CategoriesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\CategoriesComponent
     */
    public $Categories;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $registry = new ComponentRegistry();
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
     * @covers ::index()
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
            ->will($this->returnCallback(function () {
                $args = func_get_args();

                return $args[1]; // options
            }));
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
     * @covers ::names()
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
     * @covers ::map()
     */
    public function testMap(): void
    {
        // empty
        $actual = $this->Categories->map([]);
        static::assertEmpty($actual);

        // reponse
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
     * @covers ::tree()
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
        ];
        $expected = [
            '_' => [123],
            123 => [456],
            456 => [789],
        ];
        $actual = $this->Categories->tree($map);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getAvailableRoots`.
     *
     * @return void
     * @covers ::getAvailableRoots()
     */
    public function testGetAvailableRoots(): void
    {
        // empty map
        $map = [];
        $expected = ['' => '-'];
        $actual = $this->Categories->getAvailableRoots($map);
        static::assertEquals($expected, $actual);

        // not empty map
        $map = [
            ['id' => 123, 'attributes' => ['label' => 'Dummy 123', 'name' => 'dummy-123']],
            ['id' => 456, 'attributes' => ['name' => 'dummy-456']],
            ['id' => 789, 'attributes' => ['label' => 'Dummy 789', 'name' => 'dummy-789', 'parent_id' => 456]],
        ];
        $expected = [
            '' => '-',
            123 => 'Dummy 123',
            456 => 'dummy-456',
        ];
        $actual = $this->Categories->getAvailableRoots($map);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `save`.
     *
     * @return void
     * @covers ::save()
     * @covers ::invalidateSchemaCache()
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
     * @covers ::delete()
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
}
