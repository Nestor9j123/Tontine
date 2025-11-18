#!/bin/bash

echo "🗄️ Test de connexion à la base de données..."

cd /var/www/html

# Vérifier les variables d'environnement
echo "📋 Variables DB :"
echo "DB_HOST: ${DB_HOST}"
echo "DB_PORT: ${DB_PORT}"
echo "DB_DATABASE: ${DB_DATABASE}"
echo "DB_USERNAME: ${DB_USERNAME}"

# Test de connexion simple
echo "🔌 Test de connexion..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Connexion DB: SUCCESS\n';
    echo 'Driver: ' . DB::connection()->getDriverName() . '\n';
    echo 'Database: ' . DB::connection()->getDatabaseName() . '\n';
} catch (Exception \$e) {
    echo 'Connexion DB: FAILED\n';
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"

# Lister les tables
echo "📊 Tables existantes :"
php artisan tinker --execute="
try {
    \$tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = public');
    foreach(\$tables as \$table) {
        echo '- ' . \$table->table_name . '\n';
    }
} catch (Exception \$e) {
    echo 'Impossible de lister les tables: ' . \$e->getMessage() . '\n';
}
"
