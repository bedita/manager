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

namespace App\Test\TestCase\View\Helper;

use App\ApiClientProvider;
use App\View\Helper\ThumbHelper;
use BEdita\SDK\BEditaClient;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\ThumbHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\ThumbHelper
 */
class ThumbHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\ThumbHelper
     */
    public $Thumb;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        // set api client in view for helper
        $this->_initApi();

        // create helper
        $this->Thumb = new ThumbHelper(new View());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() : void
    {
        unset($this->Thumb);

        parent::tearDown();
    }

    /**
     * Init api client
     *
     * @return void
     */
    private function _initApi() : void
    {
        $apiClient = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $apiClient->authenticate($adminUser, $adminPassword);
        $apiClient->setupTokens($response['meta']);
    }

    /**
     * Create image and media stream for test.
     * Return id
     *
     * @param string $filename the File name.
     * @return int The image ID.
     */
    private function _image($filename = 'test.png') : int
    {
        $apiClient = ApiClientProvider::getApiClient();

        $filepath = sprintf('%s/tests/files/%s', getcwd(), $filename);
        $response = $apiClient->upload($filename, $filepath);

        $streamId = $response['data']['id'];
        $response = $apiClient->get(sprintf('/streams/%s', $streamId));

        $type = 'images';
        $title = 'The test image';
        $attributes = compact('title');
        $data = compact('type', 'attributes');
        $body = compact('data');
        $response = $apiClient->createMediaFromStream($streamId, $type, $body);

        return $response['data']['id'];
    }

    /**
     * Data provider for `testUrl` test case.
     *
     * @return array
     */
    public function urlProvider() : array
    {
        return [
            'basic thumb default preset' => [
                [
                    'id' => null,
                    'preset' => null, // use default
                ],
                true,
            ],
            'thumb error, return null' => [
                [
                    'id' => 999999999999999999999999999999999999999999999,
                    'preset' => null, // use default
                ],
                ThumbHelper::NOT_AVAILABLE,
            ],
        ];
    }

    /**
     * Test `url()` method.
     *
     * @dataProvider urlProvider()
     * @covers ::url()
     * @covers ::status()
     * @param array $input The input array.
     * @param boolean $expected The expected boolean.
     * @return void
     */
    public function testUrl(array $input, $expected) : void
    {
        $id = empty($input['id']) ? $this->_image() : $input['id'];
        $this->Thumb = new ThumbHelper(new View());
        $result = $this->Thumb->url($id, $input['preset']);

        if ($expected === true) {
            static::assertNotNull($result);
        } else {
            static::assertEquals($expected, $result);
        }
    }

    /**
     * Test `status()` method.
     *
     * @dataProvider urlProvider()
     * @covers ::status()
     * @param array $input The input array.
     * @param boolean $expected The expected boolean.
     * @return void
     */
    public function testStatus(array $input, $expected) : void
    {
        // case response with api call
        $id = empty($input['id']) ? $this->_image() : $input['id'];
        $this->Thumb = new ThumbHelper(new View());
        $status = $this->Thumb->status($id, $input['preset'], $result);

        if ($expected === true) {
            static::assertEquals($status, ThumbHelper::OK);
        } else {
            static::assertNull($result);
            static::assertEquals($status, $expected);
        }
        // case response empty, with mock
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $response = null;
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertNull($result);
        static::assertEquals($status, ThumbHelper::NOT_AVAILABLE);
    }

    /**
     * Test `isAcceptable()` method.
     *
     * @covers ::status()
     * @covers ::isAcceptable()
     * @return void
     */
    public function testIsAcceptable() : void
    {
        // case thumb image is acceptable
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => true,
                        'acceptable' => false,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertNull($result);
        static::assertEquals($status, ThumbHelper::NOT_ACCEPTABLE);

        // case thumb image is not acceptable
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $url = 'http://...';
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => true,
                        'acceptable' => true,
                        'url' => $url,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertEquals($result, $url);
        static::assertEquals($status, ThumbHelper::OK);
    }

    /**
     * Test `isReady()` method.
     *
     * @covers ::status()
     * @covers ::isReady()
     * @return void
     */
    public function testIsReady() : void
    {
        // case thumb ready
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => false,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertNull($result);
        static::assertEquals($status, ThumbHelper::NOT_READY);

        // case thumb not ready
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $url = 'http://...';
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => true,
                        'url' => $url,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertEquals($result, $url);
        static::assertEquals($status, ThumbHelper::OK);
    }

    /**
     * Test `hasUrl()` method.
     *
     * @covers ::status()
     * @covers ::hasUrl()
     * @return void
     */
    public function testHasUrl() : void
    {
        // case url not available
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => true,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertNull($result);
        static::assertEquals($status, ThumbHelper::NO_URL);

        // case url available
        $apiMockClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs([Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey')])
            ->setMethods(['thumbs'])
        ->getMock();
        $url = 'http://...';
        $response = [
            'meta' => [
                'thumbnails' => [
                    [
                        'ready' => true,
                        'url' => $url,
                    ]
                ]
            ]
        ];
        $apiMockClient->method('thumbs')->willReturn($response);
        ApiClientProvider::setApiClient($apiMockClient);
        $result = null;
        $status = $this->Thumb->status(1, null, $result);
        static::assertEquals($result, $url);
        static::assertEquals($status, ThumbHelper::OK);
    }
}
