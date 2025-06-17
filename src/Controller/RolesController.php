<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
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
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Roles Controller: list
 */
class RolesController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);
        if (!$this->allowed()) {
            throw new UnauthorizedException(__('You are not authorized to access this resource'));
        }

        return null;
    }

    /**
     * Check if the request is allowed.
     *
     * @return bool
     */
    protected function allowed(): bool
    {
        // block requests from browser address bar
        $sameOrigin = (string)Hash::get((array)$this->request->getHeader('Sec-Fetch-Site'), 0) === 'same-origin';
        $noReferer = empty((array)$this->request->getHeader('Referer'));
        $isNavigate = in_array('navigate', (array)$this->request->getHeader('Sec-Fetch-Mode'));
        if (!$sameOrigin || $noReferer || $isNavigate) {
            return false;
        }
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        $roles = empty($user) ? [] : (array)$user->get('roles');
        if (empty($roles)) {
            return false;
        }

        return true;
    }

    /**
     * List all roles.
     *
     * @return \Cake\Http\Response|null
     */
    public function list(): ?Response
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod(['get']);
        try {
            $response = $this->apiClient->get('roles', []);
        } catch (BEditaClientException $error) {
            $this->log($error->getMessage(), LogLevel::ERROR);
            $this->set(compact('error'));
            $this->setSerialize(['error']);

            return null;
        }
        $this->set((array)$response);
        $this->setSerialize(array_keys($response));

        return null;
    }
}
