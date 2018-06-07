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

use App\ApiClientProvider;
use App\Controller\UserProfileController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

class UserProfileControllerSample extends UserProfileController
{
}

/**
 * {@see \App\Controller\UserProfileController} Test Case
 *
 * @coversDefaultClass \App\Controller\UserProfileController
 */
class UserProfileControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var App\Test\TestCase\Controller\UserProfileControllerSample
     */
    public $UserProfileController;

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
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Setup user profile controller for test
     *
     * @param string $filter The filter class full path.
     * @return void
     */
    public function setupController(string $filter = null) : void
    {
        $this->setupApi();
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
        ];
        $request = new ServerRequest($config);
        $this->UserProfileController = new UserProfileControllerSample($request);
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     *
     * @return void
     */
    public function testView() : void
    {
        $this->setupController('App\Test\TestCase\Controller\UserProfileControllerSample');
        $this->UserProfileController->view();
        $vars = ['schema', 'object', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->UserProfileController->viewVars[$var]);
        }
    }
}
