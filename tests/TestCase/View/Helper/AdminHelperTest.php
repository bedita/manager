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

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\AdminHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\AdminHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\AdminHelper
 */
class AdminHelperTest extends TestCase
{
    /**
     * Data provider for `testControl` test case.
     *
     * @return array
     */
    public function controlProvider(): array
    {
        return [
            'text value null' => [
                'text',
                'title',
                null,
                '<div class="input text"><input type="text" name="title" size="40" id="title"/></div>',
            ],
            'text value not null' => [
                'text',
                'title',
                'something',
                '<div class="input text"><input type="text" name="title" size="40" id="title" value="something"/></div>',
            ],
            'bool value null' => [
                'bool',
                'flag',
                null,
                '<div class="input radio"><input type="hidden" name="flag" value=""/><label for="flag-1"><input type="radio" name="flag" value="1" id="flag-1">Yes</label><label for="flag-0"><input type="radio" name="flag" value="0" id="flag-0">No</label></div>',
            ],
            'bool value not null' => [
                'bool',
                'flag',
                'something',
                '<div class="input radio"><input type="hidden" name="flag" value=""/><label for="flag-1"><input type="radio" name="flag" value="1" id="flag-1">Yes</label><label for="flag-0"><input type="radio" name="flag" value="0" id="flag-0">No</label></div>',
            ],
            'json value null' => [
                'json',
                'extra',
                null,
                '<div class="input textarea"><textarea name="extra" v-jsoneditor="true" class="json" id="extra" rows="5"></textarea></div>',
            ],
            'json value not null' => [
                'json',
                'extra',
                '{"something":"else"}',
                '<div class="input textarea"><textarea name="extra" v-jsoneditor="true" class="json" id="extra" rows="5">{&quot;something&quot;:&quot;else&quot;}</textarea></div>',
            ],
            'applications value null' => [
                'applications',
                'applications',
                null,
                '<div class="input select"><select name="applications" id="applications"><option value="">No application</option><option value="1">Dummy app</option><option value="2">Another dummy app</option></select></div>',
            ],
            'applications value not null' => [
                'applications',
                'applications',
                '1',
                '<div class="input select"><select name="applications" id="applications"><option value="">No application</option><option value="1" selected="selected">Dummy app</option><option value="2">Another dummy app</option></select></div>',
            ],
            'applications value not null' => [
                'default',
                'dummy',
                'something',
                '<div class="input text"><input type="text" name="dummy" size="40" id="dummy" value="something"/></div>',
            ],
        ];
    }

    /**
     * Test `control`
     *
     * @param string $type The type
     * @param string $property The property
     * @param mixed $value The value
     * @param string $expected The expected result
     * @return void
     *
     * @dataProvider controlProvider()
     * @covers ::control()
     * @covers ::initialize()
     */
    public function testControl(string $type, string $property, $value, string $expected = ''): void
    {
        $view = new View(null, null, null, []);
        $view->set('applications', ['' => __('No application'), 1 => 'Dummy app', 2 => 'Another dummy app']);
        $helper = new AdminHelper($view);
        $actual = $helper->control($type, $property, $value);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testControlReadonly` test case.
     *
     * @return array
     */
    public function controlProviderReadonly(): array
    {
        return [
            'readonly' => [
                true,
                [],
                'title',
                'something',
                'something',
            ],
            'unchangeable' => [
                false,
                ['meta' => ['unchangeable' => true]],
                'title',
                'something',
                'something',
            ],
        ];
    }

    /**
     * Test `control` on readonly / unchangeable cases.
     *
     * @param bool $readonly The readonly
     * @param array $resource The resource
     * @param string $property The property
     * @param mixed $value The value
     * @param string $expected The expected result
     * @return void
     *
     * @dataProvider controlProviderReadonly()
     * @covers ::control()
     */
    public function testControlReadonly(bool $readonly, array $resource, string $property, $value, string $expected = ''): void
    {
        $view = new View(null, null, null, []);
        if ($readonly === true) {
            $view->set('readonly', true);
        }
        if (!empty($resource)) {
            $view->set('resource', $resource);
        }
        $helper = new AdminHelper($view);
        $actual = $helper->control('text', $property, $value);
        static::assertEquals($expected, $actual);
    }
}
