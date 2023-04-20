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
        $permissions = [['object_id' => $folder['id'], 'role_id' => 2]];
        $actual = $this->savePermissions($folder['id'], $schema, $permissions);
        static::assertFalse($actual);

        // test with 'Permissions' in schema.associations
        $schema['associations'] = ['Permissions'];
        // mock api save
        /** @var \BEdita\SDK\BEditaClient $apiClient */
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('save')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->savePermissions($folder['id'], $schema, $permissions);
        static::assertTrue($actual);
    }
}
