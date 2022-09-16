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

use App\Form\Options;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Form\Options} Test Case
 *
 * @coversDefaultClass \App\Form\Options
 */
class OptionsTest extends TestCase
{
    /**
     * Data provider for `testCustomControl` test case.
     * Custom control types (@see Form::customControls):
     *
     *     'lang', 'status', 'old_password', 'password', 'confirm-password', 'title'
     *
     * @return array
     */
    public function customControlProvider(): array
    {
        return [
            'not custom' => [
                'name',
                null,
                [],
            ],
            'lang' => [
                'lang',
                'en',
                [
                    'type' => 'text',
                    'value' => 'en',
                ],
            ],
            'lang options' => [
                'lang',
                'en',
                [
                    'type' => 'select',
                    'options' => [
                        [
                            'value' => '',
                            'text' => '',
                        ],
                        [
                            'value' => 'en',
                            'text' => 'English',
                        ],
                        [
                            'value' => 'de',
                            'text' => 'Deutsch',
                        ],
                    ],
                    'value' => 'en',
                ],
                [
                    'Project.config.I18n.languages' => [
                        'en' => 'English',
                        'de' => 'Deutsch',
                    ],
                ],
            ],
            'status' => [
                'status',
                'draft',
                [
                    'type' => 'radio',
                    'options' => [
                        ['value' => 'on', 'text' => __('On')],
                        ['value' => 'draft', 'text' => __('Draft')],
                        ['value' => 'off', 'text' => __('Off')],
                    ],
                    'templateVars' => [
                        'containerClass' => 'status',
                    ],
                    'value' => 'draft',
                ],
            ],
            'old_password' => [
                'old_password',
                '12345',
                [
                    'autocomplete' => 'current-password',
                    'class' => 'password',
                    'default' => '',
                    'label' => __('Current password'),
                    'placeholder' => __('current password'),
                    'type' => 'password',
                    'value' => '12345',
                ],
            ],
            'password' => [
                'password',
                '12345',
                [
                    'class' => 'password',
                    'placeholder' => __('new password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                    'value' => '12345',
                ],
            ],
            'confirm-password' => [
                'confirm-password',
                '12345',
                [
                    'label' => __('Retype password'),
                    'id' => 'confirm_password',
                    'name' => 'confirm-password',
                    'class' => 'confirm-password',
                    'placeholder' => __('confirm password'),
                    'autocomplete' => 'new-password',
                    'default' => '',
                    'type' => 'password',
                    'value' => '12345',
                ],
            ],
            'title' => [
                'title',
                'dummy',
                [
                    'class' => 'title',
                    'type' => 'text',
                    'templates' => [
                        'inputContainer' => '<div class="input title {{type}}{{required}}">{{content}}</div>',
                    ],
                    'value' => 'dummy',
                ],
            ],
            'start_date' => [
                'start_date',
                '2020-10-14',
                [
                    'type' => 'text',
                    'v-datepicker' => 'true',
                    'date' => 'true',
                    'time' => 'true',
                    'value' => '2020-10-14',
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                ],
            ],
            'end_date' => [
                'end_date',
                '2020-10-15',
                [
                    'type' => 'text',
                    'v-datepicker' => 'true',
                    'date' => 'true',
                    'time' => 'true',
                    'value' => '2020-10-15',
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                ],
            ],
            'date_ranges' => [
                'date_ranges',
                null,
                [
                    'type' => 'text',
                    'v-datepicker' => 'true',
                    'date' => 'true',
                    'time' => 'true',
                    'value' => null,
                    'templates' => [
                        'inputContainer' => '<div class="input datepicker {{type}}{{required}}">{{content}}</div>',
                    ],
                ],
            ],
            'coords' => [
                'coords',
                'POINT(11.123125 44.6123245)',
                [
                    'class' => 'coordinates',
                    'templates' => [
                        'inputContainer' => '<div class="input coordinates {{type}}{{required}}"><label>' . __('Long Lat Coordinates') . '</label><coordinates-view coordinates="POINT(11.123125 44.6123245)" /></div>',
                    ],
                    'type' => 'readonly',
                ],
            ],
        ];
    }

    /**
     * Test `customControl` method.
     *
     * @param string $name The field name.
     * @param mixed|null $value The field value.
     * @param array $expected Expected result.
     * @param array $config Configuration.
     * @return void
     * @dataProvider customControlProvider()
     * @covers ::customControl()
     * @covers ::lang
     * @covers ::dateRanges(()
     * @covers ::startDate()
     * @covers ::endDate()
     * @covers ::status
     * @covers ::oldPassword
     * @covers ::password
     * @covers ::confirmPassword
     * @covers ::title
     * @covers ::coords
     */
    public function testCustomControl(string $name, $value, array $expected, array $config = []): void
    {
        if (!empty($config)) {
            Configure::write($config);
        }
        $actual = Options::customControl($name, $value);
        ksort($expected);
        static::assertSame($expected, $actual);
    }
}
