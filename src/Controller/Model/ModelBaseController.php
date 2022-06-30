<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Model;

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Model base controller class
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 * @property \BEdita\WebTools\Controller\Component\ApiFormatterComponent $ApiFormatter
 */
abstract class ModelBaseController extends AppController
{
    /**
     * Resource type in use (object_types, properties, property_types)
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     * Single resource view existence flag.
     *
     * @var bool
     */
    protected $singleView = true;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');
        $this->loadComponent('BEdita/WebTools.ApiFormatter');

        $this->Schema->setConfig([
            'type' => $this->resourceType,
            'internalSchema' => true,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * Restrict `model` module access to `admin` for now
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $res = parent::beforeFilter($event);
        if ($res !== null) {
            return $res;
        }

        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if (empty($user->get('roles')) || !in_array('admin', $user->get('roles'))) {
            throw new UnauthorizedException(__('Module access not authorized'));
        }

        return null;
    }

    /**
     * Display resources list.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);

        try {
            $response = $this->apiClient->get(
                sprintf('/model/%s', $this->resourceType),
                $this->indexQuery()
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $response = $this->ApiFormatter->embedIncluded($response);
        $this->set('resources', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->indexList($this->resourceType));
        $this->set('filter', $this->Properties->filterList($this->resourceType));

        return null;
    }

    /**
     * Create query on `GET /model/{resource}` index call
     *
     * @return array
     */
    protected function indexQuery(): array
    {
        return $this->request->getQueryParams() + ['page_size' => 500];
    }

    /**
     * View single resource.
     *
     * @param string|int $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function view($id): ?Response
    {
        $endpoint = sprintf('/model/%s/%s', $this->resourceType, $id);
        try {
            $response = $this->apiClient->get($endpoint, $this->viewQuery());
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
        }

        $response = $this->ApiFormatter->embedIncluded($response);
        $resource = (array)$response['data'];
        $this->set(compact('resource'));
        $this->set('schema', $this->Schema->getSchema());
        $this->set('properties', $this->Properties->viewGroups($resource, $this->resourceType));

        return null;
    }

    /**
     * Create query on `GET /model/{resource}/{id}` call
     *
     * @return array
     * @codeCoverageIgnore
     */
    protected function viewQuery(): array
    {
        return [];
    }

    /**
     * Display new resource form.
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        $this->viewBuilder()->setTemplate('view');

        // Create stub resource with empty `attributes`.
        $schema = $this->Schema->getSchema();
        $attributes = array_fill_keys(
            array_keys(
                array_filter(
                    $schema['properties'],
                    function ($schema) {
                        return empty($schema['readOnly']);
                    }
                )
            ),
            ''
        );
        $resource = [
            'type' => $this->resourceType,
            'attributes' => $attributes,
        ];

        $this->set(compact('resource', 'schema'));
        $this->set('properties', $this->Properties->viewGroups($resource, $this->resourceType));

        return null;
    }

    /**
     * Save resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $data = $this->prepareRequest($this->resourceType);
        unset($data['_csrfToken']);
        $id = Hash::get($data, 'id');
        unset($data['id']);
        $body = [
            'data' => [
                'type' => $this->resourceType,
                'attributes' => $data,
            ],
        ];
        $endpoint = sprintf('/model/%s', $this->resourceType);

        try {
            if (empty($id)) {
                $response = $this->apiClient->post($endpoint, json_encode($body));
                $id = Hash::get($response, 'data.id');
            } else {
                $body['data']['id'] = $id;
                $this->apiClient->patch(sprintf('%s/%s', $endpoint, $id), json_encode($body));
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        if (!$this->singleView || empty($id)) {
            return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
        }

        return $this->redirect(
            [
                '_name' => 'model:view:' . $this->resourceType,
                'id' => $id,
            ]
        );
    }

    /**
     * Remove single resource.
     *
     * @param string $id Resource ID.
     * @return \Cake\Http\Response|null
     */
    public function remove(string $id): ?Response
    {
        try {
            $this->apiClient->delete(sprintf('/model/%s/%s', $this->resourceType, $id));
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => 'model:list:' . $this->resourceType]);
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        $this->set('resourceType', $this->resourceType);
        $this->set('moduleLink', ['_name' => 'model:list:' . $this->resourceType]);

        return parent::beforeRender($event);
    }
}
