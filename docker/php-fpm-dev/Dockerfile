FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
        # GD
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        # PostgreSQL
        libpq-dev \
    # GD
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    # PostgreSQL support
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) pgsql \
    # Install XDebug
    && pecl install xdebug-2.8.1 \
    && docker-php-ext-enable pgsql xdebug
RUN mkdir -p /usr/local/etc/php/conf.d/
COPY ./docker/php-fpm-dev/etc/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php-fpm-dev/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
