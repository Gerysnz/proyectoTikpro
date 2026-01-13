<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
    $db_user = 'adminsimbio';
    $db_pass = 'AdminSimbi@26';

    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = hash('sha256', $password);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // funcion LOGS
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

    if ($user && hash_equals($user['password'], $password_hash)) {

        // Guardamos datos en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        writeLog($_SESSION['user_name'] . " ha iniciado sesión en la aplicación");

        header("Location: discover.php");
        exit();

    } else {
        writeLog("Intento de login fallido con email: " . $email);
        echo "Usuario o contraseña incorrectos";
    }
}
?>







