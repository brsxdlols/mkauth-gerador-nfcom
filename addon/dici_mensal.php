<?php
include('addons.class.php');
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
</head>

<body>
<?php 
    include('config.hhvm'); 

    include('../../topo.php'); 
     include('nav/navbar.php');    
?>

<?php
$diretorio_arquivos = '/opt/mk-auth/central/disco_virtual/';
$items2 = [];

if (is_dir($diretorio_arquivos)) {
    $arquivos = scandir($diretorio_arquivos);
    foreach ($arquivos as $arquivo) {
        $caminho_completo = $diretorio_arquivos . $arquivo;
        if (is_file($caminho_completo) && preg_match('/^DICI_MENSAL_/', $arquivo)) {
            $items2[] = $caminho_completo;
        }
    }
    usort($items2, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
}
?>
    <div class='container-fluid'>

		<?php
$anoAtual = date("Y");
$mesAtual = date("m");
?>

<div class="container py-4">
    <form id="relatorioForm" class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-sliders"></i> Opções de Filtro
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="desativado" name="desativado" value="sim"
                            <?php if(isset($_GET['desativado']) && $_GET['desativado'] == 'sim') echo "checked"; ?>>
                        <label class="form-check-label" for="desativado">Incluir cliente desativado</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="bloqueado" name="bloqueado" value="sim"
                            <?php if(isset($_GET['bloqueado']) && $_GET['bloqueado'] == 'sim') echo "checked"; ?>>
                        <label class="form-check-label" for="bloqueado">Incluir cliente bloqueado</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="ngerarclienteisentoP" name="ngerarclienteisentoP" value="sim"
                            <?php if(isset($_GET['ngerarclienteisentoP']) && $_GET['ngerarclienteisentoP'] == 'sim') echo "checked"; ?>>
                        <label class="form-check-label" for="ngerarclienteisentoP">Incluir cliente isento</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="ngerarsici" name="ngerarsici" value="sim"
                            <?php if(isset($_GET['ngerarsici']) && $_GET['ngerarsici'] == 'sim') echo "checked"; ?>>
                        <label class="form-check-label" for="ngerarsici">Clientes sem marcação DICI</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ngerarnf" name="ngerarnf" value="sim"
                            <?php if(isset($_GET['ngerarnf']) && $_GET['ngerarnf'] == 'sim') echo "checked"; ?>>
                        <label class="form-check-label" for="ngerarnf">Clientes sem marcação NF-e</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-calendar-check"></i> Período de Referência
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="mes" class="form-label">Mês</label>
                        <select name="mes" id="mes" class="form-select">
                            <?php
                            $nomesMes = [
                                "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março",
                                "04" => "Abril", "05" => "Maio", "06" => "Junho",
                                "07" => "Julho", "08" => "Agosto", "09" => "Setembro",
                                "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro"
                            ];
                            foreach($nomesMes as $num => $nome): ?>
                                <option value="<?= $num ?>" 
                                    <?php 
                                    if((isset($_GET['mes']) && $_GET['mes'] == $num) || (!isset($_GET['mes']) && $mesAtual == $num)) 
                                        echo "selected"; 
                                    ?>>
                                    <?= $num ?> - <?= $nome ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="ano" class="form-label">Ano</label>
                        <select name="ano" id="ano" class="form-select">
                            <?php for($ano = 2020; $ano <= 2031; $ano++): ?>
                                <option value="<?= $ano ?>" 
                                    <?php 
                                    if((isset($_GET['ano']) && $_GET['ano'] == $ano) || (!isset($_GET['ano']) && $anoAtual == $ano)) 
                                        echo "selected"; 
                                    ?>>
                                    <?= $ano ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-center">
            <input type="hidden" name="form_submitted" value="1"/>
            <button type="submit" class="btn btn-lg btn-primary px-5 shadow mt-3">
                <i class="bi bi-file-earmark-spreadsheet"></i> Gerar documento (CSV)
            </button>
        </div>
    </form>
</div>

<div id="resultadoRelatorio"></div>

<h5 class="text-center mb-3"><b>Histórico de arquivos DICI gerados no sistema</b></h5>
<div class="table-responsive" style="max-height: 400px; overflow:auto;">
    <table class="table table-striped table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th class="text-white">Nome Arquivo</th>
                <th class="text-white">Data Geração</th>
                <th class="text-white">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items2 as $item) { ?>
                <tr>
                    <td><?php echo basename($item); ?></td>
                    <td><?php echo date("d/m/Y H:i:s", filemtime($item)); ?></td>
                    <td>
                        <a href="<?php echo '/central/disco_virtual/' . basename($item); ?>" 
                           class="btn btn-sm rounded-pill" 
                           style="background-color:#dc3545; color:white; border:none;" 
                           title="Download do Arquivo" 
                           download>
                            <i class="bi bi-cloud-arrow-down-fill me-1"></i> Download
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


    <div class="pt-3">
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap" style="font-size: 12px; padding: 8px 0;">
            
            <span><strong>Addon NFCOM NOTA 62
            <a href="https://wa.me/5582987361943?text=Ol%C3%A1%2C+gostaria+de+saber+mais+sobre+o+addon+de+Envio+de+Mensagens+Personalizadas" 
               class="btn btn-link p-0 m-0 text-success" style="font-size: 11px;" target="_blank">
            </a>
        </div>
    </div>


<?php include('../../baixo.php'); ?>
<script src="../../menu.js.hhvm"></script>

<script>
$(document).ready(function() {
    $('#relatorioForm').on('submit', function(e) {
        e.preventDefault(); 

        $.ajax({
            url: 'ajax_mensal.php',
            type: 'GET',
            data: $(this).serialize(), 
            success: function(response) {
                $('#resultadoRelatorio').html(response);
            },
            error: function(xhr, status, error) {
                var detalhe = xhr.responseText ? xhr.responseText : error;
                $('#resultadoRelatorio').html('<div class="alert alert-danger">Erro ao gerar o relatório: ' + $('<div>').text(detalhe).html() + '</div>');
            }
        });
    });
});
</script>

</body>
</html>
