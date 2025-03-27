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
use BEdita\WebTools\Utility\ApiTools;
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
    protected string $endpoint = '/admin';

    /**
     * Resource type in use
     *
     * @var string|null
     */
    protected ?string $resourceType = null;

    /**
     * Readonly flag view.
     *
     * @var bool
     */
    protected bool $readonly = true;

    /**
     * Deleteonly flag view.
     *
     * @var bool
     */
    protected bool $deleteonly = false;

    /**
     * Properties to show in index columns
     *
     * @var array
     */
    protected array $properties = [];

    /**
     * Properties to json decode before save
     *
     * @var array
     */
    protected $propertiesForceJson = [];

    /**
     * Properties that are secrets
     *
     * @var array
     */
    protected array $propertiesSecrets = [];

    /**
     * Meta to show in index columns
     *
     * @var array
     */
    protected array $meta = ['created', 'modified'];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Properties');
    }

    /**
     * {@inheritDoc}
     *
     * Restrict `model` module access to `admin`
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
        try {
            $response = $this->loadData();
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('resources', (array)Hash::get($response, 'data'));
        $this->set('meta', (array)Hash::get($response, 'meta'));
        $this->set('links', (array)Hash::get($response, 'links'));
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
        $body = $this->prepareBody($data);
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

    /**
     * Get all results iterating over pagination.
     *
     * @return array
     */
    protected function loadData(): array
    {
        $resourceEndpoint = sprintf('%s/%s', $this->endpoint, $this->resourceType);
        $endpoint = $this->resourceType === 'roles' ? 'roles' : $resourceEndpoint;
        $resultResponse = ['data' => []];
        $pageCount = $page = 1;
        $total = 0;
        $limit = 500;
        while ($limit > $total && $page <= $pageCount) {
            $response = (array)$this->apiClient->get($endpoint, compact('page') + ['page_size' => 100]);
            $response = ApiTools::cleanResponse($response);
            $resultResponse['data'] = array_merge(
                $resultResponse['data'],
                (array)Hash::get($response, 'data'),
            );
            $resultResponse['meta'] = Hash::get($response, 'meta');
            $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
            $count = (int)Hash::get($response, 'meta.pagination.page_items');
            $total += $count;
            $page++;
        }

        return $resultResponse;
    }

    /**
     * Prepare body for request
     *
     * @param array $data The data
     * @return array
     */
    protected function prepareBody(array $data): array
    {
        foreach ($this->propertiesForceJson as $property) {
            $data[$property] = json_decode((string)Hash::get($data, $property), true);
        }
        $attributes = array_filter($data, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY);

        return [
            'data' => [
                'type' => $this->resourceType,
                'attributes' => $attributes,
            ],
        ];
    }
}
