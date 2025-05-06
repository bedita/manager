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
     * Api Proxy configuration, for ApiController.
     * This refers to `/api/{endpoint}` calls.
     * Contains an array of setting to use for API proxy configuration.
     *
     * ## Options
     *
     * - `blocked` - Array of blocked methods per endpoint.
     */
    // 'ApiProxy' => [
    //     'blocked' => [
    //         'objects' => ['GET', 'POST', 'PATCH', 'DELETE'],
    //         'users' => ['GET', 'POST', 'PATCH', 'DELETE'],
    //     ],
    // ],

    /**
     * Clone configuration.
     * This adds custom rules to clone objects.
     * Rules are defined as `object type name` => ['reset' => [], 'unique' => []]
     * where:
     * - `reset` is an array of fields to reset => unset
     * Example:
     * 'users' => [
     *     'reset' => [
     *         'name', 'surname', 'address',
     *     ],
     * ],
     * will reset `name`, `surname` and `address` fields and add `-<timestamp>` to `email` field
     * when cloning a user object.
     * Note: `reset` is optional.
     */
    // 'Clone' => [
    //     // ...
    //     'users' => [
    //         'reset' => [
    //             'name', 'surname', 'address',
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
     *  'dropupload' - custom dropupload element to use for this module, f.i. 'MyPlugin.Form/dropupload'
     *  'multiupload' - custom multiupload element to use for this module, f.i. 'MyPlugin.Form/multiupload'
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
    //         'dropupload' => [
    //             '_element' => 'MyPlugin.Form/dropupload',
    //         ],
    //         'multiupload' => [
    //             '_element' => 'MyPlugin.Form/multiupload',
    //         ],
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
    //     'cleanup_regex_pattern' => '\\sstyle="[^"]*"', // remove style attributes from tags
    //     'cleanup_regex_argument' => 'gs', // global and match new lines
    //     'cleanup_regex_replace' => '', // replace with empty string
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
    //         'caption' => 'richtext'
    //     ],
    //     'videos' => [
    //         'controls' => 'boolean',
    //         'autoplay' => 'boolean',
    //         'caption' => 'richtext',
    //     ],
    // ],

    /**
     * UI settings.
     * - index: index settings. 'copy2clipboard' enables "onmouseover" of index general cells showing copy to clipboard button
     * - modules: modules settings. 'counters' to show counters in modules; 'all', 'none', <list of modules> to show all, none or custom modules. Default is ['trash']
     * - richeditor: richeditor settings per field: you can set 'config' and 'toolbar' per single field.
     * - fast_create_form: custom element to use for fast create form
     */
    // 'UI' => [
    //     'index' => [
    //         'copy2clipboard' => true,
    //     ],
    //     'modules' => [
    //         'counters' => ['objects', 'media', 'images', 'videos', 'audio', 'files', 'trash', 'users'],
    //     ],
    //     'richeditor' => [
    //         'title' => [
    //             'config' => [
    //                 'forced_root_block' => 'div',
    //                 'forced_root_block_attrs' => ['class' => 'titleContainer'],
    //             ],
    //             'toolbar' => [
    //                 'italic',
    //                 'subscript',
    //                 'superscript',
    //             ],
    //         ],
    //         'description' => [
    //             'config' => [
    //                 'forced_root_block' => 'div',
    //                 'forced_root_block_attrs' => ['class' => 'descriptionContainer'],
    //             ],
    //             'toolbar' => [
    //                 'bold',
    //                 'italic',
    //                 'subscript',
    //                 'superscript',
    //                 'link',
    //                 'unlink',
    //                 'code',
    //             ],
    //         ],
    //     ],
    //     'fast_create_form' => [
    //         '_element' => 'MyPlugin.Form/fast_create',
    //     ],
    // ],

    /**
     * Upload configurations.
     *
     * 'files' and 'media' accept all mimes, so no configuration needed.
     */
    // 'uploadAccepted' => [
    //     'audio' => [
    //         'audio/*',
    //     ],
    //     'images' => [
    //         'image/*',
    //     ],
    //     'videos' => [
    //         'application/x-mpegURL',
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
    // 'uploadMaxSize' => -1, // -1 means no limit, otherwise set a limit in bytes

    /**
     * Configuration for "Children" association parameters.
     *
     * This allows to define a set of parameters that can be used in children association between a folder and an object.
     * The configuration is an associative array where keys are the parameter names and values are
     * arrays with the following keys:
     *
     * - `description` - The description of the parameter.
     * - `type` - The type of the parameter. Supported types are: `string`, `text`, `date`, `date-time`, `integer`, `boolean`, `enum`.
     * - `format` - The format of the parameter. Supported formats are: `date`, `date-time`.
     * - `value` - The default value of the parameter.
     * - `enum` - The list of possible values for the parameter. Required if the type is `enum`.
     *
     *  An example follows. Note: "author", "summary" etc. are examples, you can define your own parameters. They will be saved in `meta.relation.params`.
     */
    // 'ChildrenParams' => [
    //     'author' => [
    //         'description' => 'The author',
    //         'type' => 'string',
    //         'value' => 'john doe',
    //     ],
    //     'summary' => [
    //         'description' => 'The summary',
    //         'type' => 'text',
    //         'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    //     ],
    //     'validation_date"' => [
    //         'description' => 'The validation date',
    //         'type' => 'string',
    //         'format' => 'date',
    //         'value' => '2024-06-30',
    //     ],
    //     'validation_date_time' => [
    //         'description' => 'The validation date time',
    //         'type' => 'string',
    //         'format' => 'date-time',
    //         'value' => '2020-01-01T00:00:00Z',
    //     ],
    //     'score' => [
    //         'description' => 'The score',
    //         'type' => 'integer',
    //         'value' => 8,
    //     ],
    //     'visible' => [
    //         'description' => 'The visible flag',
    //         'type' => 'boolean',
    //         'value' => true,
    //     ],
    //     'status' => [
    //         'description' => 'The status',
    //         'enum' => ['draft', 'ready', 'done'],
    //         'type' => 'string',
    //         'value' => 'draft',
    //     ],
    // ],

    /**
     * Relations sort fields.
     * Define sortable fields per relation.
     */
    // 'RelationsSortFields' => [
    //     'composed_by' => [
    //         ['label' => 'Short title', 'value' => 'short_title'],
    //         ['label' => 'Title', 'value' => 'title'],
    //     ],
    //     'composed_by_default' => 'short_title',
    //     'part_of' => [
    //         ['label' => 'Short title', 'value' => 'short_title'],
    //         ['label' => 'Title', 'value' => 'title'],
    //     ],
    //     'part_of_default' => 'short_title',

    /**
     * Configuration for "Schema" associations provided by the API instance.
     *
     *  An example follows. Note: "author", "summary" etc. are examples, you can define your own parameters. They will be saved in `meta.relation.params`.
     */
    // 'Schema' => [
    //     'associations' => [
    //         'Captions',
    //     ],
    // ],

    /**
     * Configuration for "Captions".
     * - formats.allowed: allowed formats for captions
     * - formats.default: default format for captions
     */
    // 'Captions' => [
    //     'formats' => [
    //         'allowed' => ['srt', 'sub', 'webvtt'],
    //         'default' => 'webvtt',
    //     ],
    // ],

    /**
     * Configuration for "TreePreview", to enable anchors on specific positions on the tree.
     * - '123' is the root id
     * - 'title' is the title for the preview anchor
     * - 'url' is the href for the preview anchor
     * - 'color' is the color of the icon for the preview anchor (default is 'white')
     */
    // 'TreePreview' => [
    //     '123' => [
    //         [
    //             'title' => 'Staging url',
    //             'url' => 'https://staging.example.com',
    //             'color' => 'orange',
    //         ],
    //         [
    //             'title' => 'Production url',
    //             'url' => 'https://example.com',
    //             'color' => 'red',
    //         ],
    //     ],
    // ],
];
