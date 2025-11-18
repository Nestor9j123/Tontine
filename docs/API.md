# 🔌 DOCUMENTATION API

## 📋 Introduction

L'API REST du système de gestion de tontines permet l'intégration avec des applications externes et le développement d'applications mobiles natives.

### Informations Générales
- **Base URL** : `https://your-domain.com/api`
- **Version** : v1
- **Format** : JSON
- **Authentification** : Bearer Token (Laravel Sanctum)
- **Rate Limiting** : 100 requêtes/minute par IP

---

## 🔐 AUTHENTIFICATION

### Obtenir un Token
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "agent@tontine.com",
    "password": "password123"
}
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "agent@tontine.com",
            "role": "agent"
        },
        "token": "1|abcd1234...",
        "expires_at": "2025-12-17T21:00:00Z"
    }
}
```

### Utiliser le Token
```http
Authorization: Bearer 1|abcd1234...
Accept: application/json
Content-Type: application/json
```

### Rafraîchir le Token
```http
POST /api/auth/refresh
Authorization: Bearer 1|abcd1234...
```

### Déconnexion
```http
POST /api/auth/logout
Authorization: Bearer 1|abcd1234...
```

---

## 👥 GESTION DES CLIENTS

### Lister les Clients
```http
GET /api/clients
Authorization: Bearer {token}
```

**Paramètres de requête :**
- `page` : Numéro de page (défaut: 1)
- `per_page` : Éléments par page (défaut: 15, max: 100)
- `search` : Recherche par nom/téléphone/email
- `agent_id` : Filtrer par agent (agents voient uniquement leurs clients)
- `is_active` : Filtrer par statut (true/false)

**Réponse :**
```json
{
    "success": true,
    "data": {
        "clients": [
            {
                "id": 1,
                "code": "CLI-000001",
                "first_name": "Jean",
                "last_name": "Dupont",
                "phone": "22961234567",
                "email": "jean@example.com",
                "is_active": true,
                "agent": {
                    "id": 2,
                    "name": "Agent Smith"
                },
                "tontines_count": 3,
                "photo_url": "https://domain.com/storage/clients/photo.jpg",
                "created_at": "2025-01-15T10:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 150,
            "last_page": 10
        }
    }
}
```

### Créer un Client
```http
POST /api/clients
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "first_name": "Marie",
    "last_name": "Martin",
    "phone": "22967890123",
    "phone_secondary": "22965432109",
    "email": "marie@example.com",
    "address": "123 Rue de la Paix",
    "city": "Cotonou",
    "id_card_number": "123456789",
    "agent_id": 2,
    "photo": [fichier image],
    "has_physical_notebook": true,
    "notebook_amount_paid": 150.00
}
```

### Détails d'un Client
```http
GET /api/clients/{id}
Authorization: Bearer {token}
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "client": {
            "id": 1,
            "code": "CLI-000001",
            "first_name": "Jean",
            "last_name": "Dupont",
            "full_name": "Jean Dupont",
            "phone": "22961234567",
            "phone_secondary": null,
            "email": "jean@example.com",
            "address": "456 Avenue des Martyrs",
            "city": "Porto-Novo",
            "id_card_number": "987654321",
            "is_active": true,
            "has_physical_notebook": true,
            "notebook_amount_paid": 300.00,
            "notebook_remaining": 0.00,
            "notebook_fully_paid": true,
            "photo_url": "https://domain.com/storage/clients/photo.jpg",
            "agent": {
                "id": 2,
                "name": "Agent Smith",
                "phone": "22967777777"
            },
            "tontines": [
                {
                    "id": 1,
                    "code": "TON-000001",
                    "status": "active",
                    "progress_percentage": 75.50,
                    "total_amount": 150000.00,
                    "paid_amount": 113250.00,
                    "remaining_amount": 36750.00,
                    "product": {
                        "id": 1,
                        "name": "iPhone 15 Pro",
                        "photo_url": "https://domain.com/storage/products/iphone.jpg"
                    }
                }
            ],
            "created_at": "2025-01-15T10:30:00Z",
            "updated_at": "2025-01-20T14:15:00Z"
        }
    }
}
```

### Modifier un Client
```http
PUT /api/clients/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "Marie-Claire",
    "phone": "22967890999",
    "address": "Nouvelle adresse"
}
```

---

## 📱 GESTION DES PRODUITS

### Lister les Produits
```http
GET /api/products
Authorization: Bearer {token}
```

**Paramètres :**
- `page`, `per_page` : Pagination
- `search` : Recherche par nom
- `is_active` : Filtrer par statut
- `in_stock` : Produits en stock uniquement
- `type` : Filtrer par type (daily/weekly/monthly/yearly)

**Réponse :**
```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "code": "PROD-000001",
                "name": "iPhone 15 Pro",
                "description": "Smartphone dernière génération...",
                "price": 850000.00,
                "formatted_price": "850 000 FCFA",
                "stock_quantity": 25,
                "type": "monthly",
                "duration_formatted": "12 mois",
                "is_active": true,
                "photos": [
                    {
                        "id": 1,
                        "path": "products/iphone-front.jpg",
                        "url": "https://domain.com/storage/products/iphone-front.jpg",
                        "is_primary": true
                    },
                    {
                        "id": 2,
                        "path": "products/iphone-back.jpg",
                        "url": "https://domain.com/storage/products/iphone-back.jpg",
                        "is_primary": false
                    }
                ],
                "primary_photo_url": "https://domain.com/storage/products/iphone-front.jpg",
                "created_at": "2025-01-10T09:00:00Z"
            }
        ]
    }
}
```

### Détails d'un Produit
```http
GET /api/products/{id}
Authorization: Bearer {token}
```

---

## 💰 GESTION DES TONTINES

### Lister les Tontines
```http
GET /api/tontines
Authorization: Bearer {token}
```

**Paramètres :**
- `client_id` : Tontines d'un client spécifique
- `agent_id` : Tontines d'un agent (agents voient uniquement les leurs)
- `status` : pending/active/completed
- `product_id` : Tontines pour un produit spécifique

### Créer une Tontine
```http
POST /api/tontines
Authorization: Bearer {token}

{
    "client_id": 1,
    "product_id": 1,
    "start_date": "2025-01-20",
    "duration_months": 12,
    "notes": "Tontine spéciale client VIP"
}
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "tontine": {
            "id": 15,
            "code": "TON-000015",
            "client_id": 1,
            "product_id": 1,
            "agent_id": 2,
            "status": "pending",
            "start_date": "2025-01-20",
            "total_amount": 850000.00,
            "paid_amount": 0.00,
            "remaining_amount": 850000.00,
            "total_payments": 12,
            "completed_payments": 0,
            "progress_percentage": 0.00,
            "client": {
                "id": 1,
                "full_name": "Jean Dupont"
            },
            "product": {
                "id": 1,
                "name": "iPhone 15 Pro",
                "price": 850000.00
            },
            "created_at": "2025-01-20T16:30:00Z"
        }
    },
    "message": "Tontine créée avec succès"
}
```

### Détails d'une Tontine
```http
GET /api/tontines/{id}
Authorization: Bearer {token}
```

---

## 💳 GESTION DES PAIEMENTS

### Lister les Paiements
```http
GET /api/payments
Authorization: Bearer {token}
```

**Paramètres :**
- `tontine_id` : Paiements d'une tontine spécifique
- `client_id` : Paiements d'un client
- `status` : pending/validated/rejected
- `date_from`, `date_to` : Filtrer par période
- `collected_by` : Paiements collectés par un agent

### Créer un Paiement Simple
```http
POST /api/payments
Authorization: Bearer {token}

{
    "tontine_id": 1,
    "client_id": 1,
    "amount": 75000.00,
    "payment_method": "cash",
    "notes": "Paiement effectué au domicile"
}
```

### Créer un Paiement Multiple
```http
POST /api/payments/multiple
Authorization: Bearer {token}

{
    "tontine_id": 1,
    "client_id": 1,
    "daily_amount": 2500.00,
    "days_count": 30,
    "payment_method": "cash",
    "notes": "Paiement échelonné sur 30 jours"
}
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "payment": {
            "id": 45,
            "reference": "PAY-67890ABCDE",
            "tontine_id": 1,
            "client_id": 1,
            "collected_by": 2,
            "amount": 75000.00,
            "daily_amount": null,
            "days_count": null,
            "is_multiple_payment": false,
            "payment_date": "2025-01-20",
            "payment_method": "cash",
            "status": "validated",
            "notes": "Paiement effectué au domicile",
            "auto_validated": true,
            "collector": {
                "id": 2,
                "name": "Agent Smith"
            },
            "created_at": "2025-01-20T14:30:00Z"
        },
        "tontine_updated": {
            "progress_percentage": 83.33,
            "paid_amount": 708333.33,
            "remaining_amount": 141666.67
        }
    },
    "message": "Paiement enregistré et validé automatiquement"
}
```

### Valider un Paiement
```http
POST /api/payments/{id}/validate
Authorization: Bearer {token}
```

### Rejeter un Paiement
```http
POST /api/payments/{id}/reject
Authorization: Bearer {token}

{
    "rejection_reason": "Montant incorrect, client a payé 50k au lieu de 75k"
}
```

---

## 🔔 NOTIFICATIONS

### Lister les Notifications
```http
GET /api/notifications
Authorization: Bearer {token}
```

**Paramètres :**
- `type` : payment_completed/low_stock/delivery_reminder
- `is_read` : true/false
- `is_delivered` : true/false (pour notifications de livraison)

**Réponse :**
```json
{
    "success": true,
    "data": {
        "notifications": [
            {
                "id": 1,
                "uuid": "550e8400-e29b-41d4-a716-446655440000",
                "type": "payment_completed",
                "title": "Paiements terminés",
                "message": "Le client Jean Dupont a terminé ses paiements pour iPhone 15 Pro...",
                "is_read": false,
                "is_delivered": false,
                "created_at": "2025-01-20T15:00:00Z",
                "tontine": {
                    "id": 1,
                    "code": "TON-000001",
                    "client": {
                        "full_name": "Jean Dupont",
                        "phone": "22961234567"
                    },
                    "product": {
                        "name": "iPhone 15 Pro",
                        "primary_photo_url": "https://domain.com/storage/products/iphone.jpg"
                    }
                }
            }
        ]
    }
}
```

### Marquer comme Lu
```http
POST /api/notifications/{uuid}/read
Authorization: Bearer {token}
```

### Marquer comme Livré
```http
POST /api/notifications/{uuid}/delivered
Authorization: Bearer {token}
```

---

## 📊 STATISTIQUES ET RAPPORTS

### Statistiques Dashboard
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "overview": {
            "total_clients": 150,
            "active_tontines": 89,
            "completed_tontines": 45,
            "monthly_revenue": 12500000.00,
            "pending_payments": 23
        },
        "agent_stats": {
            "my_clients": 25,
            "my_collections_today": 8,
            "my_collections_month": 145,
            "my_revenue_month": 2100000.00
        },
        "recent_activities": [
            {
                "type": "payment_created",
                "description": "Nouveau paiement de 50k FCFA - Marie Martin",
                "created_at": "2025-01-20T16:45:00Z"
            }
        ]
    }
}
```

### Rapport Mensuel
```http
GET /api/reports/monthly?year=2025&month=1
Authorization: Bearer {token}
```

---

## 🛠️ CODES D'ERREUR

### Codes HTTP Standard
- `200` : Succès
- `201` : Ressource créée
- `400` : Requête invalide
- `401` : Non authentifié
- `403` : Non autorisé
- `404` : Ressource non trouvée
- `422` : Erreurs de validation
- `429` : Rate limit dépassé
- `500` : Erreur serveur

### Format des Erreurs
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Les données fournies sont invalides",
        "details": {
            "email": ["Le champ email est obligatoire"],
            "phone": ["Le numéro de téléphone n'est pas valide"]
        }
    }
}
```

### Codes d'Erreur Spécifiques

| Code | Description |
|------|-------------|
| `AUTH_FAILED` | Échec d'authentification |
| `TOKEN_EXPIRED` | Token expiré |
| `VALIDATION_ERROR` | Erreurs de validation |
| `PERMISSION_DENIED` | Permissions insuffisantes |
| `RESOURCE_NOT_FOUND` | Ressource introuvable |
| `BUSINESS_RULE_VIOLATION` | Violation règle métier |
| `INSUFFICIENT_STOCK` | Stock insuffisant |
| `TONTINE_COMPLETED` | Tontine déjà terminée |
| `PAYMENT_ALREADY_VALIDATED` | Paiement déjà validé |

---

## 🔒 SÉCURITÉ

### Rate Limiting
- **Authentification** : 5 tentatives/minute
- **API générale** : 100 requêtes/minute
- **Upload fichiers** : 10 uploads/minute

### Permissions par Rôle

| Endpoint | Super Admin | Secrétaire | Agent |
|----------|-------------|------------|-------|
| `GET /clients` | Tous | Tous | Siens uniquement |
| `POST /clients` | ✅ | ✅ | ✅ |
| `PUT /clients/{id}` | ✅ | ✅ | Siens uniquement |
| `GET /products` | ✅ | ✅ | ✅ (lecture) |
| `POST /products` | ✅ | ✅ | ❌ |
| `POST /payments` | ✅ | ✅ | ✅ |
| `POST /payments/{id}/validate` | ✅ | ✅ | ≤100k uniquement |

### Validation et Sanitisation
- **Tous les inputs** sont validés et échappés
- **Upload de fichiers** : Types MIME vérifiés
- **SQL Injection** : Protection via Eloquent ORM
- **XSS** : Échappement automatique des sorties

---

## 📱 INTÉGRATION MOBILE

### Endpoints Spécialisés Mobile

#### Synchronisation Hors-ligne
```http
POST /api/mobile/sync
Authorization: Bearer {token}

{
    "last_sync": "2025-01-20T10:00:00Z",
    "offline_data": [
        {
            "type": "payment",
            "client_id": 1,
            "amount": 25000,
            "offline_id": "temp_001",
            "created_at": "2025-01-20T14:30:00Z"
        }
    ]
}
```

#### Upload Photos Compressées
```http
POST /api/mobile/photos/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "entity_type": "client",
    "entity_id": 1,
    "photo": [fichier compressé],
    "quality": 0.8
}
```

---

## 🧪 ENVIRONNEMENT DE TEST

### URL de Test
```
Base URL: https://test-api.tontine-system.com/api
```

### Comptes de Test
```json
{
    "super_admin": {
        "email": "admin@test.tontine.com",
        "password": "testpass123"
    },
    "secretary": {
        "email": "secretary@test.tontine.com",
        "password": "testpass123"
    },
    "agent": {
        "email": "agent@test.tontine.com",
        "password": "testpass123"
    }
}
```

### Collection Postman
Téléchargez notre collection Postman : [Tontine API Collection](https://api.postman.com/collections/tontine-system)

---

## 📚 SDK et Exemples

### JavaScript/Node.js
```javascript
const TontineAPI = require('@tontine/sdk-js');

const client = new TontineAPI({
    baseURL: 'https://your-domain.com/api',
    token: 'your-bearer-token'
});

// Créer un client
const newClient = await client.clients.create({
    first_name: 'Jean',
    last_name: 'Dupont',
    phone: '22961234567',
    agent_id: 2
});

// Enregistrer un paiement
const payment = await client.payments.create({
    tontine_id: 1,
    client_id: 1,
    amount: 50000
});
```

### PHP/Laravel
```php
use TontineSystem\SDK\TontineClient;

$client = new TontineClient([
    'base_url' => 'https://your-domain.com/api',
    'token' => 'your-bearer-token'
]);

// Lister les clients
$clients = $client->clients()->list([
    'per_page' => 20,
    'search' => 'Jean'
]);

// Créer une tontine
$tontine = $client->tontines()->create([
    'client_id' => 1,
    'product_id' => 1,
    'duration_months' => 12
]);
```

---

*Documentation API v1.0 - Dernière mise à jour : 17 novembre 2025*
