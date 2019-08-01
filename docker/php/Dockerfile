FROM php:7.1-fpm

ARG GITHUB_TOKEN

# Install modules
RUN apt-get update && apt-get install -y \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libicu-dev \
        --no-install-recommends

RUN docker-php-ext-install mcrypt zip intl mbstring pdo_mysql exif \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN pecl install -o -f xdebug \
    && rm -rf /tmp/pear

COPY ./php.ini /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php/

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer config -g "github-oauth.github.com" $GITHUB_TOKEN \
    && composer global require --no-progress "fxp/composer-asset-plugin" \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

RUN usermod -u 1000 www-data

WORKDIR /app
VOLUME /app

EXPOSE 9000

CMD ["php-fpm"]