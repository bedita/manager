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
            $applications = $this->apiClient->get('/admin/applications', ['filter' => ['name' => 'manager']]);
            $applicationId = (string)Hash::get($applications, 'data.0.id');
            $endpoints = $this->apiClient->get('/admin/endpoints', ['filter' => ['name' => 'auth']]);
            $endpointId = (string)Hash::get($endpoints, 'data.0.id');
            $endpointPermissions = $this->apiClient->get('/admin/endpoint_permissions', [
                'filter' => [
                    'application_id' => $applicationId,
                    'endpoint_id' => $endpointId,
                ],
            ]);
            $allowedRoles = (array)Hash::extract($endpointPermissions, 'data.{n}.attributes.role_id', []);
            $resources = $this->viewBuilder()->getVar('resources');
            $resources = array_filter($resources, function ($role) use ($allowedRoles) {
                return $role['attributes']['name'] !== 'admin' && in_array($role['id'], $allowedRoles);
            });
            $this->set('resources', $resources);
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
        }

        return null;
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
