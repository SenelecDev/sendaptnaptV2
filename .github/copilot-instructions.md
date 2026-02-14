# SENDAPTNAPT V2 - Copilot Instructions

## Project Overview
Application de gestion des Demandes d'Arrêt Pour Travaux (DAPT) et Notes d'Arrêt Pour Travaux (NAPT) pour SENELEC.

## Tech Stack
- **Framework**: Laravel 10
- **Frontend**: Tailwind CSS 3.x, Alpine.js 3.x, Livewire 3.x
- **Auth**: LDAP (directorytree/ldaprecord-laravel) + Spatie Laravel Permission
- **PDF**: barryvdh/laravel-dompdf
- **Excel**: maatwebsite/excel
- **Database**: MySQL (main) + SQL Server (GMAO) + Oracle (users)

## Architecture

### Models
- `User` - Utilisateurs avec rôles et système d'intérim
- `Demande` - DAPT (Demandes d'Arrêt Pour Travaux)
- `Note` - NAPT (Notes d'Arrêt Pour Travaux)
- `Absence` - Gestion des intérims
- `Groupe` - Groupes d'utilisateurs
- `ChargeCons`, `Correspondant`, `ServiceDest` - Contacts
- `Observation` - Feedback/remarques

### Roles
- `admin` - Full access
- `demandeur` - Create/view demandes
- `desa` - Traiter demandes, créer/gérer notes
- `verificateur` - Vérifier notes
- `valideur` - Valider notes
- `operateur` - Exécuter notes
- `operateurchef` - Exécuter + fiche manœuvre
- `directeur` - Consultation

### Workflow DAPT
1. Demandeur crée → créée
2. DESA traite → en cours de traitement
3. DESA accepte/retourne → acceptée/retournée
4. DAPT acceptée → création NAPT possible

### Workflow NAPT
1. DESA crée → brouillon/en étude
2. DESA soumet → en attente de vérification
3. Vérificateur vérifie → vérifiée/retournée
4. Valideur valide → validée/retournée
5. Opérateur exécute → en cours d'exécution → executée

### Data Sources
- **Mode GMAO**: Lignes, postes, équipements depuis SQL Server GMAO
- **Mode Manuel**: Texte libre (ouvrages_consigner_manuel, ouvrages_installer_manuel)
- **Oracle**: Données utilisateurs externes

## Conventions

### File Structure
```
app/
├── Http/Controllers/     # Controllers by role
├── Models/               # Eloquent models
├── Services/             # Business logic
├── Http/Middleware/      # Custom middleware (RoleOrInterimMiddleware)
resources/
├── views/layouts/        # Blade layouts
├── views/components/     # Reusable components
├── css/app.css           # Tailwind styles
├── js/app.js             # Alpine.js
```

### Naming Conventions
- Controllers: `{Role}{Entity}Controller` (e.g., `DemandeurDemandeController`)
- Views: `{role}/{entity}/{action}.blade.php`
- Routes: `{role}.{entity}.{action}`

### Blade Components
Use Tailwind utility classes and custom component classes defined in `resources/css/app.css`:
- `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`
- `.card`, `.card-header`, `.card-body`
- `.badge`, `.badge-success`, `.badge-warning`, `.badge-danger`, `.badge-info`
- `.input`, `.label`

### French Language
All user-facing text should be in French.
