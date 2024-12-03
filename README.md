# BEdita Manager

[![Github Actions PHP](https://github.com/bedita/manager/workflows/php/badge.svg)](https://github.com/bedita/manager/actions?query=workflow%3Aphp)
[![Github Actions Javascript](https://github.com/bedita/manager/workflows/javascript/badge.svg)](https://github.com/bedita/manager/actions?query=workflow%3Ajavascript)
[![codecov](https://codecov.io/gh/bedita/manager/branch/master/graph/badge.svg)](https://codecov.io/gh/bedita/manager)
[![phpstan](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg)](https://phpstan.org)
[![psalm](https://img.shields.io/badge/psalm-level%208-brightgreen.svg)](https://psalm.dev)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/manager/badges/quality-score.png)](https://scrutinizer-ci.com/g/bedita/manager/)
[![Version](https://img.shields.io/packagist/v/bedita/manager.svg?label=stable)](https://packagist.org/packages/bedita/manager)
[![License](https://img.shields.io/badge/License-LGPL_v3-orange.svg)](https://github.com/bedita/manager/blob/master/LICENSE.LGPL)

<!-- [![Code Coverage](https://codecov.io/gh/bedita/manager/branch/master/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/master) -->

Backend Manager for [BEdita API](https://gihub.com/bedita/bedita).

## Prerequisites

* [PHP](https://www.php.net/) >= 8.1
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [Node](https://nodejs.org) >= 18
* [Yarn](https://yarnpkg.com) >= 1.15

## Install

* Create project via `composer`

```bash
composer create-project bedita/manager
```

This will create a new `manager` folder and install composer dependencies.
If you are using a **.zip** or **.tar.gz** release file you just need to unpack it and then run ``composer install``. Run same command if you do a `git clone` on this repo.

* Build JS/CSS bundles with `yarn` from `manager` folder

```bash
yarn && yarn build
```

* Configure BEdita API base URL and API KEY in `config/.env` like:

```bash
# set BEDITA base URL
export BEDITA_API="{bedita-url}"
# set BEDITA API KEY (optional)
export BEDITA_API_KEY="{bedita-api-key}"
```

To test the webapp you can simply run builtin webserver from `manager` folder like this

```bash
bin/cake server
```

And then point your browser to `http://localhost:8765/`

For any other use than a simple test we recommend to configure your preferred web server like Nginx/Apache and point to `webroot/` as vhost document root.

## Configuration

You can further configure your BEdita Manager instance in `config/app_local.php` with environment and project specific settings.

Have look at the main [Manager configuration wiki page](https://github.com/bedita/manager/wiki/Manager-App-Configuration) on how to customize your Manager instance.

## JS Development with webpack

### Using .env

It's easy to configure `config/.env` to match your web server and proxy requirements, see below.
(default proxy: localhost:8080, default server: localhost:3000)

```env
# BE Manager Entry Point
WEBPACK_SERVER_HOST=localhost
WEBPACK_SERVER_PORT=3000

# Proxy server
WEBPACK_PROXY_HOST=local-be4-web
WEBPACK_PROXY_PORT=8080
```

To start develop mode run

```bash
yarn develop
```

### Production build with Bundle Report

```bash
yarn run bundle-report
```
### ESlint

* Run ESlint via `yarn`, to check linting on js files

```bash
yarn run eslint resources/js/app/pages/admin/index.js
```

## Run unit tests

To setup tests locally simply copy `tests/.env.example` to `tests/.env` and set env vars accordingly.
To launch tests:

```bash
vendors/bin/phpunit [test folder or file, default '/tests']
```

To run those tests you may want to use a Docker image as BEdita4 API endpoint.
For instance if you can pull a Docker image via ```docker pull bedita/bedita:4``` or ```docker pull bedita/bedita:5```

Then you may run the image with

```bash
docker run -p 8090:80 --env BEDITA_ADMIN_USR=bedita --env BEDITA_ADMIN_PWD=bedita bedita/bedita:5
```

You can then set env vars accordingly like this:

```env
export BEDITA_API="http://localhost:8090"
export BEDITA_ADMIN_USR="bedita"
export BEDITA_ADMIN_PWD="bedita"
```

and you're ready to go

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License v3.
