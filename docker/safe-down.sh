#!/bin/bash
# Usage:
#   bash docker/safe-down.sh          -> docker compose down
#   ALLOW_VOLUME_DELETE=1 bash docker/safe-down.sh --with-volumes -> docker compose down -v

set -e

if command -v docker-compose >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker-compose"
elif docker compose version >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker compose"
else
  echo "Docker Compose not found."
  exit 1
fi

if [ "${1:-}" = "--with-volumes" ]; then
  if [ "${ALLOW_VOLUME_DELETE:-0}" != "1" ]; then
    echo "Refus: suppression des volumes bloquee."
    echo "Pour confirmer: ALLOW_VOLUME_DELETE=1 bash docker/safe-down.sh --with-volumes"
    exit 1
  fi
  echo "Arret + suppression des volumes..."
  $DOCKER_COMPOSE down -v
  exit 0
fi

echo "Arret des conteneurs (volumes conserves)..."
$DOCKER_COMPOSE down

