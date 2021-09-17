<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Authentication\Authenticator;

use Authentication\Authenticator\CookieAuthenticator as BaseCookieAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieAuthenticator extends BaseCookieAuthenticator
{
    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cookies = $request->getCookieParams();
        $cookieName = $this->getConfig('cookie.name');
        if (!isset($cookies[$cookieName])) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING, [
                'Login credentials not found',
            ]);
        }

        if (is_array($cookies[$cookieName])) {
            $token = $cookies[$cookieName];
        } else {
            $token = json_decode($cookies[$cookieName], true);
        }

        if ($token === null || count($token) !== 2) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'Cookie token is invalid.',
            ]);
        }

        [$username, $token] = $token;
        $identity = $this->_identifier->identify(compact('token'));

        if (empty($identity)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        if ($username !== $identity[IdentifierInterface::CREDENTIAL_USERNAME]) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'Username does not match',
            ]);
        }

        return new Result($identity, Result::SUCCESS);
    }

    /**
     * @inheritDoc
     */
    protected function _createToken($identity)
    {
        $usernameField = $this->getConfig('fields.username');
        $passwordField = $this->getConfig('fields.password');

        return json_encode([$identity[$usernameField], $identity[$passwordField]]);
    }
}
