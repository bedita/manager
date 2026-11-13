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

namespace App\Utility;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Utility\Hash;

/**
 * Utility class to get media info via oEmebe
 */
class OEmbed
{
    /**
     * Extract metadata using oEmbed (https://oembed.com/)
     * from remote URL
     *
     * @param string $url Media remote URL
     * @return array Media metadata, empty array if no data is found
     */
    public function readMetadata(string $url): array
    {
        $parsed = (array)parse_url($url);
        $host = Hash::get($parsed, 'host', '');
        $provider = $this->findProvider($host);
        $oEmbedUrl = Hash::get($provider, 'url');
        if (empty($oEmbedUrl)) {
            return ['provider_url' => $url];
        }

        $oEmbedUrl = sprintf('%s?url=%s&format=json', $oEmbedUrl, rawurlencode($url));

        // custom UID if not found via oEmbed => query or path
        $providerUID = $this->providerUid($parsed, $host);

        $meta = $this->fetchJson($oEmbedUrl, (array)Hash::get($provider, 'options'));
        if (empty($meta)) {
            return ['provider_url' => $url];
        }

        return [
            'title' => Hash::get($meta, 'title'),
            'description' => Hash::get($meta, 'description'),
            'provider' => Hash::get($meta, 'provider_name'),
            'provider_uid' => Hash::get($meta, 'video_id', $providerUID),
            'provider_url' => $url,
            'provider_thumbnail' => Hash::get($meta, 'thumbnail_url'),
            'provider_extra' => $meta,
        ];
    }

    /**
     * Get provider uid from url and host
     *
     * @param array<string, mixed> $parsedUrl The parsed URL array
     * @param string $host The host
     * @return string
     */
    public function providerUid(array $parsedUrl, string $host): string
    {
        $host = strtolower($host);
        $path = trim((string)Hash::get($parsedUrl, 'path'), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $query = [];
        parse_str((string)Hash::get($parsedUrl, 'query'), $query);

        if ($host === 'youtu.be') {
            return $segments[0] ?? '';
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com'], true)) {
            foreach (['v', 'list'] as $key) {
                if (!empty($query[$key])) {
                    return (string)$query[$key];
                }
            }
            foreach (['embed', 'shorts', 'live', 'v'] as $marker) {
                $index = array_search($marker, $segments, true);
                if ($index !== false && isset($segments[$index + 1])) {
                    return $segments[$index + 1];
                }
            }

            return $path;
        }

        if ($host === 'soundcloud.com') {
            return $path;
        }

        if (str_ends_with($host, '.spotify.com')) {
            return $segments[array_key_last($segments)] ?? '';
        }

        if ($host === 'vimeo.com' || str_ends_with($host, '.vimeo.com')) {
            $videoIndex = array_search('video', $segments, true);
            if ($videoIndex !== false && isset($segments[$videoIndex + 1]) && ctype_digit($segments[$videoIndex + 1])) {
                return $segments[$videoIndex + 1];
            }
            foreach ($segments as $segment) {
                if (ctype_digit($segment)) {
                    return $segment;
                }
            }

            return $path;
        }

        return (string)Hash::get($parsedUrl, 'query', $path);
    }

    /**
     * Fetch oEmbed JSON response from oEmbed provider URL
     *
     * @param string $oEmbedUrl oEmbed URL
     * @param array $options The options to apply to the request
     * @return array JSON decoded response or empty array on failure
     * @codeCoverageIgnore
     */
    protected function fetchJson(string $oEmbedUrl, array $options = []): array
    {
        $o = (new Client())->get($oEmbedUrl, [], ['type' => 'json'] + $options);

        return $o->getJson() ?? [];
    }

    /**
     * Find oEmbed provider URL for a given hostname
     *
     * @param string $host Host name
     * @return array OEmbed provider configuration or null if no match
     */
    protected function findProvider(string $host): array
    {
        Configure::load('oembed');

        $providers = (array)Configure::read('OEmbed.providers');
        $providerConf = array_filter($providers, function ($conf) use ($host) {
            foreach ((array)Hash::get($conf, 'match') as $pattern) {
                if (fnmatch($pattern, $host)) {
                    return true;
                }
            }

            return false;
        });

        if (empty($providerConf)) {
            return [];
        }

        return current($providerConf);
    }
}
