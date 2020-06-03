<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Base Application Controller.
 *
 * @property \App\Controller\Component\ModulesComponent $Modules
 * @property \App\Controller\Component\SchemaComponent $Schema
 */
class AppController extends Controller
{

    /**
     * BEdita4 API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', ['enableBeforeRedirect' => false]);
        $this->loadComponent('App.Flash', ['clear' => true]);
        $this->loadComponent('Security');

        // API config may not be set in `login` for a multi-project setup
        if (Configure::check('API.apiBaseUrl')) {
            $this->apiClient = ApiClientProvider::getApiClient();
        }

        $this->loadComponent('Auth', [
            'authenticate' => [
                'BEdita/WebTools.Api' => [],
            ],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
        ]);

        $this->Auth->deny();

        $this->loadComponent('Modules', [
            'currentModuleName' => $this->name,
        ]);
        $this->loadComponent('Schema');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): ?Response
    {
        $tokens = $this->Auth->user('tokens');
        if (!empty($tokens)) {
            $this->apiClient->setupTokens($tokens);
        } elseif (!in_array($this->request->getPath(), ['/login'])) {
            $route = $this->loginRedirectRoute();
            $this->Flash->error(__('Login required'));

            return $this->redirect($route);
        }
        $this->setupOutputTimezone();

        return null;
    }

    /**
     * Return route array for login redirect.
     * When request is not a get, return route without redirect.
     * When request uri path equals request attribute webroot (the app 'webroot'), return route without redirect.
     * Return route with redirect, otherwise.
     *
     * @return array
     */
    protected function loginRedirectRoute(): array
    {
        $route = ['_name' => 'login'];

        // if request is not a get, return route without redirect.
        if (!$this->request->is('get')) {
            return $route;
        }

        // if redirect is app webroot, return route without redirect.
        $redirect = $this->request->getUri()->getPath();
        if ($redirect === $this->request->getAttribute('webroot')) {
            return $route;
        }

        return $route + compact('redirect');
    }

    /**
     * Setup output timezone from user session
     *
     * @return void
     */
    protected function setupOutputTimezone(): void
    {
        $timezone = $this->Auth->user('timezone');
        if ($timezone) {
            Configure::write('I18n.timezone', $timezone);
        }
    }

    /**
     * {@inheritDoc}
     *
     * Update session tokens if updated/refreshed by client
     */
    public function beforeRender(Event $event): ?Response
    {
        if ($this->Auth && $this->Auth->user()) {
            $user = $this->Auth->user();
            $tokens = $this->apiClient->getTokens();
            if ($tokens && $user['tokens'] !== $tokens) {
                // Update tokens in session.
                $user['tokens'] = $tokens;
                $this->Auth->setUser($user);
            }

            $this->set(compact('user'));
        }

        $this->viewBuilder()->setTemplatePath('Pages/' . $this->_viewPath());

        return null;
    }

    /**
     * Prepare request, set properly json data.
     *
     * @param string $type Object type
     * @return array request data
     */
    protected function prepareRequest($type): array
    {
        // prepare json fields before saving
        $data = (array)$this->request->getData();

        // when saving users, if password is empty, unset it
        if ($type === 'users' && array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
            unset($data['confirm-password']);
        }

        if (!empty($data['_jsonKeys'])) {
            $keys = explode(',', $data['_jsonKeys']);
            foreach ($keys as $key) {
                $data[$key] = json_decode($data[$key], true);
            }
            unset($data['_jsonKeys']);
        }

        // relations data for view/save - prepare api calls
        if (!empty($data['relations'])) {
            $api = [];
            foreach ($data['relations'] as $relation => $relationData) {
                $id = $data['id'];

                foreach ($relationData as $method => $ids) {
                    if (is_string($ids)) {
                        $relatedIds = json_decode($ids, true);
                    } else {
                        $relatedIds = array_map(
                            function ($id) {
                                return json_decode($id, true);
                            },
                            $ids
                        );
                    }
                    if (!empty($relatedIds)) {
                        $api[] = compact('method', 'id', 'relation', 'relatedIds');
                    }
                }
            }
            $data['_api'] = $api;
        }
        unset($data['relations']);

        // prepare attributes: only modified attributes
        if (!empty($data['_actualAttributes'])) {
            $attributes = json_decode($data['_actualAttributes'], true);
            foreach ($attributes as $key => $value) {
                // remove unchanged attributes from $data
                if (array_key_exists($key, $data) && !$this->hasFieldChanged($value, $data[$key])) {
                    unset($data[$key]);
                }
            }
            unset($data['_actualAttributes']);
        }

        // cleanup attributes on new objects/resources
        if (empty($data['id'])) {
            $data = array_filter($data);
        }

        return $data;
    }

    /**
     * Return true if $value1 equals $value2 or both are empty (null|'')
     *
     * @param mixed $value1 The first value | field value in model data (db)
     * @param mixed $value2 The second value | field value from form
     * @return bool
     */
    protected function hasFieldChanged($value1, $value2)
    {
        if ($value1 === $value2) {
            return false; // not changed
        }
        if (($value1 === null || $value1 === '') && ($value2 === null || $value2 === '')) {
            return false; // not changed
        }
        if (is_bool($value1) && !is_bool($value2)) { // i.e. true / "1"
            return $value1 !== boolval($value2);
        }

        return $value1 !== $value2;
    }

    /**
     * Check request data by options.
     *
     *  - $options['allowedMethods']: check allowed method(s)
     *  - $options['requiredParameters']: check required parameter(s)
     *
     * @param array $options The options for request check(s)
     * @return array The request data for required parameters, if any
     * @throws Cake\Http\Exception\BadRequestException on empty request or empty data by parameter
     */
    protected function checkRequest(array $options = []): array
    {
        // check request
        if (empty($this->request)) {
            throw new BadRequestException('Empty request');
        }

        // check allowed methods
        if (!empty($options['allowedMethods'])) {
            $this->request->allowMethod($options['allowedMethods']);
        }

        // check request required parameters, if any
        $data = [];
        if (!empty($options['requiredParameters'])) {
            foreach ($options['requiredParameters'] as $param) {
                $val = $this->request->getData($param);
                if (empty($val)) {
                    throw new BadRequestException(sprintf('Empty %s', $param));
                }
                $data[$param] = $val;
            }
        }

        return $data;
    }

    /**
     * Apply session filter (if any): if found, redirect properly.
     * Session key: '{$currentModuleName}.filter'
     * Scenarios:
     *
     * Query parameter 'reset=1': remove session key and redirect
     * Query parameters found: write them on session with proper key ({currentModuleName}.filter)
     * Session data for session key: build uri from session data and redirect to new uri.
     *
     * @return \Cake\Http\Response|null
     */
    protected function applySessionFilter(): ?Response
    {
        $session = $this->request->getSession();
        $sessionKey = sprintf('%s.filter', $this->Modules->getConfig('currentModuleName'));

        // if reset request, delete session data by key and redirect to proper uri
        if ($this->request->getQuery('reset') === '1') {
            $session->delete($sessionKey);

            return $this->redirect((string)$this->request->getUri()->withQuery(''));
        }

        // write request query parameters (if any) in session
        if (!empty($this->request->getQueryParams())) {
            $session->write($sessionKey, $this->request->getQueryParams());

            return null;
        }

        // read request query parameters from session and redirect to proper page
        if ($session->check($sessionKey)) {
            $query = http_build_query($session->read($sessionKey), null, '&', PHP_QUERY_RFC3986);

            return $this->redirect((string)$this->request->getUri()->withQuery($query));
        }

        return null;
    }

    /**
     * Set objectNav array and objectNavModule.
     * Objects can be in different modules:
     *
     *  - a document is in "documents" and "objects" index
     *  - an image is in "images" and "media" index
     *  - etc.
     *
     * The session variable objectNavModule stores the last module index visited;
     * this is used then in controller view, to obtain the proper object nav (@see \App\Controller\AppController::getObjectNav)
     *
     * @param array $objects The objects to parse to set prev and next data
     * @return void
     */
    protected function setObjectNav($objects): void
    {
        $moduleName = $this->Modules->getConfig('currentModuleName');
        $total = count(array_keys($objects));
        $objectNav = [];
        foreach ($objects as $i => $object) {
            $objectNav[$moduleName][$object['id']] = [
                'prev' => ($i > 0) ? Hash::get($objects, sprintf('%d.id', $i - 1)) : null,
                'next' => ($i + 1 < $total) ? Hash::get($objects, sprintf('%d.id', $i + 1)) : null,
                'index' => $i + 1,
                'total' => $total,
                'object_type' => Hash::get($objects, sprintf('%d.object_type', $i)),
            ];
        }
        $session = $this->request->getSession();
        $session->write('objectNav', $objectNav);
        $session->write('objectNavModule', $moduleName);
    }

    /**
     * Get objectNav for ID and current module name
     *
     * @param string $id The object ID
     * @return array
     */
    protected function getObjectNav($id): array
    {
        // get objectNav from session
        $session = $this->request->getSession();
        $objectNav = (array)$session->read('objectNav');
        if (empty($objectNav)) {
            return [];
        }

        // get objectNav by session objectNavModule
        $objectNavModule = (string)$session->read('objectNavModule');

        return (array)Hash::get($objectNav, sprintf('%s.%s', $objectNavModule, $id), []);
    }

    /**
     * Load tree data.
     *
     * @return void
     */
    public function treeJson(): void
    {
        $this->request->allowMethod(['get']);
        $query = $this->Modules->prepareQuery($this->request->getQueryParams());
        $root = Hash::get($query, 'root');
        $full = Hash::get($query, 'full');
        unset($query['full']);
        unset($query['root']);
        try {
            if (empty($full)) {
                $key = (empty($root)) ? 'roots' : 'parent';
                $val = (empty($root)) ? '' : $root;
                if (empty($query['filter'])) {
                    $query['filter'] = [];
                }
                $query['filter'][$key] = $val;
            }
            $response = $this->apiClient->getObjects('folders', $query);
        } catch (BEditaClientException $error) {
            $this->log($error, 'error');

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }
}
