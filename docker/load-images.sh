#!/bin/bash
# ==============================================
# Charger les images Docker depuis un fichier exporté
# À exécuter sur le serveur après transfert de docker-images.tar
# Usage: sudo bash docker/load-images.sh
# ==============================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
ARCHIVE="${1:-$PROJECT_DIR/docker-images.tar}"

if [ ! -f "$ARCHIVE" ]; then
    echo "❌ Fichier non trouvé: $ARCHIVE"
    echo "Usage: sudo bash docker/load-images.sh [chemin/vers/docker-images.tar]"
    exit 1
fi

echo "Chargement des images depuis $ARCHIVE..."
docker load -i "$ARCHIVE"

echo "✅ Images chargées. Vous pouvez maintenant exécuter :"
echo "   sudo bash docker/deploy.sh first"
