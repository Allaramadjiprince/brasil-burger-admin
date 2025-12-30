# Dockerfile pour Brasil Burger Admin - Symfony 7
FROM php:8.3-apache

# Mettre à jour et installer les dépendances système
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y \
    libicu-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    wget
RUN rm -rf /var/lib/apt/lists/*

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    opcache

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Augmenter la mémoire PHP
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory.ini
RUN echo 'upload_max_filesize = 10M' >> /usr/local/etc/php/conf.d/memory.ini
RUN echo 'post_max_size = 10M' >> /usr/local/etc/php/conf.d/memory.ini

# Copier les fichiers
COPY . .

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Créer les dossiers nécessaires
RUN mkdir -p public/uploads/produits var/log var/cache
RUN chown -R www-data:www-data var public/uploads
RUN chmod -R 755 public/uploads
RUN chmod -R 777 var/log var/cache

# Configuration Apache pour Symfony
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf
RUN echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf
RUN echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf
RUN echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf
RUN echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Exécuter le cache Symfony
RUN APP_ENV=prod php bin/console cache:clear --no-warmup
RUN APP_ENV=prod php bin/console cache:warmup

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]