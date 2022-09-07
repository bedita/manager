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

namespace App\Test\TestCase\Authentication\Identifier;

use App\Identifier\ApiIdentifier;
use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Identifier\ApiIdentifier} Test Case
 *
 * @coversDefaultClass \App\Identifier\ApiIdentifier
 */
class ApiIdentifierTest extends TestCase
{
    /**
     * Data provider for `testIdentify` test case.
     *
     * @return array
     */
    public function identifyProvider(): array
    {
        return [
            'missing-credentials' => [
                [],
                null,
            ],
            'wrong' => [
                [
                    'username' => 'fake-user',
                    'password' => 'fake-password',
                ],
                null,
            ],
            'ok' => [
                [
                    'username' => env('BEDITA_ADMIN_USR'),
                    'password' => env('BEDITA_ADMIN_PWD'),
                ],
                [
                    'username' => env('BEDITA_ADMIN_USR'),
                    'id' => 1,
                    'tokens' => [],
                    'token' => '',
                ],
            ],
            'timezone' => [
                [
                    'username' => env('BEDITA_ADMIN_USR'),
                    'password' => env('BEDITA_ADMIN_PWD'),
                    'timezone' => '7200 1',
                ],
                [
                    'username' => env('BEDITA_ADMIN_USR'),
                    'timezone' => 'Europe/Paris',
                    'tokens' => [],
                    'token' => '',
                ],
            ],
        ];
    }

    /**
     * Test user identification.
     *
     * @param array $credentials Test credentials
     * @param array|null $expected Expected result
     * @return void
     * @covers ::identify()
     * @covers ::findIdentity()
     * @covers ::userTimezone()
     * @dataProvider identifyProvider()
     */
    public function testIdentify(array $credentials, ?array $expected): void
    {
        ApiClientProvider::getApiClient()->setupTokens([]); // reset client
        $identifier = new ApiIdentifier(['timezoneField' => 'timezone']);
        $identity = $identifier->identify($credentials);

        if (isset($expected['tokens'])) {
            $client = ApiClientProvider::getApiClient();
            $expected['tokens'] = $client->getTokens();
            $expected['token'] = $expected['tokens']['renew'];
        }

        if ($expected === null) {
            static::assertNull($identity);

            return;
        }
        foreach ($expected as $key => $val) {
            $this->assertEquals($val, $identity[$key]);
        }
    }
}
