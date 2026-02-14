# 🏢 SENELEC DAPT/NAPT - Architecture Technique V2

> **Application de gestion des Demandes d'Arrêt Pour Travaux (DAPT) et Notes d'Arrêt Pour Travaux (NAPT) pour SENELEC**

*Document mis à jour le 14/02/2026 - Tests ajoutés*

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Stack technique](#stack-technique)
3. [Architecture des dossiers](#architecture-des-dossiers)
4. [Base de données](#base-de-données)
5. [Modèles de données](#modèles-de-données)
6. [Rôles et permissions](#rôles-et-permissions)
7. [Workflow DAPT/NAPT](#workflow-daptnapt)
8. [Routes et contrôleurs](#routes-et-contrôleurs)
9. [Système de notifications](#système-de-notifications)
10. [Système d'intérim](#système-dintérim)
11. [Intégrations externes](#intégrations-externes)
12. [Génération PDF](#génération-pdf)
13. [Envoi d'emails](#envoi-demails)
14. [Nouvelles fonctionnalités](#nouvelles-fonctionnalités)
15. [Commandes Artisan](#commandes-artisan)
16. [Tests Unitaires et Fonctionnels](#tests-unitaires-et-fonctionnels)

---

## 🎯 Vue d'ensemble

### Description
Application web Laravel pour la gestion complète du cycle de vie des demandes d'arrêt pour travaux (DAPT) et des notes d'arrêt pour travaux (NAPT) au sein de SENELEC.

### Fonctionnalités principales
- ✅ Création et suivi des DAPT par les demandeurs
- ✅ Traitement des DAPT par le DESA (acceptation/retour)
- ✅ Création des NAPT à partir des DAPT acceptées
- ✅ Workflow de vérification et validation des NAPT
- ✅ Exécution des NAPT par les opérateurs
- ✅ Système d'intérim pour les absences
- ✅ Notifications internes et par email
- ✅ Diffusion hebdomadaire des NAPT par groupe
- ✅ Génération automatique de PDF
- ✅ Historique complet des modifications
- ✅ Authentification LDAP avec synchronisation Oracle HR
- ✅ **Recherche globale** (DAPT, NAPT, utilisateurs)
- ✅ **Export Excel** avec filtres avancés
- ✅ **Calendrier des travaux** interactif
- ✅ **Indicateurs de performance (KPI)**
- ✅ **Fil de discussion** sur DAPT/NAPT
- ✅ **Tutoriel d'onboarding** pour nouveaux utilisateurs
- ✅ **Rappels automatiques** par email
- ✅ **Notifications push** navigateur

---

## 🛠️ Stack technique

### Backend
| Technologie | Version | Usage |
|-------------|---------|-------|
| PHP | 8.3+ | Langage serveur |
| Laravel | 10.x | Framework PHP |
| MySQL | 8.0+ | Base de données principale |
| Oracle | - | Synchronisation utilisateurs (HR) |
| SQL Server | - | Données GMAO (équipements) |

### Frontend
| Technologie | Version | Usage |
|-------------|---------|-------|
| Tailwind CSS | 3.x | Framework CSS |
| Alpine.js | 3.x | Interactivité JavaScript |
| Blade | - | Moteur de templates Laravel |
| Vite | 5.x | Build tool |
| FullCalendar | 6.x | Calendrier interactif |

### Packages Laravel principaux
```json
{
  "spatie/laravel-permission": "^6.0",     // Gestion des rôles
  "directorytree/ldaprecord-laravel": "^3.0", // Auth LDAP
  "barryvdh/laravel-dompdf": "^2.0",       // Génération PDF
  "yajra/laravel-oci8": "^10.0",           // Connexion Oracle
  "maatwebsite/excel": "^3.1"              // Export Excel
}
```

---

## 📁 Architecture des dossiers

```
sendaptnapt/
├── app/
│   ├── Console/Commands/           # Commandes Artisan
│   │   ├── SyncOracleUsers.php     # Synchronisation utilisateurs Oracle/LDAP
│   │   ├── SendReminders.php       # Rappels automatiques
│   │   └── UpdateDemandesDescriptions.php
│   │
│   ├── Exports/                    # Classes d'export Excel
│   │   ├── DaptExport.php
│   │   └── NaptExport.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Administration
│   │   │   │   ├── AbsenceController.php
│   │   │   │   ├── ChargeConsController.php
│   │   │   │   ├── CorrespondantController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DemandeController.php
│   │   │   │   ├── GroupeController.php
│   │   │   │   ├── ImpersonateController.php
│   │   │   │   ├── NoteController.php
│   │   │   │   ├── ObservationController.php
│   │   │   │   ├── ServiceDestController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── UserSyncController.php
│   │   │   │
│   │   │   ├── Api/                # API interne
│   │   │   │   └── GmaoController.php
│   │   │   │
│   │   │   ├── Auth/               # Authentification
│   │   │   │   └── LoginController.php
│   │   │   │
│   │   │   ├── Demandeur/          # Espace demandeur
│   │   │   │   ├── DemandeController.php
│   │   │   │   └── ObservationController.php
│   │   │   │
│   │   │   ├── Desa/               # Espace DESA
│   │   │   │   ├── DemandeController.php
│   │   │   │   ├── NoteController.php
│   │   │   │   └── ObservationController.php
│   │   │   │
│   │   │   ├── Directeur/          # Espace Directeur
│   │   │   │   └── DirecteurController.php
│   │   │   │
│   │   │   ├── Operateur/          # Espace Opérateur
│   │   │   │   └── NoteController.php
│   │   │   │
│   │   │   ├── OperateurChef/      # Espace Opérateur Chef
│   │   │   │   └── NoteController.php
│   │   │   │
│   │   │   ├── Valideur/           # Espace Valideur
│   │   │   │   └── NoteController.php
│   │   │   │
│   │   │   ├── Verificateur/       # Espace Vérificateur
│   │   │   │   └── NoteController.php
│   │   │   │
│   │   │   ├── CalendrierController.php    # Calendrier des travaux
│   │   │   ├── CommentController.php       # Fil de discussion
│   │   │   ├── DocumentationController.php
│   │   │   ├── ExportController.php        # Exports Excel
│   │   │   ├── MesAbsencesController.php
│   │   │   ├── MesObservationsController.php
│   │   │   ├── NotificationController.php
│   │   │   └── SearchController.php        # Recherche globale
│   │   │
│   │   └── Middleware/
│   │       └── RoleOrInterimMiddleware.php
│   │
│   ├── Ldap/
│   │   └── LdapAttributeHandler.php
│   │
│   ├── Mail/
│   │   ├── NaptWeeklyDiffusionMail.php
│   │   └── StatutDemandeMail.php
│   │
│   ├── Models/
│   │   ├── Absence.php             # Gestion des absences/intérims
│   │   ├── ChargeCons.php          # Chargés de consignation
│   │   ├── ChargeTravaux.php       # Chargés de travaux externes
│   │   ├── Comment.php             # Commentaires/discussions
│   │   ├── Correspondant.php       # Correspondants
│   │   ├── Demande.php             # DAPT
│   │   ├── DemandeHistory.php      # Historique DAPT
│   │   ├── Groupe.php              # Groupes de diffusion
│   │   ├── Note.php                # NAPT
│   │   ├── NoteHistory.php         # Historique NAPT
│   │   ├── Observation.php         # Feedback/Observations
│   │   ├── ServiceDest.php         # Services destinataires
│   │   └── User.php                # Utilisateurs
│   │
│   ├── Notifications/
│   │   └── WorkflowNotification.php
│   │
│   └── Services/
│       ├── NotificationService.php
│       ├── OracleHRService.php
│       └── RoleAssignmentService.php
│
├── database/
│   ├── migrations/                  # Migrations
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── ChargesConsSeeder.php
│       ├── CorrespondantsSeeder.php
│       ├── GroupesSeeder.php
│       ├── RolesAndPermissionsSeeder.php
│       └── ServiceDestSeeder.php
│
├── public/
│   ├── img/                         # Images (logo, etc.)
│   └── sw.js                        # Service Worker notifications push
│
├── resources/views/
│   ├── admin/                       # Vues administration
│   ├── auth/                        # Login
│   ├── calendrier/                  # Calendrier des travaux
│   │   └── index.blade.php
│   ├── components/                  # Composants réutilisables
│   │   ├── comments.blade.php       # Fil de discussion
│   │   ├── kpi-widget.blade.php     # Indicateurs performance
│   │   ├── onboarding-tutorial.blade.php  # Tutoriel
│   │   ├── push-notifications.blade.php   # Notifications push
│   │   └── recent-activity.blade.php      # Activité récente
│   ├── demandeur/                   # Vues demandeur
│   ├── desa/                        # Vues DESA
│   ├── directeur/                   # Vues directeur
│   ├── documentation/               # Documentation utilisateur
│   ├── emails/                      # Templates emails
│   │   ├── napt-weekly-diffusion.blade.php
│   │   ├── statut-demande.blade.php
│   │   └── workflow-notification.blade.php
│   ├── exports/                     # Page d'export Excel
│   │   └── index.blade.php
│   ├── layouts/                     # Layouts principaux
│   ├── mes-absences/                # Gestion absences
│   ├── mes-observations/            # Feedback utilisateur
│   ├── notifications/               # Centre de notifications
│   ├── operateur/                   # Vues opérateur
│   ├── operateurchef/               # Vues opérateur chef
│   ├── pdf/                         # Templates PDF
│   │   ├── dapt.blade.php
│   │   ├── dapt-combined.blade.php
│   │   ├── napt.blade.php
│   │   └── napt-combined.blade.php
│   ├── profile/                     # Profil et signature
│   ├── search/                      # Recherche globale
│   │   └── index.blade.php
│   ├── valideur/                    # Vues valideur
│   ├── verificateur/                # Vues vérificateur
│   └── vendor/mail/                 # Templates emails personnalisés
│
├── routes/
│   └── web.php                      # Routes principales (~100 routes)
│
└── config/
    ├── database.php                 # Connexions MySQL, Oracle, SQL Server
    ├── ldap.php                     # Configuration LDAP
    └── permission.php               # Spatie permissions
```

---

## 🗄️ Base de données

### Tables principales

| Table | Description |
|-------|-------------|
| `users` | Utilisateurs (LDAP + Oracle HR) |
| `demandes` | Demandes d'Arrêt Pour Travaux (DAPT) |
| `notes` | Notes d'Arrêt Pour Travaux (NAPT) |
| `demande_histories` | Historique modifications DAPT |
| `note_histories` | Historique modifications NAPT |
| `absences` | Gestion des absences et intérims |
| `groupes` | Groupes de diffusion |
| `correspondants` | Correspondants terrain |
| `charges_cons` | Chargés de consignation |
| `services_dest` | Services destinataires |
| `charges_travaux` | Chargés de travaux externes |
| `observations` | Feedback/Remarques utilisateurs |
| `comments` | Fil de discussion DAPT/NAPT |

### Tables pivot

| Table | Relation |
|-------|----------|
| `note_service` | Note ↔ ServiceDest |
| `note_correspondant` | Note ↔ Correspondant |
| `note_charge_consignation` | Note ↔ ChargeCons |

### Tables Spatie Permissions

| Table | Usage |
|-------|-------|
| `roles` | Définition des rôles |
| `permissions` | Définition des permissions |
| `model_has_roles` | Attribution rôles → utilisateurs |
| `model_has_permissions` | Attribution permissions → utilisateurs |
| `role_has_permissions` | Attribution permissions → rôles |

### Connexions configurées

```php
// config/database.php
'connections' => [
    'mysql' => [...],           // Base principale
    'oracle' => [...],          // Oracle HR (utilisateurs)
    'sqlsrv_gmao' => [...],     // SQL Server GMAO (équipements)
]
```

---

## 📊 Modèles de données

### User (Utilisateur)

```php
class User extends Authenticatable implements LdapAuthenticatable
{
    // Traits
    use HasApiTokens, HasFactory, Notifiable, HasRoles, AuthenticatesWithLdap;

    // Champs principaux
    - id, name, email, password
    - matricule, ldap_username, ldap_guid
    - nom, prenom, poste, telephone, photo
    - organisation, entreprise, service, direction, departement
    - oracle_person_id, fonction_oracle, oracle_synced_at
    - signature, stamp
    - groupe_id, is_active
    - onboarding_completed  // Tutoriel complété

    // Relations
    - groupe() : BelongsTo Groupe
    - demandes() : HasMany Demande
    - absences() : HasMany Absence
    - interims() : HasMany Absence
    - notesEtablies(), notesVerifiees(), notesValidees()

    // Méthodes intérim
    - estInterimaireA(string $role) : bool
    - hasRoleOrInterim(string $role) : bool
    - absentRemplace(string $role) : ?User
    - getRolesInterimActifs() : array
}
```

### Demande (DAPT)

```php
class Demande extends Model
{
    // Constantes de statut
    const STATUT_CREEE = 'créée';
    const STATUT_EN_COURS = 'en cours de traitement';
    const STATUT_ACCEPTEE = 'acceptée';
    const STATUT_RETOURNEE = 'retournée';
    const STATUT_BROUILLON = 'brouillon';

    // Modes de saisie
    const MODE_GMAO = 'gmao';      // Données depuis SQL Server
    const MODE_MANUEL = 'manuel';   // Saisie libre

    // Champs principaux
    - id, numero_demande (auto: GROUPE-XXXXX-YYYY)
    - date, statut, mode_saisie
    - demandeur_id, charge_travaux_id, charge_travaux_externe_id, traite_id
    - destinataire, lieu_execution, lieu_code
    - designation, renseignement
    - ouvrage_type (ligne/poste)
    - mte, mcce, etape (ue/de)
    
    // Dates prévues
    - ddp, hdp (début), dfp, hfp (fin), dmrp

    // Dates acceptées
    - dda, hda (début), dfa, hfa (fin), dmra

    // Mode GMAO (JSON)
    - ouvrages_consigner_gmao, ouvrages_installer_gmao

    // Mode Manuel (texte)
    - ouvrages_consigner_manuel, ouvrages_installer_manuel

    // Relations
    - demandeur() : BelongsTo User
    - chargeTravaux() : BelongsTo User
    - chargeTravauxExterne() : BelongsTo ChargeTravaux
    - traite() : BelongsTo User
    - note() : HasOne Note
    - histories() : HasMany DemandeHistory
    - comments() : MorphMany Comment
}
```

### Note (NAPT)

```php
class Note extends Model
{
    // Constantes de statut
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EN_ETUDE = 'en étude';
    const STATUT_EN_ATTENTE_VERIFICATION = 'en attente de vérification';
    const STATUT_VERIFIEE = 'vérifiée';
    const STATUT_EN_ATTENTE_VALIDATION = 'en attente de validation';
    const STATUT_VALIDEE = 'validée';
    const STATUT_EN_COURS_EXECUTION = 'en cours d\'exécution';
    const STATUT_EXECUTEE = 'executée';
    const STATUT_RETOURNEE = 'retournée';
    const STATUT_ANNULEE = 'annulée';

    // Champs principaux
    - id, numero_note (auto: XXXXX-YYYY)
    - numero_semaine, date
    - demande_id, statut

    // Acteurs avec signatures
    - etabli_id, verifie_id, valide_id
    - retourne1_id, retourne2_id
    - execute_id, en_cours_execution_id, annule_id

    // Dates
    - dre (date réelle début)
    - ddt, dft (début/fin travaux)
    - drex (date réelle exécution)

    // Documents
    - document, etude, fiche_manoeuvre

    // Informations
    - renseignementN, motif, motifbis, commentanul

    // Relations Many-to-Many
    - chargesConsignation() : BelongsToMany ChargeCons
    - correspondants() : BelongsToMany Correspondant
    - services() : BelongsToMany ServiceDest
    - comments() : MorphMany Comment
}
```

### Comment (Fil de discussion)

```php
class Comment extends Model
{
    // Champs
    - id
    - commentable_type, commentable_id  // Polymorphique (Demande/Note)
    - user_id
    - content
    - parent_id  // Réponses imbriquées
    - is_internal  // Note interne (non visible par demandeur)

    // Relations
    - commentable() : MorphTo
    - user() : BelongsTo User
    - parent() : BelongsTo Comment
    - replies() : HasMany Comment

    // Scopes
    - root()  // Commentaires sans parent
    - public()  // Visibles par tous
}
```

### Absence (Intérim)

```php
class Absence extends Model
{
    - id
    - user_id (absent)
    - interim_id (remplaçant)
    - date_debut, date_fin
    - motif
    - role (NULL = tous les rôles du titulaire)
}
```

---

## 👥 Rôles et permissions

### Rôles système

| Rôle | Description | Accès |
|------|-------------|-------|
| `admin` | Administrateur | Gestion complète, imports, utilisateurs, statistiques |
| `demandeur` | Demandeur | Création/suivi DAPT, visualisation NAPT |
| `desa` | Agent DESA | Traitement DAPT, création/gestion NAPT, diffusion |
| `verificateur` | Vérificateur | Vérification NAPT |
| `valideur` | Valideur | Validation NAPT |
| `operateur` | Opérateur | Exécution NAPT |
| `operateurchef` | Opérateur Chef | Exécution NAPT + Fiche manœuvre |
| `directeur` | Directeur | Consultation (lecture seule) + Feedback |

### Middleware personnalisé

```php
// RoleOrInterimMiddleware
// Permet l'accès si l'utilisateur a le rôle OU est intérimaire

Route::middleware(['roleOrInterim:desa|admin'])->group(function () {
    // Routes accessibles aux DESA ou admins ou intérimaires DESA
});
```

---

## 🔄 Workflow DAPT/NAPT

### Flux DAPT (Demande)

```
┌─────────────────┐
│   DEMANDEUR     │
│  Crée la DAPT   │
└────────┬────────┘
         │ statut: "créée"
         ▼
┌─────────────────┐
│      DESA       │
│  Traite la DAPT │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌───────┐ ┌───────────┐
│ACCEPTE│ │ RETOURNE  │
└───┬───┘ └─────┬─────┘
    │           │ statut: "retournée"
    │           │ (demandeur peut modifier)
    ▼           ▼
"acceptée"   DEMANDEUR
    │         (modifie et re-soumet)
    ▼
Création NAPT possible
```

### Flux NAPT (Note)

```
┌─────────────────────────┐
│         DESA            │
│   Crée la NAPT depuis   │
│   DAPT acceptée         │
└───────────┬─────────────┘
            │ statut: "en attente de vérification"
            ▼
┌─────────────────────────┐
│     VERIFICATEUR        │
│    Vérifie la NAPT      │
└───────────┬─────────────┘
            │
       ┌────┴────┐
       │         │
       ▼         ▼
  ┌────────┐ ┌──────────┐
  │VERIFIE │ │ RETOURNE │
  └───┬────┘ └────┬─────┘
      │           │ → DESA (modifie)
      ▼
"vérifiée" / "en attente de validation"
      │
      ▼
┌─────────────────────────┐
│       VALIDEUR          │
│    Valide la NAPT       │
└───────────┬─────────────┘
            │
       ┌────┴────┐
       │         │
       ▼         ▼
  ┌────────┐ ┌──────────┐
  │VALIDE  │ │ RETOURNE │
  └───┬────┘ └────┬─────┘
      │           │ → DESA
      ▼
"validée"
      │
      ▼
┌─────────────────────────┐
│    OPERATEUR CHEF       │
│  Joint fiche manœuvre   │
│      (optionnel)        │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│      OPERATEUR          │
│    Exécute la NAPT      │
└───────────┬─────────────┘
            │
       ┌────┴────┐
       │         │
       ▼         ▼
┌──────────────┐ ┌──────────┐
│ EN COURS     │ │ ANNULEE  │
│ D'EXECUTION  │ └──────────┘
└──────┬───────┘
       │
       ▼
  "executée" ✅
```

### Statuts et transitions

| Statut | Acteur suivant | Actions possibles |
|--------|----------------|-------------------|
| `créée` | DESA | Accepter, Retourner |
| `retournée` | Demandeur | Modifier, Re-soumettre |
| `acceptée` | DESA | Créer NAPT |
| `en attente de vérification` | Vérificateur | Vérifier, Retourner |
| `vérifiée` | Valideur | Valider, Retourner |
| `validée` | Opérateur | Exécuter, Annuler |
| `en cours d'exécution` | Opérateur | Terminer, Annuler |
| `executée` | - | Terminé |
| `annulée` | - | Terminé |

---

## 🛣️ Routes et contrôleurs

### Routes principales

```php
// ===== AUTHENTIFICATION =====
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// ===== ROUTES COMMUNES (auth) =====
Route::middleware('auth')->group(function () {
    // Recherche globale
    Route::get('/search', [SearchController::class, 'index']);
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
    
    // Exports Excel
    Route::get('/exports', [ExportController::class, 'index']);
    Route::get('/export/dapt', [ExportController::class, 'exportDapt']);
    Route::get('/export/napt', [ExportController::class, 'exportNapt']);
    
    // Calendrier des travaux
    Route::get('/calendrier', [CalendrierController::class, 'index']);
    Route::get('/calendrier/events', [CalendrierController::class, 'events']);
    
    // Fil de discussion
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    
    // Onboarding
    Route::post('/onboarding/complete', ...);
    
    // Absences et observations personnelles
    Route::resource('mes-absences', MesAbsencesController::class);
    Route::resource('mes-observations', MesObservationsController::class);
    
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', ...);
    Route::get('api/notifications/count', ...);
    Route::get('api/notifications/latest', ...);
    
    // Documentation
    Route::get('documentation', [DocumentationController::class, 'index']);
});

// ===== ADMIN =====
Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('groupes', GroupeController::class);
    Route::resource('chargecons', ChargeConsController::class);
    Route::resource('correspondants', CorrespondantController::class);
    Route::resource('services', ServiceDestController::class);
    Route::resource('absences', AbsenceController::class);
    Route::resource('demandes', DemandeController::class);
    Route::resource('notes', NoteController::class);
    Route::resource('observations', ObservationController::class);
    
    // Synchronisation Oracle/LDAP
    Route::get('users-sync', [UserSyncController::class, 'index']);
    Route::post('users-sync-all', [UserSyncController::class, 'syncAll']);
    Route::post('users-import-all', [UserSyncController::class, 'importAll']);
    
    // Impersonation
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start']);
});

// ===== DEMANDEUR =====
Route::prefix('demandeur')->middleware('roleOrInterim:demandeur|admin')->group(...);

// ===== DESA =====
Route::prefix('desa')->middleware('roleOrInterim:desa|admin')->group(function () {
    // DAPT
    Route::resource('demandes', DemandeController::class);
    Route::post('demandes/{demande}/faire-napt', 'faire_napt');
    
    // NAPT
    Route::resource('notes', NoteController::class);
    Route::post('notes/{note}/annuler', 'annuler');
    
    // Diffusion hebdomadaire
    Route::get('diffusion', 'manageDiffusion');
    Route::post('diffusion/send', 'sendDiffusion');
});

// ===== VERIFICATEUR, VALIDEUR, OPERATEUR, OPERATEURCHEF, DIRECTEUR =====
// ... (voir routes/web.php)

// ===== API INTERNE (GMAO) =====
Route::prefix('api-internal')->group(function () {
    Route::get('/lieux-execution', [GmaoController::class, 'lieuxExecution']);
    Route::get('/equipements-enfants', [GmaoController::class, 'equipementsEnfants']);
    Route::get('/all-lignes', [GmaoController::class, 'allLignes']);
});
```

---

## 🔔 Système de notifications

### Types de notifications

| Type | Titre | Destinataires |
|------|-------|---------------|
| `dapt_created` | Nouvelle DAPT créée | DESA |
| `dapt_accepted` | DAPT acceptée | Demandeur |
| `dapt_returned` | DAPT retournée | Demandeur |
| `napt_submitted` | NAPT soumise | Vérificateurs |
| `napt_verified` | NAPT vérifiée | Valideurs, DESA |
| `napt_validated` | NAPT validée | Opérateurs, DESA, Demandeur |
| `napt_returned` | NAPT retournée | DESA |
| `napt_executed` | NAPT exécutée | DESA, Demandeur |
| `napt_cancelled` | NAPT annulée | Demandeur, DESA |
| `comment` | Nouveau commentaire | Participants discussion |
| `reminder` | Rappel automatique | Selon contexte |
| `interim_assigned` | Intérim assigné | Intérimaire |

### Canaux de notification

- **Database** : Notifications internes (centre de notifications)
- **Mail** : Emails avec templates personnalisés (couleurs Senelec)
- **Browser** : Notifications push via Service Worker

### Notifications Push (Navigateur)

```javascript
// public/sw.js - Service Worker
self.addEventListener('push', (event) => {
    // Affiche notification native du navigateur
});

// Composant push-notifications.blade.php
// - Enregistre le Service Worker
// - Demande permission
// - Polling pour nouvelles notifications
```

---

## 🔄 Système d'intérim

### Principe

Quand un utilisateur est absent, un intérimaire peut être désigné pour :
- Un rôle spécifique (ex: `desa`, `verificateur`)
- Tous les rôles du titulaire (`role = NULL`)

### Méthodes User

```php
$user->estInterimaireA('desa');       // bool
$user->hasRoleOrInterim('verificateur'); // bool
$user->absentRemplace('valideur');    // User|null
$user->getRolesInterimActifs();       // array
```

---

## 🔌 Intégrations externes

### Oracle HR (Utilisateurs)

```php
// app/Services/OracleHRService.php
// Synchronisation des données utilisateurs depuis Oracle HR

- Matricule, nom, prénom
- Service, direction, département
- Fonction, téléphone
- oracle_person_id pour le mapping
```

### LDAP (Authentification)

```php
// config/ldap.php
// Authentification et récupération des photos

- Authentification avec matricule
- Synchronisation des attributs LDAP
- Récupération des photos de profil (thumbnailPhoto)
```

### SQL Server GMAO (Équipements)

```php
// app/Http/Controllers/Api/GmaoController.php
// Données des équipements électriques

- Lignes électriques
- Postes électriques
- Travées
- Équipements et sous-équipements
```

---

## 📄 Génération PDF

### Templates PDF

| Template | Usage | Route |
|----------|-------|-------|
| `pdf/dapt.blade.php` | PDF DAPT individuel | `/dapt/{demande}` |
| `pdf/dapt-combined.blade.php` | PDF DAPT combiné | Export admin |
| `pdf/napt.blade.php` | PDF NAPT individuel | `/napt/{note}` |
| `pdf/napt-combined.blade.php` | PDF NAPT combiné | Diffusion hebdo |

### Génération

```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('pdf.napt', compact('note'));
return $pdf->download('NAPT-' . $note->numero_note . '.pdf');
```

---

## 📧 Envoi d'emails

### Couleurs Senelec

- **Orange** : #E85D04
- **Violet** : #2B1444
- **Magenta** : #B3006C
- **Bleu** : #0D1CB0

### Templates disponibles

| Template | Usage |
|----------|-------|
| `emails/statut-demande.blade.php` | Changement statut DAPT |
| `emails/napt-weekly-diffusion.blade.php` | Diffusion hebdomadaire NAPT |
| `emails/workflow-notification.blade.php` | Notifications workflow |
| `vendor/mail/html/` | Templates Laravel personnalisés |

### Traductions

Les emails sont traduits en français via `lang/fr/mail.php`.

---

## 🆕 Nouvelles fonctionnalités

### 1. Recherche globale

```php
// Route: /search
// Contrôleur: SearchController

// Recherche sur:
- DAPT (numero_demande, designation, lieu, demandeur)
- NAPT (numero_note, numero_semaine, designation)
- Utilisateurs (nom, matricule, email) - admin seulement

// Suggestions AJAX: /search/suggestions
```

**Usage:**
- Barre de recherche dans le header
- Vue dédiée avec résultats groupés

### 2. Export Excel

```php
// Routes: /exports, /export/dapt, /export/napt
// Contrôleur: ExportController
// Classes: DaptExport, NaptExport (maatwebsite/excel)

// Filtres disponibles:
- Date début / Date fin
- Statut
- Numéro de semaine (NAPT)
```

**Usage:**
- Page `/exports` avec formulaires de filtres
- Export instantané au format `.xlsx`
- En-têtes colorés (Senelec purple/orange)

### 3. Calendrier des travaux

```php
// Route: /calendrier
// Contrôleur: CalendrierController
// Bibliothèque: FullCalendar 6.x

// Fonctionnalités:
- Vue mois / semaine / liste
- Événements colorés par statut
- Modal de détails au clic
- Lien vers la NAPT
```

**Codes couleurs:**
- Jaune : En attente
- Vert : Validée
- Bleu : En exécution
- Gris : Exécutée
- Orange : Autre

### 4. Widget Activité récente

```blade
{{-- Composant: components/recent-activity.blade.php --}}
@include('components.recent-activity')
```

**Affiche:**
- Dernières demandes créées (demandeur)
- Dernières notes établies (DESA)
- Dernières notifications

### 5. Widget KPI

```blade
{{-- Composant: components/kpi-widget.blade.php --}}
@include('components.kpi-widget')
```

**Indicateurs:**
- DAPT créées ce mois / acceptées
- NAPT créées ce mois / exécutées
- Temps moyen de traitement
- NAPT en retard (alerte)
- Taux de validation (barre de progression)

### 6. Tutoriel d'onboarding

```blade
{{-- Composant: components/onboarding-tutorial.blade.php --}}
{{-- S'affiche automatiquement si onboarding_completed = false --}}
```

**Caractéristiques:**
- Étapes personnalisées selon le rôle
- Modal interactif avec navigation
- Marque automatiquement comme complété
- Route: `POST /onboarding/complete`

### 7. Rappels automatiques

```bash
# Commande Artisan
php artisan reminders:send
php artisan reminders:send --dry-run  # Test sans envoi
```

**Rappels:**
- DAPT en attente > 3 jours → DESA
- NAPT à vérifier > 2 jours → Vérificateurs
- NAPT en retard (date dépassée) → Opérateurs, DESA
- NAPT prévues demain → Demandeur, Opérateurs

**À programmer dans le scheduler:**
```php
// app/Console/Kernel.php
$schedule->command('reminders:send')->dailyAt('08:00');
```

### 8. Fil de discussion

```blade
{{-- Composant: components/comments.blade.php --}}
<x-comments type="demande" :id="$demande->id" />
<x-comments type="note" :id="$note->id" />
```

**Fonctionnalités:**
- Commentaires sur DAPT et NAPT
- Réponses imbriquées
- Notes internes (non visibles par demandeur)
- Notifications aux participants
- Suppression (auteur ou admin)

**Routes:**
- `GET /comments` - Liste des commentaires
- `POST /comments` - Ajouter un commentaire
- `DELETE /comments/{id}` - Supprimer

### 9. Notifications push navigateur

```blade
{{-- Composant: components/push-notifications.blade.php --}}
{{-- Chargé automatiquement dans le layout --}}
```

**Fonctionnement:**
- Service Worker (`/sw.js`)
- Demande de permission au premier chargement
- Polling toutes les 30 secondes
- Notifications natives du navigateur
- Clic → ouvre l'application

---

## 🚀 Commandes Artisan

```bash
# Démarrer le serveur
php artisan serve

# Vider les caches
php artisan optimize:clear

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Seeders
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=GroupesSeeder
php artisan db:seed --class=CorrespondantsSeeder
php artisan db:seed --class=ChargesConsSeeder
php artisan db:seed --class=ServiceDestSeeder

# Synchronisation utilisateurs
php artisan users:sync-oracle --import-all --limit=5000

# Rappels automatiques
php artisan reminders:send
php artisan reminders:send --dry-run

# Tests
php artisan test
php artisan test --filter=NaptWorkflowTest
php artisan test --coverage

# Compilation assets
npm run dev
npm run build
```

---

## 🧪 Tests Unitaires et Fonctionnels

### Structure des tests

```
tests/
├── Unit/
│   ├── ExampleTest.php
│   └── Models/
│       ├── DemandeTest.php          # 7 tests - Modèle DAPT
│       └── NoteTest.php             # 10 tests - Modèle NAPT
├── Feature/
│   ├── ExampleTest.php
│   ├── AuthenticationTest.php       # 13 tests - Login, logout, redirections
│   ├── ExportTest.php               # 11 tests - Export Excel DAPT/NAPT
│   ├── NotificationTest.php         # 10 tests - Notifications workflow
│   └── Workflow/
│       ├── DaptWorkflowTest.php     # 14 tests - Workflow complet DAPT
│       └── NaptWorkflowTest.php     # 18 tests - Workflow complet NAPT
└── Factories/
    ├── DemandeFactory.php           # Factory avec états
    └── NoteFactory.php              # Factory avec états
```

### Tests par catégorie

| Catégorie | Fichier | Nb Tests | Description |
|-----------|---------|----------|-------------|
| **Modèles** | `DemandeTest.php` | 7 | Création, relations, statuts, méthodes |
| **Modèles** | `NoteTest.php` | 10 | Workflow, relations, validations |
| **Auth** | `AuthenticationTest.php` | 13 | Login, logout, redirections par rôle |
| **Export** | `ExportTest.php` | 11 | Excel DAPT/NAPT, filtres, permissions |
| **Notifications** | `NotificationTest.php` | 10 | Emails, notifications internes |
| **Workflow DAPT** | `DaptWorkflowTest.php` | 14 | Création → acceptation/retour |
| **Workflow NAPT** | `NaptWorkflowTest.php` | 18 | Vérification → validation → exécution |

**Total : 88 tests**

### Factories disponibles

```php
// DemandeFactory - États disponibles
Demande::factory()->creee()->create();
Demande::factory()->acceptee()->create();
Demande::factory()->retournee()->create();

// NoteFactory - États disponibles
Note::factory()->brouillon()->create();
Note::factory()->enAttenteVerification()->create();
Note::factory()->verifiee()->create();
Note::factory()->validee()->create();
Note::factory()->enCoursExecution()->create();
Note::factory()->executee()->create();
Note::factory()->retournee()->create();
Note::factory()->annulee()->create();
```

### Commandes de test

```bash
# Lancer tous les tests
php artisan test

# Lancer un groupe spécifique
php artisan test --filter=AuthenticationTest
php artisan test --filter=DaptWorkflowTest
php artisan test --filter=NaptWorkflowTest
php artisan test --filter=NotificationTest

# Lancer un test spécifique
php artisan test --filter=complete_workflow_test
php artisan test --filter=return_workflow_test_valideur_to_verificateur

# Lister tous les tests
php artisan test --list-tests

# Avec couverture de code (nécessite Xdebug)
php artisan test --coverage
php artisan test --coverage-html=coverage-report
```

### Tests de workflow clés

| Test | Description |
|------|-------------|
| `complete_workflow_test` | Workflow NAPT complet : DESA → Vérif → Valid → Exec |
| `return_workflow_test_valideur_to_verificateur` | Retour valideur → vérificateur |
| `return_workflow_test_verificateur_to_desa` | Retour vérificateur → DESA |
| `desa_can_cancel_note` | Annulation NAPT par DESA |
| `operateur_can_cancel_validated_note` | Annulation NAPT par opérateur |

### Configuration des tests

```php
// phpunit.xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>

// tests/TestCase.php
use Illuminate\Foundation\Testing\RefreshDatabase;
// Les tests utilisent une base SQLite en mémoire
```

---

## 📊 Statistiques du projet

| Métrique | Valeur |
|----------|--------|
| Contrôleurs | 35 |
| Modèles | 13 |
| Vues Blade | 135+ |
| Migrations | 26 |
| Seeders | 8 |
| Routes | ~100 |
| Rôles | 8 |
| Composants | 6 |
| **Tests unitaires** | **88** |
| **Factories** | **3** |

---

## 📱 Sidebar - Section Outils

La sidebar inclut une section "Outils" accessible à tous les utilisateurs :

- 📅 **Calendrier** - Vue calendrier des NAPT
- 📥 **Exports Excel** - Export DAPT/NAPT avec filtres
- 📚 **Documentation** - Guide utilisateur complet

---

## 👨‍💻 Auteurs

- **SENELEC** - Direction Exploitation Système Électrique (DESE)
- **DESA** - Division Exploitation Système Automatisé

---

*© 2026 SENELEC - Tous droits réservés*
