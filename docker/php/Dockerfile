FROM php:8.2-fpm-alpine

# 📦 dépendances utiles Symfony + composer
RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    mysql-dev \
    bash

# extensions PHP nécessaires Symfony
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 👤 gestion UID/GID (clé de ton besoin)
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN addgroup -g ${GROUP_ID} appgroup && \
    adduser -D -u ${USER_ID} -G appgroup appuser

# 📁 dossier projet
RUN mkdir -p /var/www/html && \
    chown -R appuser:appgroup /var/www/html

USER appuser

WORKDIR /var/www/html