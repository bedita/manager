<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\ImportController;
use App\Core\Filter\ImportFilter;
use App\Core\Result\ImportResult;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

class ImportControllerSample extends ImportController
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        // do nothing
    }
}

/**
 * Test sample filter
 */
class ImportFilterSample extends ImportFilter
{
    /**
     * @inheritDoc
     */
    public function import($filename, $filepath, ?array $options = []): ImportResult
    {
        return new ImportResult($filename, 10, 0, 0, 'ok', '', ''); // ($created, $updated, $errors, $info, $warn, $error)
    }
}

/**
 * Test sample filter
 */
class ImportFilterSampleError extends ImportFilter
{
    /**
     * @inheritDoc
     */
    public function import($filename, $filepath, ?array $options = []): ImportResult
    {
        throw new InternalErrorException('An expected exception');
    }
}

/**
 * {@see \App\Controller\ImportController} Test Case
 *
 * @coversDefaultClass \App\Controller\ImportController
 */
class ImportControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var App\Test\TestCase\Controller\ImportControllerSample
     */
    public $Import;

    /**
     * Test file name
     *
     * @var string
     */
    protected $filename = 'test.png';

    /**
     * Test file error
     *
     * @var string
     */
    protected $fileError = 0;

    /**
     * Setup import controller for test
     *
     * @param string $filter The filter class full path.
     * @return void
     */
    public function setupController(string $filter = null): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'post' => [
                'file' => [
                    'name' => $this->filename,
                    'tmp_name' => sprintf('%s/tests/files/%s', getcwd(), $this->filename),
                    'error' => $this->fileError,
                ],
                'filter' => $filter,
            ],
        ];
        $request = new ServerRequest($config);
        $this->Import = new ImportControllerSample($request);
    }

    /**
     * Test `file` method
     *
     * @covers ::file()
     * @covers ::loadFilters()
     *
     * @return void
     */
    public function testFile(): void
    {
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSample');

        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $result = $this->Import->request->getSession()->read('Import.result');
        $expected = new ImportResult($this->filename, 10, 0, 0, 'ok', '', ''); // ($created, $updated, $errors, $info, $warn, $error)
        static::assertEquals($result, $expected);
    }

    /**
     * Test `file` fail method, missing filter
     *
     * @covers ::file()
     *
     * @return void
     */
    public function testFileBadRequestFilter(): void
    {
        $this->setupController();
        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->request->getSession()->read('Flash.flash');
        static::assertEquals('Import filter not selected', Hash::get($flash, '0.message'));
        static::assertEquals(400, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `file` fail method, missing files
     *
     * @covers ::file()
     * @covers:: uploadErrorMessage()
     *
     * @return void
     */
    public function testFileBadRequestFile(): void
    {
        $this->fileError = 4;
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSample');

        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->request->getSession()->read('Flash.flash');
        static::assertEquals('Missing import file', Hash::get($flash, '0.message'));
        static::assertEquals(400, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `file` fail method, internal error
     *
     * @covers ::file()
     *
     * @return void
     */
    public function testFileError(): void
    {
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSampleError');
        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->request->getSession()->read('Flash.flash');
        static::assertEquals('An expected exception', Hash::get($flash, '0.message'));
        static::assertEquals(500, Hash::get($flash, '0.params.status'));
    }
}
