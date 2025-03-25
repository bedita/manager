<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2025 Atlas Srl, Chialab Srl
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
 * ExternalAuth Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ExternalAuthController extends AdministrationBaseController
{
    /**
     * @inheritDoc
     */
    protected $resourceType = 'external_auth';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'user_id' => 'string',
        'auth_provider_id' => 'auth_providers',
        'provider_username' => 'string',
        'params' => 'json',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        parent::index();
        $authProviders = $this->apiClient->get('/admin/auth_providers', []);
        $authProviders = Hash::combine((array)$authProviders, 'data.{n}.id', 'data.{n}.attributes.name');
        $authProviders['-'] = '-';
        $this->set('auth_providers', $authProviders);

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

    /**
     * @inheritDoc
     */
    protected $meta = [];
}
