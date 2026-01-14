<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    
    $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
    $db_user = 'root';
    $db_pass = 'Heector7';

    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        writeLog("Intento de login fallido a la base de datos");
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = hash('sha256', $password);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if ($user && hash_equals($user['password'], $password_hash)) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <title>Document</title>


      
</head>
<body>
     <header class="header">
        Chamba
    </header>
    <main class="login-contenedor">
        
        <h2>Iniciar sesión</h2>
        <form id="loginForm" class="login-form" action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            

            <button type="submit">Entrar</button>
        </form>
        <div id="mensaje-login"></div>
    </main>


    <script>
        // Aquí puedes agregar cualquier script necesario para la página de login
    </script>
   
    
    
</body>
</html>

