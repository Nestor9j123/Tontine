# 🎯 SYSTÈME DE GESTION DE TONTINES

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-8BC34A?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
</p>

<p align="center">
  <strong>Application web complète de gestion de tontines avec paiements échelonnés, suivi client et reporting automatisé</strong>
</p>

## 📋 Vue d'Ensemble

Système de gestion de tontines permettant aux clients d'acheter des produits via des paiements échelonnés. L'application offre un suivi complet depuis la création de la tontine jusqu'à la livraison du produit, avec gestion multi-rôles (Super Admin, Secrétaire, Agent).

### 🎯 Fonctionnalités Principales

- **Gestion Clients** : CRUD complet avec historique et géolocalisation
- **Catalogue Produits** : Photos multiples, gestion stock temps réel
- **Système Tontines** : Paiements échelonnés avec progression visuelle
- **Validation Hiérarchique** : Selon rôles et montants
- **Notifications Intelligentes** : Alertes automatisées
- **Rapports Automatisés** : Génération mensuelle programmée
- **Messagerie Interne** : Communication inter-équipes
- **Dashboard Analytique** : Métriques temps réel

## 🚀 Installation Rapide

```bash
# Cloner le projet
git clone [repository-url]
cd tontine-app

# Installer les dépendances
composer install
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate --seed

# Assets
npm run build

# Serveur de développement
php artisan serve
```

## 👥 Comptes par Défaut

```
Super Admin : admin@tontine.com / password123
Secrétaire  : secretaire@tontine.com / password123
Agent       : agent@tontine.com / password123
```

## 📚 Documentation

- **[Installation](docs/INSTALLATION.md)** - Guide d'installation détaillé
- **[Guide Utilisateur](docs/USER-GUIDE.md)** - Manuel d'utilisation
- **[Documentation API](docs/API.md)** - Endpoints et authentification
- **[Guide Développeur](docs/DEVELOPER.md)** - Architecture et contribution
- **[Déploiement](docs/DEPLOYMENT.md)** - Production et maintenance
- **[Cahier des Charges](CAHIER-DES-CHARGES.md)** - Spécifications complètes

## 🏗️ Stack Technique

- **Backend** : Laravel 9+ / PHP 8.1+
- **Frontend** : Blade Templates + Alpine.js
- **Styling** : TailwindCSS
- **Base de données** : MySQL/PostgreSQL
- **Authentification** : Laravel Breeze + Spatie Permissions
- **File Storage** : Laravel Storage
- **Task Scheduling** : Laravel Scheduler

## 🔧 Commandes Utiles

```bash
# Génération rapport mensuel
php artisan reports:generate-monthly

# Nettoyage cache
php artisan optimize:clear

# Reset développement
php artisan migrate:fresh --seed

# Queue worker
php artisan queue:work
```

## 📊 Modèles Principaux

- **User** - Agents, Secrétaires, Super Admin
- **Client** - Clients finaux avec tontines
- **Product** - Catalogue avec photos multiples
- **Tontine** - Contrats de paiement échelonné
- **Payment** - Paiements individuels avec validation
- **TontineNotification** - Système de notifications

## 🛡️ Sécurité

- Authentification Laravel Breeze
- Permissions basées sur les rôles (Spatie)
- Soft Delete sur toutes les entités
- Activity Logs complets
- CSRF Protection
- Validation côté client et serveur

## 📈 Performance

- Requêtes optimisées avec Eager Loading
- Cache intelligent (Config, Routes, Views)
- Pagination sur toutes les listes
- Lazy Loading des images
- Compression assets (Vite)

## 🧪 Tests

```bash
# Tests unitaires
php artisan test

# Tests avec couverture
php artisan test --coverage
```

## 🔄 Workflow de Développement

1. **Feature Branch** : `git checkout -b feature/nom-feature`
2. **Développement** : Code + tests
3. **Pull Request** : Review obligatoire
4. **Tests CI/CD** : Validation automatique
5. **Merge** : Vers main après validation

## 📞 Support

- **Email** : support@tontine-system.com
- **Documentation** : Voir dossier `/docs`
- **Issues** : GitHub Issues
- **Wiki** : Documentation communautaire

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

*Développé avec ❤️ pour la gestion moderne des tontines*
