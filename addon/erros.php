<?php
include('mkauth_session.php');
mkauth_addon_require_login();
?>
<!DOCTYPE html>
<?php
if(isset($_SESSION['MM_Usuario'])){
    echo '<html lang="pt-BR">';
}else{
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <title>MK - AUTH :: <?php echo $Manifest->{'name'}." - V ". $Manifest->{'version'};  ?></title>
        <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />
        <script src="../../scripts/jquery.js"></script>
        <script src="../../scripts/mk-auth.js"></script>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="css/css.css" rel="stylesheet" type="text/css" />
    <style>
        .comp-box { 
            padding:12px; 
            background:#ffebee; 
            border-radius:8px; 
            word-break:break-all; 
            font-size:14px;
            line-height:1.4;
        }
    </style>
</head>
<body>
  <?php include('config.hhvm'); ?>
    <?php include('../../topo.hhvm'); ?> 
    <?php include('nav/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <div class="card border-danger shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 d-flex align-items-center justify-content-between">
                    <div>
                        Clientes com complemento acima de 60 caracteres
                    </div>
                    <span class="badge bg-light text-danger fs-5">
                        <?php
                        $qtd = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS t FROM sis_cliente 
                            WHERE cli_ativado='s' 
                            AND LENGTH(COALESCE(complemento, complemento_res, '')) > 60"))['t'];
                        echo $qtd . " registros";
                        ?>
                    </span>
                </h5>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-danger text-white">
                            <tr>
                                <th>Cliente</th>
                                <th>Complemento</th>
                                <th class="text-center">Tamanho</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT nome, login, uuid_cliente, COALESCE(complemento, complemento_res, '') AS complemento
                                FROM sis_cliente 
                                WHERE cli_ativado='s' 
                                  AND LENGTH(COALESCE(complemento, complemento_res, '')) > 60
                                ORDER BY LENGTH(COALESCE(complemento, complemento_res, '')) DESC";

                        $res = mysqli_query($link, $sql);
                        while($r = mysqli_fetch_array($res)):
                            $comp = $r['complemento'];
                            $tam  = strlen($comp);
                        ?>
                            <tr class="table-danger">
                                <td class="align-middle">
                                    <a href="../../cliente_alt.hhvm?uuid=<?=urlencode($r['uuid_cliente'])?>" 
                                       target="_blank" class="fw-bold text-dark d-block">
                                        <?=htmlspecialchars($r['nome'])?>
                                    </a>
                                    <small class="text-muted">@<?=htmlspecialchars($r['login'])?></small>
                                    <br><br>
                                    <a href="../../cliente_alt.hhvm?uuid=<?=urlencode($r['uuid_cliente'])?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-warning">
                                        ALTERAR COMPLEMENTO
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <div class="comp-box">
                                        <?=nl2br(htmlspecialchars($comp))?>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-danger fs-5"><?=$tam?> chars</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-4">
                    <strong>Dica:</strong> Clique em <strong>"ALTERAR COMPLEMENTO"</strong> para abrir a ficha do cliente e corrigir o endereço.
                </div>
            </div>
        </div>
    </div>

    <?php include('../../baixo.php'); ?>
</body>
</html>
