<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use App\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Base Application Controller.
 *
 * @property \App\Controller\Component\ModulesComponent $Modules
 * @property \App\Controller\Component\SchemaComponent $Schema
 */
class AppController extends Controller
{

    /**
     * BEdita4 API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Security');
        $this->loadComponent('Csrf');

        $this->apiClient = ApiClientProvider::getApiClient();

        $this->loadComponent('Auth', [
            'authenticate' => [
                'Api' => [],
            ],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
        ]);
        $this->Auth->deny();

        $this->loadComponent('Modules', [
            'currentModuleName' => $this->name,
        ]);
        $this->loadComponent('Schema');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event) : void
    {
        parent::beforeFilter($event);

        $tokens = $this->Auth->user('tokens');
        if ($tokens) {
            $this->apiClient->setupTokens($tokens);
        }
    }

    /**
     * {@inheritDoc}
     *
     * Update session tokens if updated/refreshed by client
     */
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);

        if ($this->Auth && $this->Auth->user()) {
            $user = $this->Auth->user();
            $tokens = $this->apiClient->getTokens();
            if ($tokens && $user['tokens'] !== $tokens) {
                // Update tokens in session.
                $user['tokens'] = $tokens;
                $this->Auth->setUser($user);
            }

            $this->set(compact('user'));
        }

        $this->viewBuilder()->setTemplatePath('Pages/' . $this->name);
    }

    /**
     * Prepare request, set properly json data.
     *
     * @param string $type Object type
     * @return array request data
     */
    protected function prepareRequest($type) : array
    {
        // prepare json fields before saving
        $data = $this->request->getData();

        // when saving users, if password is empty, unset it
        if ($type === 'users' && array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
            unset($data['confirm-password']);
        }

        if (!empty($data['_jsonKeys'])) {
            $keys = explode(',', $data['_jsonKeys']);
            foreach ($keys as $key) {
                $data[$key] = json_decode($data[$key]);
            }
        }

        // relations data for view/save - prepare api calls
        if (!empty($data['relations'])) {
            $api = [];
            foreach ($data['relations'] as $relation => $relationData) {
                $id = $data['id'];

                foreach ($relationData as $method => $ids) {
                    $relatedIds = json_decode($ids, true);
                    if (!empty($relatedIds)) {
                        $api[] = compact('method', 'id', 'relation', 'relatedIds');
                    }
                }
            }

            $data['api'] = $api;
        }

        return $data;
    }
}
