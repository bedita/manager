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

use App\View\Helper\MediaHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\MediaHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\MediaHelper
 */
class MediaHelperTest extends TestCase
{
    /**
     * Data provider for `testView` test case.
     *
     * @return array
     */
    public function viewProvider() : array
    {
        return [
            'provider thumb' => [
                '<img src="http://example.org/thumb.png" width="50" height="50" alt=""/>',
                [
                    'attributes' => [
                        'provider_thumbnail' => 'http://example.org/thumb.png',
                    ]
                ],
                null,
                [
                    'providerThumb' => true,
                    'options' => [
                        'w' => 50,
                        'h' => 50,
                    ]
                ],
            ],
            'provider html' => [
                '<div>A video</div>',
                [
                    'attributes' => [
                        'provider_extra' => [
                            'html' => '<div>A video</div>',
                        ]
                    ]
                ],
                null,
                [
                ],
            ],
            'no image' => [
                '',
                [
                    'id' => '123',
                    'type' => 'files',
                ],
                null,
                [
                ],
            ],
            'image thumb' => [
                '<img src="http://example.org/thumb.png" alt=""/>',
                [
                    'id' => '123',
                    'type' => 'images',
                ],
                null,
                [
                ],
                'http://example.org/thumb.png',
            ],
            'image thumb error' => [
                '<img src="/img/iconMissingImage.gif" alt=""/>',
                [
                    'id' => '123',
                    'type' => 'images',
                ],
                null,
                [
                ],
                -30,
            ],
            'image thumb wait' => [
                '<img src="/img/iconMissingImage.gif" alt=""/>',
                [
                    'id' => '123',
                    'type' => 'images',
                ],
                null,
                [
                ],
                -10,
            ],
            'image thumb media url' => [
                '<a href="http://example.org/image.png" target="_blank"><img src="http://example.org/thumb.png" alt=""/></a>',
                [
                    'id' => '123',
                    'type' => 'images',
                ],
                [
                    [
                        'meta' => [
                            'url' => 'http://example.org/image.png',
                        ],
                    ],
                ],
                [
                    'mediaUrl' => true,
                ],
                 'http://example.org/thumb.png',
            ],
        ];
    }

    /**
     * Test `view()` method.
     *
     * @param string $expected Expected result.
     * @param array $media Media object data.
     * @param array $streams Streams data.
     * @param array $options Options.
     * @param string|int $thumbResponse Thumb response.
     * @param boolean $expected The expected boolean.
     *
     * @return void
     *
     * @dataProvider viewProvider()
     * @covers ::view()
     * @covers ::providerThumb()
     * @covers ::image()
     */
    public function testView(string $expected, array $media, ?array $streams = [], ?array $options = [], $thumbResponse = null) : void
    {
        $mediaHelper = new MediaHelper(new View());
        if (!empty($thumbResponse)) {
            $mediaHelper = $this->getMockBuilder(MediaHelper::class)
                ->setConstructorArgs([new View()])
                ->setMethods(['thumb'])
                ->getMock();

            $mediaHelper->method('thumb')->willReturn($thumbResponse);
        }

        $result = $mediaHelper->view($media, $streams, $options);
        static::assertEquals($expected, $result);
    }
}
