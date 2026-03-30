#!/bin/bash
# Backup script for SENDAPTNAPT (DB + storage/app)

set -euo pipefail

if command -v docker-compose >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker-compose"
elif docker compose version >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker compose"
else
  echo "Docker Compose non trouve."
  exit 1
fi

BACKUP_DIR="${BACKUP_DIR:-/opt/backups/sendaptnapt}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
DATE="$(date +%Y%m%d_%H%M%S)"

mkdir -p "$BACKUP_DIR"

echo "[1/3] Backup PostgreSQL..."
$DOCKER_COMPOSE exec -T postgres sh -c 'pg_dump -U "$POSTGRES_USER" "$POSTGRES_DB"' | gzip > "$BACKUP_DIR/db_${DATE}.sql.gz"

echo "[2/3] Backup storage/app..."
tar -czf "$BACKUP_DIR/storage_${DATE}.tar.gz" storage/app 2>/dev/null || true

echo "[3/3] Retention (${RETENTION_DAYS} jours)..."
find "$BACKUP_DIR" -type f -mtime +"$RETENTION_DAYS" -delete 2>/dev/null || true

echo "Backup termine:"
echo " - $BACKUP_DIR/db_${DATE}.sql.gz"
echo " - $BACKUP_DIR/storage_${DATE}.tar.gz"

