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
        echo "DATABASE_URL=$DATABASE_URL" >> .env
    else
        echo "🗄️ Configuration base de données depuis variables individuelles..."
        echo "DB_CONNECTION=pgsql" >> .env
        echo "DB_HOST=${DB_HOST}" >> .env
        echo "DB_PORT=${DB_PORT:-5432}" >> .env
        echo "DB_DATABASE=${DB_DATABASE}" >> .env
        echo "DB_USERNAME=${DB_USERNAME}" >> .env
        echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
    fi
    
    # Debug des variables DB
    echo "📋 Variables DB détectées :"
    echo "DATABASE_URL: ${DATABASE_URL:-NON_DEFINI}"
    echo "DB_HOST: ${DB_HOST:-NON_DEFINI}"
    echo "DB_DATABASE: ${DB_DATABASE:-NON_DEFINI}"
    
    # Autres configurations
    echo "APP_ENV=local" >> .env
    echo "APP_DEBUG=true" >> .env
    echo "APP_URL=https://tontine-app-sskl.onrender.com" >> .env
    echo "LOG_CHANNEL=single" >> .env
    echo "LOG_LEVEL=debug" >> .env
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

# Permissions finales et debug Laravel
echo "🔐 Configuration des permissions..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 777 storage bootstrap/cache
chown -R application:application storage bootstrap/cache

# Debug Laravel - forcer les logs
echo "🐛 Activation du debug Laravel..."
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
sed -i 's/APP_ENV=production/APP_ENV=local/' .env

# Test rapide Laravel
echo "🧪 Test rapide Laravel..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "✅ Application Tontine Laravel prête !"
echo "🌐 Accessible via navigateur web"
echo "📱 PWA installable via HTTPS"
