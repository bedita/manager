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
     * {@inheritDoc}
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
        $this->request->allowMethod(['get', 'head', 'post']);

        if (!$this->request->is('post')) {
            // Handle flash messages
            $this->handleFlashMessages($this->request->getQueryParams());
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
            (string)$this->request->getData('project'),
            (string)$this->getConfig('projectsPath')
        );
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            // Setup current project name.
            $this->setupCurrentProject();
            // Redirect.
            $target = $this->Authentication->getLoginRedirect() ?? ['_name' => 'dashboard'];

            return $this->redirect($target);
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
        $project = $this->request->getData('project');
        if (empty($project)) {
            return;
        }
        $this->request->getSession()->write('_project', $project);
    }

    /**
     * Logout and redirect to login page.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->request->allowMethod(['get']);
        $redirect = $this->redirect($this->Authentication->logout());
        $this->request->getSession()->destroy();

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
            $this->request->getSession()->delete('Flash');
        }
    }
}
