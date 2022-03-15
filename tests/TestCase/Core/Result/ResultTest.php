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

use App\Core\Result\Result;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Core\Result\Result} Test Case
 *
 * @coversDefaultClass \App\Core\Result\Result
 */
class ResultTest extends TestCase
{
    /**
     * Data provider for `testAddMessage`
     *
     * @return array
     */
    public function addMessageProvider(): array
    {
        return [
            'info' => [
                'info message<br/>',
                'info',
                'info message',
            ],
            'counter' => [
                0,
                'created',
                'a message',
            ],
        ];
    }

    /**
     * Test `addMessage` method
     *
     * @param mixed $expected Expected value
     * @param string $name Message name
     * @param string $msg Message string
     * @return void
     * @dataProvider addMessageProvider
     * @covers ::addMessage()
     */
    public function testAddMessage($expected, string $name, string $msg): void
    {
        $result = new Result();
        $result->addMessage('info', 'info message');
        if (property_exists($result, $name)) {
            static::assertEquals($expected, $result->{$name});
        }
    }

    /**
     * Data provider for `testAddMessage`
     *
     * @return array
     */
    public function incrementProvider(): array
    {
        return [
            'errors' => [
                1,
                'errors',
            ],
            'message' => [
                '',
                'info',
            ],
        ];
    }

    /**
     * Test `increment` method
     *
     * @param mixed $expected Expected value
     * @param string $name Counter name
     * @return void
     * @dataProvider incrementProvider
     * @covers ::increment()
     */
    public function testIncrement($expected, string $name): void
    {
        $result = new Result();
        $result->increment($name);
        if (property_exists($result, $name)) {
            static::assertEquals($expected, $result->{$name});
        }
    }
}
