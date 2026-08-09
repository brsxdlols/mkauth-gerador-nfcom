<style>
#guia-anatel-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 10000;
    background: rgba(0, 0, 0, .58);
    padding: 24px;
    overflow-y: auto;
}
#guia-anatel-modal.aberto { display: block; }
.guia-anatel-conteudo {
    max-width: 900px;
    margin: 20px auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, .28);
    color: #212529;
}
.guia-anatel-cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #dee2e6;
}
.guia-anatel-corpo { padding: 20px; line-height: 1.5; }
.guia-anatel-corpo h4 { margin-top: 20px; color: #0d6efd; }
.guia-anatel-corpo li { margin-bottom: 8px; }
.guia-anatel-alerta {
    padding: 12px;
    border-left: 4px solid #ffc107;
    background: #fff8df;
    margin: 14px 0;
}
.guia-anatel-links a { display: inline-block; margin: 4px 10px 4px 0; }
.guia-anatel-fechar {
    border: 0;
    background: transparent;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
}
</style>

<div id="guia-anatel-modal" role="dialog" aria-modal="true" aria-labelledby="guia-anatel-titulo">
    <div class="guia-anatel-conteudo">
        <div class="guia-anatel-cabecalho">
            <h3 id="guia-anatel-titulo" style="margin:0;">Como enviar os relatórios pós-outorga à ANATEL</h3>
            <button type="button" class="guia-anatel-fechar" onclick="fecharGuiaAnatel()" aria-label="Fechar">&times;</button>
        </div>
        <div class="guia-anatel-corpo">
            <p>Este addon prepara arquivos de apoio para as coletas de <strong>Acessos do SCM</strong> e de informações de <strong>PPP</strong>. O envio oficial é feito no sistema Coleta de Dados da ANATEL.</p>

            <h4>Antes de começar</h4>
            <ol>
                <li>Tenha acesso à empresa no portal da ANATEL com representante ou procuração eletrônica válida.</li>
                <li>Gere o CSV na aba correspondente deste addon e confira o mês, trimestre ou ano de referência.</li>
                <li>Confira se o arquivo está em <strong>UTF-8 com BOM</strong> e com linhas <strong>CRLF</strong>. Os arquivos gerados por este addon já usam esse formato.</li>
            </ol>

            <h4>Envio no sistema Coleta de Dados</h4>
            <ol>
                <li>Acesse <a href="https://apps.anatel.gov.br/ColetaDados/" target="_blank" rel="noopener noreferrer">apps.anatel.gov.br/ColetaDados</a> e faça o login.</li>
                <li>Abra o menu <strong>Envio de Arquivos</strong> e selecione a empresa/entidade correta.</li>
                <li>Escolha a coleta correspondente ao arquivo: <strong>Acessos – SCM</strong> para o CSV mensal ou a coleta econômico-financeira/técnico-operacional de <strong>PPP</strong> exibida na agenda.</li>
                <li>Na <strong>Agenda de Envio</strong>, localize a referência correta e clique em <strong>Enviar arquivo</strong>. Para período antigo ou reenvio, use a agenda excepcional disponibilizada pela ANATEL.</li>
                <li>Antes do upload, abra <strong>Ver Leiautes</strong> e <strong>Material de Apoio</strong>. Compare cabeçalho, campos, tipos e regras com o CSV gerado.</li>
                <li>Volte à referência, clique em <strong>Adicionar Arquivo</strong>, selecione o CSV e confirme o carregamento.</li>
            </ol>

            <h4>Depois do envio</h4>
            <ol>
                <li>Aguarde o processamento assíncrono; o simples upload não conclui a obrigação.</li>
                <li>Abra <strong>Histórico de Processamento</strong>. Se houver falha, consulte <strong>Ver Ocorrências</strong>, corrija o arquivo e reenvie.</li>
                <li>Confirme o estado <strong>Processado com sucesso</strong>.</li>
                <li>Clique em <strong>Ver Comprovante</strong> e salve o comprovante de recebimento com os documentos da competência.</li>
            </ol>

            <div class="guia-anatel-alerta">
                <strong>Atenção:</strong> nomes de coleta, leiautes, periodicidades e prazos podem ser alterados pela ANATEL. Sempre prevalecem a agenda, o leiaute e o material de apoio mostrados no sistema no dia do envio.
            </div>

            <div class="guia-anatel-links">
                <strong>Links oficiais:</strong><br>
                <a href="https://apps.anatel.gov.br/ColetaDados/" target="_blank" rel="noopener noreferrer">Sistema Coleta de Dados</a>
                <a href="https://www.gov.br/anatel/pt-br/dados/coleta-de-dados-setoriais/manual-do-sistema-coleta-de-dados-anatel" target="_blank" rel="noopener noreferrer">Manual do sistema</a>
                <a href="https://www.gov.br/anatel/pt-br/regulado/universalizacao/coletas-de-dados-de-acessos" target="_blank" rel="noopener noreferrer">Coletas de acessos</a>
                <a href="https://www.gov.br/anatel/pt-br/regulado/prestadoras-de-pequeno-porte/guia-das-ppps" target="_blank" rel="noopener noreferrer">Guia das PPPs</a>
            </div>
        </div>
    </div>
</div>

<script>
function abrirGuiaAnatel() {
    document.getElementById('guia-anatel-modal').classList.add('aberto');
    document.body.style.overflow = 'hidden';
}
function fecharGuiaAnatel() {
    document.getElementById('guia-anatel-modal').classList.remove('aberto');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') fecharGuiaAnatel();
});
document.addEventListener('click', function (event) {
    if (event.target && event.target.id === 'guia-anatel-modal') fecharGuiaAnatel();
});
</script>
