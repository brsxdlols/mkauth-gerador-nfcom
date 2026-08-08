# MK-Auth — Gerador NFCOM / NF DICI

Distribuição independente do addon Gerador de NFcom + DICI para MK-Auth, incluindo o layout `modelo01` de impressão NFCOM.

## Instalação ou atualização

Execute como `root` no servidor MK-Auth:

```sh
curl -fsSL https://raw.githubusercontent.com/brsxdlols/mkauth-gerador-nfcom/main/install.sh | sh
```

O comando sempre baixa a versão atual da branch `main`. O instalador valida o pacote antes de alterar o MK-Auth, cria backup com data/hora e pode ser executado novamente sem duplicar o menu.

## Destinos

- Addon: `/opt/mk-auth/admin/addons/gerador_nf_dici`
- Layout: `/opt/mk-auth/print_pdf/nfcom/modelo01`
- Menu: `/opt/mk-auth/admin/addons/addon.js`
- Backups: `/opt/mk-auth/backups/gerador_nf_dici/AAAAmmdd-HHMMSS`

O addon reutiliza `/opt/mk-auth/include/conexao.php`; nenhuma senha de banco é distribuída.

## Rollback

Baixe o instalador e informe um backup, ou use `latest`:

```sh
curl -fsSL https://raw.githubusercontent.com/brsxdlols/mkauth-gerador-nfcom/main/install.sh -o /tmp/install-gerador-nfcom.sh
sh /tmp/install-gerador-nfcom.sh --rollback latest
```

## Compatibilidade e segurança

- Requer MK-Auth em `/opt/mk-auth`, PHP CLI, `tar` e `curl` ou `wget`.
- Compatível com nomes de sessão e páginas `.hhvm` usados por instalações antigas.
- O instalador não altera banco, clientes, certificados nem documentos fiscais.
- Antes da troca, addon, layout e `addon.js` existentes são copiados integralmente para backup.
- Arquivos locais, logs, certificados, chaves, configurações particulares e `desktop.ini` não fazem parte da distribuição.

## Correções da versão 1.1.0

- Detecta automaticamente sessões `mka`, `MKA` e a sessão PHP ativa do painel.
- Mantém o resultado da geração DICI na tela e mostra erros retornados pelo servidor.
- Gera o CSV DICI diretamente em UTF-8 com BOM e finais de linha CRLF, conforme exigência exibida pelo portal da ANATEL.
- Não depende mais do privilégio MySQL `INTO OUTFILE` para gerar o arquivo DICI.

## Validação manual sem emitir NFCOM

Após instalar, autentique-se no painel e abra **Opções → NFCOM / Gerador NF DICI**. Confirme apenas carregamento, navegação e presença do layout; não processe ou emita documento fiscal em produção durante o teste.

## Licença

MIT. Consulte [LICENSE](LICENSE).
