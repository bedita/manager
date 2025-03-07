<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Form;

use App\Form\CustomComponentControl;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\Form\CustomComponentControl} Test Case
 */
#[CoversClass(CustomComponentControl::class)]
#[CoversMethod(CustomComponentControl::class, 'control')]
#[CoversMethod(CustomComponentControl::class, 'jsonValue')]
class CustomComponentControlTest extends TestCase
{
    /**
     * Data provider for `testControl` test case.
     *
     * @return array
     */
    public static function controlProvider(): array
    {
        return [
            'default simple' => [
                [
                    'type' => '',
                    'html' => '<key-value-list label="Field1" name="field1" value="" :readonly=false></key-value-list>',
                    'readonly' => false,
                ],
                'field1',
                [],
                [],
            ],
            'custom tag' => [
                [
                    'type' => 'json',
                    'html' => '<my-component label="Abstract" name="subtitle" value="{&quot;a&quot;:2}" :readonly=false></my-component>',
                    'readonly' => false,
                ],
                'subtitle',
                ['a' => 2],
                [
                    'type' => 'json',
                    'tag' => 'my-component',
                    'label' => 'Abstract',
                ],
            ],
            'json encoded' => [
                [
                    'type' => 'json',
                    'html' => '<key-value-list label="Some Field" name="some_field" value="{&quot;a&quot;:&quot;b&quot;}" :readonly=true></key-value-list>',
                    'readonly' => true,
                ],
                'some_field',
                '{"a":"b"}',
                [
                    'type' => 'json',
                    'readonly' => true,
                ],
            ],
        ];
    }

    /**
     * Test `control` method.
     *
     * @param array $expected Expected result.
     * @param string $name The field name.
     * @param mixed|null $value The field value.
     * @param array $options Control options.
     * @return void
     */
    #[DataProvider('controlProvider')]
    public function testCustomControl(array $expected, string $name, $value, array $options = []): void
    {
        $control = new CustomComponentControl();

        $result = $control->control($name, $value, $options);
        ksort($expected);
        static::assertEquals($expected, $result);
    }
}
