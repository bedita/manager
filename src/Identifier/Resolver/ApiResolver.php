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
namespace App\Identifier\Resolver;

use Authentication\Identifier\IdentifierInterface;
use Authentication\Identifier\Resolver\ResolverInterface;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Log\Log;
use Cake\Utility\Hash;

/**
 * Resolver class that uses an API to obtain the user identity.
 */
class ApiResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function find(array $conditions, $type = self::TYPE_AND)
    {
        $apiClient = ApiClientProvider::getApiClient();
        if (isset($conditions[IdentifierInterface::CREDENTIAL_USERNAME], $conditions[IdentifierInterface::CREDENTIAL_PASSWORD])) {
            // Authenticate with credentials
            try {
                $result = $apiClient->authenticate($conditions[IdentifierInterface::CREDENTIAL_USERNAME], $conditions[IdentifierInterface::CREDENTIAL_PASSWORD]);
            } catch (BEditaClientException $e) {
                Log::info(sprintf('Login failed - %s', $e->getMessage()));

                return null;
            }

            if (empty($result['meta'])) {
                Log::info('Missing meta from authentication response');

                return null;
            }

            $apiClient->setupTokens($result['meta']);
        } elseif (isset($conditions[IdentifierInterface::CREDENTIAL_TOKEN])) {
            // Authenticate with renew token
            $apiClient->setupTokens(['renew' => $conditions[IdentifierInterface::CREDENTIAL_TOKEN]]);
            try {
                $apiClient->refreshTokens();
            } catch (BEditaClientException $e) {
                Log::info(sprintf('Failed renewing token - %s', $e->getMessage()));

                return null;
            }
        }

        try {
            $result = $apiClient->get('/auth/user');
        } catch (BEditaClientException $e) {
            Log::info(sprintf('Failed retrieving user data - %s', $e->getMessage()));

            return null;
        }

        $roles = Hash::extract($result, 'included.{n}.attributes.name');
        $tokens = $apiClient->getTokens();

        return $result['data'] + compact('tokens') + compact('roles');
    }
}
