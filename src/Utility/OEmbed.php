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
    public static function readMetadata($url) : array
    {
        $parsed = parse_url($url);
        $host = Hash::get($parsed, 'host', '');
        $oemBedurl = static::findProvider($host);
        if (empty($oemBedurl)) {
            return ['provider_url' => $url];
        }

        $oemBedurl = sprintf('%s?url=%s&format=json', $oemBedurl, $url);

        // custom UID if not found via oEmbed => query or path
        $providerUID = Hash::get($parsed, 'query', Hash::get($parsed, 'path'));

        $meta = (new Client())->get($oemBedurl)->json;
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
     * Find oEmbed provider URL for a given hostname
     */
    protected static function findProvider(string $host) : ?string
    {
        Configure::load('oembed');
        // exact match
        $oemBedurl = Configure::read(sprintf('OEmbed.providers.%s', $host));
        if (!empty($oemBedurl)) {
            return $oemBedurl;
        }
        // wildcard match
        $providers = Configure::read('OEmbed.providers');
        foreach ($providers as $h => $url) {
            if (fnmatch($h, $host)) {
                return $url;
            }
        }

        return null;
    }
}
