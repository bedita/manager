<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper class to handle permissions on modules.
 */
class PermsHelper extends Helper
{
    /**
     * API methods allowed in current module
     *
     * @var array
     */
    protected $current = [];

    /**
     * API methods allowed in all modules
     *
     * @var array
     */
    protected $allowed = [];

    /**
     * {@inheritDoc}
     *
     * Init API and WebAPP base URL
     *
     * @return  void
     */
    public function initialize(array $config): void
    {
        $modules = (array)$this->_View->get('modules');
        $this->allowed = Hash::combine($modules, '{s}.name', '{s}.hints.allow');
        $currentModule = (array)$this->_View->get('currentModule');
        $this->current = (array)Hash::get($currentModule, 'hints.allow');
    }

    /**
     * Check lock/unlock permission.
     *
     * @return bool
     */
    public function canLock(): bool
    {
        /** @var \Authentication\Identity $identity */
        $identity = $this->_View->get('user');

        return in_array('admin', (array)$identity->get('roles'));
    }

    /**
     * Check create permission.
     *
     * @param string|null $module Module name
     * @return bool
     */
    public function canCreate(?string $module = null): bool
    {
        return $this->isAllowed('POST', $module);
    }

    /**
     * Check delete permission.
     *
     * @param array $object The object
     * @return bool
     */
    public function canDelete(array $object): bool
    {
        $locked = (bool)Hash::get($object, 'meta.locked', false);
        $module = (string)Hash::get($object, 'type');

        return !$locked && $this->isAllowed('DELETE', $module);
    }

    /**
     * Check save permission.
     *
     * @param string|null $module Module name
     * @return bool
     */
    public function canSave(?string $module = null): bool
    {
        return $this->isAllowed('PATCH', $module);
    }

    /**
     * Check read permission.
     *
     * @param string|null $module Module name
     * @return bool
     */
    public function canRead(?string $module = null): bool
    {
        return $this->isAllowed('GET', $module);
    }

    /**
     * Check if a method is allowed on a module.
     *
     * @param string $method Method to check
     * @param string|null $module Module name, if missing or null current module is used.
     * @return bool
     */
    protected function isAllowed(string $method, ?string $module = null): bool
    {
        if (empty($module)) {
            if (empty($this->current)) {
                return true;
            }

            return in_array($method, $this->current);
        }

        $allowed = (array)Hash::get($this->allowed, $module);

        return in_array($method, $allowed);
    }

    /**
     * Access string (can be 'read', 'write', 'hidden') per role and module.
     *
     * @param array $accessControl The access control array
     * @param string $roleName The role name
     * @param string $moduleName The module name
     * @return string
     */
    public function access(array $accessControl, string $roleName, string $moduleName): string
    {
        $roleAccesses = Hash::get($accessControl, $roleName, []);
        if (empty($roleAccesses)) {
            return 'write';
        }
        $hiddenModules = Hash::get($roleAccesses, 'hidden', []);
        if (in_array($moduleName, $hiddenModules)) {
            return 'hidden';
        }
        $readonlyModules = Hash::get($roleAccesses, 'readonly', []);

        return in_array($moduleName, $readonlyModules) ? 'read' : 'write';
    }
}
