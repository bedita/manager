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
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Console Command tests.
 *
 * {@see \App\Command\Console} Test Case
 *
 * @coversDefaultClass \App\Command\Console
 */
class ConsoleCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test help output
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testConsoleHelp(): void
    {
        $this->exec('console -h');

        $this->assertExitCode(0);
        $this->assertOutputContains('You will need to have psysh installed for this Shell to work.');
    }
}
