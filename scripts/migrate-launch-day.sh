#!/bin/bash
# =============================================================
# SCRIPT DE MIGRATION JOUR J - V1 → V2
# À exécuter sur le serveur de production (10.101.3.217)
# depuis le répertoire /var/www/sendaptnapt
# =============================================================
#
# Usage:
#   cd /var/www/sendaptnapt
#   sudo bash scripts/migrate-launch-day.sh
#
# Ce script effectue dans l'ordre :
#   1. Pull du dernier code
#   2. Migration complète V1 → V2 (truncate + réinsertion)
#   3. Copie des fichiers (PDFs, schémas, etc.)
#   4. Correction des permissions
#   5. Nettoyage du cache
# =============================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

V1_STORAGE="/var/www/html/desa/storage/app/public"
V2_CONTAINER_STORAGE="/var/www/html/storage/app/public"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  MIGRATION V1 → V2 - JOUR DU LANCEMENT${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

if [ ! -f "docker-compose.yml" ]; then
    echo -e "${RED}Erreur: Exécutez ce script depuis /var/www/sendaptnapt${NC}"
    exit 1
fi

# Étape 1 : Pull du code
echo -e "${YELLOW}[1/6] Récupération du dernier code...${NC}"
sudo git pull origin main
echo -e "${GREEN}  ✓ Code à jour${NC}"
echo ""

# Étape 2 : Rebuild si nécessaire et restart
echo -e "${YELLOW}[2/6] Redémarrage des conteneurs...${NC}"
sudo docker compose up -d
echo -e "${GREEN}  ✓ Conteneurs démarrés${NC}"
echo ""

# Étape 3 : Vider le cache
echo -e "${YELLOW}[3/6] Nettoyage du cache...${NC}"
sudo docker compose exec -T app php artisan optimize:clear
echo -e "${GREEN}  ✓ Cache vidé${NC}"
echo ""

# Étape 4 : Migration des données V1 → V2
echo -e "${YELLOW}[4/6] Migration des données V1 → V2 (truncate + réinsertion complète)...${NC}"
echo -e "${YELLOW}  Cela va vider les tables V2 et réinsérer toutes les données de V1.${NC}"
echo ""

START_TIME=$(date +%s)

sudo docker compose exec -T app php artisan migrate:v1-data --truncate --force

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
echo ""
echo -e "${GREEN}  ✓ Migration des données terminée en ${DURATION}s${NC}"

# Vérification que les données sont bien persistées
echo ""
echo -e "${YELLOW}  Vérification des données...${NC}"
COUNTS=$(sudo docker compose exec -T app php artisan tinker --execute="echo DB::table('demandes')->count() . '|' . DB::table('notes')->count() . '|' . DB::table('charges_travaux')->count();")
DEMANDES_COUNT=$(echo "$COUNTS" | tr -d '[:space:]' | cut -d'|' -f1)
NOTES_COUNT=$(echo "$COUNTS" | tr -d '[:space:]' | cut -d'|' -f2)
CT_COUNT=$(echo "$COUNTS" | tr -d '[:space:]' | cut -d'|' -f3)

echo -e "  Demandes: ${DEMANDES_COUNT} | Notes: ${NOTES_COUNT} | CT externes: ${CT_COUNT}"

if [ "$DEMANDES_COUNT" = "0" ]; then
    echo -e "${RED}  ✗ ERREUR: Aucune demande en base ! La migration a échoué.${NC}"
    exit 1
fi
echo -e "${GREEN}  ✓ Données vérifiées${NC}"
echo ""

# Étape 5 : Copie des fichiers V1 → V2
echo -e "${YELLOW}[5/6] Copie des fichiers (PDFs, schémas, documents)...${NC}"

CONTAINER_ID=$(sudo docker compose ps -q app)

for dir in pdfs schema documents fiches_manoeuvre signatures stamps pdf etudes; do
    if [ -d "${V1_STORAGE}/${dir}" ]; then
        FILE_COUNT=$(find "${V1_STORAGE}/${dir}" -type f | wc -l)
        sudo docker compose exec -T app mkdir -p "${V2_CONTAINER_STORAGE}/${dir}"
        sudo docker cp "${V1_STORAGE}/${dir}/." "${CONTAINER_ID}:${V2_CONTAINER_STORAGE}/${dir}/"
        echo -e "${GREEN}  ✓ ${dir} : ${FILE_COUNT} fichiers copiés${NC}"
    else
        echo -e "  - ${dir} : absent en V1, ignoré"
    fi
done

echo ""

# Étape 6 : Permissions et symlink
echo -e "${YELLOW}[6/6] Correction des permissions et symlink storage...${NC}"
sudo docker compose exec -T app chown -R 48:48 /var/www/html/storage/
sudo docker compose exec -T app chmod -R 775 /var/www/html/storage/
sudo docker compose exec -T app php artisan storage:link 2>/dev/null || true
sudo docker compose exec -T app php artisan optimize:clear
echo -e "${GREEN}  ✓ Permissions corrigées${NC}"
echo ""

# Résumé
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}  MIGRATION TERMINÉE AVEC SUCCÈS !${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo -e "  Durée totale migration données : ${DURATION}s"
echo ""
echo -e "${YELLOW}  Prochaines étapes :${NC}"
echo -e "  1. Vérifier l'application sur http://10.101.3.217:8888"
echo -e "  2. Tester quelques demandes et notes migrées"
echo -e "  3. Vérifier les PDFs et documents"
echo -e "  4. Si tout est OK, désactiver/arrêter la V1"
echo ""
