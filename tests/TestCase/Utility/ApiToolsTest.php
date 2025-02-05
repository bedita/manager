<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase;

use App\Utility\ApiTools;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\ApiTools Test Case
 *
 * @coversDefaultClass App\Utility\ApiTools
 */
class ApiToolsTest extends TestCase
{
    /**
     * Test `cleanResponse` method.
     *
     * @return void
     * @covers ::cleanResponse()
     */
    public function testCleanResponse(): void
    {
        $response = [
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'uname' => 'gustavo',
                        'title' => 'gustavo supporto',
                        'name' => 'gustavo',
                        'surname' => 'supporto',
                    ],
                    'links' => [
                        'self' => 'https://api.example.org/users/1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'self' => 'https://api.example.org/users/1/relationships/roles',
                                'related' => 'https://api.example.org/users/1/roles',
                            ],
                        ],
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                    'count' => 1,
                ],
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                        ],
                        'type' => [
                            'type' => 'string',
                        ],
                        'attributes' => [
                            'type' => 'object',
                        ],
                        'links' => [
                            'type' => 'object',
                        ],
                        'relationships' => [
                            'type' => 'object',
                        ],
                    ],
                    'required' => ['id', 'type', 'attributes'],
                ],
            ],
        ];
        $actual = ApiTools::cleanResponse($response);
        $expected = [
            'data' => [
                [
                    'id' => 1,
                    'attributes' => [
                        'uname' => 'gustavo',
                        'title' => 'gustavo supporto',
                        'name' => 'gustavo',
                        'surname' => 'supporto',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                    'count' => 1,
                ],
            ],
        ];
        static::assertEquals($expected, $actual);
    }
}
