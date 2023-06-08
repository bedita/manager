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
namespace App\Test\TestCase\Utility;

use App\Utility\ApiClientTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Utility\ApiClientTrait} Test Case
 *
 * @coversDefaultClass \App\Utility\ApiClientTrait
 */
class ApiClientTraitTest extends TestCase
{
    use ApiClientTrait;

    /**
     * Test getClient method.
     *
     * @return void
     * @covers ::getClient()
     */
    public function testGetClient(): void
    {
        $this->apiClient = null;
        $actual = $this->getClient();
        static::assertNotNull($actual);
    }
}
