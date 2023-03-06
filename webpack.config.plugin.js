// require environment setup
require('./webpack.config.environment');

const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
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

    plugins: [new MiniCssExtractPlugin({
        filename: '[name]/webroot/css/[name].plugin.css',
    })],

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
