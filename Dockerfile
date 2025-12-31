# Dockerfile ULTIME pour Render Free
FROM php:8.3

# 1. Installer UNIQUEMENT l'essentiel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    gd \
    zip \
    opcache

# 2. Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# 3. Répertoire de travail
WORKDIR /var/www/html

# 4. Copier d'abord les fichiers de dépendances (optimisation cache)
COPY composer.json composer.lock symfony.lock ./

# 5. Augmenter mémoire PHP (CRITIQUE pour Render Free)
RUN echo 'memory_limit = 256M' > /usr/local/etc/php/conf.d/memory.ini
RUN echo 'upload_max_filesize = 10M' >> /usr/local/etc/php/conf.d/memory.ini
RUN echo 'post_max_size = 10M' >> /usr/local/etc/php/conf.d/memory.ini

# 6. Installer dépendances SANS SCRIPTS
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# 7. Copier tout le reste
COPY . .

# 8. Créer dossiers et permissions (CRITIQUE)
RUN mkdir -p \
    var/cache \
    var/log \
    public/uploads \
    && chmod -R 777 var \
    && chmod -R 755 public/uploads

# 9. Nettoyer cache Symfony (CRITIQUE)
RUN APP_ENV=prod php bin/console cache:clear --no-warmup
RUN APP_ENV=prod php bin/console cache:warmup

# 10. Lancer le serveur PHP sur le PORT Render (ULTRA CRITIQUE)
CMD ["php", "-S", "0.0.0.0:${PORT}", "-t", "public"]