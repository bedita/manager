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

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\ArrayHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\View\Helper\ArrayHelper} Test Case
 */
#[CoversClass(ArrayHelper::class)]
#[CoversMethod(ArrayHelper::class, 'combine')]
#[CoversMethod(ArrayHelper::class, 'extract')]
#[CoversMethod(ArrayHelper::class, 'onlyKeys')]
#[CoversMethod(ArrayHelper::class, 'removeKeys')]
class ArrayHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\ArrayHelper
     */
    public ArrayHelper $Array;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $view = new View();
        $this->Array = new ArrayHelper($view);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Array);

        parent::tearDown();
    }

    /**
     * Data provider for `testCombine` test case.
     *
     * @return array
     */
    public static function getCombineSchemaProvider(): array
    {
        return [
            'combine arrays' => [
                [
                    10 => 10,
                    20 => 20,
                    50 => 50,
                    100 => 100,
                ],
                [10, 20, 50, 100],
            ],
        ];
    }

    /**
     * Test `combine()` method.
     *
     * @param array $expected The expected array.
     * @param array $arr The array.
     * @return void
     */
    #[DataProvider('getCombineSchemaProvider')]
    public function testCombine(array $expected, array $arr): void
    {
        $actual = $this->Array->combine($arr);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testRemoveKeys` test case.
     *
     * @return array
     */
    public static function getRemoveKeysSchemaProvider(): array
    {
        return [
            'basic data' => [
                [
                    'title' => 'string',
                    'description' => 'string',
                    'uname' => 'string',
                    'status' => 'string',
                ], // expected
                [
                    'title' => 'string',
                    'description' => 'string',
                    'uname' => 'string',
                    'status' => 'string',
                    'extra' => 'object',
                ], // data
                [
                    'extra',
                ], // keys to remove
            ],
        ];
    }

    /**
     * Test `removeKeys()` method.
     *
     * @param array $expected The expected array.
     * @param array $arr The array.
     * @param array $keys The keys to remove.
     * @return void
     */
    #[DataProvider('getRemoveKeysSchemaProvider')]
    public function testRemoveKeys(array $expected, array $arr, array $keys): void
    {
        $actual = $this->Array->removeKeys($arr, $keys);

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testOnlyKeys` test case.
     *
     * @return array
     */
    public static function onlyKeysProvider(): array
    {
        return [
            'basic' => [
                [
                    'extra' => 'object',
                    'description' => 'string',
                ], // expected
                [
                    'title' => 'string',
                    'description' => 'string',
                    'uname' => 'string',
                    'status' => 'string',
                    'extra' => 'object',
                ], // data
                [
                    'extra',
                    'description',
                ], // keys to keep
            ],
        ];
    }

    /**
     * Test `onlyKeys()` method.
     *
     * @param array $expected The expected array.
     * @param array $arr The array.
     * @param array $keys The keys to keep.
     * @return void
     */
    #[DataProvider('onlyKeysProvider')]
    public function testOnlyKeys(array $expected, array $arr, array $keys): void
    {
        $actual = $this->Array->onlyKeys($arr, $keys);

        static::assertSame($expected, $actual);
    }

    /**
     * Test `extract()` method.
     *
     * @return void
     */
    public function testExtract(): void
    {
        $data = [
            [ 'id' => 1, 'title' => 'one', ],
            [ 'id' => 2, 'title' => 'two', ],
            [ 'id' => 3, 'title' => 'three', ],
        ];
        $expected = [1, 2, 3];
        $pattern = '{*}.id';
        $actual = $this->Array->extract($data, $pattern);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `intersect()` method.
     *
     * @return void
     * @covers ::intersect()
     */
    public function testIntersect(): void
    {
        $a = ['a', 'b', 'c', 'd'];
        $b = ['c', 'd', 'e', 'f'];
        $expected = ['c', 'd'];
        $actual = $this->Array->intersect($a, $b);
        static::assertSame($expected, $actual);
    }
}
