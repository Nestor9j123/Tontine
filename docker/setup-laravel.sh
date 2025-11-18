#!/bin/bash

echo "🚀 Configuration Tontine App..."

# Aller dans le répertoire de l'app
cd /var/www/html

# Installer Composer si pas présent
if [ ! -f /usr/local/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Supprimer le lock file et réinstaller proprement
echo "🧹 Nettoyage des dépendances..."
rm -f composer.lock

# Installer les dépendances
echo "📦 Installation des dépendances..."
composer update --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction
composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction

# Créer .env si pas présent
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Attendre la base de données
echo "⏳ Attente de la base de données..."
sleep 10

# Configurations Laravel
echo "⚡ Configuration Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Migration et seed
echo "🗄️ Migration de la base de données..."
php artisan migrate --force || echo "Migration échouée, on continue..."
php artisan db:seed --force --class=RenderDemoSeeder || echo "Seeding échoué, on continue..."

# Storage link
echo "🔗 Configuration du stockage..."
php artisan storage:link || true

# Optimisations
echo "🚀 Optimisations finales..."
php artisan optimize || true
composer dump-autoload --optimize || true

echo "✅ Tontine App configuré!"
