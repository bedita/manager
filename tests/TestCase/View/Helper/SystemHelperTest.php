<?php

declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\SystemHelper;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\SystemHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\SystemHelper
 */
class SystemHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\SystemHelper
     */
    protected $System;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->System = new SystemHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->System);
        parent::tearDown();
    }

    /**
     * Test `getMaxFileSize`
     *
     * @return void
     * @covers ::getMaxFileSize()
     */
    public function testGetMaxFileSize(): void
    {
        $pms = intVal(substr(ini_get('post_max_size'), 0, -1));
        $umf = intVal(substr(ini_get('upload_max_filesize'), 0, -1));
        $expected = min($pms, $umf) * 1024 * 1024;
        $actual = $this->System->getMaxFileSize();
        static::assertSame($expected, $actual);
    }

    /**
     * Test `checkBeditaApiVersion`
     *
     * @return void
     * @covers ::checkBeditaApiVersion()
     */
    public function testCheckBeditaApiVersion(): void
    {
        // no project
        $actual = $this->System->checkBeditaApiVersion();
        static::assertTrue($actual);

        // project version 4.7.1
        $this->System->getView()->set('project', ['version' => '4.7.1']);
        $actual = $this->System->checkBeditaApiVersion();
        static::assertFalse($actual);

        // project version 4.13.0
        $this->System->getView()->set('project', ['version' => '4.13.0']);
        $actual = $this->System->checkBeditaApiVersion();
        static::assertTrue($actual);

        // project version 5.13.0
        $this->System->getView()->set('project', ['version' => '5.13.0']);
        $actual = $this->System->checkBeditaApiVersion();
        static::assertTrue($actual);
    }

    /**
     * Test `uploadConfig`
     *
     * @return void
     * @covers ::uploadConfig()
     */
    public function testUploadConfig(): void
    {
        // empty config, defaultUploadAccepted
        $reflectionClass = new \ReflectionClass($this->System);
        $property = $reflectionClass->getProperty('defaultUploadAccepted');
        $property->setAccessible(true);
        $accepted = $property->getValue($this->System);
        $property = $reflectionClass->getProperty('defaultUploadForbidden');
        $property->setAccessible(true);
        $forbidden = $property->getValue($this->System);
        $expected = compact('accepted', 'forbidden');
        $actual = $this->System->uploadConfig();
        static::assertSame($expected, $actual);
    }

    /**
     * Test `alertBgColor`
     *
     * @return void
     * @covers ::alertBgColor()
     */
    public function testAlertBgColor(): void
    {
        Configure::write('Recovery', true);
        static::assertSame('#FE2F03', $this->System->alertBgColor());
        Configure::delete('Recovery');
        static::assertSame('', $this->System->alertBgColor());
        Configure::write('AlertMessage.color', '#FFF000');
        static::assertSame('#FFF000', $this->System->alertBgColor());
        Configure::write('AlertMessageByArea..color', '#000FFF');
        static::assertSame('#000FFF', $this->System->alertBgColor());
    }

    /**
     * Test `alertMsg`
     *
     * @return void
     * @covers ::alertMsg()
     */
    public function testAlertMsg(): void
    {
        // test with no alert
        static::assertSame('', $this->System->alertMsg());
        // test with recovery mode
        Configure::write('Recovery', true);
        static::assertSame('Recovery Mode - Access restricted to admin users', $this->System->alertMsg());
        Configure::delete('Recovery');
        // test AlertMessage.text
        Configure::write('AlertMessage.text', 'Alert Message');
        static::assertSame('Alert Message', $this->System->alertMsg());
        // test AlertMessageByArea..text
        Configure::write('AlertMessageByArea..text', 'Alert Message by prefix');
        static::assertSame('Alert Message by prefix', $this->System->alertMsg());
        // test with api version
        Configure::delete('AlertMessageByArea..text');
        $user = new class
        {
            public function get($key)
            {
                return $key === 'roles' ? ['admin'] : null;
            }
        };
        $this->System->getView()->set('user', $user);
        $this->System->getView()->set('project', ['version' => '4.0.0']);
        static::assertStringContainsString('API version required', $this->System->alertMsg());
    }
}
