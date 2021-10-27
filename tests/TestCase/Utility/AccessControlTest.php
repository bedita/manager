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

use App\Utility\AccessControl;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\AccessControl Test Case
 *
 * @coversDefaultClass App\Utility\AccessControl
 */
class AccessControlTest extends TestCase
{
    /**
     * Data provider for `filtered` method
     *
     * @return array
     */
    public function filteredProvider(): array
    {
        $cfg = [
            'admin' => [
                'filtered' => [
                    'objects' => [
                        'query' => ['filter' => ['status' => ['on', 'draft']]],
                    ],
                ],
            ],
        ];

        return [
            'empty all' => [
                [],
                'objects',
                [],
                [],
            ],
            'empty roles' => [
                $cfg,
                'objects',
                [],
                [],
            ],
            'config + roles' => [
                $cfg,
                'objects',
                ['admin'],
                [
                    ['query' => ['filter' => ['status' => ['on', 'draft']]]],
                ],
            ],
        ];
    }

    /**
     * Test `filtered` method
     *
     * @return void
     * @covers ::filtered()
     * @dataProvider filteredProvider()
     */
    public function testFiltered(array $accessControl, string $objectType, array $roles, array $expected): void
    {
        Configure::write('AccessControl', $accessControl);
        $actual = AccessControl::filtered($objectType, $roles);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `filteredClassMethod` method
     *
     * @return array
     */
    public function filteredClassMethodProvider(): array
    {
        $cfg = [
            'admin' => [
                'filtered' => [
                    'objects' => [
                        'class' => '\BEdita\WebTools\ApiClientProvider',
                        'method' => 'getObjects',
                    ],
                ],
            ],
        ];

        return [
            'empty all' => [
                [],
                'objects',
                [],
                [],
            ],
            'empty roles' => [
                $cfg,
                'objects',
                [],
                [],
            ],
            'config + roles' => [
                $cfg,
                'objects',
                ['admin'],
                [
                    'class' => '\BEdita\WebTools\ApiClientProvider',
                    'method' => 'getObjects',
                ],
            ],
        ];
    }

    /**
     * Test `filteredClassMethod` method
     *
     * @return void
     * @covers ::filteredClassMethod()
     * @dataProvider filteredClassMethodProvider()
     */
    public function testfilteredClassMethod(array $accessControl, string $objectType, array $roles, array $expected): void
    {
        Configure::write('AccessControl', $accessControl);
        $actual = AccessControl::filteredClassMethod($objectType, $roles);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `filteredQuery` method
     *
     * @return array
     */
    public function filteredQueryProvider(): array
    {
        $cfg = [
            'admin' => [
                'filtered' => [
                    'objects' => [
                        'query' => ['filter' => ['status' => ['on', 'draft']]],
                    ],
                ],
            ],
        ];

        return [
            'empty all' => [
                [],
                'objects',
                [],
                [],
            ],
            'empty roles' => [
                $cfg,
                'objects',
                [],
                [],
            ],
            'config + roles' => [
                $cfg,
                'objects',
                ['admin'],
                ['filter' => ['status' => ['on', 'draft']]],
            ],
        ];
    }

    /**
     * Test `filteredQuery` method
     *
     * @return void
     * @covers ::filteredQuery()
     * @dataProvider filteredQueryProvider()
     */
    public function testfilteredQuery(array $accessControl, string $objectType, array $roles, array $expected): void
    {
        Configure::write('AccessControl', $accessControl);
        $actual = AccessControl::filteredQuery($objectType, $roles);
        static::assertEquals($expected, $actual);
    }
}
