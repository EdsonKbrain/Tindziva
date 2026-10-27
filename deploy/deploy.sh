#!/usr/bin/env bash
#
# Deploy the latest version of the Tindziva website. Run as the "tindziva" user from the app folder:
#   cd /var/www/tindziva && deploy/deploy.sh
# The first installation (called by provision.sh) passes --first to also seed the essential content.
#
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_FPM_SERVICE="php8.4-fpm"
FIRST_DEPLOY=false
[[ "${1:-}" == "--first" ]] && FIRST_DEPLOY=true

cd "$APP_DIR"

if [[ "$(whoami)" == "root" ]]; then
    echo "Não execute como root. Use: sudo -u tindziva deploy/deploy.sh" >&2
    exit 1
fi

on_error() {
    echo "" >&2
    echo "O deploy falhou. O website fica em modo de manutenção para não servir uma versão incompleta." >&2
    echo "Corrija o problema e execute de novo deploy/deploy.sh, ou reponha com: php artisan up" >&2
}
trap on_error ERR

if [[ "$FIRST_DEPLOY" == false ]]; then
    php artisan down --retry=30 --refresh=15 || true
    git pull --ff-only
fi

composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
npm ci --no-audit --no-fund
npm run build
rm -rf node_modules

if [[ "$FIRST_DEPLOY" == true ]] && ! grep -qE '^APP_KEY=.+' .env; then
    php artisan key:generate --force
fi

php artisan migrate --force

if [[ "$FIRST_DEPLOY" == true ]]; then
    php artisan db:seed --force
fi

[[ -L public/storage ]] || php artisan storage:link

php artisan optimize:clear
php artisan optimize
php artisan icons:cache

sudo /usr/bin/systemctl reload "$PHP_FPM_SERVICE"

php artisan up

trap - ERR
echo "Deploy concluído: $(git log -1 --pretty='%h %s')"
