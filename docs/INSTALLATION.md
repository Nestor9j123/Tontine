# 🚀 GUIDE D'INSTALLATION

## Prérequis Système

### Serveur Web
- **PHP** : 8.1 ou supérieur
- **Composer** : 2.0 ou supérieur
- **Node.js** : 16.0 ou supérieur
- **NPM** : 8.0 ou supérieur

### Base de Données
- **MySQL** : 8.0 ou supérieur (recommandé)
- **PostgreSQL** : 13.0 ou supérieur (alternatif)
- **SQLite** : 3.35 ou supérieur (développement)

### Extensions PHP Requises
```bash
# Extensions obligatoires
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-mysql (ou php-pgsql)
php-redis (optionnel)
```

---

## 🏗️ Installation Locale

### 1. Cloner le Projet
```bash
git clone https://github.com/username/tontine-app.git
cd tontine-app
```

### 2. Installation des Dépendances
```bash
# Backend (PHP)
composer install

# Frontend (Node.js)
npm install
```

### 3. Configuration Environnement
```bash
# Copier le fichier d'exemple
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 4. Configuration Base de Données

#### MySQL (Recommandé)
```bash
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tontine_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### PostgreSQL
```bash
# .env  
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tontine_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### SQLite (Développement)
```bash
# .env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Créer le fichier
touch database/database.sqlite
```

### 5. Migrations et Données
```bash
# Migrations seules
php artisan migrate

# Avec données de test (recommandé pour développement)
php artisan migrate --seed

# Reset complet (développement uniquement)
php artisan migrate:fresh --seed
```

### 6. Configuration du Stockage
```bash
# Lier le stockage public
php artisan storage:link

# Créer les dossiers nécessaires
mkdir -p storage/app/public/clients
mkdir -p storage/app/public/products
mkdir -p storage/app/public/users
```

### 7. Compilation des Assets
```bash
# Développement
npm run dev

# Production
npm run build

# Watch (développement)
npm run dev -- --watch
```

### 8. Permissions (Linux/Mac)
```bash
# Dossiers de cache et logs
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Propriétaire web server
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

---

## 🔧 Configuration Avancée

### Configuration Mail
```bash
# .env - SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tontine.com
MAIL_FROM_NAME="Système Tontine"

# .env - Mailgun (alternatif)
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
```

### Configuration Queue
```bash
# .env - Database (simple)
QUEUE_CONNECTION=database

# .env - Redis (performance)
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Démarrer le worker
php artisan queue:work
```

### Configuration Scheduler
```bash
# Crontab (nécessaire pour rapports automatiques)
* * * * * cd /path/to/tontine-app && php artisan schedule:run >> /dev/null 2>&1

# Ou avec PM2 (Node.js)
pm2 start "php artisan schedule:work" --name tontine-scheduler
```

### Configuration Session
```bash
# .env - File (défaut)
SESSION_DRIVER=file

# .env - Database (multi-serveur)
SESSION_DRIVER=database
php artisan session:table
php artisan migrate

# .env - Redis (performance)
SESSION_DRIVER=redis
```

---

## 📊 Données Initiales

### Utilisateurs par Défaut
Après `php artisan migrate --seed` :

```
Super Admin:
- Email: admin@tontine.com
- Mot de passe: password123
- Rôle: super_admin

Secrétaire:
- Email: secretaire@tontine.com  
- Mot de passe: password123
- Rôle: secretary

Agent:
- Email: agent@tontine.com
- Mot de passe: password123
- Rôle: agent
```

### Données de Test Générées
- **50 Clients** : Répartis entre agents
- **20 Produits** : Avec photos d'exemple
- **100 Tontines** : Différents statuts et progressions
- **500 Paiements** : Historique complet
- **Notifications** : Exemples de tous types

---

## 🔍 Vérification Installation

### Tests de Fonctionnement
```bash
# Tests unitaires
php artisan test

# Vérification configuration
php artisan about

# Vérification routes
php artisan route:list

# Vérification permissions
php artisan permission:show
```

### Vérifications Manuelles
1. **Interface Web** : Accéder à `http://localhost:8000`
2. **Login** : Tester avec comptes par défaut
3. **Upload** : Tester upload photo client/produit
4. **Base de Données** : Vérifier les tables créées
5. **Scheduler** : `php artisan schedule:run`

---

## 🐛 Problèmes Courants

### Erreur de Permissions
```bash
# Solution
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Erreur de Clé App
```bash
# Solution
php artisan key:generate
php artisan config:clear
```

### Erreur de Base de Données
```bash
# Vérifier la connexion
php artisan db:show

# Recréer les tables
php artisan migrate:fresh --seed
```

### Erreur Assets
```bash
# Nettoyer le cache
npm run build
php artisan view:clear
php artisan config:clear
```

### Erreur de Storage
```bash
# Recréer le lien
rm public/storage
php artisan storage:link
```

---

## 🚀 Optimisations Production

### Cache de Performance
```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation Composer
composer install --optimize-autoloader --no-dev
```

### Configuration Production
```bash
# .env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# Serveur web (Apache/Nginx)
# Document root: /public
# PHP-FPM recommandé
```

---

## 📞 Support Installation

### Logs Utiles
```bash
# Laravel
tail -f storage/logs/laravel.log

# Serveur web
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

### Commandes de Debug
```bash
# Informations système
php artisan about
php artisan env
php -m  # Extensions PHP

# Tests de base
php artisan migrate:status
php artisan route:list
php artisan config:show database
```

Pour des problèmes spécifiques, consultez le [Guide Développeur](DEVELOPER.md) ou créez une issue sur le repository du projet.
