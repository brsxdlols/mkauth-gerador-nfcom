<?php
include('mkauth_session.php');
mkauth_addon_require_login();

$manifestTitle = isset($Manifest->{'name'}) ? $Manifest->{'name'} : '';
$manifestVersion = isset($Manifest->{'version'}) ? $Manifest->{'version'} : '';
?>

<!DOCTYPE html>
<?php
if (isset($_SESSION['MM_Usuario'])) {
    echo '<html lang="pt-BR">';
} else {
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK - AUTH :: <?= htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?></title>

    <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />    
    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>
</head>

<body>
    <?php include('../../topo.php'); ?>
    <?php include('config.hhvm'); ?>
    <?php
      include('nav/navbar.php');
    $busca        = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $conta_filtro = isset($_GET['conta']) ? trim($_GET['conta']) : '';
    $ordem        = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome';


    $query_contas = "SELECT id, nome FROM sis_boleto ORDER BY nome ASC";
    $result_contas = mysqli_query($link, $query_contas);
    $itens_por_pagina = 100;
    $pagina_atual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
    $offset = ($pagina_atual - 1) * $itens_por_pagina;

    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome';


$where_conditions = ["c.cli_ativado = 's'"];

if (!empty($busca)) {
    $busca_esc = mysqli_real_escape_string($link, $busca);
    $where_conditions[] = "(c.nome LIKE '%$busca_esc%' OR c.login LIKE '%$busca_esc%')";
}
if (!empty($conta_filtro)) {
    $conta_esc = mysqli_real_escape_string($link, $conta_filtro);
    $where_conditions[] = "c.conta = '$conta_esc'";
}
$where_clause = implode(" AND ", $where_conditions);


$query_total = "SELECT COUNT(*) as total FROM sis_cliente c WHERE $where_clause";
$result_total = mysqli_query($link, $query_total);
$total_registros = mysqli_fetch_assoc($result_total)['total'] ?? 0;
$total_paginas = max(1, ceil($total_registros / $itens_por_pagina));


$offset = ($pagina_atual - 1) * $itens_por_pagina;
$query = "SELECT 
            c.nome, c.login, c.uuid_cliente, c.geranfe, c.local_dici, c.gsici,
            c.conta AS conta_id,
            COALESCE(b.nome, 'Sem conta vinculada') AS nome_conta
          FROM sis_cliente c
          LEFT JOIN sis_boleto b ON c.conta = b.id
          WHERE $where_clause
          ORDER BY $ordem 
          LIMIT $itens_por_pagina OFFSET $offset";
$consulta_clientes = mysqli_query($link, $query);


$query_stats = "SELECT 
    SUM(CASE WHEN geranfe = 'sim' THEN 1 ELSE 0 END) as nfcom_ativo,
    SUM(CASE WHEN gsici = '1' THEN 1 ELSE 0 END) as dici_ativo
    FROM sis_cliente c WHERE $where_clause";
$result_stats = mysqli_query($link, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);
$nfcom_ativo = (int)($stats['nfcom_ativo'] ?? 0);
$dici_ativo  = (int)($stats['dici_ativo'] ?? 0);
$p_nfcom = $total_registros > 0 ? round(($nfcom_ativo / $total_registros) * 100, 1) : 0;
$p_dici  = $total_registros > 0 ? round(($dici_ativo / $total_registros) * 100, 1) : 0;


$result_stats = mysqli_query($link, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

$nfcom_ativo = (int)($stats['nfcom_ativo'] ?? 0);
$dici_ativo  = (int)($stats['dici_ativo'] ?? 0);

$p_nfcom = $total_registros > 0 ? round(($nfcom_ativo / $total_registros) * 100, 1) : 0;
$p_dici  = $total_registros > 0 ? round(($dici_ativo / $total_registros) * 100, 1) : 0;
?>


   <div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-people"></i> Clientes Ativos - Configurações NF/DICI</h5>
    </div>
    <div class="card-body">

 
  <form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-4">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou login" value="<?= htmlspecialchars($busca) ?>">
    </div>

    <div class="col-md-4">
        <select name="conta" class="form-select">
            <option value="">Todas as contas</option>
            <?php 
            mysqli_data_seek($result_contas, 0); 
            while ($row = mysqli_fetch_assoc($result_contas)): 
            ?>
                <option value="<?= $row['id'] ?>" <?= ($conta_filtro == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nome']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-2">
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
    </div>

    <div class="col-md-2">
        <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-secondary w-100">Limpar</a>
    </div>
</form>


<style>
    .mini-box {
        font-size: 15px;
        font-weight: normal;
        display: flex;
        align-items: center;
        gap: 12px; /* separação clara */
        color: #0d6efd;
        padding: 4px 0;
    }

    /* Linha vertical */
    .mini-divider {
        width: 1px;
        height: 24px;
        background: #bcd3ff; /* azul bem suave */
        margin: 0 26px;
    }

    /* Cor do "Total" */
    .total-label {
        color: #0a58ca; /* azul mais forte */
    }

    /* Cor da porcentagem */
    .percent {
        padding: 2px 8px;
        background: #e8f0ff;  
        border-radius: 6px;
        color: #1a1b1dff;    
        font-size: 14px;
    }
</style>

<div class="d-flex justify-content-center align-items-center mb-3">

    <div class="mini-box">
        <span class="total-label">Total NFcom ativada <?= number_format($nfcom_ativo) ?></span>
        <span class="percent"><?= $p_nfcom ?>%</span>
    </div>

    <div class="mini-divider"></div>

    <div class="mini-box">
        <span class="total-label">Total DICI ativado <?= number_format($dici_ativo) ?></span>
        <span class="percent"><?= $p_dici ?>%</span>
    </div>

</div>





<div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead class="table-primary text-white">
    <tr>
        <th>Nome / Login</th>
         <th>Conta</th>
        <th class="text-center">Ativar AMBOS</th>
        <th class="text-center">Ativar NFcom</th>
        <th class="text-center">Ativar DICI</th>
        <th class="text-center">Local (Urbano/Rural)</th>
    </tr>
</thead>
<tbody>
    <?php while ($res = mysqli_fetch_array($consulta_clientes)): 
        $nome       = $res['nome'];
        $login      = $res['login'];
        $uuid       = $res['uuid_cliente'];
        $geranfe    = $res['geranfe'] ?? 'nao';
        $gsici      = $res['gsici'] ?? '0';
        $local_dici = $res['local_dici'] ?? '';
        $ambos      = ($geranfe === 'sim' && $gsici === '1');
        $hash       = md5($nome);
    ?>
    <tr>
<td>
    <a href="../../cliente_alt.hhvm?uuid=<?= urlencode($uuid) ?>" target="_blank" class="text-decoration-none fw-bold text-primary">
        <?= htmlspecialchars($nome) ?>
    </a><br>
    <small class="text-muted">@<?= htmlspecialchars($login) ?></small>
</td>

<td>
    <?php if (!empty($res['conta_id'])): ?>
        <span style="color:#d63384; font-weight:bold;">
            <?= htmlspecialchars($res['nome_conta']) ?>
        </span>
    <?php else: ?>
        <span style="color:#d63384;">
            <?= htmlspecialchars($res['nome_conta']) ?>
        </span>
    <?php endif; ?>
</td>


      
        <td class="text-center align-middle">
            <div class="msg-feedback small mb-1" id="msg-ambos-<?= $hash ?>"></div>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="ambos_<?= $hash ?>" id="ambos_sim_<?= $hash ?>" autocomplete="off"
                       <?= $ambos ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'nfcom_dici', 'sim', 'ambos', this)">
                <label class="btn btn-outline-success" for="ambos_sim_<?= $hash ?>">Sim</label>

                <input type="radio" class="btn-check" name="ambos_<?= $hash ?>" id="ambos_nao_<?= $hash ?>" autocomplete="off"
                       <?= !$ambos ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'nfcom_dici', 'nao', 'ambos', this)">
                <label class="btn btn-outline-danger" for="ambos_nao_<?= $hash ?>">Não</label>
            </div>
        </td>

   
        <td class="text-center align-middle">
            <div class="msg-feedback small mb-1" id="msg-nf-<?= $hash ?>"></div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nf_<?= $hash ?>" value="sim"
                       <?= $geranfe === 'sim' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'geranfe', 'sim', 'nf', this)">
                <label class="form-check-label text-success fw-bold">Sim</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nf_<?= $hash ?>" value="nao"
                       <?= $geranfe === 'nao' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'geranfe', 'nao', 'nf', this)">
                <label class="form-check-label text-danger fw-bold">Não</label>
            </div>
        </td>

  
        <td class="text-center align-middle">
            <div class="msg-feedback small mb-1" id="msg-dici-<?= $hash ?>"></div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="dici_<?= $hash ?>" value="1"
                       <?= $gsici === '1' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'gsici', '1', 'dici', this)">
                <label class="form-check-label text-success fw-bold">Sim</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="dici_<?= $hash ?>" value="0"
                       <?= $gsici === '0' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'gsici', '0', 'dici', this)">
                <label class="form-check-label text-danger fw-bold">Não</label>
            </div>
        </td>

        <td class="text-center align-middle">
            <div class="msg-feedback small mb-1" id="msg-local-<?= $hash ?>"></div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="local_<?= $hash ?>" value="u"
                       <?= $local_dici === 'u' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'local_dici', 'u', 'local', this)">
                <label class="form-check-label">Urbano</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="local_<?= $hash ?>" value="r"
                       <?= $local_dici === 'r' ? 'checked' : '' ?>
                       onchange="atualizarCliente('<?= addslashes($nome) ?>', 'local_dici', 'r', 'local', this)">
                <label class="form-check-label">Rural</label>
            </div>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
    </table>
</div>

            <!-- Paginação -->
            <nav aria-label="Paginação" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagina_atual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>&ordem=<?= urlencode($ordem) ?>">Anterior</a>
                    </li>

                    <?php
                    $range = 2;
                    $start = max(1, $pagina_atual - $range);
                    $end = min($total_paginas, $pagina_atual + $range);

                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?pagina=1&busca=' . urlencode($busca) . '&ordem=' . urlencode($ordem) . '">1</a></li>';
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        $active = $i == $pagina_atual ? 'active' : '';
                        echo "<li class='page-item $active'><a class='page-link' href='?pagina=$i&busca=" . urlencode($busca) . "&ordem=" . urlencode($ordem) . "'>$i</a></li>";
                    }

                    if ($end < $total_paginas) {
                        if ($end < $total_paginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?pagina=' . $total_paginas . '&busca=' . urlencode($busca) . '&ordem=' . urlencode($ordem) . '">' . $total_paginas . '</a></li>';
                    }
                    ?>

                    <li class="page-item <?= $pagina_atual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>&busca=<?= urlencode($busca) ?>&ordem=<?= urlencode($ordem) ?>">Próximo</a>
                    </li>
                </ul>
            </nav>

            <div class="text-center text-muted small mt-3">
                Total: <strong><?= number_format($total_registros) ?></strong> clientes
            </div>
        </div>
    </div>
</div>

<?php mysqli_close($link); ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../scripts/jquery.js"></script>
<script src="../../scripts/mk-auth.js"></script>

<script>
function atualizarCliente(nome, campo, valor, tipo, element) { 
    element.disabled = true;

    const formData = new FormData();
    formData.append("nome", nome);
    formData.append("campo", campo);
    formData.append("valor", valor);

    const msgId = 'msg-' + tipo + '-' + md5(nome);
    const msgDiv = document.getElementById(msgId);

    fetch("atualizar_cliente.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (msgDiv) {
            msgDiv.innerHTML = '<i class="fas fa-check"></i> ' + data.trim();
            msgDiv.classList.add('text-success');
            setTimeout(() => {
                msgDiv.innerHTML = '';
                msgDiv.classList.remove('text-success');
                element.disabled = false;
            }, 2000);
        }
    })
    .catch(error => {
        console.error("Erro:", error);
        if (msgDiv) {
            msgDiv.innerHTML = '<i class="fas fa-times"></i> Erro';
            msgDiv.classList.add('text-danger');
            setTimeout(() => {
                msgDiv.innerHTML = '';
                msgDiv.classList.remove('text-danger');
                element.disabled = false;
            }, 3000);
        }
    });
}

function md5(str) {
    return [...str].reduce((a,b) => (a = ((a << 5) - a) + b.charCodeAt(0))|0, 0).toString(16);
}
</script>

<?php include('../../baixo.php'); ?>
<script src="../../menu.js.hhvm"></script>

</body>
</html>
