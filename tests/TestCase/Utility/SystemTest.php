<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase;

use App\Utility\System;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * App\Utility\System Test Case
 */
#[CoversClass(System::class)]
#[CoversMethod(System::class, 'compareBEditaApiVersion')]
class SystemTest extends TestCase
{
    /**
     * Data provider for `testCompareBEditaApiVersion` test case.
     *
     * @return array
     */
    public static function compareBEditaApiVersionProvider(): array
    {
        return [
            'same version' => ['4.0.0', '4.0.0', true],
            'greater version' => ['4.0.1', '4.0.0', true],
            'lower version' => ['4.0.0', '4.0.1', false],
            'different major version' => ['5.0.0', '4.0.0', false],
        ];
    }

    /**
     * Test `compareBEditaApiVersion` method
     *
     * @param string $v1 The version
     * @param string $v2 The version to compare against
     * @param bool $expected The expected result
     * @return void
     */
    #[DataProvider('compareBEditaApiVersionProvider')]
    public function testCompareBEditaApiVersion(string $v1, string $v2, bool $expected): void
    {
        $actual = System::compareBEditaApiVersion($v1, $v2);
        static::assertSame($expected, $actual);
    }
}
