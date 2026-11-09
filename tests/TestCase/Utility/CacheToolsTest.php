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
namespace App\Test\TestCase\Utility;

use App\Utility\CacheTools;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * {@see \App\Utility\CacheTools} Test Case
 */
#[CoversClass(CacheTools::class)]
#[CoversMethod(CacheTools::class, 'cacheKey')]
#[CoversMethod(CacheTools::class, 'existsCount')]
#[CoversMethod(CacheTools::class, 'getModuleCount')]
#[CoversMethod(CacheTools::class, 'homeCacheKey')]
#[CoversMethod(CacheTools::class, 'setModuleCount')]
#[CoversMethod(CacheTools::class, 'getUsersInfo')]
class CacheToolsTest extends TestCase
{
    protected BEditaClient $originalClient;

    protected BEditaClient&MockObject $apiClient;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::enable();
        $this->originalClient = ApiClientProvider::getApiClient();
        $this->apiClient = $this->createMock(BEditaClient::class);
        $this->apiClient->method('getApiBaseUrl')->willReturn('https://cache-tools-test.example');
        ApiClientProvider::setApiClient($this->apiClient);
        foreach (['user_1', 'user_2', 'user_3', 'statistics_test_count'] as $name) {
            Cache::delete(CacheTools::cacheKey($name));
        }
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        foreach (['user_1', 'user_2', 'user_3', 'statistics_test_count'] as $name) {
            Cache::delete(CacheTools::cacheKey($name));
        }
        ApiClientProvider::setApiClient($this->originalClient);
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Test `cacheKey` method.
     *
     * @return void
     */
    public function testCacheKey(): void
    {
        $apiSignature = md5(ApiClientProvider::getApiClient()->getApiBaseUrl());
        $expected = sprintf('%s_%s', 'test', $apiSignature);
        $actual = CacheTools::cacheKey('test');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getModuleCount` and `setModuleCount` methods.
     *
     * @return void
     */
    public function testGetSetModuleCount(): void
    {
        $moduleName = 'test';
        $response = [
            'meta' => [
                'pagination' => [
                    'count' => 42,
                ],
            ],
        ];
        $exists = CacheTools::existsCount($moduleName);
        static::assertFalse($exists);
        CacheTools::setModuleCount($response, $moduleName);
        $expected = 42;
        $actual = CacheTools::getModuleCount($moduleName);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `homeCacheKey`.
     *
     * @return void
     */
    public function testHomeCacheKey(): void
    {
        $userId = 123;
        $apiSignature = md5(ApiClientProvider::getApiClient()->getApiBaseUrl());
        $expected = sprintf('home_%d_%s', $userId, $apiSignature);
        $actual = CacheTools::homeCacheKey($userId);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getUsersInfo` method.
     *
     * @return void
     */
    public function testGetUsersInfo(): void
    {
        $first = ['id' => '1', 'attributes' => ['name' => 'First']];
        $second = ['id' => '2', 'attributes' => ['name' => 'Second']];
        $this->apiClient->expects(static::once())->method('get')
            ->with('/users', [
                'filter' => ['id' => [1, 2]],
                'fields' => 'name,surname,email,username',
                'page_size' => 2,
            ])
            ->willReturn(['data' => [$first, $second]]);

        static::assertSame([1 => $first, 2 => $second], CacheTools::getUsersInfo([1, 2, 1]));
        static::assertSame($first, Cache::read(CacheTools::cacheKey('user_1')));
        static::assertSame($second, Cache::read(CacheTools::cacheKey('user_2')));
        static::assertSame([2 => $second], CacheTools::getUsersInfo([2]));
        static::assertSame([2 => $second, 1 => $first], CacheTools::getUsersInfo([2, 1]));
    }

    /**
     * Only uncached IDs are fetched when another page reuses users.
     *
     * @return void
     */
    public function testGetUsersInfoMixedCache(): void
    {
        $first = ['id' => '1', 'attributes' => ['name' => 'First']];
        $second = ['id' => '2', 'attributes' => ['name' => 'Second']];
        $third = ['id' => '3', 'attributes' => ['name' => 'Third']];
        Cache::write(CacheTools::cacheKey('user_1'), $first);
        $this->apiClient->expects(static::once())->method('get')
            ->with('/users', [
                'filter' => ['id' => [2, 3]],
                'fields' => 'name,surname,email,username',
                'page_size' => 2,
            ])
            ->willReturn(['data' => [$second, $third]]);

        static::assertSame(
            [1 => $first, 2 => $second, 3 => $third],
            CacheTools::getUsersInfo([1, 2, 3, 2]),
        );
        static::assertSame([3 => $third, 1 => $first], CacheTools::getUsersInfo([3, 1]));
    }

    /**
     * Unavailable users do not trigger repeated lookups before cache expiry.
     *
     * @return void
     */
    public function testGetUsersInfoMissing(): void
    {
        $this->apiClient->expects(static::once())->method('get')->willReturn(['data' => []]);
        static::assertSame([], CacheTools::getUsersInfo([1]));
        static::assertSame([], CacheTools::getUsersInfo([1]));
    }

    /**
     * API failures are surfaced rather than cached as successful empty responses.
     *
     * @return void
     */
    public function testGetUsersInfoFailure(): void
    {
        $this->apiClient->expects(static::once())->method('get')
            ->willThrowException(new BEditaClientException('Forbidden', 403));
        $this->expectException(BEditaClientException::class);
        try {
            CacheTools::getUsersInfo([1]);
        } finally {
            static::assertNull(Cache::read(CacheTools::cacheKey('user_1')));
        }
    }

    /**
     * Test `getUsersInfo` with empty array.
     *
     * @return void
     */
    public function testGetUsersInfoEmpty(): void
    {
        $this->apiClient->expects(static::never())->method('get');
        static::assertSame([], CacheTools::getUsersInfo([]));
    }
}
