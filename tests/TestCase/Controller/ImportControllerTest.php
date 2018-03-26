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
use Cake\Http\ServerRequest;
use Cake\Network\Exception\BadRequestException;
use Cake\TestSuite\TestCase;

class ImportControllerSample extends ImportController
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        // do nothing
    }

    /**
     * {@inheritDoc}
     */
    public function redirect($url, $status = 302)
    {
        return null;
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
    public function import($filename, $filepath, ?array $options = []) : ImportResult
    {
        return new ImportResult($filename, 10, 0, 'ok', '');
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
    public function import($filename, $filepath, ?array $options = []) : ImportResult
    {
        throw new \Exception('An expected exception');
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
     * Setup test
     *
     * @param string $filter The filter class full path.
     * @return void
     */
    public function setup(string $filter = null) : void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'post' => [
                'file' => [
                    'name' => $this->filename,
                    'tmp_name' => sprintf('%s/tests/files/%s', getcwd(), $this->filename),
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
    public function testFile() : void
    {
        $this->setup('App\Test\TestCase\Controller\ImportFilterSample');

        $response = $this->Import->file();
        static::assertNull($response);
        $result = $this->Import->viewVars['result'];
        $expected = new ImportResult($this->filename, 10, 0, 'ok', '');
        static::assertEquals($result, $expected);
    }

    /**
     * Test `file` fail method
     *
     * @covers ::file()
     *
     * @return void
     */
    public function testFileBadRequest() : void
    {
        $expected = new BadRequestException('Import filter not selected', 500);
        $this->expectException(get_class($expected));
        $this->expectExceptionCode($expected->getCode());
        $this->expectExceptionMessage($expected->getMessage());

        $this->setup();
        $response = $this->Import->file();
        static::assertNull($response);
    }

    /**
     * Test `file` fail method
     *
     * @covers ::file()
     *
     * @return void
     */
    public function testFileError() : void
    {
        $expected = new \Exception('An expected exception');
        $this->expectException(get_class($expected));
        $this->expectExceptionCode($expected->getCode());
        $this->expectExceptionMessage($expected->getMessage());

        $this->setup('App\Test\TestCase\Controller\ImportFilterSampleError');
        $response = $this->Import->file();
    }
}
