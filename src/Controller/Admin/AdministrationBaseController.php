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

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Administration Controller
 */
abstract class AdministrationBaseController extends AppController
{
    /**
     * Endpoint
     *
     * @var string
     */
    protected $endpoint = '/admin';

    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     * Readonly flag view.
     *
     * @var bool
     */
    protected $readonly = true;

    /**
     * Deleteonly flag view.
     *
     * @var bool
     */
    protected $deleteonly = false;

    /**
     * Properties to show in index columns
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Properties that are secrets
     *
     * @var array
     */
    protected $propertiesSecrets = [];

    /**
     * Meta to show in index columns
     *
     * @var array
     */
    protected $meta = ['created', 'modified'];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');
    }

    /**
     * Restrict `model` module access to `admin`
     *
     * {@inheritDoc}
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $res = parent::beforeFilter($event);
        if ($res !== null) {
            return $res;
        }

        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        $roles = (array)$user->get('roles');
        if (empty($roles) || !in_array('admin', $roles)) {
            throw new UnauthorizedException(__('Module access not authorized'));
        }

        return null;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $query = $this->getRequest()->getQueryParams();

        try {
            $endpoint = $this->resourceType === 'roles' ? $this->endpoint : sprintf('%s/%s', $this->endpoint, $this->resourceType);
            $response = $this->apiClient->get($endpoint, $query);
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('resources', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('resourceType', $this->resourceType);
        $this->set('properties', $this->properties);
        $this->set('propertiesSecrets', $this->propertiesSecrets);
        $this->set('metaColumns', $this->meta);
        $this->set('filter', []);
        $this->set('schema', (array)$this->Schema->getSchema($this->resourceType));
        $this->set('readonly', $this->readonly);
        $this->set('deleteonly', $this->deleteonly);

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
        $data = (array)$this->getRequest()->getData();
        $id = (string)Hash::get($data, 'id');
        unset($data['id']);
        $body = [
            'data' => [
                'type' => $this->resourceType,
                'attributes' => $data,
            ],
        ];
        $endpoint = $this->endpoint();
        try {
            if (empty($id)) {
                $this->apiClient->post($endpoint, json_encode($body));
            } else {
                $body['data']['id'] = $id;
                $this->apiClient->patch(sprintf('%s/%s', $endpoint, $id), json_encode($body));
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => sprintf('admin:list:%s', $this->resourceType)]);
    }

    /**
     * Remove roles by ID
     *
     * @param string $id The role ID
     * @return \Cake\Http\Response|null
     */
    public function remove(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        try {
            $this->apiClient->delete(sprintf('%s/%s', $this->endpoint(), $id));
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => sprintf('admin:list:%s', $this->resourceType)]);
    }

    /**
     * Get endpoint by resource type and endpoint.
     * Roles => /roles
     * Other => /admin/:endpoint
     *
     * @return string
     */
    protected function endpoint(): string
    {
        if ($this->resourceType === 'roles') {
            return $this->endpoint;
        }

        return sprintf('%s/%s', $this->endpoint, $this->resourceType);
    }
}
