# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.4
ARG NODE_VERSION=24.10.0
ARG YARN_VERSION=1.22.22

FROM node:${NODE_VERSION}-alpine AS node

###
### Base runtime image (kept small): PHP-FPM + nginx + required PHP extensions.
###
FROM php:${PHP_VERSION}-fpm-alpine AS runtime-base

RUN apk add --no-cache \
        nginx \
        curl \
        icu-libs \
        libpng \
        libjpeg-turbo \
        freetype \
        libzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd zip \
    && apk del .build-deps \
    && mkdir -p /run/nginx

# Copy files
COPY . /var/www/html

# Set APP_NAME to avoid .env load
ENV APP_NAME=BE-MANAGER
ARG DEBUG
ENV DEBUG=${DEBUG:-false}

# Install libraries
WORKDIR /var/www/html

###
### Builder image: installs git + node/yarn + composer and produces vendor + built assets.
###
FROM runtime-base AS build

RUN apk add --no-cache git

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Bring in a recent Node.js (pinned) only for the build stage
COPY --from=node /usr/local /usr/local
RUN node -v && npm -v

RUN rm -f /usr/local/bin/yarn /usr/local/bin/yarnpkg \
    && corepack enable \
    && corepack prepare yarn@${YARN_VERSION} --activate \
    && yarn -v

# Build assets (includes plugin assets via yarn install:plugins)
RUN yarn install --frozen-lockfile \
    && yarn build \
    && yarn cache clean

# Composer deps (merge-plugin includes plugins/*/composer.json) + cleanup
RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && rm -rf /root/.composer/cache

RUN rm -rf /var/www/html/node_modules /var/www/html/config/.env

RUN chown -R www-data:www-data /var/www/html

###
### Final runtime image: copy only built artifacts from builder.
###
FROM runtime-base AS runtime

COPY --from=build /var/www/html /var/www/html

# Docker nginx + PHP-FPM configuration
COPY deploy/docker/nginx.conf /etc/nginx/http.d/default.conf
COPY deploy/docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# App configuration
COPY deploy/docker/app_local.php /var/www/html/config/app_local.php

# Entrypoint
COPY deploy/docker/docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=120s --timeout=5s --retries=3 CMD curl --fail http://login || exit 1

ENTRYPOINT ["/docker-entrypoint.sh"]
