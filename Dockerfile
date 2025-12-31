FROM php:8.3-apache

# Installer les extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql gd zip \
    && a2enmod rewrite

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copier tout
COPY . .

# Augmenter mémoire
RUN echo 'memory_limit = 512M' > /usr/local/etc/php/conf.d/memory.ini

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Créer dossiers et permissions
RUN mkdir -p var/cache var/log public/uploads \
    && chmod -R 777 var \
    && chmod -R 755 public/uploads

# Configuration Apache
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf
RUN echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf
RUN echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        Options -Indexes' >> /etc/apache2/sites-available/000-default.conf
RUN echo '        DirectoryIndex index.php' >> /etc/apache2/sites-available/000-default.conf
RUN echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf
RUN echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Script de démarrage
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]