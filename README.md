# Simple BEdita4 backend webapp

Minimal BEdita3 like backend webapp for BE4 API.

UI/UX are supposed to be similar to BE3, but may change in the near future.

## Prerequisites

* PHP 7.1 or 7.2
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

1. Clone repo & run composer

```bash
~$ composer install
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
~$ bin/cake server
```

And then point your browser to `http://localhost:8765/`

Or you can configure your preferred web server like Nginx/Apache and point to `webroot/` as vhost document root.

## Docker

### Pull official image

Get latest offical image build from Docker Hub

```bash
~$ docker pull bedita/web
 ```

### Build image

If you want to build an image from local sources you can do it like this from root folder:

```bash

~$ docker build -t be4web-local .

```

You may of course choose whatever name you like for the generated image instead of `be4web-local`.

### Run

Run a Docker image setting an initial API KEY and admin username and password like this:

```bash

~$ docker run -p 8080:80 \
     --env BEDITA_API={bedita-api-url} --env BEDITA_API_KEY={bedita-api-key} \
    bedita/web:latest

```

Replace `bedita/web:latest` with `be4web-local` (or other chosen name) to lanch a local built image.

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License v3.
