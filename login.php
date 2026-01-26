<?php
session_start();
require_once __DIR__ . "/feedback.php";
require_once __DIR__ . "/admin/logs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once __DIR__ . '/api/db.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = hash('sha256', $password);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if ($user && hash_equals($user['password'], $password_hash)) {
        // Guardar usuario en sesión
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        writeLog($_SESSION['user_email'] . " ha iniciado sesión en la aplicación");
        session_write_close();
        header("Location: discover.php");
        exit();

    } else {
        setNotification('error', 'Usuario o contraseña incorrectos');
        writeLog("Intento de login fallido con email: " . $email);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
    <header class="login-header">Simbio</header>
    <main class="login-contenedor">
        <h2 id="formTitle">Iniciar Sessió</h2>
        
        <form id="loginForm" class="login-form" action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
            <label for="password">Contrasenya</label>
            <input type="password" id="password" name="password" required />
            <button type="submit">Accedir</button>
        </form>

        <form id="forgotPasswordForm" class="login-form hidden" action="forgot_password.php" method="POST">
            <label for="recover-email">Email</label>
            <input type="email" id="recover-email" name="email" required />
            <button type="submit">Enviar codi</button>
        </form>

        <div style="text-align: center; margin-top: 15px;">
            <a href="#" id="forgotPasswordLink" style="color: #007bff; text-decoration: none; font-size: 14px;">Contrasenya oblidada?</a>
            <a href="#" id="backToLoginLink" style="color: #007bff; text-decoration: none; font-size: 14px;" class="hidden">Tornar al login</a>
        </div>

        <br>
        <?php showNotification(); ?>
    </main>
    <script src="js/login.js?t=<?php echo time(); ?>"></script>
</body>
</html>
