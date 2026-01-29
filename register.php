<?php
session_start();
require_once __DIR__ . "/admin/logs.php";

// PHPMailer manual (sin Composer)
require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/api/db.php';



$mensaje = '';

function sendValidationEmail($to, $hash) {
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'loopsis753@gmail.com';
        $mail->Password = 'xehg clnw axvr obac';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('loopsis753@gmail.com', 'Simbio');
        $mail->addAddress($to);

        $link = "https://simbio3.ieti.site/login.php?validate=$hash";


        $mail->isHTML(true);
        $mail->Subject = 'Valida el teu compte a Simbio';

        $mail->Body = "
        <div style='font-family: Arial, Helvetica, sans-serif; background-color:#f6f7f9; padding:20px;'>
            <div style='max-width:600px; margin:0 auto; background-color:#ffffff; padding:30px; border-radius:6px;'>
                
                <h2 style='color:#2c3e50; margin-top:0;'>Benvingut a Simbio 👋</h2>

                <p style='color:#555; font-size:14px; line-height:1.5;'>
                    Gràcies per registrar-te. Per activar el teu compte, fes clic en el següent botó:
                </p>

                <p style='text-align:center; margin:30px 0;'>
                    <a href='$link'
                       style='background-color:#4CAF50; color:#ffffff; padding:12px 20px;
                              text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;'>
                        Validar el meu compte
                    </a>
                </p>

                <p style='color:#777; font-size:13px;'>
                    Si el botó no funciona, copia i enganxa aquest enllaç al teu navegador:
                </p>

                <p style='font-size:12px; word-break:break-all;'>
                    <a href='$link' style='color:#4CAF50;'>$link</a>
                </p>

                <hr style='border:none; border-top:1px solid #eee; margin:30px 0;'>

                <p style='font-size:12px; color:#999;'>
                    Aquest enllaç caduca en 48 hores.<br>
                    Si no has creat aquest compte, pots ignorar aquest correu.
                </p>

                <p style='font-size:12px; color:#999; margin-bottom:0;'>
                    — L'equip de <strong>Simbio</strong>
                </p>
            </div>
        </div>
        ";

        $mail->AltBody = "Valida el teu compte aquí: $link";

        $mail->send();
        writeLog("Correo de validación enviado a $to");
        return true;

    } catch (Exception $e) {
        writeLog("Error enviando correo: {$mail->ErrorInfo}");
        return false;
    }
}


// VALIDACIÓN DEL LINK
if (isset($_GET['validate'])) {
    $hash = $_GET['validate'];

    $stmt = $pdo->prepare("SELECT user_id, validation_expires, is_active FROM users WHERE validation_hash = ?");
    $stmt->execute([$hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_active'] == 1) {
            $mensaje = "<div class='notification error'>El compte ja està activat.</div>";
        } elseif (strtotime($user['validation_expires']) > time()) {
            $pdo->prepare("
                UPDATE users 
                SET is_active = 1, validation_hash = NULL, validation_expires = NULL 
                WHERE user_id = ?
            ")->execute([$user['user_id']]);

            $mensaje = "
                <div class='notification success'>
                    <h2>Gràcies per validar el teu correu!</h2>
                    <p>El teu compte ha estat activat. Ara pots iniciar sessió.</p>
                </div>
            ";
            writeLog("Usuario validó correo con hash: $hash");
        } else {
            $mensaje = "<div class='notification error'>L'enllaç ha caducat. Registra't de nou.</div>";
        }
    } else {
        $mensaje = "<div class='notification error'>Enllaç no vàlid.</div>";
    }
}


// REGISTRO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['user_name'] ?? '';
    $surname = $_POST['user_surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $entity = $_POST['entity_name'] ?? '';
    $user_type = $_POST['user_type'] ?? 'company';

    if (empty($email) || empty($password)) {
        $mensaje = "<div class='notification error'>Has de completar el correu i la contrasenya.</div>";
    } else {
        $password_hash = hash('sha256', $password);
        $validation_hash = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 48 * 3600);

        $stmt = $pdo->prepare("
            INSERT INTO users 
            (user_name, user_surname, email, password, entity_name, user_type, is_active, validation_hash, validation_expires)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)
        ");

        try {
            $stmt->execute([
                $name,
                $surname,
                $email,
                $password_hash,
                $entity,
                $user_type,
                $validation_hash,
                $expires
            ]);

            sendValidationEmail($email, $validation_hash);

            $mensaje = "
                <div class='notification success'>
                    <h2>Registre completat!</h2>
                    <p>Revisa el teu correu per validar el teu compte.</p>
                </div>
            ";

            writeLog("$name nuevo usuario registrado (email: $email)");

        } catch (PDOException $e) {
            writeLog("Error al registrar usuario $name: " . $e->getMessage());
            $mensaje = "<div class='notification error'>Error al registrar l'usuari.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<title>Registro - Simbio</title>

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>

<body class="login-page">
<header class="login-header">
    <span>Simbio</span>
    <a href="login.php" class="register-link">Inicia sessió</a>
</header>

<main class="login-contenedor">

    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <h2>Registre d'Usuari</h2>

    <form class="login-form" method="POST">
        <label>Nom</label>
        <input type="text" name="user_name" required>

        <label>Cognoms</label>
        <input type="text" name="user_surname" required>

        <label>Correu electrònic</label>
        <input type="email" name="email" required>

        <label>Contrasenya</label>
        <input type="password" name="password" required>

        <label>Entitat</label>
        <input type="text" name="entity_name" required>

        <label>Tipus d'usuari</label>
        <select name="user_type">
            <option value="company">Empresa</option>
            <option value="center">Centre</option>
        </select>

        <button type="submit">Registrar-se</button>
        <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
    </form>

</main>
</body>
</html>
