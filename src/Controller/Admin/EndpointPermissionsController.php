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
     * @inheritDoc
     */
    protected bool $paginated = true;

    /**
     * @inheritDoc
     */
    protected array $filters = [
        'endpoint_id' => 'endpoints',
        'application_id' => 'applications',
        'role_id' => 'roles',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        parent::index();
        $this->set('applications', $this->comboOptions('/admin/applications'));
        $this->set('endpoints', $this->comboOptions('/admin/endpoints'));
        $this->set('roles', $this->comboOptions('/roles'));

        return null;
    }

    /**
     * Get `id => name` options from endpoint, sorted by name, with leading `-` (null) option.
     *
     * @param string $endpoint The API endpoint
     * @return array
     */
    protected function comboOptions(string $endpoint): array
    {
        $response = $this->apiClient->get($endpoint, ['page_size' => 100]);
        $options = Hash::combine((array)$response, 'data.{n}.id', 'data.{n}.attributes.name');
        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return ['-' => '-'] + $options;
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
