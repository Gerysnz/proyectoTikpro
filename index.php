<?php
// Conexión PDO aquí
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8mb4';
    $usuario = 'adminsimbio';
    $clave = 'AdminSimbi@26';
    try {
        $pdo = new PDO($dsn, $usuario, $clave);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';


    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    $hash = hash('sha256', $password);


    // Guardar
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar
    if ($usuario && password_verify($password, $usuario['Password'])) { 
        header("Location: discover.php");
        exit(); 
    } else {
        echo "Usuario o contraseña incorrectos";
    }
    exit();
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
        <form class="login-form" action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>


            <button type="submit">Entrar</button>
        </form>
    </main>


    <script>
        // Aquí puedes agregar cualquier script necesario para la página de login
    </script>



</body>
</html>