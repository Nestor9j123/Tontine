#!/bin/bash

echo "🚀 Démarrage Tontine App..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
sleep 10

# Configurer Laravel
echo "🔧 Configuration Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration et seeding
echo "🗄️ Migration de la base de données..."
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction --class=RenderDemoSeeder

# Créer le lien de stockage
echo "🔗 Configuration du stockage..."
php artisan storage:link --force

# Optimisations
echo "⚡ Optimisations..."
php artisan optimize
composer dump-autoload --optimize

echo "✅ Tontine App prêt!"

# Démarrer Apache
exec apache2-foreground
