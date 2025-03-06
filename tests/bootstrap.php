<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use josegonzalez\Dotenv\Loader;

/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */

// set `APP_NAME` env to avoid config/.env load
putenv('APP_NAME=TESTAPP');

require dirname(__DIR__) . '/vendor/autoload.php';

define('ROOT', dirname(__DIR__));
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

require CORE_PATH . 'config' . DS . 'bootstrap.php';
require CAKE . 'functions.php';

require dirname(__DIR__) . '/config/bootstrap.php';

/**
 * Load test env vars from tests/.env file if not already set by environment
 */
if (!getenv('BEDITA_API') && file_exists(dirname(__DIR__) . '/tests/.env')) {
    $dotenv = new Loader([dirname(__DIR__) . '/tests/.env']);
    $dotenv->parse()
        ->putenv()
        ->toEnv()
        ->toServer();
}

Cache::disable();

if (empty(Configure::read('API'))) {
    Configure::write('API', [
        'apiBaseUrl' => env('BEDITA_API'),
        'apiKey' => env('BEDITA_API_KEY'),
    ]);
}

$_SERVER['PHP_SELF'] = '/';
