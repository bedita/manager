<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase;

use App\Utility\OEmbed;
use Cake\TestSuite\TestCase;

class MyOEmbed extends OEmbed
{
    /**
     * Mock JSON response
     *
     * @var array
     */
    public $json = [];

    protected function fetchJson(string $oembedUrl): array
    {
        return $this->json;
    }
}

/**
 * App\Utility\OEmbed Test Case
 *
 * @coversDefaultClass App\Utility\OEmbed
 */
class OEmbedTest extends TestCase
{
    /**
     * Data provider for `testReadMetadata`
     *
     * @return array
     */
    public function readMetadataProvider(): array
    {
        return [
            'not found' => [
                [
                    'provider_url' => 'https://video.example.com',
                ],
                'https://video.example.com',
                [],
            ],
            'found incomplete' => [
                [
                    'provider_url' => 'https://www.vimeo.com/something',
                ],
                'https://www.vimeo.com/something',
                [],
            ],
            'found' => [
                [
                    'provider_url' => 'https://www.youtube.com/watch?v=_qSYCXHNvJo',
                    'title' => 'Inscenare la Corazzata Potëmkin all\'imbrunire',
                    'description' => null,
                    'provider' => 'YouTube',
                    'provider_thumbnail' => 'https://i.ytimg.com/vi/_qSYCXHNvJo/hqdefault.jpg',
                    'provider_uid' => 'v=_qSYCXHNvJo',
                    'provider_extra' => [
                        'title' => 'Inscenare la Corazzata Potëmkin all\'imbrunire',
                        'provider_name' => 'YouTube',
                        'thumbnail_url' => 'https://i.ytimg.com/vi/_qSYCXHNvJo/hqdefault.jpg',
                        'provider_url' => 'https://www.youtube.com/',
                    ],
                ],
                'https://www.youtube.com/watch?v=_qSYCXHNvJo',
                [
                    'title' => 'Inscenare la Corazzata Potëmkin all\'imbrunire',
                    'provider_name' => 'YouTube',
                    'thumbnail_url' => 'https://i.ytimg.com/vi/_qSYCXHNvJo/hqdefault.jpg',
                    'provider_url' => 'https://www.youtube.com/',
                ],
            ],
        ];
    }

    /**
     * Test `readMetadata` method
     *
     * @return void
     *
     * @covers ::readMetadata()
     * @covers ::findProvider()
     * @dataProvider readMetadataProvider
     */
    public function testReadMetadata(array $expected, string $url, array $oembedResponse): void
    {
        $oembed = new MyOEmbed();
        $oembed->json = $oembedResponse;
        $result = $oembed->readMetadata($url);
        static::assertEquals($expected, $result);
    }
}
