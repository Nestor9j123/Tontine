# 📊 Système de Génération Automatique des Rapports Mensuels

## 🎯 Vue d'ensemble

Ce système génère automatiquement le rapport mensuel **chaque 28 du mois à 8h00** et envoie des notifications aux administrateurs pour les informer de la disponibilité du rapport.

## ⚙️ Fonctionnement Automatique

### 📅 Planification
- **Fréquence** : Chaque 28 du mois à 8h00
- **Commande** : `php artisan reports:generate-monthly`
- **Scheduler** : Laravel Task Scheduling

### 🔄 Processus Automatique

#### 1. **Vérification Quotidienne (28 de chaque mois)**
Le système vérifie s'il existe déjà un rapport pour le mois précédent :
- ✅ **Rapport inexistant** → Génération automatique
- ⚠️ **Rapport existant** → Notification de rappel (si pas consulté récemment)

#### 2. **Génération du Rapport**
Le système génère automatiquement :
- 📊 **Statistiques complètes** du mois écoulé
- 💰 **Chiffre d'affaires** et résultat financier
- 📦 **État des stocks** (initial vs final)
- 👥 **Performance des agents**
- 📈 **Analyse des paiements**

#### 3. **Notifications Automatiques**
Trois types de notifications sont envoyées :

##### 📊 **Rapport Généré Automatiquement**
- **Recipients** : Super admins + Secrétaires
- **Contenu** : Résumé financier + lien vers le rapport
- **Type** : `monthly_report_auto`

##### 🔔 **Rappel de Consultation** 
- **Conditions** : Rapport existant mais non consulté
- **Recipients** : Super admins + Secrétaires
- **Fréquence** : Maximum 1 par semaine
- **Type** : `monthly_report_reminder`

##### ❌ **Erreur de Génération**
- **Conditions** : Échec de la génération automatique
- **Recipients** : Super admins uniquement
- **Contenu** : Message d'erreur + demande d'intervention manuelle
- **Type** : `monthly_report_error`

## 🛠️ Utilisation Manuelle

### Commandes Disponibles

#### Génération Standard
```bash
php artisan reports:generate-monthly
```
Génère le rapport pour le mois précédent.

#### Génération pour un Mois Spécifique
```bash
php artisan reports:generate-monthly --month=10 --year=2024
```

#### Génération Forcée (Écrase l'existant)
```bash
php artisan reports:generate-monthly --month=10 --year=2024 --force
```

### Vérification du Planning
```bash
php artisan schedule:list
```
Affiche toutes les tâches planifiées.

## 📱 Gestion des Notifications

### Types de Notifications Rapport

| Type | Icône | Couleur | Description |
|------|-------|---------|-------------|
| `monthly_report_auto` | 📊 | Violet | Rapport généré automatiquement |
| `monthly_report_reminder` | 🔔 | Orange | Rappel de consultation |
| `monthly_report_error` | ❌ | Rouge | Erreur de génération |

### Actions Disponibles dans les Notifications

#### Pour les Notifications de Rapport Généré
- ✅ **Marquer comme lu**
- 👁️ **Voir les détails** → Page de détail de la notification
- 🗑️ **Supprimer** (admin/secretary uniquement)

#### Page de Détail
La page de détail d'une notification de rapport affiche :
- 📊 **Résumé financier**
- 🔗 **Lien direct** vers le rapport complet
- 📅 **Date et heure** de génération
- 👤 **Traçabilité** (qui a généré le rapport)

## 🔧 Configuration Technique

### Prérequis
- ✅ **Laravel Scheduler** configuré
- ✅ **Cron Job** configuré sur le serveur :
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Fichiers Modifiés
- `app/Console/Commands/GenerateMonthlyReportCommand.php` - Commande principale
- `app/Console/Kernel.php` - Planification des tâches
- `app/Models/TontineNotification.php` - Types de notifications étendus
- `resources/views/notifications/index.blade.php` - Interface notifications
- `resources/views/notifications/show.blade.php` - Page de détail

### Base de Données
- ✅ **Contrainte mise à jour** : Nouveaux types de notifications autorisés
- ✅ **Migration** : `2025_11_17_201738_update_notification_types_constraint`

## 📊 Intégration avec le Système de Notifications

### Pagination et Filtres
Les notifications de rapport s'intègrent parfaitement avec :
- 📄 **Pagination** (20 notifications par page)
- 🔍 **Filtres par type** (incluant les nouveaux types de rapport)
- 📈 **Statistiques** en temps réel
- 🎨 **Interface unifiée** avec badges colorés

### Permissions
- 👑 **Super Admin** : Peut tout voir, supprimer définitivement
- 👤 **Secretary** : Peut voir et supprimer (soft delete)
- 🕵️ **Agent** : Voit uniquement ses notifications

## 🚀 Avantages du Système

### ✨ Automatisation Complète
- ✅ **Aucune intervention manuelle** requise
- ✅ **Génération fiable** chaque mois
- ✅ **Notifications proactives** aux gestionnaires

### 📊 Traçabilité
- ✅ **Qui** a généré le rapport
- ✅ **Quand** le rapport a été généré
- ✅ **Erreurs** trackées et notifiées

### 🔔 Notifications Intelligentes
- ✅ **Résumé financier** dans la notification
- ✅ **Rappels automatiques** si non consulté
- ✅ **Gestion d'erreurs** avec notification des admins

### 🎯 UX Optimisée
- ✅ **Interface intuitive** avec icônes colorées
- ✅ **Actions contextuelles** selon les permissions
- ✅ **Intégration parfaite** avec le système existant

## 🔮 Prochaine Exécution

La prochaine génération automatique aura lieu le **28 de ce mois à 8h00**.

Vous pouvez vérifier le planning avec :
```bash
php artisan schedule:list
```

---

## 📞 Support

En cas de problème, vérifiez :
1. 🕐 **Cron job** configuré sur le serveur
2. 📧 **Configuration mail** pour les notifications d'erreur
3. 💾 **Espace disque** suffisant pour les logs
4. 🗄️ **Base de données** accessible

Les logs sont stockés dans `storage/logs/monthly-reports.log`.
