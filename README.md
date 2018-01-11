# Simple BEdita4 backend webapp

Minimal BEdita3 like backend webapp for BE4 API.

UI/UX are supposed to be similar to BE3, but may change in the near future.

## Prerequisites

* PHP 7.1 (recommended) or 7
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

1. Clone repo & run composer

```bash
~$ composer install
```

If you are using a **.zip** or **.tar.gz** release file you just need to unpack it and then run ``composer install``.

1. Edit `config/app.php` and configure BEdita 4 API

```php
    'API' => [
        // API base URL and API KEY
        'apiBaseUrl' => 'https://your-bedita-4-project-url',
        'apiKey' => 'your-bedita4-project-apikey',
    ],
```

You are then ready to use the webapp by simply run builtin webserver like this

```bash
~$ bin/cake server
```

And then point your browser to `http://localhost:8765/`

Or you can configure your preferred web server like Nginx/Apache and point to `webroot/` as vhost document root.

## Docker

TBD

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License v3.
