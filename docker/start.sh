#!/bin/bash
set -e

echo "🔧 Démarrage de Brasil Burger Admin..."

cd /var/www/html

# 1. Vérifier et créer les dossiers
mkdir -p var/cache var/log public/uploads
chmod -R 777 var
chmod -R 755 public/uploads

# 2. Nettoyer le cache Symfony
if [ -f bin/console ]; then
    echo "🧹 Nettoyage du cache Symfony..."
    APP_ENV=prod php bin/console cache:clear --no-warmup || true
    APP_ENV=prod php bin/console cache:warmup || true
fi

# 3. Vérifier la base de données (optionnel)
if [ -f bin/console ] && [ ! -z "$DATABASE_URL" ]; then
    echo "🗄️  Vérification de la base de données..."
    APP_ENV=prod php bin/console doctrine:schema:update --force --no-interaction || true
fi

# 4. Redémarrer Apache
echo "🚀 Démarrage d'Apache..."
exec apache2-foreground
