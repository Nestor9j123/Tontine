# Dockerfile pour Tontine App Laravel sur Render
FROM php:8.3-apache

# Installer les dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances PHP avec gestion d'erreurs
RUN composer self-update && \
    composer clear-cache && \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || \
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Configurer Apache
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Créer le fichier de configuration Apache
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Exposer le port
EXPOSE 80

# Script de démarrage
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
