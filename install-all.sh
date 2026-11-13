#!/bin/sh
set -eu
# Instalacao unica: cada instalador oficial cria seu backup e valida o pacote.
tmp="$(mktemp -d /tmp/mkauth-completo.XXXXXX)"
trap 'rm -rf "$tmp"' EXIT HUP INT TERM
for repo in mkauth-gerador-nfcom mkauth-dici; do
    curl -fsSL --retry 3 --connect-timeout 15 "https://raw.githubusercontent.com/brsxdlols/$repo/main/install.sh" -o "$tmp/$repo.sh"
done
sh "$tmp/mkauth-gerador-nfcom.sh"
sh "$tmp/mkauth-dici.sh"
printf '%s\n' 'NFCOM, DICI e Relatorio de Trafego instalados. Atualize o painel com Ctrl+F5; atalhos em Provedor.'
