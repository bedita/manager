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

use Authentication\Identity;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Controller\Exception\SecurityException;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Base Application Controller.
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \App\Controller\Component\ConfigComponent $Config
 * @property \App\Controller\Component\FlashComponent $Flash
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
     * @inheritDoc
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

        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => '/login',
        ]);

        $this->loadComponent('Modules', [
            'currentModuleName' => $this->name,
        ]);
        $this->loadComponent('Schema');
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        /** @var \Authentication\Identity|null $identity */
        $identity = $this->Authentication->getIdentity();
        if ($identity && $identity->get('tokens')) {
            $this->apiClient->setupTokens($identity->get('tokens'));
        } elseif (!in_array(rtrim($this->getRequest()->getPath(), '/'), ['/login'])) {
            $route = $this->loginRedirectRoute();
            $this->Flash->error(__('Login required'));

            return $this->redirect($route);
        }
        $this->setupOutputTimezone();
        $this->Security->setConfig('blackHoleCallback', 'blackhole');

        return null;
    }

    /**
     * Handle security blackhole with logs for now
     *
     * @param string $type Exception type
     * @param \Cake\Controller\Exception\SecurityException $exception Raised exception
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException
     * @codeCoverageIgnore
     */
    public function blackhole(string $type, SecurityException $exception): void
    {
        // Log original exception
        $this->log($exception->getMessage(), 'error');

        // Log form data & session id
        $token = (array)$this->getRequest()->getData('_Token');
        unset($token['debug']);
        $this->log('[Blackhole] type: ' . $type, 'debug');
        $this->log('[Blackhole] form token: ' . json_encode($token), 'debug');
        $this->log('[Blackhole] form fields: ' . json_encode(array_keys((array)$this->getRequest()->getData())), 'debug');
        $this->log('[Blackhole] form session id: ' . (string)$this->getRequest()->getData('_session_id'), 'debug');
        $sessionId = $this->getRequest()->getSession()->id();
        $this->log('[Blackhole] current session id: ' . $sessionId, 'debug');

        // Throw a generic bad request exception.
        throw new BadRequestException();
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
        if (!$this->getRequest()->is('get')) {
            return $route;
        }

        // if redirect is app webroot, return route without redirect.
        $redirect = $this->getRequest()->getUri()->getPath();
        if ($redirect === $this->getRequest()->getAttribute('webroot')) {
            return $route;
        }

        return $route + ['?' => compact('redirect')];
    }

    /**
     * Setup output timezone from user session
     *
     * @return void
     */
    protected function setupOutputTimezone(): void
    {
        /** @var \Authentication\Identity|null $identity */
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return;
        }

        $timezone = $identity->get('timezone');
        if (!$timezone) {
            return;
        }

        Configure::write('I18n.timezone', $timezone);
    }

    /**
     * {@inheritDoc}
     *
     * Update session tokens if updated/refreshed by client
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        /** @var \Authentication\Identity|null $user */
        $user = $this->Authentication->getIdentity();
        if ($user) {
            $tokens = $this->apiClient->getTokens();
            if ($tokens && $user->get('tokens') !== $tokens) {
                $data = compact('tokens') + (array)$user->getOriginalData();
                $user = new Identity($data);
                $this->Authentication->setIdentity($user);
            }

            $this->set(compact('user'));
        }

        $path = $this->viewBuilder()->getTemplatePath();
        $this->viewBuilder()->setTemplatePath('Pages/' . $path);

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
        $data = (array)$this->getRequest()->getData();

        $this->specialAttributes($data);
        $this->setupParentsRelation($type, $data);
        $this->prepareRelations($data);
        $this->changedAttributes($data);

        // cleanup attributes on new objects/resources
        if (empty($data['id'])) {
            $data = array_filter($data);
        }

        return $data;
    }

    /**
     * Setup special attributes to be saved.
     *
     * @param array $data Request data
     * @return void
     */
    protected function specialAttributes(array &$data): void
    {
        // remove temporary session id
        unset($data['_session_id']);

        // if password is empty, unset it
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
            unset($data['confirm-password']);
        }

        if (!empty($data['_jsonKeys'])) {
            $keys = explode(',', $data['_jsonKeys']);
            foreach ($keys as $key) {
                $value = Hash::get($data, $key);
                $data = Hash::insert($data, $key, json_decode((string)$value, true));
            }
            unset($data['_jsonKeys']);
        }

        // remove date_ranges items having empty both start & end dates
        if (!empty($data['date_ranges'])) {
            $data['date_ranges'] = array_filter(
                (array)$data['date_ranges'],
                function ($item) {
                    return !empty($item['start_date']) || !empty($item['end_date']);
                }
            );
        }

        // prepare categories
        if (!empty($data['categories'])) {
            $data['categories'] = array_map(function ($category) {
                return ['name' => $category];
            }, $data['categories']);
        }

        // decode json fields
        $types = (array)Hash::get($data, '_types');
        if (!empty($types)) {
            foreach ($types as $field => $type) {
                if ($type === 'json' && is_string($data[$field])) {
                    $data[$field] = json_decode($data[$field], true);
                }
            }
            unset($data['_types']);
        }
    }

    /**
     * Prepare request relation data.
     *
     * @param array $data Request data
     * @return void
     */
    protected function prepareRelations(array &$data): void
    {
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
                    if ($method === 'replaceRelated' || !empty($relatedIds)) {
                        $api[] = compact('method', 'id', 'relation', 'relatedIds');
                    }
                }
            }
            $data['_api'] = $api;
        }
        unset($data['relations']);
    }

    /**
     * Handle `parents` or `parent` relationship looking at `_changedParents` input flag
     *
     * @param string $type Object type
     * @param array $data Form data
     * @return void
     */
    protected function setupParentsRelation(string $type, array &$data): void
    {
        $changedParents = (bool)Hash::get($data, '_changedParents');
        unset($data['_changedParents']);
        $relation = 'parents';
        if ($type === 'folders') {
            $relation = 'parent';
        }
        if (empty($changedParents)) {
            unset($data['relations'][$relation]);

            return;
        }
        if (empty($data['relations'][$relation])) {
            // all parents deselected => replace with empty set
            $data['relations'][$relation] = ['replaceRelated' => []];
        }
    }

    /**
     * Setup changed attributes to be saved.
     * Remove unchanged attributes from $data array.
     *
     * @param array $data Request data
     * @return void
     */
    protected function changedAttributes(array &$data): void
    {
        if (!empty($data['_actualAttributes'])) {
            $attributes = json_decode($data['_actualAttributes'], true);
            if ($attributes === null) {
                $this->log(sprintf('Wrong _actualAttributes, not a json string: %s', $data['_actualAttributes']), 'error');
                $attributes = [];
            }
            foreach ($attributes as $key => $value) {
                // remove unchanged attributes from $data
                if (array_key_exists($key, $data) && !$this->hasFieldChanged($value, $data[$key])) {
                    unset($data[$key]);
                }
            }
            unset($data['_actualAttributes']);
        }
    }

    /**
     * Return true if $value1 equals $value2 or both are empty (null|'')
     *
     * @param mixed $value1 The first value | field value in model data (db)
     * @param mixed $value2 The second value | field value from form
     * @return bool
     */
    protected function hasFieldChanged($value1, $value2): bool
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
        if (is_numeric($value1) && is_string($value2)) {
            return (string)$value1 !== $value2;
        }
        if (is_string($value1) && is_numeric($value2)) {
            return $value1 !== (string)$value2;
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
     * @throws \Cake\Http\Exception\BadRequestException on empty request or empty data by parameter
     */
    protected function checkRequest(array $options = []): array
    {
        // check allowed methods
        if (!empty($options['allowedMethods'])) {
            $this->getRequest()->allowMethod($options['allowedMethods']);
        }

        // check request required parameters, if any
        $data = [];
        if (!empty($options['requiredParameters'])) {
            foreach ($options['requiredParameters'] as $param) {
                $val = $this->getRequest()->getData($param);
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
        $session = $this->getRequest()->getSession();
        $sessionKey = sprintf('%s.filter', $this->Modules->getConfig('currentModuleName'));

        // if reset request, delete session data by key and redirect to proper uri
        if ($this->getRequest()->getQuery('reset') === '1') {
            $session->delete($sessionKey);

            return $this->redirect((string)$this->getRequest()->getUri()->withQuery(''));
        }

        // write request query parameters (if any) in session
        $params = $this->getRequest()->getQueryParams();
        if (!empty($params)) {
            unset($params['_search']);
            $session->write($sessionKey, $params);

            return null;
        }

        // read request query parameters from session and redirect to proper page
        $params = (array)$session->read($sessionKey);
        if (!empty($params)) {
            $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

            return $this->redirect((string)$this->getRequest()->getUri()->withQuery($query));
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
                'prev' => $i > 0 ? Hash::get($objects, sprintf('%d.id', $i - 1)) : null,
                'next' => $i + 1 < $total ? Hash::get($objects, sprintf('%d.id', $i + 1)) : null,
                'index' => $i + 1,
                'total' => $total,
                'object_type' => Hash::get($objects, sprintf('%d.object_type', $i)),
            ];
        }
        $session = $this->getRequest()->getSession();
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
        $session = $this->getRequest()->getSession();
        $objectNav = (array)$session->read('objectNav');
        if (empty($objectNav)) {
            return [];
        }

        // get objectNav by session objectNavModule
        $objectNavModule = (string)$session->read('objectNavModule');

        return (array)Hash::get($objectNav, sprintf('%s.%s', $objectNavModule, $id), []);
    }

    /**
     * Cake 4 compatibility wrapper method: set items to serialize for the view
     *
     * In Cake 3 => $this->set('_serialize', ['data']);
     * In Cake 4 => $this->viewBuilder()->setOption('serialize', ['data'])
     *
     * @param array $items Items to serialize
     * @return void
     * @codeCoverageIgnore
     */
    protected function setSerialize(array $items): void
    {
        $this->viewBuilder()->setOption('serialize', $items);
    }
}
