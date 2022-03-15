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

namespace App\Test\TestCase\Authentication\Identifier\Resolver;

use App\Authentication\Identifier\Resolver\ApiResolver;
use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Authentication\Identifier\Resolver\ApiResolver} Test Case
 *
 * @coversDefaultClass \App\Authentication\Identifier\Resolver\ApiResolver
 */
class ApiResolverTest extends TestCase
{
    public function findProvider(): array
    {
        return [
            'missing-credentials' => [
                [],
                null,
            ],
            'wrong-credentials' => [
                [
                    'username' => 'fake-user',
                    'password' => 'fake-password',
                ],
                null,
            ],
            'wrong-token' => [
                [
                    'token' => 'asd123',
                ],
                null,
            ],
            'ok' => [
                [
                    'username' => env('BEDITA_ADMIN_USR'),
                    'password' => env('BEDITA_ADMIN_PWD'),
                ],
                [
                    'attributes' => [
                        'username' => env('BEDITA_ADMIN_USR'),
                    ],
                    'id' => '1',
                    'tokens' => [],
                ],
            ],
        ];
    }

    /**
     * Test identity resolution.
     *
     * @param array $credentials Test credentials
     * @param array|null $expected Expected result
     * @return void
     * @covers ::find()
     * @dataProvider findProvider()
     */
    public function testFind(array $credentials, ?array $expected): void
    {
        ApiClientProvider::getApiClient()->setupTokens([]); // reset client
        $resolver = new ApiResolver();
        $identity = $resolver->find($credentials);

        if (isset($expected['tokens'])) {
            $client = ApiClientProvider::getApiClient();
            $expected['tokens'] = $client->getTokens();
        }

        if ($expected === null) {
            static::assertNull($identity);

            return;
        }

        static::assertArraySubset($expected, $identity);

        // test renew token for successful cases
        $token = $identity['tokens']['renew'];
        $identity = $resolver->find(compact('token'));

        static::assertArraySubset($expected, $identity);
    }
}
