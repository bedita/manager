<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Identifier;

use ArrayObject;
use Authentication\Identifier\AbstractIdentifier;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;

/**
 * Identifies authentication credentials through an OAuth2 external provider.
 */
class OAuth2Identifier extends AbstractIdentifier
{
    use LogTrait;

    protected $_defaultConfig = [
        'fields' => [
            'auth_provider' => 'auth_provider',
            'provider_username' => 'provider_username',
            'access_token' => 'access_token',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials)
    {
        $apiClient = ApiClientProvider::getApiClient();
        try {
            $result = $apiClient->post('/auth', json_encode($credentials), ['Content-Type' => 'application/json']);
            $tokens = $result['meta'];
            $result = $apiClient->get('/auth/user', null, ['Authorization' => sprintf('Bearer %s', $tokens['jwt'])]);
        } catch (BEditaClientException $ex) {
            $this->log($ex->getMessage(), 'debug');

            return null;
        }

        return new ArrayObject($result['data']
            + compact('tokens')
            + Hash::combine($result, 'included.{n}.attributes.name', 'included.{n}.id', 'included.{n}.type'));
    }
}
