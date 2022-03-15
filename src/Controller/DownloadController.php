<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Client;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Download Controller
 */
class DownloadController extends AppController
{
    use InstanceConfigTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        // HTTP client configuration
        'client' => [],
    ];

    /**
     * Download a single stream
     *
     * @param string $uuid Stream UUID
     * @return \Cake\Http\Response
     */
    public function download(string $uuid): Response
    {
        $response = $this->apiClient->get(sprintf('/streams/%s', $uuid));
        $stream = (array)Hash::get($response, 'data');
        $filename = Hash::get($stream, 'attributes.file_name');
        $contentType = Hash::get($stream, 'attributes.mime_type');

        // output
        $response = $this->getResponse()->withStringBody($this->content($stream));
        $response = $response->withType($contentType);

        return $response->withDownload($filename);
    }

    /**
     * Return file content as string
     *
     * @param array $stream Stream data
     * @return string
     */
    protected function content(array $stream): string
    {
        $url = Hash::get($stream, 'meta.url');
        if (!empty($url)) {
            return (string)file_get_contents($url);
        }

        return $this->streamDownload($stream);
    }

    /**
     * Download file content via `/streams/download`
     *
     * @param array $stream Stream data
     * @return string
     */
    protected function streamDownload(array $stream): string
    {
        $headers = [
            'Authorization' => sprintf('Bearer %s', Hash::get($this->apiClient->getTokens(), 'jwt')),
            'X-Api-Key' => Configure::read('API.apiKey'),
            'Accept' => Hash::get($stream, 'attributes.mime_type'),
        ];
        $url = sprintf('%s/streams/download/%s', $this->apiClient->getApiBaseUrl(), $stream['id']);

        $client = new Client((array)$this->getConfig('client'));
        $response = $client->get($url, [], compact('headers'));

        return $response->getStringBody();
    }
}
