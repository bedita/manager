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

    // Login.
    $routes->connect(
        '/login',
        ['controller' => 'Login', 'action' => 'login'],
        ['_name' => 'login']
    );

    // Logout.
    $routes->connect(
        '/logout',
        ['controller' => 'Login', 'action' => 'logout'],
        ['_name' => 'logout']
    );

    // Dashboard
    $routes->connect(
        '/',
        ['controller' => 'Dashboard', 'action' => 'index'],
        ['_name' => 'dashboard']
    );

    // Trash | GET => list items
    $routes->connect(
        '/trash',
        ['controller' => 'Trash', 'action' => 'index', 'method' => 'GET'],
        ['_name' => 'trash']
    );

    // Trash | GET => view item
    $routes->connect(
        '/trash/view/:id',
        ['controller' => 'Trash', 'action' => 'view', 'method' => 'GET'],
        ['pass' => ['id']]
    );

    // Trash | POST => restore item
    $routes->connect(
        '/trash/restore',
        ['controller' => 'Trash', 'action' => 'restore', 'method' => 'POST']
    );

    // Trash | POST => delete item
    $routes->connect(
        '/trash/delete',
        ['controller' => 'Trash', 'action' => 'delete', 'method' => 'POST']
    );

    // GET => list items
    $routes->connect(
        '/:object_type',
        ['controller' => 'Modules', 'action' => 'index']
    );

    // GET => view single item
    $routes->connect(
        '/:object_type/view/:id',
        ['controller' => 'Modules', 'action' => 'view'],
        ['pass' => ['id']]
    );

    // GET => display new single item form
    $routes->connect(
        '/:object_type/new',
        ['controller' => 'Modules', 'action' => 'new', 'method' => 'GET']
    );

    // POST => create new item or edit existing
    $routes->connect(
        '/:object_type/save',
        ['controller' => 'Modules', 'action' => 'save', 'method' => 'POST']
    );

    // DELETE => remove item
    $routes->connect(
        '/:object_type/delete',
        ['controller' => 'Modules', 'action' => 'delete', 'method' => 'POST']
    );

    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
