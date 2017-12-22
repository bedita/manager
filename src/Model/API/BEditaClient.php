<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Model\API;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Http\Adapter\Guzzle6\Client;
use WoohooLabs\Yang\JsonApi\Client\JsonApiClient;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

/**
 * BEdita4 API Client class
 */
class BEditaClient
{
    /**
     * BEdita4 API base URL
     *
     * @var string
     */
    private $apiBaseUrl = null;

    /**
     * BEdita4 API KEY
     *
     * @var string
     */
    private $apiKey = null;

    /**
     * JSON API BEdita4 client
     *
     * @var \WoohooLabs\Yang\JsonApi\Client\JsonApiClient
     */
    private $apiClient = null;
    
    /**
     * Setup main client options: API base URL and API KEY
     * 
     * @param string $apiUrl API base URL
     * @param string $apiKey API key
     * @return void
     */
    public function __construct($apiUrl, $apiKey = null) 
    {
        $this->apiBaseUrl = $apiUrl;
        $this->apiKey = $apiKey;

        // setup an asynchronous JSON API client
        $guzzleClient = Client::createWithConfig([]);
        $this->apiClient = new JsonApiClient($guzzleClient);
    }

    /**
     * Send a GET request a list of resources or objects or a single resource or object
     *
     * @param string $endpoint Endpoint URL path to invoke
     * @param array $query Optional query string
     * @return array Response in array format
     */
    public function get($path, $query = []) 
    {
        $response = $this->sendRequest('GET', $path, $query);
        $result = $response->document()->toArray();
        // avoid missing `data` key even if it should be there... to investigate..  
        if (empty($result['error']) && !isset($result['data'])) {
            $result['data'] = [];
        }

        return $result;
    }

    /**
     * Send a generic JSON API request and retrieve response
     *
     * @param string $endpoint Endpoint URL path to invoke
     * @param array $query Optional query string
     * @return \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse JSON API Response object
     */
    protected function sendRequest($method, $path, $query = []) 
    {        
        $uri = $this->apiBaseUrl . $path;
        if ($query) {
            $uri .= '?' . http_build_query($query);
        }
        $request = new Request($method, $uri);
        $request = $request
            ->withHeader('Accept', 'application/vnd.api+json')
            ->withHeader('X-Api-Key', $this->apiKey);

        // Send the request syncronously to retrieve the response
        $psr7Response = $this->apiClient->sendRequest($request);
        $response = new JsonApiResponse($psr7Response);

        return $response;
    }


}