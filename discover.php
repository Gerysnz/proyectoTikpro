<?php
    session_start();

    function writeLog($mensaje) {
        $fechaArchivo = date("Y-m-d");
        $fechaHora = date("Y-m-d H:i:s");
        $archivo = basename(__FILE__);

        $logDir = __DIR__ . "/logs";
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $ruta = "$logDir/$fechaArchivo.txt";
        $linea = "[$fechaHora] [$archivo] $mensaje" . PHP_EOL;

        file_put_contents($ruta, $linea, FILE_APPEND);
    }

    writeLog($_SESSION['name'] . " ha dado like a un proyecto");
    writeLog($_SESSION['name'] . " ha dado nope a un proyecto");
    writeLog($_SESSION['name'] . " y el proyecto han hecho match");





?>