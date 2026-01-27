<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/SMTP.php';
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../feedback.php';
require_once __DIR__ . '/../admin/logs.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$email = $_POST['email'] ?? '';

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El correo es requerido']);
    exit();
}

// Activar debug si pasas ?debug=1 (temporal)
$debug = (isset($_GET['debug']) && $_GET['debug'] === '1');

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // No revelar si el email existe o no por seguridad
        echo json_encode(['success' => true, 'message' => 'Si el correo existe, se ha enviado un código']);
        exit();
    }

    $user_id = $user['user_id'];

    // Generar código de 6 dígitos
    $reset_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Marcar resets anteriores como usados
    $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE user_id = ? AND used = FALSE");
    $stmt->execute([$user_id]);

    // Insertar nuevo código (expira en 15 minutos)
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, reset_code, expires_at) 
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
    ");
    $stmt->execute([$user_id, $reset_code]);

    // Enviar email con el código
    $mail = new PHPMailer(true);

    // Opcional: enviar debug de SMTP a los logs (solo en debug)
    if ($debug) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // muy verboso
        $mail->Debugoutput = function($str, $level) {
            // Escribe en tu sistema de logs
            writeLog("[PHPMailer] " . trim($str));
        };
    }

    // Configuración SMTP (ajusta si usas otro proveedor)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'simbio.noreply@gmail.com';   // <-- verifica este correo
    $mail->Password = 'jgvc vqev wpry dafn';        // <-- verifica que sea app password válido
    $mail->SMTPSecure = 'tls'; // o PHPMailer::ENCRYPTION_SMTPS para 465
    $mail->Port = 587;

    $mail->setFrom('simbio.noreply@gmail.com', 'Simbio');
    $mail->addAddress($email);
    $mail->Subject = 'Código de recuperación de contraseña';
    $mail->Body = "Tu código de recuperación de contraseña es: <strong>$reset_code</strong>\n\nEste código expirará en 15 minutos.";
    $mail->isHTML(true);

    // Intentar enviar
    if (!$mail->send()) {
        // Registrar error y devolver false (para debugging devolvemos ErrorInfo si debug=1)
        writeLog("Error al enviar código de reset a: $email. PHPMailer ErrorInfo: " . $mail->ErrorInfo);

        if ($debug) {
            echo json_encode(['success' => false, 'message' => 'Error al enviar correo', 'error' => $mail->ErrorInfo]);
        } else {
            // Mantener la respuesta indistinguible para usuarios
            echo json_encode(['success' => true, 'message' => 'Si el correo existe, se ha enviado un código']);
        }
        exit();
    }

    writeLog("Código de recuperación enviado a: $email (code: $reset_code)");
    // En modo debug también devolvemos el código en la respuesta para pruebas locales (NO dejar en prod)
    if ($debug) {
        echo json_encode(['success' => true, 'message' => 'Código enviado (modo debug)', 'code' => $reset_code]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Si el correo existe, se ha enviado un código']);
    }

} catch (PDOException $e) {
    writeLog("Error en send_reset_code.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>