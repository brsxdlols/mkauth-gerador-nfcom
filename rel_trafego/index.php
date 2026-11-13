<?php
require_once __DIR__ . '/config.php';

// Assuming $Manifest is defined somewhere before this code
$manifestTitle = isset($Manifest->{'name'}) ? $Manifest->{'name'} : '';
$manifestVersion = isset($Manifest->{'version'}) ? $Manifest->{'version'} : '';
?>

<!DOCTYPE html>
<?php
if (isset($_SESSION['MM_Usuario'])) {
    echo '<html lang="pt-BR">'; // Fix versão antiga MK-AUTH
} else {
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK - AUTH :: <?php echo $Manifest->{'name'} . " - V " . $Manifest->{'version'};  ?></title>

    <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />

    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>
    <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        /* Estilos CSS personalizados */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        form,
        .table-container,
        .client-count-container {
            width: 80%;
            margin: 0 auto;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="date"],
        input[type="submit"],
        .clear-button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .clear-button:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 2px;
            text-align: left;
        }

        table th {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        h1 {
            color: #4caf50;
        }

        .client-count-container {
            text-align: center;
            margin-top: 10px;
        }

        .client-count {
            color: #4caf50;
            font-weight: bold;
        }

        .client-count.blue {
            color: #2196F3;
        }

        .nome_cliente a {
            color: blue;
            text-decoration: none;
            font-weight: bold;
        }

        .nome_cliente a:hover {
            text-decoration: underline;
        }

        .nome_cliente td {
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .nome_cliente:nth-child(odd) {
            background-color: #FFFF99;
        }

        .totals-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 300px;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f3f3f3;
            border-radius: 5px;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            color: #4caf50;
            font-weight: bold;
        }
		.totals-container {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        }

        .total-item {
        width: 50%; /* Ajuste conforme necessário */
        padding: 10px;
        background-color: #f3f3f3;
        border-radius: 5px;
        }

        .total-label {
        font-weight: bold;
        }

        .total-value {
        color: #4caf50;
        font-weight: bold;
        }

    </style>

    <script type="text/javascript">
        function clearSearch() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.forms['searchForm'].submit();
        }
    </script>

</head>

<body>
    <?php include('../../topo.php'); ?>

    <nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs">
        <ul>
            <li><a href="#"> ADDON</a></li>
            <li class="is-active">
                <a href="#" aria-current="page"> <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?> </a>
            </li>
        </ul>
    </nav>

    <?php require_once __DIR__ . '/config.php'; ?>

    <?php
    if ($acesso_permitido) {
        // Formulário Atualizado com Funcionalidade de Busca
    ?>
        <form id="searchForm" method="GET" onsubmit="return validateForm()">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="width: 48%;">
                    <label for="startDate">Data de Início:</label>
                    <input type="date" id="startDate" name="startDate" value="<?php echo isset($_GET['startDate']) ? htmlspecialchars($_GET['startDate']) : ''; ?>">
                </div>
                <div style="width: 48%;">
                    <label for="endDate">Data de Fim:</label>
                    <input type="date" id="endDate" name="endDate" value="<?php echo isset($_GET['endDate']) ? htmlspecialchars($_GET['endDate']) : ''; ?>">
                </div>
            </div>
            <input type="submit" value="Buscar">
            <button type="button" onclick="clearSearch()" class="clear-button">Limpar</button>
        </form>

        <?php
        // Dados de conexão com o banco de dados já estão em config.php

// Consulta SQL para obter o total de octetos de sa�da e entrada de acordo com as datas selecionadas
if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    $totalOutputQuery = "SELECT SUM(acctoutputoctets) AS total_output_octets, SUM(acctinputoctets) AS total_input_octets FROM radacct WHERE DATE(acctstarttime) BETWEEN ? AND ?";

    $stmtTotalOutput = mysqli_prepare($link, $totalOutputQuery);

    mysqli_stmt_bind_param($stmtTotalOutput, "ss", $_GET['startDate'], $_GET['endDate']);

    mysqli_stmt_execute($stmtTotalOutput);
    $totalOutputResult = mysqli_stmt_get_result($stmtTotalOutput);
    $totalOutputRow = mysqli_fetch_assoc($totalOutputResult);

    $totalOutputOctets = $totalOutputRow['total_output_octets'];
    $totalInputOctets = $totalOutputRow['total_input_octets'];
    $totalUpDownOctets = $totalOutputOctets + $totalInputOctets; // Total de download e upload juntos

    // Fun��o para formatar automaticamente o tamanho em KB, MB, GB, TB, PB, EB, ZB ou YB
    function formatSize($sizeInOctets) {
        $units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $size = $sizeInOctets / 1024; // Converte para KB inicialmente

        foreach ($units as $unit) {
            if ($size < 1024) {
                return round($size, 2) . " " . $unit;
            }
            $size /= 1024;
        }
        
        // Se ultrapassar YB
        return round($size, 2) . " YB+";
    }

    ?>
    <div class="totals-container">
        <div class="total-item">
            <div class="total-label">Total de Download:</div>
            <div class="total-value"><?php echo formatSize($totalOutputOctets); ?></div>
        </div>
        <div class="total-item">
            <div class="total-label">Total de Upload:</div>
            <div class="total-value"><?php echo formatSize($totalInputOctets); ?></div>
        </div>
        <div class="total-item">
            <div class="total-label">Total de Download e Upload Juntos:</div>
            <div class="total-value"><?php echo formatSize($totalUpDownOctets); ?></div>
        </div>
    </div>
        <?php } ?>


        </div>
    <?php
    } else {
        echo "Acesso não permitido!";
    }
    ?>

    <?php include('../../baixo.php'); ?>

    <script src="../../menu.js.php"></script>
    <?php include('../../rodape.php'); ?>

    <script>
        function validateForm() {
            var startDate = document.getElementById("startDate").value;
            var endDate = document.getElementById("endDate").value;

            if (startDate === '' || endDate === '') {
                alert("Por favor, insira a Data de Início e a Data de Fim.");
                return false;
            }
            return true;
        }

        function clearSearch() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.forms['searchForm'].submit();
        }
    </script>
</body>

</html>
