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

namespace App\Test\TestCase\Controller;

use App\Controller\TrashController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

class TrashControllerSample extends TrashController
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
        // do nothing
    }

    /**
     * {@inheritDoc}
     */
    public function redirect($url, $status = 302) : Response
    {
        return new Response();
    }

    /**
     * Set api client for controller
     *
     * @param BEditaClient $client the Api Client
     * @return void
     */
    public function setApiClient(BEditaClient $client) : void
    {
        $this->apiClient = $client;
    }
}

/**
 * {@see \App\Controller\TrashController} Test Case
 *
 * @coversDefaultClass \App\Controller\TrashController
 */
class TrashControllerTest extends TestCase
{
    /**
     * Test controller
     *
     * @var App\Test\TestCase\Controller\TrashControllerSample
     */
    public $Trash;

    /**
     * Test api client
     *
     * @var BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi() : void
    {
        $apiBaseUrl = getenv('BEDITA_API');
        $apiKey = getenv('BEDITA_API_KEY');
        $this->client = new BEditaClient($apiBaseUrl, $apiKey);
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Create an object and "soft" delete it
     *
     * @return int The object ID.
     */
    private function createTrashObject() : int
    {
        $type = 'documents';
        $body = [
            'data' => [
                'type' => $type,
                'attributes' => [
                    'title' => 'this is a test object',
                    'status' => 'on',
                ],
            ],
        ];
        $response = $this->client->post(sprintf('/%s', $type), json_encode($body));
        $id = $response['data']['id'];
        $response = $this->client->delete(sprintf('/%s/%s', $type, $id));

        return $id;
    }

    /**
     * Setup controller for test and create an object for test.
     *
     * @param bool $auth Set authorized api client on test controller if true
     * @return int The object ID
     */
    public function setupControllerAndData($auth = true) : int
    {
        // setup and auth
        $this->setupApi();

        // post a new object and move it to trash (soft delete)
        $id = $this->createTrashObject();

        // set request and trash controller
        $serialized = serialize(['filter' => ['type' => 'documents']]);
        $query = htmlspecialchars($serialized);
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'id' => $id,
                'query' => $query,
            ],
        ];
        $request = new ServerRequest($config);
        $this->Trash = new TrashControllerSample($request);
        if (!$auth) {
            $this->client->setupTokens(['jwt' => '']);
        }
        $this->Trash->setApiClient($this->client);

        return $id;
    }

    /**
     * Test `restore` method
     *
     * @covers ::restore()
     *
     * @return void
     */
    public function testRestore() : void
    {
        $id = $this->setupControllerAndData();
        $this->Trash->restore();

        $response = $this->client->getObject($id);
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertArrayNotHasKey('error', $response);
        static::assertArrayHasKey('id', $response['data']);
        static::assertEquals($id, $response['data']['id']);
    }

    /**
     * Test `restore` method when unauthorized
     *
     * @covers ::restore()
     *
     * @return void
     */
    public function testRestoreUnauthorized() : void
    {
        $id = $this->setupControllerAndData(false);
        $this->Trash->restore();
        $expected = new BEditaClientException('Not Found', 404);
        static::expectException(get_class($expected));
        static::expectExceptionCode($expected->getCode());
        $response = $this->client->getObject($id);
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     *
     * @return void
     */
    public function testDelete() : void
    {
        $id = $this->setupControllerAndData();
        $this->Trash->delete();

        try {
            $this->client->getObject($id);
        } catch (BEditaClientException $e) {
            $expected = new BEditaClientException('Not Found', 404);
            static::assertEquals($expected->getCode(), $e->getCode());
        }

        try {
            $this->client->get(sprintf('/trash/%d', $id));
        } catch (BEditaClientException $e) {
            $expected = new BEditaClientException('Not Found', 404);
            static::assertEquals($expected->getCode(), $e->getCode());
        }
    }

    /**
     * Test `delete` method when unauthorized
     *
     * @covers ::delete()
     *
     * @return void
     */
    public function testDeleteUnauthorized() : void
    {
        $this->setupControllerAndData(false);
        $this->Trash->delete();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertNotEmpty($response['data']);
    }

    /**
     * Test `empty` method
     *
     * @covers ::empty()
     *
     * @return void
     */
    public function testEmpty() : void
    {
        $this->setupControllerAndData();
        $this->Trash->empty();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertEmpty($response['data']);
    }

    /**
     * Test `empty` method when unauthorized
     *
     * @covers ::empty()
     *
     * @return void
     */
    public function testEmptyUnauthorized() : void
    {
        $this->setupControllerAndData(false);
        $this->Trash->empty();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertNotEmpty($response['data']);
    }
}
