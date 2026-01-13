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

    // Hasheamos el password introducido
    $password_hash = hash('sha256', $password);

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ?"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && hash_equals($user['password'], $password_hash)) {
        // Guardar usuario en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: discover.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <title>Document</title>
</head>
<body class="login-page">
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

    <!--<script src="js/login.js"></script>-->
</body>
</html>