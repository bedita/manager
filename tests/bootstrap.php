<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */

use Cake\Core\Configure;
use Cake\I18n\I18n;

// set `APP_NAME` env to avoid config/.env load
putenv('APP_NAME=TESTAPP');

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

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

\Cake\Cache\Cache::disable();

if (empty(\Cake\Core\Configure::read('API'))) {
    \Cake\Core\Configure::write('API', [
        'apiBaseUrl' => env('BEDITA_API'),
        'apiKey' => env('BEDITA_API_KEY'),
    ]);
}

$_SERVER['PHP_SELF'] = '/';

// ensure default locale is set to English
Configure::write('I18n.default', 'en');
Configure::write('I18n.lang', 'en');
I18n::setLocale('en_US');
