<?php
require_once __DIR__ . '/config.hhvm';
if (!$link) {
    http_response_code(500);
    echo "Erro na conexão com o banco.";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}

$nome  = trim($_POST['nome'] ?? '');
$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? '';

if (empty($nome) || empty($campo) || $valor === '') {
    http_response_code(400);
    echo "Dados incompletos.";
    exit;
}

$nome_esc = mysqli_real_escape_string($link, $nome);

$campos_permitidos = [
    'geranfe'     => ['sim', 'nao'],
    'local_dici'  => ['u', 'r'],
    'gsici'       => ['1', '0'],
    'nfcom_dici'  => ['sim', 'nao']
];

if (!array_key_exists($campo, $campos_permitidos)) {
    http_response_code(400);
    echo "Campo inválido.";
    exit;
}

if (!in_array($valor, $campos_permitidos[$campo], true)) {
    http_response_code(400);
    echo "Valor inválido para o campo.";
    exit;
}

if ($campo === 'nfcom_dici') {
    $geranfe_val = $valor === 'sim' ? 'sim' : 'nao';
    $gsici_val   = $valor === 'sim' ? '1'   : '0';

    $query = "UPDATE sis_cliente SET geranfe = ?, gsici = ? WHERE nome = ?";
    $stmt  = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $geranfe_val, $gsici_val, $nome);
} else {
    $query = "UPDATE sis_cliente SET `$campo` = ? WHERE nome = ?";
    $stmt  = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $valor, $nome);
}

if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar consulta.";
    exit;
}

$sucesso = mysqli_stmt_execute($stmt);

if ($sucesso && mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Atualizado!";
} elseif ($sucesso) {
    echo "Nenhum registro alterado.";
} else {
    http_response_code(500);
    echo "Erro ao atualizar.";
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>
