#!/bin/sh
set -eu

REPO="brsxdlols/mkauth-gerador-nfcom"
BRANCH="main"
MK_ROOT="/opt/mk-auth"
ADDON_REL="admin/addons/gerador_nf_dici"
LAYOUT_REL="print_pdf/nfcom/modelo01"
MENU_REL="admin/addons/addon.js"
BACKUP_ROOT="$MK_ROOT/backups/gerador_nf_dici"
BEGIN_MARKER="// BEGIN mkauth-gerador-nfcom (managed)"
END_MARKER="// END mkauth-gerador-nfcom (managed)"

say() { printf '%s\n' "[mkauth-gerador-nfcom] $*"; }
die() { say "ERRO: $*" >&2; exit 1; }
need() { command -v "$1" >/dev/null 2>&1 || die "Comando obrigatório ausente: $1"; }

[ "$(id -u)" -eq 0 ] || die "Execute como root."
[ -d "$MK_ROOT/admin/addons" ] || die "MK-Auth não encontrado em $MK_ROOT."
[ -r "$MK_ROOT/include/conexao.php" ] || die "Conexão padrão do MK-Auth não encontrada."
need tar
need php

download() {
    url="$1" dest="$2"
    if command -v curl >/dev/null 2>&1; then
        curl --fail --location --silent --show-error --retry 3 --connect-timeout 15 "$url" -o "$dest"
    elif command -v wget >/dev/null 2>&1; then
        wget -q --timeout=30 -O "$dest" "$url"
    else
        die "Instale curl ou wget."
    fi
}

restore_backup() {
    backup="$1"
    [ "$backup" = latest ] && backup="$(find "$BACKUP_ROOT" -mindepth 1 -maxdepth 1 -type d | sort | tail -n 1)"
    [ -n "$backup" ] && [ -d "$backup" ] || die "Backup não encontrado: $1"
    say "Restaurando $backup"
    [ -d "$backup/addon" ] && { rm -rf "$MK_ROOT/$ADDON_REL"; cp -a "$backup/addon" "$MK_ROOT/$ADDON_REL"; }
    [ -d "$backup/layout" ] && { rm -rf "$MK_ROOT/$LAYOUT_REL"; cp -a "$backup/layout" "$MK_ROOT/$LAYOUT_REL"; }
    [ -f "$backup/addon.js" ] && cp -a "$backup/addon.js" "$MK_ROOT/$MENU_REL"
    say "Rollback concluído."
    exit 0
}

if [ "${1:-}" = "--rollback" ]; then
    restore_backup "${2:-latest}"
fi

timestamp="$(date +%Y%m%d-%H%M%S)"
tmp="$(mktemp -d /tmp/mkauth-gerador-nfcom.XXXXXX)"
trap 'rm -rf "$tmp"' EXIT HUP INT TERM
archive="$tmp/source.tar.gz"
url="https://github.com/$REPO/archive/refs/heads/$BRANCH.tar.gz"
say "Baixando a versão mais recente da branch $BRANCH..."
download "$url" "$archive"
tar -xzf "$archive" -C "$tmp"
src="$(find "$tmp" -mindepth 1 -maxdepth 1 -type d -name 'mkauth-gerador-nfcom-*' | head -n 1)"
[ -d "$src/addon" ] && [ -d "$src/layout/modelo01" ] || die "Pacote incompleto."

version="$(tr -d '\r\n' < "$src/VERSION")"
say "Validando versão $version com PHP $(php -r 'echo PHP_VERSION;')..."
find "$src/addon" -type f \( -name '*.php' -o -name '*.hhvm' \) -exec php -l {} \; >/dev/null
[ -s "$src/layout/modelo01/nota.html" ] || die "layout/nota.html ausente."
[ -s "$src/layout/modelo01/topo.html" ] || die "layout/topo.html ausente."

backup="$BACKUP_ROOT/$timestamp"
mkdir -p "$backup"
[ ! -e "$MK_ROOT/$ADDON_REL" ] || cp -a "$MK_ROOT/$ADDON_REL" "$backup/addon"
[ ! -e "$MK_ROOT/$LAYOUT_REL" ] || cp -a "$MK_ROOT/$LAYOUT_REL" "$backup/layout"
[ ! -e "$MK_ROOT/$MENU_REL" ] || cp -a "$MK_ROOT/$MENU_REL" "$backup/addon.js"
say "Backup criado em $backup"

stage_addon="$tmp/addon.new"
stage_layout="$tmp/layout.new"
cp -a "$src/addon" "$stage_addon"
cp -a "$src/layout/modelo01" "$stage_layout"

# addons.class.php é reservado e pode ser recriado pelo próprio MK-Auth.
rm -f "$stage_addon/addons.class.php"
if [ -e "$MK_ROOT/include/addons.inc.hhvm" ]; then
    ln -s "$MK_ROOT/include/addons.inc.hhvm" "$stage_addon/addons.class.php"
elif [ -e "$MK_ROOT/$ADDON_REL/addons.class.php" ] || [ -L "$MK_ROOT/$ADDON_REL/addons.class.php" ]; then
    cp -a "$MK_ROOT/$ADDON_REL/addons.class.php" "$stage_addon/addons.class.php"
fi
find "$stage_addon" -type d -exec chmod 755 {} \;
find "$stage_addon" -type f -exec chmod 644 {} \;
find "$stage_layout" -type d -exec chmod 755 {} \;
find "$stage_layout" -type f -exec chmod 644 {} \;
chown -R root:root "$stage_addon" "$stage_layout"

rm -rf "$MK_ROOT/$ADDON_REL.new" "$MK_ROOT/$LAYOUT_REL.new"
mkdir -p "$(dirname "$MK_ROOT/$ADDON_REL")" "$(dirname "$MK_ROOT/$LAYOUT_REL")"
mv "$stage_addon" "$MK_ROOT/$ADDON_REL.new"
mv "$stage_layout" "$MK_ROOT/$LAYOUT_REL.new"
rm -rf "$MK_ROOT/$ADDON_REL" "$MK_ROOT/$LAYOUT_REL"
mv "$MK_ROOT/$ADDON_REL.new" "$MK_ROOT/$ADDON_REL"
mv "$MK_ROOT/$LAYOUT_REL.new" "$MK_ROOT/$LAYOUT_REL"

menu="$MK_ROOT/$MENU_REL"
[ -f "$menu" ] || : > "$menu"
awk -v begin="$BEGIN_MARKER" -v end="$END_MARKER" '
    $0 == begin { managed=1; next }
    $0 == end { managed=0; next }
    managed { next }
    /addons\/gerador_nf_dici\// { next }
    { print }
' "$menu" > "$tmp/addon.js"
{
    cat "$tmp/addon.js"
    printf '\n%s\n' "$BEGIN_MARKER"
    printf '%s\n' "add_menu.opcoes('{\"plink\": \"' + minha_url + 'addons/gerador_nf_dici/index.hhvm\", \"ptext\": \"NFCOM / Gerador NF DICI\"}');"
    printf '%s\n' "$END_MARKER"
} > "$tmp/addon.js.new"
cp "$tmp/addon.js.new" "$menu"
chmod 644 "$menu"
chown root:root "$menu"

find "$MK_ROOT/$ADDON_REL" -type f \( -name '*.php' -o -name '*.hhvm' \) -exec php -l {} \; >/dev/null
count="$(grep -cF "$BEGIN_MARKER" "$menu")"
[ "$count" -eq 1 ] || die "Registro de menu inválido; use --rollback $backup"
printf '%s\n' "$version" > "$MK_ROOT/$ADDON_REL/.installed-version"

say "Instalação $version concluída."
say "Addon: $MK_ROOT/$ADDON_REL"
say "Layout: $MK_ROOT/$LAYOUT_REL"
say "Backup/rollback: sh install.sh --rollback $backup"
