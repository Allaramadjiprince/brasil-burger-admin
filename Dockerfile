FROM php:8.3-apache

# 1. Installer seulement l'essentiel
RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libzip-dev unzip

# 2. Installer extensions PHP
RUN docker-php-ext-install pdo pdo_pgsql gd zip

# 3. Activer Apache rewrite
RUN a2enmod rewrite

# 4. Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 5. Répertoire de travail
WORKDIR /var/www/html

# 6. Copier tout
COPY . .

# 7. Augmenter mémoire PHP (CRITIQUE)
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory.ini

# 8. Installer dépendances SANS scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 9. Créer dossiers avec permissions
RUN mkdir -p var/cache var/log
RUN chmod -R 777 var/cache var/log

# Configuration Apache pour Symfony
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ✅ CRITIQUE : Ajouter DirectoryIndex pour Apache
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf
RUN echo "Options -Indexes" >> /etc/apache2/apache2.conf

# ✅ CRITIQUE : Nettoyer le cache Symfony
RUN APP_ENV=prod php bin/console cache:clear --no-warmup || true
RUN APP_ENV=prod php bin/console cache:warmup || true

# 10. Démarrer Apache
CMD ["apache2-foreground"]