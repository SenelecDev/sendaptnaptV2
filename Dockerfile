# ==============================================
# SENDAPTNAPT - Dockerfile Production
# PHP 8.3 + Extensions Oracle/LDAP/PostgreSQL
# ==============================================

FROM php:8.3-fpm-alpine

LABEL maintainer="SENELEC DSI <dsi@senelec.sn>"
LABEL description="SENDAPTNAPT - Application DAPT/NAPT"

# Arguments de build (48 = apache sur CentOS)
ARG USER_ID=48
ARG GROUP_ID=48

# Variables d'environnement
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances système
RUN apk add --no-cache \
    # Utilitaires
    bash \
    curl \
    git \
    zip \
    unzip \
    supervisor \
    # Bibliothèques pour les extensions PHP
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    # PostgreSQL
    postgresql-dev \
    # LDAP
    openldap-dev \
    # Pour Oracle (optionnel, commenté car nécessite instantclient)
    # libaio \
    # libnsl \
    && rm -rf /var/cache/apk/*

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        mbstring \
        xml \
        intl \
        opcache \
        pcntl \
        ldap

# Installer Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer l'utilisateur www
RUN addgroup -g ${GROUP_ID} www \
    && adduser -u ${USER_ID} -G www -s /bin/bash -D www

# Configurer PHP pour la production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copier la configuration PHP personnalisée
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configurer PHP-FPM
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances d'abord (pour le cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP (sans dev)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copier le reste de l'application
COPY --chown=www:www . .

# Finaliser l'installation Composer
RUN composer dump-autoload --optimize --no-dev

# Créer les dossiers nécessaires et définir les permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www:www storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exposer le port PHP-FPM
EXPOSE 9000

# Utilisateur par défaut
USER www

# Commande par défaut
CMD ["php-fpm"]
