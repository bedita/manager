#!/usr/bin/env node
'use strict'

// util
const bundler = {
    printMessage(message, separator) {
        console.log(chalk.red.bold(separator));
        console.log(chalk.blue.bold(message));
        console.log(chalk.red.bold(separator));
    }
}

// node dependencies
const path = require('path');
const chalk = require('chalk');

// webpack dependencies
const webpack = require('webpack');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const WatchExternalFilesPlugin = require('webpack-watch-files-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

// vue dependencies
const { VueLoaderPlugin } = require('vue-loader');

// Environment: default true
// from Node env
let devMode = process.env.NODE_ENV !== 'production';

// from args
const args = process.argv;
let index = args.indexOf('--mode');
if (index !== -1) {
    let paramIndex = ++index;
    if (paramIndex <= args.length) {
        devMode = args[paramIndex] !== 'production';
    }
}

let proxy = 'localhost:8080';
index = args.indexOf('--proxy');
if (index !== -1) {
    let paramIndex = ++index;
    if (paramIndex <= args.length) {
        proxy = args[paramIndex];
    }
}

// Env Config Object
const ENVIRONMENT = {
    mode: devMode ? 'develop' : 'production',
    proxy: proxy,
    host: 'localhost',
    port: 3000,
}

// Bundle Config Object
const BUNDLE = {
    // source
    jsRoot: 'src/Template/Layout/js',               // source .js
    appPath: 'app',
    appName: 'app.js',
    templateRoot: 'src/Template',                   // source .scss/ .twig

    // destination
    webroot: 'webroot',    // destination webroot
    cssDir: 'css',                                  // destination css dir
    cssStyle: 'style.css',                          // destination app styles filename [app/ ....scss]
    cssVendors: 'vendors.css',                      // destination vendors styles [node_modules]
    jsDir: 'js',                                    // destination js dir
    bundleFileName: '[name].bundle.js'              // [name] == entry name [app, vendors]
}

// config
const appEntry = `${path.resolve(__dirname, BUNDLE.jsRoot)}/${BUNDLE.appPath}/${BUNDLE.appName}`;

// Show Bundle Report
let forceReport = false;
index = args.indexOf('--report');
if (index) {
    forceReport = true;
}

// Create multiple instances of ExtractTextPlugin for Vendors and src/.scss
const extractVendorsCSS = new ExtractTextPlugin({
        filename: `${BUNDLE.cssDir}/${BUNDLE.cssVendors}`,
        allChunks: true,
        // disable: devMode,
    });
const extractSass = new ExtractTextPlugin({
        filename: `${BUNDLE.cssDir}/${BUNDLE.cssStyle}`,
        allChunks: true,
        // disable: devMode,
    });


let message = ' Production Bundle';
let separator = '-------------------';
if (devMode) {
    separator = '--------------------';
    message = ' Development Bundle';
}

// Print env infos
bundler.printMessage(message, separator);

// Create webpack plugins list
// Common Plugins
let webpackPlugins = [
    new CleanWebpackPlugin([
        BUNDLE.jsDir,
        BUNDLE.cssDir,
    ], {
        root: path.resolve(__dirname, BUNDLE.webroot),
        verbose: false,
        // temp
        exclude: ['be-icons-codes.css', 'be-icons-font.css'],
    }),
    new webpack.DefinePlugin({
        'process.env.NODE_ENV': `'${ENVIRONMENT.mode}'`
    }),

    extractVendorsCSS,
    extractSass,

    new VueLoaderPlugin(),
];

// Development or report bundle Plugin
if (devMode || forceReport) {
    webpackPlugins.push(
        new BundleAnalyzerPlugin({
            openAnalyzer: false,
            analyzerMode: 'static',
            reportFilename: `report/bundle-report.${ENVIRONMENT.mode}.html`,
        })
    );
}

// Development Plugins
if (devMode) {
    webpackPlugins.push(
        new BrowserSyncPlugin({
            proxy: {
                target: process.argv.host || ENVIRONMENT.proxy,
                proxyReq: [
                    (proxyReq) => {
                        proxyReq.setHeader('Access-Control-Allow-Origin', '*');
                        proxyReq.setHeader('Access-Control-Allow-Headers', 'content-type, origin, x-api-key, x-requested-with, authorization');
                        proxyReq.setHeader('Access-Control-Allow-Methods', 'PUT, GET, POST, PATCH, DELETE, OPTIONS');
                    }
                ]
            },
            cors: true,
            notify: true,
            open: false,
            reloadOnRestart: true,
            host: ENVIRONMENT.host,
            port: ENVIRONMENT.port,
        })
    );

    // used to watch twig template files
    webpackPlugins.push(
        new WatchExternalFilesPlugin.default({
            files: [
            `./${BUNDLE.templateRoot}/**/*.twig`,
            ]
        }),
    );
} else {
    // Production Plugins

    webpackPlugins.push(
        new OptimizeCssAssetsPlugin({
            assetNameRegExp: /.css$/g,
            cssProcessor: require('cssnano'),
            cssProcessorOptions: { discardComments: { removeAll: true } },
            canPrint: true
        })
    );
}

// Read dynamically src dir [BUNDLE.jsRoot] direct subdir and create aliases for import
// Add template dir [BUNDLE.templateRoot] alias
const { readdirSync, statSync } = require('fs')
const { join } = require('path')

const readDirs = p => readdirSync(p).filter(f => statSync(join(p, f)).isDirectory());
const entries = readDirs(BUNDLE.jsRoot);

const SRC_TEMPLATE_ALIAS = {
    Template: path.resolve(__dirname, BUNDLE.templateRoot),
};

for (const dir of entries) {
    SRC_TEMPLATE_ALIAS[dir] = path.resolve(__dirname, `${BUNDLE.jsRoot}/${dir}`);
}

module.exports = {
    entry: {
        app: [appEntry],
    },

    output: {
        path: path.resolve(__dirname, BUNDLE.webroot),              // webpack needs and absolute path
        filename: `${BUNDLE.jsDir}/${BUNDLE.bundleFileName}`,
        chunkFilename: `${BUNDLE.jsDir}/${BUNDLE.bundleFileName}`,

        // this is a way to put sourcemaps under the corect tree in the source inspector
        devtoolModuleFilenameTemplate: info => {
            if (info.identifier.indexOf('webpack') === -1 && info.identifier.indexOf('.scss') === -1) {
                return `sourcemap/${info.resourcePath}`;
            }
            return '';
        }
    },

    // extract vendors import and put them in separate file
    optimization: {
        runtimeChunk: {
            name: "manifest"
        },
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: "vendors",
                    priority: -20,
                    chunks: "all"
                }
            }
        }
    },

    resolve: {
        // aliases for import
        alias: SRC_TEMPLATE_ALIAS,

        extensions: ['.js', '.vue', '.json', '.scss', '.css'],
    },

    module: {
        rules: [
            // if dev mode don't use babel
            devMode ? {} : {
                test: /\.js$/,
                exclude: /(node_modules)/,
                loader: 'babel-loader',
                options: {
                    compact: false,
                    presets: [
                        ['@babel/preset-env', {
                            modules: false,
                            browsers: ['> 99%'],
                            // browsers: ['last 1 version'],
                            useBuiltIns: "usage",
                            // debug: true,
                        }]
                    ]
                }
            },
            {
                test: /\.scss$/,
                include: [
                    path.resolve(__dirname, BUNDLE.templateRoot),
                ],

                use: extractSass.extract({
                    fallback: 'style-loader',
                    use: [
                        {
                            loader: 'css-loader',
                            options: {
                                minimize: !devMode,
                                sourceMap: devMode,
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: devMode
                            }
                        }
                    ]
                }),
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.resolve(__dirname, 'node_modules'),
                ],
                use: extractVendorsCSS.extract({
                    fallback: 'style-loader',
                    use: [
                        {
                            loader: 'css-loader',
                            options: {
                                minimize: !devMode,
                                sourceMap: devMode,
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: devMode
                            }
                        }
                    ]
                }),
            },
            {
                test: /\.vue$/,
                include: [
                    path.resolve(__dirname, `${BUNDLE.jsRoot}`),
                ],
                use: 'vue-loader'
            },
            {
                test: /\.(woff|eot|gif)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 8092 * 1024,
                        }
                    }
                ]
            },
            {
                include: [
                    path.resolve(__dirname, 'node_modules'),
                ],
                test: /\.svg$/,
                use: 'svg-url-loader',
            },
        ],
    },

    devtool: devMode ? "source-map" : false,

    watch: devMode,

    plugins: webpackPlugins,

    stats: {
        // Display the entry points with the corresponding bundles
        entrypoints: false,
        modules: false,
        warnings: devMode,
    },
}
