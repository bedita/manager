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
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * User Profile controller: view and edit logged user data
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class UserProfileController extends AppController
{
    /**
     * View profile data
     *
     * @return void
     */
    public function view() : void
    {
        $this->request->allowMethod(['get']);

        $id = $this->Auth->user('id');
        try {
            $response = $this->apiClient->getObject($id, 'users');
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);
        }

        $this->set('object', $response['data']);
    }

    /**
     * Save user profile data
     *
     * @return \Cake\Http\Response|null
     */
    public function save() : ?Response
    {
        $data = $this->request->getData();
        if (empty($data['password'])) {
            unset($data['password']);
            unset($data['confirm-password']);
        } elseif ($data['password'] != $data['confirm-password']) {
            $this->Flash->error(__('Invalid data: password and confirm-password do not match'));

            return $this->redirect(['_name' => 'profile:view']);
        }
        try {
            $this->apiClient->saveObject('users', $data);
            $this->Flash->success(__('User profile saved'));
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);
        }

        return $this->redirect(['_name' => 'profile:view']);
    }
}
