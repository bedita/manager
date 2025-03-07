<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\View\Helper;

use App\Application;
use App\Test\TestCase\Controller\AppControllerTest;
use App\View\Helper\LinkHelper;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\View\Helper\LinkHelper} Test Case
 */
#[CoversClass(LinkHelper::class)]
#[CoversMethod(LinkHelper::class, 'baseUrl')]
#[CoversMethod(LinkHelper::class, 'cssBundle')]
#[CoversMethod(LinkHelper::class, 'findFiles')]
#[CoversMethod(LinkHelper::class, 'fromAPI')]
#[CoversMethod(LinkHelper::class, 'here')]
#[CoversMethod(LinkHelper::class, 'initialize')]
#[CoversMethod(LinkHelper::class, 'jsBundle')]
#[CoversMethod(LinkHelper::class, 'page')]
#[CoversMethod(LinkHelper::class, 'pageSize')]
#[CoversMethod(LinkHelper::class, 'pluginsBundle')]
#[CoversMethod(LinkHelper::class, 'pluginAsset')]
#[CoversMethod(LinkHelper::class, 'replaceQueryParams')]
#[CoversMethod(LinkHelper::class, 'sortClass')]
#[CoversMethod(LinkHelper::class, 'sortField')]
#[CoversMethod(LinkHelper::class, 'sortUrl')]
#[CoversMethod(LinkHelper::class, 'sortValue')]
class LinkHelperTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->createSampleBundle();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->removeSampleBundle();
    }

    /**
     * Test `baseUrl`
     *
     * @return void
     */
    public function testBaseUrl(): void
    {
        $expected = 'http://localhost';
        $link = new LinkHelper(new View(null, null, null, []));
        $link->setConfig('webBaseUrl', 'http://localhost:80');
        $actual = $link->baseUrl();
        static::assertEquals($expected, $actual);

        $expected = 'http://something';
        $link->setConfig('webBaseUrl', $expected);
        $actual = $link->baseUrl();
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testFromAPI` test case.
     *
     * @return array
     */
    public static function fromAPIProvider(): array
    {
        return [
            'empty url' => [
                '', // apiBaseUrl
                '', // webBaseUrl
                '', // apiUrl
                '', // expected
            ],
            'not empty url' => [
                '/api/objects', // apiBaseUrl
                '/objects', // webBaseUrl
                'https://www.gustavo.com/api/objects', // apiUrl
                'https://www.gustavo.com/objects', // expected
            ],
        ];
    }

    /**
     * Test `fromAPI`
     *
     * @param string $apiBaseUrl The api base url
     * @param string $webBaseUrl The web base url
     * @param string $apiUrl The api url
     * @param string $expected The url expected
     * @return void
     */
    #[DataProvider('fromAPIProvider')]
    public function testFromAPI($apiBaseUrl, $webBaseUrl, $apiUrl, $expected): void
    {
        $link = new LinkHelper(new View(null, null, null, []));
        $link->setConfig('apiBaseUrl', $apiBaseUrl);
        $link->setConfig('webBaseUrl', $webBaseUrl);
        $link->fromAPI($apiUrl);
        $this->expectOutputString($expected);
    }

    /**
     * Data provider for `sortUrl` test case.
     *
     * @return array
     */
    public static function sortUrlProvider(): array
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        return [
            'sort field' => [
                $request->withQueryParams(['page' => 2]), // request
                'title', // field
                false, // reset page
                'http://localhost/?page=2&sort=title', // expected
            ],
            'sort -field' => [
                $request->withQueryParams(['page' => 3]), // request
                '-title', // field
                false, // reset page
                'http://localhost/?page=3&sort=-title', // expected
            ],
            'sort and reset page' => [
                $request->withQueryParams(['page' => 4]), // request
                'title', // field
                true, // reset page
                'http://localhost/?page=1&sort=title', // expected
            ],
            'revert sort' => [
                $request->withQueryParams(['sort' => 'title']), // request
                'title', // field
                false, // reset page
                'http://localhost/?sort=-title', // expected
            ],
            'date ranges' => [
                $request->withQueryParams(['page' => 1]), // request
                'date_ranges', // field
                false, // reset page
                'http://localhost/?page=1&sort=date_ranges_min_start_date', // expected
            ],
        ];
    }

    /**
     * Test `sortUrl`
     *
     * @param \Cake\Http\ServerRequest $request The server request
     * @param string $field The field to sort by
     * @param bool $resetPage The reset page flag
     * @param string $expected The expected output
     * @return void
     */
    #[DataProvider('sortUrlProvider')]
    public function testSortUrl(ServerRequest $request, string $field, bool $resetPage, string $expected): void
    {
        $link = new LinkHelper(new View($request, null, null, []));
        $actual = $link->sortUrl($field, $resetPage);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `sortClass` test case.
     *
     * @return array
     */
    public static function sortClassProvider(): array
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        return [
            'no sort' => [
                $request->withQueryParams([]), // request
                'title', // field
                '', // expected
            ],
            'sort by not managed field' => [
                $request->withQueryParams(['sort' => 'dummy']), // request
                'title', // field
                '', // expected
            ],
            'sort down' => [
                $request->withQueryParams(['sort' => 'title']), // request
                'title', // field
                'sort down', // expected
            ],
            'sort up' => [
                $request->withQueryParams(['sort' => '-title']), // request
                'title', // field
                'sort up', // expected
            ],
        ];
    }

    /**
     * Test `sortClass`
     *
     * @param \Cake\Http\ServerRequest $request The server request
     * @param string $field The field to sort by
     * @param string $expected The expected output
     * @return void
     */
    #[DataProvider('sortClassProvider')]
    public function testSortClass(ServerRequest $request, string $field, string $expected): void
    {
        $link = new LinkHelper(new View($request, null, null, []));
        $actual = $link->sortClass($field);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `page` test case.
     *
     * @return array
     */
    public static function pageProvider(): array
    {
        return [
            'zero' => [0],
            'non zero' => [999],
        ];
    }

    /**
     * Test `page` method
     *
     * @param int $page The page number
     * @return void
     */
    #[DataProvider('pageProvider')]
    public function testPage(int $page): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $link = new LinkHelper(new View($request, null, null, []));
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest('test');
        $expected = $test->invokeMethod($link, 'replaceQueryParams', [compact('page')]);
        $this->expectOutputString($expected);
        // call page method
        $link->page($page);
    }

    /**
     * Data provider for `pageSize` test case.
     *
     * @return array
     */
    public static function pageSizeProvider(): array
    {
        return [
            '1' => [1],
            '10' => [10],
            '100' => [100],
            '500' => [500],
        ];
    }

    /**
     * Test `pageSize` method
     *
     * @param int $pageSize The page size
     * @return void
     */
    #[DataProvider('pageSizeProvider')]
    public function testPageSize(int $pageSize): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $link = new LinkHelper(new View($request, null, null, []));
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest('test');
        $expected = $test->invokeMethod($link, 'replaceQueryParams', [['page_size' => $pageSize]]);
        $this->expectOutputString($expected);
        // call page method
        $link->pageSize($pageSize);
    }

    /**
     * Data provider for `here` test case.
     *
     * @return array
     */
    public static function hereProvider(): array
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        return [
            'empty query' => [
                $request->withQueryParams([]), // request
                [], // options
                '/', // expected
            ],
            'options no-query' => [
                $request->withQueryParams(['some' => 'thing']), // request
                ['no-query' => true], // options
                '/', // expected
            ],
            'options exclude query page, empty q' => [
                $request->withQueryParams(['page' => 2]), // request
                ['exclude' => 'page'], // options
                '/', // expected
            ],
            'options exclude query page, not empty q' => [
                $request->withQueryParams(['page' => 2, 'sort' => '-id']), // request
                ['exclude' => 'page'], // options
                '/?sort=-id', // expected
            ],
        ];
    }

    /**
     * Test `here` method
     *
     * @param \Cake\Http\ServerRequest $request The server request
     * @param array $options The options
     * @param string $expected The expected url string
     * @return void
     */
    #[DataProvider('hereProvider')]
    public function testHere(ServerRequest $request, array $options, string $expected): void
    {
        $link = new LinkHelper(new View($request, null, null, []));
        $actual = $link->here($options);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `replaceQueryParams` test case.
     *
     * @return array
     */
    public static function replaceQueryParamsProvider(): array
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        return [
            'empty params' => [
                $request->withQueryParams([]), // request
                [], // query params
                'http://localhost/', // expected
            ],
            'some params' => [
                $request->withQueryParams(['q' => 'search']), // request
                ['q' => 'search'], // query params
                'http://localhost/?q=search', // expected
            ],
        ];
    }

    /**
     * Test `replaceQueryParams` method
     *
     * @param \Cake\Http\ServerRequest $request The server request
     * @param array $queryParams The query params
     * @param string $expected The expected url string
     * @return void
     */
    #[DataProvider('replaceQueryParamsProvider')]
    public function testReplaceQueryParams(ServerRequest $request, array $queryParams, string $expected): void
    {
        $link = new LinkHelper(new View($request, null, null, []));
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest('test');
        $actual = $test->invokeMethod($link, 'replaceQueryParams', [$queryParams]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `pluginsBundle`
     *
     * @return void
     */
    public function testPluginsBundle(): void
    {
        // load plugins from config for test
        $app = new Application(CONFIG);
        $app->bootstrap();
        $pluginsConfig = [
            'DebugKit' => ['debugOnly' => true],
            'Bake' => ['debugOnly' => false],
        ];
        Configure::write('Plugins', $pluginsConfig);
        $app->loadPluginsFromConfig();
        // do test
        $this->expectOutputString('');
        $link = new LinkHelper(new View(new ServerRequest(), null, null, []));
        $link->pluginsBundle();
    }

    /**
     * Test `pluginAsset`
     *
     * @return void
     */
    public function testPluginAsset(): void
    {
        // load plugins from config for test
        $app = new Application(CONFIG);
        $app->bootstrap();
        $pluginsConfig = [
            'DebugKit' => ['debugOnly' => true],
        ];
        Configure::write('Plugins', $pluginsConfig);
        $app->loadPluginsFromConfig();
        // do test
        $link = new LinkHelper(new View(new ServerRequest(), null, null, []));
        $actual = $link->pluginAsset('DebugKit', 'css');
        static::assertEquals('', $actual);
        $actual = $link->pluginAsset('DebugKit', 'js');
        static::assertEquals('', $actual);

        // create plugin asset for test, remove folders and file afterwards
        if (!is_dir(getcwd() . '/plugins')) {
            mkdir(getcwd() . '/plugins');
        }
        mkdir(getcwd() . '/plugins/Dummy');
        mkdir(getcwd() . '/plugins/Dummy/webroot');
        mkdir(getcwd() . '/plugins/Dummy/webroot/js');
        file_put_contents(getcwd() . '/plugins/Dummy/webroot/js/Dummy.plugin.js', '');
        Configure::write('Plugins', ['Dummy' => ['debugOnly' => true]]);
        $app->loadPluginsFromConfig();
        $actual = $link->pluginAsset('Dummy', 'js');
        static::assertEquals('<script src="/dummy/js/Dummy.plugin.js"></script>', $actual);
        array_map('unlink', glob(getcwd() . '/plugins/Dummy/webroot/js/*.*'));
        rmdir(getcwd() . '/plugins/Dummy/webroot/js');
        rmdir(getcwd() . '/plugins/Dummy/webroot');
        rmdir(getcwd() . '/plugins/Dummy');
    }

    /**
     * Data provider for `testJsBundle` test case.
     *
     * @return array
     */
    public static function jsBundleProvider(): array
    {
        return [
            'non existing js' => [
                ['abcdefg'], // filter
                '', // expected
            ],
        ];
    }

    /**
     * Test `jsBundle`
     *
     * @param array $filter The filter param
     * @param string $expected The expected string
     * @return void
     */
    #[DataProvider('jsBundleProvider')]
    public function testJsBundle(array $filter, string $expected): void
    {
        $link = new LinkHelper(new View(new ServerRequest(), null, null, []));
        $link->jsBundle($filter);
        $actual = $this->output();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `jsBundle`, `cssBundle`
     *
     * @return void
     */
    public function testBundlesWithMockFindFiles(): void
    {
        $view = new View(new ServerRequest(), null, null, []);
        $mock = new class ($view) extends LinkHelper {
            public function findFiles(array $filter, string $extension): array
            {
                return ['app.bundle.js'];
            }
        };
        $mock->jsBundle(['timezone']);
        $mock = new class ($view) extends LinkHelper {
            public function findFiles(array $filter, string $extension): array
            {
                return ['app.bundle.css'];
            }
        };
        $mock->cssBundle(['timezone']);
        $actual = $this->output();
        static::assertEquals('<script src="/js/app.bundle.js"></script><link rel="stylesheet" href="/css/app.bundle.css">', $actual);
    }

    /**
     * Data provider for `testCssBundle` test case.
     *
     * @return array
     */
    public static function cssBundleProvider(): array
    {
        return [
            'non existing css' => [
                ['abcdefg'], // filter
                '', // expected
            ],
        ];
    }

    /**
     * Test `cssBundle`
     *
     * @param array $filter The filter param
     * @param string $expected The expected string
     * @return void
     */
    #[DataProvider('cssBundleProvider')]
    public function testCssBundle(array $filter, string $expected): void
    {
        $link = new LinkHelper(new View(new ServerRequest(), null, null, []));
        $link->cssBundle($filter);
        $actual = $this->output();
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for testFindFiles
     *
     * @return array
     */
    public static function findFilesProvider(): array
    {
        return [
            'non existing file' => [
                ['abcdefg'], // filter
                'hijlm',
                '', // expected
            ],
            'no filter' => [
                [], // filter
                'js',
                '', // expected
            ],
            'manifest js' => [
                ['manifest'], // filter
                'js',
                'manifest.bundle.61b75d.js', // expected
            ],
            'app css' => [
                ['app'], // filter
                'css',
                'app.css', // expected
            ],
        ];
    }

    /**
     * Test `findFiles`
     *
     * @param array $filter The filter param
     * @param string $extension The extension param
     * @param string $expected The expected string
     * @return void
     */
    #[DataProvider('findFilesProvider')]
    public function testFindFiles(array $filter, string $extension, string $expected): void
    {
        $config = ['manifestPath' => TESTS . 'files' . DS . 'manifest.json'];
        $link = new LinkHelper(new View(new ServerRequest(), null, null, []), $config);
        $actual = $link->findFiles($filter, $extension);
        if (empty($expected)) {
            static::assertEmpty($actual);

            return;
        }
        static::assertNotEmpty($actual);
        if ($expected !== 'multi') {
            static::assertEquals($expected, $actual[0]);
        }
    }

    /**
     * Create sample files
     *
     * @return void
     */
    private function createSampleBundle(): void
    {
        file_put_contents(getcwd() . '/webroot/css/app.css', '');
        file_put_contents(getcwd() . '/webroot/js/app.bundle.abcde.js', '');
        if (!is_dir(getcwd() . '/webroot/js/vendors')) {
            mkdir(getcwd() . '/webroot/js/vendors');
        }
        file_put_contents(getcwd() . '/webroot/js/vendors/sample1.bundle.abcde.js', '');
        file_put_contents(getcwd() . '/webroot/js/vendors/sample2.bundle.abcde.js', '');
        file_put_contents(getcwd() . '/webroot/js/vendors/sample3.bundle.abcde.js', '');
    }

    /**
     * Remove sample files
     *
     * @return void
     */
    private function removeSampleBundle(): void
    {
        unlink(getcwd() . '/webroot/css/app.css');
        unlink(getcwd() . '/webroot/js/app.bundle.abcde.js');
        unlink(getcwd() . '/webroot/js/vendors/sample1.bundle.abcde.js');
        unlink(getcwd() . '/webroot/js/vendors/sample2.bundle.abcde.js');
        unlink(getcwd() . '/webroot/js/vendors/sample3.bundle.abcde.js');
        if (scandir(getcwd() . '/webroot/js/vendors') === false) {
            rmdir(getcwd() . '/webroot/js/vendors');
        }
    }
}
