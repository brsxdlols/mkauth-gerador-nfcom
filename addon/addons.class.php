<?php
/** Bootstrap transparente; nome histórico mantido para compatibilidade. */
$manifestPath = __DIR__ . '/manifest.json';
$Manifest = (object) array('name' => 'GERADOR DE NFcom + Dici', 'version' => '1.0.0');
if (is_readable($manifestPath)) {
    $manifestDecoded = json_decode(file_get_contents($manifestPath));
    if (is_object($manifestDecoded)) {
        $Manifest = $manifestDecoded;
    }
}
