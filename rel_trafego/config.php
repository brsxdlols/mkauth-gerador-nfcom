<?php
require_once __DIR__ . '/mkauth_session.php';
$connectionFile = '/opt/mk-auth/include/conexao.php';
if (!is_readable($connectionFile)) {
    http_response_code(500);
    exit('Conexao do MK-Auth nao encontrada.');
}
require_once $connectionFile;
if (!isset($LOADMYSQL) || !($LOADMYSQL instanceof mysqli)) {
    http_response_code(500);
    exit('Conexao mysqli do MK-Auth indisponivel.');
}
$link = $LOADMYSQL;
$acesso_permitido = mkauth_trafego_is_authenticated();
