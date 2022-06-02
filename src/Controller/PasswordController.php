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
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Routing\Router;

/**
 * Perform password reset and change.
 */
class PasswordController extends AppController
{
    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['reset', 'change']);
    }

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if (empty($user->get('tokens'))) {
            return null;
        }

        // if authenticated, redirect to dashboard
        return $this->redirect(['_name' => 'dashboard']);
    }

    /**
     * Password reset.
     *
     * @return \Cake\Http\Response|null
     */
    public function reset(): ?Response
    {
        $this->getRequest()->allowMethod(['get', 'post']);

        if ($this->getRequest()->is('get')) {
            return null;
        }

        $data = [
            'contact' => $this->getRequest()->getData('email'),
            'change_url' => Router::url(['_name' => 'password:change'], true),
        ];
        try {
            $result = $this->apiClient->post(
                '/auth/change',
                json_encode($data),
                ['Content-Type' => 'application/json']
            );
            $this->set(compact('result'));
        } catch (BEditaClientException $ex) {
            $this->Flash->error(__($ex->getMessage()));

            return $this->redirect(['_name' => 'password:reset']);
        }
        $this->viewBuilder()->setTemplate('request_sent');

        return null;
    }

    /**
     * Password change action.
     *
     * @return \Cake\Http\Response|null
     */
    public function change(): ?Response
    {
        $this->getRequest()->allowMethod(['get', 'post']);

        if ($this->getRequest()->is('get')) {
            $this->set('uuid', $this->getRequest()->getQuery('uuid'));

            return null;
        }

        $data = [
            'uuid' => $this->getRequest()->getData('uuid'),
            'password' => $this->getRequest()->getData('password'),
            'login' => true,
        ];
        try {
            $result = (array)$this->apiClient->patch(
                '/auth/change',
                json_encode($data),
                ['Content-Type' => 'application/json']
            );
        } catch (BEditaClientException $ex) {
            $this->Flash->error(__($ex->getMessage()));

            return $this->redirect(['_name' => 'password:reset']);
        }

        $tokens = $result['meta'];
        $this->apiClient->setupTokens($tokens);
        $this->set(compact('result'));

        return $this->redirect(['_name' => 'dashboard']);
    }
}
