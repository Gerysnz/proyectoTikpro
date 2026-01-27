<?php
session_start();
require_once __DIR__ . "/admin/logs.php";

// PHPMailer manual (sin Composer)
require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =====================
// CONEXIÓN PDO
// =====================
$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
$db_user = 'javi';
// $db_user = 'root';
$db_pass = 'superlocal';
// $db_pass = 'Heector7';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    writeLog("Error de conexión a DB: " . $e->getMessage());
    die("Error de conexión: " . $e->getMessage());
}

$mensaje = '';

function sendValidationEmail($to, $hash) {
    $mail = new PHPMailer(true);
    try {
        // Configuración SMTP 
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'loopsis753@gmail.com';   //correo  
        $mail->Password = 'xehg clnw axvr obac'; //clave mail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('loopsis753@gmail.com', 'Simbio');
        $mail->addAddress($to);

        $link = "http://simbio3.ieti.site/register.php?validate=$hash";

        $mail->isHTML(true);
        $mail->Subject = 'Valida tu cuenta en Simbio';
        $mail->Body = "
            <h2>Bienvenido a Simbio</h2>
            <p>Para activar tu cuenta haz clic en el siguiente enlace:</p>
            <p><a href='$link'>$link</a></p>
            <p>Este enlace caduca en 48 horas.</p>
        ";
        $mail->AltBody = "Valida tu cuenta aquí: $link";

        $mail->send();
        writeLog("Correo de validación enviado a $to");
        return true;
    } catch (Exception $e) {
        writeLog("Error enviando correo: {$mail->ErrorInfo}");
        return false;
    }
}


if (isset($_GET['validate'])) {
    $hash = $_GET['validate'];

    $stmt = $pdo->prepare("SELECT user_id, validation_expires, is_active FROM users WHERE validation_hash = ?");
    $stmt->execute([$hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_active'] == 1) {
            $mensaje = "<div class='notification error'>La cuenta ya está activada.</div>";
        } elseif (strtotime($user['validation_expires']) > time()) {
            $pdo->prepare("UPDATE users SET is_active = 1, validation_hash = NULL, validation_expires = NULL WHERE user_id = ?")
                ->execute([$user['user_id']]);

            $mensaje = "<div class='notification success'>
                            <h2>¡Gracias por validar tu correo!</h2>
                            <p>Tu cuenta ha sido activada. Ahora puedes iniciar sesión.</p>
                        </div>";
            writeLog("Usuario validó correo con hash: $hash");
        } else {
            $mensaje = "<div class='notification error'>
                            El enlace ha caducado. Regístrate de nuevo.
                        </div>";
        }
    } else {
        $mensaje = "<div class='notification error'>Enlace no válido.</div>";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['user_name'] ?? '';
    $surname = $_POST['user_surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $entity = $_POST['entity_name'] ?? '';
    $user_type = $_POST['user_type'] ?? 'company';

    if (empty($email) || empty($password)) {
        $mensaje = "<div class='notification error'>Debe completar email y contraseña.</div>";
    } else {
        $password_hash = hash('sha256', $password);

        $validation_hash = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 48*3600);

        $stmt = $pdo->prepare("
            INSERT INTO users (user_name, user_surname, email, password, entity_name, user_type, is_active, validation_hash, validation_expires)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
        ");

        try {
            $stmt->execute([$name, $surname, $email, $password_hash, $entity, $user_type, $validation_hash, $expires]);

            sendValidationEmail($email, $validation_hash);

            $mensaje = "<div class='notification success'>
                            <h2>¡Registro completado!</h2>
                            <p>Revisa tu correo para validar tu cuenta.</p>
                        </div>";
            writeLog("$name nuevo usuario registrado en la aplicación (email: $email)");
        } catch (PDOException $e) {
            writeLog("Error al registrar usuario $name: " . $e->getMessage());
            $mensaje = "<div class='notification error'>Error al registrar el usuario: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - Simbio</title>
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
<header class="login-header">Simbio</header>
<main class="login-contenedor">

    <?php if(!empty($mensaje)) echo $mensaje; ?>

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
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>

    </form>
</main>
</body>
</html>
