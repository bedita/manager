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

use App\Controller\UserProfileController;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\UserProfileController} Test Case
 */
#[CoversClass(UserProfileController::class)]
#[CoversMethod(UserProfileController::class, 'changeData')]
#[CoversMethod(UserProfileController::class, 'changePassword')]
#[CoversMethod(UserProfileController::class, 'initialize')]
#[CoversMethod(UserProfileController::class, 'view')]
#[CoversMethod(UserProfileController::class, 'save')]
class UserProfileControllerTest extends TestCase
{
    /**
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

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
     * test `initialize` function
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };
        static::assertNotEmpty($controller->{'Properties'});
    }

    /**
     * Test `view` method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };
        $controller->view();
        $vars = ['schema', 'object', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `view` method on exception
     *
     * @return void
     */
    public function testViewOnException(): void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };

        // mock api get /auth/user
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/auth/user')
            ->willThrowException(new BEditaClientException('test'));
        $controller->apiClient = $apiClient;
        $controller->view();

        static::assertNotEmpty($controller->viewBuilder()->getVar('schema'));
        static::assertEmpty($controller->viewBuilder()->getVar('object'));
        static::assertNotEmpty($controller->viewBuilder()->getVar('properties'));
    }

    /**
     * Test `save` method on exception
     *
     * @return void
     */
    public function testSave(): void
    {
        $this->setupApi();
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'name' => 'Gustavo',
            ],
        ]);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };

        // mock api patch /auth/user
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('patch')
            ->with('/auth/user')
            ->willThrowException(new BEditaClientException('some error, whatever'));
        $controller->apiClient = $apiClient;
        $controller->save();
        $flash = $controller->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('some error, whatever', (string)Hash::get($flash, '0.message'));

        // save with password data, no other changes
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'password' => 'p4ssw0rd',
                'old_password' => '__p4ssw0rd__',
            ],
        ]);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };
        $apiClient->method('patch')
            ->with('/auth/user')
            ->willThrowException(new BEditaClientException('some error, whatever'));
        $controller->apiClient = $apiClient;
        $controller->save();
        $flash = $controller->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('some error, whatever', (string)Hash::get($flash, '0.message'));

        // save with no data changed
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'name' => 'Gustavo',
                '_actualAttributes' => json_encode(['name' => 'Gustavo']),
            ],
        ]);
        $controller = new class ($request) extends UserProfileController
        {
            public ?BEditaClient $apiClient;
        };
        $apiClient->method('patch')
            ->with('/auth/user')
            ->willThrowException(new BEditaClientException('some error, whatever'));
        $controller->apiClient = $apiClient;
        $controller->save();
        $flash = $controller->getRequest()->getSession()->read('Flash.flash');
        static::assertEquals('User profile saved', (string)Hash::get($flash, '0.message'));
    }
}
