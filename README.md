# BEdita Manager

[![Build Status](https://travis-ci.org/bedita/manager.svg)](https://travis-ci.org/bedita/manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/manager/badges/quality-score.png)](https://scrutinizer-ci.com/g/bedita/manager/)
<!-- [![Code Coverage](https://codecov.io/gh/bedita/manager/branch/master/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/master) -->

Official Backend Admin WebApp for BEdita4 API.

## Prerequisites

* [PHP](https://www.php.net/) >= 7.1
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [Node](https://nodejs.org) 10 or 12
* [Yarn](https://yarnpkg.com) >= 1.15

## Install

* Create project via `composer`

```bash
composer create-project bedita/manager
```

If you are using a **.zip** or **.tar.gz** release file you just need to unpack it and then run ``composer install``. Run same command if you do a `git clone` on this repo.

* Build JS/CSS bundles with `yarn`

```bash
yarn && yarn build
```

* Configure BEdita 4 API base URL and API KEY in `config/.env` like:

```bash
# set BEDITA4 base URL
export BEDITA_API="{bedita-4-url}"
# set BEDITA4 API KEY (optional)
export BEDITA_API_KEY="{bedita4-api-key}"
```

You are then ready to use the webapp by simply run builtin webserver like this

```bash
bin/cake server
```

And then point your browser to `http://localhost:8765/` to give it a try.

We recommend to configure your preferred web server like Nginx/Apache and point to `webroot/` as vhost document root.

## Docker

### Pull official image

Get latest offical image build from Docker Hub

```bash
docker pull bedita/manager
```

### Build image

If you want to build an image from local sources you can do it like this from root folder:

```bash

docker build -t manager-local .

```

You may of course choose whatever name you like for the generated image instead of `manager-local`.

### Run

Run a Docker image setting API base url and API KEY like this:

```bash

docker run -p 8080:80 \
    --env BEDITA_API={bedita-api-url} --env BEDITA_API_KEY={bedita-api-key} \
    bedita/manager:latest

```

Replace `bedita/manager:latest` with `manager-local` (or other chosen name) to lanch a local built image.

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

### Using command parameters

```bash
yarn run develop --proxy localhost:8888 --server localhost:9000
```

### Production build with Bundle Report

```bash
yarn run bundle-report
```

## Run unit tests

To setup tests locally simply copy `tests/.env.default` to `tests/.env` and set env vars accordingly.
To launch tests:

```bash
vendors/bin/phpunit [test folder or file, default '/tests']
```

To run those tests you may want to use a Docker image as BEdita4 API endpoint.
For instance if you can pull a Docker image via ```docker pull bedita/bedita:4.0.0```

Then you may run the image with

```bash
docker run -p 8090:80 --env BEDITA_ADMIN_USR=bedita --env BEDITA_ADMIN_PWD=bedita bedita/bedita:4.0.0
```

You can then set env vars accordingly like this

```env
export BEDITA_API="http://localhost:8090"
export BEDITA_ADMIN_USR="bedita"
export BEDITA_ADMIN_PWD="bedita"
```

and you're ready to go

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License v3.
