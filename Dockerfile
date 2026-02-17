# ==============================================
# SENDAPTNAPT - Dockerfile Production
# PHP 8.3-FPM + OCI8 (Oracle) + LDAP + PostgreSQL
# Multi-stage: Oracle Instant Client depuis donvito + php:8.3-fpm
# ==============================================

# Stage 1: Récupérer Oracle Instant Client depuis donvito
FROM donvito/php-oci8:8.3 AS oci8-source

# Stage 2: Image finale avec PHP-FPM (Bookworm = Debian 12, libaio1 disponible)
FROM php:8.3-fpm-bookworm

LABEL maintainer="SENELEC DSI <dsi@senelec.sn>"
LABEL description="SENDAPTNAPT - Application DAPT/NAPT"

ARG USER_ID=48
ARG GROUP_ID=48

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copier Oracle Instant Client depuis donvito
COPY --from=oci8-source /opt/oracle /opt/oracle
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_21_13

# Installer OCI8 (nécessite libaio)
RUN apt-get update && apt-get install -y --no-install-recommends libaio-dev \
    && echo "instantclient,/opt/oracle/instantclient_21_13" | pecl install oci8 \
    && docker-php-ext-enable oci8 \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les dépendances (PostgreSQL, LDAP, GD, ZIP, Redis)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libldap2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql ldap gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer l'utilisateur www
RUN groupadd -g ${GROUP_ID} www 2>/dev/null || true \
    && useradd -u ${USER_ID} -g www -s /bin/bash -m -d /var/www www 2>/dev/null || true

# Configuration PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Dépendances Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

# Permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R 48:48 storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
