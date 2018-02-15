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

use BEdita\SDK\BEditaClient;
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

        $this->apiClient = new BEditaClient(Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey'));

        $this->loadComponent('Auth', [
            'authenticate' => [
                'Api' => [
                    'apiClient' => $this->apiClient,
                ],
            ],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
        ]);
        $this->Auth->deny();

        $this->loadComponent('Modules', [
            'apiClient' => $this->apiClient,
            'currentModuleName' => $this->name,
        ]);
        $this->loadComponent('Schema', [
            'apiClient' => $this->apiClient,
        ]);
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
    }
}
