# 🚀 Guide de Développement - Tontine App

## ⚠️ Problème CORS Fréquent

### Symptômes
```
Access to script at 'http://localhost:8000/build/assets/app-xxx.js' from origin 'http://127.0.0.1:8000' 
has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

### Cause
Le serveur **Vite** n'est pas démarré ou s'est arrêté.

### Solutions

#### 🎯 Solution Rapide
```bash
# 1. Redémarrer Vite
npm run dev

# 2. Dans un autre terminal, démarrer Laravel
php artisan serve --host=0.0.0.0 --port=8000
```

#### 🚀 Solution Automatique
```bash
# Utiliser le script de démarrage automatique
./start-dev.sh
```

#### 🧹 Solution Complète (si problèmes persistent)
```bash
# 1. Tuer tous les processus
pkill -f "vite"
pkill -f "php artisan serve"

# 2. Vider les caches
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear

# 3. Redémarrer les serveurs
npm run dev &
php artisan serve --host=0.0.0.0 --port=8000
```

## 🔧 Configuration Vite

Le fichier `vite.config.js` est configuré pour éviter les CORS :

```javascript
server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    hmr: {
        host: 'localhost',
    },
}
```

## 📱 URLs de Développement

- **Application Laravel** : http://localhost:8000
- **Vite HMR** : http://localhost:5173
- **Base de données** : Voir `.env`

## 🐛 Debug

### Vérifier si Vite fonctionne
```bash
curl http://localhost:5173
# Doit retourner du HTML, pas d'erreur
```

### Vérifier les processus
```bash
ps aux | grep vite
ps aux | grep "php artisan serve"
```

### Logs Vite
Les logs Vite apparaissent dans le terminal où vous avez lancé `npm run dev`.

## 💡 Bonnes Pratiques

1. **Toujours démarrer Vite en premier**
2. **Utiliser des terminaux séparés** pour Vite et Laravel
3. **Vider les caches** après les modifications de config
4. **Utiliser le script `start-dev.sh`** pour automatiser

## 🆘 En cas de problème

1. Vérifier que Node.js et npm sont installés
2. Vérifier que les dépendances sont installées : `npm install`
3. Vérifier les ports (5173 et 8000) ne sont pas utilisés
4. Redémarrer complètement avec le script `start-dev.sh`
