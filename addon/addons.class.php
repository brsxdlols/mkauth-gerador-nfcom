<?php
/** Abre a sessão autenticada do painel em instalações antigas e novas. */
function mkauth_addon_bootstrap_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $candidates = array();
    foreach (array('mka', 'MKA', session_name()) as $candidate) {
        if ($candidate !== '' && isset($_COOKIE[$candidate])) {
            $candidates[] = $candidate;
        }
    }
    foreach (array_keys($_COOKIE) as $cookieName) {
        if (strcasecmp($cookieName, 'mka') === 0 && !in_array($cookieName, $candidates, true)) {
            $candidates[] = $cookieName;
        }
    }
    if (!$candidates) {
        $candidates[] = 'mka';
    }
    foreach ($candidates as $candidate) {
        session_name($candidate);
        session_start();
        $authenticated = !empty($_SESSION['mka_logado'])
            || !empty($_SESSION['MKA_Logado'])
            || !empty($_SESSION['MM_Usuario'])
            || !empty($_SESSION['MKA_Usuario']);
        if ($authenticated) {
            if (empty($_SESSION['mka_logado']) && empty($_SESSION['MKA_Logado'])) {
                $_SESSION['MKA_Logado'] = true;
            }
            return;
        }
        session_write_close();
        $_SESSION = array();
    }
    session_name($candidates[0]);
    session_start();
}
mkauth_addon_bootstrap_session();

function mkauth_addon_is_authenticated()
{
    return !empty($_SESSION['mka_logado'])
        || !empty($_SESSION['MKA_Logado'])
        || !empty($_SESSION['MM_Usuario'])
        || !empty($_SESSION['MKA_Usuario']);
}

function mkauth_addon_require_login()
{
    if (!mkauth_addon_is_authenticated()) {
        exit('Acesso negado... <a href="/admin/login.hhvm">Fazer Login</a>');
    }
}
/** Bootstrap transparente; nome histórico mantido para compatibilidade. */
$manifestPath = __DIR__ . '/manifest.json';
$Manifest = (object) array('name' => 'GERADOR DE NFcom + Dici', 'version' => '1.1.1');
if (is_readable($manifestPath)) {
    $manifestDecoded = json_decode(file_get_contents($manifestPath));
    if (is_object($manifestDecoded)) {
        $Manifest = $manifestDecoded;
    }
}
