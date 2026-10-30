<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
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
 * UserTokens Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class UserTokensController extends AdministrationBaseController
{
    /**
     * @inheritDoc
     */
    protected ?string $resourceType = 'user_tokens';

    /**
     * @inheritDoc
     */
    protected bool $readonly = false;

    /**
     * @inheritDoc
     */
    protected array $properties = [
        'user_id' => 'users',
        'application_id' => 'applications',
        'client_token' => 'string',
        'secret_token' => 'string',
        'token_type' => 'token',
        'expires' => 'date',
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
    protected array $meta = ['created', 'used'];

    /**
     * @inheritDoc
     */
    protected ?string $sortBy = 'application_id';

    /**
     * @inheritDoc
     */
    protected bool $paginated = true;

    /**
     * @inheritDoc
     */
    protected array $filters = [
        'application_id' => 'applications',
        'user_id' => 'users',
    ];

    /**
     * @inheritDoc
     */
    protected function labels(): array
    {
        return [
            'user_id' => __('User'),
            'application_id' => __('Application'),
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
        $applications = $this->apiClient->get('/admin/applications', ['page_size' => 100]);
        $applications = Hash::combine((array)$applications, 'data.{n}.id', 'data.{n}.attributes.name');
        $this->set('applications', $applications);
        $resources = (array)$this->viewBuilder()->getVar('resources');
        $ids = Hash::extract($resources, '{n}.attributes.user_id');
        $activeFilterUserId = (string)$this->getRequest()->getQuery('filter.user_id', '');
        if ($activeFilterUserId !== '') {
            $ids[] = $activeFilterUserId;
        }
        $this->set('users', $this->usersLabels($ids));

        return null;
    }

    /**
     * Get "<name> <surname> (<username>)" labels of users referenced by resources, keyed by user id.
     *
     * @param array<int> $ids The ids
     * @return array<string, string>
     */
    protected function usersLabels(array $ids): array
    {
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
