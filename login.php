<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dsn = 'mysql:host=localhost;dbname=tikprop;charset=utf8';
    $db_user = 'gery';
    $db_pass = 'superlocal';

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

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && hash_equals($user['password'], $password_hash)) {
        // Guardar usuario en sesión
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        // Si es AJAX, responde solo OK
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo 'OK';
            exit();
        } else {
            header("Location: discover.php");
            exit();
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo "Usuario o contraseña incorrectos";
            exit();
        }
        // Si no es AJAX, sigue mostrando el HTML normalmente
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chamba - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header class="header">Chamba</header>
    <main class="login-contenedor">
        <h2>Iniciar sesión</h2>
        <form id="loginForm" class="login-form" action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required />
            <button type="submit">Entrar</button>
        </form>
        <div id="mensaje-login"></div>
    </main>
    <script src="js/login.js"></script>
</body>
</html>
