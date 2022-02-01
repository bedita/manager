<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\LayoutHelper;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\LayoutHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\LayoutHelper
 */
class LayoutHelperTest extends TestCase
{
    /**
     * Data provider for `testIsDashboard` test case.
     *
     * @return array
     */
    public function isDashboardProvider(): array
    {
        return [
            'dashboard' => [
                'Dashboard',
                true,
            ],
        ];
    }

    /**
     * Test isDashboard
     *
     * @param string $name The view name
     * @param bool $expected The expected result
     *
     * @dataProvider isDashboardProvider()
     * @covers ::isDashboard()
     */
    public function testIsDashboard($name, $expected): void
    {
        $request = $response = $events = null;
        $data = ['name' => $name];
        $layout = new LayoutHelper(new View($request, $response, $events, $data));
        $result = $layout->isDashboard();
        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testIsLogin` test case.
     *
     * @return array
     */
    public function isLoginProvider(): array
    {
        return [
            'login' => [
                'Login',
                true,
            ],
            'other' => [
                'Trash',
                false,
            ],
        ];
    }

    /**
     * Test isLogin
     *
     * @param string $name The view name
     * @param bool $expected The expected result
     *
     * @dataProvider isLoginProvider()
     * @covers ::isLogin()
     */
    public function testIsLogin($name, $expected): void
    {
        $request = $response = $events = null;
        $data = ['name' => $name];
        $layout = new LayoutHelper(new View($request, $response, $events, $data));
        $result = $layout->isLogin();
        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testMessages` test case.
     *
     * @return array
     */
    public function messagesProvider(): array
    {
        return [
            'login' => [
                'Login',
                false,
            ],
            'not login' => [
                'Objects',
                true,
            ],
        ];
    }

    /**
     * Test layoutFooter
     *
     * @param string $name The view name
     * @param bool $expected The expected result
     *
     * @dataProvider messagesProvider()
     * @covers ::messages()
     */
    public function testMessages($name, $expected): void
    {
        $request = $response = $events = null;
        $data = ['name' => $name];
        $layout = new LayoutHelper(new View($request, $response, $events, $data));
        $result = $layout->messages();
        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testModuleLink` test case.
     *
     * @return array
     */
    public function moduleLinkProvider(): array
    {
        return [
            'user profile' => [
                '<a href="/user_profile" class="has-background-black icon-user">UserProfile</a>',
                'UserProfile',
                [
                    'moduleLink' => ['_name' => 'user_profile:view'],
                ],
            ],
            'import' => [
                '<a href="/import" class="has-background-black icon-download-alt">Import</a>',
                'Import',
                [
                    'moduleLink' => ['_name' => 'import:index'],
                ],
            ],
            'objects' => [
                '<a href="/objects" class="has-background-module-objects">Objects</a>',
                'Module',
                [
                    'currentModule' => ['name' => 'objects'],
                ],
            ],
        ];
    }

    /**
     * Test `moduleLink` method
     *
     * @param string $expected The expected link
     * @param string $name The view name
     * @param array $viewVars The view vars
     *
     * @dataProvider moduleLinkProvider()
     * @covers ::moduleLink()
     * @covers ::commandLinkClass()
     */
    public function testModuleLink($expected, $name, array $viewVars = []): void
    {
        $request = $response = $events = null;
        $data = ['name' => $name];
        $view = new View($request, $response, $events, $data);
        foreach ($viewVars as $key => $value) {
            $view->set($key, $value);
        }
        $layout = new LayoutHelper($view);
        $result = $layout->moduleLink();
        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testCustomElement` test case.
     *
     * @return array
     */
    public function customElementProvider(): array
    {
        return [
            'empty' => [
                '',
                'test',
            ],
            'empty relation' => [
                'empty',
                'my_relation',
                'relation',
                [
                    'relations' => [
                        '_element' => [
                            'my_relation' => 'empty',
                        ],
                    ],
                ],
            ],
            'my_element' => [
                'MyPlugin.my_element',
                'my_group',
                'group',
                [
                    'view' => [
                        'my_group' => ['_element' => 'MyPlugin.my_element'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `customElement` method
     *
     * @param string $expected The expected element
     * @param string $item The item
     * @param string $type The item type
     * @param array $conf Configuration to use
     * @return void
     *
     * @dataProvider customElementProvider()
     * @covers ::customElement()
     */
    public function testCustomElement(string $expected, string $item, string $type = 'relation', array $conf = []): void
    {
        Configure::write('Properties.documents', $conf);
        $view = new View();
        $view->set('currentModule', ['name' => 'documents']);
        $layout = new LayoutHelper($view);

        $result = $layout->customElement($item, $type);
        static::assertSame($expected, $result);
    }

    /**
     * Test `typeLabel` method
     *
     * @return void
     * @covers ::typeLabel()
     */
    public function testTypeLabel(): void
    {
        $view = new View();
        $view->set('currentModule', ['name' => 'documents']);
        $layout = new LayoutHelper($view);
        $expected = __('Objects');
        $actual = $layout->typeLabel('Objects');
        static::assertSame($expected, $actual);

        Configure::write('Plugins', ['DummyPlugin' => ['bootstrap' => true, 'routes' => true, 'ignoreMissing' => true]]);
        $expected = __d('DummyPlugin', 'Objects');
        $actual = $layout->typeLabel('Objects');
        static::assertSame($expected, $actual);
    }

    public function publishStatusProvider(): array
    {
        return [
            'empty object' => [
                [],
                '',
            ],
            'expired' => [
                ['attributes' => ['publish_end' => '2022-01-01 00:00:00']],
                'expired',
            ],
            'future' => [
                ['attributes' => ['publish_start' => '2222-01-01 00:00:00']],
                'future',
            ],
            'locked' => [
                ['meta' => ['locked' => true]],
                'locked',
            ],
            'draft' => [
                ['attributes' => ['status' => 'draft']],
                'draft',
            ],
            'none of above' => [
                ['attributes' => ['title' => 'dummy']],
                '',
            ],
        ];
    }

    /**
     * Test `publishStatus` method
     *
     * @return void
     * @dataProvider publishStatusProvider()
     * @covers ::publishStatus()
     */
    public function testPublishStatus(array $object, string $expected): void
    {
        $view = new View();
        $layout = new LayoutHelper($view);
        $actual = $layout->publishStatus($object);
        static::assertSame($expected, $actual);
    }
}
