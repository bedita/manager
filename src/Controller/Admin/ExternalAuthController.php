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

use BEdita\SDK\BEditaClientException;
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
    protected ?string $resourceType = 'external_auth';

    /**
     * @inheritDoc
     */
    protected bool $readonly = false;

    /**
     * @inheritDoc
     */
    protected array $properties = [
        'user_id' => 'users',
        'auth_provider_id' => 'auth_providers',
        'provider_username' => 'string',
        'params' => 'json',
    ];

    /**
     * @inheritDoc
     */
    protected array $propertiesForceJson = [
        'params',
    ];

    /**
     * @inheritDoc
     */
    protected array $meta = [];

    /**
     * @inheritDoc
     */
    protected ?string $sortBy = 'auth_provider_id';

    /**
     * @inheritDoc
     */
    protected bool $paginated = true;

    /**
     * @inheritDoc
     */
    protected array $filters = [
        'auth_provider_id' => 'auth_providers',
        'user_id' => 'users',
    ];

    /**
     * @inheritDoc
     */
    protected function labels(): array
    {
        return [
            'user_id' => __('User'),
            'auth_provider_id' => __('Auth provider'),
        ];
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        parent::index();
        $authProviders = $this->apiClient->get('/admin/auth_providers', ['page_size' => 100]);
        $authProviders = Hash::combine((array)$authProviders, 'data.{n}.id', 'data.{n}.attributes.name');
        $this->set('auth_providers', $authProviders);
        if (empty($authProviders)) {
            $this->Flash->warning(__('No auth providers found: you cannot create external auth entries. Create at least one auth provider first'));
        }
        $resources = (array)$this->viewBuilder()->getVar('resources');
        $ids = Hash::extract($resources, '{n}.attributes.user_id');
        $activeFilterUserId = (string)Hash::get((array)$this->viewBuilder()->getVar('activeFilter'), 'user_id', '');
        if ($activeFilterUserId !== '') {
            $ids[] = $activeFilterUserId;
        }
        $this->set('users', $this->usersLabels($ids));

        return null;
    }

    /**
     * Get "<name> <surname> (<username>)" labels of users by id, keyed by user id.
     *
     * @param array $ids User ids
     * @return array<string, string>
     */
    protected function usersLabels(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) {
            return [];
        }
        try {
            $response = (array)$this->apiClient->get('/users', [
                'filter' => ['id' => $ids],
                'fields' => 'name,surname,username',
                'page_size' => count($ids),
            ]);
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');

            return [];
        }
        $labels = [];
        foreach ((array)Hash::get($response, 'data') as $user) {
            $name = trim(sprintf('%s %s', Hash::get($user, 'attributes.name'), Hash::get($user, 'attributes.surname')));
            $labels[(string)$user['id']] = trim(sprintf('%s (%s)', $name, Hash::get($user, 'attributes.username')));
        }

        return $labels;
    }
}
