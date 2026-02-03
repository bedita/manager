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
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Laminas\Diactoros\UploadedFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

/**
 * {@see \App\Controller\ImportController} Test Case
 */
#[CoversClass(ImportController::class)]
#[CoversMethod(ImportController::class, 'beforeRender')]
#[CoversMethod(ImportController::class, 'file')]
#[CoversMethod(ImportController::class, 'index')]
#[CoversMethod(ImportController::class, 'jobs')]
#[CoversMethod(ImportController::class, 'loadAsyncJobs')]
#[CoversMethod(ImportController::class, 'loadFilters')]
#[CoversMethod(ImportController::class, 'uploadErrorMessage')]
#[CoversMethod(ImportController::class, 'updateServiceList')]
class ImportControllerTest extends TestCase
{
    public ServerRequest $request;

    /**
     * Test file name
     *
     * @var string
     */
    protected string $filename = 'test.png';

    /**
     * Test file error
     *
     * @var int
     */
    protected int $fileError = 0;

    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient|null
     */
    protected ?BEditaClient $apiClient = null;

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
        $this->request = new ServerRequest($config);
    }

    /**
     * Test `file` method
     *
     * @return void
     */
    public function testFile(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $import = new class ($this->request) extends ImportController
        {
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $response = $import->file();
        static::assertEquals(302, $response->getStatusCode());
        $result = $import->getRequest()->getSession()->read('Import.result');
        $expected = new ImportResult($this->filename, 10, 0, 0, 'ok', '', ''); // ($created, $updated, $errors, $info, $warn, $error)
        static::assertEquals($result, $expected);
    }

    /**
     * Test `loadFilters`
     *
     * @return void
     */
    public function testLoadFilters(): void
    {
        $filters = [
            [
                'class' => 'App\Test\Utils\ImportFilterSample',
                'name' => 'my-dummy-filter',
                'label' => 'Dummy Filter',
                'options' => [],
            ],
        ];
        Configure::write('Filters.import', $filters);
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $import = new class ($this->request) extends ImportController
        {
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $reflectionClass = new ReflectionClass($import);
        $method = $reflectionClass->getMethod('loadFilters');
        $method->setAccessible(true);
        $method->invokeArgs($import, []);
        static::assertTrue(is_array($import->viewBuilder()->getVar('filters')));
        $expected = [
            [
                'accept' => ['text/xml', 'text/csv'],
                'name' => 'my-dummy-filter',
                'value' => 'App\Test\Utils\ImportFilterSample',
                'text' => 'Dummy Filter',
                'options' => [],
            ],
        ];
        static::assertEquals($expected, $import->viewBuilder()->getVar('filters'));
        static::assertTrue(is_array($import->viewBuilder()->getVar('services')));
        static::assertSame(['ImportFilterSampleService'], $import->viewBuilder()->getVar('services'));
        Configure::write('Filters.import', []);
    }

    /**
     * Test `updateServiceList`
     *
     * @return void
     */
    public function testUpdateServiceList(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $import = new class ($this->request) extends ImportController
        {
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $reflectionClass = new ReflectionClass($import);
        $method = $reflectionClass->getMethod('updateServiceList');
        $method->setAccessible(true);
        $method->invokeArgs($import, ['App\Test\Utils\ImportFilterSample']);
        $property = $reflectionClass->getProperty('services');
        $property->setAccessible(true);
        $actual = $property->getValue($import);
        $expected = ['ImportFilterSampleService'];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `loadAsyncJobs`
     *
     * @return void
     */
    public function testLoadAsyncJobs(): void
    {
        // empty jobs
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $reflectionClass = new ReflectionClass($import);
        $method = $reflectionClass->getMethod('loadAsyncJobs');
        $method->setAccessible(true);
        $method->invokeArgs($import, []);
        $actual = $import->viewBuilder()->getVar('jobs');
        $expected = [];
        static::assertEquals($expected, $actual);

        // api call with exception
        $property = $reflectionClass->getProperty('services');
        $property->setAccessible(true);
        $property->setValue($import, ['dummy']);
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/async_jobs')
            ->willThrowException(new BEditaClientException('My test exception'));
        $import->apiClient = $apiClient;
        // pass service query param
        $request = $this->Import->getRequest()->withQueryParams(['service' => 'dummy']);
        $this->Import->setRequest($request);
        $method->invokeArgs($import, []);
        $actual = $import->viewBuilder()->getVar('jobs');
        $expected = [];
        static::assertEquals($expected, $actual);
        $flash = $import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('My test exception', Hash::get($flash, '0.message'));

        // mock api get /async_jobs
        $expected = [['id' => 1], ['id' => 2], ['id' => 3]];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/async_jobs')
            ->willReturn(['data' => $expected]);
        $import->apiClient = $apiClient;
        $method->invokeArgs($import, []);
        $actual = $import->viewBuilder()->getVar('jobs');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `file` fail method, missing filter
     *
     * @return void
     */
    public function testFileBadRequestFilter(): void
    {
        $this->setupController();
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $response = $import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('Import filter not selected', Hash::get($flash, '0.message'));
        static::assertEquals(400, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `file` fail method, missing files
     *
     * @return void
     */
    public function testFileBadRequestFile(): void
    {
        $this->fileError = 4;
        $this->setupController('App\Test\Utils\ImportFilterSample');
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };

        $response = $import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('Missing import file', Hash::get($flash, '0.message'));
        static::assertEquals(400, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `uploadErrorMessage`.
     *
     * @return void
     */
    public function testUploadErrorMessage(): void
    {
        $this->setupController();
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $reflectionClass = new ReflectionClass($import);
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
            $actual = $method->invokeArgs($import, [$code]);
            static::assertEquals($expected, $actual);
        }
        $expected = __('Unknown upload error');
        $actual = $method->invokeArgs($import, [123456789]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `file` fail method, internal error
     *
     * @return void
     */
    public function testFileError(): void
    {
        $this->setupController('App\Test\Utils\ImportFilterSampleError');
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $response = $import->file();
        static::assertEquals(302, $response->getStatusCode());
        $flash = $import->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('An expected exception', Hash::get($flash, '0.message'));
        static::assertEquals(500, Hash::get($flash, '0.params.status'));
    }

    /**
     * Test `index`
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->setupController();
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $user = new Identity([
            'id' => 1,
            'username' => 'dummy',
            'roles' => ['readers'],
        ]);
        $import->setRequest($import->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $import->Authentication->setIdentity($user);
        $import->index();
        static::assertEmpty($import->viewBuilder()->getVar('jobs'));
        static::assertEmpty($import->viewBuilder()->getVar('services'));
        static::assertEmpty($import->viewBuilder()->getVar('filters'));
        static::assertEmpty($import->viewBuilder()->getVar('result'));
        static::assertArrayHasKey('jobsAllow', $import->viewBuilder()->getVars());
        $import->dispatchEvent('Controller.beforeRender');
        static::assertEquals(['_name' => 'import:index'], $import->viewBuilder()->getVar('moduleLink'));
    }

    /**
     * Test `jobs`
     *
     * @return void
     */
    public function testJobs(): void
    {
        $this->setupController();
        $import = new class ($this->request) extends ImportController
        {
            public ?BEditaClient $apiClient;
            public function render($template = null, $layout = null): Response
            {
                return $this->getResponse();
            }
        };
        $import->jobs();
        static::assertNotEmpty($import->viewBuilder()->getOption('serialize'));
        static::assertEmpty($import->viewBuilder()->getVar('jobs'));
        static::assertEmpty($import->viewBuilder()->getVar('services'));
        static::assertEmpty($import->viewBuilder()->getVar('filters'));
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }
}
