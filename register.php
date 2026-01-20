<?php
session_start();
require_once __DIR__ . "/admin/logs.php";

$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
$db_user = 'root';
$db_pass = 'Heector7';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    writeLog("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión: " . $e->getMessage());
}


function sendValidationEmail($email, $hash) {
    $subject = "Valida tu cuenta en Simbio";
    $link = "http://localhost/register.php?validate=$hash";
    $message = "Hola!\n\nGracias por registrarte. Haz clic en el enlace para validar tu cuenta:\n$link\n\nSaludos!";
    $headers = "From: noreply@projectplatform.com\r\n";
    
    $sent = mail($email, $subject, $message, $headers);
    if ($sent) writeLog("Correo de validación enviado a $email");
    else writeLog("Fallo al enviar correo de validación a $email");
    return $sent;
}

if (isset($_GET['validate'])) {
    $hash = $_GET['validate'];
    echo "<div class='notification success'>";
    echo "<h2>¡Gracias por validar tu correo!</h2>";
    echo "<p>Tu cuenta ha sido registrada. Ahora puedes iniciar sesión.</p>";
    echo "</div>";
    writeLog("Usuario validó correo con hash: $hash");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['user_name'] ?? '';
    $surname = $_POST['user_surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $entity = $_POST['entity_name'] ?? '';
    $user_type = $_POST['user_type'] ?? 'company';
    
    if (empty($email)) writeLog("$name Ha intentado registrarse sin email");
    if (empty($password)) writeLog("$name Ha intentado registrarse sin contraseña");
    
    if (empty($email) || empty($password)) {
        echo "<div class='notification error'>Debe completar email y contraseña.</div>";
        exit();
    }

    $password_hash = hash('sha256', $password);
    
    $stmt = $pdo->prepare("
        INSERT INTO users (user_name, user_surname, email, password, entity_name, user_type)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([$name, $surname, $email, $password_hash, $entity, $user_type]);
        $user_id = $pdo->lastInsertId();
        
        $validation_hash = bin2hex(random_bytes(16));
        sendValidationEmail($email, $validation_hash);
        
        echo "<div class='notification success'>";
        echo "<h2>¡Registro completado!</h2>";
        echo "<p>Revisa tu correo para validar tu cuenta.</p>";
        echo "</div>";
        writeLog("$name nuevo usuario registrado en la aplicación (email: $email)");
        exit();
        
    } catch (PDOException $e) {
        writeLog("Error al registrar usuario $name: " . $e->getMessage());
        echo "<div class='notification error'>Error al registrar el usuario: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - Simbio</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
<header class="login-header">Simbio</header>
<main class="login-contenedor">
    <h2>Registro de Usuario</h2>
    <form class="login-form" method="POST">
        <label for="user_name">Nombre</label>
        <input type="text" id="user_name" name="user_name" required>
        
        <label for="user_surname">Apellidos</label>
        <input type="text" id="user_surname" name="user_surname" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
        
        <label for="entity_name">Entidad</label>
        <input type="text" id="entity_name" name="entity_name" required>
        
        <label for="user_type">Tipo de usuario</label>
        <select id="user_type" name="user_type">
            <option value="company">Company</option>
            <option value="center">Center</option>
        </select>
        
        <button type="submit">Registrarse</button>
    </form>
</main>
</body>
</html>
  