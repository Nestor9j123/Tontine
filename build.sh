#!/bin/bash

echo "🚀 Build Render - Tontine App"
echo "============================="

# Installer les dépendances Composer
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Générer la clé d'application si elle n'existe pas
echo "🔑 Configuration de l'application..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Cache des configurations Laravel
echo "⚡ Mise en cache des configurations..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimisation Composer
echo "🔧 Optimisation autoloader..."
composer dump-autoload --optimize --no-dev

# Créer les dossiers nécessaires
echo "📁 Création des dossiers..."
mkdir -p storage/logs
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p bootstrap/cache
mkdir -p public/storage

# Permissions
echo "🔐 Configuration des permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Migration et seed de la base de données
echo "🗄️ Configuration de la base de données..."
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

# Créer le lien symbolique pour le stockage
echo "🔗 Configuration du stockage..."
php artisan storage:link --force

echo "✅ Build terminé avec succès!"
