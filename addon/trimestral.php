<?php
include('addons.class.php');

session_name('mka');
if (!isset($_SESSION)) session_start();
if (isset($_SESSION['mka_logado'])) {
} else {
    session_name(strtoupper('mka'));
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['MKA_Logado'])) {
        exit('Acesso negado... <a href="/admin/login.hhvm">Fazer Login</a>');
    } else {
    }
}
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('../../topo.php'); ?>
    <?php include 'nav/navbar.php'; ?>
    <div class='container-fluid'>

        <?php 
        $mes_ref;
        
        $path = '/opt/mk-auth/central/disco_virtual/*PPP*.csv';
        $items = array();
        $fileList = glob($path);
        foreach($fileList as $filename){
            if(is_file($filename)){
                $items[] = $filename;
            }   
        }
        
        function sortFunction($a, $b) {					
            $v1 = filemtime($a);
            $v2 = filemtime($b);
            return $v2 - $v1;
        }
        usort($items, "sortFunction");
        
        $path2 = '/opt/mk-auth/central/disco_virtual/*dbexport1*.csv';
        $items2 = array();
        $fileList2 = glob($path2);
        foreach($fileList2 as $filename){
            if(is_file($filename)){
                $items2[] = $filename;
            }   
        }
        
        usort($items2, "sortFunction");
        ?>

        <?php
        $anoAtual = date("Y");
        $mesAtual = date("n");

        if ($mesAtual >= 1 && $mesAtual <= 3) {
            $trimestreAtual = "01";
        } elseif ($mesAtual >= 4 && $mesAtual <= 6) {
            $trimestreAtual = "04";
        } elseif ($mesAtual >= 7 && $mesAtual <= 9) {
            $trimestreAtual = "07";
        } else {
            $trimestreAtual = "10";
        }
        ?>

        <div class="container py-4">
            <form id="form_ppp" class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="bi bi-sliders"></i> Opções de Geração
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ngerarsiciRef" name="ngerarsiciRef" value="sim" 
                                    <?php if(isset($_GET['ngerarsiciRef']) && $_GET['ngerarsiciRef'] == 'sim') echo "checked"; ?>>
                                <label class="form-check-label" for="ngerarsiciRef">Clientes sem DICI</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ngerarnfRef" name="ngerarnfRef" value="sim" 
                                    <?php if(isset($_GET['ngerarnfRef']) && $_GET['ngerarnfRef'] == 'sim') echo "checked"; ?>>
                                <label class="form-check-label" for="ngerarnfRef">Clientes sem NF-e</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ngerarclienteisento" name="ngerarclienteisento" value="sim" 
                                    <?php if(isset($_GET['ngerarclienteisento']) && $_GET['ngerarclienteisento'] == 'sim') echo "checked"; ?>>
                                <label class="form-check-label" for="ngerarclienteisento">Clientes ISENTOS</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ngreflancmanual" name="ngreflancmanual" value="sim" 
                                    <?php if(isset($_GET['ngreflancmanual']) && $_GET['ngreflancmanual'] == 'sim') echo "checked"; ?>>
                                <label class="form-check-label" for="ngreflancmanual">Lançamentos manuais</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ngboletodesp" name="ngboletodesp" value="sim" 
                                    <?php if(isset($_GET['ngboletodesp']) && $_GET['ngboletodesp'] == 'sim') echo "checked"; ?>>
                                <label class="form-check-label" for="ngboletodesp">Taxa do boleto como despesa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-calendar-check"></i> Período de Referência
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="mesRef" class="form-label">Trimestre</label>
                                <select id="mesRef" name="mesRef" class="form-select">
                                    <option value="01" <?php if((isset($_GET['mesRef']) && $_GET['mesRef'] == '01') || (!isset($_GET['mesRef']) && $trimestreAtual == "01")) echo "selected"; ?>>1º (JAN / FEV / MAR)</option>
                                    <option value="04" <?php if((isset($_GET['mesRef']) && $_GET['mesRef'] == '04') || (!isset($_GET['mesRef']) && $trimestreAtual == "04")) echo "selected"; ?>>2º (ABR / MAI / JUN)</option>
                                    <option value="07" <?php if((isset($_GET['mesRef']) && $_GET['mesRef'] == '07') || (!isset($_GET['mesRef']) && $trimestreAtual == "07")) echo "selected"; ?>>3º (JUL / AGO / SET)</option>
                                    <option value="10" <?php if((isset($_GET['mesRef']) && $_GET['mesRef'] == '10') || (!isset($_GET['mesRef']) && $trimestreAtual == "10")) echo "selected"; ?>>4º (OUT / NOV / DEZ)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="anoRef" class="form-label">Ano</label>
                                <select name="anoRef" id="anoRef" class="form-select">
                                    <?php for($ano = 2020; $ano <= 2031; $ano++): ?>
                                        <option value="<?= $ano ?>" <?php 
                                            if((isset($_GET['anoRef']) && $_GET['anoRef'] == $ano) || (!isset($_GET['anoRef']) && $ano == $anoAtual)) 
                                                echo "selected"; 
                                        ?>><?= $ano ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-warning fw-bold">
                            <i class="bi bi-cash-stack"></i> Valores Extras
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Valor por boleto</label>
                                <input type="number" step="0.01" name="valorpgboleto" class="form-control" 
                                    value="<?php if(isset($_GET['valorpgboleto']) && $_GET['valorpgboleto'] != '0') echo $_GET['valorpgboleto']; ?>">
                                <small class="text-muted">Ex.: Taxa Efi, Iugu, entre outros</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Investimento no período</label>
                                <input type="number" step="0.01" name="valorinvestidopel" class="form-control" 
                                    value="<?php if(isset($_GET['valorinvestidopel']) && $_GET['valorinvestidopel'] != '0') echo $_GET['valorinvestidopel']; ?>">
                                <small class="text-muted">Ex.: Aquisição fora do sistema</small>
                            </div>

                            <div>
                                <label class="form-label">Despesas não listadas</label>
                                <input type="number" step="0.01" name="valordespnaolistada" class="form-control" 
                                    value="<?php if(isset($_GET['valordespnaolistada']) && $_GET['valordespnaolistada'] != '0') echo $_GET['valordespnaolistada']; ?>">
                                <small class="text-muted">Ex.: Funcionário não lançado</small>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="col-12 text-center">
                    <input type="hidden" name="form_pp2" value="1"/>
                    <button type="submit" class="btn btn-lg btn-primary px-5 shadow">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Gerar CSV
                    </button>
                </div>
            </form>
        </div>
        <div id="resultado_relatorio" class="mt-4"></div>

        <br/><br/>

        <h5 class="text-center mb-3"><b>Histórico de arquivos PPP gerados no sistema</b></h5>

        <div class="table-responsive mx-auto" style="height:150px; overflow:auto; max-width: 800px;">
            <table class="table table-striped table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th class="text-white">Nome Arquivo</th>
                        <th class="text-white">Data Geração</th>
                        <th class="text-white">Ações</th>
                    </tr>
                </thead>
                <tbody>  
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <td><?php echo basename($item); ?></td>   
                            <td><?php echo date("d/m/Y H:i:s", filemtime($item)); ?></td>   
                            <td>
                                <a href="<?php echo '/central/disco_virtual/' . basename($item); ?>" 
                                   class="btn btn-danger btn-sm rounded-pill" 
                                   download 
                                   title="Download do Arquivo">
                                    Download
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

    <div class="text-center" style="font-size: 12px; color: #555;">
        <p class="mb-1"><strong>🙏 AJUDE O PROJETO</strong></p>
        <p class="mb-1">Chave pix Celular: <span id="pixKey">82987361943</span></p>
        <p class="mb-1">🏦 Banco Inter — 👤 Elton Pereira</p>
        <button class="btn btn-sm btn-success" onclick="copiarPix()">📋 Copiar PIX</button>
    </div>
</div>

<script>
function copiarPix() {
    var pix = document.getElementById("pixKey").innerText;
    navigator.clipboard.writeText(pix).then(function() {
        alert("✅ PIX copiado: " + pix);
    }, function() {
        alert("❌ Erro ao copiar PIX!");
    });
}
</script>       

    
        <script>
$(document).ready(function() {
    $('#form_ppp').on('submit', function(e) {
        e.preventDefault(); 

        // Pega os valores para montar a mensagem de confirmação
        const trimestre = $("#mesRef option:selected").text();
        const ano = $("#anoRef").val();
        const checks = [];
        if ($('#ngerarsiciRef').is(':checked')) checks.push("Clientes sem DICI");
        if ($('#ngerarnfRef').is(':checked')) checks.push("Clientes sem NF-e");
        if ($('#ngerarclienteisento').is(':checked')) checks.push("Clientes ISENTOS");
        if ($('#ngreflancmanual').is(':checked')) checks.push("Lançamentos manuais");
        if ($('#ngboletodesp').is(':checked')) checks.push("Taxa boleto como despesa");

        const extras = [];
        if ($('[name="valorpgboleto"]').val() > 0) extras.push("Taxa boleto: R$ " + $('[name="valorpgboleto"]').val());
        if ($('[name="valorinvestidopel"]').val() > 0) extras.push("Investimento: R$ " + $('[name="valorinvestidopel"]').val());
        if ($('[name="valordespnaolistada"]').val() > 0) extras.push("Despesas extras: R$ " + $('[name="valordespnaolistada"]').val());

        Swal.fire({
            title: 'Confirmar geração do CSV PPP?',
            html: `
                <p><strong>Período:</strong> ${trimestre} de ${ano}</p>
                ${checks.length > 0 ? '<p><strong>Opções marcadas:</strong><br>• ' + checks.join('<br>• ') + '</p>' : ''}
                ${extras.length > 0 ? '<p><strong>Valores extras:</strong><br>• ' + extras.join('<br>• ') + '</p>' : ''}
                <hr>
                <small>O arquivo será gerado em poucos segundos.<br>
                <strong>Deseja continuar?</strong></small>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, gerar CSV!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostra loading
                Swal.fire({
                    title: 'Gerando arquivo...',
                    html: 'Por favor aguarde, isso pode levar alguns segundos.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'ajax_ppp.php',
                    type: 'GET',
                    data: $('#form_ppp').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pronto!',
                                html: 'Arquivo gerado com sucesso!<br><br>' + response.html,
                                confirmButtonText: 'OK'
                            });
                            // Atualiza a lista de arquivos sem recarregar a página (opcional)
                            location.reload(); // ou você pode fazer um AJAX para atualizar só a tabela
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Falha ao gerar o relatório.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de comunicação',
                            text: 'Não foi possível conectar ao servidor.'
                        });
                    }
                });
            }
        });
    });
});
</script>

        <?php include('../../baixo.php'); ?>
        <script src="../../menu.js.hhvm"></script>
    </div>
    
</body>
</html>