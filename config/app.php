<?php

use BEdita\WebTools\Error\ExceptionRenderer;
use Cake\Cache\Engine\FileEngine;
use Cake\Log\Engine\FileLog;

return [
    /**
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /**
     * Configure basic information about the application.
     *
     * - namespace - The namespace to find app classes under.
     * - defaultLocale - The default locale for translation, formatting currencies and numbers, date and time.
     * - encoding - The encoding used for HTML + database connections.
     * - base - The base directory the app resides in. If false this
     *   will be auto detected.
     * - dir - Name of app directory.
     * - webroot - The webroot directory.
     * - wwwRoot - The file path to webroot.
     * - baseUrl - To configure CakePHP to *not* use mod_rewrite and to
     *   use CakePHP pretty URLs, remove these .htaccess
     *   files:
     *      /.htaccess
     *      /webroot/.htaccess
     *   And uncomment the baseUrl key below.
     * - fullBaseUrl - A base URL to use for absolute links.
     *   CakePHP generates required value based on `HTTP_HOST` environment variable.
     *   However, you can define it manually to optimize performance or if you
     *   are concerned about people manipulating the `Host` header.
     * - imageBaseUrl - Web path to the public images directory under webroot.
     * - cssBaseUrl - Web path to the public css directory under webroot.
     * - jsBaseUrl - Web path to the public js directory under webroot.
     * - paths - Configure paths for non class based resources. Supports the
     *   `plugins`, `templates`, `locales` subkeys, which allow the definition of
     *   paths for plugins, view templates and locale files respectively.
     */
    'App' => [
        'namespace' => 'App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'UTC'),
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        //'baseUrl' => env('SCRIPT_NAME'),
        'fullBaseUrl' => false,
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS],
            'templates' => [APP . 'Template' . DS],
            'locales' => [APP . 'Locale' . DS],
        ],
    ],

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT'),
    ],

    /*
     * Apply timestamps with the last modified time to static assets (js, css, images).
     * Will append a querystring parameter containing the time the file was modified.
     * This is useful for busting browser caches.
     *
     * Set to true to apply timestamps when debug is true. Set to 'force' to always
     * enable timestamping regardless of debug value.
     */
    'Asset' => [
        //'timestamp' => true,
        // 'cacheTime' => '+1 year'
    ],

    /**
     * Configure the cache adapters.
     */
    'Cache' => [
        'default' => [
            'className' => FileEngine::class,
            'path' => CACHE,
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        /**
         * Configure the cache used for general framework caching.
         * Translation cache files are stored with this configuration.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         * If you set 'className' => 'Null' core cache will be disabled.
         */
        '_cake_core_' => [
            'className' => FileEngine::class,
            'prefix' => 'manager_cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKECORE_URL', null),
        ],

        /**
         * Configure the cache used for schema types caching.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_schema_types_' => [
            'className' => FileEngine::class,
            'prefix' => 'schema_types_',
            'path' => CACHE . 'schema_types/',
            'serialize' => true,
            'duration' => '+1 day',
            'url' => env('CACHE_SCHEMATYPES_URL', null),
        ],

        /**
         * Configure the cache used for project configuration caching.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_project_config_' => [
            'className' => FileEngine::class,
            'prefix' => '_project_config_',
            'path' => CACHE . 'project_config/',
            'serialize' => true,
            'duration' => '+1 day',
            'url' => env('CACHE_PROJECTCONFIG_URL', null),
        ],

        /**
         * Configure the cache for model and datasource caches. This cache
         * configuration is used to store schema descriptions, and table listings
         * in connections.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_cake_model_' => [
            'className' => FileEngine::class,
            'prefix' => 'manager_cake_model_',
            'path' => CACHE . 'models/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEMODEL_URL', null),
        ],

        /**
         * Thumbnails cache
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         * If you set 'className' => 'Null' core cache will be disabled.
         */
        '_thumbs_' => [
            'className' => FileEngine::class,
            'prefix' => 'thumbs_',
            'path' => CACHE . 'thumbs/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_THUMBS_URL', null),
        ],

        /*
         * Configure the cache for routes. The cached routes collection is built the
         * first time the routes are processed through `config/routes.php`.
         * Duration will be set to '+2 seconds' in bootstrap.php when debug = true
         */
        '_cake_routes_' => [
            'className' => FileEngine::class,
            'prefix' => 'manager_cake_routes_',
            'path' => CACHE,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEROUTES_URL', null),
        ],
    ],

    /**
     * Configure the Error and Exception handlers used by your application.
     *
     * By default errors are displayed using Debugger, when debug is true and logged
     * by Cake\Log\Log when debug is false.
     *
     * In CLI environments exceptions will be printed to stderr with a backtrace.
     * In web environments an HTML page will be displayed for the exception.
     * With debug true, framework errors like Missing Controller will be displayed.
     * When debug is false, framework errors will be coerced into generic HTTP errors.
     *
     * Options:
     *
     * - `errorLevel` - int - The level of errors you are interested in capturing.
     * - `trace` - boolean - Whether or not backtraces should be included in
     *   logged errors/exceptions.
     * - `log` - boolean - Whether or not you want exceptions logged.
     * - `exceptionRenderer` - string - The class responsible for rendering
     *   uncaught exceptions. If you choose a custom class you should place
     *   the file for that class in src/Error. This class needs to implement a
     *   render method.
     * - `skipLog` - array - List of exceptions to skip for logging. Exceptions that
     *   extend one of the listed exceptions will also be skipped for logging.
     *   E.g.:
     *   `'skipLog' => ['Cake\Http\Exception\NotFoundException', 'Cake\Http\Exception\UnauthorizedException']`
     * - `extraFatalErrorMemory` - int - The number of megabytes to increase
     *   the memory limit by when a fatal error is encountered. This allows
     *   breathing room to complete logging or error handling.
     * - `ignoredDeprecationPaths` - array - A list of glob compatible file paths that deprecations
     *   should be ignored in. Use this to ignore deprecations for plugins or parts of
     *   your application that still emit deprecations.
     */
    'Error' => [
        'errorLevel' => E_ALL,
        'exceptionRenderer' => ExceptionRenderer::class,
        'skipLog' => [],
        'log' => true,
        'trace' => true,
        'ignoredDeprecationPaths' => [],
    ],

    /*
     * Debugger configuration
     *
     * Define development error values for Cake\Error\Debugger
     *
     * - `editor` Set the editor URL format you want to use.
     *   By default atom, emacs, macvim, phpstorm, sublime, textmate, and vscode are
     *   available. You can add additional editor link formats using
     *   `Debugger::addEditor()` during your application bootstrap.
     * - `outputMask` A mapping of `key` to `replacement` values that
     *   `Debugger` should replace in dumped data and logs generated by `Debugger`.
     */
    'Debugger' => [
        'editor' => 'vscode',
    ],

    /**
     * Configures logging options
     */
    'Log' => [
        'debug' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'debug',
            'url' => env('LOG_DEBUG_URL', null),
            'scopes' => false,
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'error',
            'url' => env('LOG_ERROR_URL', null),
            'scopes' => false,
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
    ],

    /**
     * Session configuration.
     *
     * Contains an array of settings to use for session configuration. The
     * `defaults` key is used to define a default preset to use for sessions, any
     * settings declared here will override the settings of the default config.
     *
     * ## Options
     *
     * - `cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'. Avoid using `.` in cookie names,
     *   as PHP will drop sessions from cookies with `.` in the name.
     * - `cookiePath` - The url path for which session cookie is set. Maps to the
     *   `session.cookie_path` php.ini config. Defaults to base path of app.
     * - `timeout` - The time in minutes the session should be valid for.
     *    Pass 0 to disable checking timeout.
     *    Please note that php.ini's session.gc_maxlifetime must be equal to or greater
     *    than the largest Session['timeout'] in all served websites for it to have the
     *    desired effect.
     * - `defaults` - The default configuration set to use as a basis for your session.
     *    There are four built-in options: php, cake, cache, database.
     * - `handler` - Can be used to enable a custom session handler. Expects an
     *    array with at least the `engine` key, being the name of the Session engine
     *    class to use for managing the session. CakePHP bundles the `CacheSession`
     *    and `DatabaseSession` engines.
     * - `ini` - An associative array of additional ini values to set.
     *
     * The built-in `defaults` options are:
     *
     * - 'php' - Uses settings defined in your php.ini.
     * - 'cake' - Saves session files in CakePHP's /tmp directory.
     * - 'database' - Uses CakePHP's database sessions.
     * - 'cache' - Use the Cache class to save sessions.
     *
     * To define a custom session handler, save it at src/Network/Session/<name>.php.
     * Make sure the class implements PHP's `SessionHandlerInterface` and set
     * Session.handler to <name>
     *
     * To use database sessions, load the SQL file located at config/schema/sessions.sql
     */
    'Session' => [
        'defaults' => 'php',
        'cookie' => 'BEDITA-MANAGER',
    ],

    /**
     * API configuration.
     *
     * Contains an array of setting to use for API client configuration.
     *
     * ## Options
     *
     * - `apiBaseUrl` - The base URL for BEdita API instance.
     * - `apiKey` - The API key to use with BEdita API instance.
     * - `log` - Loggin options, optional log file with `log_file`.
     */
    'API' => [
        'apiBaseUrl' => env('BEDITA_API'),
        'apiKey' => env('BEDITA_API_KEY', null),
        'log' => [
            //'log_file' => LOGS . 'api.log',
        ],
    ],

    /**
     * Modules configuration.
     *
     * See https://github.com/bedita/manager/wiki/Setup:-Modules-configuration
     *
     * Keys must be actual API endpoint names like `documents`, `users` or `folders`.
     * Modules order will follow key order of this configuration.
     * In case of core or plugin modules not directly served by ModulesController
     * (generally modules not related to bject types) a 'route' attribute can be specified for
     * custom controller and action rules.
     *
     * Array value may contain:
     *
     *  'label' - module label to display, if not set `key` will be used
     *  'shortLabel' - short label, 3 character recommended
     *  'color' - primary color code,
     *  'route' - (optional) custom route (named route or absolute/relative URL) used by plugin modules mainly
     *  'secondaryColor' - secondary color code,
     *  'sort' - sort order to be used in index; use a field name prepending optionl `-` sign
     *          to indicate a descendant order, f.i. '-title' will sort by title in reverse alphabetical order
     *          (default is '-id'),
     *  'icon' - icon code, f.i. `icon-article`, have a look in
     *      `webroot/css/be-icons-codes.css` for a complete list of codes
     *  'sidebar' - additional custom sidebar links added in modules index and single item view,
     *     defined as associative array with 'index' and 'view' keys
     */
    'Modules' => [
        'objects' => [
            'shortLabel' => 'obj',
            'color' => '#230637',
            // 'secondaryColor' => '#d95700',
            'sort' => '-modified',
            // 'icon' => 'icon-cube',
        ],
        'folders' => [
            'color' => '#072440',
        ],
        'documents' => [
            'color' => '#cc4700',
        ],
        'events' => [
            'color' => '#09c',
        ],
        'news' => [
            'color' => '#036',
        ],
        'locations' => [
            'color' => '#641',
        ],
        'media' => [
            'color' => '#a80019',
        ],
        'images' => [
            'color' => '#d5002b',
        ],
        'videos' => [
            'color' => '#d5002b',
        ],
        'audio' => [
            'color' => '#d5002b',
        ],
        'files' => [
            'color' => '#d5002b',
        ],
        'users' => [
            'color' => '#000000',
        ],
        'profiles' => [
            'color' => '#093',
        ],
    ],

    /**
     * Export default settings
     *
     * - limit => max number of exported elements on export all
     */
    'Export' => [
        'limit' => 10000,
    ],

    /**
     * External OAuth2 Providers configuration
     */
    'OAuth2Providers' => [
        'google' => [
            // OAuth2 class name
            'class' => '\League\OAuth2\Client\Provider\Google',
            // Provider class setup parameters - should be set in `app_local.php` to activate this provider
            // 'setup' => [
            //     'clientId' => '####',
            //     'clientSecret' => '####',
            // ],
            // Provider authorization options
            'options' => [
                'scope' => ['https://www.googleapis.com/auth/userinfo.email'],
            ],
            // Map BEdita user fields with auth provider data path, using dot notation like 'user.id'
            'map' => [
                'provider_username' => 'email',
            ],
        ],
        'github' => [
            // OAuth2 class name
            'class' => '\League\OAuth2\Client\Provider\Github',
            // Provider class setup parameters - should be set in `app_local.php` to activate this provider
            // 'setup' => [
            //     'clientId' => '####',
            //     'clientSecret' => '####',
            // ],
            // Provider authorization options
            'options' => [
                'scope' => ['read:user', 'user:email'],
            ],
            // Map BEdita user fields with auth provider data path, using dot notation like 'user.id'
            'map' => [
                'provider_username' => 'login',
            ],
        ],
    ],
];
