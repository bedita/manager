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
namespace App;

use App\Identifier\ApiIdentifier;
use App\Middleware\ConfigurationMiddleware;
use App\Middleware\ProjectMiddleware;
use App\Middleware\RecoveryMiddleware;
use App\Middleware\StatusMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use BEdita\I18n\Middleware\I18nMiddleware;
use BEdita\WebTools\Middleware\OAuth2Middleware;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application class.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    /**
     * Default plugin options
     *
     * @var array
     */
    protected const PLUGIN_DEFAULTS = [
        'debugOnly' => false,
        'autoload' => false,
        'bootstrap' => true,
        'routes' => true,
        'ignoreMissing' => true,
    ];

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addOptionalPlugin('DebugKit');
        }

        $this->addPlugin('BEdita/WebTools');
        $this->addPlugin('BEdita/I18n');
        $this->addPlugin('Authentication');
    }

    /**
     * Load CLI plugins
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        $this->addOptionalPlugin('Bake');
        $this->addOptionalPlugin('IdeHelper');
        $this->addOptionalPlugin('Cake/Repl');
        $this->loadPluginsFromConfig();
    }

    /**
     * Load plugins from 'Plugins' configuration.
     * This method will be invoked by `ProjectMiddleware`.
     * Should not be invoked in `bootstrap`, to avoid duplicate plugin bootstratp calls.
     *
     * @return void
     */
    public function loadPluginsFromConfig(): void
    {
        $plugins = (array)Configure::read('Plugins');
        foreach ($plugins as $plugin => $options) {
            $options = array_merge(static::PLUGIN_DEFAULTS, $options);
            if (!$options['debugOnly'] || Configure::read('debug')) {
                $this->addPlugin($plugin, $options);
                $this->plugins->get($plugin)->bootstrap($this);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function middleware($middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Load current project configuration if `multiproject` instance
            // Manager plugins will also be loaded here via `loadPluginsFromConfig()`
            ->add(new ProjectMiddleware($this))

            // Provides a `GET /status` endpoint. This must be
            ->add(new StatusMiddleware())

            // Load configuration from API for the current project.
            ->add(new ConfigurationMiddleware())

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add I18n middleware.
            ->add(new I18nMiddleware([
                'cookie' => Configure::read('I18n.cookie'),
                'switchLangUrl' => Configure::read('I18n.switchLangUrl'),
            ]))

            // Add routing middleware.
            ->add(new RoutingMiddleware($this))

            // Csrf Middleware
            ->add($this->csrfMiddleware())

            // Authentication middleware.
            ->add(new AuthenticationMiddleware($this))

            // Authentication middleware.
            ->add(new OAuth2Middleware())

            // Recovery middleware
            ->add(new RecoveryMiddleware());

        return $middlewareQueue;
    }

    /**
     * Get internal Csrf Middleware
     *
     * @return \Cake\Http\Middleware\CsrfProtectionMiddleware
     */
    protected function csrfMiddleware(): CsrfProtectionMiddleware
    {
        // Csrf Middleware
        $csrf = new CsrfProtectionMiddleware(['httponly' => true]);
        // Token check will be skipped when callback returns `true`.
        $csrf->skipCheckCallback(function ($request) {
            $actions = (array)Configure::read(sprintf('CsrfExceptions.%s', $request->getParam('controller')));
            // Skip token check for API URLs.
            if (in_array($request->getParam('action'), $actions)) {
                return true;
            }
        });

        return $csrf;
    }

    /**
     * Load project configuration if corresponding config file is found
     *
     * @param string|null $project The project name.
     * @param string $projectsPath The project configuration files base path.
     * @return void
     */
    public static function loadProjectConfig(?string $project, string $projectsPath): void
    {
        if (empty($project)) {
            return;
        }

        if (file_exists($projectsPath . $project . '.php')) {
            Configure::config('projects', new PhpConfig($projectsPath));
            Configure::load($project, 'projects');
        }
    }

    /**
     * @inheritDoc
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService([
            'unauthenticatedRedirect' => '/login',
            'queryParam' => 'redirect',
        ]);

        $path = $request->getUri()->getPath();
        $query = $request->getQueryParams();
        if (strpos($path, '/ext/login') === 0) {
            $providers = (array)Configure::read('OAuth2Providers');
            $service->loadIdentifier('BEdita/WebTools.OAuth2', compact('providers'));
            $service->loadAuthenticator('BEdita/WebTools.OAuth2', compact('providers') + [
                'redirect' => ['_name' => 'login:oauth2'],
            ]);

            if (empty($query)) {
                return $service;
            }
        }

        $service->loadAuthenticator('Authentication.Session', [
            'sessionKey' => 'BEditaManagerAuth',
            'fields' => [
                IdentifierInterface::CREDENTIAL_TOKEN => 'token',
            ],
        ]);

        $service->loadIdentifier(ApiIdentifier::class, [
                'timezoneField' => 'timezone',
        ]);

        if ($path === '/login') {
            $service->loadAuthenticator('Authentication.Form', [
                'loginUrl' => '/login',
                'fields' => [
                    IdentifierInterface::CREDENTIAL_USERNAME => 'username',
                    IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
                    'timezone' => 'timezone_offset',
                ],
            ]);
        }

        return $service;
    }
}
