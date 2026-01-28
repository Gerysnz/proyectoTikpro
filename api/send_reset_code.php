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

// Limpiar y normalizar email
$email = trim(strtolower($_POST['email'] ?? ''));

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El correo es requerido']);
    exit();
}

// Validar formato email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de correo inválido']);
    exit();
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE LOWER(email) = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ❌ EMAIL NO REGISTRADO
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'El correu introduït no està registrat en la nostra aplicació'
        ]);
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

    // Enviar email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'xalomes276@gmail.com';
        $mail->Password = 'ugtibqpevipgtvom';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 30;
        $mail->SMTPDebug = 0;

        $mail->setFrom('simbio.noreply@gmail.com', 'Simbio');
        $mail->addAddress($email);
        $mail->Subject = 'Codi de recuperacio de contrasenya - Simbio';

        $mail->Body = "
            Hola,<br><br>
            El teu codi de recuperació d'accés és:<br><br>
            <h2 style='color: #007bff;'>$reset_code</h2>
            Aquest codi caducarà en 15 minuts.<br><br>
            No comparteixis aquest codi amb ningú.<br><br>
            Salutacions,<br>
            L'equip de Simbio
        ";

        $mail->AltBody = "El teu codi de recuperació és: $reset_code. Caduca en 15 minuts.";
        $mail->isHTML(true);

        $mail->send();
        writeLog("✓ Código de recuperación enviado a: $email");

    } catch (Exception $e) {
        $fullError = "PHPMailer Exception: " . $e->getMessage() . " | SMTP Error: " . $mail->ErrorInfo;
        writeLog("✗ Error al enviar a: $email. Detalles: $fullError");
        error_log($fullError);

        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo']);
        exit();
    }

    echo json_encode(['success' => true, 'message' => 'Codi enviat correctament']);

} catch (PDOException $e) {
    writeLog("Error en send_reset_code.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>
