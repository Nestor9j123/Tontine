# 🤝 GUIDE DE CONTRIBUTION

Merci de votre intérêt pour contribuer au système de gestion de tontines ! Ce guide vous explique comment participer efficacement au développement.

---

## 🎯 Façons de Contribuer

### 🐛 Signaler des Bugs
- Utilisez les [GitHub Issues](https://github.com/username/tontine-app/issues)
- Suivez le template de rapport de bug
- Incluez les étapes de reproduction détaillées
- Précisez votre environnement (OS, navigateur, version PHP)

### ✨ Proposer des Fonctionnalités
- Ouvrez une issue avec le label `enhancement`
- Décrivez le problème métier résolu
- Proposez une solution technique si possible
- Discutez avec la communauté avant d'implémenter

### 📝 Améliorer la Documentation
- Corrections de fautes de frappe
- Ajout d'exemples pratiques
- Traductions (nous accueillons toutes les langues)
- Amélioration des guides utilisateur

### 💻 Contribuer au Code
- Corrections de bugs
- Nouvelles fonctionnalités approuvées
- Optimisations de performance
- Amélioration des tests

---

## 🛠️ Configuration Développement

### Prérequis
- **PHP** 8.1+ avec extensions requises
- **Composer** 2.0+
- **Node.js** 16+ et npm
- **MySQL** ou **PostgreSQL**
- **Git** pour gestion de version

### Installation Locale
```bash
# Fork du projet sur GitHub puis clone
git clone https://github.com/your-username/tontine-app.git
cd tontine-app

# Installation dépendances
composer install
npm install

# Configuration environnement
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate --seed

# Assets et serveur
npm run dev
php artisan serve
```

### Branches de Travail
- **`main`** : Branche principale (production)
- **`develop`** : Branche de développement
- **`feature/nom-feature`** : Nouvelles fonctionnalités
- **`bugfix/nom-bug`** : Corrections de bugs
- **`hotfix/nom-hotfix`** : Corrections urgentes production

---

## 📋 Processus de Contribution

### 1. Préparation
```bash
# Fork du repository sur GitHub
# Clone de votre fork
git clone https://github.com/your-username/tontine-app.git

# Ajout du remote upstream
git remote add upstream https://github.com/original-username/tontine-app.git

# Création branche de travail
git checkout -b feature/ma-nouvelle-fonctionnalite
```

### 2. Développement
```bash
# Synchronisation avec upstream
git fetch upstream
git rebase upstream/develop

# Développement avec commits atomiques
git add .
git commit -m "feat: ajout système de favoris produits"

# Push réguliers
git push origin feature/ma-nouvelle-fonctionnalite
```

### 3. Tests Obligatoires
```bash
# Tests unitaires et fonctionnels
php artisan test

# Tests avec couverture minimum 80%
php artisan test --coverage --min=80

# Vérification code style (PSR-12)
vendor/bin/phpcs app/ --standard=PSR12

# Analyse statique
vendor/bin/phpstan analyse app/
```

### 4. Pull Request
1. **Titre explicite** : `feat: ajout système de favoris produits`
2. **Description détaillée** : Problème résolu, solution technique
3. **Tests** : Preuves que ça fonctionne
4. **Screenshots** : Si changements visuels
5. **Breaking Changes** : Si modifications incompatibles

---

## 📖 Standards de Code

### Conventions PHP
```php
<?php
// PSR-12 pour le formatage
// DocBlocks obligatoires pour classes et méthodes publiques

/**
 * Service de gestion des paiements tontines
 * 
 * @author Votre Nom <email@example.com>
 */
class PaymentService
{
    /**
     * Enregistre un nouveau paiement avec validation
     * 
     * @param array $data Données du paiement validées
     * @return Payment Instance du paiement créé
     * @throws ValidationException Si données invalides
     */
    public function createPayment(array $data): Payment
    {
        // Validation métier
        $this->validateBusinessRules($data);
        
        // Création avec transaction
        return DB::transaction(function () use ($data) {
            return Payment::create($data);
        });
    }
}
```

### Conventions Base de Données
```php
// Migrations : noms explicites avec timestamps
2025_11_17_create_client_favorites_table.php

// Modèles : Relations claires et scopes utiles
class Client extends Model
{
    // Fillable : sécurité explicit
    protected $fillable = ['name', 'email', 'phone'];
    
    // Casts : types appropriés
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime'
    ];
    
    // Relations : nommage intuitif
    public function tontines()
    {
        return $this->hasMany(Tontine::class);
    }
    
    // Scopes : réutilisabilité
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### Conventions Frontend
```php
{{-- Blade : Components réutilisables --}}
@component('components.card')
    @slot('title', 'Mes Tontines')
    
    <div class="space-y-4">
        @foreach($tontines as $tontine)
            @include('components.tontine-card', compact('tontine'))
        @endforeach
    </div>
@endcomponent

{{-- Alpine.js : Logique simple --}}
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" x-transition>Contenu</div>
</div>
```

### Git Commit Convention
```bash
# Format : type(scope): description
# Types : feat, fix, docs, style, refactor, test, chore

# Exemples corrects
git commit -m "feat(payments): ajout paiements échelonnés"
git commit -m "fix(tontines): correction calcul progression"
git commit -m "docs(api): mise à jour endpoints paiements"
git commit -m "test(clients): ajout tests création client"

# Breaking changes
git commit -m "feat(auth)!: migration vers Laravel Sanctum"
```

---

## 🧪 Tests et Qualité

### Tests Obligatoires
```php
<?php
// Tests unitaires pour logique métier
class PaymentServiceTest extends TestCase
{
    public function test_can_create_simple_payment()
    {
        $client = Client::factory()->create();
        $tontine = Tontine::factory()->create(['client_id' => $client->id]);
        
        $paymentData = [
            'tontine_id' => $tontine->id,
            'client_id' => $client->id,
            'amount' => 50000
        ];
        
        $payment = $this->paymentService->createPayment($paymentData);
        
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(50000, $payment->amount);
    }
}

// Tests fonctionnels pour workflows
class PaymentFlowTest extends TestCase
{
    public function test_agent_can_create_and_validate_small_payment()
    {
        $agent = User::factory()->create();
        $agent->assignRole('agent');
        
        $response = $this->actingAs($agent)
                         ->post('/payments', $this->validPaymentData());
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('payments', [
            'status' => 'validated', // Auto-validation ≤100k
            'collected_by' => $agent->id
        ]);
    }
}
```

### Couverture de Tests
- **Minimum requis** : 80% de couverture
- **Critique** : 95%+ pour logique métier
- **Tests obligatoires** : Tous les services et contrôleurs
- **Tests recommandés** : Modèles, middlewares, commandes

### Outils de Qualité
```bash
# Code style automatique
vendor/bin/php-cs-fixer fix app/

# Analyse statique
vendor/bin/phpstan analyse app/ --level=8

# Tests de sécurité  
composer audit

# Performance
php artisan route:list --columns=method,uri,name,action
```

---

## 📝 Documentation

### Code Documentation
```php
/**
 * Calcule la progression d'une tontine basée sur les paiements validés
 * 
 * Cette méthode prend en compte uniquement les paiements avec le statut
 * 'validated' pour calculer un pourcentage précis de completion.
 * 
 * @param Tontine $tontine Instance de tontine à analyser
 * @return float Pourcentage de progression (0.0 à 100.0)
 * 
 * @example
 * $progression = $this->calculateProgress($tontine);
 * // Retourne 75.5 pour une tontine à 75.5% de completion
 * 
 * @throws InvalidArgumentException Si tontine invalide
 * @since 1.2.0
 */
public function calculateProgress(Tontine $tontine): float
```

### README et Guides
- **Clarté** : Explications simples et exemples pratiques
- **Complétude** : Couvrir tous les cas d'usage
- **Mise à jour** : Synchronisation avec le code
- **Multi-langues** : Français prioritaire, anglais bienvenu

---

## 🔍 Code Review

### Checklist Reviewer
- [ ] **Fonctionnalité** : Résout le problème décrit
- [ ] **Tests** : Couverture adéquate et tests pertinents
- [ ] **Performance** : Pas de régression, optimisations
- [ ] **Sécurité** : Validation, permissions, échappement
- [ ] **Compatibilité** : Pas de breaking changes non documentés
- [ ] **Documentation** : Code et API documentés
- [ ] **UI/UX** : Interface cohérente et accessible

### Checklist Contributeur
- [ ] **Branche à jour** : Rebase sur develop récent
- [ ] **Tests passants** : Tous verts localement
- [ ] **Code style** : PSR-12 respecté
- [ ] **Commits propres** : Messages explicites et atomiques
- [ ] **Documentation** : README mis à jour si nécessaire

---

## 🏷️ Labels GitHub

### Types d'Issues
- `bug` : Dysfonctionnement confirmé
- `enhancement` : Nouvelle fonctionnalité
- `documentation` : Amélioration docs
- `question` : Demande d'aide ou clarification
- `duplicate` : Issue déjà existante
- `invalid` : Issue non valide ou hors scope

### Priorités
- `priority: critical` : Bloque la production
- `priority: high` : Important pour prochaine release
- `priority: medium` : Peut attendre
- `priority: low` : Nice to have

### Statuts
- `status: needs-discussion` : Require débat design
- `status: ready-for-dev` : Spec validée, peut être développée
- `status: in-progress` : En cours de développement
- `status: needs-review` : Attend code review

---

## 🎉 Reconnaissance

### Contributeurs
Tous les contributeurs sont listés dans le fichier [CONTRIBUTORS.md](CONTRIBUTORS.md) et reçoivent :
- **Crédit** dans les release notes
- **Mention** dans la documentation
- **Badge** contributeur sur le profil GitHub

### Types de Contributions Valorisées
- **Code** : Développement fonctionnalités et corrections
- **Documentation** : Guides, tutorials, traductions
- **Tests** : Amélioration couverture et qualité
- **Design** : UI/UX et expérience utilisateur
- **Community** : Support utilisateurs, modération

---

## 📞 Support et Communication

### Canaux Officiels
- **GitHub Issues** : Bugs, features, questions techniques
- **Discussions** : Design decisions, brainstorming
- **Email** : contribute@tontine-system.com
- **Discord** : Chat temps réel développeurs (lien à venir)

### Code de Conduite
Nous appliquons le [Contributor Covenant](https://www.contributor-covenant.org/fr/) :
- **Respect** : Envers tous les participants
- **Inclusivité** : Bienvenue à tous backgrounds
- **Constructivité** : Critiques techniques seulement
- **Professionnalisme** : Communication claire et respectueuse

---

## 📄 Licence et Copyright

En contribuant à ce projet, vous acceptez que vos contributions soient sous la même licence [MIT](LICENSE) que le projet principal.

Vos contributions restent vôtres, mais vous accordez au projet le droit de les utiliser, modifier et distribuer sous licence MIT.

---

**Merci de faire de ce projet un succès ! 🚀**

*Guide de contribution v1.0 - Mis à jour le 17 novembre 2025*
