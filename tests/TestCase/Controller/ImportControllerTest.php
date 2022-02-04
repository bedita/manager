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
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @uses \App\Controller\ImportController
 */
class ImportControllerSample extends ImportController
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null): void
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

    /**
     * @inheritDoc
     */
    public static function getServiceName(): ?string
    {
        return 'ImportFilterSampleService';
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
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

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
     * Test `loadFilters`
     *
     * @return void
     * @covers ::loadFilters()
     */
    public function testLoadFilters(): void
    {
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('loadFilters');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, []);
        static::assertTrue(is_array($this->Import->viewVars['filters']));
        static::assertTrue(is_array($this->Import->viewVars['services']));
    }

    /**
     * Test `updateServiceList`
     *
     * @return void
     * @covers ::updateServiceList()
     */
    public function testUpdateServiceList(): void
    {
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('updateServiceList');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, ['App\Test\TestCase\Controller\ImportFilterSample']);
        $property = $reflectionClass->getProperty('services');
        $property->setAccessible(true);
        $actual = $property->getValue($this->Import);
        $expected = ['ImportFilterSampleService'];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `loadAsyncJobs`
     *
     * @return void
     * @covers ::loadAsyncJobs()
     */
    public function testLoadAsyncJobs(): void
    {
        // empty jobs
        $this->setupController('App\Test\TestCase\Controller\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('loadAsyncJobs');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, []);
        $actual = $this->Import->viewVars['jobs'];
        $expected = [];
        static::assertEquals($expected, $actual);

        // api call with exception
        $property = $reflectionClass->getProperty('services');
        $property->setAccessible(true);
        $actual = $property->setValue($this->Import, ['dummy']);
        $actual = $this->Import->viewVars['jobs'];
        $expected = [];
        static::assertEquals($expected, $actual);

        // mock api get /admin/async_jobs
        $expected = [['id' => 1], ['id' => 2], ['id' => 3]];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/admin/async_jobs')
            ->willReturn(['data' => $expected]);
        $this->Import->apiClient = $apiClient;
        $method->invokeArgs($this->Import, []);
        $actual = $this->Import->viewVars['jobs'];
        static::assertEquals($expected, $actual);
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
     * Test `uploadErrorMessage`.
     *
     * @covers:: uploadErrorMessage()
     * @return void
     */
    public function testUploadErrorMessage(): void
    {
        $this->setupController();
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('uploadErrorMessage');
        $method->setAccessible(true);
        $errors = [
            UPLOAD_ERR_INI_SIZE => __('File is too big, max allowed size is {0}', ini_get('upload_max_filesize')),
            UPLOAD_ERR_FORM_SIZE => __('File is too big, form MAX_FILE_SIZE exceeded'),
            UPLOAD_ERR_PARTIAL => __('File only partially uploaded'),
            UPLOAD_ERR_NO_FILE => __('Missing import file'),
            UPLOAD_ERR_NO_TMP_DIR => __('Temporary folder missing'),
            UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk'),
            UPLOAD_ERR_EXTENSION => __('An extension stopped the file upload'),
        ];
        foreach ($errors as $code => $expected) {
            $actual = $method->invokeArgs($this->Import, [$code]);
            static::assertEquals($expected, $actual);
        }
        $expected = __('Unknown upload error');
        $actual = $method->invokeArgs($this->Import, [123456789]);
        static::assertEquals($expected, $actual);
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
