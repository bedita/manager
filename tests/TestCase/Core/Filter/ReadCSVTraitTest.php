<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Core\Filter;

use App\Core\Filter\ReadCSVTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\Core\Filter\ReadCSVTrait} Test Case
 */
#[CoversClass(ReadCSVTrait::class)]
#[CoversTrait(ReadCSVTrait::class)]
#[CoversMethod(ReadCSVTrait::class, 'readCSVFile')]
class ReadCSVTraitTest extends TestCase
{
    use ReadCSVTrait;

    /**
     * Provider for testReadCSVFile
     *
     * @return array
     */
    public static function readCSVFileProvider(): array
    {
        return [
            'empty' => [
                [],
                [],
                getcwd() . '/tests/files/empty.csv',
            ],
            'import' => [
                ['Column 1', 'Column 2', 'Column 3'],
                [
                    ['Column 1' => 'Name', 'Column 2' => 'Surname', 'Column 3' => '123,23'],
                    ['Column 1' => 'Another', 'Column 2' => 'Value', 'Column 3' => '35,99'],
                ],
                getcwd() . '/tests/files/import.csv',
            ],
        ];
    }

    /**
     * Test `readCSVFile` method
     *
     * @param array $keys Expected keys
     * @param array $data Expected data
     * @param string $filepath CSV File path
     * @param array $options CSV options
     * @return void
     */
    #[DataProvider('readCSVFileProvider')]
    public function testReadCSVFile(array $keys, array $data, string $filepath, array $options = []): void
    {
        $this->readCSVFile($filepath, $options);
        static::assertEquals($keys, $this->csvKeys);
        static::assertEquals($data, $this->csvData);
    }
}
