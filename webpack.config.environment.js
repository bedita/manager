
// node dependencies
const path = require('path');
const chalk = require('chalk');

const { readdirSync, statSync } = require('fs')
const { join } = require('path')

const readDirs = p => readdirSync(p).filter(f => statSync(join(p, f)).isDirectory());

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

// Show Bundle Report
let forceReport = false;
index = args.indexOf('--report');
if (index) {
    forceReport = true;
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

global.readDirs = readDirs;
global.path = path;
global.devMode = devMode;
global.forceReport = forceReport;
global.ENVIRONMENT = ENVIRONMENT;
global.BUNDLE = BUNDLE;
global.bundler = bundler;
