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

use App\View\Helper\PermsHelper;
use Authentication\Identity;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\ArrayHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\PermsHelper
 */
class PermsHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\PermsHelper
     */
    public $Perms;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $view = new View(null, null, null, []);
        $view->set('modules', [
            'documents' => [
                'name' => 'documents',
                'hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']],
            ],
            'profiles' => [
                'name' => 'profiles',
                'hints' => ['allow' => ['GET']],
            ],
        ]);
        $view->set('currentModule', [
            'name' => 'profiles',
            'hints' => ['allow' => ['GET']],
        ]);
        $this->Perms = new PermsHelper($view);
        $this->Perms->initialize([]);
    }

    /**
     * Data provider for `testIsAllowed` test case
     *
     * @return array
     */
    public function isAllowedProvider(): array
    {
        return [
            [
                false,
                'canDelete',
                ['type' => 'documents', 'meta' => ['locked' => true]],
            ],
            [
                true,
                'canRead',
            ],
            [
                true,
                'canCreate',
                'documents',
            ],
            [
                false,
                'canSave',
                'events',
            ],
        ];
    }

    /**
     * Test main `isAllowed` method logic
     *
     * @param bool $expected Expected result
     * @param string $method Helper method
     * @param array|string $arg The argument for function
     * @return void
     * @dataProvider isAllowedProvider()
     * @covers ::isAllowed()
     * @covers ::initialize()
     * @covers ::canDelete()
     * @covers ::canRead()
     * @covers ::canCreate()
     * @covers ::canSave()
     */
    public function testIsAllowed(bool $expected, string $method, $arg = null): void
    {
        $result = $this->Perms->{$method}($arg);
        static::assertEquals($expected, $result);
    }

    /**
     * Test `isAllowed` method with no current module perms.
     *
     * @return void
     * @covers ::isAllowed()
     */
    public function testIsAllowedNoCurrent(): void
    {
        $this->Perms->getView()->set('currentModule', null);
        $this->Perms->initialize([]);
        $result = $this->Perms->canSave();
        static::assertTrue($result);
    }

    /**
     * Test `canLock` method.
     *
     * @return void
     * @covers ::canLock()
     */
    public function testCanLock(): void
    {
        $this->Perms->getView()->set('user', new Identity([]));
        $result = $this->Perms->canLock();
        static::assertFalse($result);

        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $result = $this->Perms->canLock();
        static::assertTrue($result);
    }

    /**
     * Test `canCreate` method
     *
     * @return void
     * @covers ::canCreate()
     */
    public function testCanCreate(): void
    {
        $result = $this->Perms->canCreate();
        static::assertFalse($result);
    }

    /**
     * Test `canRead` method
     *
     * @return void
     * @covers ::canRead()
     */
    public function testCanRead(): void
    {
        $result = $this->Perms->canRead();
        static::assertTrue($result);
    }

    /**
     * Test `canSave` method
     *
     * @return void
     * @covers ::canSave()
     */
    public function testCanSave(): void
    {
        $result = $this->Perms->canSave();
        static::assertFalse($result);
    }

    /**
     * Test `canDelete` method
     *
     * @return void
     * @covers ::canDelete()
     */
    public function testCanDelete(): void
    {
        $document = [
            'type' => 'documents',
            'meta' => ['locked' => true],
        ];
        $result = $this->Perms->canDelete($document);
        static::assertFalse($result);
    }

    /**
     * Data provider for testAccess.
     *
     * @return array
     */
    public function accessProvider(): array
    {
        return [
            'write' => [
                [],
                'manager',
                'cats',
                'write',
            ],
            'hidden' => [
                [
                    'manager' => [
                        'hidden' => ['cats', 'dogs', 'horses'],
                    ],
                ],
                'manager',
                'dogs',
                'hidden',
            ],
            'read' => [
                [
                    'manager' => [
                        'hidden' => ['cats', 'dogs'],
                        'readonly' => ['horses'],
                    ],
                ],
                'manager',
                'horses',
                'read',
            ],
        ];
    }

    /**
     * Test `access` method
     *
     * @param array $accessControl The access control
     * @param string $roleName The role name
     * @param string $moduleName The module name
     * @param string $expected The expected value
     * @return void
     * @covers ::access()
     * @dataProvider accessProvider()
     */
    public function testAccess(array $accessControl, string $roleName, string $moduleName, string $expected): void
    {
        $actual = $this->Perms->access($accessControl, $roleName, $moduleName);
        static::assertSame($expected, $actual);
    }
}
