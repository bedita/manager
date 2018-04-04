<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase;

use App\ApiClientProvider;
use Cake\TestSuite\TestCase;

/**
 * {@see App\ApiClientProvider} Test Case
 *
 * @coversDefaultClass \App\ApiClientProvider
 */
class ApiClientProviderTest extends TestCase
{
    /**
     * Test `ApiClientProvider` methods
     *
     * @return void
     */
    public function testApiClient() : void
    {
        ApiClientProvider::setApiClient(null);
        // test create
        $client = ApiClientProvider::getApiClient();
        static::assertNotEmpty($client);
        // test use created
        $client = ApiClientProvider::getApiClient();
        static::assertNotEmpty($client);
    }
}
