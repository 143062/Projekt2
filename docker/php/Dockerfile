FROM php:8.3-fpm-alpine

# Dodajemy niezbędne pakiety (np. PostgreSQL)
RUN apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/main \
    --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community postgresql17-client

# Definiujemy wymagane biblioteki systemowe
ENV RUN_DEPS \
    zlib \
    libzip \
    libpng \
    libjpeg-turbo \
    postgresql-libs \
    linux-headers \
    musl-dev \
    rabbitmq-c \
    rabbitmq-c-dev

ENV BUILD_DEPS \
    zlib-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    postgresql-dev \
    linux-headers \
    musl-dev \
    rabbitmq-c-dev

ENV PHP_EXTENSIONS \
    opcache \
    zip \
    gd \
    bcmath \
    pgsql \
    pdo_pgsql \
    sockets  

# Instalujemy wymagane rozszerzenia PHP
RUN apk add --no-cache --virtual .build-deps $BUILD_DEPS \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j "$(nproc)" $PHP_EXTENSIONS \
    && apk del .build-deps

# Instalujemy zależności systemowe
RUN apk add --no-cache --virtual .run-deps $RUN_DEPS

# Instalujemy rozszerzenie sockets
RUN docker-php-ext-install sockets

# Ustawiamy brak limitu pamięci
RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

# Instalujemy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalujemy php-amqplib dla RabbitMQ
RUN composer require php-amqplib/php-amqplib

# Ustawiamy katalog roboczy
WORKDIR /app-laravel
