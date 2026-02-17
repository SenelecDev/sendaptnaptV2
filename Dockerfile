# ==============================================
# SENDAPTNAPT - Dockerfile Production
# PHP 8.3 + OCI8 (Oracle) + LDAP + PostgreSQL
# Base: donvito/php-oci8 (PHP 8.3 avec Oracle Instant Client)
# ==============================================

FROM donvito/php-oci8:8.3

LABEL maintainer="SENELEC DSI <dsi@senelec.sn>"
LABEL description="SENDAPTNAPT - Application DAPT/NAPT"

# Arguments de build (48 = apache sur CentOS)
ARG USER_ID=48
ARG GROUP_ID=48

# Variables d'environnement
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances manquantes (PostgreSQL, LDAP, GD, ZIP)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libldap2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql ldap gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer l'utilisateur www pour les permissions (48 = apache CentOS)
RUN groupadd -g ${GROUP_ID} www 2>/dev/null || true \
    && useradd -u ${USER_ID} -g www -s /bin/bash -m -d /var/www www 2>/dev/null || true

# Copier la configuration PHP personnalisée
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances d'abord (pour le cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP (sans dev) - OCI8 est présent dans l'image
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copier le reste de l'application
COPY . .

# Finaliser l'installation Composer
RUN composer dump-autoload --optimize --no-dev

# Créer les dossiers nécessaires et définir les permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R 48:48 storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exposer le port PHP-FPM
EXPOSE 9000

# PHP-FPM démarre en root et délègue les workers à www (www.conf)

# Commande par défaut
CMD ["php-fpm"]
