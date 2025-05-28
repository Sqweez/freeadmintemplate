FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
