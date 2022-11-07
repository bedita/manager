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
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * User Profile controller: view and edit logged user data
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class UserProfileController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        $this->set('moduleLink', ['_name' => 'user_profile:view']);

        return parent::beforeRender($event);
    }

    /**
     * View profile data
     *
     * @return void
     */
    public function view(): void
    {
        $this->getRequest()->allowMethod(['get']);

        try {
            $response = $this->apiClient->get('/auth/user');
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            $response = [];
        }

        $revision = Hash::get($response, 'meta.schema.users.revision', null);
        $schema = $this->Schema->getSchema('users', $revision);
        $object = (array)Hash::get($response, 'data');
        $this->set('schema', $schema);
        $this->set('object', $object);
        $this->set('properties', $this->Properties->viewGroups($object, 'user_profile'));
        $this->set('currentAttributes', json_encode((array)Hash::get($object, 'attributes')));
    }

    /**
     * Save user profile data
     *
     * @return void
     */
    public function save(): void
    {
        $data = $this->getRequest()->getData();
        unset($data['id']);
        $this->changedAttributes($data);
        try {
            $this->changePassword($data);
            $this->changeData($data);
            $this->Flash->success(__('User profile saved'));
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        $this->redirect(['_name' => 'user_profile:view']);
    }

    /**
     * Change password, if needed.
     *
     * @param array $data The data
     * @return void
     */
    protected function changePassword(array &$data): void
    {
        $password = (string)Hash::get($data, 'password');
        if (!empty($password)) {
            $this->apiClient->patch('/auth/user', json_encode([
                'password' => $password,
                'old_password' => (string)Hash::get($data, 'old_password'),
            ]));
        }
        unset($data['password']);
        unset($data['old_password']);
        unset($data['confirm-password']);
    }

    /**
     * Change data, if changed
     *
     * @param array $data The data
     * @return void
     */
    protected function changeData(array $data): void
    {
        if (empty($data)) {
            return;
        }
        $this->apiClient->patch('/auth/user', json_encode($data));
    }
}
