# 🚀 Guide de Déploiement - SENDAPTNAPT

## Prérequis Serveur

### Logiciels requis
- **PHP** 8.3+
- **Composer** 2.x
- **Node.js** 18+ & npm
- **MySQL** 8.0+
- **Apache** ou **Nginx**
- Extensions PHP :
  - `pdo_mysql`
  - `pdo_oci` (pour Oracle)
  - `pdo_sqlsrv` (pour SQL Server GMAO)
  - `ldap`
  - `gd` ou `imagick`
  - `mbstring`
  - `xml`
  - `zip`

### Services externes
- **Serveur Oracle HR** (synchronisation utilisateurs)
- **Serveur SQL Server GMAO** (équipements)
- **Serveur LDAP** (authentification)
- **Serveur SMTP** (envoi emails)

---

## 📋 Étapes de Déploiement

### 1. Cloner le projet

```bash
git clone https://github.com/senelec/sendaptnapt.git
cd sendaptnapt
```

### 2. Installer les dépendances

```bash
# Dépendances PHP (sans dev)
composer install --no-dev --optimize-autoloader

# Dépendances Node.js
npm ci
```

### 3. Configurer l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

**Modifier `.env` avec les vraies valeurs :**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sendaptnapt.senelec.sn

# Base de données MySQL
DB_HOST=votre-serveur-mysql
DB_DATABASE=sendaptnapt
DB_USERNAME=votre-user
DB_PASSWORD=votre-mot-de-passe

# Oracle HR
ORACLE_HOST=votre-serveur-oracle
ORACLE_USERNAME=votre-user
ORACLE_PASSWORD=votre-mot-de-passe

# SQL Server GMAO
SQLSRV_GMAO_HOST=votre-serveur-sqlserver
SQLSRV_GMAO_USERNAME=votre-user
SQLSRV_GMAO_PASSWORD=votre-mot-de-passe

# LDAP
LDAP_HOST=votre-serveur-ldap
LDAP_BASE_DN="dc=senelec,dc=sn"
LDAP_USERNAME=votre-admin-ldap
LDAP_PASSWORD=votre-mot-de-passe

# SMTP
MAIL_HOST=votre-serveur-smtp
MAIL_USERNAME=sendaptnapt@senelec.sn
MAIL_PASSWORD=votre-mot-de-passe
```

### 4. Compiler les assets

```bash
npm run build
```

### 5. Configurer la base de données

```bash
# Lancer les migrations
php artisan migrate --force

# Exécuter les seeders (première installation uniquement)
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=GroupesSeeder
php artisan db:seed --class=CorrespondantsSeeder
php artisan db:seed --class=ChargesConsSeeder
php artisan db:seed --class=ServiceDestSeeder
```

### 6. Configurer les permissions

```bash
# Permissions sur les dossiers
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Propriétaire (Apache/Nginx)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 7. Lien symbolique storage

```bash
php artisan storage:link
```

### 8. Optimiser pour la production

```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation autoloader
composer dump-autoload --optimize
```

---

## 🌐 Configuration Serveur Web

### Apache (.htaccess déjà inclus)

Assurez-vous que `mod_rewrite` est activé :
```bash
a2enmod rewrite
systemctl restart apache2
```

**VirtualHost exemple :**
```apache
<VirtualHost *:443>
    ServerName sendaptnapt.senelec.sn
    DocumentRoot /var/www/sendaptnapt/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    <Directory /var/www/sendaptnapt/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sendaptnapt_error.log
    CustomLog ${APACHE_LOG_DIR}/sendaptnapt_access.log combined
</VirtualHost>
```

### Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name sendaptnapt.senelec.sn;
    root /var/www/sendaptnapt/public;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## ⏰ Tâches Planifiées (Cron)

Ajouter au crontab (`crontab -e`) :

```bash
# Scheduler Laravel (chaque minute)
* * * * * cd /var/www/sendaptnapt && php artisan schedule:run >> /dev/null 2>&1

# Rappels automatiques (tous les jours à 8h)
0 8 * * * cd /var/www/sendaptnapt && php artisan reminders:send >> /var/log/sendaptnapt/reminders.log 2>&1
```

---

## 🔒 Sécurité

### Vérifications importantes

1. **APP_DEBUG=false** dans `.env`
2. **APP_ENV=production** dans `.env`
3. Certificat SSL valide (HTTPS obligatoire)
4. Fichier `.env` non accessible publiquement
5. Dossier `storage/logs` protégé

### Headers de sécurité (recommandés)

```apache
# Apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

---

## 📊 Synchronisation des Utilisateurs

### Première synchronisation

```bash
# Import massif depuis Oracle + LDAP (photos)
php artisan users:sync-oracle --import-all --limit=5000
```

### Synchronisation régulière (cron)

```bash
# Ajouter au crontab (tous les jours à 6h)
0 6 * * * cd /var/www/sendaptnapt && php artisan users:sync-oracle --limit=100 >> /var/log/sendaptnapt/sync.log 2>&1
```

---

## 🐛 Dépannage

### Logs

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs de synchronisation
tail -f storage/logs/sync_users.log
```

### Vider les caches

```bash
php artisan optimize:clear
```

### Regénérer les caches

```bash
php artisan optimize
```

### Vérifier les permissions

```bash
php artisan permission:cache-reset
```

---

## 📧 Test de l'envoi d'emails

```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@senelec.sn')->subject('Test'); });
```

---

## ✅ Checklist Pré-Déploiement

- [ ] `.env` configuré avec les vraies valeurs
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Certificat SSL installé
- [ ] Migrations exécutées
- [ ] Seeders exécutés (première fois)
- [ ] Assets compilés (`npm run build`)
- [ ] Caches optimisés
- [ ] Permissions fichiers OK
- [ ] Cron configuré
- [ ] Test d'envoi d'email OK
- [ ] Test de connexion LDAP OK
- [ ] Test de connexion Oracle OK
- [ ] Test de connexion SQL Server GMAO OK

---

## 📞 Support

En cas de problème, contacter :
- **DESA** : desa@senelec.sn
- **DSI** : support-dsi@senelec.sn

---

*© 2026 SENELEC - SENDAPTNAPT*
