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

namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * An authentication adapter for authenticating using BEdita 4 API /auth endpoint.
 *
 * @see https://docs.bedita.net/en/latest/authorization.html#authentication
 */
class ApiAuthenticate extends BaseAuthenticate
{

    /**
     * Default config for this object.
     *
     * - `fields` The fields to use to perform classic authentication.
     * - 'apiClient' API client instance
     *
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => [
            'username' => 'username',
            'password' => 'password',
        ],
        'apiClient' => null,
    ];

    /**
     * {@inheritDoc}
     *
     * Perform authentication via /auth
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        /** @var \App\Model\API\BEditaClient $apiClient */
        $apiClient = $this->getConfig('apiClient');

        $usernameField = $this->getConfig('fields.username', 'username');
        $passwordField = $this->getConfig('fields.password', 'password');

        $result = $apiClient->authenticate($request->getData($usernameField), $request->getData($passwordField));
        if (empty($result['meta'])) {
            return false;
        }

        $tokens = $result['meta'];
        $result = $apiClient->get('/auth/user', null, ['Authorization' => sprintf('Bearer %s', $tokens['jwt'])]);
        $user = $result['data'] + compact('tokens');

        return $user;
    }
}
