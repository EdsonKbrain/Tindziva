#!/usr/bin/env bash
#
# Install and update the Tindziva website on DirectAdmin shared hosting (no root, no Node.js).
#
# Layout inside ~/domains/<domínio>/:
#   app/releases/<versão>/   each uploaded release
#   app/shared/.env          production configuration (kept between releases)
#   app/shared/storage/      uploads, logs, cache and sessions (kept between releases)
#   app/current -> releases/<versão activa>
#   public_html -> app/current/public
#
# Usage:
#   bash server.sh install <pacote.tar.gz>    first installation
#   bash server.sh update  <pacote.tar.gz>    publish a new release (no downtime)
#   bash server.sh rollback                   return to the previous release
#   bash server.sh refresh                    re-cache configuration after editing app/shared/.env
#   bash server.sh status                     show the active release and checks
#
set -euo pipefail

DOMAIN="${DOMAIN:-tindziva.co.mz}"
DOMAIN_DIR="${DOMAIN_DIR:-$HOME/domains/$DOMAIN}"
APP_BASE="$DOMAIN_DIR/app"
RELEASES="$APP_BASE/releases"
SHARED="$APP_BASE/shared"
CURRENT="$APP_BASE/current"
PHP_BIN="${PHP_BIN:-$(command -v php)}"
KEEP_RELEASES=3

step() { printf '\n\033[1;33m==> %s\033[0m\n' "$1"; }
fail() { printf '\033[1;31mErro: %s\033[0m\n' "$1" >&2; exit 1; }
artisan() { (cd "$1" && "$PHP_BIN" artisan "${@:2}"); }

check_php() {
    "$PHP_BIN" -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' \
        || fail "É necessário PHP 8.3 ou superior na linha de comandos ($("$PHP_BIN" -r 'echo PHP_VERSION;'))."

    local missing
    missing="$("$PHP_BIN" -r 'echo implode(" ", array_diff(["pdo_mysql","gd","intl","mbstring","exif","fileinfo","zip","curl","xml","openssl"], array_map("strtolower", get_loaded_extensions())));')"
    [[ -z "$missing" ]] || fail "Extensões PHP em falta: $missing"
}

extract_release() {
    local package="$1"
    [[ -f "$package" ]] || fail "Pacote não encontrado: $package"

    local name
    name="$(basename "$package" .tar.gz)"
    local target="$RELEASES/$name"
    [[ ! -e "$target" ]] || fail "A versão $name já existe em $RELEASES."

    mkdir -p "$target"
    tar -xzf "$package" -C "$target"
    echo "$target"
}

link_shared() {
    local release="$1"

    if [[ ! -d "$SHARED/storage" ]]; then
        cp -R "$release/storage" "$SHARED/storage"
    fi

    chmod 600 "$SHARED/.env"
    rm -rf "$release/storage"
    ln -s "$SHARED/storage" "$release/storage"
    ln -sfn "$SHARED/.env" "$release/.env"
    ln -sfn ../storage/app/public "$release/public/storage"
}

prepare_release() {
    local release="$1"

    step "Migrations da base de dados"
    artisan "$release" migrate --force

    step "Caches de produção"
    artisan "$release" optimize:clear >/dev/null
    artisan "$release" optimize
    artisan "$release" icons:cache
}

activate() {
    local release="$1"
    ln -sfn "$release" "$CURRENT.next"
    mv -Tf "$CURRENT.next" "$CURRENT"
    echo "Versão activa: $(basename "$release")"
}

cleanup_old_releases() {
    local old
    old="$(ls -1dt "$RELEASES"/*/ 2>/dev/null | tail -n +$((KEEP_RELEASES + 1)) || true)"
    [[ -z "$old" ]] || { echo "$old" | xargs rm -rf; echo "Versões antigas removidas."; }
}

point_public_html() {
    local web_root="$DOMAIN_DIR/public_html"

    if [[ -L "$web_root" ]]; then
        return
    fi

    if [[ -d "$web_root" ]]; then
        local backup="$DOMAIN_DIR/public_html.backup-$(date +%Y%m%d%H%M%S)"
        mv "$web_root" "$backup"
        echo "A pasta public_html original foi guardada em $(basename "$backup")."
    fi

    ln -s app/current/public "$web_root"
    echo "public_html aponta agora para app/current/public."
}

install_cron() {
    local schedule="* * * * * cd $CURRENT && $PHP_BIN artisan schedule:run >> /dev/null 2>&1"
    local backup="30 2 * * * bash $CURRENT/deploy/backup.sh $CURRENT $HOME/backups/tindziva 14 >> $SHARED/storage/logs/backup.log 2>&1"

    mkdir -p "$HOME/backups/tindziva"
    chmod 700 "$HOME/backups/tindziva"

    local existing
    existing="$(crontab -l 2>/dev/null || true)"
    {
        printf '%s\n' "$existing" | grep -v "artisan schedule:run" | grep -v "deploy/backup.sh" | sed '/^$/d' || true
        echo "$schedule"
        echo "$backup"
    } | crontab -
    echo "Cron instalado: scheduler a cada minuto e backup diário às 02:30."
}

create_env() {
    [[ ! -f "$SHARED/.env" ]] || return 0

    step "Configuração (.env)"
    echo "Crie primeiro a base de dados em DirectAdmin → MySQL Management e indique os dados:"
    read -r -p "Nome da base de dados: " db_name
    read -r -p "Utilizador da base de dados: " db_user
    read -r -s -p "Palavra-passe da base de dados: " db_password; echo

    umask 077
    cp "$1/.env.production.example" "$SHARED/.env"
    sed -i \
        -e "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" \
        -e "s|^DB_DATABASE=.*|DB_DATABASE=${db_name}|" \
        -e "s|^DB_USERNAME=.*|DB_USERNAME=${db_user}|" \
        "$SHARED/.env"
    # The password is written separately so special characters are not interpreted by sed.
    DB_PASSWORD_VALUE="$db_password" "$PHP_BIN" -r '
        $file = $argv[1];
        $value = str_replace(["\\", "\""], ["\\\\", "\\\""], getenv("DB_PASSWORD_VALUE"));
        file_put_contents($file, preg_replace("/^DB_PASSWORD=.*$/m", "DB_PASSWORD=\"".$value."\"", file_get_contents($file)));
    ' "$SHARED/.env"
    chmod 600 "$SHARED/.env"
}

cmd_install() {
    local package="${1:?Indique o pacote .tar.gz}"
    check_php
    mkdir -p "$RELEASES" "$SHARED"

    step "A extrair o pacote"
    local release
    release="$(extract_release "$package")"

    create_env "$release"
    link_shared "$release"

    if ! grep -qE '^APP_KEY=.+' "$SHARED/.env"; then
        artisan "$release" key:generate --force
    fi

    step "Ligação à base de dados"
    artisan "$release" db:show --counts >/dev/null || fail "Não foi possível ligar à base de dados. Verifique $SHARED/.env."

    prepare_release "$release"

    step "Conteúdo essencial (páginas, serviços, clientes, configurações)"
    artisan "$release" db:seed --force

    activate "$release"
    point_public_html
    install_cron

    if [[ "${SKIP_ADMIN:-0}" != "1" ]]; then
        step "Superadministrador"
        artisan "$CURRENT" tindziva:create-admin
    fi

    step "Instalação concluída"
    cat <<DONE
Falta fazer no DirectAdmin:
  1. Domain Setup → ${DOMAIN} → activar SSL e «Force SSL with https redirect».
  2. Confirmar que o domínio usa PHP 8.3 ou superior (Select PHP version / PHP Selector).
  3. Criar a conta de e-mail nao-responder@${DOMAIN} e preencher MAIL_* em:
       $SHARED/.env
     depois executar: bash server.sh refresh
DONE
}

cmd_update() {
    local package="${1:?Indique o pacote .tar.gz}"
    [[ -L "$CURRENT" ]] || fail "Ainda não existe instalação. Use: bash server.sh install <pacote>"
    check_php

    step "A extrair o pacote"
    local release
    release="$(extract_release "$package")"
    link_shared "$release"

    prepare_release "$release"
    activate "$release"
    cleanup_old_releases

    step "Actualização concluída"
}

cmd_rollback() {
    [[ -L "$CURRENT" ]] || fail "Não existe instalação."
    local active previous
    active="$(readlink -f "$CURRENT")"
    previous="$(ls -1dt "$RELEASES"/*/ | sed 's:/$::' | grep -vx "$active" | head -n1 || true)"
    [[ -n "$previous" ]] || fail "Não existe versão anterior."

    artisan "$previous" optimize
    activate "$previous"
    echo "Atenção: as migrations da versão anulada não são revertidas automaticamente."
}

cmd_refresh() {
    [[ -L "$CURRENT" ]] || fail "Não existe instalação."
    artisan "$CURRENT" optimize
    echo "Configuração recarregada."
}

cmd_status() {
    [[ -L "$CURRENT" ]] || fail "Não existe instalação."
    echo "Versão activa: $(basename "$(readlink -f "$CURRENT")") (commit $(cat "$CURRENT/REVISION" 2>/dev/null || echo '?'))"
    echo "Versões disponíveis:"; ls -1t "$RELEASES"
    echo "PHP: $("$PHP_BIN" -r 'echo PHP_VERSION;')"
    artisan "$CURRENT" about --only=environment
    artisan "$CURRENT" schedule:list
}

case "${1:-}" in
    install) cmd_install "${2:-}" ;;
    update) cmd_update "${2:-}" ;;
    rollback) cmd_rollback ;;
    refresh) cmd_refresh ;;
    status) cmd_status ;;
    *) sed -n '3,20p' "$0"; exit 1 ;;
esac
