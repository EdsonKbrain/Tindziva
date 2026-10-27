#!/usr/bin/env bash
#
# One-time setup of an Ubuntu 22.04/24.04 VPS for the Tindziva website.
# Installs PHP 8.4, Nginx, MySQL, Composer, Node.js and Certbot, creates the database,
# the "tindziva" system user and the .env file, and configures Nginx, PHP-FPM and cron.
#
# Usage (as root):
#   bash provision.sh <domínio> <url-do-repositório-git> [email-para-certificado-https]
# Example:
#   bash provision.sh tindziva.co.mz git@github.com:turing/tindziva.git ti@tindziva.co.mz
#
set -euo pipefail

DOMAIN="${1:?Indique o domínio, por exemplo tindziva.co.mz}"
REPO_URL="${2:?Indique o URL do repositório Git}"
CERT_EMAIL="${3:-}"

APP_USER="tindziva"
APP_DIR="/var/www/tindziva"
BACKUP_DIR="/var/backups/tindziva"
PHP="8.4"
DB_NAME="tindziva"
DB_USER="tindziva"

if [[ $EUID -ne 0 ]]; then
    echo "Execute como root (sudo bash provision.sh …)." >&2
    exit 1
fi

step() { printf '\n\033[1;33m==> %s\033[0m\n' "$1"; }

step "Pacotes do sistema"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y software-properties-common ca-certificates curl gnupg git unzip ufw
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y nginx mysql-server certbot python3-certbot-nginx \
    "php${PHP}-fpm" "php${PHP}-cli" "php${PHP}-mysql" "php${PHP}-mbstring" "php${PHP}-xml" \
    "php${PHP}-curl" "php${PHP}-zip" "php${PHP}-gd" "php${PHP}-intl" "php${PHP}-bcmath" "php${PHP}-opcache"

step "Composer"
if ! command -v composer >/dev/null; then
    EXPECTED="$(curl -fsSL https://composer.github.io/installer.sig)"
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
    ACTUAL="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"
    [[ "$EXPECTED" == "$ACTUAL" ]] || { echo "Assinatura do instalador do Composer inválida." >&2; exit 1; }
    php /tmp/composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer
    rm /tmp/composer-setup.php
fi

step "Node.js 22 (para compilar os assets)"
if ! command -v node >/dev/null || [[ "$(node -v | cut -d. -f1 | tr -d v)" -lt 20 ]]; then
    install -d -m 0755 /etc/apt/keyrings
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" > /etc/apt/sources.list.d/nodesource.list
    apt-get update -y
    apt-get install -y nodejs
fi

step "Utilizador e pastas"
id "$APP_USER" >/dev/null 2>&1 || adduser --system --group --shell /bin/bash --home "/home/${APP_USER}" "$APP_USER"
install -d -o "$APP_USER" -g "$APP_USER" -m 0755 "$APP_DIR"
install -d -o "$APP_USER" -g "$APP_USER" -m 0700 "$BACKUP_DIR"
install -d -o "$APP_USER" -g "$APP_USER" -m 0700 "/home/${APP_USER}/.ssh"

if [[ ! -f "/home/${APP_USER}/.ssh/id_ed25519" ]]; then
    sudo -u "$APP_USER" ssh-keygen -t ed25519 -N "" -C "deploy@${DOMAIN}" -f "/home/${APP_USER}/.ssh/id_ed25519" >/dev/null
fi

step "Base de dados MySQL"
CREDENTIALS_FILE="/root/tindziva-credenciais.txt"
if [[ -f "$CREDENTIALS_FILE" ]]; then
    DB_PASSWORD="$(grep '^DB_PASSWORD=' "$CREDENTIALS_FILE" | cut -d= -f2-)"
else
    DB_PASSWORD="$(openssl rand -base64 32 | tr -dc 'A-Za-z0-9' | head -c 32)"
    umask 077
    printf 'DB_DATABASE=%s\nDB_USERNAME=%s\nDB_PASSWORD=%s\n' "$DB_NAME" "$DB_USER" "$DB_PASSWORD" > "$CREDENTIALS_FILE"
fi
mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

step "Código da aplicação"
if [[ ! -d "${APP_DIR}/.git" ]]; then
    echo "Chave pública de deploy (adicione-a ao repositório como «Deploy key» só de leitura):"
    cat "/home/${APP_USER}/.ssh/id_ed25519.pub"
    read -r -p "Prima Enter depois de adicionar a chave ao repositório… "
    sudo -u "$APP_USER" GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=accept-new" git clone "$REPO_URL" "$APP_DIR"
fi

if [[ ! -f "${APP_DIR}/.env" ]]; then
    sudo -u "$APP_USER" cp "${APP_DIR}/.env.production.example" "${APP_DIR}/.env"
    sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|; s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" "${APP_DIR}/.env"
    chmod 640 "${APP_DIR}/.env"
fi

step "PHP-FPM"
cp "${APP_DIR}/deploy/php-fpm-pool.conf" "/etc/php/${PHP}/fpm/pool.d/tindziva.conf"
cat > "/etc/php/${PHP}/fpm/conf.d/99-tindziva.ini" <<INI
opcache.enable=1
opcache.memory_consumption=192
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=16
INI
cat > /etc/sudoers.d/tindziva <<SUDO
${APP_USER} ALL=(root) NOPASSWD: /usr/bin/systemctl reload php${PHP}-fpm
SUDO
chmod 440 /etc/sudoers.d/tindziva
systemctl restart "php${PHP}-fpm"

step "Primeira instalação da aplicação"
sudo -u "$APP_USER" bash -c "cd '${APP_DIR}' && deploy/deploy.sh --first"

step "Nginx"
sed "s/tindziva\.co\.mz/${DOMAIN}/g" "${APP_DIR}/deploy/nginx.conf" > /etc/nginx/sites-available/tindziva
ln -sf /etc/nginx/sites-available/tindziva /etc/nginx/sites-enabled/tindziva
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

step "Firewall"
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

step "Cron (scheduler e backups)"
crontab -u "$APP_USER" "${APP_DIR}/deploy/crontab"

if [[ -n "$CERT_EMAIL" ]]; then
    step "Certificado HTTPS (Let's Encrypt)"
    certbot --nginx --non-interactive --agree-tos --redirect -m "$CERT_EMAIL" -d "$DOMAIN" -d "www.${DOMAIN}"
fi

step "Concluído"
cat <<DONE
Próximos passos:
  1. Configure o SMTP em ${APP_DIR}/.env (MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD) e execute:
       sudo -u ${APP_USER} bash -c "cd ${APP_DIR} && php artisan config:cache"
  2. Crie o superadministrador:
       sudo -u ${APP_USER} bash -c "cd ${APP_DIR} && php artisan tindziva:create-admin"
  3. Credenciais da base de dados guardadas em ${CREDENTIALS_FILE} (só root).
DONE
