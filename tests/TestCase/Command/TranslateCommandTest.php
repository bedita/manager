<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Command;

use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Command\TranslateCommand} Test Case
 *
 * @coversDefaultClass \App\Command\TranslateCommand
 */
class TranslateCommandTest extends TestCase
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
     * Test `execute` with code error on no file
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::execute()
     */
    public function testExecuteNoFile(): void
    {
        $this->exec('translate -i input.txt -o output.txt -f en -t it -e google');
        $this->assertErrorContains('Input file "input.txt" does not exist');
    }

    /**
     * Test `execute` with code error on no translator
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::execute()
     */
    public function testExecuteNoTranslator(): void
    {
        $input = sprintf('%s/tests/files/input.txt', ROOT);
        $this->exec('translate -i ' . $input . ' -o output.txt -f en -t it -e mytranslator');
        $this->assertErrorContains('No translator engine "mytranslator" is set in configuration');
    }

    /**
     * Test `execute` with code success
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::execute()
     */
    public function testExecute(): void
    {
        $input = sprintf('%s/tests/files/input.txt', ROOT);
        $output = sprintf('%s/tests/files/output.txt', ROOT);
        Configure::write('Translators.dummy', [
            'class' => 'App\Test\TestCase\Core\I18n\DummyTranslator',
            'options' => ['auth_key' => 'secret'],
        ]);
        $this->exec('translate -i ' . $input . ' -o ' . $output . ' -f en -t it -e dummy');
        $this->assertOutputContains('"' . $input . '" [en] -> "' . $output . '" [it] using "dummy" engine.');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }
}
