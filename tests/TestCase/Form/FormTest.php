<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Form;

use App\Form\Form;
use App\Form\Options;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Form\Form} Test Case
 *
 * @coversDefaultClass \App\Form\Form
 */
class FormTest extends TestCase
{
    /**
     * Data provider for `testGetMethod` test case.
     *
     * @return array
     */
    public function getMethodProvider(): array
    {
        return [
            'name with chars to remove' => [
                Options::class,
                'old_password',
                [Options::class, 'oldPassword'],
            ],
            'name with chars to remove' => [
                Options::class,
                'confirm_password',
                [Options::class, 'confirmPassword'],
            ],
        ];
    }

    /**
     * Test `getMethod` method
     *
     * @param string $class The class
     * @param string $methodName The method name
     * @param array $expected The expected method array
     * @return void
     * @dataProvider getMethodProvider()
     * @covers ::getMethod
     */
    public function testGetMethod(string $class, string $methodName, array $expected): void
    {
        $actual = Form::getMethod($class, $methodName);

        static::assertSame($expected, $actual);
    }

    /**
     * Test `getMethod` method exception 'not callable'
     *
     * @return void
     *
     * @covers ::getMethod
     */
    public function testGetMethodNotCallable(): void
    {
        $methodName = 'dummy';
        $expected = new \InvalidArgumentException(sprintf('Method "%s" is not callable', $methodName));
        static::expectException(get_class($expected));
        static::expectExceptionMessage($expected->getMessage());
        Form::getMethod(Form::class, $methodName);
    }

    /**
     * Data provider for `testLabel`.
     *
     * @return array
     */
    public function labelProvider(): array
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
        ];
    }
}
