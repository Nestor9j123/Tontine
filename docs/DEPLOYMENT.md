# 🚀 GUIDE DE DÉPLOIEMENT

## 🎯 Vue d'Ensemble

Ce guide détaille le déploiement du système de gestion de tontines en environnement de production.

---

## 🖥️ PRÉREQUIS SERVEUR

### Spécifications Minimales
- **CPU** : 2 vCPU
- **RAM** : 4 GB
- **Stockage** : 50 GB SSD
- **Bande passante** : 100 Mbps

### Spécifications Recommandées
- **CPU** : 4 vCPU
- **RAM** : 8 GB  
- **Stockage** : 100 GB SSD
- **Bande passante** : 1 Gbps

### Stack Serveur
- **OS** : Ubuntu 20.04 LTS ou plus récent
- **Serveur Web** : Nginx 1.18+ ou Apache 2.4+
- **PHP** : 8.1+ avec PHP-FPM
- **Base de données** : MySQL 8.0+ ou PostgreSQL 13+
- **Cache** : Redis 6.0+
- **Certificat SSL** : Let's Encrypt ou commercial

---

## 📋 INSTALLATION SERVEUR

### 1. Préparation Système
```bash
# Mise à jour système
sudo apt update && sudo apt upgrade -y

# Installation paquets de base
sudo apt install -y curl wget git unzip software-properties-common

# Ajout repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

### 2. Installation PHP
```bash
# Installation PHP 8.1 et extensions
sudo apt install -y php8.1-fpm php8.1-cli php8.1-common php8.1-curl \
php8.1-zip php8.1-gd php8.1-mysql php8.1-xml php8.1-mbstring \
php8.1-json php8.1-intl php8.1-bcmath php8.1-redis

# Installation Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Installation Base de Données
```bash
# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Création base de données
sudo mysql -e "CREATE DATABASE tontine_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'tontine_user'@'localhost' IDENTIFIED BY 'strong_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON tontine_prod.* TO 'tontine_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

### 4. Installation Redis
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Configuration sécurisée
sudo nano /etc/redis/redis.conf
# Modifier: requirepass your_redis_password
sudo systemctl restart redis-server
```

### 5. Installation Node.js
```bash
# Installation Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Vérification
node --version && npm --version
```

---

## 🌐 CONFIGURATION NGINX

### Configuration Site
```nginx
# /etc/nginx/sites-available/tontine
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/tontine-app/public;
    index index.php index.html;

    # Sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache des assets statiques
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Upload size
    client_max_body_size 20M;
}
```

### Activation Site
```bash
# Lien symbolique
sudo ln -s /etc/nginx/sites-available/tontine /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Redémarrage
sudo systemctl reload nginx
```

---

## 🔒 SSL/HTTPS avec Let's Encrypt

```bash
# Installation Certbot
sudo apt install -y certbot python3-certbot-nginx

# Génération certificat
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Renouvellement automatique
sudo crontab -e
# Ajouter: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 📦 DÉPLOIEMENT APPLICATION

### 1. Clonage et Installation
```bash
# Répertoire application
sudo mkdir -p /var/www/tontine-app
cd /var/www/tontine-app

# Clone du repository (production branch)
sudo git clone -b main https://github.com/username/tontine-app.git .

# Permissions
sudo chown -R www-data:www-data /var/www/tontine-app
sudo chmod -R 755 /var/www/tontine-app

# Installation dépendances
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci && sudo -u www-data npm run build
```

### 2. Configuration Environment
```bash
# Copie et édition .env
sudo -u www-data cp .env.example .env
sudo nano .env
```

**Fichier .env Production :**
```env
APP_NAME="Système Tontine"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tontine_prod
DB_USERNAME=tontine_user
DB_PASSWORD=strong_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

### 3. Initialisation Application
```bash
# Génération clé
sudo -u www-data php artisan key:generate

# Migrations et seeders
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --class=ProductionSeeder

# Liens storage
sudo -u www-data php artisan storage:link

# Cache optimisations
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Permissions finales
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## ⚙️ SERVICES SYSTÈME

### 1. Queue Worker Service
```bash
# Créer service systemd
sudo nano /etc/systemd/system/tontine-worker.service
```

**Contenu du service :**
```ini
[Unit]
Description=Tontine Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/tontine-app
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
# Activation service
sudo systemctl daemon-reload
sudo systemctl enable tontine-worker
sudo systemctl start tontine-worker
```

### 2. Scheduler Cron
```bash
# Crontab pour www-data
sudo crontab -u www-data -e

# Ajouter ligne:
* * * * * cd /var/www/tontine-app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 MONITORING ET LOGS

### 1. Configuration Logs
```bash
# Rotation logs Laravel
sudo nano /etc/logrotate.d/tontine

# Contenu:
/var/www/tontine-app/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
```

### 2. Monitoring Système
```bash
# Installation monitoring tools
sudo apt install -y htop iotop nethogs

# Monitoring services
sudo systemctl status nginx php8.1-fpm mysql redis-server tontine-worker
```

### 3. Scripts de Maintenance
```bash
# Script backup quotidien
sudo nano /usr/local/bin/tontine-backup.sh
```

**Script de sauvegarde :**
```bash
#!/bin/bash
BACKUP_DIR="/backups/tontine"
DATE=$(date +%Y%m%d_%H%M%S)

# Création répertoire
mkdir -p $BACKUP_DIR

# Backup base de données
mysqldump -u tontine_user -p'strong_password' tontine_prod > $BACKUP_DIR/db_$DATE.sql

# Backup fichiers application
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www tontine-app --exclude='node_modules' --exclude='vendor'

# Nettoyage anciens backups (garde 7 jours)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Permissions et crontab
sudo chmod +x /usr/local/bin/tontine-backup.sh
sudo crontab -e
# Ajouter: 0 2 * * * /usr/local/bin/tontine-backup.sh >> /var/log/tontine-backup.log 2>&1
```

---

## 🔄 PROCESSUS DE MISE À JOUR

### Script de Déploiement Automatisé
```bash
# /usr/local/bin/tontine-deploy.sh
#!/bin/bash
set -e

APP_DIR="/var/www/tontine-app"
BACKUP_DIR="/backups/tontine"
DATE=$(date +%Y%m%d_%H%M%S)

echo "🚀 Déploiement Tontine - $DATE"

# 1. Backup avant mise à jour
echo "📦 Backup en cours..."
mysqldump -u tontine_user -p'strong_password' tontine_prod > $BACKUP_DIR/pre_deploy_$DATE.sql

# 2. Mode maintenance
echo "🔧 Activation mode maintenance..."
sudo -u www-data php $APP_DIR/artisan down

# 3. Mise à jour code
echo "📥 Mise à jour du code..."
cd $APP_DIR
sudo -u www-data git pull origin main

# 4. Dépendances
echo "📚 Mise à jour dépendances..."
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci && sudo -u www-data npm run build

# 5. Migrations
echo "🗃️ Migrations base de données..."
sudo -u www-data php artisan migrate --force

# 6. Cache et optimisations
echo "⚡ Optimisations..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 7. Restart services
echo "🔄 Restart services..."
sudo systemctl reload nginx
sudo systemctl reload php8.1-fpm
sudo systemctl restart tontine-worker

# 8. Sortie mode maintenance
echo "✅ Désactivation mode maintenance..."
sudo -u www-data php artisan up

echo "🎉 Déploiement terminé avec succès!"
```

---

## 🛡️ SÉCURITÉ PRODUCTION

### 1. Firewall UFW
```bash
# Activation et configuration
sudo ufw enable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
```

### 2. Fail2Ban
```bash
# Installation
sudo apt install -y fail2ban

# Configuration
sudo nano /etc/fail2ban/jail.local
```

**Configuration Fail2Ban :**
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
logpath = /var/log/nginx/error.log
```

### 3. Sécurisation PHP
```bash
# Configuration PHP sécurisée
sudo nano /etc/php/8.1/fpm/php.ini

# Modifications importantes:
expose_php = Off
display_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off
```

---

## 📈 OPTIMISATIONS PERFORMANCE

### 1. Configuration MySQL
```sql
-- /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 128M
```

### 2. Configuration PHP-FPM
```ini
; /etc/php/8.1/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 15
pm.max_requests = 500
```

### 3. Configuration Redis
```conf
# /etc/redis/redis.conf
maxmemory 1gb
maxmemory-policy allkeys-lru
```

---

## 🔍 DÉPANNAGE

### Logs Utiles
```bash
# Application Laravel
tail -f /var/www/tontine-app/storage/logs/laravel.log

# Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM  
tail -f /var/log/php8.1-fpm.log

# Queue Worker
sudo journalctl -u tontine-worker -f
```

### Commandes de Diagnostic
```bash
# Status services
sudo systemctl status nginx php8.1-fpm mysql redis-server tontine-worker

# Espace disque
df -h

# Mémoire
free -h

# Processus PHP
ps aux | grep php

# Connexions base de données
mysql -u tontine_user -p'strong_password' -e "SHOW PROCESSLIST;"
```

---

**Documentation Déploiement v1.0 - Production Ready**
