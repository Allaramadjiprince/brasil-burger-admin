FROM php:8.3-apache

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

# ✅ CRITIQUE : Augmenter la mémoire PHP AVANT composer install
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory.ini
RUN echo 'upload_max_filesize = 10M' >> /usr/local/etc/php/conf.d/memory.ini
RUN echo 'post_max_size = 10M' >> /usr/local/etc/php/conf.d/memory.ini

# ✅ CRITIQUE : Copier d'abord seulement les fichiers de dépendances
COPY composer.json composer.lock symfony.lock ./

# ✅ CRITIQUE : Installer les dépendances AVEC optimisation mémoire et SANS scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress --no-scripts

# Maintenant copier le reste des fichiers
COPY . .

# ✅ CRITIQUE : Supprimer le cache vendor inutile pour gagner de l'espace
RUN rm -rf ~/.composer/cache

# ✅ CRITIQUE : Créer les dossiers AVANT d'exécuter les scripts Symfony
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

# ✅ CRITIQUE : Exécuter les scripts Symfony MANUELLEMENT en environnement production
RUN APP_ENV=prod php bin/console cache:clear --no-warmup
RUN APP_ENV=prod php bin/console cache:warmup

# ✅ CRITIQUE : Configurer Apache pour Symfony (version garantie)
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        FallbackResource /index.php' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# ✅ CRITIQUE : Configuration PHP de production
RUN echo 'date.timezone = "Africa/Porto-Novo"' > /usr/local/etc/php/conf.d/production.ini && \
    echo 'display_errors = Off' >> /usr/local/etc/php/conf.d/production.ini && \
    echo 'log_errors = On' >> /usr/local/etc/php/conf.d/production.ini && \
    echo 'error_log = /var/log/php_errors.log' >> /usr/local/etc/php/conf.d/production.ini && \
    echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/production.ini && \
    echo 'opcache.memory_consumption=256' >> /usr/local/etc/php/conf.d/production.ini && \
    echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/production.ini

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]