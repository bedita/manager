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

use BEdita\WebTools\ApiClientProvider;
use Cake\Utility\Hash;

/**
 * Save and delete permissions via API
 */
trait PermissionsTrait
{
    /**
     * Save permissions for single object.
     *
     * @param string $id The object ID
     * @param array $schema The object type schema
     * @param array $permissions The permissions to save
     * @return bool
     */
    public function savePermissions(string $id, array $schema, array $permissions): bool
    {
        if (!in_array('Permissions', (array)Hash::get($schema, 'associations'))) {
            return false;
        }
        foreach ($permissions as $roleId) {
            ApiClientProvider::getApiClient()->save('permissions', ['object_id' => $id, 'role_id' => $roleId]);
        }

        return true;
    }
}
