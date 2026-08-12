<?php
require_once __DIR__ . '/addons.class.php';
if (empty($_SESSION['mka_logado']) && empty($_SESSION['MKA_Logado'])) {
    http_response_code(401);
    exit('Sessão expirada. Entre novamente no MK-Auth.');
}
require_once __DIR__ . '/config.hhvm';

if (isset($_GET['form_submitted'])) {
    $cli_isentoP = isset($_GET['ngerarclienteisentoP']) ? "(a.isento = 'sim' OR a.isento = 'nao')" : "a.isento = 'nao'";
    $cli_gerarIsento_aux = isset($_GET['ngerarclienteisentoP']) ? "'F'" : "'T'";
    $cli_mes = isset($_GET['mes']) ? $link->real_escape_string($_GET['mes']) : "";
    $cli_ano = isset($_GET['ano']) ? $link->real_escape_string($_GET['ano']) : "";
    
    $csv_filename = "DICI_MENSAL_$cli_mes_$cli_ano-dbexport" . time() . '.csv';
    $csv_caminho = "/opt/mk-auth/central/disco_virtual/$csv_filename";
    
    $cli_periodoFimBloc = "$cli_ano-$cli_mes-30 00:00:00";
    $cli_bloqueado = isset($_GET['bloqueado']) ? "(a.bloqueado = 'nao' OR a.bloqueado = 'sim')" : "(a.data_bloq IS NULL OR DATE(a.data_bloq) > '$cli_periodoFimBloc')";
    $cli_bloqueado_aux = isset($_GET['bloqueado']) ? "'T'" : "'F'";
    $cli_ativado = isset($_GET['desativado']) ? "(a.cli_ativado = 's' OR a.cli_ativado = 'n')" : "(a.data_desativacao IS NULL OR DATE(a.data_desativacao) > '$cli_periodoFimBloc')";
    $cli_ativado_aux = isset($_GET['desativado']) ? "'F'" : "'T'";
    $cli_gerarSici = isset($_GET['ngerarsici']) ? "(a.gsici = 1 OR a.gsici = 0)" : "a.gsici = 1";
    $cli_gerarSici_aux = isset($_GET['ngerarsici']) ? "'F'" : "'T'";
    $cli_gerarNF = isset($_GET['ngerarnf']) ? "(a.geranfe = 'sim' OR a.geranfe = 'nao')" : "a.geranfe = 'sim'";
    $cli_gerarNF_aux = isset($_GET['ngerarnf']) ? "'F'" : "'T'";

    /*
     * Resolve o IBGE sem alterar o cadastro:
     * 1) código válido do cliente; 2) cidade/UF inequívoca da tabela NAS;
     * 3) cidade/UF com um único código válido entre os demais clientes.
     */
    $normalizeCitySql = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(%s), '  ', ' '), '  ', ' '), '  ', ' '), '  ', ' ')) COLLATE utf8mb4_unicode_ci";
    $clientCityKey = sprintf($normalizeCitySql, 'a.cidade');
    $ibgeSql = "CASE
        WHEN TRIM(COALESCE(a.cidade_ibge, '')) REGEXP '^[0-9]{7}$'
            THEN TRIM(a.cidade_ibge)
        WHEN nas_ibge.COD_IBGE IS NOT NULL THEN nas_ibge.COD_IBGE
        WHEN cli_ibge.COD_IBGE IS NOT NULL THEN cli_ibge.COD_IBGE
        ELSE ''
    END";
    $ibgeJoins = "
        LEFT JOIN (
            SELECT LOWER(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(cidade), '  ', ' '), '  ', ' '), '  ', ' '), '  ', ' ')) COLLATE utf8mb4_unicode_ci AS CIDADE_KEY,
                   UPPER(TRIM(estado)) AS UF_KEY,
                   MIN(TRIM(cidade_ibge)) AS COD_IBGE
            FROM nas
            WHERE TRIM(COALESCE(cidade_ibge, '')) REGEXP '^[0-9]{7}$'
            GROUP BY CIDADE_KEY, UF_KEY
            HAVING COUNT(DISTINCT TRIM(cidade_ibge)) = 1
        ) nas_ibge ON nas_ibge.CIDADE_KEY COLLATE utf8mb4_unicode_ci = $clientCityKey
                  AND nas_ibge.UF_KEY = UPPER(TRIM(a.estado))
        LEFT JOIN (
            SELECT LOWER(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(cidade), '  ', ' '), '  ', ' '), '  ', ' '), '  ', ' ')) COLLATE utf8mb4_unicode_ci AS CIDADE_KEY,
                   UPPER(TRIM(estado)) AS UF_KEY,
                   MIN(TRIM(cidade_ibge)) AS COD_IBGE
            FROM sis_cliente
            WHERE TRIM(COALESCE(cidade_ibge, '')) REGEXP '^[0-9]{7}$'
            GROUP BY CIDADE_KEY, UF_KEY
            HAVING COUNT(DISTINCT TRIM(cidade_ibge)) = 1
        ) cli_ibge ON cli_ibge.CIDADE_KEY COLLATE utf8mb4_unicode_ci = $clientCityKey
                  AND cli_ibge.UF_KEY = UPPER(TRIM(a.estado))";

    $unresolved = $link->query("
        SELECT a.nome, a.cidade, a.estado, a.cidade_ibge
        FROM sis_cliente a
        INNER JOIN sis_plano p ON a.plano = p.nome
        $ibgeJoins
        WHERE $cli_ativado
        AND $cli_bloqueado
        AND $cli_gerarSici
        AND $cli_gerarNF
        AND $cli_isentoP
        AND a.plano IS NOT NULL
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        AND ($ibgeSql) = ''
        ORDER BY a.cidade, a.estado, a.nome");
    if (!$unresolved) {
        http_response_code(500);
        exit('Falha ao validar os códigos IBGE: ' . htmlspecialchars($link->error, ENT_QUOTES, 'UTF-8'));
    }
    if ($unresolved->num_rows > 0) {
        http_response_code(422);
        echo '<strong>CSV não gerado:</strong> existem ' . (int)$unresolved->num_rows
            . ' clientes sem um código IBGE municipal válido e inequívoco.<br>';
        echo 'Corrija Cidade, UF e Código IBGE no cadastro dos clientes abaixo:<ul>';
        $shown = 0;
        while ($bad = $unresolved->fetch_assoc()) {
            if ($shown++ >= 50) break;
            echo '<li>' . htmlspecialchars($bad['nome'], ENT_QUOTES, 'UTF-8') . ' — '
                . htmlspecialchars((string)$bad['cidade'], ENT_QUOTES, 'UTF-8') . '/'
                . htmlspecialchars((string)$bad['estado'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        echo '</ul>';
        if ($unresolved->num_rows > 50) echo '<em>Lista limitada aos primeiros 50 registros.</em>';
        exit;
    }

    $result = $link->query("
        SELECT IF(prov.cnpj IS NULL, '00000000000000', REPLACE(REPLACE(REPLACE(prov.cnpj, '.', ''), '-', ''), '/', '')) AS CNPJ, '$cli_ano' AS ANO, '$cli_mes' AS MES, ($ibgeSql) AS COD_IBGE, IF(a.tipo_pessoa = 3, 'PF', 'PJ') AS TIPO_CLIENTE,
            IF(INSTR(a.tags, 'rural') <> 0, 'RURAL', 'URBANO') AS TIPO_ATENDIMENTO,
            IF(p.tecnologia = 'H', 'fibra', IF(p.tecnologia IN ('k', 'D', 'C'), 'radio', IF(p.tecnologia = 'G', 'satelite', IF(p.tecnologia = 'M', 'cabo_metalico', IF(p.tecnologia = 'J', 'cabo_coaxial', 'cabo_metalico'))))) AS TIPO_MEIO,
            'internet' AS TIPO_PRODUTO, 'ETHERNET' AS TIPO_TECNOLOGIA, FORMAT(p.veldown, 0) AS VELOCIDADE, COUNT(*) AS ACESSOS
        FROM sis_provedor prov, sis_cliente a INNER JOIN sis_plano p ON a.plano = p.nome
        $ibgeJoins
        WHERE $cli_ativado
        AND $cli_bloqueado
        AND $cli_gerarSici
        AND $cli_gerarNF
        AND $cli_isentoP
        AND a.plano IS NOT NULL
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        GROUP BY COD_IBGE, a.tipo_pessoa, TIPO_ATENDIMENTO, TIPO_MEIO, p.veldown");

    if (!$result) {
        http_response_code(500);
        exit('Falha na consulta DICI: ' . htmlspecialchars($link->error, ENT_QUOTES, 'UTF-8'));
    }

    $csvHandle = @fopen($csv_caminho, 'xb');
    if (!$csvHandle) {
        http_response_code(500);
        exit('Não foi possível criar o CSV em disco_virtual. Verifique as permissões do diretório.');
    }
    fwrite($csvHandle, "\xEF\xBB\xBF");
    $writeCsvLine = function ($handle, $fields) {
        $memory = fopen('php://temp', 'r+');
        fputcsv($memory, $fields, ';');
        rewind($memory);
        $line = rtrim(stream_get_contents($memory), "\r\n") . "\r\n";
        fclose($memory);
        fwrite($handle, $line);
    };
    $writeCsvLine($csvHandle, array('CNPJ', 'ANO', 'MES', 'COD_IBGE', 'TIPO_CLIENTE', 'TIPO_ATENDIMENTO', 'TIPO_MEIO', 'TIPO_PRODUTO', 'TIPO_TECNOLOGIA', 'VELOCIDADE', 'ACESSOS'));
    while ($csvRow = $result->fetch_assoc()) {
        $writeCsvLine($csvHandle, array_values($csvRow));
    }
    fclose($csvHandle);
    @chmod($csv_caminho, 0644);
    $result->data_seek(0);

    $resultSucesso = $link->query("
        SELECT a.nome AS NOME
        FROM sis_cliente a
        WHERE $cli_ativado
        AND $cli_bloqueado
        AND $cli_gerarSici
        AND $cli_gerarNF
        AND $cli_isentoP
        AND a.cidade_ibge IS NOT NULL
        AND a.plano IS NOT NULL
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        ORDER BY a.nome");

    $resultFalha = $link->query("
        SELECT a.nome AS NOME, 'Ciente Nao possui o código IBGE da Cidade no cadastro' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.cidade_ibge IS NULL
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente Nao possui plano definido' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.plano IS NULL
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente Desativado' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.data_desativacao IS NOT NULL 
        AND DATE(a.data_desativacao) < '$cli_periodoFimBloc'
        AND $cli_ativado_aux = 'T'
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente Bloqueado' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.data_bloq IS NOT NULL 
        AND DATE(a.data_bloq) < '$cli_periodoFimBloc' 
        AND $cli_bloqueado_aux = 'F'
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente sem a marcação no cadastrado para GERAR SICI' AS SITUACAO 
        FROM sis_cliente a 
        WHERE a.gsici = 0 
        AND $cli_gerarSici_aux = 'T'
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente sem a marcação no cadastrado para GERAR NFE' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.geranfe = 'nao' 
        AND $cli_gerarNF_aux = 'T'
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')
        UNION
        SELECT a.nome AS NOME, 'Cliente na Situacao de isento' AS SITUACAO 
        FROM sis_cliente a
        WHERE a.isento = 'sim' 
        AND $cli_gerarIsento_aux = 'T'
        AND STR_TO_DATE(a.cadastro, '%d/%m/%Y') <= STR_TO_DATE('30/$cli_mes/$cli_ano', '%d/%m/%Y')");
?>

Relatorio Gerado - Mes de Referencia= <b><?php echo $cli_mes; ?></b> e Ano= <b><?php echo $cli_ano; ?></b>
<div style="display: grid;">
    <table>
        <thead>
            <tr class="tab_th">
                <th>CNPJ</th>
                <th>ANO</th>
                <th>MES</th>
                <th>COD_IBGE</th>
                <th>TIPO_CLIENTE</th>
                <th>TIPO_ATENDIMENTO</th>
                <th>TIPO_MEIO</th>
                <th>TIPO_PRODUTO</th>
                <th>TIPO_TECNOLOGIA</th>
                <th>VELOCIDADE</th>
                <th>ACESSOS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['CNPJ']; ?></td>
                    <td><?php echo $row['ANO']; ?></td>
                    <td><?php echo $row['MES']; ?></td>
                    <td><?php echo $row['COD_IBGE']; ?></td>
                    <td><?php echo $row['TIPO_CLIENTE']; ?></td>
                    <td><?php echo $row['TIPO_ATENDIMENTO']; ?></td>
                    <td><?php echo $row['TIPO_MEIO']; ?></td>
                    <td><?php echo $row['TIPO_PRODUTO']; ?></td>
                    <td><?php echo $row['TIPO_TECNOLOGIA']; ?></td>
                    <td><?php echo $row['VELOCIDADE']; ?></td>
                    <td><?php echo $row['ACESSOS']; ?></td>   
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="text-center my-5">
    <a href="<?php echo '/central/disco_virtual/' . $csv_filename; ?>" 
       class="btn btn-primary btn-lg px-4 py-2 shadow rounded-pill" 
       title="Download do Arquivo" 
       download>
        <i class="bi bi-cloud-arrow-down-fill me-2"></i> 
        DOWNLOAD DO ARQUIVO
    </a>
</div>
<br/>
<b>Clientes Adicionados no Relatorio</b>
<div style="display: grid; overflow:scroll; height:150px;">
    <table>
        <thead>
            <tr class="tab_th">
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultSucesso->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['NOME']; ?></td>     
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<br/>
<b>Clientes Nao adicionados no Relatorio</b>
<div style="display: grid; overflow:scroll; height:150px;">
    <table>
        <thead>
            <tr class="tab_th">
                <th>Nome</th>
                <th>Situacao</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultFalha->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['NOME']; ?></td>    
                    <td><?php echo $row['SITUACAO']; ?></td>                     
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<br/>
<?php } ?>
