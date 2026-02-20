# Template Laravel - SENELEC Design System

> Ce fichier contient l'architecture complète, le design system, la structure et tous les fichiers nécessaires pour recréer un projet Laravel avec le même look & feel que SENDAPTNAPT. Seules les fonctionnalités métier changent.

---

## Table des matières

1. [Stack technique](#1-stack-technique)
2. [Structure du projet](#2-structure-du-projet)
3. [Installation rapide](#3-installation-rapide)
4. [Docker (docker-compose.yml + Dockerfile)](#4-docker)
5. [Configuration (.env, auth, vite, tailwind)](#5-configuration)
6. [CSS / Design System (app.css)](#6-css--design-system)
7. [Page de connexion (login.blade.php)](#7-page-de-connexion)
8. [Layout principal (app.blade.php)](#8-layout-principal)
9. [Header (header.blade.php)](#9-header)
10. [Sidebar (sidebar.blade.php)](#10-sidebar)
11. [Authentification (LoginController.php)](#11-authentification)
12. [Modèle User](#12-modèle-user)
13. [Gestion des rôles (Spatie + Seeder)](#13-gestion-des-rôles)
14. [Middleware personnalisé (RoleOrInterim)](#14-middleware-personnalisé)
15. [CRUD Utilisateurs (Controller + Vues)](#15-crud-utilisateurs)
16. [Trait de recherche (SearchableTrait)](#16-trait-de-recherche)
17. [Routes (web.php)](#17-routes)
18. [Kernel HTTP](#18-kernel-http)
19. [Migrations clés](#19-migrations-clés)
20. [Checklist nouveau projet](#20-checklist-nouveau-projet)

---

## 1. Stack technique

| Composant | Technologie |
|-----------|-------------|
| **Framework** | Laravel 10.x |
| **PHP** | 8.3+ |
| **Base de données** | PostgreSQL 15 |
| **Cache/Session** | Redis 7 |
| **CSS** | Tailwind CSS 4 |
| **JS** | Alpine.js 3 |
| **Bundler** | Vite |
| **Auth LDAP** | LdapRecord-Laravel |
| **Rôles** | Spatie Laravel-Permission |
| **PDF** | Dompdf (barryvdh) |
| **Excel** | Maatwebsite Excel |
| **Temps réel** | Livewire 4 |
| **Conteneur** | Docker (PHP-FPM + Nginx + PostgreSQL + Redis) |

---

## 2. Structure du projet

```
mon-projet/
├── app/
│   ├── Console/Commands/       # Commandes Artisan custom
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/           # LoginController
│   │   │   ├── Admin/          # UserController, ImpersonateController, etc.
│   │   │   ├── Demandeur/      # Controllers par rôle
│   │   │   └── ...
│   │   └── Middleware/         # Authenticate, RoleOrInterimMiddleware
│   ├── Models/                 # User, etc.
│   ├── Services/               # RoleAssignmentService, etc.
│   └── Traits/                 # SearchableTrait
├── config/                     # auth.php, permission.php, etc.
├── database/
│   ├── migrations/
│   └── seeders/                # RolesAndPermissionsSeeder
├── docker/
│   ├── nginx/default.conf
│   ├── php/php.ini, opcache.ini, www.conf
│   └── postgres/init.sql
├── public/
│   ├── fonts/                  # Conthrax-SemiBold.otf
│   ├── img/                    # logo.png, login_bg.png
│   └── profil/                 # Photos utilisateurs
├── resources/
│   ├── css/app.css             # Design system complet
│   ├── js/app.js               # Alpine.js + Axios
│   └── views/
│       ├── auth/login.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── partials/
│       │       ├── header.blade.php
│       │       └── sidebar.blade.php
│       └── admin/users/        # index, create, edit, show
├── routes/web.php
├── docker-compose.yml
├── Dockerfile
├── tailwind.config.js
└── vite.config.js
```

---

## 3. Installation rapide

```bash
# 1. Créer le projet
composer create-project laravel/laravel mon-projet "10.*"
cd mon-projet

# 2. Installer les dépendances
composer require spatie/laravel-permission barryvdh/laravel-dompdf \
  directorytree/ldaprecord-laravel livewire/livewire laravel/sanctum \
  maatwebsite/excel

# 3. Installer les dépendances JS
npm install alpinejs @fontsource/open-sans @fontsource/rajdhani @fortawesome/fontawesome-free
npm install -D tailwindcss @tailwindcss/forms @tailwindcss/postcss postcss autoprefixer vite laravel-vite-plugin

# 4. Publier les configs
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 5. Migrations + Seed
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder

# 6. Assets
npm run build
```

---

## 4. Docker

### docker-compose.yml

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: myapp_app
    restart: unless-stopped
    network_mode: host
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/custom.ini:ro
    environment:
      - APP_ENV=${APP_ENV:-production}
      - DB_CONNECTION=pgsql
      - DB_HOST=127.0.0.1
      - DB_PORT=5432
      - DB_DATABASE=${DB_DATABASE:-myapp}
      - DB_USERNAME=${DB_USERNAME:-myapp}
      - DB_PASSWORD=${DB_PASSWORD:-secret}
      - REDIS_HOST=127.0.0.1
      - REDIS_PORT=6380
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_started

  nginx:
    image: nginx:alpine
    container_name: myapp_nginx
    restart: unless-stopped
    ports:
      - "${APP_PORT:-80}:80"
    volumes:
      - .:/var/www/html:ro
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - app
    networks:
      - myapp_network

  postgres:
    image: postgres:15-alpine
    container_name: myapp_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: ${DB_DATABASE:-myapp}
      POSTGRES_USER: ${DB_USERNAME:-myapp}
      POSTGRES_PASSWORD: ${DB_PASSWORD:-secret}
    volumes:
      - postgres_data:/var/lib/postgresql/data
    ports:
      - "${DB_PORT:-5432}:5432"
    networks:
      - myapp_network
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_USERNAME:-myapp}"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    container_name: myapp_redis
    restart: unless-stopped
    command: redis-server --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru
    ports:
      - "6380:6379"
    volumes:
      - redis_data:/data
    networks:
      - myapp_network

volumes:
  postgres_data:
  redis_data:

networks:
  myapp_network:
    driver: bridge
```

### Dockerfile

```dockerfile
FROM php:8.3-fpm-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libldap2-dev libpng-dev libjpeg62-turbo-dev \
    libfreetype6-dev libzip-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql ldap gd zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

---

## 5. Configuration

### vite.config.js

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### tailwind.config.js

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./vendor/livewire/livewire/dist/livewire.js",
  ],
  theme: {
    extend: {
      colors: {
        senelec: {
          blue: { DEFAULT: '#0D1CB0', dark: '#0A1580', light: '#1A2DD0' },
          teal: { DEFAULT: '#0A91A3', dark: '#077A8A', light: '#0CB0C5' },
          purple: { DEFAULT: '#2B1444', dark: '#1E0E30', light: '#3D1E5C' },
          orange: { DEFAULT: '#E87400', dark: '#C56200', light: '#FF8C1A' },
          magenta: { DEFAULT: '#B3006C', dark: '#8F0056', light: '#D41A82' },
          yellow: { DEFAULT: '#FFD100', dark: '#D4AF00', light: '#FFE033' },
        },
      },
      fontFamily: {
        'conthrax': ['Conthrax', 'Rajdhani', 'Open Sans', 'system-ui', 'sans-serif'],
        'title': ['Rajdhani', 'Open Sans', 'system-ui', 'sans-serif'],
        'body': ['Open Sans', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
```

### package.json

```json
{
    "private": true,
    "scripts": { "dev": "vite", "build": "vite build" },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.11",
        "@tailwindcss/postcss": "^4.1.18",
        "autoprefixer": "^10.4.23",
        "axios": "^1.1.2",
        "laravel-vite-plugin": "^0.7.8",
        "postcss": "^8.5.6",
        "tailwindcss": "^4.1.18",
        "vite": "^4.5.14"
    },
    "dependencies": {
        "alpinejs": "^3.15.5",
        "@fontsource/open-sans": "^5.x",
        "@fontsource/rajdhani": "^5.x",
        "@fortawesome/fontawesome-free": "^6.x"
    }
}
```

### resources/js/app.js

```javascript
import './bootstrap';

// Polices self-hosted (pas de dépendance CDN)
import '@fontsource/open-sans/300.css';
import '@fontsource/open-sans/400.css';
import '@fontsource/open-sans/500.css';
import '@fontsource/open-sans/600.css';
import '@fontsource/open-sans/700.css';
import '@fontsource/open-sans/800.css';
import '@fontsource/rajdhani/400.css';
import '@fontsource/rajdhani/500.css';
import '@fontsource/rajdhani/600.css';
import '@fontsource/rajdhani/700.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

---

## 6. CSS / Design System

> Copier le contenu de `resources/css/app.css` du projet SENDAPTNAPT.
> Il contient toutes les classes utilitaires : `btn-senelec`, `card-senelec`, `sidebar-link`, `sidebar-link-active`, `sidebar-sublink`, `input-senelec`, `badge-*`, `stat-card-*`, `table-senelec`, `bg-senelec-gradient`, etc.

Couleurs clés :
- **Violet foncé (sidebar)** : `#2B1444`
- **Magenta (header, boutons)** : `#B3006C`
- **Gradient principal** : `linear-gradient(135deg, #2B1444 0%, #B3006C 100%)`
- **Teal (accent)** : `#0A91A3`
- **Orange (alertes)** : `#E87400`

---

## 7. Page de connexion

> Fichier : `resources/views/auth/login.blade.php`

Points clés :
- Page plein écran avec image de fond + overlay gradient violet-magenta
- Branding à gauche, formulaire glass-card à droite
- Police Conthrax pour le titre, Open Sans pour les textes
- Polices Open Sans, Rajdhani et Font Awesome bundlées via Vite (pas de CDN)
- Champs : matricule + mot de passe (avec toggle visibilité via Alpine.js)
- Checkbox "Se souvenir" + lien "Mot de passe oublié"
- Messages d'erreur et succès stylisés

Assets requis dans `public/` :
- `img/logo.png` - Logo de l'entreprise
- `img/login_bg.png` - Image de fond
- `fonts/Conthrax-SemiBold.otf` - Police titre

> **Important** : Les polices (Open Sans, Rajdhani) et Font Awesome sont importées via `app.js` (npm packages `@fontsource/*` et `@fortawesome/fontawesome-free`). Aucun CDN n'est nécessaire. Le serveur fonctionne sans accès internet.

---

## 8. Layout principal

> Fichier : `resources/views/layouts/app.blade.php`

Structure :
```
┌──────────────────────────────────────────┐
│ Bannière Impersonation (si active)       │
├────────┬─────────────────────────────────┤
│        │ Header (magenta #B3006C)        │
│ Side-  ├─────────────────────────────────┤
│ bar    │                                 │
│ w-72   │ Main Content                    │
│ violet │ (bg-senelec-gradient-soft)      │
│ #2B1444│ max-w-7xl centered              │
│        │                                 │
│        │ Flash messages (success/error)  │
│        │ @yield('content')               │
│        │                                 │
└────────┴─────────────────────────────────┘
```

- Sidebar fixe à gauche (72px, caché en mobile)
- Overlay mobile avec toggle Alpine.js
- Header sticky en haut
- Flash messages automatiques (success, error, warning)
- Stacks : `@stack('styles')`, `@stack('scripts')`, `@stack('modals')`

---

## 9. Header

> Fichier : `resources/views/layouts/partials/header.blade.php`

- Fond magenta `#B3006C`
- Barre de recherche globale
- Icône notifications avec badge compteur (polling toutes les 30s)
- Dropdown notifications (chargement AJAX)
- Menu profil utilisateur avec : photo/initiales, nom, matricule, rôles, liens profil/signature/déconnexion

---

## 10. Sidebar

> Fichier : `resources/views/layouts/partials/sidebar.blade.php`

- Fond violet foncé `#2B1444`
- Logo + nom de l'app en haut
- Navigation par rôle avec `@if($user->hasRoleOrInterim('role'))` (sans exclusion admin, multi-rôles visibles)
- Un utilisateur avec plusieurs rôles (ex: admin + desa) voit toutes ses sections
- Sections collapsibles (Alpine.js `x-show` + `x-collapse`)
- Sous-liens avec indicateur point
- Active state via `request()->routeIs()`
- Badge intérim pour les rôles intérimaires
- Bouton déconnexion en bas

Classes CSS sidebar :
- `sidebar-link` / `sidebar-link-active`
- `sidebar-sublink` / `sidebar-sublink-active`

---

## 11. Authentification

> Fichier : `app/Http/Controllers/Auth/LoginController.php`

Flux de connexion :
1. Essai auth locale (Hash::check) - pour dev/admin
2. Si LDAP activé (`LDAP_ENABLED=true`), essai LDAP
3. Sync attributs LDAP vers User local (recherche par matricule, ldap_guid, puis email en fallback)
4. Extraction matricule depuis LDAP avec gestion double-espace dans company
5. Sync Oracle HR si activé
6. Auto-attribution du rôle via `RoleAssignmentService`
7. Si aucun rôle -> assigne `demandeur` par défaut
8. Réutilisation du user synchronisé (pas de re-lookup par matricule)
9. Redirect vers dashboard du rôle principal
10. Protection contre redirect vers `/api/` routes

---

## 12. Modèle User

> Fichier : `app/Models/User.php`

Champs clés :
- Identifiants : `matricule`, `ldap_username`, `ldap_guid`
- Personnel : `nom`, `prenom`, `poste`, `telephone`, `photo`
- Organisation : `service`, `direction`, `departement`, `groupe_id`
- Oracle : `oracle_person_id`, `fonction_oracle`, `oracle_synced_at`
- Signatures : `signature`, `stamp`
- Statut : `is_active`, `onboarding_completed`

Accesseurs : `full_name`, `initials`, `photo_url`, `signature_url`, `appartenance`

Méthodes clés :
- `isSuperAdmin()` : vérifie le matricule super admin (seul lui peut simuler tout le monde y compris les admins, et accéder à Sync Oracle/LDAP)
- `hasRoleOrInterim($role)` : rôle direct ou intérimaire
- `estInterimaireA($role)` : vérifie si intérimaire actif
- `scopeSearch($query, $search)` : recherche insensible casse/accents

---

## 13. Gestion des rôles

> Fichier : `database/seeders/RolesAndPermissionsSeeder.php`

Rôles par défaut :
| Rôle | Description |
|------|-------------|
| `admin` | Accès complet |
| `demandeur` | Créer et suivre ses demandes |
| `desa` | Traiter demandes, créer notes |
| `verificateur` | Vérifier les notes |
| `valideur` | Valider les notes |
| `operateur` | Exécuter les notes |
| `operateurchef` | Exécuter + fiche manoeuvre |
| `directeur` | Consultation et supervision |

> **Adapter les rôles et permissions selon le métier du nouveau projet.**

---

## 14. Middleware personnalisé

> Fichier : `app/Http/Middleware/RoleOrInterimMiddleware.php`

Permet l'accès si l'utilisateur a le rôle OU est intérimaire.
Usage dans routes :

```php
Route::middleware(['roleOrInterim:desa|admin'])->group(function () { ... });
```

Enregistré dans `Kernel.php` :
```php
'roleOrInterim' => \App\Http\Middleware\RoleOrInterimMiddleware::class,
```

---

## 15. CRUD Utilisateurs

> Fichier : `app/Http/Controllers/Admin/UserController.php`

Fonctionnalités :
- Liste paginée avec filtres (recherche, rôle, groupe, statut)
- Création manuelle
- Modification (champs LDAP/Oracle en readonly)
- Détail avec statistiques
- Suppression (interdit pour LDAP/Oracle)
- Bouton Simuler (super admin only, peut simuler tout le monde y compris les admins)
- Bouton Sync Oracle/LDAP (super admin only)

Vues : `admin/users/index.blade.php`, `create`, `edit`, `show`

---

## 16. Trait de recherche

> Fichier : `app/Traits/SearchableTrait.php`

Deux méthodes :
- `applySearch()` : recherche multi-mots (chaque mot doit matcher)
- `applySimpleSearch()` : recherche simple (un seul terme)

Caractéristiques :
- Insensible à la casse (`LOWER()`)
- Insensible aux accents (`unaccent()` si PostgreSQL extension disponible)
- Recherche dans colonnes directes + relations
- Support PostgreSQL et MySQL

Usage :
```php
$this->applySimpleSearch($query, $search, 
    ['numero', 'designation'],           // colonnes directes
    ['demandeur' => ['name', 'matricule']] // relations
);
```

---

## 17. Routes

Structure des routes (`routes/web.php`) :

```php
// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected
Route::middleware(['auth'])->group(function () {
    // Dashboard redirect par rôle
    Route::get('/dashboard', function () { ... })->name('dashboard');
    
    // Routes communes (profil, notifications, etc.)
    
    // Admin
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        // ...
    });
    
    // Par rôle avec middleware roleOrInterim
    Route::middleware(['roleOrInterim:demandeur|admin'])->prefix('demandeur')->name('demandeur.')->group(function () {
        // ...
    });
});
```

---

## 18. Kernel HTTP

> Fichier : `app/Http/Kernel.php`

Middleware aliases clés :
```php
'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
'roleOrInterim' => \App\Http\Middleware\RoleOrInterimMiddleware::class,
```

---

## 19. Migrations clés

### Table users (étendue)

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('matricule')->unique()->nullable();
    $table->string('ldap_username')->nullable();
    $table->string('ldap_guid')->nullable();
    $table->string('nom')->nullable();
    $table->string('prenom')->nullable();
    $table->string('poste')->nullable();
    $table->string('telephone')->nullable();
    $table->string('photo')->nullable();
    $table->string('organisation')->nullable();
    $table->string('entreprise')->nullable();
    $table->string('service')->nullable();
    $table->string('direction')->nullable();
    $table->string('departement')->nullable();
    $table->unsignedBigInteger('oracle_person_id')->nullable();
    $table->string('fonction_oracle')->nullable();
    $table->timestamp('oracle_synced_at')->nullable();
    $table->string('signature')->nullable();
    $table->string('stamp')->nullable();
    $table->foreignId('groupe_id')->nullable()->constrained('groupes')->nullOnDelete();
    $table->boolean('is_active')->default(true);
    $table->boolean('onboarding_completed')->default(false);
    $table->timestamp('last_sync_at')->nullable();
    $table->timestamp('last_activity_at')->nullable();
});
```

### Tables Spatie Permission
Publiées automatiquement avec `vendor:publish`.

---

## 20. Checklist nouveau projet

- [ ] Créer le projet Laravel 10
- [ ] Installer les dépendances (Spatie, LDAP, Alpine, Tailwind, etc.)
- [ ] Copier `resources/css/app.css` (design system complet)
- [ ] Copier `resources/js/app.js` (Alpine + imports polices self-hosted)
- [ ] Copier `tailwind.config.js` et `vite.config.js`
- [ ] `npm install @fontsource/open-sans @fontsource/rajdhani @fortawesome/fontawesome-free`
- [ ] Copier les assets : `public/img/logo.png`, `public/img/login_bg.png`, `public/fonts/Conthrax-SemiBold.otf`
- [ ] Copier les layouts : `app.blade.php`, `header.blade.php`, `sidebar.blade.php`
- [ ] Copier `auth/login.blade.php`
- [ ] Copier le modèle `User.php` et la migration étendue
- [ ] Copier `LoginController.php` (adapter LDAP si nécessaire)
- [ ] Copier `RoleAssignmentService.php` (adapter les mappings)
- [ ] Copier `RoleOrInterimMiddleware.php` et l'enregistrer dans `Kernel.php`
- [ ] Copier `SearchableTrait.php`
- [ ] Copier les vues admin/users (index, create, edit, show)
- [ ] Copier `RolesAndPermissionsSeeder.php` (adapter les rôles)
- [ ] Copier `docker-compose.yml` et `Dockerfile` (adapter les noms)
- [ ] Adapter le sidebar avec les routes/rôles du nouveau projet
- [ ] Adapter le header (titre, recherche)
- [ ] Adapter la page login (titre, description, features pills)
- [ ] Lancer les migrations et le seeder
- [ ] `npm run build` pour compiler les assets
- [ ] Tester la connexion et la navigation

---

> **Note** : Pour les fichiers complets (code source intégral), se référer directement aux fichiers du projet SENDAPTNAPT dans les chemins indiqués dans chaque section.
