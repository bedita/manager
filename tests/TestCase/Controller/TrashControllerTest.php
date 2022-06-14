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

namespace App\Test\TestCase\Controller;

use App\Controller\TrashController;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\TrashController} Test Case
 *
 * @coversDefaultClass \App\Controller\TrashController
 * @uses \App\Controller\TrashController
 */
class TrashControllerTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

    /**
     * Test controller
     *
     * @var \App\Controller\TrashController
     */
    public $Trash;

    /**
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
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
    private function createTrashObject(): int
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
     * @param bool $auth Set authorized api client on test controller if true (default true)
     * @param bool $query Set filter query in POST data (default true)
     * @param bool $multiple Invoke multiple delete with `ids` (default false)
     * @return int The object ID
     */
    public function setupControllerAndData($auth = true, $query = true, $multiple = false): int
    {
        // setup and auth
        $this->setupApi();

        // post a new object and move it to trash (soft delete)
        $id = $this->createTrashObject();

        // set request and trash controller
        $serialized = serialize(['filter' => ['type' => 'documents']]);
        if ($query) {
            $query = htmlspecialchars($serialized);
        } else {
            $query = [];
        }
        $idParam = $multiple ? ['ids' => [$id]] : ['id' => $id];

        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => $idParam + [
                'query' => $query,
            ],
        ];
        $request = new ServerRequest($config);
        $this->Trash = new TrashController($request);
        if (!$auth) {
            $this->client->setupTokens(['jwt' => '']);
        }

        return $id;
    }

    /**
     * Test `restore` method
     *
     * @covers ::restore()
     * @return void
     */
    public function testRestore(): void
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
     * @return void
     */
    public function testRestoreUnauthorized(): void
    {
        $id = $this->setupControllerAndData(false);
        $this->Trash->restore();
        $expected = new BEditaClientException('Not Found', 404);
        static::expectException(get_class($expected));
        static::expectExceptionCode($expected->getCode());
        $response = $this->client->getObject($id);
    }

    /**
     * Test `restore` method with multiple items
     *
     * @covers ::restore()
     * @return void
     */
    public function testRestoreMulti(): void
    {
        $id = $this->setupControllerAndData(true, false, true);
        $this->Trash->restore();
        $response = $this->client->getObject($id);
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals($id, $response['data']['id']);
    }

    /**
     * Test `restore` method failure with multiple items
     *
     * @covers ::restore()
     * @return void
     */
    public function testRestoreMultiFailure(): void
    {
        $id = $this->setupControllerAndData(true, false, true);
        $this->client->remove($id);
        $response = $this->Trash->restore();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/trash', $response->getHeaderLine('Location'));

        $message = $this->Trash->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $message);
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     * @return void
     */
    public function testDelete(): void
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
     * @return void
     */
    public function testDeleteUnauthorized(): void
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
     * Test `restore` method with multiple items
     *
     * @covers ::delete()
     * @return void
     */
    public function testDeleteMulti(): void
    {
        $id = $this->setupControllerAndData(true, false, true);
        $this->Trash->delete();

        $expected = new BEditaClientException('Not Found', 404);
        static::expectException(get_class($expected));
        static::expectExceptionCode($expected->getCode());
        $this->client->get(sprintf('/trash/%d', $id));
    }

    /**
     * Test `delete` method failure with multiple items
     *
     * @covers ::delete()
     * @return void
     */
    public function testDeleteMultiFailure(): void
    {
        $id = $this->setupControllerAndData(true, false, true);
        $this->client->remove($id);
        $response = $this->Trash->delete();
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('/trash', $response->getHeaderLine('Location'));

        $message = $this->Trash->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $message);
    }

    /**
     * Test `emptyTrash` method
     *
     * @covers ::emptyTrash()
     * @covers ::listQuery()
     * @return void
     */
    public function testEmpty(): void
    {
        $this->setupControllerAndData(true, false);
        $this->Trash->emptyTrash();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertEmpty($response['data']);
    }

    /**
     * Test `emptyTrash` method with query filter
     *
     * @covers ::emptyTrash()
     * @covers ::listQuery()
     * @return void
     */
    public function testEmptyFilter(): void
    {
        $this->setupControllerAndData(true, true);
        $this->Trash->emptyTrash();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEmpty($response['data']);
    }

    /**
     * Test `emptyTrash` method when unauthorized
     *
     * @covers ::emptyTrash()
     * @return void
     */
    public function testEmptyUnauthorized(): void
    {
        $this->setupControllerAndData(false);
        $this->Trash->emptyTrash();
        $response = $this->client->get('/trash');
        static::assertEquals(200, $this->client->getStatusCode());
        static::assertEquals('OK', $this->client->getStatusMessage());
        static::assertNotEmpty($response);
        static::assertArrayHasKey('data', $response);
        static::assertNotEmpty($response['data']);
    }
}
