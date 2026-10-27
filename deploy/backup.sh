#!/usr/bin/env bash
#
# Backup da base de dados MySQL e dos ficheiros carregados (storage/app/public).
# Lê as credenciais do ficheiro .env da aplicação; nunca as escreva neste script.
#
# Uso:      deploy/backup.sh /caminho/da/aplicacao /caminho/dos/backups [dias_a_manter]
# Cron:     30 2 * * * /var/www/tindziva/deploy/backup.sh /var/www/tindziva /var/backups/tindziva 14
#
set -euo pipefail

APP_DIR="${1:?Indique a pasta da aplicação}"
BACKUP_DIR="${2:?Indique a pasta de destino dos backups}"
KEEP_DAYS="${3:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"

env_value() {
    grep -E "^$1=" "$APP_DIR/.env" | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//'
}

DB_HOST="$(env_value DB_HOST)"
DB_PORT="$(env_value DB_PORT)"
DB_DATABASE="$(env_value DB_DATABASE)"
DB_USERNAME="$(env_value DB_USERNAME)"
DB_PASSWORD="$(env_value DB_PASSWORD)"

mkdir -p "$BACKUP_DIR"
umask 077

# The password is passed through a temporary option file so it never appears in the process list.
MYSQL_CNF="$(mktemp)"
trap 'rm -f "$MYSQL_CNF"' EXIT
printf '[client]\nuser=%s\npassword=%s\nhost=%s\nport=%s\n' "$DB_USERNAME" "$DB_PASSWORD" "${DB_HOST:-127.0.0.1}" "${DB_PORT:-3306}" > "$MYSQL_CNF"

mysqldump --defaults-extra-file="$MYSQL_CNF" --single-transaction --quick --routines --no-tablespaces "$DB_DATABASE" \
    | gzip > "$BACKUP_DIR/db-$STAMP.sql.gz"

tar -czf "$BACKUP_DIR/files-$STAMP.tar.gz" -C "$APP_DIR/storage/app" public

find "$BACKUP_DIR" -type f \( -name 'db-*.sql.gz' -o -name 'files-*.tar.gz' \) -mtime +"$KEEP_DAYS" -delete

echo "Backup concluído: $BACKUP_DIR (db-$STAMP.sql.gz, files-$STAMP.tar.gz)"
