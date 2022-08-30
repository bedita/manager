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

use App\Identifier\Resolver\ApiResolver;
use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Identifier\Resolver\ApiResolver} Test Case
 *
 * @coversDefaultClass \App\Identifier\Resolver\ApiResolver
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
                        'name' => null,
                        'surname' => null,
                        'email' => null,
                        'person_title' => null,
                        'gender' => null,
                        'birthdate' => null,
                        'deathdate' => null,
                        'company' => false,
                        'company_name' => null,
                        'company_kind' => null,
                        'street_address' => null,
                        'city' => null,
                        'zipcode' => null,
                        'country' => null,
                        'state_name' => null,
                        'phone' => null,
                        'website' => null,
                        'national_id_number' => null,
                        'vat_number' => null,
                        'pseudonym' => null,
                        'status' => 'on',
                        'uname' => 'bedita',
                        'title' => null,
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                        'user_preferences' => null,
                    ],
                    'id' => '1',
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

        if ($expected === null) {
            static::assertNull($identity);

            return;
        }

        foreach ($expected as $key => $val) {
            $this->assertEquals($val, $identity[$key]);
        }

        // test renew token for successful cases
        $token = $identity['tokens']['renew'];
        $identity = $resolver->find(compact('token'));

        foreach ($expected as $key => $val) {
            $this->assertEquals($val, $identity[$key]);
        }
    }
}
