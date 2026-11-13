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
namespace App\Test\TestCase\Utility;

use App\Utility\OEmbed;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\Utility\OEmbed} Test Case
 */
#[CoversClass(OEmbed::class)]
#[CoversMethod(OEmbed::class, 'findProvider')]
#[CoversMethod(OEmbed::class, 'readMetadata')]
#[CoversMethod(OEmbed::class, 'providerUid')]
class OEmbedTest extends TestCase
{
    /**
     * Data provider for `testProviderUid`
     *
     * @return array
     */
    public static function providerUidProvider(): array
    {
        return [
            'youtube watch share link' => [
                'WGyEelzJaMs',
                'https://www.youtube.com/watch?v=WGyEelzJaMs&si=58cEvfbrPf_ICutz',
            ],
            'youtube short share link' => [
                'WGyEelzJaMs',
                'https://youtu.be/WGyEelzJaMs?si=58cEvfbrPf_ICutz',
            ],
            'youtube no share link' => [
                'WGyEelzJaMs',
                'https://www.youtube.com/WGyEelzJaMs',
            ],
            'youtube shorts path' => [
                'WGyEelzJaMs',
                'https://www.youtube.com/shorts/WGyEelzJaMs?si=58cEvfbrPf_ICutz',
            ],
            'spotify track share link' => [
                '4uLU6hMCjMI75M1A2tKUQC',
                'https://open.spotify.com/intl-it/track/4uLU6hMCjMI75M1A2tKUQC?si=share-token',
            ],
            'soundcloud track share link' => [
                'artist/track-name',
                'https://soundcloud.com/artist/track-name?utm_source=clipboard&utm_medium=text',
            ],
            'vimeo share link' => [
                '123456789',
                'https://player.vimeo.com/video/123456789?h=private-hash&share=copy',
            ],
            'vimeo showcase link' => [
                '987654321',
                'https://vimeo.com/showcase/123456/video/987654321?share=copy',
            ],
            'vimeo unlisted link with numeric hash' => [
                '123456789',
                'https://vimeo.com/123456789/9876543210?share=copy',
            ],
            'other host' => [
                '123',
                'https://other.com/123',
            ],
        ];
    }

    /**
     * Data provider for `testReadMetadata`
     *
     * @return array
     */
    public static function readMetadataProvider(): array
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
                    'provider_uid' => '_qSYCXHNvJo',
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
            'youtube short link with share parameter' => [
                [
                    'provider_url' => 'https://youtu.be/WGyEelzJaMs?si=58cEvfbrPf_ICutz',
                    'title' => null,
                    'description' => null,
                    'provider' => 'YouTube',
                    'provider_uid' => 'WGyEelzJaMs',
                    'provider_thumbnail' => null,
                    'provider_extra' => [
                        'provider_name' => 'YouTube',
                    ],
                ],
                'https://youtu.be/WGyEelzJaMs?si=58cEvfbrPf_ICutz',
                [
                    'provider_name' => 'YouTube',
                ],
            ],
        ];
    }

    /**
     * Test `providerUid` method
     *
     * @return void
     */
    #[DataProvider('providerUidProvider')]
    public function testProviderUid(string $expected, string $url): void
    {
        $parsedUrl = (array)parse_url($url);
        $oembed = new OEmbed();

        static::assertSame($expected, $oembed->providerUid($parsedUrl, $parsedUrl['host']));
    }

    /**
     * Test `readMetadata` method
     *
     * @return void
     */
    #[DataProvider('readMetadataProvider')]
    public function testReadMetadata(array $expected, string $url, array $oembedResponse): void
    {
        $oembed = new class () extends OEmbed
        {
            public array $json = [];
            protected function fetchJson(string $oEmbedUrl, array $options = []): array
            {
                return $this->json;
            }
        };
        $oembed->json = $oembedResponse;
        $result = $oembed->readMetadata($url);
        static::assertEquals($expected, $result);
    }
}
