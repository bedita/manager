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
namespace App\Utility;

use App\Controller\Admin\RolesController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

/**
 * Save and delete permissions via API
 */
trait PermissionsTrait
{
    /**
     * Save permissions for single object.
     *
     * @param array $response The object response
     * @param array $schema The object type schema
     * @param array $newPermissions The permissions to save
     * @return bool
     */
    public function savePermissions(array $response, array $schema, array $newPermissions): bool
    {
        if (!in_array('Permissions', (array)Hash::get($schema, 'associations'))) {
            return false;
        }
        $objectId = (string)Hash::get($response, 'data.id');
        $oldPermissions = (array)Hash::get($response, 'data.meta.perms.roles');
        $oldPermissions = $this->rolesByNames($oldPermissions);
        $newPermissions = $this->rolesByIds($newPermissions);
        $toRemove = array_keys(array_diff($oldPermissions, $newPermissions));
        $toAdd = array_keys(array_diff($newPermissions, $oldPermissions));
        $toRemove = $this->objectPermissionsIds($objectId, $toRemove);
        $this->removePermissions($toRemove);
        $this->addPermissions($objectId, $toAdd);

        return true;
    }

    /**
     * Add permissions per object by ID
     *
     * @param string $objectId The object ID
     * @param array $roleIds The role IDs
     * @return void
     */
    public function addPermissions(string $objectId, array $roleIds): void
    {
        foreach ($roleIds as $roleId) {
            ApiClientProvider::getApiClient()->save(
                'object_permissions',
                ['object_id' => $objectId, 'role_id' => $roleId]
            );
        }
    }

    /**
     * Remove permissions by object permission IDs
     *
     * @param array $objectPermissionIds The object permission IDs
     * @return void
     */
    public function removePermissions(array $objectPermissionIds): void
    {
        foreach ($objectPermissionIds as $id) {
            ApiClientProvider::getApiClient()->deleteObject($id, 'object_permissions');
        }
    }

    /**
     * Object permissions IDs per role IDs.
     *
     * @param string $objectId The object ID
     * @param array $roleIds The role IDs
     * @return array
     */
    public function objectPermissionsIds(string $objectId, array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }
        $query = ['filter' => ['object_id' => $objectId], 'page_size' => 100];
        $objectPermissions = (array)ApiClientProvider::getApiClient()->getObjects('object_permissions', $query);
        $objectPermissions = Hash::combine($objectPermissions, 'data.{n}.attributes.role_id', 'data.{n}.id');

        return array_map(function ($roleId) use ($objectPermissions) {
            return $objectPermissions[$roleId];
        }, $roleIds);
    }

    /**
     * Return roles data (<id>:<name), using cache.
     *
     * @return array
     */
    public function roles(): array
    {
        return Cache::remember(RolesController::CACHE_KEY_ROLES, function () {
            return Hash::combine(
                (array)ApiClientProvider::getApiClient()->get('/roles'),
                'data.{n}.id',
                'data.{n}.attributes.name'
            );
        });
    }

    /**
     * Return roles data (<id>:<name) from role names
     *
     * @param array $names Role names
     * @return array Roles IDs and names
     */
    public function rolesByNames(array $names): array
    {
        $result = [];
        $flipped = array_flip($this->roles());
        foreach ($names as $name) {
            $result[(string)Hash::get($flipped, $name)] = $name;
        }

        return $result;
    }

    /**
     * Return roles data (<id>:<name) from role ids
     *
     * @param array $ids Roles IDs
     * @return array Roles IDs and names
     */
    public function rolesByIds(array $ids): array
    {
        $roles = $this->roles();
        $result = [];
        foreach ($ids as $id) {
            $result[$id] = (string)Hash::get($roles, $id);
        }

        return $result;
    }
}
