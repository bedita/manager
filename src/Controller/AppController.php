<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use App\Model\API\BEditaClient;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Base Application Controller
 *
 */
class AppController extends Controller
{

    /**
     * BEdita4 API client
     *
     * @var \App\Model\API\BEditaClient
     */
    protected $apiClient = null;

    /**
     * Available user modules
     *
     * @var array
     */
    protected $modules = null;

    /**
     * Project & versions info
     *
     * @var array
     */
    protected $project = null;

    /**
     * Tokens used in this call
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');

        // use JWT auth tokens if stored in session
        $session = $this->request->session();
        $this->tokens = (array)$session->read('tokens');
        $opts = Configure::read('API');
        $this->apiClient = new BEditaClient($opts['apiBaseUrl'], $opts['apiKey'], $this->tokens);

        $user = $session->read('user');
        if (empty($user) && $this->name !== 'Login') {
            return $this->redirect(['_name' => 'login']);
        }
        $this->set('user', $user);
        $this->set('baseUrl', Router::url('/'));
        $this->modules = (array)$session->read('modules');
        $this->set('modules', $this->modules);
        $this->project = (array)$session->read('project');
        $this->set('project', $this->project);
    }

    /**
     * {@inheritDoc}
     *
     * Update session tokens if updated/refreshed by client
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        if (!empty(array_diff_assoc($this->tokens, $this->apiClient->getTokens()))) {
            $this->tokens = $this->apiClient->getTokens();
            $this->request->session()->write('tokens', $this->tokens);
        }
    }

    /**
     * Read modules and project info from `/home' endpoint.
     * Save data in session and view.
     *
     * @return void
     */
    protected function readModules()
    {
        $home = $this->apiClient->get('/home');
        if (empty($home['meta']['resources'])) {
            throw new NotFoundException(__('Modules not found'));
        }
        $this->modules = [];
        // TODO: add info on /home response like "object_type": "true"
        $exclude = ['auth', 'admin', 'model', 'roles', 'signup', 'status', 'trash'];
        foreach ($home['meta']['resources'] as $endpoint => $value) {
            $name = \substr($endpoint, 1);
            if (!in_array($name, $exclude)) {
                $this->modules[] = array_merge(compact('name'), $value);
            }
        }
        $this->set('modules', $this->modules);
        $this->request->session()->write('modules', $this->modules);

        // this should not be in a user session....
        $this->project = [
            'name' => $home['meta']['project']['name'],
            'version' => $home['meta']['version'],
            'colophon' => '', // left empty for now
        ];
        $this->set('project', $this->project);
        $this->request->session()->write('project', $this->project);
    }
}
