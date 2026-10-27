#!/usr/bin/env bash
#
# Build a production release package on the development machine (the hosting server has no Node.js).
# The package contains the committed code, production Composer dependencies and compiled assets.
#
# Usage (from the project root):  deploy/directadmin/build-release.sh
# Output: dist/tindziva-<data>-<commit>.tar.gz and dist/server.sh
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

if [[ -n "$(git status --porcelain)" ]]; then
    echo "Existem alterações por guardar no Git. Faça commit antes de gerar o pacote." >&2
    exit 1
fi

COMMIT="$(git rev-parse --short HEAD)"
NAME="tindziva-$(date +%Y%m%d-%H%M)-${COMMIT}"
BUILD_DIR="$(mktemp -d)"
trap 'rm -rf "$BUILD_DIR"' EXIT

echo "==> A exportar o commit ${COMMIT}"
git archive HEAD | tar -x -C "$BUILD_DIR"

cd "$BUILD_DIR"

echo "==> Dependências PHP (produção)"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --quiet

echo "==> Assets (Vite)"
npm ci --no-audit --no-fund --silent
npm run build >/dev/null
rm -rf node_modules

echo "==> A remover ficheiros de desenvolvimento"
rm -rf tests phpunit.xml .editorconfig .npmrc CLAUDE.md AGENTS.md boost.json .ai .claude
printf '%s\n' "$COMMIT" > REVISION

mkdir -p "$ROOT/dist"
tar -czf "$ROOT/dist/${NAME}.tar.gz" .
cp "$ROOT/deploy/directadmin/server.sh" "$ROOT/dist/server.sh"

echo ""
echo "Pacote criado: dist/${NAME}.tar.gz ($(du -h "$ROOT/dist/${NAME}.tar.gz" | cut -f1))"
echo "Envie para o servidor, por exemplo:"
echo "  scp dist/${NAME}.tar.gz dist/server.sh aemozorg@<servidor>:~/"
