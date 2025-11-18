# 🛠️ GUIDE DÉVELOPPEUR

## 🏗️ ARCHITECTURE

### Structure du Projet
```
tontine-app/
├── app/
│   ├── Console/Commands/     # Commandes Artisan
│   ├── Http/Controllers/     # Contrôleurs
│   ├── Http/Middleware/      # Middlewares
│   ├── Models/               # Modèles Eloquent
│   ├── Services/             # Services métier
│   └── Notifications/        # Notifications
├── database/
│   ├── migrations/           # Migrations DB
│   ├── seeders/              # Seeders
│   └── factories/            # Model factories
├── resources/
│   ├── views/                # Templates Blade
│   ├── js/                   # JavaScript
│   └── css/                  # Styles
└── tests/                    # Tests automatisés
```

### Conventions de Code
- **PSR-12** : Standard de codage PHP
- **Eloquent** : Utilisation exclusive pour DB
- **Repository Pattern** : Pour logique complexe
- **Service Classes** : Pour logique métier

---

## 📊 MODÈLES DE DONNÉES

### Relations Principales
```php
// User (1) → (N) Client
User::class → hasMany(Client::class, 'agent_id')
Client::class → belongsTo(User::class, 'agent_id')

// Client (1) → (N) Tontine  
Client::class → hasMany(Tontine::class)
Tontine::class → belongsTo(Client::class)

// Product (1) → (N) Tontine
Product::class → hasMany(Tontine::class)
Tontine::class → belongsTo(Product::class)

// Tontine (1) → (N) Payment
Tontine::class → hasMany(Payment::class)
Payment::class → belongsTo(Tontine::class)
```

### Traits Utilisés
- **HasFactory** : Factories pour tests
- **SoftDeletes** : Suppression logique
- **HasRoles** : Système de permissions (Spatie)
- **Notifiable** : Notifications Laravel

---

## 🔧 DÉVELOPPEMENT

### Configuration Locale
```bash
# Clone et install
git clone [repo]
composer install && npm install

# Configuration
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Assets en mode watch
npm run dev -- --watch
```

### Tests
```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter UserTest
php artisan test tests/Feature/PaymentTest.php

# Avec couverture
php artisan test --coverage --min=80
```

### Commandes Artisan Personnalisées
```bash
# Génération rapports
php artisan reports:generate-monthly --month=12 --year=2024

# Nettoyage données
php artisan cleanup:expired-tokens
php artisan cleanup:old-notifications

# Développement
php artisan make:service PaymentService
php artisan make:repository ClientRepository
```

---

## 🎨 FRONTEND

### Stack Frontend
- **Blade Templates** : Templating Laravel
- **Alpine.js** : Interactivité JavaScript
- **TailwindCSS** : Framework CSS
- **Vite** : Build tool moderne

### Composants Réutilisables
```php
// resources/views/components/
@include('components.client-card', ['client' => $client])
@include('components.progress-bar', ['percentage' => 75])
@include('components.photo-upload', ['entity' => 'client'])
```

### JavaScript Patterns
```javascript
// Alpine.js pour interactivité
<div x-data="{ open: false }" x-show="open">
    <!-- Contenu -->
</div>

// Fonctions globales
window.showSuccess = function(message) { /* Toast */ };
window.toggleFavorite = function(clientId, productId) { /* AJAX */ };
```

---

## 🔒 SÉCURITÉ

### Middlewares
```php
// app/Http/Middleware/
'auth'           // Authentification requise
'role:admin'     // Rôle spécifique (Spatie)
'verified'       // Email vérifié
'throttle:60,1'  // Rate limiting
```

### Validation
```php
// FormRequest classes
public function rules(): array {
    return [
        'email' => 'required|email|unique:users',
        'phone' => 'required|regex:/^229[0-9]{8}$/',
        'amount' => 'required|numeric|min:1',
        'photo' => 'image|max:2048'
    ];
}
```

### Activity Logs
```php
// Automatique via observer ou manuel
ActivityLog::log('create', 'Client', $client->id, null, $data);
```

---

## 🚀 DÉPLOIEMENT

### Production Checklist
```bash
# Optimisations
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache  
php artisan view:cache
npm run build

# Sécurité
php artisan config:clear  # Après changements .env
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Variables Environnement Production
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
```

---

*Guide technique v1.0*
