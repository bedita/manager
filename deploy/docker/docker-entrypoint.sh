#!/bin/sh
set -e

php --version >&2

chown -R www-data:www-data /var/www/html/tmp /var/www/html/logs

# Start PHP-FPM in the background
php-fpm -D

# Start nginx in the foreground
exec nginx -g 'daemon off;'
