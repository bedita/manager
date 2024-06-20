<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    // 'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /**
     * Modules accesses per role(s)
     */
    // 'AccessControl' => [
    //     'manager' => [
    //         'hidden' => ['objects'],
    //         'readonly' => ['documents'],
    //     ],
    //     'guest' => [
    //         'hidden' => ['objects', 'users'],
    //         'readonly' => ['documents'],
    //     ],
    // ],

    /**
     * Display an alert message in a top bar.
     * Useful to announce mainteinance or to specify a non-production environment
     *
     * - text => text message to display
     * - color => background color to use
     */
    // 'AlertMessage' => [
    //     'text' => 'Test Message',
    //     'color' => '#498fde',
    // ],

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
     * - `guzzleConfig` - Optional custom configuration for underlying GuzzleHTTP client.
     */
    // 'API' => [
    //     'apiBaseUrl' => 'https://api.example.com',
    //     'apiKey' => 'MY-API-KEY',
    //     'log' => [
    //         //'log_file' => LOGS . 'api.log',
    //     ],
    //     //'guzzleConfig' => [
    //     //    'timeout' => 3,
    //     //],
    // ],

    /**
     * Clone configuration.
     * This adds custom rules to clone objects.
     * Rules are defined as `object type name` => ['reset' => [], 'unique' => []]
     * where:
     * - `reset` is an array of fields to reset => unset
     * - `unique` is an array of fields that must be unique => add `-<timestamp>` to val
     * Example:
     * 'users' => [
     *     'reset' => [
     *         'name', 'surname', 'address',
     *     ],
     *     'unique' => [
     *         'email',
     *     ],
     * ],
     * will reset `name`, `surname` and `address` fields and add `-<timestamp>` to `email` field
     * when cloning a user object.
     * Note: `reset` and `unique` are optional.
     */
    // 'Clone' => [
    //     // ...
    //     'users' => [
    //         'reset' => [
    //             'name', 'surname', 'address',
    //         ],
    //         'unique' => [
    //             'email',
    //         ],
    //     ],
    //     // ...
    // ],

    /**
     * Control handler configuration.
     * This adds custom handlers to render object fields controls.
     *
     * In the following example: cats.moustaches control is rendered through Path\To\SomeClass::countMoustaches
     */
    // 'Control' => [
    //     'handlers' => [
    //         'cats' => [
    //             'moustaches' => [
    //                 'class' => 'Path\To\SomeClass',
    //                 'method' => 'countMoustaches',
    //             ],
    //         ],
    //     ],
    // ],

    /**
     * Editors configuration.
     * concurrentCheckTime: the time in milliseconds that a concurrent access is considered still active
     */
    // 'Editors' => [
    //     'concurrentCheckTime' => 30000, // 30 seconds
    // ],

    /**
     * Export default settings
     *
     * - limit => max number of exported elements on export all
     */
    // 'Export' => [
    //     'limit' => 10000,
    // ],

    /**
     * Configuration for import filters
     */
    // 'Filters' => [
    //     'import' => [
    //         [
    //             'accept' => ['application/zip'],
    //             'name' => 'import-objects-from-zip',
    //             'label' => 'Import data from zip file',
    //             'class' => \Path\To\ZipImportFilter::class,
    //             'options' => [
    //                 'uuid' => [
    //                     'dataType' => 'text',
    //                     'defaultValue' => '',
    //                     'label' => 'Uuid',
    //                 ],
    //                 'flag' => [
    //                     'dataType' => 'boolean',
    //                     'defaultValue' => true,
    //                     'label' => 'Flag',
    //                 ],
    //                 'opts' => [
    //                     'dataType' => 'options',
    //                     'defaultValue' => 1,
    //                     'label' => 'Options',
    //                     'values' => [
    //                         1 => 'one',
    //                         2 => 'two',
    //                         3 => 'three',
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ],
    // ],

    /**
     * Configuration for flash messages.
     *  - 'modal': whether to show flash messages as modal or not (default: false)
     */
    // 'Flash' => [
    //     'modal' => false,
    // ],

    /**
     * I18n setup.
     *
     *  - 'I18n.locales': array of supported locales and language code used as `prefix` like `/en`
     *  - 'I18n.default': default language code
     *  - 'I18n.languages': array of supported language codes with their names
     *  - 'I18n.lang': language code in use (written by the application)
     *  - 'I18n.timezone': timezone code to use (i.e. 'UTC')
     *  - 'I18n.cookie': array representing cookie config used by middleware
     *  - 'I18n.switchLangUrl': url used by middleware to switch lang
     *
     * Uncomment the following 'I18n' array to activate multilanguage support
     */
    // 'I18n' => [
    //     'locales' => [
    //         'en_US' => 'en',
    //         'it_IT' => 'it',
    //         // etc.
    //     ],
    //     'default' => 'en',
    //     'languages' => [
    //         'en' => 'English',
    //         'it' => 'Italiano',
    //         // etc.
    //     ],
    //     'timezone' => 'UTC',
    //     'cookie' => [
    //         'name' => 'BEditaWebI18n',
    //         'create' => true,
    //     ],
    //     'switchLangUrl' => '/lang',
    // ],

    /**
     * Location providers and access tokens
     */
    // 'Location' => [
    //     'google' => [
    //         'url' => '###',
    //         'key' => '###',
    //     ],
    // ],

    /**
     * The under work config. When set, a courtesy page is shown with `Maintenance.message` on it.
     */
    // 'Maintenance' => [
    //     'message' => 'This page won\'t be available for some time. Try later',
    // ],

    /**
     * The manager application name in the API (default is `manager`).
     * This is used in reading/writing manager configuration data.
     */
    // 'ManagerAppName' => 'my-manager-app',

    /**
     * Maps providers and access tokens
     */
    // 'Maps' => [
    //     'mapbox' => [
    //         'token' => '###',
    //     ],
    // ],

    /**
     * Object types to add to core tables, used in Data Modeling -> Object types.
     */
    // 'Model' => [
    //     'objectTypesTables' => [
    //         'MyPlugin.MyTable',
    //     ],
    // ],

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
     *  'icon' - icon, f.i. `carbon:document`, have a look at https://icon-sets.iconify.design/
     *          for a complete list of icons
     *  'sidebar' - additional custom sidebar links added in modules index and single item view,
     *     defined as associative array with 'index' and 'view' keys
     */
    // 'Modules' => [
    //     'objects' => [
    //         'shortLabel' => 'obj',
    //         'color' => '#230637',
    //         // 'secondaryColor' => '#d95700',
    //         'sort' => '-modified',
    //         // 'icon' => 'carbon:document',
    //     ],
    //     'folders' => [
    //         'color' => '#072440',
    //     ],
    //     'documents' => [
    //         'color' => '#cc4700',
    //     ],
    //     'events' => [
    //         'color' => '#09c',
    //     ],
    //     'news' => [
    //         'color' => '#036',
    //     ],
    //     'locations' => [
    //         'color' => '#641',
    //     ],
    //     'media' => [
    //         'color' => '#a80019',
    //     ],
    //     'images' => [
    //         'color' => '#d5002b',
    //     ],
    //     'videos' => [
    //         'color' => '#d5002b',
    //     ],
    //     'audio' => [
    //         'color' => '#d5002b',
    //     ],
    //     'files' => [
    //         'color' => '#d5002b',
    //     ],
    //     'users' => [
    //         'color' => '#000000',
    //     ],
    //     'profiles' => [
    //         'color' => '#093',
    //     ],
    // ],

    /**
     * External OAuth2 Providers setup
     */
    // 'OAuth2Providers.google.setup' => [
    //     'clientId' => '####',
    //     'clientSecret' => '####',
    // ],
    // 'OAuth2Providers.github.setup' => [
    //     'clientId' => '####',
    //     'clientSecret' => '####',
    // ],

    /**
     * Pagination default settings
     *
     * - sizeAvailable => available page size on modules view index
     */
    // 'Pagination' => [
    //     'sizeAvailable' => [10, 20, 50, 100],
    // ],

    /**
     * Additional plugins to load with this format: 'PluginName' => load options array
     * Where options array may contain
     *
     * - `debugOnly` - boolean - (default: false) Whether or not you want to load the plugin when in 'debug' mode only
     * - `bootstrap` - boolean - (default: true) Whether or not you want the $plugin/config/bootstrap.php file loaded.
     * - `routes` - boolean - (default: true) Whether or not you want to load the $plugin/config/routes.php file.
     * - `ignoreMissing` - boolean - (default: true) Set to true to ignore missing bootstrap/routes files.
     * - `autoload` - boolean - (default: false) Whether or not you want an autoloader registered
     */
    //'Plugins' => [
    //    'MyPlugin' => ['autoload' => true], // a simple plugin
    //],

    /**
     * Project data that can override data from `/home` API call
     * Currently only `name` is used
     */
    // 'Project' => [
    //     'name' => 'My Project',
    // ],

    /**
     * Properties display configuration settings.
     *
     * Every key in this array is a module name, for each one we may have:
     *
     *  - 'view' properties groups to present in object view, where groups are:
     *      + '_keep' special group of properties to keep and display even if not found in object
     *      + '_hide' special group of properties to not display
     *      + 'core' always open on the top
     *      + 'publish' publishing related
     *      + 'advanced' for power users
     *      + 'other' remaining attributes
     *      + any custom name can be added as key, like 'my_group' or 'some_info'
     *          => a tab named `My Group` or `Some Info` will be generated
     *      + inside any of the groups above an optional '_element' can define a custom view element for this group
     *
     *  - 'index' properties to display in index view (other than `id`, `status` and `modified`, always displayed if set)
     *
     *  - 'relations' relations ordering by relation name, containing these optional keys
     *      + 'main' first relations to show on main column, other relations will be appended
     *      + 'aside' relations to show on right aside column
     *      + '_element' associative array with custom view element to use for a relation, defined like
     *          '{relation_name}' => '{MyPlugin.template_path}'
     *      + '_hide' array of relations to hide, not viewable in view(s)
     *      + '_readonly' array of readonly relations, to show in readonly mode in view(s)
     *
     *  - 'filter' filters to display
     *  - 'bulk' bulk actions list
     *  - 'fastCreate' fields for fast creation forms, by type
     *
     * A special custom element 'Form/empty' can be used to hide a property group or relation via `_element`
     */
    // 'Properties' => [
    //     'foos' => [
    //         'view' => [
    //             '_keep' => [
    //                 'some_field',
    //             ],
    //             '_hide' => [
    //                 'some_other_field',
    //             ],
    //             'core' => [
    //                  'some_field',
    //                  'title',
    //             ],
    //             'publish' => [
    //                  'publish_field',
    //             ],
    //             'advanced' => [
    //                  // Use custom element in `MyPlugin` to display this group
    //                  '_element' => 'MyPlugin/advanced',
    //                  'extra_field',
    //             ],
    //         ],
    //         'index' => [
    //             'name',
    //             'surname',
    //             'username',
    //         ],
    //         'relations' => [
    //             'main' => [
    //                 'foo_with',
    //                 'fooed_by',
    //             ],
    //             'aside' => [
    //                 'fooing',
    //             ],
    //             '_element' => [
    //                 // use custom element in `MyPlugin` for `fooed_by`
    //                 'fooed_by' => 'MyPlugin.fooed_by',
    //             ],
    //             '_hide' => [
    //                 'download',
    //                 'downloadable_by',
    //             ],
    //             '_readonly' => [
    //                 'seealso',
    //             ],
    //         ],
    //         'filter' => [
    //             'select_field',
    //             'another_one',
    //         ],
    //         'bulk' => [
    //             'status',
    //             'other_field',
    //         ],
    //         'fastCreate' => [
    //             'required' => ['status', 'title'],
    //             'all' => ['status', 'title', 'description'],
    //         ],
    //     ],
    // ],

    /**
     * Recovery mode. Only admin can access manager when Recovery is true.
     */
    // 'Recovery' => true,

    /**
     * Default RichTextEditor configuration.
     */
    // 'RichTextEditor' => [
    //     'default' => [
    //         'toolbar' => [
    //             'heading',
    //             '|',
    //             'bold',
    //             'italic',
    //             'underline',
    //             'strikethrough',
    //             'code',
    //             'subscript',
    //             'superscript',
    //             'removeFormat',
    //             '|',
    //             'alignment',
    //             '|',
    //             'placeholders',
    //             'specialCharacters',
    //             'link',
    //             'bulletedList',
    //             'numberedList',
    //             'blockQuote',
    //             'insertTable',
    //             'horizontalLine',
    //             '|',
    //             'undo',
    //             'redo',
    //             '|',
    //             'editSource',
    //         ],
    //     ],
    // ],

    /**
     * Configuration to organize roles in groups.
     */
    // 'RolesGroups' => [
    //     'BEdita Manager' => ['admin', 'manager', 'guest'],
    //     'Website' => ['frontend'],
    // ],

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /**
     * Configuration for thumbnails component.
     */
    // 'Thumbs' => [
    //     // Query parameters used for calling API to generate the thumbnail
    //     'queryParams' => ['preset' => 'default'],
    //     // Object types for which to call API to generate the thumbnails
    //     'objectTypes' => ['images', 'videos'],
    // ],

    /**
     * Translators engine configuration
     */
    // 'Translators' => [
    //     [
    //         'name' => 'AWS',
    //         'class' => '\BEdita\I18n\AWS\Core\Translator',
    //         'options' => [
    //             'auth_key' => 'abcde',
    //         ],
    //     ],
    //     [
    //         'name' => 'DeepL',
    //         'class' => '\BEdita\I18n\Deepl\Core\Translator',
    //         'options' => [
    //             'auth_key' => 'abcde',
    //         ],
    //     ],
    //     [
    //         'name' => 'Google',
    //         'class' => '\BEdita\I18n\Google\Core\Translator',
    //         'options' => [
    //             'auth_key' => 'abcde',
    //         ],
    //     ],
    //     [
    //         'name' => 'Microsoft',
    //         'class' => '\BEdita\I18n\Microsoft\Core\Translator',
    //         'options' => [
    //             'auth_key' => 'abcde',
    //         ],
    //     ],
    // ],

    /**
     * UI settings.
     * index.copy2clipboard => enable "onmouseover" of index general cells showing copy to clipboard button
     */
    // 'UI' => [
    //     'index' => [
    //         'copy2clipboard' => true,
    //     ],
    // ],

    /**
     * Richeditor configuration.
     */
    // 'Richeditor' => [
    //     'style_formats' => [
    //         [
    //             'title' => 'Custom Blocks',
    //             'items' => [
    //                 ['title' => 'Highlight', 'block' => 'div', 'classes' => ['be-highlight']],
    //             ],
    //         ],
    //     ],
    //     'style_formats_merge' => true,
    //     'content_style' => '.be-highlight { background-color: #F6F6F6; }',
    // ],

    /**
     * Placeholders configuration.
     */
    // 'Placeholders' => [
    //     'audio' => [
    //         'controls' => 'boolean',
    //         'autoplay' => 'boolean',
    //     ],
    //     'files' => [
    //         'download' => 'boolean',
    //     ],
    //     'images' => [
    //         'width' => ['small', 'medium', 'large'],
    //         'height' => ['small', 'medium', 'large'],
    //         'bearing' => 'integer',
    //         'pitch' => 'integer',
    //         'zoom' => 'integer',
    //     ],
    //     'videos' => [
    //         'controls' => 'boolean',
    //         'autoplay' => 'boolean',
    //     ],
    // ],

    /**
     * Upload configurations.
     */
    // 'uploadAccepted' => [
    //     'audio' => [
    //         'audio/*',
    //     ],
    //     'files' => [
    //         'application/msword', // .doc, .dot
    //         'application/pdf', // .pdf
    //         'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    //         'application/vnd.ms-excel', // .xls, .xlt, .xla
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
    //         'application/vnd.ms-powerpoint', // .ppt, .pot, .pps, .ppa
    //         'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
    //         'application/x-mpegURL',
    //         'audio/*',
    //         'text/csv',
    //         'text/html',
    //         'text/plain',
    //         'text/rtf',
    //         'text/xml',
    //         'image/*',
    //         'video/*',
    //     ],
    //     'images' => [
    //         'image/*',
    //     ],
    //     'videos' => [
    //         'application/x-mpegURL',
    //         'video/*',
    //     ],
    //     'media' => [
    //         'application/msword', // .doc, .dot
    //         'application/pdf', // .pdf
    //         'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    //         'application/vnd.ms-excel', // .xls, .xlt, .xla
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
    //         'application/vnd.ms-powerpoint', // .ppt, .pot, .pps, .ppa
    //         'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
    //         'application/x-mpegURL',
    //         'audio/*',
    //         'image/*',
    //         'text/csv',
    //         'text/html',
    //         'text/plain',
    //         'text/rtf',
    //         'text/xml',
    //         'video/*',
    //     ],
    // ],
    // 'uploadForbidden' => [
    //     'mimetypes' => [
    //         'application/javascript',
    //         'application/x-cgi',
    //         'application/x-perl',
    //         'application/x-php',
    //         'application/x-ruby',
    //         'application/x-shellscript',
    //         'text/javascript',
    //         'text/x-perl',
    //         'text/x-php',
    //         'text/x-python',
    //         'text/x-ruby',
    //         'text/x-shellscript',
    //     ],
    //     'extensions' => [
    //         'cgi',
    //         'exe',
    //         'js',
    //         'perl',
    //         'php',
    //         'py',
    //         'rb',
    //         'sh',
    //     ],
    // ],
    // 'uploadMaxResolution' => '1920x1080',
];
