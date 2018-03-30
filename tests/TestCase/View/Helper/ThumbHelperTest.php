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

use App\View\Helper\ThumbHelper;
use BEdita\SDK\BEditaClient;
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
     * Test apiClient class
     *
     * @var \BEdita\SDK\BEditaClient
     */
    private $apiClient = null;

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

        // create view
        $view = new View();
        $view->set('apiClient', $this->apiClient);

        // create helper
        $this->Thumb = new ThumbHelper($view);
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
     * @return BEditaClient
     */
    private function _initApi() : BEditaClient
    {
        if ($this->apiClient === null) {
            $apiBaseUrl = getenv('BEDITA_API');
            $apiKey = getenv('BEDITA_API_KEY');
            $this->apiClient = new BEditaClient($apiBaseUrl, $apiKey);
            $adminUser = getenv('BEDITA_ADMIN_USR');
            $adminPassword = getenv('BEDITA_ADMIN_PWD');
            $response = $this->apiClient->authenticate($adminUser, $adminPassword);
            $this->apiClient->setupTokens($response['meta']);
        }

        return $this->apiClient;
    }

    /**
     * Create image and media stream for test.
     * Return id
     *
     * @return int The image ID.
     */
    private function _image() : int
    {
        $this->_initApi();

        $filename = 'test.png';
        $filepath = sprintf('%s/tests/files/%s', getcwd(), $filename);
        $response = $this->apiClient->upload($filename, $filepath);

        $streamId = $response['data']['id'];
        $response = $this->apiClient->get(sprintf('/streams/%s', $streamId));

        $type = 'images';
        $title = 'The test image';
        $attributes = compact('title');
        $data = compact('type', 'attributes');
        $body = compact('data');
        $response = $this->apiClient->createMediaFromStream($streamId, $type, $body);

        return $response['data']['id'];
    }

    /**
     * Data provider for `testUrl` test case.
     *
     * @return array
     */
    public function urlProvider() : array
    {
        $id = $this->_image();

        return [
            'basic thumb default preset' => [
                [
                    'id' => $id,
                    'preset' => null, // use default
                ],
                'not-null',
            ],
            'thumb error, return null' => [
                [
                    'id' => 999999999999999999999999999999999999999999999,
                    'preset' => null, // use default
                ],
                'null',
            ],
        ];
    }

    /**
     * Test `url()` method.
     *
     * @dataProvider urlProvider()
     * @covers ::url()
     * @param array $input The input array.
     * @return void
     */
    public function testUrl(array $input, string $expected) : void
    {
        $result = $this->Thumb->url($input['id'], $input['preset']);
        if ($expected === 'null') {
            static::assertNull($result);
        }
        if ($expected === 'not-null') {
            static::assertNotNull($result);
        }
    }
}
