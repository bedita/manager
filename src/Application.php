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
namespace App;

use App\Middleware\ProjectMiddleware;
use BEdita\I18n\Middleware\I18nMiddleware;
use BEdita\WebTools\BaseApplication;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application class.
 */
class Application extends BaseApplication
{
    /**
     * Default plugin options
     *
     * @var array
     */
    const PLUGIN_DEFAULTS = [
        'debugOnly' => false,
        'autoload' => false,
        'bootstrap' => true,
        'routes' => true,
        'ignoreMissing' => true,
    ];

    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin('BEdita/WebTools', ['bootstrap' => true]);
    }

    /**
     * @return void
     */
    protected function bootstrapCli(): void
    {
        parent::bootstrapCli();
        $this->addPluginDev('IdeHelper');
        $this->addPlugin('BEdita/I18n');
        $this->loadPluginsFromConfig();
    }

    /**
     * Add plugin considered as a dev dependency.
     * It could be missing in production env.
     *
     * @param string $name The plugin name
     * @return bool
     */
    public function addPluginDev(string $name): bool
    {
        try {
            $this->addPlugin($name);
        } catch (\Exception $e) {
            return false;
        }

        return true;
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
            $options = array_merge(self::PLUGIN_DEFAULTS, $options);
            if (!$options['debugOnly'] || ($options['debugOnly'] && Configure::read('debug'))) {
                $this->addPlugin($plugin, $options);
                $this->plugins->get($plugin)->bootstrap($this);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function middleware($middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(null, Configure::read('Error')))

            // Load current project configuration if `multiproject` instance
            // Manager plugins will also be loaded here via `loadPluginsFromConfig()`
            ->add(new ProjectMiddleware($this))

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
            ->add($this->csrfMiddleware());

        return $middlewareQueue;
    }

    /**
     * Get internal Csrf Middleware
     *
     * @return CsrfProtectionMiddleware
     */
    protected function csrfMiddleware(): CsrfProtectionMiddleware
    {
        // Csrf Middleware
        $csrf = new CsrfProtectionMiddleware();
        // Token check will be skipped when callback returns `true`.
        $csrf->whitelistCallback(function ($request) {
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
}
