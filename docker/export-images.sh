#!/bin/bash
# ==============================================
# Exporter les images Docker nécessaires
# À exécuter sur une machine avec accès à Docker Hub
# Usage: bash docker/export-images.sh
# ==============================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
OUTPUT="$PROJECT_DIR/docker-images.tar"

cd "$PROJECT_DIR"

echo "Téléchargement des images Docker..."
docker pull php:8.3-fpm-alpine
docker pull composer:2
docker pull nginx:alpine
docker pull postgres:15-alpine
docker pull redis:7-alpine

echo "Export des images vers docker-images.tar..."
docker save php:8.3-fpm-alpine composer:2 nginx:alpine postgres:15-alpine redis:7-alpine -o "$OUTPUT"

echo "✅ Fichier docker-images.tar créé ($(du -h "$OUTPUT" | cut -f1))"
echo ""
echo "Transférer sur le serveur :"
echo "  scp docker-images.tar user@serveur:/var/www/sendaptnapt/"
echo ""
echo "Sur le serveur, charger les images :"
echo "  cd /var/www/sendaptnapt"
echo "  sudo docker load -i docker-images.tar"
echo "  sudo bash docker/deploy.sh first"
