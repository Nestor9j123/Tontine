#!/bin/bash

echo "🚀 Démarrage de l'application Tontine Laravel..."

# Aller dans le répertoire de l'app
cd /var/www/html

# Installer Composer
echo "📦 Installation de Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Nettoyer et installer les dépendances
echo "🧹 Nettoyage des dépendances..."
rm -f composer.lock
cp composer.clean.json composer.json

echo "📥 Installation des dépendances PHP..."
composer install --no-dev --ignore-platform-reqs --no-interaction --verbose

# Configuration Laravel
echo "⚙️ Configuration Laravel..."
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
    
    # Configuration automatique des variables d'environnement Render
    if [ ! -z "$DATABASE_URL" ]; then
        echo "🗄️ Configuration base de données depuis DATABASE_URL..."
        echo "DB_CONNECTION=pgsql" >> .env
        echo "DB_URL=$DATABASE_URL" >> .env
    else
        echo "🗄️ Configuration base de données depuis variables..."
        echo "DB_CONNECTION=pgsql" >> .env
        echo "DB_HOST=${DB_HOST:-localhost}" >> .env
        echo "DB_PORT=${DB_PORT:-5432}" >> .env
        echo "DB_DATABASE=${DB_DATABASE:-tontine}" >> .env
        echo "DB_USERNAME=${DB_USERNAME:-postgres}" >> .env
        echo "DB_PASSWORD=${DB_PASSWORD:-}" >> .env
    fi
    
    # Autres configurations
    echo "APP_ENV=production" >> .env
    echo "APP_DEBUG=false" >> .env
    echo "APP_URL=${APP_URL:-https://tontine-app-l9ng.onrender.com}" >> .env
fi

# Générer la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate --force

# Attendre la base de données
echo "⏳ Attente de la base de données..."
sleep 20

# Test de connexion DB
echo "🔌 Test de connexion à la base de données..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Connexion DB: SUCCESS\n';
} catch (Exception \$e) {
    echo 'Connexion DB: FAILED - ' . \$e->getMessage() . '\n';
}
" || echo "Test de connexion échoué"

# Migrations et seeds
echo "📊 Exécution des migrations..."
php artisan migrate --force || echo "❌ Migration échouée"

echo "🌱 Exécution des seeders..."
php artisan db:seed --force --class=RenderDemoSeeder || echo "❌ Seeding échoué"

# Cache et optimisations
echo "⚡ Optimisations Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan storage:link || true
php artisan optimize || true

# Permissions finales
echo "🔐 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
chown -R application:application storage bootstrap/cache

echo "✅ Application Tontine Laravel prête !"
echo "🌐 Accessible via navigateur web"
echo "📱 PWA installable via HTTPS"
