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

namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * An authentication adapter for authenticating using BEdita 4 API /auth endpoint.
 *
 * @see https://docs.bedita.net/en/latest/authorization.html#authentication
 *
 */
class APIAuthenticate extends BaseAuthenticate
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
        // auth tokens used in API calls
        'tokens' => null,
        // logged user data
        'user' => null,
    ];

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => 'afterIdentify',
        ];
    }

    /**
     * Exception.
     *
     * @var \Exception
     */
    protected $error;

    /**
     * {@inheritDoc}
     *
     * Perform authentication via /auth
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $tokens = $request->session()->read('tokens');
        if (!empty($tokens)) {
            return $tokens;
        }

        /** @var \App\Model\API\BEditaClient $apiClient */
        $apiClient = $this->_config['apiClient'];

        $usernameField = $this->_config['fields']['username'];
        $passwordField = $this->_config['fields']['password'];

        $result = $apiClient->authenticate($request->getData($usernameField), $request->getData($passwordField));
        if (empty($result['meta'])) {
            return false;
        }
        $this->setConfig('tokens', $result['meta']);

        return $result['meta'];
    }

    /**
     * {@inheritDoc}
     *
     * Read logged user data via /auth/user
     */
    public function afterIdentify(Event $event, array $data)
    {
        /** @var \App\Model\API\BEditaClient $apiClient */
        $apiClient = $this->_config['apiClient'];


        $result = $apiClient->get('/auth/user', null, ['Authorization' => 'Bearer ' . $data['jwt']]);
        if (!empty($result['data'])) {
            $this->setConfig('user', $result);
        }
        return $result;
    }
}
