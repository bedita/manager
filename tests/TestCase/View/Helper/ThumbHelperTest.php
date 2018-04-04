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
                false,
            ],
        ];
    }

    /**
     * Test `url()` method.
     *
     * @dataProvider urlProvider()
     * @covers ::url()
     * @covers ::isAcceptable()
     * @covers ::isReady()
     * @covers ::hasUrl()
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
            static::assertNull($result);
        }
    }

    /**
     * Test `isAcceptable()` method.
     *
     * @covers ::url()
     * @covers ::isAcceptable()
     * @return void
     */
    public function testIsAcceptable() : void
    {
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
        $result = $this->Thumb->url(1, null);
        static::assertNull($result);
    }

    /**
     * Test `isReady()` method.
     *
     * @covers ::url()
     * @covers ::isReady()
     * @return void
     */
    public function testIsReady() : void
    {
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
        $result = $this->Thumb->url(1, null);
        static::assertNull($result);
    }

    /**
     * Test `hasUrl()` method.
     *
     * @covers ::url()
     * @covers ::hasUrl()
     * @return void
     */
    public function testHasUrl() : void
    {
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
        $result = $this->Thumb->url(1, null);
        static::assertNull($result);
    }
}
