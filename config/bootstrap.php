<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Security;

/*
 * See https://github.com/josegonzalez/php-dotenv for API details.
 *
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.example` to `config/.env` and set/modify the
 * variables as required.
 *
 * The purpose of the .env file is to emulate the presence of the environment
 * variables like they would be present in production.
 *
 * If you use .env files, be careful to not commit them to source control to avoid
 * security risks. See https://github.com/josegonzalez/php-dotenv#general-security-information
 * for more information for recommended practices.
*/
if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
    $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
    $dotenv->parse()
        ->putenv()
        ->toEnv()
        ->toServer();
}

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    // I18n default config (can be overwritten in app.php)
    Configure::write('I18n', [
        'default' => 'en',
        'locales' => ['en_US' => 'en'],
        'languages' => ['en' => 'English'],
        'timezone' => 'UTC',
        'cookie' => [
            'name' => 'BEditaWebI18n',
            'create' => true,
        ],
        'switchLangUrl' => '/lang',
    ]);
    // default pagination
    Configure::write('Pagination', [
        'sizeAvailable' => [10, 20, 50, 100],
    ]);

    Configure::load('app', 'default', false);

    Configure::config('ini', new IniConfig());
    Configure::load('version', 'ini');
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file to provide overrides to your configuration.
 * Notice: For security reasons app_local.php **should not** be included in your git repo.
 */
if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    Configure::write('Cache._schema_types_.duration', '+2 minutes');
    Configure::write('Cache._project_config_.duration', '+2 minutes');
    Configure::write('Cache._thumbs_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
(new ErrorTrap(Configure::read('Error')))->register();
(new ExceptionTrap(Configure::read('Error')))->register();

/*
 * Include the CLI bootstrap overrides.
 */
if (PHP_SAPI === 'cli') {
    require CONFIG . 'bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');
if (!$fullBaseUrl) {
    /*
     * When using proxies or load balancers, SSL/TLS connections might
     * get terminated before reaching the server. If you trust the proxy,
     * you can enable `$trustProxy` to rely on the `X-Forwarded-Proto`
     * header to determine whether to generate URLs using `https`.
     *
     * See also https://book.cakephp.org/4/en/controllers/request-response.html#trusting-proxy-headers
     */
    $trustProxy = false;
    $s = null;
    if (env('HTTPS') || ($trustProxy && env('HTTP_X_FORWARDED_PROTO') === 'https')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        $fullBaseUrl = 'http' . $s . '://' . $httpHost;
    }
    unset($httpHost, $s);
}
if ($fullBaseUrl) {
    Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

Cache::setConfig(Configure::consume('Cache'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Setup detectors for mobile and tablet.
 * If you don't use these checks you can safely remove this code
 * and the mobiledetect package from composer.json.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/**
 * CSRF whitelist
 *
 * Array having `controller` as key and actions array as value.
 */
Configure::write('CsrfExceptions', [
    'Login' => ['login'],
    'PropertyTypes' => ['save'],
    'Modules' => ['save'],
    'Translator' => ['translate'],
]);
