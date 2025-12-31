# Solution ultime pour Render - Brasil Burger Admin
FROM php:8.3

# 1. Installer l'essentiel (minimum pour Render Free)
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
RUN echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/memory.ini
RUN echo 'opcache.memory_consumption=256' >> /usr/local/etc/php/conf.d/memory.ini

# 6. Installer dépendances (SANS scripts pour éviter erreurs)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# 7. Copier le reste des fichiers
COPY . .

# 8. Créer les dossiers CRITIQUES pour Symfony
RUN mkdir -p \
    var/cache/prod \
    var/log \
    public/uploads/produits

# 9. Donner TOUTES les permissions (évite erreur 500)
RUN chmod -R 777 var
RUN chmod -R 755 public/uploads

# 10. Nettoyer le cache Symfony EN SILENCIEUX (|| true = ignore les erreurs)
RUN APP_ENV=prod php bin/console cache:clear --no-warmup --no-debug --quiet || true
RUN APP_ENV=prod php bin/console cache:warmup --env=prod --no-debug --quiet || true

# 11. COMMANDE FINALE : Démarrer sur le PORT Render
# Render utilise la variable $PORT (généralement 10000)
CMD ["php", "-S", "0.0.0.0:${PORT}", "-t", "public"]