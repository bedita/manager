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
     * {@inheritDoc}
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
                ['type' => 'documents', 'meta' => ['locked' => true]]
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
     *
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
     * Test `canCreate` method
     *
     * @return void
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
}
