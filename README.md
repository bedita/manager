# BEdita4 Backend Admin WebApp

[![Build Status](https://travis-ci.org/bedita/web.svg)](https://travis-ci.org/bedita/web)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/web/badges/quality-score.png)](https://scrutinizer-ci.com/g/bedita/web/)
<!-- [![Code Coverage](https://codecov.io/gh/bedita/web/branch/master/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/master) -->

Official Backend Admin WebApp for BEdita4 API.

## Prerequisites

* PHP >= 7.1
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

1. Clone repo & run composer

```bash
composer install
```

If you are using a **.zip** or **.tar.gz** release file you just need to unpack it and then run ``composer install``.

1. Copy `config/.env.default` to `config/.env` and configure BEdita 4 API base URL and API KEY like:

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

And then point your browser to `http://localhost:8765/`

Or you can configure your preferred web server like Nginx/Apache and point to `webroot/` as vhost document root.

## Docker

### Pull official image

Get latest offical image build from Docker Hub

```bash
docker pull bedita/web
```

### Build image

If you want to build an image from local sources you can do it like this from root folder:

```bash

docker build -t be4web-local .

```

You may of course choose whatever name you like for the generated image instead of `be4web-local`.

### Run

Run a Docker image setting API base url and API KEY like this:

```bash

docker run -p 8080:80 \
    --env BEDITA_API={bedita-api-url} --env BEDITA_API_KEY={bedita-api-key} \
    bedita/web:latest

```

Replace `bedita/web:latest` with `be4web-local` (or other chosen name) to lanch a local built image.

## Development

### Run dev with webpack

### Using .env

It's easy to configure .env to match your web server and proxy requirement. Simply copy the .default.env and configure it.
(default proxy: localhost:8080, default server: localhost:3000)

```env
# Be4 Entry Point

WEBPACK_SERVER_HOST=localhost
WEBPACK_SERVER_PORT=3000

# Proxy server

WEBPACK_PROXY_HOST=local-be4-web
WEBPACK_PROXY_PORT=8080
```

To start develop run

```bash
yarn develop
```

### Using command parameters

```bash
yarn run develop --proxy localhost:8888 --server localhost:9000
```

## Build

```bash
yarn run build
```

## Bundle Report

```bash
yarn run bundle-report
```

Host passed via `--host` option points to your local instance, builtin webserver is used in this example.

## Run unit tests

To setup tests locally simply copy `tests/.env.default` to `tests/.env` and set env vars accordingly.
To launch tests:

```bash
vendors/bin/phpunit [test folder or file, default '/tests']
```

To run those tests you may want to use a Docker image as BEdita4 API endpoint.
For instance if you can pull a Docker image via ```docker pull bedita/bedita:4.0.0-RC2```

Then you may run the image with

```bash
docker run -p 8090:80 --env BEDITA_ADMIN_USR=bedita --env BEDITA_ADMIN_PWD=bedita bedita/bedita:4.0.0-RC2
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
