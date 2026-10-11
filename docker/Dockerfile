FROM php:8.3-cli-alpine

# 1. Instalar dependencias del sistema y bibliotecas necesarias
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    postgresql-dev \
    libpng-dev \
    zip \
    libzip-dev \
    linux-headers

# 2. Instalar y habilitar extensiones PDO PostgreSQL y MongoDB
RUN docker-php-ext-install pdo pdo_pgsql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb pdo_pgsql

WORKDIR /var/www/html