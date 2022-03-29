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
}
