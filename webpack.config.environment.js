// default values
const DEFAULT_PROXY_HOST = 'localhost';
const DEFAULT_PROXY_PORT = '8080';
const DEFAULT_HOST = 'localhost';
const DEFAULT_PORT = '3000';

// node dependencies
const path = require('path');
const chalk = require('chalk');
const dotenv = require('dotenv').config({path: __dirname + '/config/.env'});

const { readdirSync, statSync } = require('fs')
const { join } = require('path')

const readDirs = p => readdirSync(p).filter(f => statSync(join(p, f)).isDirectory());

// Environment: default development
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

/**
 * setting up local server with proxy
 */
let proxy_host = DEFAULT_PROXY_HOST;
let proxy_port = DEFAULT_PROXY_PORT;

if ('WEBPACK_PROXY_HOST' in process.env) {
    proxy_host = process.env.WEBPACK_PROXY_HOST;
} else if (dotenv.parsed && 'WEBPACK_PROXY_HOST' in dotenv.parsed) {
    proxy_host = dotenv.parsed.WEBPACK_PROXY_HOST;
}

if ('WEBPACK_PROXY_PORT' in process.env) {
    proxy_port = process.env.WEBPACK_PROXY_PORT;
} else if (dotenv.parsed && 'WEBPACK_PROXY_PORT' in dotenv.parsed) {
    proxy_port = dotenv.parsed.WEBPACK_PROXY_PORT;
}

proxy = `${proxy_host}:${proxy_port}`;

let server_host = DEFAULT_HOST;
let server_port = DEFAULT_PORT;

index = args.indexOf('--server');
if (index !== -1) {
    let paramIndex = ++index;
    if (paramIndex <= args.length) {
        server = args[paramIndex].split(':');
        server_host = server.length > 0 ? server[0] : DEFAULT_HOST;
        server_port = server.length > 1 ? server[1] : DEFAULT_PORT;
    }
} else if ('WEBPACK_SERVER_HOST' in process.env) {
    server_host = process.env.WEBPACK_SERVER_HOST;
} else if (dotenv.parsed && 'WEBPACK_SERVER_HOST' in dotenv.parsed) {
    server_host = dotenv.parsed.WEBPACK_SERVER_HOST;
} else if ('WEBPACK_SERVER_PORT' in process.env) {
    server_port = process.env.WEBPACK_SERVER_PORT;
} else if (dotenv.parsed && 'WEBPACK_SERVER_PORT' in dotenv.parsed) {
    server_port = dotenv.parsed.WEBPACK_SERVER_PORT;
}

// Show Bundle Report
let forceReport = false;
index = args.indexOf('--report');
if (index) {
    forceReport = true;
}

// Environment Config Object
const ENVIRONMENT = {
    mode: devMode ? JSON.stringify("development") : JSON.stringify("production"),
    proxy: proxy,
    host: server_host,
    port: server_port,
}

// Bundle Config Object
const BUNDLE = {
    // source
    jsRoot: 'src/Template/Layout/js',               // source .js
    appPath: 'app',
    appName: 'app.js',
    templateRoot: 'src/Template',                   // source .scss/ .twig
    localeDir: 'src/Locale',
    beditaPluginsRoot: 'plugins',

    // destination
    webroot: 'webroot',    // destination webroot
    cssDir: 'css',                                  // destination css dir
    cssStyle: 'style.css',                          // destination app styles filename [app/ ....scss]
    cssVendors: 'vendors.css',                      // destination vendors styles [node_modules]
    jsDir: 'js',                                    // destination js dir
    bundleFileName: '[name].bundle.js'              // [name] == entry name [app, vendors]
}

// util
const bundler = {
    printMessage(message, separator) {
        console.log(chalk.red.bold(separator));
        console.log(chalk.blue.bold(message));
        console.log(chalk.red.bold(separator));
    }
}

// Read dynamically src dir [BUNDLE.jsRoot] direct subdir and create aliases for import
// Add template dir [BUNDLE.templateRoot] alias
const entries = readDirs(BUNDLE.jsRoot);

let SRC_TEMPLATE_ALIAS = {
    Locale: path.resolve(__dirname, BUNDLE.localeDir),
    Template: path.resolve(__dirname, BUNDLE.templateRoot),
};

for (const dir of entries) {
    SRC_TEMPLATE_ALIAS[dir] = path.resolve(__dirname, `${BUNDLE.jsRoot}/${dir}`);
}

// auto aliases for vendors dependencies TO-DO
const packageJson = require("./package.json");
const dependencies = packageJson.dependencies;

if (devMode) {
    SRC_TEMPLATE_ALIAS['vue'] = 'vue/dist/vue';
} else {
    SRC_TEMPLATE_ALIAS['vue'] = 'vue/dist/vue.min';
}

const locales = require(__dirname + '/' + BUNDLE.jsRoot + '/config/locales.js');

global.readDirs = readDirs;
global.path = path;
global.devMode = devMode;
global.forceReport = forceReport;
global.ENVIRONMENT = ENVIRONMENT;
global.BUNDLE = BUNDLE;
global.locales = locales;
global.bundler = bundler;
global.SRC_TEMPLATE_ALIAS = SRC_TEMPLATE_ALIAS;
