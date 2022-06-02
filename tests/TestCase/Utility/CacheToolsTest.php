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
use Cake\TestSuite\TestCase;

/**
 * App\Utility\CacheTools Test Case
 *
 * @coversDefaultClass App\Utility\CacheTools
 */
class CacheToolsTest extends TestCase
{
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
}
