<?php
    $mensaje = $_SESSION['name']. " ha dado like a un proyecto";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);

    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    
    $ruta_logs = ("/logs/" . $fecha . ".txt");
    file_put_contents($ruta_logs, $linea, FILE_APPEND);



    $mensaje = $_SESSION['name']. " ha dado dislike a un proyecto";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);

    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    
    $ruta_logs = ("/logs/" . $fecha . ".txt");
    file_put_contents($ruta_logs, $linea, FILE_APPEND);










?>