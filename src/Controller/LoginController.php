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

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;

/**
 * Perform basic login and logout operations
 */
class LoginController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->request->action === 'login') {
            $this->loadComponent('Auth', [
                'authenticate' => [
                'API' => [
                    'apiClient' => $this->apiClient,
                        ],
                    ],
                    'loginAction' => ['_name' => 'login'],
                    'loginRedirect' => ['_name' => 'dashboard'],
                ]);
        }
    }

    /**
     * Display login page or perform login via API
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if (!$user) {
                throw new UnauthorizedException(__('Login not successful'));
            }
            $session = $this->request->session();
            $tokens = $this->Auth->getAuthenticate('API')->getConfig('tokens');
            $session->write('tokens', $tokens);
            $session->write('user', $user);

            return $this->redirect(['_name' => 'dashboard']);
        }
    }

    /**
     * Perform user logout
     */
    public function logout()
    {
        $session = $this->request->session();
        setcookie(Configure::read('Session.cookie', 'CAKEPHP'), '', time() - 42000, '/');
        $session->destroy();

        return $this->redirect(['_name' => 'login']);
    }
}
