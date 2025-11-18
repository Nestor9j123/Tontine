#!/bin/bash

echo "🚀 Configuration Tontine App..."

# Aller dans le répertoire de l'app
cd /var/www/html

# Installer Composer si pas présent
if [ ! -f /usr/local/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# NETTOYAGE COMPLET DE COMPOSER
echo "🧹 Nettoyage complet de Composer..."
rm -rf vendor/
rm -f composer.lock
rm -rf /root/.composer

# Utiliser le composer.json propre
echo "📝 Utilisation du composer.json simplifié..."
cp composer.clean.json composer.json

# Réinstaller Composer proprement
echo "🔧 Réinstallation de Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --force

# Installer les dépendances de base
echo "📦 Installation des dépendances de base..."
composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction --verbose

# Ajouter les packages optionnels après coup si nécessaire
echo "➕ Ajout des packages optionnels..."
composer require barryvdh/laravel-dompdf --no-interaction --ignore-platform-reqs || echo "PDF package skipped"

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
