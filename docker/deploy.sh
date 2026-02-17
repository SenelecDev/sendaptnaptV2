#!/bin/bash
# ==============================================
# SENDAPTNAPT - Script de déploiement automatique
# Usage: ./docker/deploy.sh [first|update]
# ==============================================

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   SENDAPTNAPT - Déploiement Docker    ${NC}"
echo -e "${GREEN}========================================${NC}"

# Vérifier Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker n'est pas installé!${NC}"
    exit 1
fi

# Docker Compose: v1 (docker-compose) ou v2 (docker compose)
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
else
    echo -e "${RED}❌ Docker Compose n'est pas installé!${NC}"
    exit 1
fi

MODE=${1:-update}

case $MODE in
    first)
        echo -e "${YELLOW}🚀 Premier déploiement...${NC}"
        
        # Copier l'environnement si pas existe
        if [ ! -f .env ]; then
            echo -e "${YELLOW}📄 Création du fichier .env...${NC}"
            cp docker/env.docker.example .env
            echo -e "${RED}⚠️  IMPORTANT: Éditez le fichier .env avec vos paramètres!${NC}"
            echo -e "${YELLOW}   nano .env${NC}"
            exit 1
        fi

        # Construire et démarrer
        echo -e "${YELLOW}🔨 Construction des images...${NC}"
        $DOCKER_COMPOSE build --no-cache

        echo -e "${YELLOW}🚀 Démarrage des services...${NC}"
        $DOCKER_COMPOSE up -d

        # Attendre que PostgreSQL soit prêt
        echo -e "${YELLOW}⏳ Attente de PostgreSQL...${NC}"
        sleep 10

        # Initialisation Laravel
        echo -e "${YELLOW}🔑 Génération de la clé d'application...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan key:generate --force

        echo -e "${YELLOW}📊 Exécution des migrations...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan migrate --force

        echo -e "${YELLOW}🌱 Exécution des seeders...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan db:seed --force

        echo -e "${YELLOW}🔗 Création du lien storage...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan storage:link || true

        echo -e "${YELLOW}⚡ Optimisation...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan config:cache
        $DOCKER_COMPOSE exec -T app php artisan route:cache
        $DOCKER_COMPOSE exec -T app php artisan view:cache
        $DOCKER_COMPOSE exec -T app php artisan optimize

        echo -e "${GREEN}✅ Déploiement initial terminé!${NC}"
        ;;

    update)
        echo -e "${YELLOW}🔄 Mise à jour...${NC}"

        # Tirer les dernières modifications
        if [ -d .git ]; then
            echo -e "${YELLOW}📥 Récupération des mises à jour Git...${NC}"
            git pull
        fi

        # Reconstruire si nécessaire
        echo -e "${YELLOW}🔨 Reconstruction des images...${NC}"
        $DOCKER_COMPOSE build

        # Redémarrer les services
        echo -e "${YELLOW}🔄 Redémarrage des services...${NC}"
        $DOCKER_COMPOSE up -d

        # Migrations
        echo -e "${YELLOW}📊 Exécution des migrations...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan migrate --force

        # Vider et recréer les caches
        echo -e "${YELLOW}🗑️  Nettoyage des caches...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan optimize:clear
        
        echo -e "${YELLOW}⚡ Optimisation...${NC}"
        $DOCKER_COMPOSE exec -T app php artisan optimize

        # Redémarrer les workers
        echo -e "${YELLOW}🔄 Redémarrage des workers...${NC}"
        $DOCKER_COMPOSE restart queue scheduler

        echo -e "${GREEN}✅ Mise à jour terminée!${NC}"
        ;;

    *)
        echo -e "${RED}Usage: $0 [first|update]${NC}"
        echo "  first  - Premier déploiement"
        echo "  update - Mise à jour de l'application"
        exit 1
        ;;
esac

# Afficher l'état
echo ""
echo -e "${GREEN}📊 État des services:${NC}"
$DOCKER_COMPOSE ps

echo ""
echo -e "${GREEN}🌐 Application disponible sur:${NC}"
echo -e "   http://$(hostname):80"
echo ""
