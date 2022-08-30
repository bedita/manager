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

use App\Identifier\Resolver\ApiResolver;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\Resolver\ResolverAwareTrait;

/**
 * Identifies authentication credentials through an API.
 */
class ApiIdentifier extends AbstractIdentifier
{
    use ResolverAwareTrait;

    /**
     * Default configuration.
     * - `timezoneField` The field where timezone is stored in the identity. Default to 'timezone'.
     * - `resolver` The resolver implementation to use.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'timezoneField' => 'timezone',
        'resolver' => ApiResolver::class,
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $credentials)
    {
        if (
            empty($credentials[self::CREDENTIAL_USERNAME]) &&
            empty($credentials[self::CREDENTIAL_PASSWORD]) &&
            empty($credentials[self::CREDENTIAL_TOKEN])
        ) {
            return null;
        }

        return $this->findIdentity($credentials);
    }

    /**
     * Retrieve user record using provided credentials.
     *
     * @param array $credentials The user credentials
     * @return array|\ArrayAccess|null
     */
    protected function findIdentity(array $credentials)
    {
        $identity = $this->getResolver()->find($credentials);
        if (empty($identity)) {
            return null;
        }

        // Store user timezone in identity
        $timezoneField = $this->getConfig('timezoneField', 'timezone');
        if (!empty($credentials[$timezoneField])) {
            $identity[$timezoneField] = $this->userTimezone($credentials[$timezoneField]);
        }

        // Flatten username and renew token in identity
        $identity[self::CREDENTIAL_USERNAME] = $identity['attributes']['username'];
        $identity[self::CREDENTIAL_TOKEN] = $identity['tokens']['renew'];

        return $identity;
    }

    /**
     * Get user's timezone from offset.
     *
     * @param string $offset Must contain UTC offset in seconds, plus Daylight Saving Time DST 0 or 1 like: '3600 1' or '7200 0'. Defaults to UTC.
     * @return string The timezone name.
     */
    protected function userTimezone(string $offset = '0'): string
    {
        $data = explode(' ', $offset);
        $dst = empty($data[1]) ? 0 : 1;

        return timezone_name_from_abbr('', intval($data[0]), $dst);
    }
}
