# 👤 GUIDE UTILISATEUR

## 🎯 Introduction

Ce guide détaille l'utilisation du système de gestion de tontines selon votre rôle. Chaque utilisateur a des permissions et fonctionnalités spécifiques.

---

## 🔐 Connexion et Premiers Pas

### Accès à la Plateforme
1. Rendez-vous sur l'URL de l'application
2. Cliquez sur "Se connecter"
3. Saisissez vos identifiants fournis par l'administrateur
4. Vous serez redirigé vers votre dashboard selon votre rôle

### Comptes de Démonstration
```
Super Admin : admin@tontine.com / password123
Secrétaire  : secretaire@tontine.com / password123
Agent       : agent@tontine.com / password123
```

---

## 👑 SUPER ADMINISTRATEUR

### Dashboard Principal
Le dashboard Super Admin affiche :
- **Statistiques globales** : CA, clients, tontines, stock
- **Graphiques de performance** : Évolutions mensuelles
- **Alertes système** : Stock faible, erreurs
- **Actions rapides** : Accès aux fonctions principales

### Gestion des Utilisateurs
**Navigation :** Menu → Utilisateurs

#### Créer un Utilisateur
1. Cliquez sur "Nouvel utilisateur"
2. Remplissez le formulaire :
   - **Nom complet** (obligatoire)
   - **Email** (unique, obligatoire)
   - **Téléphone** (optionnel)
   - **Adresse** (optionnel)
   - **Rôle** : Super Admin, Secrétaire, ou Agent
   - **Photo** (optionnel, drag & drop)
3. Cliquez "Enregistrer"
4. L'utilisateur recevra ses identifiants par email

#### Gérer les Rôles
- **Super Admin** : Accès total, gestion système
- **Secrétaire** : Gestion opérationnelle, validation illimitée
- **Agent** : Terrain, validation ≤ 100k FCFA

### Configuration Système
**Navigation :** Menu → Paramètres

#### Paramètres Globaux
- **Seuil stock faible** : Définit le niveau d'alerte
- **Devise par défaut** : FCFA
- **Timezone** : Africa/Porto-Novo
- **Notifications** : Types activés/désactivés

#### Sauvegarde et Maintenance
- **Export données** : Backup complet base de données
- **Nettoyage logs** : Purge automatique anciens logs
- **Cache système** : Optimisation performance

---

## 📝 SECRÉTAIRE

### Gestion des Clients

#### Créer un Client
**Navigation :** Menu → Clients → Nouveau Client

1. **Informations personnelles** :
   - Prénom, Nom (obligatoires)
   - Téléphones (principal obligatoire)
   - Email (optionnel mais recommandé)
   - Adresse complète
   - Numéro carte d'identité

2. **Photo du client** :
   - Drag & drop ou sélection fichier
   - Formats acceptés : JPG, PNG (max 2MB)
   - Redimensionnement automatique

3. **Assignment** :
   - Agent responsable (obligatoire)
   - Notes spéciales (optionnel)

4. **Carnet physique** :
   - Cocher si le client a un carnet
   - Montant déjà payé (sur 300 FCFA)

#### Rechercher un Client
- **Barre de recherche** : Nom, téléphone, email
- **Filtres avancés** : Agent, ville, statut
- **Export** : Liste clients en Excel/PDF

### Gestion des Produits

#### Ajouter un Produit
**Navigation :** Menu → Produits → Nouveau Produit

1. **Informations de base** :
   - Nom du produit (obligatoire)
   - Description détaillée
   - Code produit (généré automatiquement)

2. **Tarification** :
   - Prix de vente (obligatoire)
   - Prix d'achat (optionnel, pour calcul marge)
   - Type de tontine : Daily, Weekly, Monthly, Yearly

3. **Durée et paiements** :
   - Durée de la tontine (flexible)
   - Unité : jours, semaines, mois, années
   - Calcul automatique des échéances

4. **Photos multiples** :
   - Upload jusqu'à 10 photos par produit
   - Première photo = photo principale
   - Réorganisation par drag & drop
   - Formats : JPG, PNG, WebP (max 5MB)

5. **Stock** :
   - Quantité initiale
   - Seuil d'alerte personnalisé (optionnel)

#### Gestion du Stock
**Navigation :** Menu → Stock

- **Vue d'ensemble** : Tous produits avec niveaux stock
- **Alertes visuelles** : Rouge (rupture), Orange (faible), Vert (OK)
- **Mouvements** : Historique entrées/sorties
- **Ajustements** : Corrections manuelles avec motif

### Gestion des Tontines

#### Créer une Tontine
**Navigation :** Menu → Tontines → Nouvelle Tontine

1. **Sélection client** : Recherche et sélection
2. **Choix produit** : Catalogue avec filtres
3. **Configuration** :
   - Durée personnalisée (optionnel)
   - Date de début (défaut : aujourd'hui)
   - Notes spéciales
4. **Validation** : Vérification calculs automatiques

#### Suivi des Tontines
- **Tableau de bord** : Toutes tontines avec progression
- **Filtres** : Statut, agent, période, client
- **Actions** : Valider, modifier, voir détails
- **Export** : Rapports Excel/PDF

### Validation des Paiements

#### Processus de Validation
**Navigation :** Menu → Paiements → En attente

1. **Liste des paiements** : Triés par date/montant
2. **Détails paiement** : Clic pour voir détails complets
3. **Validation** :
   - **Valider** : Paiement accepté
   - **Rejeter** : Avec motif obligatoire
4. **Notifications** : Agent informé automatiquement

#### Règles de Validation
- **Secrétaire** : Peut valider tous montants
- **Vérifications automatiques** : Montant, client, tontine
- **Traçabilité** : Qui a validé, quand, pourquoi

---

## 🎒 AGENT

### Interface Terrain

#### Dashboard Agent
- **Mes statistiques** : Clients, collectes du jour/mois
- **Mes clients** : Liste clients assignés
- **Paiements en attente** : À valider/collecter
- **Notifications** : Alertes et livraisons

### Collecte de Paiements

#### Enregistrer un Paiement Simple
**Navigation :** Menu → Paiements → Nouveau Paiement

1. **Sélection client** : Vos clients uniquement
2. **Choix tontine** : Tontines actives du client
3. **Montant** : 
   - Saisie manuelle (≥ 1 FCFA)
   - Validation temps réel
4. **Date** : Automatiquement aujourd'hui
5. **Notes** : Optionnel (lieu, conditions, etc.)

#### Enregistrer un Paiement Multiple
**Pour paiements échelonnés sur plusieurs jours**

1. **Montant quotidien** : Exemple 1000 FCFA
2. **Nombre de jours** : Exemple 10 jours  
3. **Calcul automatique** : 10 000 FCFA total
4. **Validation** : Vérification cohérence

#### Validation Automatique
- **≤ 100 000 FCFA** : Validation automatique
- **> 100 000 FCFA** : Envoi vers secrétaire

### Gestion de vos Clients

#### Consulter un Client
**Navigation :** Menu → Mes Clients → [Nom Client]

- **Profil complet** : Infos personnelles, photo
- **Historique tontines** : Actives, terminées
- **Historique paiements** : Tous paiements avec statuts
- **Progression** : Barres visuelles par tontine

#### Modifier les Informations
- **Téléphones** : Mise à jour numéros
- **Adresse** : Changement domicile
- **Notes** : Observations terrain
- **Photo** : Nouvelle photo si nécessaire

### Livraisons

#### Notifications de Livraison
Quand une tontine est terminée :

1. **Notification reçue** : "Prêt à livrer"
2. **Détails produit** : Nom, photo, client
3. **Informations client** : Adresse, téléphone
4. **Action requise** : Se rendre chez le client

#### Marquer comme Livré
**Navigation :** Notifications → [Notification] → Détails

1. **Vérifier informations** : Client, produit, adresse
2. **Effectuer livraison** : Se rendre chez le client
3. **Cliquer "Marquer comme livré"**
4. **Confirmations automatiques** :
   - Tontine marquée livrée
   - Stock produit décrémenté
   - Client notifié (si email)

### Messagerie Interne

#### Envoyer un Message
**Navigation :** Menu → Messages → Nouveau Message

1. **Destinataire** : Secrétaires ou Super Admin uniquement
2. **Objet** : Titre clair du message
3. **Contenu** : Message détaillé
4. **Pièces jointes** : Photos, documents (optionnel)

#### Recevoir et Répondre
- **Notifications temps réel** : Badge sur menu Messages
- **Conversations** : Historique complet préservé
- **Statuts** : Lu/Non lu, répondu/en attente

---

## 🔔 SYSTÈME DE NOTIFICATIONS

### Types de Notifications

#### Pour les Agents
1. **Paiement validé** : Votre collecte a été approuvée
2. **Paiement rejeté** : Motif et actions correctives
3. **Livraison requise** : Tontine terminée → livrer produit
4. **Message reçu** : Nouveau message d'un secrétaire

#### Pour les Secrétaires
1. **Paiement en attente** : Nouveau paiement > 100k à valider
2. **Stock faible** : Produit sous seuil d'alerte
3. **Tontine terminée** : Paiements complets → organiser livraison
4. **Rapport mensuel** : Génération automatique disponible

#### Pour les Super Admin
1. **Toutes notifications** : Supervision globale
2. **Erreurs système** : Problèmes techniques
3. **Rapports automatiques** : Génération mensuelle
4. **Alertes sécurité** : Connexions suspectes

### Gestion des Notifications
- **Centre de notifications** : Menu → Notifications
- **Filtrage** : Par type, statut, période
- **Actions** : Marquer lu, archiver, supprimer
- **Rappels** : Notifications non traitées

---

## 📊 RAPPORTS ET STATISTIQUES

### Rapports Disponibles

#### Rapport Client (Secrétaires/Admin)
**Navigation :** Menu → Rapports → Clients
- **Nouveaux clients** : Par période
- **Clients actifs/inactifs**
- **Répartition par agent**
- **Performance collecte**

#### Rapport Produits (Secrétaires/Admin)
**Navigation :** Menu → Rapports → Produits
- **Produits populaires** : Plus vendus
- **Rotation stock** : Vitesse écoulement
- **Rentabilité** : Marges par produit
- **Prévisions** : Besoins réapprovisionnement

#### Rapport Mensuel Automatique (Admin)
**Généré automatiquement le 28 de chaque mois**
- **Chiffre d'affaires** : Évolution vs mois précédent
- **Performance agents** : Collectes, nouveaux clients
- **Statistiques paiements** : Validés, rejetés, en attente
- **État des stocks** : Mouvements, alertes
- **Notifications** : Envoyées automatiquement

### Export et Partage
- **Formats** : PDF, Excel, CSV
- **Personnalisation** : Filtres par période, agent, produit
- **Envoi email** : Partage automatique aux parties prenantes
- **Archivage** : Conservation historique tous rapports

---

## 📱 INTERFACE MOBILE

### Optimisations Agents Terrain
- **Design responsive** : Adapté smartphones/tablettes
- **Navigation simplifiée** : Actions essentielles accessibles
- **Upload photos** : Directement depuis caméra téléphone
- **Mode hors-ligne** : Synchronisation automatique à la reconnexion
- **Géolocalisation** : Enregistrement lieu de collecte (à venir)

### Fonctionnalités Mobiles
- **Scan QR codes** : Identification rapide clients (à venir)
- **Signature électronique** : Confirmation livraisons (à venir)
- **Chat en temps réel** : Communication équipe (à venir)

---

## ❓ FAQ et RÉSOLUTION DE PROBLÈMES

### Questions Fréquentes

**Q : Je ne peux pas uploader une photo**
R : Vérifiez le format (JPG/PNG) et la taille (max 2-5MB selon le type)

**Q : Mon paiement n'apparaît pas**
R : Vérifiez si le montant > 100k FCFA (validation secrétaire requise)

**Q : Je ne reçois pas de notifications**
R : Vérifiez vos paramètres de profil et autorisations navigateur

**Q : Erreur lors de la création d'une tontine**
R : Vérifiez que le produit a du stock disponible

### Problèmes Techniques Courants

**Problème : Page lente à charger**
Solution : Videz le cache navigateur (Ctrl+F5)

**Problème : Session expirée fréquemment**
Solution : Contactez l'administrateur pour ajuster la durée de session

**Problème : Impossible de se connecter**
Solution : 
1. Vérifiez vos identifiants
2. Contactez l'administrateur si compte bloqué
3. Utilisez la fonction "Mot de passe oublié"

---

## 📞 SUPPORT UTILISATEUR

### Contacts Support
- **Email technique** : support@tontine-system.com
- **Téléphone** : [Numéro à définir]
- **Heures d'ouverture** : Lun-Ven 8h-18h

### Escalade des Problèmes
1. **Niveau 1** : Agent → Secrétaire
2. **Niveau 2** : Secrétaire → Super Admin
3. **Niveau 3** : Super Admin → Support technique

### Formation et Accompagnement
- **Formation initiale** : Obligatoire pour tous nouveaux utilisateurs
- **Formations métier** : Spécialisées par rôle
- **Documentation** : Guides, tutoriels, FAQ
- **Support continu** : Assistance quotidienne équipes

---

*Ce guide est régulièrement mis à jour. Consultez la version en ligne pour les dernières fonctionnalités.*
