# Dockerfile
FROM php:8.0.0-fpm-alpine3.12

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
