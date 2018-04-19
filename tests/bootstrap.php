<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */

/**
 * Load test env vars from tests/.env file if not already set by environment
 */
if (!getenv('BEDITA_API') && file_exists(dirname(__DIR__) . '/tests/.env')) {
    $dotenv = new \josegonzalez\Dotenv\Loader([dirname(__DIR__) . '/tests/.env']);
    $dotenv->parse()
        ->putenv()
        ->toEnv()
        ->toServer();
}

// set `APP_NAME` env to avoid config/.env load
putenv('APP_NAME=TESTAPP');

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

\Cake\Cache\Cache::disable();

$_SERVER['PHP_SELF'] = '/';

// make sure env vars are used for API client setup
\Cake\Core\Configure::write('API', [
    'apiBaseUrl' => env('BEDITA_API'),
    'apiKey' => env('BEDITA_API_KEY', null),
    ]);
