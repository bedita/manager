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

use App\Utility\Message;
use BEdita\SDK\BEditaClientException;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\Message Test Case
 *
 * @coversDefaultClass App\Utility\Message
 */
class MessageTest extends TestCase
{
    /**
     * Data provider for `testGet` test case.
     *
     * @return array
     */
    public function getProvider(): array
    {
        return [
            '400 invalid data' => [
                new BEditaClientException('[400] Invalid data', 400),
                'Invalid data',
            ],
            '401 unauthorized' => [
                new BEditaClientException('[401] Unauthorized', 401),
                'Unauthorized',
            ],
            '403 forbidden' => [
                new BEditaClientException('[403] Forbidden', 403),
                'Forbidden',
            ],
            '404 not found' => [
                new BEditaClientException('[404] Not Found', 404),
                'Not Found',
            ],
            '405 method not allowed' => [
                new BEditaClientException('[405] Method Not Allowed', 405),
                'Method Not Allowed',
            ],
            '409 conflict' => [
                new BEditaClientException('[409] Conflict', 409),
                'Conflict',
            ],
            '500 internal server error' => [
                new BEditaClientException('[500] Internal Server Error', 500),
                'Internal Server Error',
            ],
            '400 username.unique' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[username.unique]: The provided value is invalid',
                ]),
                'Invalid data. Username is in use',
            ],
            '400 email.unique' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[email.unique]: The provided value is invalid',
                ]),
                'Invalid data. Email is in use',
            ],
            '400 username._required' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[username._required]: This field is required',
                ]),
                'Invalid data. Username is required',
            ],
            '400 email.unique and username._required' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[email.unique]: The provided value is invalid [username._required]: This field is required',
                ]),
                'Invalid data. Email is in use. Username is required',
            ],
            '400 dummy._required (not translated)' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[dummy._required]: This field is required',
                ]),
                'Invalid data. [dummy is required]: This field is required',
            ],
            '400 other not translated' => [
                new BEditaClientException([
                    'status' => 400,
                    'title' => 'Invalid data',
                    'detail' => '[dummy.wrong]: This field is wrong',
                ]),
                'Invalid data. [dummy.wrong]: This field is wrong',
            ],
        ];
    }

    /**
     * Test `get` method
     *
     * @param \BEdita\SDK\BEditaClientException $error The error
     * @param string $expected The expected result
     * @return void
     * @covers ::get()
     * @covers ::__construct()
     * @covers ::prepareDetail()
     * @dataProvider getProvider
     */
    public function testGet(BEditaClientException $error, string $expected): void
    {
        $message = new Message($error);
        $actual = $message->get();
        $this->assertEquals($expected, $actual);
    }
}
