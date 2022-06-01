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

use App\Utility\Applications;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\Applications Test Case
 *
 * @coversDefaultClass App\Utility\Applications
 */
class ApplicationsTest extends TestCase
{
    /**
     * Test `list` method.
     *
     * @return void
     * @covers ::list()
     */
    public function testList(): void
    {
        $expected = [['id' => 1, 'name' => 'sample']];
        Applications::$applications = $expected;
        $actual = Applications::list();
        static::assertEquals($expected, $actual);
        Applications::$applications = [];

        $response = [
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'dummy',
                    ],
                ],
                [
                    'id' => 2,
                    'attributes' => [
                        'name' => 'test',
                    ],
                ],
            ],
        ];
        $expected = [1 => 'dummy', 2 => 'test'];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($response);
        ApiClientProvider::setApiClient($apiClient);
        $actual = Applications::list();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `list` method, exception case.
     *
     * @return void
     * @covers ::list()
     */
    public function testListException(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willThrowException(new BEditaClientException());
        ApiClientProvider::setApiClient($apiClient);
        Applications::$applications = [];
        $actual = Applications::list();
        static::assertEquals([], $actual);
    }

    /**
     * Test `getName` method
     *
     * @return void
     * @covers ::getName()
     */
    public function testGetName(): void
    {
        $response = [
            'data' => [
                [
                    'id' => 3,
                    'attributes' => [
                        'name' => 'dummy test',
                    ],
                ],
            ],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($response);
        ApiClientProvider::setApiClient($apiClient);
        $expected = 'dummy test';
        Applications::$applications = [];
        $actual = Applications::getName('3');
        static::assertEquals($expected, $actual);
    }
}
