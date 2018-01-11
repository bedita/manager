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

use App\Model\API\BEditaClient;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Base Application Controller
 */
class AppController extends Controller
{

    /**
     * BEdita4 API client
     *
     * @var \App\Model\API\BEditaClient
     */
    protected $apiClient = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');

        $this->apiClient = new BEditaClient(Configure::read('API.apiBaseUrl'), Configure::read('API.apiKey'));

        $this->loadComponent('Auth', [
            'authenticate' => [
                'API' => [
                    'apiClient' => $this->apiClient,
                ],
            ],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
        ]);
        $this->Auth->deny();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
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
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if ($this->Auth) {
            $user = $this->Auth->user();
            $tokens = $this->apiClient->getTokens();
            if (!empty(array_diff_assoc((array)$user['tokens'], $tokens))) {
                $user['tokens'] = $tokens;
                $this->Auth->setUser($user);
            }

            $this->set(compact('user'));
            $this->readModules();
        }
    }

    /**
     * Read modules and project info from `/home' endpoint.
     *
     * @return void
     */
    protected function readModules()
    {
        static $excludedModules = ['auth', 'admin', 'model', 'roles', 'signup', 'status', 'trash'];

        $home = Cache::remember(
            sprintf('home_%d', $this->Auth->user('id')),
            function () {
                return $this->apiClient->get('/home');
            }
        );

        $modules = collection($home['meta']['resources'])
            ->map(function (array $data, $endpoint) {
                $name = substr($endpoint, 1);

                return $data + compact('name');
            })
            ->reject(function (array $data) use ($excludedModules) {
                return in_array($data['name'], $excludedModules);
            })
            ->toList();
        $project = [
            'name' => $home['meta']['project']['name'],
            'version' => $home['meta']['version'],
            'colophon' => '', // TODO: populate this value.
        ];

        $this->set(compact('modules', 'project'));
    }
}
