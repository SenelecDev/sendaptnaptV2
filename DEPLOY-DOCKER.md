# 🐳 Guide de Déploiement Docker - SENDAPTNAPT

## 📋 Prérequis Serveur

**Serveur cible :**
- **OS** : CentOS Linux 7.9 (Core)
- **Architecture** : x86_64
- **Hostname** : sendaptnapt

---

## 🚀 Installation sur CentOS 7

### 1. Mise à jour du système

```bash
sudo yum update -y
sudo yum install -y yum-utils device-mapper-persistent-data lvm2 git curl wget
```

### 2. Installation de Docker

```bash
# Ajouter le repo Docker
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

# Installer Docker
sudo yum install -y docker-ce docker-ce-cli containerd.io

# Démarrer et activer Docker
sudo systemctl start docker
sudo systemctl enable docker

# Vérifier l'installation
docker --version
```

### 3. Installation de Docker Compose

```bash
# Télécharger Docker Compose v2
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Rendre exécutable
sudo chmod +x /usr/local/bin/docker-compose

# Vérifier
docker-compose --version
```

### 4. Configuration utilisateur

```bash
# Ajouter l'utilisateur au groupe docker (remplacer 'votre_user' par votre nom d'utilisateur)
sudo usermod -aG docker $USER

# Recharger les groupes (ou déconnectez-vous et reconnectez-vous)
newgrp docker
```

---

## 📦 Déploiement de l'Application

### 1. Cloner le projet

```bash
# Créer le répertoire
sudo mkdir -p /opt/sendaptnapt
sudo chown $USER:$USER /opt/sendaptnapt

# Cloner le projet (remplacer par votre URL)
cd /opt/sendaptnapt
git clone https://votre-repo-git.senelec.sn/sendaptnapt.git .

# OU copier les fichiers si pas de Git
# scp -r /chemin/local/* user@sendaptnapt:/opt/sendaptnapt/
```

### 2. Configurer l'environnement

```bash
# Copier le fichier d'environnement
cp docker/env.docker.example .env

# Éditer les variables
nano .env
```

**Variables importantes à modifier :**
```ini
APP_KEY=               # Sera généré automatiquement
APP_URL=http://sendaptnapt.senelec.sn

DB_PASSWORD=VotreMotDePassePostgres
MAIL_PASSWORD=VotreMotDePasseMail
LDAP_PASSWORD=VotreMotDePasseLdap
ORACLE_PASSWORD=VotreMotDePasseOracle
```

### 3. Construire et démarrer

```bash
# Construire les images
docker-compose build --no-cache

# Démarrer les services
docker-compose up -d

# Vérifier que tout fonctionne
docker-compose ps
```

### 4. Initialisation Laravel

```bash
# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Exécuter les migrations
docker-compose exec app php artisan migrate --force

# Exécuter les seeders
docker-compose exec app php artisan db:seed --force

# Créer le lien symbolique storage
docker-compose exec app php artisan storage:link

# Optimiser pour la production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize
```

---

## 🔧 Commandes Utiles

### Gestion des conteneurs

```bash
# Voir les logs en temps réel
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f postgres

# Redémarrer les services
docker-compose restart

# Arrêter les services
docker-compose down

# Arrêter et supprimer les volumes (⚠️ DANGER: perte de données)
docker-compose down -v
```

### Artisan et maintenance

```bash
# Exécuter des commandes Artisan
docker-compose exec app php artisan [commande]

# Exemples
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan queue:restart
docker-compose exec app php artisan users:sync-oracle --import-all

# Accès au shell du conteneur
docker-compose exec app bash

# Accès à PostgreSQL
docker-compose exec postgres psql -U sendaptnapt -d sendaptnapt
```

### Mise à jour de l'application

```bash
cd /opt/sendaptnapt

# Télécharger les mises à jour
git pull origin main

# Reconstruire les images si nécessaire
docker-compose build app

# Exécuter les migrations
docker-compose exec app php artisan migrate --force

# Vider les caches
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize

# Redémarrer les workers de queue
docker-compose restart queue scheduler
```

---

## 🔒 Configuration SSL (HTTPS)

### Option 1 : Certificat Let's Encrypt (recommandé)

```bash
# Installer certbot
sudo yum install -y certbot

# Arrêter Nginx temporairement
docker-compose stop nginx

# Obtenir le certificat
sudo certbot certonly --standalone -d sendaptnapt.senelec.sn

# Copier les certificats
sudo cp /etc/letsencrypt/live/sendaptnapt.senelec.sn/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/sendaptnapt.senelec.sn/privkey.pem docker/nginx/ssl/
sudo chown $USER:$USER docker/nginx/ssl/*

# Décommenter la config HTTPS dans docker/nginx/default.conf
# Puis redémarrer
docker-compose up -d nginx
```

### Option 2 : Certificat auto-signé (développement)

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/privkey.pem \
  -out docker/nginx/ssl/fullchain.pem \
  -subj "/CN=sendaptnapt.senelec.sn"
```

---

## 📊 Monitoring et Sauvegardes

### Sauvegardes automatiques

Créer `/opt/sendaptnapt/backup.sh` :

```bash
#!/bin/bash
BACKUP_DIR="/opt/backups/sendaptnapt"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup PostgreSQL
docker-compose exec -T postgres pg_dump -U sendaptnapt sendaptnapt | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup des fichiers uploadés
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz -C /opt/sendaptnapt storage/app

# Garder seulement les 7 derniers backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup terminé: $DATE"
```

```bash
# Rendre exécutable
chmod +x /opt/sendaptnapt/backup.sh

# Ajouter au cron (backup quotidien à 2h du matin)
echo "0 2 * * * /opt/sendaptnapt/backup.sh >> /var/log/sendaptnapt_backup.log 2>&1" | crontab -
```

### Vérification de l'état

```bash
# État des conteneurs
docker-compose ps

# Utilisation des ressources
docker stats

# Espace disque des volumes
docker system df

# Nettoyer les ressources inutilisées
docker system prune -f
```

---

## 🔥 Pare-feu

```bash
# Ouvrir les ports nécessaires
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --reload

# Vérifier
sudo firewall-cmd --list-all
```

---

## ❗ Dépannage

### Problèmes courants

**1. Timeout / "failed to resolve source metadata" / "i/o timeout" (Docker Hub inaccessible)**
```bash
# Le serveur ne peut pas atteindre Docker Hub. Solutions :

# A) Augmenter le timeout Docker (daemon.json)
sudo mkdir -p /etc/docker
sudo tee /etc/docker/daemon.json << 'EOF'
{
  "max-concurrent-downloads": 3,
  "max-concurrent-uploads": 1,
  "registry-mirrors": []
}
EOF
sudo systemctl restart docker

# B) Tester la connectivité vers Docker Hub
curl -I https://registry-1.docker.io/v2/

# C) Si proxy requis, configurer Docker :
# /etc/systemd/system/docker.service.d/http-proxy.conf
# [Service]
# Environment="HTTP_PROXY=http://proxy:port"
# Environment="HTTPS_PROXY=http://proxy:port"
# Puis: sudo systemctl daemon-reload && sudo systemctl restart docker

# D) Contournement : exporter/importer les images (réseau restreint)
# Sur une machine avec accès Internet (ex: votre PC) :
#   cd /chemin/vers/sendaptnapt
#   bash docker/export-images.sh
#   scp docker-images.tar user@serveur:/var/www/sendaptnapt/
# Sur le serveur :
#   cd /var/www/sendaptnapt
#   sudo bash docker/load-images.sh
#   sudo bash docker/deploy.sh first
```

**2. "commande introuvable" ou "Permission denied" avec deploy.sh**
```bash
# Vérifier que le script existe
ls -la docker/deploy.sh

# Corriger les fins de ligne Windows (CRLF → LF) si nécessaire
sed -i 's/\r$//' docker/deploy.sh

# Exécuter avec bash explicitement
sudo bash docker/deploy.sh first

# Ou avec le chemin complet
sudo bash /var/www/sendaptnapt/docker/deploy.sh first
```

**3. Permission denied sur les volumes**
```bash
# Sur le serveur (48 = apache sur CentOS)
sudo chown -R apache:apache /var/www/sendaptnapt/storage
sudo chmod -R 775 /var/www/sendaptnapt/storage
```

**4. Connexion à la base de données impossible**
```bash
# Vérifier que PostgreSQL est prêt
docker-compose logs postgres
docker-compose exec postgres pg_isready -U sendaptnapt
```

**5. Application lente**
```bash
# Vérifier les caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

**6. Espace disque insuffisant**
```bash
# Nettoyer Docker
docker system prune -af
docker volume prune -f
```

**7. Voir les logs d'erreur**
```bash
docker-compose logs -f app 2>&1 | tail -100
docker-compose exec app cat storage/logs/laravel.log | tail -50
```

---

## 📞 Support

En cas de problème :
1. Vérifier les logs : `docker-compose logs -f`
2. Consulter la documentation : `/opt/sendaptnapt/ARCHITECTURE_V2.md`
3. Contacter l'équipe DSI

---

**Bonne chance pour le déploiement ! 🚀**
