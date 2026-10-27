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
#   bash server.sh install [pacote.tar.gz]    first installation (from GitHub when no package is given)
#   bash server.sh update  [pacote.tar.gz]    publish a new release without downtime (from GitHub by default)
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
COMPOSER_BIN="${COMPOSER_BIN:-$(command -v composer || echo "$HOME/bin/composer")}"
REPO_URL="${REPO_URL:-https://github.com/EdsonKbrain/Tindziva.git}"
REPO_BRANCH="${REPO_BRANCH:-production}"
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

fetch_release() {
    if [[ -n "${1:-}" ]]; then
        extract_release "$1"
    else
        clone_release
    fi
}

# Download the compiled "production" branch from GitHub and install the PHP dependencies.
clone_release() {
    local target="$RELEASES/tindziva-$(date +%Y%m%d-%H%M%S)"

    git clone --quiet --depth 1 --branch "$REPO_BRANCH" "$REPO_URL" "$target" >&2 \
        || fail "Não foi possível descarregar $REPO_URL ($REPO_BRANCH)."
    git -C "$target" rev-parse --short HEAD > "$target/REVISION"
    rm -rf "$target/.git"

    [[ -f "$target/public/build/manifest.json" ]] \
        || fail "O branch $REPO_BRANCH não tem os assets compilados (public/build). Confirme que o GitHub Actions terminou."

    # --no-scripts: Composer scripts run "php artisan" in a child process, which needs proc_open
    # (disabled on this hosting). Package discovery is run directly afterwards instead.
    if ! (cd "$target" && "$PHP_BIN" "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress --no-scripts >&2 \
        && "$PHP_BIN" artisan package:discover >&2); then
        rm -rf "$target"
        fail "A instalação das dependências PHP falhou."
    fi

    rm -rf "$target/tests" "$target/phpunit.xml" "$target/CLAUDE.md" "$target/AGENTS.md" "$target/boost.json" "$target/.github"
    echo "$target"
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

ENV_CREATED_NOW=0

create_env() {
    [[ ! -f "$SHARED/.env" ]] || return 0
    ENV_CREATED_NOW=1

    step "Configuração (.env)"
    local suggestion="$(whoami)_tindziva"
    echo "Crie primeiro a base de dados em DirectAdmin → MySQL Management e indique os dados."
    echo "No DirectAdmin, os nomes levam o prefixo da conta (por exemplo ${suggestion})."
    read -r -p "Servidor MySQL [localhost]: " db_host
    read -r -p "Nome da base de dados [${suggestion}]: " db_name
    read -r -p "Utilizador da base de dados [${suggestion}]: " db_user
    read -r -s -p "Palavra-passe da base de dados: " db_password; echo
    db_host="${db_host:-localhost}"
    db_name="${db_name:-$suggestion}"
    db_user="${db_user:-$suggestion}"

    umask 077
    cp "$1/.env.production.example" "$SHARED/.env"
    sed -i \
        -e "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" \
        -e "s|^DB_HOST=.*|DB_HOST=${db_host}|" \
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

create_admin() {
    step "Superadministrador"
    echo "Palavra-passe: mínimo de 10 caracteres, com maiúsculas, minúsculas e números."

    while true; do
        read -r -p "Nome completo: " admin_name
        read -r -p "E-mail: " admin_email
        read -r -s -p "Palavra-passe: " admin_password; echo
        read -r -s -p "Confirmar palavra-passe: " admin_confirmation; echo

        if [[ "$admin_password" != "$admin_confirmation" ]]; then
            echo "As palavras-passe não coincidem. Tente novamente."
            continue
        fi

        if (cd "$CURRENT" && TINDZIVA_ADMIN_PASSWORD="$admin_password" "$PHP_BIN" artisan tindziva:create-admin \
            --name="$admin_name" --email="$admin_email" --no-interaction); then
            break
        fi

        echo "Corrija os dados indicados e tente novamente."
    done

    unset admin_password admin_confirmation
}

cmd_install() {
    local package="${1:-}"
    [[ ! -L "$CURRENT" ]] || fail "O website já está instalado. Para publicar uma nova versão use: bash server.sh update"
    check_php
    mkdir -p "$RELEASES" "$SHARED"

    # Remove releases left behind by an interrupted installation (nothing is active yet).
    rm -rf "${RELEASES:?}"/*

    step "A obter a versão ${package:-do GitHub ($REPO_BRANCH)}"
    local release
    release="$(fetch_release "$package")"

    create_env "$release"
    link_shared "$release"

    if ! grep -qE '^APP_KEY=.+' "$SHARED/.env"; then
        artisan "$release" key:generate --force
    fi

    step "Ligação à base de dados"
    local db_error
    if ! db_error="$(cd "$release" && "$PHP_BIN" artisan tindziva:check-database 2>&1)"; then
        printf 'MySQL: %s\n' "$(printf '%s' "$db_error" | tr -s ' \n' ' ' | grep -o 'SQLSTATE.*' || printf '%s' "$db_error" | tr -s ' \n' ' ')" >&2
        if [[ "$ENV_CREATED_NOW" == "1" ]]; then
            # The details were typed in this run: discard them so the next run asks again.
            rm -f "$SHARED/.env"
            fail "Não foi possível ligar à base de dados. Confirme no DirectAdmin o nome (com o prefixo da conta), o utilizador e a palavra-passe, e execute de novo: bash server.sh install"
        fi
        fail "Não foi possível ligar à base de dados. Corrija os dados DB_* em $SHARED/.env e execute de novo: bash server.sh install"
    fi

    prepare_release "$release"

    step "Conteúdo essencial (páginas, serviços, clientes, configurações)"
    artisan "$release" db:seed --force

    activate "$release"
    point_public_html
    install_cron

    if [[ "${SKIP_ADMIN:-0}" != "1" ]]; then
        create_admin
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
    local package="${1:-}"
    [[ -L "$CURRENT" ]] || fail "Ainda não existe instalação. Use: bash server.sh install"
    check_php

    step "A obter a versão ${package:-do GitHub ($REPO_BRANCH)}"
    local release
    release="$(fetch_release "$package")"
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
