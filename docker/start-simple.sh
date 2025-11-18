#!/bin/sh

echo "🚀 Configuration Tontine App Laravel..."

# Attendre la base de données
sleep 15

# Vérifier si .env existe, sinon copier depuis .env.example
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
fi

# Générer la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate --force

# Cache des configurations
echo "⚡ Mise en cache des configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations et seeding
echo "🗄️ Migration de la base de données..."
php artisan migrate --force
php artisan db:seed --force --class=RenderDemoSeeder

# Lien symbolique pour le stockage
echo "🔗 Configuration du stockage..."
php artisan storage:link

# Optimisations finales
echo "🚀 Optimisations..."
php artisan optimize
composer dump-autoload --optimize

echo "✅ Tontine App prêt sur le port 80!"
