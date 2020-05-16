FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
        # Git
        git \
        # Zip
        libzip4 \
        libzip-dev \
        # GD
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        # PostgreSQL
        libpq-dev \
    # GD
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    # Zip support (for composer)
    && docker-php-ext-install -j$(nproc) zip \
    # PostgreSQL support
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) pgsql
RUN mkdir -p /usr/local/etc/php/conf.d/
COPY ./docker/php-fpm/etc/php/php.ini /usr/local/etc/php/php.ini
