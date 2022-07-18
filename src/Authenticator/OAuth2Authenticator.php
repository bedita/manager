<?php
declare(strict_types=1);

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
namespace App\Authenticator;

use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Log\LogTrait;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2Authenticator extends AbstractAuthenticator
{
    use LogTrait;

    /**
     * External Auth provider
     *
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $provider = null;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'fields' => [
            'auth_provider' => 'auth_provider',
            'provider_username' => 'provider_username',
            'access_token' => 'access_token',
        ],
        'sessionKey' => 'oauth2state',
        'routeUrl' => ['_name' => 'login:oauth2'],
    ];

    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        // extract provider from request
        $provider = basename($request->getUri()->getPath());

        $connect = $this->providerConnect($provider, $request);
        if (!empty($connect['authUrl'])) {
            return new Result($connect, Result::SUCCESS);
        }

        $usernameField = (string)Configure::read(sprintf('AuthProviders.%s.map.provider_username', $provider));
        $data = [
            'auth_provider' => $provider,
            'provider_username' => Hash::get($connect, sprintf('user.%s', $usernameField)),
            'access_token' => Hash::get($connect, 'token.access_token'),
        ];
        $user = $this->_identifier->identify($data);

        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    /**
     * Perform Oauth2 connect action on Auth Provider.
     *
     * @param string $provider Provider name.
     * @return array;
     * @throws \Cake\Http\Exception\BadRequestException
     */
    protected function providerConnect(string $provider, ServerRequestInterface $request): array
    {
        $this->initProvider($provider, $request);

        $query = $request->getQueryParams();
        $sessionKey = $this->getConfig('sessionKey');
        /** @var \Cake\Http\Session $session */
        $session = $request->getAttribute('session');

        if (!isset($query['code'])) {
            // If we don't have an authorization code then get one
            $options = (array)Configure::read(sprintf('AuthProviders.%s.options', $provider));
            $authUrl = $this->provider->getAuthorizationUrl($options);
            $session->write($sessionKey, $this->provider->getState());

            return compact('authUrl');
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($query['state']) || ($query['state'] !== $session->read($sessionKey))) {
            $session->delete($sessionKey);
            throw new BadRequestException('Invalid state');
        }

        // Try to get an access token (using the authorization code grant)
        $token = $this->provider->getAccessToken('authorization_code', ['code' => $query['code']]);
        // We got an access token, let's now get the user's details
        $user = $this->provider->getResourceOwner($token)->toArray();
        $token = $token->jsonSerialize();

        return compact('token', 'user');
    }

    /**
     * Init external auth provider via configuration
     *
     * @param string $provider Provider name.
     * @return void
     */
    protected function initProvider(string $provider, ServerRequestInterface $request): void
    {
        $providerConf = (array)Configure::read(sprintf('AuthProviders.%s', $provider));
        if (empty($providerConf['class']) || empty($providerConf['setup'])) {
            throw new BadRequestException('Invalid auth provider ' . $provider);
        }

        $routeUrl = (array)$this->getConfig('routeUrl') + compact('provider');
        $query = $request->getQueryParams();
        $queryRedirectUrl = Hash::get($query, 'redirect');
        if (!empty($queryRedirectUrl)) {
            $routeUrl['?'] = ['redirect' => $queryRedirectUrl];
        }
        $redirectUri = Router::url($routeUrl, true);
        $this->log(sprintf('Creating %s provider with redirect url %s', $provider, $redirectUri), 'info');
        $setup = (array)Hash::get($providerConf, 'setup') + compact('redirectUri');

        $class = Hash::get($providerConf, 'class');
        $this->provider = new $class($setup);
    }
}
