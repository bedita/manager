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

use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Hash;
use Locale;

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
    public function initialize() : void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Security');
        $this->loadComponent('Csrf');

        $options = ['Log' => (array)Configure::read('API.log', [])];
        $this->apiClient = ApiClientProvider::getApiClient($options);

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
    public function beforeFilter(Event $event) : void
    {
        parent::beforeFilter($event);

        $tokens = $this->Auth->user('tokens');
        if ($tokens) {
            $this->apiClient->setupTokens($tokens);
        }
        $this->setupOutputTimezone();
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
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);

        if ($this->Auth && $this->Auth->user()) {
            $user = $this->Auth->user();
            // set tokens
            $tokens = $this->apiClient->getTokens();
            if ($tokens && $user['tokens'] !== $tokens) {
                // Update tokens in session.
                $user['tokens'] = $tokens;
                $this->Auth->setUser($user);
            }

            $this->set(compact('user'));
        }

        $this->viewBuilder()->setTemplatePath('Pages/' . $this->name);

        $this->setupLocale();
    }

    /**
     * Prepare request, set properly json data.
     *
     * @param string $type Object type
     * @return array request data
     */
    protected function prepareRequest($type) : array
    {
        // prepare json fields before saving
        $data = $this->request->getData();

        // when saving users, if password is empty, unset it
        if ($type === 'users' && array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
            unset($data['confirm-password']);
        }

        if (!empty($data['_jsonKeys'])) {
            $keys = explode(',', $data['_jsonKeys']);
            foreach ($keys as $key) {
                $data[$key] = json_decode($data[$key]);
            }
            unset($data['_jsonKeys']);
        }

        // relations data for view/save - prepare api calls
        if (!empty($data['relations'])) {
            $api = [];
            foreach ($data['relations'] as $relation => $relationData) {
                $id = $data['id'];

                foreach ($relationData as $method => $ids) {
                    $relatedIds = json_decode($ids, true);
                    if (!empty($relatedIds)) {
                        $api[] = compact('method', 'id', 'relation', 'relatedIds');
                    }
                }
            }

            $data['api'] = $api;
        }

        return $data;
    }

    /**
     * Check request data by options.
     *
     *  - $options['allowedMethods']: check allowed method(s)
     *  - $options['requiredParameters']: check required parameter(s)
     *
     * @param array $options The options for request check(s)
     * @return array The request data for required parameters, if any
     * @throws Cake\Network\Exception\BadRequestException on empty request or empty data by parameter
     */
    protected function checkRequest(array $options = []) : array
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
     * Setup current locale
     *
     * @return void
     */
    protected function setupLocale() : void
    {
        // detect user session lang; if none, use default from config 'I18n.default'
        $user = $this->Auth->user();
        $sessionLang = $user['sessionLang'];
        if (empty($sessionLang) || !Configure::read(sprintf('I18n.languages.%s', $sessionLang))) {
            $sessionLang = Configure::read('I18n.default');
        }
        Configure::write('I18n.lang', $sessionLang);

        // if detected locale matches language code let's use it, if not use a primary lang locale
        $locale = Locale::acceptFromHttp($this->request->getHeaderLine('Accept-Language'));
        if ($sessionLang !== Configure::read(sprintf('I18n.locales.%s', $locale))) {
            $locales = array_flip((array)Configure::read('I18n.locales'));
            $locale = Hash::get($locales, $sessionLang);
        }
        I18n::setLocale($locale);
    }
}
