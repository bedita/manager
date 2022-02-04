<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Core\Filter;

use App\Core\Result\ImportResult;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Core\Result\ImportResult} Test Case
 *
 * @coversDefaultClass \App\Core\Result\ImportResult
 */
class ImportResultTest extends TestCase
{
    /**
     * Test `__construct` method
     *
     * @return void
     * @covers ::__construct()
     */
    public function testConstruct(): void
    {
        $result = new ImportResult();
        static::assertEmpty($result->filename);
        static::assertEmpty($result->info);
        static::assertEmpty($result->warn);
        static::assertEmpty($result->error);
        static::assertEquals(0, $result->created);
        static::assertEquals(0, $result->updated);
        static::assertEquals(0, $result->errors);
    }

    /**
     * Test `reset` method
     *
     * @return void
     * @covers ::reset()
     */
    public function testReset(): void
    {
        $result = new ImportResult();
        $result->reset();
        static::assertEmpty($result->filename);
        static::assertEmpty($result->info);
        static::assertEmpty($result->warn);
        static::assertEmpty($result->error);
        static::assertEquals(0, $result->created);
        static::assertEquals(0, $result->updated);
        static::assertEquals(0, $result->errors);
    }
}
