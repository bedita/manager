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

use App\Controller\TranslationsController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\TranslationsController} Test Case
 *
 * @coversDefaultClass \App\Controller\TranslationsController
 * @uses \App\Controller\TranslationsController
 */
class TranslationsControllerTest extends TestCase
{
    /**
     * Test Translations controller
     *
     * @var App\Controller\TranslationsController
     */
    public $controller;

    /**
     * Test api client
     *
     * @var BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Uname for test object
     *
     * @var string
     */
    protected $uname = 'translations-controller-test-document';

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'get' => [],
        'params' => [
            'object_type' => 'documents',
        ],
    ];

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
     * Setup controller to test with request config
     *
     * @param array|null $requestConfig
     * @return void
     */
    protected function setupController(?array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        $this->controller->objectType = 'documents';
        $this->setupApi();
        $this->createTestObject();
    }

    /**
     * Test `initialize` method
     *
     * @covers ::initialize()
     * @return void
     */
    public function testInitialize(): void
    {
        $request = new ServerRequest($this->defaultRequestConfig);
        $this->controller = new TranslationsController($request);
        $actual = (string)$this->controller->request->getParam('object_type');
        $expected = 'translations';
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `add` method
     *
     * @covers ::add()
     * @return void
     */
    public function testAdd(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();

        // do controller call
        $result = $this->controller->add($id);

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->getStatusCode());
        static::assertEquals('text/html', $this->controller->response->getType());

        // verify expected vars in view
        $this->assertExpectedViewVars(['schema', 'object', 'translation']);

        // on error
        $result = $this->controller->add(123456789);
        $expected = get_class(new \Cake\Http\Response());
        $actual = get_class($result);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `edit` method
     *
     * @covers ::edit()
     * @return void
     */
    public function testEdit(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object ID for test
        $id = $this->getTestId();
        $lang = 'it';

        // do controller call
        $result = $this->controller->edit($id, $lang);

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->controller->response->getStatusCode());
        static::assertEquals('text/html', $this->controller->response->getType());

        // verify expected vars in view
        $this->assertExpectedViewVars(['schema', 'object', 'translation']);

        // on error
        $result = $this->controller->edit(123456789, $lang);
        $expected = get_class(new \Cake\Http\Response());
        $actual = get_class($result);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `save` method
     *
     * @covers ::save()
     * @return void
     */
    public function testSave(): void
    {
        // Setup controller for test
        $this->setupController();

        // delete translation before starting test
        $lang = 'it';
        $objectId = $this->getTestId();
        $id = $this->getTestTranslationId($objectId, 'documents', $lang);
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                [
                    'id' => $id,
                    'object_id' => $objectId,
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        $this->controller->delete();

        // get object for test
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'lang' => $lang,
                'object_id' => $objectId,
                'status' => 'draft',
                'translated_fields' => [
                    'title' => 'Titolo in italiano',
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);

        // do controller call
        $this->controller->save();

        $response = $this->controller->getResponse();

        // verify response status code and type
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('text/html', $response->getType());

        // error, with not empty request id
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [ // missing required 'status'
                'lang' => $lang,
                'id' => $id,
                'object_id' => $objectId,
                'translated_fields' => [
                    'title' => 'Titolo in italiano',
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);

        // do controller call
        $this->controller->save();

        $response = $this->controller->getResponse();

        // verify response status code and type
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('text/html', $response->getType());

        // error, with empty request id
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [ // missing required 'status'
                'lang' => $lang,
                'object_id' => $objectId,
                'translated_fields' => [
                    'title' => 'Titolo in italiano',
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);

        // do controller call
        $this->controller->save();

        $response = $this->controller->getResponse();

        // verify response status code and type
        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals('text/html', $response->getType());
    }

    /**
     * Test `delete` method
     *
     * @covers ::delete()
     * @return void
     */
    public function testDelete(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $objectId = $this->getTestId();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                [
                    'id' => $this->getTestTranslationId($objectId, 'documents', 'it'),
                    'object_id' => $objectId,
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);

        // do controller call
        $result = $this->controller->delete();

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // restore test object
        $this->restoreTestObject($objectId, 'documents');

        // on missing post data
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        try {
            $this->controller->delete();
        } catch (\Exception $e) {
            $expected = get_class(new BadRequestException());
            $actual = get_class($e);
            static::assertEquals($expected, $actual);
        }

        // on missing post data id
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                ['object_id' => 1234567789],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        try {
            $this->controller->delete();
        } catch (\Exception $e) {
            $expected = get_class(new BadRequestException());
            $actual = get_class($e);
            static::assertEquals($expected, $actual);
        }

        // on missing post data object_id
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                ['id' => 1234567789],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        try {
            $this->controller->delete();
        } catch (\Exception $e) {
            $expected = get_class(new BadRequestException());
            $actual = get_class($e);
            static::assertEquals($expected, $actual);
        }

        // on missing wrong payload
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                ['id' => 1234567789, 'object_id' => 9999999999],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $this->controller = new TranslationsController($request);
        $response = $this->controller->delete();
        $expected = get_class(new \Cake\Http\Response());
        $actual = get_class($response);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `typeFromUrl` method.
     *
     * @return void
     * @covers ::typeFromUrl()
     */
    public function testTypeFromUrl(): void
    {
        $request = new ServerRequest($this->defaultRequestConfig);
        $request = $request->withAttribute('here', '/documents/1/translation/lang');
        $this->controller = new TranslationsController($request);
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('typeFromUrl');
        $method->setAccessible(true);
        $expected = 'documents';
        $actual = $method->invokeArgs($this->controller, []);
        static::assertEquals($expected, $actual);

        $request = new ServerRequest($this->defaultRequestConfig);
        $this->controller = new TranslationsController($request);
        $this->controller->objectType = 'dummies';
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('typeFromUrl');
        $method->setAccessible(true);
        $expected = 'dummies';
        $actual = $method->invokeArgs($this->controller, []);
        static::assertEquals($expected, $actual);
    }

    /**
     * Get test object id
     *
     * @return int
     */
    private function getTestId(): int
    {
        // call index and get first available object, for test view
        $o = $this->getTestObject();

        return (int)Hash::get($o, 'id');
    }

    /**
     * Get an object for test purposes
     *
     * @return array|null
     */
    private function getTestObject(): ?array
    {
        $response = $this->client->getObjects('documents', ['filter' => ['uname' => $this->uname]]);

        if (!empty($response['data'][0])) {
            return $response['data'][0];
        }

        return null;
    }

    /**
     * Get the translation for test object
     *
     * @param string|int $id The object ID
     * @param string $objectType The object type
     * @param string $lang The lang code
     * @return int|null The translation ID
     */
    private function getTestTranslationId($id, $objectType, $lang): ?int
    {
        $response = $this->client->getObject($id, $objectType, compact('lang'));

        if (empty($response['included'])) { // if not found, create dummy translation
            $response = $this->client->save('translations', ['object_id' => $id, 'status' => 'draft', 'lang' => 'it', 'translated_fields' => [ 'title' => 'Titolo di test' ]]);

            return (int)Hash::get($response, 'data.id');
        } else {
            foreach ($response['included'] as $included) {
                if ($included['type'] === 'translations' && $included['attributes']['object_id'] == $id && $included['attributes']['lang'] === $lang) {
                    return (int)Hash::get($included, 'id');
                }
            }
        }

        return null;
    }

    /**
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    private function createTestObject(): array
    {
        $o = $this->getTestObject();
        if ($o == null) {
            // create document
            $response = $this->client->save('documents', ['title' => 'translations controller test document', 'uname' => $this->uname]);
            $o = $response['data'];

            // create translation
            $this->client->save('translations', ['object_id' => $o['id'], 'status' => 'draft', 'lang' => 'it', 'translated_fields' => [ 'title' => 'Titolo di test' ]]);
        }

        return $o;
    }

    /**
     * Restore object by id
     *
     * @param string|int $id The object ID
     * @param string $type The object type
     * @return void
     */
    private function restoreTestObject($id, $type): void
    {
        $o = $this->getTestObject();
        if ($o == null) {
            $response = $this->client->restoreObject($id, $type);
        }
    }

    /**
     * Verify existence of vars in controller view
     *
     * @param array $expected The expected vars in view
     * @return void
     */
    private function assertExpectedViewVars($expected): void
    {
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $this->controller->viewVars);
        }
    }
}
