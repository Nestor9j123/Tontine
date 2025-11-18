# CAHIER DES CHARGES - SYSTÈME DE GESTION DE TONTINES

## 📋 PRÉSENTATION DU PROJET

### Contexte
Application web de gestion de tontines permettant aux clients d'acheter des produits via un système de paiements échelonnés, avec suivi complet par des agents commerciaux.

### Objectifs
- **Gestion complète des tontines** : de la création à la livraison
- **Suivi des paiements** : validation, historique, progression
- **Gestion des stocks** : alertes, mouvements, approvisionnement  
- **Reporting** : rapports mensuels automatisés
- **Communication** : messagerie interne et notifications
- **Multi-rôles** : Super Admin, Secrétaire, Agent

---

## 🎯 FONCTIONNALITÉS PRINCIPALES

### 1. GESTION DES CLIENTS
- **Création/Modification** : Informations personnelles complètes
- **Soft Delete** : Suppression logique avec possibilité de restauration
- **Historique** : Suivi des tontines et paiements
- **Carnets physiques** : Gestion des paiements carnets (300 FCFA)
- **Photos** : Upload et gestion des photos clients
- **Géolocalisation** : Adresses et villes

**Champs clients :**
```
- Code unique auto-généré
- Nom, Prénom, Téléphones
- Email, Adresse, Ville
- N° Carte d'identité
- Agent assigné
- Statut actif/inactif
- Carnet physique (montant payé/300)
- Paiements existants antérieurs
```

### 2. GESTION DES PRODUITS
- **Catalogue complet** : Nom, description, photos multiples
- **Tarification** : Prix vente, prix achat, marge bénéficiaire
- **Stock temps réel** : Quantités, seuils d'alerte, mouvements
- **Durée tontine** : Flexible (jours/semaines/mois/années)
- **Types** : Daily, Weekly, Monthly, Yearly
- **Soft Delete** : Archivage avec historique

**Champs produits :**
```
- Code unique auto-généré
- Nom, Description
- Photos multiples (galerie)
- Prix de vente/achat, Marge
- Stock actuel, Seuil d'alerte
- Durée de tontine configurable
- Type de paiement
- Statut actif/inactif
```

### 3. SYSTÈME DE TONTINES
- **Création automatisée** : Calcul automatique des échéances
- **Suivi progression** : Barres de progression visuelles
- **Statuts** : Pending → Active → Completed
- **Livraison** : Workflow complet avec notifications
- **Validation** : Processus d'approbation

**Workflow tontine :**
```
1. Création → Statut "pending"
2. Validation → Statut "active" 
3. Paiements progressifs → Mise à jour progression
4. Completion automatique → Statut "completed"
5. Notification livraison → Agent
6. Livraison → Décrémentation stock
```

### 4. GESTION DES PAIEMENTS
- **Types multiples** : Simple, Multiple (étalé)
- **Validation hiérarchique** : Agent (≤100k), Secrétaire (illimité)
- **Statuts** : Pending → Validated/Rejected
- **Traçabilité** : Collecteur, validateur, dates
- **Automatisation** : Date automatique (aujourd'hui)

**Règles de validation :**
```
- Agent : Peut valider ≤ 100 000 FCFA
- Secrétaire : Validation illimitée
- Super Admin : Tous pouvoirs
- Auto-validation agents selon montant
```

---

## 🏗️ ARCHITECTURE TECHNIQUE

### Stack Technologique
- **Backend** : Laravel 9+ (PHP 8.1+)
- **Frontend** : Blade Templates + Alpine.js
- **CSS** : TailwindCSS
- **Base de données** : MySQL/PostgreSQL
- **Authentification** : Laravel Breeze + Spatie Permissions
- **File Storage** : Laravel Storage (local/S3)
- **Scheduler** : Laravel Cron Jobs

### Structure Modulaire
```
app/
├── Models/           # 15 modèles principaux
├── Controllers/      # Contrôleurs par fonctionnalité
├── Middleware/       # Sécurité et permissions
├── Console/Commands/ # Commandes automatisées
├── Services/         # Services métier
└── Notifications/    # Système de notifications
```

---

## 📊 MODÈLES DE DONNÉES

### Entités Principales
1. **User** (Agents/Secrétaires/Admin)
2. **Client** (Clients finaux)
3. **Product** (Catalogue produits)
4. **Tontine** (Contrats de paiement)
5. **Payment** (Paiements individuels)
6. **TontineNotification** (Notifications système)

### Relations Clés
```
User (1) → (N) Client [agent_id]
Client (1) → (N) Tontine [client_id] 
Tontine (N) → (1) Product [product_id]
Tontine (1) → (N) Payment [tontine_id]
Product (1) → (N) ProductPhoto [product_id]
Product (1) → (N) StockMovement [product_id]
```

### Fonctionnalités Avancées
- **Soft Deletes** : Tous les modèles principaux
- **UUID** : Identifiants sécurisés pour routes
- **Activity Logs** : Traçabilité complète des actions
- **Timestamps** : Suivi création/modification

---

## 👥 SYSTÈME DE RÔLES

### Super Admin
- **Permissions** : Accès total système
- **Fonctionnalités** :
  - Gestion utilisateurs et rôles
  - Configuration système
  - Rapports globaux
  - Suppression définitive
  - Restauration données

### Secrétaire  
- **Permissions** : Gestion opérationnelle
- **Fonctionnalités** :
  - CRUD Clients/Produits/Tontines
  - Validation paiements illimitée
  - Gestion stock et notifications
  - Rapports mensuels
  - Messagerie interne

### Agent
- **Permissions** : Terrain et collecte
- **Fonctionnalités** :
  - Gestion clients assignés
  - Collecte paiements
  - Validation ≤ 100k FCFA
  - Messagerie avec hiérarchie
  - Livraisons produits

---

## 🎨 INTERFACES UTILISATEUR

### Dashboard Principal
- **Statistiques temps réel** : CA, paiements, stock
- **Graphiques** : Évolution mensuelle, performance
- **Alertes** : Stock faible, paiements en retard
- **Actions rapides** : Création client/tontine/paiement

### Gestion des Clients
- **Liste paginée** : Filtres avancés, recherche
- **Formulaires** : Création/modification avec validation
- **Profil détaillé** : Historique, tontines, paiements
- **Upload photos** : Drag & drop avec prévisualisation

### Catalogue Produits
- **Galerie photos** : Upload multiple, réorganisation
- **Gestion stock** : Seuils, mouvements, alertes
- **Configuration tontines** : Durées flexibles
- **Statistiques** : Popularité, rentabilité

### Suivi des Tontines
- **Tableaux de bord** : Progression visuelle
- **Filtres dynamiques** : Statut, agent, période
- **Actions en lot** : Validation multiple
- **Export données** : PDF, Excel

---

## 🔔 SYSTÈME DE NOTIFICATIONS

### Types de Notifications
1. **payment_completed** : Tontine terminée → Livraison
2. **low_stock** : Alerte stock faible
3. **delivery_reminder** : Rappel livraison
4. **monthly_report_auto** : Rapport généré automatiquement
5. **monthly_report_reminder** : Rappel consultation rapport
6. **monthly_report_error** : Erreur génération rapport

### Fonctionnalités
- **Notification centralisée** : Interface unifiée
- **Marquage livraison** : Décrémentation stock automatique
- **Soft delete** : Archivage avec restauration
- **Permissions** : Actions selon rôles
- **Session unique** : Affichage unique par session

---

## 📈 RAPPORTS ET STATISTIQUES

### Rapports Mensuels Automatisés
- **Génération automatique** : 28 de chaque mois à 8h00
- **Contenu complet** :
  - Chiffre d'affaires et dépenses
  - Performance agents
  - Évolution stock
  - Statistiques paiements
- **Notifications proactives** : Admin informés automatiquement
- **Gestion erreurs** : Notifications d'échec

### Tableaux de Bord
- **Métriques temps réel** : KPI principaux
- **Graphiques interactifs** : Évolutions, comparaisons
- **Filtres personnalisables** : Période, agent, produit
- **Export** : PDF, Excel, impression

---

## 🛡️ SÉCURITÉ

### Authentification
- **Laravel Breeze** : Login/Register sécurisé
- **Middleware** : Contrôle d'accès par route
- **Sessions** : Gestion automatique déconnexion
- **CSRF Protection** : Formulaires protégés

### Permissions
- **Spatie/Laravel-Permission** : Système de rôles avancé
- **Guards** : Séparation admin/client (si nécessaire)
- **Middleware personnalisés** : Contrôles spécifiques

### Données
- **Soft Delete** : Pas de suppression définitive
- **Activity Logs** : Traçabilité toutes actions
- **Validation** : Côté client et serveur
- **File Upload** : Sécurisation téléchargements

---

## 🔄 WORKFLOWS MÉTIER

### Création Tontine
```
1. Agent sélectionne Client + Produit
2. Système calcule échéancier automatique
3. Tontine créée en statut "pending"
4. Validation secrétaire → "active"
5. Notification client et agent
```

### Cycle de Paiement
```
1. Agent collecte paiement terrain
2. Saisie dans système (date auto = aujourd'hui)
3. Validation selon montant :
   - ≤100k : Auto-validation agent
   - >100k : Validation secrétaire
4. Mise à jour progression tontine
5. Si complete → Notification livraison
```

### Processus Livraison
```
1. Tontine terminée → Notification agent
2. Agent consulte notification détaillée
3. "Marquer comme livré" → Actions automatiques :
   - Statut tontine → "delivered"
   - Stock produit décrémenté -1
   - Mouvement stock tracé
   - Notification marquée livrée
```

---

## 🚀 FONCTIONNALITÉS AVANCÉES

### Messaging Interne
- **Conversations privées** : User-to-user
- **Permissions hiérarchiques** : Selon rôles
- **Temps réel** : Mise à jour automatique
- **Historique** : Conservation messages

### Gestion Stock Intelligente
- **Seuils configurables** : Par produit ou global
- **Mouvements tracés** : Historique complet
- **Alertes proactives** : Notifications automatiques
- **Rapports** : Évolution, rotation stock

### Système de Recherche
- **Recherche globale** : Clients, produits, tontines
- **Filtres avancés** : Multi-critères
- **Suggestions** : Auto-complétion
- **Historique** : Recherches récentes

---

## 📱 RESPONSIVE DESIGN

### Compatibilité
- **Desktop** : Interface complète optimisée
- **Tablet** : Adaptation layout fluide
- **Mobile** : Version simplifiée agents terrain

### Technologies
- **TailwindCSS** : Framework CSS utilitaire
- **Alpine.js** : Interactivité légère
- **Responsive Grid** : Adaptation automatique écrans

---

## ⚙️ CONFIGURATION SYSTÈME

### Paramètres Globaux
- **Seuil stock faible** : Configurable globalement
- **Durées tontines** : Templates par défaut
- **Notifications** : Fréquences, types activés
- **Rapports** : Planning génération automatique

### Variables d'Environnement
```
DB_CONNECTION=mysql
MAIL_MAILER=smtp
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
APP_TIMEZONE=Africa/Porto-Novo
```

---

## 🔧 MAINTENANCE ET ÉVOLUTIONS

### Commandes Artisan
- `reports:generate-monthly` : Génération manuelle rapports
- `queue:work` : Traitement jobs asynchrones
- `storage:link` : Liaison stockage public
- `migrate:fresh --seed` : Reset développement

### Logs et Monitoring
- **Laravel Logs** : storage/logs/
- **Activity Logs** : Base de données
- **Error Tracking** : Notifications admin
- **Performance** : Requêtes optimisées

### Évolutions Prévues
- **API REST** : Mobile app native
- **Dashboard Client** : Interface dédiée clients
- **Paiements électroniques** : Intégration gateways
- **Géolocalisation** : Suivi livraisons GPS

---

## 📋 DONNÉES DE TEST

### Utilisateurs par Défaut
```
Super Admin: admin@tontine.com / password123
Secrétaire: secretaire@tontine.com / password123  
Agent: agent@tontine.com / password123
```

### Données d'Exemple
- **50+ Clients** : Répartis par agents
- **20+ Produits** : Avec photos multiples
- **100+ Tontines** : Différents statuts
- **500+ Paiements** : Historique complet

---

## 🎯 INDICATEURS DE PERFORMANCE

### KPI Métier
- **Taux conversion** : Prospects → Clients
- **Panier moyen** : Montant tontines
- **Délai paiement** : Respect échéances
- **Satisfaction** : Livraisons dans les temps

### KPI Techniques
- **Temps réponse** : < 2s pages principales
- **Disponibilité** : 99.9% uptime
- **Sécurité** : 0 vulnérabilité critique
- **Performance** : Optimisation requêtes DB

---

*Document généré le 17 novembre 2025*  
*Version 1.0 - Système de Gestion de Tontines*
