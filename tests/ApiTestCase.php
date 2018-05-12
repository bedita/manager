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

namespace App\Test;

use App\ApiClientProvider;
use Cake\Network\Exception\UnauthorizedException;
use Cake\TestSuite\TestCase;

/**
 * Test case base class involving API calls
 */
class ApiTestCase extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $apiClient;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * Admin API authentication
     *
     * @return void
     * @throws UnauthorizedException
     */
    protected function adminAuth() : void
    {
        $admin = getenv('BEDITA_ADMIN_USR');
        $passwd = getenv('BEDITA_ADMIN_PWD');
        $response = $this->apiClient->authenticate($admin, $passwd);
        if ($this->apiClient->getStatusCode() != 200 || !empty($response['error'])) {
            throw new UnauthorizedException('Admin authentication failed!');
        }

        $this->apiClient->setupTokens($response['meta']);
    }
}
