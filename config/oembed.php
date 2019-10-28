<?php
return [
    /**
     * Simplified oEmbed configuration.
     * This method is not meant to give a precise provider match but it simply gives
     * the most probable oembed provider for an URL.
     *
     *  - providers: list of hostnames with associated providers oEmbed endpoints
     */
    'OEmbed' => [
        'providers' => [

            'soundcloud.com' => 'https://soundcloud.com/oembed',

            '*.spotify.com' => 'https://embed.spotify.com/oembed/',

            'vimeo.com' => 'https://vimeo.com/api/oembed.json',
            '*.vimeo.com' => 'https://vimeo.com/api/oembed.json',

            'youtube.com' => 'https://www.youtube.com/oembed',
            'www.youtube.com' => 'https://www.youtube.com/oembed',
            'youtu.be' => 'https://www.youtube.com/oembed',
        ],
    ],
];
