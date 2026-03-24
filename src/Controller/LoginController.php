<?php
declare(strict_types=1);

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

use App\Application;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Throwable;

/**
 * Perform basic login and logout operations.
 */
class LoginController extends AppController
{
    use InstanceConfigTrait;

    /**
     * @inheritDoc
     */
    protected array $_defaultConfig = [
        'otp' => null,
        'projectsPath' => CONFIG . 'projects' . DS,
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
        $otpConfig = (array)Configure::read('Otp');
        if (!empty($otpConfig)) {
            $this->setConfig('otp', $otpConfig);
        }
    }

    /**
     * Display login page or perform login via API.
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        // Add `head` to avoid errors on http `HEAD /` calls since they are redirected to `HEAD /login`
        $this->getRequest()->allowMethod(['get', 'head', 'post']);

        $provider = $this->request->getParam('provider');
        if (!$this->request->is('post') && empty($provider)) {
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
            (string)$this->getConfig('projectsPath'),
        );

        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            // Setup current project name.
            $this->setupCurrentProject();

            $provider = $this->request->getParam('provider');
            $username = $result->getData()['attributes']['username'];

            if (empty($provider) && $this->otpEnabled($username)) {
                return $this->redirect('/otp');
            }

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
        $project = $this->getRequest()->getData('project');
        if (empty($project)) {
            return;
        }
        $this->getRequest()->getSession()->write('_project', $project);
    }

    /**
     * Logout and redirect to login page.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $redirect = $this->redirect($this->Authentication->logout());
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

    /**
     * Otp verification page.
     *
     * @return \Cake\Http\Response|null
     */
    public function otp(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        if (!$this->otpEnabled()) {
            return $this->redirect('/login');
        }
        $otpSession = (array)$this->getRequest()->getSession()->read('Otp');
        $pending = (string)Hash::get($otpSession, 'pending');
        $force = $this->getRequest()->getQuery('force', false);
        if (!$pending || $force) {
            // Send OTP code via API
            $response = $this->apiClient->post(
                $this->getConfig('otp.send'),
                null,
                ['Content-Type' => 'application/json'],
            );
            $this->getRequest()->getSession()->write('Otp', [
                'otp_code' => $response['data']['otp_code'],
                'expires_at' => $response['data']['expires_at'],
                'pending' => true,
            ]);
        }

        return null;
    }

    /**
     * Otp verification request.
     *
     * @return \Cake\Http\Response|null
     */
    public function otpVerify(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        if (!$this->otpEnabled()) {
            return $this->redirect('/login');
        }
        $requestOtpCode = (string)$this->getRequest()->getData('otp_code');
        $sessionOtpData = $this->getRequest()->getSession()->read('Otp');
        $sessionOtpCode = (string)Hash::get($sessionOtpData, 'otp_code');
        $sessionExpiresAt = (string)Hash::get($sessionOtpData, 'expires_at');
        if (empty($sessionOtpData) || $sessionOtpCode !== $requestOtpCode || strtotime($sessionExpiresAt) < time()) {
            $reason = __('OTP code is expired or invalid');
            $this->getRequest()->getSession()->delete('Otp');
            $this->Flash->error(__($reason));

            return $this->redirect('/otp');
        }
        $this->getRequest()->getSession()->delete('Otp');

        return $this->redirect($this->Authentication->getLoginRedirect() ?? ['_name' => 'dashboard']);
    }

    /**
     * Check otp is enabled
     *
     * @return bool
     */
    protected function otpEnabled(?string $username = null): bool
    {
        $otpConfig = (array)$this->getConfig('otp');
        $usersSkipOtp = (array)Hash::get($otpConfig, 'users_skip_otp', []);
        $skip = $username !== null && in_array($username, $usersSkipOtp);

        return !empty($otpConfig) && !$skip;
    }
}
