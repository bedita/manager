<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use App\Utility\ApiConfigTrait;
use BEdita\SDK\BEditaClientException;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Roles Modules Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class RolesModulesController extends AdministrationBaseController
{
    use ApiConfigTrait;

    /**
     * @inheritDoc
     */
    protected $endpoint = '/roles';

    /**
     * @inheritDoc
     */
    protected $resourceType = 'roles';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'name' => 'string',
        'description' => 'text',
    ];

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        parent::index();
        $this->set('access_control', (array)Configure::read(Inflector::camelize('access_control')));
        // get roles that manager app has on auth endpoint
        try {
            $endpointPermissions = $this->apiClient->get('/admin/endpoint_permissions', [
                'filter' => [
                    'application_id' => $this->managerApplicationId(),
                    'endpoint_id' => $this->authEndpointId(),
                ],
            ]);
            $this->set('resources', $this->allowedRoles(
                $this->viewBuilder()->getVar('resources'),
                (array)Hash::extract($endpointPermissions, 'data')
            ));
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return null;
    }

    /**
     * Filter roles, return non admin allowed ones.
     *
     * @param array $roles The roles
     * @param array $permissions The permissions
     * @return array
     */
    protected function allowedRoles(array $roles, array $permissions): array
    {
        return array_filter($roles, function ($role) use ($permissions) {
            if ($role['attributes']['name'] === 'admin') {
                return false;
            }
            if (empty($permissions)) {
                return true;
            }
            $roleId = (int)Hash::get($role, 'id');
            $allowed = array_filter($permissions, function ($endpointPermission) use ($roleId) {
                return Hash::get($endpointPermission, 'attributes.write') === true && Hash::get($endpointPermission, 'attributes.role_id') === $roleId;
            });

            return !empty($allowed);
        });
    }

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $data = (array)$this->getRequest()->getData('roles');
        $roles = array_keys($data);
        $payload = [];
        foreach ($roles as $roleName) {
            foreach ($data[$roleName] as $module => $perms) {
                if ($perms === 'write') {
                    continue;
                }
                if ($perms === 'read') {
                    $payload[$roleName]['readonly'][] = $module;
                } elseif ($perms === 'hidden') {
                    $payload[$roleName]['hidden'][] = $module;
                }
            }
        }
        $this->saveApiConfig(Inflector::camelize('access_control'), $payload);

        return $this->redirect(['_name' => 'admin:list:roles_modules']);
    }
}
