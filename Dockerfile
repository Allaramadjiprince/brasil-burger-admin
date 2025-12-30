# Dockerfile pour Brasil Burger Admin - Symfony 7

# Utilise l'image PHP 8.2 avec Apache
FROM php:8.2-apache

# Mettre à jour et installer les dépendances système
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    libicu-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    wget \
    gnupg \
    lsb-release \
    && rm -rf /var/lib/apt/lists/*

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
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
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Installer les dépendances Composer (production seulement)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Créer les dossiers nécessaires avec les bonnes permissions
RUN mkdir -p \
    public/uploads/produits \
    var/log \
    var/cache \
    && chown -R www-data:www-data \
    var \
    public/uploads \
    && chmod -R 755 \
    public/uploads \
    && chmod -R 777 \
    var/log \
    var/cache

# Configurer Apache pour Symfony
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Configurer PHP pour production
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
