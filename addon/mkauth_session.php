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

/* O arquivo reservado do MK-Auth inicializa constantes e o tema do painel. */
$mkauthAddonBootstrap = __DIR__ . '/addons.class.php';
if (is_readable($mkauthAddonBootstrap)) {
    include_once $mkauthAddonBootstrap;
}

$manifestPath = __DIR__ . '/manifest.json';
$Manifest = (object) array('name' => 'GERADOR DE NFcom + Dici', 'version' => '1.1.4');
if (is_readable($manifestPath)) {
    $decoded = json_decode(file_get_contents($manifestPath));
    if (is_object($decoded)) $Manifest = $decoded;
}
