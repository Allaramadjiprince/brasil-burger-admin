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

# 10. Démarrer Apache
CMD ["apache2-foreground"]