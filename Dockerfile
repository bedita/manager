ARG PHP_VERSION=7.2
FROM chialab/php:${PHP_VERSION}-apache

# Install Wait-for-it and configure PHP
RUN curl -o /wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /wait-for-it.sh \
    && echo "[PHP]\noutput_buffering = 4096\nmemory_limit = -1" > /usr/local/etc/php/php.ini

# Copy files
COPY . /var/www/html

# Set APP_NAME to avoid .env load
ENV APP_NAME BE4-MANAGER
ARG DEBUG
ENV DEBUG ${DEBUG:-false}

# Install libraries
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
USER www-data:www-data

RUN if [ ! "$DEBUG" = "true" ]; then export COMPOSER_ARGS='--no-dev'; fi \
    && composer install $COMPOSER_ARGS --optimize-autoloader --no-interaction --quiet

# Restore user `root` to install node & yarn and to make sure we can bind to address 0.0.0.0:80
USER root:root

# Install node and yarn
ENV NODE_VERSION=12.6.0
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN npm install -g yarn
RUN yarn && yarn build

ENV LOG_DEBUG_URL="console:///?stream=php://stdout" \
    LOG_ERROR_URL="console:///?stream=php://stderr"

HEALTHCHECK --interval=30s --timeout=3s --start-period=1m \
    CMD curl -f http://localhost/login || exit 1

CMD ["apache2-foreground"]
