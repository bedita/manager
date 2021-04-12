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

use App\Utility\File;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Utility\File} Test Case
 *
 * @coversDefaultClass \App\Utility\File
 */
class FileTest extends TestCase
{
    /**
     * Data provider for `testFormatBytes` test case.
     *
     * @return array
     */
    public function formatBytesProvider(): array
    {
        return [
            '1.000000 ' => [
                1024 ** 0, // size
                null, // precision
                '1.000000 ', // expected
            ],
            '1.000000 Kb' => [
                1024 ** 1, // size
                null, // precision
                '1.000000 Kb', // expected
            ],
            '1.000000 Mb' => [
                1024 ** 2, // size
                null, // precision
                '1.000000 Mb', // expected
            ],
            '1.000000 Gb' => [
                1024 ** 3, // size
                2, // precision
                '1.000000 Gb', // expected
            ],
            '1.000000 Tb' => [
                1024 ** 4, // size
                2, // precision
                '1.000000 Tb', // expected
            ],
        ];
    }

    /**
     * Test `formatBytes` method
     *
     * @param float $size The size
     * @param int|null $precision The precision
     * @param string $expected The expected result
     * @return void
     *
     * @covers ::formatBytes()
     * @dataProvider formatBytesProvider
     */
    public function testFormatBytes(float $size, ?int $precision, string $expected): void
    {
        if ($precision != null) {
            $actual = File::formatBytes($size, $precision);
        } else {
            $actual = File::formatBytes($size);
        }
        static::assertEquals($expected, $actual);
    }
}
