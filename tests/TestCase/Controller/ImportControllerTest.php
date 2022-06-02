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
use App\Core\Result\ImportResult;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Laminas\Diactoros\UploadedFile;

/**
 * {@see \App\Controller\ImportController} Test Case
 *
 * @coversDefaultClass \App\Controller\ImportController
 */
class ImportControllerTest extends TestCase
{
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
     * @var int
     */
    protected $fileError = 0;

    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->loadRoutes();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * Setup import controller for test
     *
     * @param string|null $filter The filter class full path.
     * @return void
     */
    public function setupController(?string $filter = null): void
    {
        $filename = sprintf('%s/tests/files/%s', getcwd(), $this->filename);
        $file = new UploadedFile($filename, filesize($filename), $this->fileError, $this->filename);
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'post' => [
                'file' => $file,
                'filter' => $filter,
            ],
        ];
        $request = new ServerRequest($config);
        $this->Import = new class ($request) extends ImportController
        {
            public function render($view = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
    }

    /**
     * Test `file` method
     *
     * @covers ::file()
     * @covers ::loadFilters()
     * @return void
     */
    public function testFile(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSample');

        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $result = $this->Import->getRequest()->getSession()->read('Import.result');
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
        $filters = [
            [
                'class' => 'App\Test\Utils\ImportFilterSample',
                'label' => 'Dummy Filter',
                'options' => [],
            ],
        ];
        Configure::write('Filters.import', $filters);
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('loadFilters');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, []);
        static::assertTrue(is_array($this->Import->viewBuilder()->getVar('filters')));
        $expected = [
            [
                'value' => 'App\Test\Utils\ImportFilterSample',
                'text' => 'Dummy Filter',
                'options' => [],
            ],
        ];
        static::assertEquals($expected, $this->Import->viewBuilder()->getVar('filters'));
        static::assertTrue(is_array($this->Import->viewBuilder()->getVar('services')));
        static::assertSame(['ImportFilterSampleService'], $this->Import->viewBuilder()->getVar('services'));
        Configure::write('Filters.import', []);
    }

    /**
     * Test `updateServiceList`
     *
     * @return void
     * @covers ::updateServiceList()
     */
    public function testUpdateServiceList(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('updateServiceList');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, ['App\Test\Utils\ImportFilterSample']);
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
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $reflectionClass = new \ReflectionClass($this->Import);
        $method = $reflectionClass->getMethod('loadAsyncJobs');
        $method->setAccessible(true);
        $method->invokeArgs($this->Import, []);
        $actual = $this->Import->viewBuilder()->getVar('jobs');
        $expected = [];
        static::assertEquals($expected, $actual);

        // api call with exception
        $property = $reflectionClass->getProperty('services');
        $property->setAccessible(true);
        $property->setValue($this->Import, ['dummy']);
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/admin/async_jobs')
            ->willThrowException(new BEditaClientException('My test exception'));
        $this->Import->apiClient = $apiClient;
        $method->invokeArgs($this->Import, []);
        $actual = $this->Import->viewBuilder()->getVar('jobs');
        $expected = [];
        static::assertEquals($expected, $actual);
        $flash = $this->Import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('My test exception', Hash::get($flash, '0.message'));

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
        $actual = $this->Import->viewBuilder()->getVar('jobs');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `file` fail method, missing filter
     *
     * @covers ::file()
     * @return void
     */
    public function testFileBadRequestFilter(): void
    {
        $this->setupController();
        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('Import filter not selected', Hash::get($flash, '0.message'));
        static::assertEquals(400, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `file` fail method, missing files
     *
     * @covers ::file()
     * @covers:: uploadErrorMessage()
     * @return void
     */
    public function testFileBadRequestFile(): void
    {
        $this->fileError = 4;
        $this->setupController('App\Test\Utils\ImportFilterSample');

        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->getRequest()->getSession()->read('Flash.flash');
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
     * @return void
     */
    public function testFileError(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSampleError');
        $response = $this->Import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $this->Import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('An expected exception', Hash::get($flash, '0.message'));
        static::assertEquals(500, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `index`
     *
     * @return void
     * @covers ::index()
     * @covers ::beforeRender()
     */
    public function testIndex(): void
    {
        $this->setupController();
        $this->Import->index();
        static::assertEmpty($this->Import->viewBuilder()->getVar('jobs'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('services'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('filters'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('result'));
        $this->Import->dispatchEvent('Controller.beforeRender');
        static::assertEquals(['_name' => 'import:index'], $this->Import->viewBuilder()->getVar('moduleLink'));
    }

    /**
     * Test `jobs`
     *
     * @return void
     * @covers ::jobs()
     */
    public function testJobs(): void
    {
        $this->setupController();
        $this->Import->jobs();
        static::assertNotEmpty($this->Import->viewBuilder()->getOption('serialize'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('jobs'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('services'));
        static::assertEmpty($this->Import->viewBuilder()->getVar('filters'));
    }
}
