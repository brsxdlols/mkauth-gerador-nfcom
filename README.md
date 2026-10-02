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

## Novidades da versão 1.2.0

- Remove todas as mensagens, dados e botões de doação/Pix do addon.
- Adiciona o botão **Como enviar à ANATEL**, com guia passo a passo para envio, processamento, correção de ocorrências e obtenção do comprovante no sistema Coleta de Dados.
- Inclui links oficiais da ANATEL e alerta para sempre conferir agenda, leiaute e material de apoio vigentes.

## Correções da versão 1.2.1

- Impede a geração de DICI com `COD_IBGE` vazio, que aparecia no CSV como dois pontos e vírgulas consecutivos depois do mês.
- Completa o IBGE sem alterar o banco quando Cidade/UF correspondem de forma inequívoca ao cadastro municipal do MK-Auth, inclusive com diferenças de acentuação e espaços.
- Quando o município não pode ser determinado com segurança, bloqueia o arquivo e informa os clientes que precisam de correção cadastral.

## Correções da versão 1.2.2

- Adota a mesma consulta DICI do addon de referência que já funciona no MK-Auth, usando diretamente o `cidade_ibge` cadastrado.
- Remove a tentativa de localizar ou completar códigos IBGE e não altera cadastros nem o banco.
- Mantém a melhoria de saída: CSV com 11 colunas, separador `;`, finais de linha CRLF e UTF-8 com BOM.

## Correções da versão 1.2.3

- Remove a rolagem horizontal indevida da página do addon.
- Mantém a rolagem somente dentro das tabelas quando a tela for estreita.
- Corrige larguras em `vw` que podiam ultrapassar a área visível do navegador.

## Correções da versão 1.2.4

- Elimina a segunda barra vertical causada pela combinação de overflow em `html` e `body`.
- Remove alturas fixas e rolagens internas artificiais das telas DICI e PPP.
- Em telas grandes, ajusta as tabelas à largura disponível; em telas pequenas, preserva a rolagem horizontal responsiva.

## Correções da versão 1.1.1

- Centraliza a validação da sessão autenticada do MK-Auth e elimina chamadas duplicadas a `session_name()` nas páginas do addon.
- Corrige a tela `Acesso negado` em instalações com PHP 8 quando o usuário já está autenticado no painel.

## Validação manual sem emitir NFCOM

Após instalar, autentique-se no painel e abra **Opções → NFCOM / Gerador NF DICI**. Confirme apenas carregamento, navegação e presença do layout; não processe ou emita documento fiscal em produção durante o teste.

## Licença

MIT. Consulte [LICENSE](LICENSE).
