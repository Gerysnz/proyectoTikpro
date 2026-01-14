<?php
function writeLog($mensaje) {
    $fechaArchivo = date("Y-m-d");
    $fechaHora = date("Y-m-d H:i:s");
    $archivo = basename($_SERVER['SCRIPT_NAME']);

    $logDir = __DIR__ . "/logs";
    if (!is_dir($logDir)) {
        $result_m = mkdir($logDir, 0775, true);
        var_dump(array($logDir, $result_m));
    }

    $ruta = "$logDir/$fechaArchivo.txt";
    $linea = "[$fechaHora] [$archivo] $mensaje" . PHP_EOL;

    $result = file_put_contents($ruta, $linea, FILE_APPEND | LOCK_EX);
    var_dump(array($ruta, $linea, $result));
    die('Hola');
}
