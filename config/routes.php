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

use Cake\Core\Configure;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;

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
 */

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

if (Configure::read('Maintenance')) {
    $routes->connect(
        '/*',
        ['controller' => 'CourtesyPage', 'action' => 'index'],
        ['_name' => 'courtesypage'],
    );

    return;
}

$routes->scope('/', function (RouteBuilder $routes): void {

    // Reset & change password
    $routes->connect(
        '/login/reset',
        ['controller' => 'Password', 'action' => 'reset'],
        ['_name' => 'password:reset'],
    );
    $routes->connect(
        '/login/change',
        ['controller' => 'Password', 'action' => 'change'],
        ['_name' => 'password:change'],
    );

    // Login.
    $routes->connect(
        '/login',
        ['controller' => 'Login', 'action' => 'login'],
        ['_name' => 'login'],
    );
    $routes->connect(
        '/logout',
        ['controller' => 'Login', 'action' => 'logout'],
        ['_name' => 'logout'],
    );
    $routes->connect(
        '/ext/login/{provider}',
        ['controller' => 'Login', 'action' => 'login'],
        ['_name' => 'login:oauth2'],
    );

    // Dashboard.
    $routes->connect(
        '/',
        ['controller' => 'Dashboard', 'action' => 'index'],
        ['_name' => 'dashboard'],
    );
    $routes->connect(
        '/dashboard/messages',
        ['controller' => 'Dashboard', 'action' => 'messages'],
        ['_name' => 'dashboard:messages'],
    );
    $routes->connect(
        '/tree',
        ['controller' => 'Tree', 'action' => 'get'],
        ['_name' => 'tree:get'],
    );
    $routes->connect(
        '/tree/node/{id}',
        ['controller' => 'Tree', 'action' => 'node'],
        ['_name' => 'tree:node', 'pass' => ['id']],
    );
    $routes->connect(
        '/tree/parent/{id}',
        ['controller' => 'Tree', 'action' => 'parent'],
        ['_name' => 'tree:parent', 'pass' => ['id']],
    );
    $routes->connect(
        '/tree/parents/{type}/{id}',
        ['controller' => 'Tree', 'action' => 'parents'],
        ['_name' => 'tree:parents', 'pass' => ['type', 'id']],
    );
    // Slug
    $routes->connect(
        '/tree/slug',
        ['controller' => 'Tree', 'action' => 'slug'],
        ['_name' => 'tree:slug'],
    );

    // Admin.
    $routes->prefix('admin', ['_namePrefix' => 'admin:'], function (RouteBuilder $routes): void {
        $adminRoutes = [
            'appearance',
            'applications',
            'auth_providers',
            'async_jobs',
            'config',
            'endpoints',
            'external_auth',
            'roles',
            'roles_modules',
            'endpoint_permissions',
            'objects_history',
            'user_accesses',
            'system_info',
            'statistics',
        ];

        foreach ($adminRoutes as $controller) {
            // Routes connected here are prefixed with '/admin'
            $name = Inflector::camelize($controller);
            $routes->get(
                "/$controller",
                ['controller' => $name, 'action' => 'index'],
                'list:' . $controller,
            );

            $routes->get(
                "/$controller/view/{id}",
                ['controller' => $name, 'action' => 'view'],
                'view:' . $controller,
            )->setPass(['id']);

            $routes->post(
                "/$controller/save",
                ['controller' => $name, 'action' => 'save'],
                'save:' . $controller,
            );

            $routes->post(
                "/$controller/remove/{id}",
                ['controller' => $name, 'action' => 'remove'],
                'remove:' . $controller,
            )->setPass(['id']);
        }
        $routes->get(
            '/cache',
            ['controller' => 'Cache', 'action' => 'clear'],
            'cache:clear',
        );
    });

    // Profile.
    $routes->connect(
        '/user_profile',
        ['controller' => 'UserProfile', 'action' => 'view'],
        ['_name' => 'user_profile:view'],
    );
    $routes->connect(
        '/user_profile/save',
        ['controller' => 'UserProfile', 'action' => 'save'],
        ['_name' => 'user_profile:save'],
    );

    // Model.
    $routes->prefix('model', ['_namePrefix' => 'model:'], function (RouteBuilder $routes): void {

        foreach (['object_types', 'property_types', 'relations', 'categories', 'tags'] as $controller) {
            // Routes connected here are prefixed with '/model'
            $name = Inflector::camelize($controller);
            $routes->get(
                "/$controller",
                ['controller' => $name, 'action' => 'index'],
                'list:' . $controller,
            );

            $routes->get(
                "/$controller/view",
                ['controller' => $name, 'action' => 'create'],
                'create:' . $controller,
            );

            $routes->get(
                "/$controller/view/{id}",
                ['controller' => $name, 'action' => 'view'],
                'view:' . $controller,
            )->setPass(['id']);

            $routes->post(
                "/$controller/save",
                ['controller' => $name, 'action' => 'save'],
                'save:' . $controller,
            );

            $routes->post(
                "/$controller/remove/{id}",
                ['controller' => $name, 'action' => 'remove'],
                'remove:' . $controller,
            )->setPass(['id']);
        }

        // export project model
        $routes->get(
            '/export',
            ['controller' => 'Export', 'action' => 'model'],
            'export',
        );
    });

    // Import.
    $routes->connect(
        '/import',
        ['controller' => 'Import', 'action' => 'index'],
        ['_name' => 'import:index'],
    );
    $routes->connect(
        '/import/file',
        ['controller' => 'Import', 'action' => 'file', '_method' => 'POST'],
        ['_name' => 'import:file'],
    );
    $routes->connect(
        '/import/jobs',
        ['controller' => 'Import', 'action' => 'jobs'],
        ['_name' => 'import:jobs'],
    );

    // Trash.
    $routes->connect(
        '/trash',
        ['controller' => 'Trash', 'action' => 'index', '_method' => 'GET'],
        ['_name' => 'trash:list'],
    );
    $routes->connect(
        '/trash/view/{id}',
        ['controller' => 'Trash', 'action' => 'view'],
        ['pass' => ['id'], '_name' => 'trash:view'],
    );
    $routes->connect(
        '/trash/restore',
        ['controller' => 'Trash', 'action' => 'restore'],
        ['_name' => 'trash:restore'],
    );
    $routes->connect(
        '/trash/delete',
        ['controller' => 'Trash', 'action' => 'delete'],
        ['_name' => 'trash:delete'],
    );
    $routes->connect(
        '/trash/empty',
        ['controller' => 'Trash', 'action' => 'emptyTrash'],
        ['_name' => 'trash:empty'],
    );

    // tags
    $routes->connect(
        '/tags',
        ['controller' => 'Tags', 'action' => 'index'],
        ['_name' => 'tags:index'],
    );
    $routes->connect(
        '/tags/delete/{id}',
        ['controller' => 'Tags', 'action' => 'delete'],
        ['pass' => ['id'], '_name' => 'tags:delete'],
    );
    $routes->connect(
        '/tags/create',
        ['controller' => 'Tags', 'action' => 'create'],
        ['_name' => 'tags:create'],
    );
    $routes->connect(
        '/tags/patch/{id}',
        ['controller' => 'Tags', 'action' => 'patch'],
        ['pass' => ['id'], '_name' => 'tags:patch'],
    );
    $routes->connect(
        '/tags/search',
        ['controller' => 'Tags', 'action' => 'search'],
        ['_name' => 'tags:search'],
    );

    // view resource by id / uname
    $routes->connect(
        '/view/{id}',
        ['controller' => 'Modules', 'action' => 'uname'],
        ['pass' => ['id'], '_name' => 'modules:uname'],
    );

    // API proxy
    $routes->scope('/api', ['_namePrefix' => 'api:'], function (RouteBuilder $routes): void {
        $routes->get('/**', ['controller' => 'Api', 'action' => 'get'], 'get');
        $routes->post('/**', ['controller' => 'Api', 'action' => 'post'], 'post');
        $routes->patch('/**', ['controller' => 'Api', 'action' => 'patch'], 'patch');
        $routes->delete('/**', ['controller' => 'Api', 'action' => 'delete'], 'delete');
    });

    // Modules.
    $routes->connect(
        '/{object_type}',
        ['controller' => 'Modules', 'action' => 'index'],
        ['_name' => 'modules:list'],
    );
    $routes->connect(
        '/{object_type}/view',
        ['controller' => 'Modules', 'action' => 'create'],
        ['_name' => 'modules:create'],
    );
    $routes->connect(
        '/{object_type}/setup',
        ['controller' => 'Modules', 'action' => 'setup'],
        ['_name' => 'modules:setup'],
    );
    $routes->connect(
        '/{object_type}/multiupload',
        ['controller' => 'Multiupload', 'action' => 'index'],
        ['_name' => 'modules:multiupload'],
    );
    $routes->connect(
        '/{object_type}/categories',
        ['controller' => 'Categories', 'action' => 'index'],
        ['_name' => 'modules:categories:index'],
    );
    $routes->connect(
        '/{object_type}/categories/save',
        ['controller' => 'Categories', 'action' => 'save'],
        ['_name' => 'modules:categories:save'],
    );
    $routes->connect(
        '/{object_type}/categories/remove/{id}',
        ['controller' => 'Categories', 'action' => 'delete'],
        ['_name' => 'modules:categories:remove', 'pass' => ['id']],
    );

    $routes->connect(
        '/{object_type}/view/{id}',
        ['controller' => 'Modules', 'action' => 'view'],
        ['pass' => ['id'], '_name' => 'modules:view'],
    );
    $routes->connect(
        '/{object_type}/view/{id}/history/{historyId}',
        ['controller' => 'History', 'action' => 'restore'],
        ['pass' => ['id', 'historyId'], '_name' => 'history:restore'],
    );
    // Translations
    $routes->connect(
        '/translations',
        ['controller' => 'Translations', 'action' => 'index'],
        ['_name' => 'translations:list'],
    );
    $routes->connect(
        '/{object_type}/translation/save',
        ['controller' => 'Translations', 'action' => 'save'],
        ['_name' => 'translations:save'],
    );
    $routes->connect(
        '/{object_type}/translation/delete',
        ['controller' => 'Translations', 'action' => 'delete'],
        ['_name' => 'translations:delete'],
    );
    $routes->connect(
        '/{object_type}/translation/{id}/add',
        ['controller' => 'Translations', 'action' => 'add'],
        ['pass' => ['id'], '_name' => 'translations:add'],
    );
    $routes->connect(
        '/{object_type}/translation/{id}/{lang}',
        ['controller' => 'Translations', 'action' => 'edit'],
        ['pass' => ['id', 'lang'], '_name' => 'translations:edit'],
    );
    // Relations ...
    $routes->connect(
        '/{object_type}/view/{id}/related/{relation}',
        ['controller' => 'Modules', 'action' => 'related'],
        ['pass' => ['id', 'relation'], '_name' => 'modules:related'],
    );
    $routes->connect(
        '/{object_type}/view/{id}/relationships/{relation}',
        ['controller' => 'Modules', 'action' => 'relationships'],
        ['pass' => ['id', 'relation'], '_name' => 'modules:relationships'],
    );
    $routes->connect(
        '/{object_type}/view/{id}/resources/{relation}',
        ['controller' => 'Modules', 'action' => 'resources'],
        ['pass' => ['id', 'relation'], '_name' => 'modules:resources'],
    );
    $routes->connect(
        '/{object_type}/view/{id}/relationData/{relation}',
        ['controller' => 'Modules', 'action' => 'relationData'],
        ['pass' => ['id', 'relation'], '_name' => 'modules:relationData'],
    );
    $routes->connect(
        '/{object_type}/save',
        ['controller' => 'Modules', 'action' => 'save'],
        ['_name' => 'modules:save'],
    );
    $routes->connect(
        '/{object_type}/clone/{id}',
        ['controller' => 'Modules', 'action' => 'clone'],
        ['pass' => ['id'], '_name' => 'modules:clone'],
    );
    $routes->connect(
        '/{object_type}/clone/{id}/history/{historyId}',
        ['controller' => 'History', 'action' => 'clone'],
        ['pass' => ['id', 'historyId'], '_name' => 'history:clone'],
    );
    $routes->connect(
        '/{object_type}/history/{id}/{page}',
        ['controller' => 'History', 'action' => 'info'],
        ['pass' => ['id', 'page'], '_name' => 'history:info'],
    );
    $routes->connect(
        '/{object_type}/delete',
        ['controller' => 'Modules', 'action' => 'delete'],
        ['_name' => 'modules:delete'],
    );

    // Export
    $routes->connect(
        '/{object_type}/export',
        ['controller' => 'Export', 'action' => 'export'],
        ['_name' => 'export:export'],
    );
    $routes->connect(
        '/{object_type}/export/{id}/{relation}/{format}',
        ['controller' => 'Export', 'action' => 'related'],
        ['pass' => ['id', 'relation', 'format'], '_name' => 'export:related'],
    );
    $routes->connect(
        '/{object_type}/exportFiltered/{id}/{relation}/{format}/{query}',
        ['controller' => 'Export', 'action' => 'relatedFiltered'],
        ['pass' => ['id', 'relation', 'format', 'query'], '_name' => 'export:related:filtered'],
    );

    $routes->get(
        '/history/get',
        ['controller' => 'History', 'action' => 'get'],
        'history:get',
    );
    $routes->get(
        '/history/objects',
        ['controller' => 'History', 'action' => 'objects'],
        'history:objects',
    );

    $routes->get(
        '/users/list',
        ['controller' => 'Modules', 'action' => 'users'],
        'users:list',
    );
    $routes->connect(
        '/resources/get/{id}',
        ['controller' => 'Modules', 'action' => 'get'],
        ['pass' => ['id'], '_name' => 'resource:get'],
    );
    $routes->get(
        '/roles/list',
        ['controller' => 'Roles', 'action' => 'list'],
        'roles:list',
    );

    // Download stream
    $routes->get(
        '/download/{id}',
        ['controller' => 'Download', 'action' => 'download'],
        'stream:download',
    )
    ->setPass(['id']);

    // Bulk actions
    $routes->connect(
        '/{object_type}/bulkAttribute',
        ['controller' => 'Bulk', 'action' => 'attribute'],
        ['_name' => 'modules:bulkAttribute'],
    )->setMethods(['post']);

    $routes->connect(
        '/{object_type}/bulkCategories',
        ['controller' => 'Bulk', 'action' => 'categories'],
        ['_name' => 'modules:bulkCategories'],
    )->setMethods(['post']);

    $routes->connect(
        '/{object_type}/bulkPosition',
        ['controller' => 'Bulk', 'action' => 'position'],
        ['_name' => 'modules:bulkPosition'],
    )->setMethods(['post']);

    $routes->connect(
        '/{object_type}/bulkCustom',
        ['controller' => 'Bulk', 'action' => 'custom'],
        ['_name' => 'modules:bulkCustom'],
    )->setMethods(['post']);

    // translator service
    $routes->connect(
        '/translate',
        ['controller' => 'Translator', 'action' => 'translate'],
        ['_name' => 'translator:translate'],
    );

    // lock and unlock objects
    $routes->connect(
        '/{object_type}/{id}/lock',
        ['controller' => 'Lock', 'action' => 'add'],
        ['_name' => 'lock:add'],
    );
    $routes->connect(
        '/{object_type}/{id}/unlock',
        ['controller' => 'Lock', 'action' => 'remove'],
        ['_name' => 'lock:remove'],
    );

    // Session
    $routes->get(
        '/session/{name}',
        ['controller' => 'Session', 'action' => 'view'],
        'session:view',
    )->setPass(['name']);
    $routes->connect(
        '/session',
        ['controller' => 'Session', 'action' => 'save'],
        ['_name' => 'session:save'],
    )->setMethods(['POST', 'PATCH']);
    $routes->delete(
        '/session/{name}',
        ['controller' => 'Session', 'action' => 'delete'],
        'session:delete',
    )->setPass(['name']);
});
