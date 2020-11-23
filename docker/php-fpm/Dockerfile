FROM composer:1 AS composer

FROM php:7.4-fpm
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

COPY --from=composer /usr/bin/composer /usr/bin/composer

# unzip for composer
RUN apt-get update && apt-get install -y --no-install-recommends unzip

# docker offical php image not allow debian package installation
# read "E: Package 'php-XXX' has no installation candidate" at https://hub.docker.com/_/php
RUN pecl install phalcon \
    && echo "extension=psr.so" > /usr/local/etc/php/conf.d/pecl-psr.ini \
    && echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/pecl-phalcon.ini