<?php
require_once __DIR__ . '/config.hhvm';

header('Content-Type: application/json');

if (!isset($_GET['form_pp2'])) {
    echo json_encode(['success' => false, 'message' => 'Formulário não enviado.']);
    exit;
}

$cli_ano = isset($_GET['anoRef']) ? $link->real_escape_string($_GET['anoRef']) : "";
$cli_periodoIni = "";
$cli_periodoFim = "";
$mes_ref_str = "";
$cli_ano_aux4 = $cli_ano;

if (isset($_GET['mesRef'])) {
    $mes_ref = $link->real_escape_string($_GET['mesRef']);
    $mes_aux = ($mes_ref == '10') ? '01' : $mes_ref + 3;
    if ($mes_ref == '10') {
        $cli_ano_aux4++;
    }
    $cli_periodoIni = "$cli_ano-$mes_ref-01 00:00:00";
    $cli_periodoFim = "$cli_ano_aux4-$mes_aux-01 00:00:00";

    switch ($mes_ref) {
        case '01':
            $nomeArquivoPersonalizado = "PPP_JAN_FEV_MAR$cli_ano_";
            $mes_ref_str = '( JAN / FEV / MAR)';
            break;
        case '04':
            $nomeArquivoPersonalizado = "PPP_ABR_MAI_JUN_$cli_ano_";
            $mes_ref_str = '( ABR / MAI / JUN)';
            break;
        case '07':
            $nomeArquivoPersonalizado = "PPP_JUL_AGO_SET_$cli_ano_";
            $mes_ref_str = '( JUL / AGO / SET)';
            break;
        case '10':
            $nomeArquivoPersonalizado = "PPP_OUT_NOV_DEZ_$cli_ano_";
            $mes_ref_str = '( OUT / NOV / DEZ)';
            break;
    }
}

$csv_filenamePPP = $nomeArquivoPersonalizado . time() . '.csv';
$csv_caminhoPPP = "/opt/mk-auth/central/disco_virtual/$csv_filenamePPP";

$valorInvestidoNoPeriodo = isset($_GET['valorinvestidopel']) && $_GET['valorinvestidopel'] != '' ? floatval($_GET['valorinvestidopel']) : 0;
$cli_desp_nao_listadas = isset($_GET['valordespnaolistada']) && $_GET['valordespnaolistada'] != '' ? floatval($_GET['valordespnaolistada']) : 0;
$cli_valor_pg_boleto = isset($_GET['valorpgboleto']) && $_GET['valorpgboleto'] != '' ? floatval($_GET['valorpgboleto']) : 0;

$cli_boletoDespesa = isset($_GET['ngboletodesp']);
$cli_LancManual = isset($_GET['ngreflancmanual']);
$cli_gerarSici = isset($_GET['ngerarsiciRef']) ? "(a.gsici = 1 OR a.gsici = 0)" : "a.gsici = 1";
$cli_gerarNF = isset($_GET['ngerarnfRef']) ? "(a.geranfe = 'sim' OR a.geranfe = 'nao')" : "a.geranfe = 'sim'";
$cli_isento = isset($_GET['ngerarclienteisento']) ? "(a.isento = 'sim' OR a.isento = 'nao')" : "a.isento = 'nao'";

$getCnpjEmp = $link->query("SELECT REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '-', ''), '/', '') AS cnpj FROM sis_provedor");
$resultadoCNPJProv = $getCnpjEmp->num_rows > 0 ? $getCnpjEmp->fetch_assoc()['cnpj'] : '00000000000000';

$resultadoMb = $link->query("
    SELECT TRUNCATE(SUM((tabRad.acctinputoctets + tabRad.acctoutputoctets) * 8 / (8*1024*1024)), 0) AS TOTAL
    FROM radacct tabRad
    INNER JOIN sis_cliente a ON tabRad.username = a.login
    WHERE tabRad.acctstarttime IS NOT NULL 
    AND tabRad.acctstoptime IS NOT NULL
    AND DATE(tabRad.acctstarttime) >= '$cli_periodoIni'
    AND DATE(tabRad.acctstarttime) < '$cli_periodoFim'
    AND DATE(tabRad.acctstoptime) < '$cli_periodoFim'
    AND $cli_gerarSici
    AND $cli_gerarNF
    AND $cli_isento
    AND a.cidade_ibge IS NOT NULL 
    AND a.plano IS NOT NULL");
$resultadoTotalMBTrafego = $resultadoMb->num_rows > 0 ? $resultadoMb->fetch_assoc()['TOTAL'] : 0;

$resultadoReceitaBrutaCliente = $link->query("
    SELECT SUM(lancamento.valor) AS TOTAL_BRUTO
    FROM sis_cliente a 
    INNER JOIN sis_lanc lancamento ON a.login = lancamento.login
    WHERE lancamento.datapag IS NOT NULL 
    AND DATE(lancamento.datapag) >= '$cli_periodoIni'
    AND DATE(lancamento.datapag) < '$cli_periodoFim'
    AND $cli_gerarSici
    AND $cli_gerarNF
    AND $cli_isento
    AND a.cidade_ibge IS NOT NULL 
    AND a.plano IS NOT NULL");
$resultadoReceitaBrutaClienteAux = $resultadoReceitaBrutaCliente->num_rows > 0 ? $resultadoReceitaBrutaCliente->fetch_assoc()['TOTAL_BRUTO'] : 0;

$resultadoReceitaBrutaManual = $link->query("
    SELECT SUM(lancamento.entrada) AS TOTAL_BRUTO_MANUAL
    FROM sis_caixa lancamento 
    WHERE lancamento.tipomov = 'man' 
    AND lancamento.data IS NOT NULL 
    AND DATE(lancamento.data) >= '$cli_periodoIni'
    AND DATE(lancamento.data) < '$cli_periodoFim'");
$resultadoReceitaBrutaManualAux = $resultadoReceitaBrutaManual->num_rows > 0 ? $resultadoReceitaBrutaManual->fetch_assoc()['TOTAL_BRUTO_MANUAL'] : 0;

$resultadoDescontoBoleto = $link->query("
    SELECT COUNT(*) AS TOTAL_BOLETO
    FROM sis_cliente a INNER JOIN sis_lanc lancamento ON a.login = lancamento.login 
    WHERE lancamento.datapag IS NOT NULL 
    AND DATE(lancamento.datapag) >= '$cli_periodoIni'
    AND DATE(lancamento.datapag) < '$cli_periodoFim'
    AND $cli_gerarSici
    AND $cli_gerarNF
    AND $cli_isento
    AND a.cidade_ibge IS NOT NULL 
    AND a.plano IS NOT NULL");
$resultadoDescontoBoletoAux = $resultadoDescontoBoleto->num_rows > 0 ? $resultadoDescontoBoleto->fetch_assoc()['TOTAL_BOLETO'] * $cli_valor_pg_boleto : 0;

$resultadoReceitaDespesa = $link->query("
    SELECT SUM(lancamento.saida) AS RESULT_DESP
    FROM sis_caixa lancamento 
    WHERE lancamento.data IS NOT NULL 
    AND DATE(lancamento.data) >= '$cli_periodoIni'
    AND DATE(lancamento.data) < '$cli_periodoFim' 
    AND lancamento.saida IS NOT NULL
    AND lancamento.saida <> 0
    AND lancamento.tipomov = 'man'");
$resultadoReceitaAux = $resultadoReceitaDespesa->num_rows > 0 ? $resultadoReceitaDespesa->fetch_assoc()['RESULT_DESP'] : 0;

if ($cli_LancManual) {
    $resultadoReceitaBrutaClienteAux += $resultadoReceitaBrutaManualAux;
}
$resultadoReceitaLiquidaGeral = $resultadoReceitaBrutaClienteAux - $resultadoReceitaAux;
if ($cli_boletoDespesa) {
    $resultadoReceitaLiquidaGeral -= $resultadoDescontoBoletoAux;
}
$resultadoReceitaLiquidaGeral -= $cli_desp_nao_listadas;
$totalDespesas = $cli_desp_nao_listadas + $resultadoDescontoBoletoAux + $resultadoReceitaAux;

$varAux = ($resultadoReceitaLiquidaGeral != null && $resultadoReceitaLiquidaGeral != 0) ? strval($resultadoReceitaLiquidaGeral) : 0;
$varInvestidoAux = ($valorInvestidoNoPeriodo != null && $valorInvestidoNoPeriodo != 0) ? strval($valorInvestidoNoPeriodo) : 0;

$stmtResult = $link->query("
    SELECT 'DADO_INFORMADO', 'SERVICO', 'UNIDADE_DA_FEDERACAO_UF', 'VALORES', 'CNPJ'
    UNION
    SELECT 'Receita_Operacional_Líquida_ROL' AS 'DADO_INFORMADO', 'SCM' AS 'SERVICO', prov.estado AS 'UNIDADE_DA_FEDERACAO_UF', REPLACE('$varAux', '.', ',') AS 'VALORES', CONCAT('\"', IF(prov.cnpj IS NULL, '00000000000000', REPLACE(REPLACE(REPLACE(prov.cnpj, '.', ''), '-', ''), '/', '')), '\"') AS 'CNPJ'
    FROM sis_provedor prov
    UNION
    SELECT 'Capital_Expenditure_CAPEX' AS 'DADO_INFORMADO', 'SCM' AS 'SERVICO', prov.estado AS 'UNIDADE_DA_FEDERACAO_UF', REPLACE('$varInvestidoAux', '.', ',') AS 'VALORES', CONCAT('\"', IF(prov.cnpj IS NULL, '00000000000000', REPLACE(REPLACE(REPLACE(prov.cnpj, '.', ''), '-', ''), '/', '')), '\"') AS 'CNPJ'
    FROM sis_provedor prov
    UNION
    SELECT 'Tráfego_SCM_Total_MB' AS 'DADO_INFORMADO', 'SCM' AS 'SERVICO', prov.estado AS 'UNIDADE_DA_FEDERACAO_UF', '$resultadoTotalMBTrafego' AS 'VALORES', CONCAT('\"', IF(prov.cnpj IS NULL, '00000000000000', REPLACE(REPLACE(REPLACE(prov.cnpj, '.', ''), '-', ''), '/', '')), '\"') AS 'CNPJ'
    FROM sis_provedor prov
    INTO OUTFILE '$csv_caminhoPPP'
    CHARACTER SET latin1
    FIELDS TERMINATED BY ';'
    LINES TERMINATED BY '\r\n'");


ob_start();
?>
<b>Resultado do relatório PPP para o período informado</b>
<div style="display: grid; height:80;">
    <table>
        <thead>
            <tr class="tab_th">
                <th>Trimestre</th>
                <th>Receita Operacional Bruta</th>
                <th>Despesas</th>
                <th>Receita Operacional Líquida</th>
                <th>Investimento no período</th>
                <th>Tráfego total</th>
                <th>CNPJ</th>                            
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $mes_ref_str; ?></td>   
                <td><?php echo '$' . number_format($resultadoReceitaBrutaClienteAux, 2, ',', '.'); ?></td>   
                <td><?php echo '$' . number_format($totalDespesas, 2, ',', '.'); ?></td>   
                <td><?php echo '$' . number_format($resultadoReceitaLiquidaGeral, 2, ',', '.'); ?></td>   
                <td><?php echo '$' . number_format($valorInvestidoNoPeriodo, 2, ',', '.'); ?></td>  
                <td><?php echo $resultadoTotalMBTrafego; ?>MB</td> 
                <td><?php echo $resultadoCNPJProv; ?></td>  
            </tr>
        </tbody>
    </table>
</div>
<div class="text-center my-5">
    <a href="<?php echo '/central/disco_virtual/' . $csv_filenamePPP; ?>" 
       class="btn btn-primary btn-lg px-5 py-3 shadow rounded-pill" 
       title="Download do Arquivo" 
       download>
        <i class="bi bi-cloud-arrow-down-fill me-2"></i>
        DOWNLOAD DO ARQUIVO GERADO
    </a>
</div>
<?php
$html_resultado = ob_get_clean();


echo json_encode([
    'success' => true,
    'html' => $html_resultado,
    'message' => 'Relatório gerado com sucesso.'
]);
exit;
?>
