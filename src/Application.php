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
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\MiddlewareQueue;
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
    public function middleware($middlewareQueue) : MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(ErrorHandlerMiddleware::class)

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)

            // Add I18n middleware.
            ->add(new I18nMiddleware([
                'cookie' => Configure::read('I18n.cookie'),
                'switchLangUrl' => Configure::read('I18n.switchLangUrl'),
            ]))

            // Add routing middleware.
            ->add(new RoutingMiddleware($this));

        return $middlewareQueue;
    }
}
