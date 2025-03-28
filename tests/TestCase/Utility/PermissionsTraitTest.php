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

use App\Controller\Admin\RolesController;
use App\Test\TestCase\Controller\BaseControllerTest;
use App\Utility\PermissionsTrait;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversTrait;

/**
 * {@see \App\Utility\PermissionsTrait} Test Case
 */
#[CoversClass(PermissionsTrait::class)]
#[CoversTrait(PermissionsTrait::class)]
#[CoversMethod(PermissionsTrait::class, 'addPermissions')]
#[CoversMethod(PermissionsTrait::class, 'objectPermissionsIds')]
#[CoversMethod(PermissionsTrait::class, 'removePermissions')]
#[CoversMethod(PermissionsTrait::class, 'roles')]
#[CoversMethod(PermissionsTrait::class, 'rolesByIds')]
#[CoversMethod(PermissionsTrait::class, 'rolesByNames')]
#[CoversMethod(PermissionsTrait::class, 'savePermissions')]
class PermissionsTraitTest extends BaseControllerTest
{
    use PermissionsTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Test `savePermission` method.
     *
     * @return void
     */
    public function testSavePermissions(): void
    {
        // test with schema.associations empty
        $response = [
            'data' => [
                'id' => '999',
            ],
        ];
        $schema = ['associations' => []];
        $permissions = [1];
        $actual = $this->savePermissions($response, $schema, $permissions);
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
        $actual = $this->savePermissions($response, $schema, $permissions);
        static::assertTrue($actual);
    }

    /**
     * Test `addPermissions` method
     *
     * @return void
     */
    public function testAddPermissions(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('save')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        /** @phpstan-ignore-next-line */
        $this->assertNull($this->addPermissions('999', [1,2,3]));
    }

    /**
     * Test `removePermissions` method
     *
     * @return void
     */
    public function testRemovePermissions(): void
    {
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('deleteObject')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        /** @phpstan-ignore-next-line */
        $this->assertNull($this->removePermissions([1,2,3]));
    }

    /**
     * Test `objectPermissionsIds` method
     *
     * @return void
     */
    public function testObjectPermissionsIds(): void
    {
        $objectId = '114';
        // check empty roles
        $actual = $this->objectPermissionsIds($objectId, []);
        static::assertEmpty($actual);

        // check non empty roles
        $objectPermissions = [
            'data' => [
                ['id' => 11, 'attributes' => ['object_id' => 111, 'role_id' => 1111]],
                ['id' => 12, 'attributes' => ['object_id' => 112, 'role_id' => 1112]],
                ['id' => 13, 'attributes' => ['object_id' => 113, 'role_id' => 1113]],
                ['id' => 14, 'attributes' => ['object_id' => 114, 'role_id' => 1114]],
            ],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('getObjects')->willReturn($objectPermissions);
        ApiClientProvider::setApiClient($apiClient);
        $roles = [1111, 1112, 1113, 1114];
        $expected = [11, 12, 13, 14];
        $actual = $this->objectPermissionsIds($objectId, $roles);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `roles` method
     *
     * @return void
     */
    public function testRoles(): void
    {
        Cache::delete(RolesController::CACHE_KEY_ROLES);
        $actual = $this->roles();
        static::assertIsArray($actual);
    }

    /**
     * Test `rolesByNames` method
     *
     * @return void
     */
    public function testRolesByNames(): void
    {
        Cache::write(RolesController::CACHE_KEY_ROLES, [
            1 => 'developer',
            2 => 'business',
            3 => 'guest',
            4 => 'other',
        ]);
        $actual = $this->rolesByNames(['developer','guest']);
        $expected = [1 => 'developer', 3 => 'guest'];
        static::assertSame($expected, $actual);
    }

    /**
     * Test `rolesByIds` method
     *
     * @return void
     */
    public function testRolesByIds(): void
    {
        Cache::write(RolesController::CACHE_KEY_ROLES, [
            1 => 'developer',
            2 => 'business',
            3 => 'guest',
            4 => 'other',
        ]);
        $actual = $this->rolesByIds([1,3]);
        $expected = [1 => 'developer', 3 => 'guest'];
        static::assertSame($expected, $actual);
    }
}
