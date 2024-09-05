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
namespace App\Test\TestCase;

use App\Utility\CacheTools;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\CacheTools Test Case
 *
 * @coversDefaultClass App\Utility\CacheTools
 */
class CacheToolsTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Cache::disable();
    }

    /**
     * Test `cacheKey` method.
     *
     * @return void
     * @covers ::cacheKey()
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
     * @covers ::existsCount()
     * @covers ::setModuleCount()
     * @covers ::getModuleCount()
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
}
