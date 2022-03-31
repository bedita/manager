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
namespace App\Test\TestCase;

use App\Utility\Translate;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\Translate Test Case
 *
 * @coversDefaultClass App\Utility\Translate
 */
class TranslateTest extends TestCase
{
    /**
     * Data provider for testTranslate
     *
     * @return array
     */
    public function translateProvider(): array
    {
        return [
            'empty' => [
                '',
                '',
            ],
            'dummy' => [
                'dummy',
                'Dummy',
            ],
            'label' => [
                'PR expert',
                'PR Expert',
            ],
        ];
    }

    /**
     * Test `get` method
     *
     * @param string $name The field name
     * @param string|null $expected The expected result
     * @return void
     * @dataProvider translateProvider()
     * @covers ::get()
     */
    public function testTranslate(string $name, ?string $expected): void
    {
        $actual = Translate::get($name);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `get` method, plugin case
     *
     * @return void
     * @covers ::get()
     */
    public function testPlugin(): void
    {
        Configure::write('Plugins', ['DummyPlugin' => ['bootstrap' => true, 'routes' => true, 'ignoreMissing' => true]]);
        $actual = Translate::get('dummy');
        static::assertSame('Dummy', $actual);
    }
}
