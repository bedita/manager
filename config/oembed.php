<?php

use Cake\Routing\Router;

return [
    /**
     * Simplified oEmbed configuration.
     * This method is not meant to give a precise provider match but it simply gives
     * the most probable oembed provider for an URL.
     *
     *  - providers: list providers with oEmbed endpoints and hostnames to match.
     *    With optional 'options' you can specify options for the oembed request as headers, ...
     */
    'OEmbed' => [
        'providers' => [
            'soundcloud' => [
                'name' => 'SoundCloud',
                'match' => ['soundcloud.com'],
                'url' => 'https://soundcloud.com/oembed',
            ],
            'spotify' => [
                'name' => 'Spotify',
                'match' => ['*.spotify.com'],
                'url' => 'https://embed.spotify.com/oembed',
            ],
            'vimeo' => [
                'name' => 'Vimeo',
                'match' => [
                    'vimeo.com',
                    '*.vimeo.com',
                ],
                'url' => 'https://vimeo.com/api/oembed.json',
                'options' => [
                    'headers' => [
                        'Referer' => env('VIMEO_REFERER_HEADER', Router::url('/', true)),
                    ],
                ],
            ],
            'youtube' => [
                'name' => 'YouTube',
                'match' => [
                    'youtube.com',
                    'www.youtube.com',
                    'youtu.be',
                ],
                'url' => 'https://www.youtube.com/oembed',
            ],
        ],
    ],
];
