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

use App\Application;
use BEdita\SDK\BEditaClientException;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Perform basic login and logout operations.
 */
class LoginController extends AppController
{
    use InstanceConfigTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        // Projects configuration files base path
        'projectsPath' => CONFIG . 'projects' . DS,
    ];

    /**
     * Display login page or perform login via API.
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        // Add `head` to avoid errors on http `HEAD /` calls since they are redirected to `HEAD /login`
        $this->getRequest()->allowMethod(['get', 'head', 'post']);

        if (!$this->getRequest()->is('post')) {
            // Handle flash messages
            $this->handleFlashMessages($this->getRequest()->getQueryParams());
            // Load available projects info
            $this->loadAvailableProjects();

            // Display login form.
            return null;
        }

        return $this->authRequest();
    }

    /**
     * Perform login auth request via POST.
     *
     * @return \Cake\Http\Response|null
     */
    protected function authRequest(): ?Response
    {
        $reason = __('Invalid username or password');
        // Load project config if `multi project` setup
        Application::loadProjectConfig(
            (string)$this->getRequest()->getData('project'),
            (string)$this->getConfig('projectsPath')
        );
        try {
            $user = $this->Auth->identify();
        } catch (BEditaClientException $e) {
            $this->log('Login failed - ' . $e->getMessage(), LogLevel::INFO);
            $attributes = $e->getAttributes();
            if (!empty($attributes['reason'])) {
                $reason = $attributes['reason'];
            }
        }
        if (!empty($user) && is_array($user)) {
            // setup timezone from request
            $user['timezone'] = $this->userTimezone();
            // Successful login. Redirect.
            $this->Auth->setUser($user);
            // Setup current project name.
            $this->setupCurrentProject();

            return $this->redirect($this->Auth->redirectUrl());
        }

        // Failed login.
        $this->Flash->error(__($reason));
        $this->loadAvailableProjects();

        return null;
    }

    /**
     * Look for available projects in `config/projects/` folder
     *
     * @return void
     */
    protected function loadAvailableProjects(): void
    {
        $projects = [];
        $lookup = (string)$this->getConfig('projectsPath') . '*.php';
        $available = (array)glob($lookup);
        foreach ($available as $filename) {
            $return = include $filename;
            if (!is_array($return) || empty($return['Project']['name'])) {
                continue;
            }
            $projects[] = [
                'value' => pathinfo($filename, PATHINFO_FILENAME),
                'text' => $return['Project']['name'],
            ];
        }
        $this->set(compact('projects'));
    }

    /**
     * Setup current project name in session if selected from login form
     *
     * @return void
     */
    protected function setupCurrentProject(): void
    {
        $project = $this->getRequest()->getData('project');
        if (empty($project)) {
            return;
        }
        $this->getRequest()->getSession()->write('_project', $project);
    }

    /**
     * Retrieve user timezone from request data.
     *
     * @return string User timezone
     */
    protected function userTimezone(): string
    {
        // 'timezone_offset' must contain UTC offset in seconds
        // plus Daylight Saving Time DST 0 or 1 like: '3600 1' or '7200 0'
        $offset = $this->getRequest()->getData('timezone_offset');
        if (empty($offset)) {
            return 'UTC';
        }
        $data = explode(' ', (string)$offset);
        $dst = empty($data[1]) ? 0 : 1;

        return timezone_name_from_abbr('', intval($data[0]), $dst);
    }

    /**
     * Logout and redirect to login page.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $redirect = $this->redirect($this->Auth->logout());
        $this->getRequest()->getSession()->destroy();

        return $redirect;
    }

    /**
     * Handle flash messages for login page.
     * If login from a redirect, show messages; otherwise clear flash messages.
     *
     * @param array $query The query param
     * @return void
     */
    public function handleFlashMessages(array $query): void
    {
        if (!isset($query['redirect'])) {
            // Remove flash messages
            $this->getRequest()->getSession()->delete('Flash');
        }
    }
}
