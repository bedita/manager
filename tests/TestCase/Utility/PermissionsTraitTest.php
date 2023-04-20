<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase\Utility;

use App\Test\TestCase\Controller\BaseControllerTest;
use App\Utility\PermissionsTrait;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;

/**
 * {@see \App\Utility\PermissionsTrait} Test Case
 *
 * @coversDefaultClass \App\Utility\PermissionsTrait
 */
class PermissionsTraitTest extends BaseControllerTest
{
    use PermissionsTrait;

    /**
     * Test `savePermission` method.
     *
     * @return void
     * @covers ::savePermissions()
     */
    public function testSavePermissions(): void
    {
        // test with schema.associations empty
        $folder = ['id' => '999'];
        $schema = ['associations' => []];
        $permissions = [1];
        $actual = $this->savePermissions($folder['id'], $schema, $permissions);
        static::assertFalse($actual);

        // test with 'Permissions' in schema.associations
        $schema['associations'] = ['Permissions'];
        // mock api save
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('save')->willReturn([]);
        $apiClient->method('deleteObject')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->savePermissions($folder['id'], $schema, $permissions);
        static::assertTrue($actual);
    }

    /**
     * Test `addPermissions` method
     *
     * @return void
     * @covers ::addPermissions()
     */
    public function testAddPermissions(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('save')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        $this->assertNull($this->addPermissions('999', [1,2,3]));
    }

    /**
     * Test `removePermissions` method
     *
     * @return void
     * @covers ::removePermissions()
     */
    public function testRemovePermissions(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('deleteObject')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        $this->assertNull($this->removePermissions([1,2,3]));
    }

    /**
     * Test `setupPermissionsRoles` method
     *
     * @return void
     * @covers ::setupPermissionsRoles()
     */
    public function testSetupPermissionsRoles(): void
    {
        $actual = $this->setupPermissionsRoles([1,2,3]);
        $expected = [1 => '', 2 => '', 3 => ''];
        static::assertSame($expected, $actual);
    }
}
