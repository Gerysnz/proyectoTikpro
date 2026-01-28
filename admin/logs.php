<?php
function writeLog($mensaje) {
    $fechaArchivo = date("Y-m-d");
    $fechaHora = date("Y-m-d H:i:s");
    $archivo = basename($_SERVER['SCRIPT_NAME']);

    $logDir = __DIR__ . "/logs";
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true);
    }

    $ruta = "$logDir/$fechaArchivo.txt";
    $linea = "[$fechaHora] [$archivo] $mensaje" . PHP_EOL;

    file_put_contents($ruta, $linea, FILE_APPEND | LOCK_EX);
}
?>
