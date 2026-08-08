<?php
function mkauth_addon_is_authenticated()
{
    return !empty($_SESSION['mka_logado']) || !empty($_SESSION['MKA_Logado'])
        || !empty($_SESSION['MM_Usuario']) || !empty($_SESSION['MKA_Usuario']);
}
function mkauth_addon_bootstrap_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $candidates = array();
    foreach (array('mka', 'MKA', session_name()) as $candidate) {
        if ($candidate !== '' && isset($_COOKIE[$candidate])) $candidates[] = $candidate;
    }
    if (!$candidates) $candidates[] = 'mka';
    foreach (array_unique($candidates) as $candidate) {
        session_name($candidate);
        session_start();
        if (mkauth_addon_is_authenticated()) return;
        session_write_close();
        $_SESSION = array();
    }
    session_name($candidates[0]);
    session_start();
}
function mkauth_addon_require_login()
{
    if (!mkauth_addon_is_authenticated()) exit('Acesso negado... <a href="/admin/login.hhvm">Fazer Login</a>');
}
mkauth_addon_bootstrap_session();

/* Carrega somente constantes/funções básicas, sem o bootstrap reservado do addon. */
$mkauthConfigure = dirname(__DIR__, 3) . '/include/configure.php';
if (is_readable($mkauthConfigure)) {
    include_once $mkauthConfigure;
}

/* Compatibilidade com instalações que não possuem configure.php. */
if (!defined('ADMIN2URL')) {
    define('ADMIN2URL', '/admin/');
}

$manifestPath = __DIR__ . '/manifest.json';
$Manifest = (object) array('name' => 'GERADOR DE NFcom + Dici', 'version' => '1.1.6');
if (is_readable($manifestPath)) {
    $decoded = json_decode(file_get_contents($manifestPath));
    if (is_object($decoded)) $Manifest = $decoded;
}
