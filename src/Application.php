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
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
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

        // Load 'BEdita/WebTools' and other plugins here
        $this->addPlugin('BEdita/WebTools');
        // To activate I18nMiddleware use $this->addPlugin('BEdita/I18n', ['middleware' => true]);
        $this->addPlugin('BEdita/I18n');

        /*
         * Uncomment to setup AssetsRevisions with an appropriate strategy.
         *
         * @see https://github.com/bedita/web-tools#load-assets-with-assetrevisions
         */
        // \BEdita\WebTools\Utility\AssetsRevisions::setStrategy(
        //     new \BEdita\WebTools\Utility\Asset\Strategy\EntrypointsStrategy()
        // );
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
        $this->loadPluginsFromConfig();
    }

    /**
     * Add an optional plugin
     *
     * If it isn't available, ignore it.
     *
     * @param string|\Cake\Core\PluginInterface $name The plugin name or plugin object.
     * @param array $config The configuration data for the plugin if using a string for $name
     * @return $this
     */
    public function addOptionalPlugin($name, array $config = [])
    {
        try {
            $this->addPlugin($name, $config);
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        return $this;
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
            if (!$options['debugOnly'] || ($options['debugOnly'] && Configure::read('debug'))) {
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
     * @return \Cake\Http\Middleware\CsrfProtectionMiddleware
     */
    protected function csrfMiddleware(): CsrfProtectionMiddleware
    {
        // Csrf Middleware
        $csrf = new CsrfProtectionMiddleware(['httponly' => true]);
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
