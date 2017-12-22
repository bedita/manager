<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {

    // Dashboard endpoint
    $routes->connect(
        '/',
        ['controller' => 'Dashboard', 'action' => 'display', 'method' => 'GET']
    );

    // GET => list items
    $routes->connect(
        '/:object_type',
        ['controller' => 'Modules', 'action' => 'index', 'method' => 'GET']
    );

    // GET => view item
    $routes->connect(
        '/:object_type/:id',
        ['controller' => 'Modules', 'action' => 'view', 'method' => 'GET'],
        ['pass' => ['id']]
    );

    // POST => create new item
    $routes->connect(
        '/:object_type',
        ['controller' => 'Modules', 'action' => 'create', 'method' => 'POST']
    );

    // PATCH => edit item
    $routes->connect(
        '/:object_type/:id',
        ['controller' => 'Modules', 'action' => 'edit', 'method' => 'PATCH'],
        ['pass' => ['id']]
    );

    // DELETE => remove item
    $routes->connect(
        '/:object_type/:id',
        ['controller' => 'Modules', 'action' => 'delete', 'method' => 'DELETE'],
        ['pass' => ['id']]
    );

    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
