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

use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Perform basic login and logout operations.
 */
class LoginController extends AppController
{

    /**
     * Display login page or perform login via API.
     *
     * @return \Cake\Http\Response|null
     */
    public function login() : ?Response
    {
        $this->request->allowMethod(['get', 'post']);

        if (!$this->request->is('post')) {
            // Display login form.
            return null;
        }

        // Attempted login.
        $user = null;
        $reason = 'Username or password is incorrect';
        try {
            $user = $this->Auth->identify();
        } catch (BEditaClientException $e) {
            $this->log('Login failed - ' . $e->getMessage(), LogLevel::INFO);
            $attributes = $e->getAttributes();
            if (!empty($attributes['reason'])) {
                $reason = $attributes['reason'];
            }
        }
        if (!empty($user)) {
            // Successful login. Redirect.
            $this->Auth->setUser($user);
            $this->Flash->success(__('Successfully logged in'));

            return $this->redirect($this->Auth->redirectUrl());
        }

        // Failed login.
        $this->Flash->error(__($reason));

        return null;
    }

    /**
     * Logout and redirect to login page.
     *
     * @return \Cake\Http\Response
     */
    public function logout() : Response
    {
        $this->request->allowMethod(['get']);

        return $this->redirect($this->Auth->logout());
    }
}
