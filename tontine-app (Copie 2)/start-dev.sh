#!/bin/bash

echo "🚀 Démarrage de l'environnement de développement..."

# Tuer les processus Vite existants
echo "🔄 Arrêt des processus Vite existants..."
pkill -f "vite" 2>/dev/null || true

# Vider les caches Laravel
echo "🧹 Nettoyage des caches Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Démarrer Vite en arrière-plan
echo "⚡ Démarrage de Vite..."
npm run dev &
VITE_PID=$!

# Attendre que Vite soit prêt
echo "⏳ Attente du démarrage de Vite..."
sleep 5

# Démarrer le serveur Laravel
echo "🌐 Démarrage du serveur Laravel..."
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

echo "✅ Environnement de développement démarré !"
echo "📱 Application: http://localhost:8000"
echo "⚡ Vite HMR: http://localhost:5173"
echo ""
echo "Pour arrêter les serveurs, appuyez sur Ctrl+C"

# Fonction pour nettoyer à la sortie
cleanup() {
    echo ""
    echo "🛑 Arrêt des serveurs..."
    kill $VITE_PID 2>/dev/null || true
    kill $LARAVEL_PID 2>/dev/null || true
    pkill -f "vite" 2>/dev/null || true
    pkill -f "php artisan serve" 2>/dev/null || true
    echo "✅ Serveurs arrêtés"
    exit 0
}

# Capturer Ctrl+C
trap cleanup SIGINT SIGTERM

# Attendre indéfiniment
wait
