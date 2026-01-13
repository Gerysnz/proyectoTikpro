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

    <link rel="stylesheet" href="styles.css?=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        /* ESTILOS PARA LOGIN PAGE */
.login-page header {
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

.login-page {
    font-family: 'Open Sans', Arial, sans-serif;
    background-color: var(--beige-light);
    color: var(--blue-gray);
    margin: 0;
    padding: 0;
}

.login-page .login-contenedor {
    max-width: 370px;
    margin: 48px auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 15px rgba(38, 70, 83, 0.10);
    padding: 2.5rem 2rem 2rem 2rem;
}

.login-page .login-contenedor h2 {
    text-align: center;
    color: var(--green-forest);
    margin-bottom: 1.7rem;
    font-weight: 700;
    font-family: 'Poppins', sans-serif;
    font-size: 1.6rem;
}

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

.login-page #mensaje-login {
    margin-top: 12px;
    font-weight: 600;
    font-size: 24px;
    color: var(--green-forest);
    text-align: center;
}

.login-page .container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.login-page .card {
    background-color: white;
    border-radius: 16px;
    padding: 24px 20px;
    margin-bottom: 30px;
    box-shadow: 0 6px 15px rgba(38, 70, 83, 0.1);
    transition: transform 0.3s ease;
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

.login-page .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(38, 70, 83, 0.15);
}

.login-page .video-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.login-page iframe {
    border-radius: 12px;
    width: 100%;
    max-width: 640px;
    height: 360px;
    border: none;
}

.login-page h2 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    margin: 0 0 12px;
    color: var(--blue-gray);
}

.login-page p {
    font-size: 17px;
    line-height: 1.5;
    margin: 0 0 16px;
}

.login-page .tags {
    margin-bottom: 20px;
}

.login-page .tag {
    display: inline-block;
    background-color: var(--orange-bright);
    color: white;
    padding: 6px 14px;
    border-radius: 24px;
    font-size: 15px;
    font-weight: 600;
    margin-right: 10px;
    user-select: none;
    font-family: 'Poppins', sans-serif;
    transition: background-color 0.3s ease;
    cursor: default;
}

.login-page .tag:hover {
    background-color: #e65a2a;
}

.login-page .actions {
    display: flex;
    gap: 16px;
}

.login-page button {
    flex: 1;
    padding: 14px 0;
    border-radius: 12px;
    border: none;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    user-select: none;
}

.login-page .btn-like {
    background-color: var(--green-forest);
    color: white;
}

.login-page .btn-like:hover {
    background-color: #238171;
}

.login-page .btn-dislike {
    background-color: var(--gray-medium);
    color: white;
}

.login-page .btn-dislike:hover {
    background-color: #5c5c5c;
}

.login-page footer {
    text-align: center;
    padding: 20px;
    font-size: 14px;
    color: #666;
    font-family: 'Open Sans', sans-serif;
}

/* Responsive */
@media (max-width: 600px) {
    .login-page .card {
        padding: 12px 2vw;
    }

    .login-page iframe {
        height: 200px;
    }
}
    </style>

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