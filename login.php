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


    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
/* =========================
   LOGIN PAGE (solo login)
   ========================= */

.login-page {
    font-family: 'Open Sans', Arial, sans-serif;
    background-color: var(--beige-light);
    color: var(--blue-gray);
    margin: 0;
    padding: 0;
}

/* Header */
.login-page .login-header {
    background-color: white;
    padding: 20px 24px;
    border-bottom: 2px solid var(--green-forest);
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 30px;
    color: var(--green-forest);
    text-align: center;
    letter-spacing: 2px;
}

/* Contenedor principal */
.login-page .login-contenedor {
    max-width: 370px;
    margin: 48px auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 15px rgba(38, 70, 83, 0.10);
    padding: 2.5rem 2rem 2rem;
}

/* Título */
.login-page .login-contenedor h2 {
    text-align: center;
    color: var(--green-forest);
    margin-bottom: 1.7rem;
    font-weight: 700;
    font-family: 'Poppins', sans-serif;
    font-size: 1.6rem;
}

/* Formulario */
.login-page .login-form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.login-page .login-form label {
    color: var(--blue-gray);
    font-size: 1rem;
    margin-bottom: 0.3rem;
    font-weight: 600;
}

.login-page .login-form input {
    padding: 0.7rem;
    border: 1.5px solid var(--green-forest);
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
    background: var(--beige-light);
    transition: border-color 0.2s;
}

.login-page .login-form input:focus {
    border-color: var(--turquoise-light);
}

/* Botón */
.login-page .login-form button {
    background: var(--green-forest);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.9rem;
    font-size: 1.1rem;
    font-weight: 700;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background 0.2s;
    margin-top: 0.5rem;
}

.login-page .login-form button:hover {
    background: var(--turquoise-light);
}

/* Mensaje login */
.login-page #mensaje-login {
    margin-top: 12px;
    font-weight: 600;
    font-size: 16px;
    color: var(--green-forest);
    text-align: center;
}

/* Responsive */
@media (max-width: 600px) {
    .login-page .login-contenedor {
        margin: 32px 16px;
        padding: 2rem 1.5rem;
    }
}

    </style>

    <title>Document</title>
</head>
<body class="login-page">
     <header class="login-header">
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