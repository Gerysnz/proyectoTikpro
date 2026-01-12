<?php
    $mensaje = $_SESSION['name']. " Ha intentado registrarse sin email";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);

    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    
    $ruta_logs = ("/logs/" . $fecha . ".txt");
    file_put_contents($ruta_logs, $linea, FILE_APPEND);

    $mensaje = $_SESSION['name']. " Ha intentado registrarse sin contraseña";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);

    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    
    $ruta_logs = ("/logs/" . $fecha . ".txt");
    file_put_contents($ruta_logs, $linea, FILE_APPEND);



    $mensaje = $_SESSION['name']. " nuevo usuario registrado en la aplicación";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);

    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    
    $ruta_logs = ("/logs/" . $fecha . ".txt");
    file_put_contents($ruta_logs, $linea, FILE_APPEND);


    






?>