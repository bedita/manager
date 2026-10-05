// require environment setup
require('./webpack.config.environment');

const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const WatchExternalFilesPlugin = require('webpack-watch-files-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const path = require('path');

// auto load installed plugins
const pluginsFound = readDirs(BUNDLE.beditaPluginsRoot);

let entries = {};
let aliases = {};

pluginsFound.forEach(plugin => {
    let jsRoot = BUNDLE.jsRoot;
    if (!fileExists(`${BUNDLE.beditaPluginsRoot}/${plugin}/${jsRoot}`)) {
        for (let root of BUNDLE.alternateJsRoots) {
            if (fileExists(`${BUNDLE.beditaPluginsRoot}/${plugin}/${root}`)) {
                jsRoot = root;

                break;
            }
        }
    }
    entries[plugin] = path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/${plugin}/${jsRoot}/index.js`);
    aliases[plugin] = path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/${plugin}/node_modules`);
});

module.exports = {
    entry: entries,
    output: {
        path: path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}`),
        filename: '[name]/webroot/js/[name].plugin.js',
        libraryTarget: 'window',
        library: '[name]',
    },

    externals: {
        vue: 'node_modules/vue/dist/vue',
    },

    resolve: {
        alias: aliases,
        extensions: ['.js', '.vue', '.json', '.scss', '.css', 'po'],
    },

    optimization: {
        minimize: true,
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name]/webroot/css/[name].plugin.css',
        }),
        new VueLoaderPlugin(),
    ].concat(devMode ? [
        new BrowserSyncPlugin({
            proxy: {
                target: ENVIRONMENT.proxy,
                proxyReq: [
                    (proxyReq) => {
                        proxyReq.setHeader('Access-Control-Allow-Origin', '*');
                        proxyReq.setHeader('Access-Control-Allow-Headers', 'content-type, origin, x-api-key, x-requested-with, authorization');
                        proxyReq.setHeader('Access-Control-Allow-Methods', 'PUT, GET, POST, PATCH, DELETE, OPTIONS');
                    }
                ]
            },
            cors: true,
            notify: false,
            open: true,
            reloadOnRestart: true,
            host: ENVIRONMENT.host,
            port: ENVIRONMENT.port,
            watch: true,
            logLevel: 'debug'
        }),
        new WatchExternalFilesPlugin.default({
            files: [
                `./${BUNDLE.templateRoot}/**/*.twig`,
                `./${BUNDLE.resourcesRoot}/**/*.js`,
                `./${BUNDLE.resourcesRoot}/**/*.vue`,
                `./${BUNDLE.resourcesRoot}/**/*.scss`,
                `./${BUNDLE.resourcesRoot}/**/*.css`,
            ]
        }),
    ] : []),

    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                include: [
                    path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/[name]/node_modules`),
                ],
                options: {
                    compact: false,
                    presets: [
                        ['@babel/preset-env', {
                            modules: false,
                            browsers: ['> 99%'],
                            useBuiltIns: 'usage',
                        }]
                    ]
                }
            },
            {
                test: /\.(scss|css)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: devMode,
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: devMode
                        }
                    }
                ],
            },
            {
                test: /\.po$/,
                use: [
                    { loader: 'json-loader' },
                    { loader: './webpack-gettext-loader' },
                ],
            },
            {
                test: /\.vue$/,
                use: [
                    { loader: 'vue-loader' },
                ],
            },
        ]
    },

    devtool: devMode ? 'source-map' : false,

    watch: devMode,

    stats: {
        // Display the entry points with the corresponding bundles
        entrypoints: false,
        modules: false,
        warnings: devMode,
    },
};
