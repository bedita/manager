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

use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Permissions Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class EndpointPermissionsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string|null
     */
    protected ?string $resourceType = 'endpoint_permissions';

    /**
     * @inheritDoc
     */
    protected bool $readonly = false;

    /**
     * @inheritDoc
     */
    protected array $properties = [
        'endpoint_id' => 'endpoints',
        'application_id' => 'applications',
        'role_id' => 'roles',
        'read' => 'bool',
        'write' => 'bool',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        parent::index();
        $applications = $this->apiClient->get('/admin/applications', []);
        $applications = Hash::combine((array)$applications, 'data.{n}.id', 'data.{n}.attributes.name');
        $applications['-'] = '-';
        $this->set('applications', $applications);
        $endpoints = $this->apiClient->get('/admin/endpoints', []);
        $endpoints = Hash::combine((array)$endpoints, 'data.{n}.id', 'data.{n}.attributes.name');
        $endpoints['-'] = '-';
        $this->set('endpoints', $endpoints);
        $roles = $this->apiClient->get('/roles', []);
        $roles = Hash::combine((array)$roles, 'data.{n}.id', 'data.{n}.attributes.name');
        $roles['-'] = '-';
        $this->set('roles', $roles);

        return null;
    }

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        // check '-' values and set to null
        $data = $this->request->getData();
        foreach ($data as $key => $value) {
            if ($value === '-') {
                $data[$key] = null;
            }
        }

        return parent::save();
    }
}
