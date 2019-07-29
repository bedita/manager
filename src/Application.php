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
namespace App;

use BEdita\I18n\Middleware\I18nMiddleware;
use BEdita\WebTools\BaseApplication;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
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
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        parent::bootstrap();
        $this->addPlugin('BEdita/WebTools', ['bootstrap' => true]);
        $this->loadFromConfig();
    }

    /**
     * @return void
     */
    protected function bootstrapCli()
    {
        parent::bootstrapCli();
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }
        $this->addPlugin('BEdita/I18n');
    }

    /**
     * Load plugins from 'Plugins' configuration
     *
     * @return void
     */
    public function loadFromConfig() : void
    {
        $plugins = Configure::read('Plugins');
        if ($plugins) {
            $_defaults = [
                'debugOnly' => false,
                'autoload' => false,
                'bootstrap' => false,
                'routes' => false,
                'ignoreMissing' => false
            ];
            foreach ($plugins as $plugin => $options) {
                $options = array_merge($_defaults, $options);
                if (!$options['debugOnly'] || ($options['debugOnly'] && Configure::read('debug'))) {
                    $this->addPlugin($plugin, $options);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function middleware($middlewareQueue) : MiddlewareQueue
    {
        $middlewareQueue
                    // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(null, Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime')
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
    protected function csrfMiddleware() : CsrfProtectionMiddleware
    {
        // Csrf Middleware
        $csrf = new CsrfProtectionMiddleware();
            // Token check will be skipped when callback returns `true`.
        $csrf->whitelistCallback(function ($request) {
            $actions = (array)Configure::read(sprintf('CrsfExceptions.%s', $request->getParam('controller')));
            // Skip token check for API URLs.
            if (in_array($request->getParam('action'), $actions)) {
                return true;
            }
        });

        return $csrf;
    }
}
