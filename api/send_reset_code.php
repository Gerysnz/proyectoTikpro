<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/SMTP.php';
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../feedback.php';
require_once __DIR__ . '/../admin/logs.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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
    
    // Marcar reseteos anteriores como usados
    $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE user_id = ? AND used = FALSE");
    $stmt->execute([$user_id]);

    // Insertar nuevo código (expira en 15 minutos)
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, reset_code, expires_at) 
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
    ");
    $stmt->execute([$user_id, $reset_code]);

    // Enviar email con el código
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'simbio.noreply@gmail.com';
    $mail->Password = 'jgvc vqev wpry dafn'; // App password de Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('simbio.noreply@gmail.com', 'Simbio');
    $mail->addAddress($email);
    $mail->Subject = 'Código de recuperación de contraseña';
    $mail->Body = "Tu código de recuperación de contraseña es: <strong>$reset_code</strong>\n\nEste código expirará en 15 minutos.";
    $mail->isHTML(true);

    if (!$mail->send()) {
        writeLog("Error al enviar código de reset a: $email. Error: " . $mail->ErrorInfo);
        echo json_encode(['success' => true, 'message' => 'Si el correo existe, se ha enviado un código']);
        exit();
    }

    writeLog("Código de recuperación enviado a: $email");
    echo json_encode(['success' => true, 'message' => 'Si el correo existe, se ha enviado un código']);
    
} catch (PDOException $e) {
    writeLog("Error en send_reset_code.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>
