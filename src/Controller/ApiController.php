<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use BEdita\WebTools\Controller\ApiProxyTrait;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * ApiController class.
 *
 * It proxies requests to BEdita API.
 * The response will be the raw json response of the API itself.
 * Links of API host is masked with manager host.
 */
class ApiController extends AppController
{
    use ApiProxyTrait;

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);
        if (!$this->allowed()) {
            throw new UnauthorizedException(__('You are not authorized to access this resource'));
        }
        $this->FormProtection->setConfig('unlockedActions', ['post', 'patch', 'delete']);

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
        if (in_array('admin', $roles)) {
            return true;
        }
        $method = $this->request->getMethod();
        $action = $this->request->getParam('pass')[0] ?? null;
        $action = $action != null && strpos($action, '/') > 0 ? explode('/', $action)[0] : $action;
        $blockedMethods = (array)Configure::read('ApiProxy.blocked', [
            'objects' => ['GET', 'POST', 'PATCH', 'DELETE'],
            'users' => ['GET', 'POST', 'PATCH', 'DELETE'],
        ]);
        $blocked = in_array($method, $blockedMethods[$action] ?? []);
        $modules = $this->viewBuilder()->getVar('modules');
        $modules = array_values($modules);
        $modules = (array)Hash::combine($modules, '{n}.name', '{n}.hints.allow');
        $modules = array_merge(
            $modules,
            [
                'history' => ['GET'],
                'model' => ['GET'],
            ],
        );
        $allowedMethods = (array)Hash::get($modules, $action, []);
        $allowed = in_array($method, $allowedMethods);

        return $allowed && !$blocked;
    }
}
