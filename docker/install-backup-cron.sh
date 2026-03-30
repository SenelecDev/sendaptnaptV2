#!/bin/bash
# Install/update daily backup cron for SENDAPTNAPT

set -euo pipefail

PROJECT_DIR="${PROJECT_DIR:-/opt/sendaptnapt}"
SCHEDULE="${SCHEDULE:-0 2 * * *}"
LOG_FILE="${LOG_FILE:-/var/log/sendaptnapt_backup.log}"
BACKUP_DIR="${BACKUP_DIR:-/opt/backups/sendaptnapt}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"

CRON_CMD="cd ${PROJECT_DIR} && BACKUP_DIR=${BACKUP_DIR} RETENTION_DAYS=${RETENTION_DAYS} bash docker/backup.sh >> ${LOG_FILE} 2>&1"
NEW_LINE="${SCHEDULE} ${CRON_CMD}"

TMP_FILE="$(mktemp)"
crontab -l 2>/dev/null | rg -v 'docker/backup\.sh' > "$TMP_FILE" || true
echo "$NEW_LINE" >> "$TMP_FILE"
crontab "$TMP_FILE"
rm -f "$TMP_FILE"

echo "Cron backup installe:"
echo "$NEW_LINE"

