// require environment setup
require('./webpack.config.environment');

const webpack = require('webpack');
// const ExtractTextPlugin = require('extract-text-webpack-plugin');

// const extractSass = new ExtractTextPlugin({
//     filename: `${BUNDLE.beditaPluginsRoot}/OpenCorporation/${BUNDLE.webroot}/${BUNDLE.cssDir}/OpenCorporation.style.css`,
//     allChunks: true,
// });


// auto load installed plugins
const pluginsFound = readDirs(BUNDLE.beditaPluginsRoot);

let entries = {};
let aliases = {};

pluginsFound.forEach(plugin => {
    entries[plugin] = path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/${plugin}/${BUNDLE.jsRoot}/index.js`);
    aliases[plugin] = path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/${plugin}/node_modules`)
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

        extensions: ['.js', '.vue', '.json', '.scss', '.css'],
    },

    optimization: {
        minimize: true,
    }

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
                            useBuiltIns: "usage",
                        }]
                    ]
                }
            },
            // {
            //     test: /\.(css|scss)$/,
            //     include: [
            //         path.resolve(__dirname, `${BUNDLE.beditaPluginsRoot}/OpenCorporation/${BUNDLE.templateRoot}/Layout`),
            //     ],
            //     use: extractSass.extract({
            //         fallback: 'style-loader',
            //         use: [
            //             {
            //                 loader: 'css-loader',
            //                 options: {
            //                     minimize: !devMode,
            //                     sourceMap: devMode,
            //                 }
            //             },
            //             {
            //                 loader: 'sass-loader',
            //                 options: {
            //                     sourceMap: devMode
            //                 }
            //             }
            //         ],
            //     }),
            // },
        ]
    },
    devtool: devMode ? "source-map" : false,

    watch: devMode,

    stats: {
        // Display the entry points with the corresponding bundles
        entrypoints: false,
        modules: false,
        warnings: devMode,
    },
};
