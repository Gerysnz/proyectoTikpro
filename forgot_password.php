<?php
session_start();
require_once __DIR__ . '/feedback.php';
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/admin/logs.php';

$step = $_GET['step'] ?? '1'; // step 1: verificar código, step 2: cambiar contraseña
$reset_code = $_SESSION['verified_reset_code'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === '1') {
        // Verificar el código: si es correcto, INICIAMOS SESIÓN DIRECTAMENTE y redirigimos a discover.php
        $code = $_POST['reset_code'] ?? '';

        if (empty($code)) {
            setNotification('error', 'El código es obligatorio');
        } elseif (strlen($code) !== 6 || !ctype_digit($code)) {
            setNotification('error', 'El código debe tener 6 dígitos');
        } else {
            try {
                // Verificar que el código es válido, no usado y no ha expirado
                $stmt = $pdo->prepare("
                    SELECT pr.user_id, u.email
                    FROM password_resets pr
                    JOIN users u ON pr.user_id = u.user_id
                    WHERE pr.reset_code = ?
                      AND pr.used = FALSE
                      AND pr.expires_at > NOW()
                    LIMIT 1
                ");
                $stmt->execute([$code]);
                $reset = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$reset) {
                    setNotification('error', 'El código es inválido o ha expirado');
                    writeLog("Intento de reset con código inválido: $code");
                } else {
                    // LOGIN directo: crear sesión del usuario y marcar el código como usado
                    $_SESSION['user_id'] = $reset['user_id'];
                    $_SESSION['user_email'] = $reset['email'];

                    // Marcar el reset como usado para que no se pueda reutilizar
                    $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE reset_code = ?");
                    $stmt->execute([$code]);

                    writeLog("Inicio de sesión mediante código para: " . $reset['email']);
                    session_write_close();

                    // Redirigir a la zona autenticada
                    header("Location: discover.php");
                    exit();
                }
            } catch (PDOException $e) {
                setNotification('error', 'Error al verificar el código');
                writeLog("Error en forgot_password.php (verificación): " . $e->getMessage());
            }
        }
    } elseif ($step === '2') {
        // (Mantengo la lógica existente para cambio de contraseña si alguien la usa)
        // Cambiar contraseña (requiere que se haya validado antes y exista $_SESSION['verified_reset_code'])
        if (!$reset_code) {
            setNotification('error', 'Debes verificar el código primero');
            header("Location: forgot_password.php?step=1");
            exit();
        }

        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || empty($confirm_password)) {
            setNotification('error', 'Todos los campos son obligatorios');
        } elseif ($new_password !== $confirm_password) {
            setNotification('error', 'Las contraseñas no coinciden');
        } elseif (strlen($new_password) < 6) {
            setNotification('error', 'La contraseña debe tener al menos 6 caracteres');
        } else {
            try {
                // Obtener el usuario por el código verificado
                $stmt = $pdo->prepare("
                    SELECT pr.user_id, u.email 
                    FROM password_resets pr
                    JOIN users u ON pr.user_id = u.user_id
                    WHERE pr.reset_code = ? 
                    AND pr.used = FALSE 
                    AND pr.expires_at > NOW()
                ");
                $stmt->execute([$reset_code]);
                $reset = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$reset) {
                    setNotification('error', 'El código ha expirado');
                    unset($_SESSION['verified_reset_code']);
                    header("Location: forgot_password.php?step=1");
                    exit();
                }

                // Actualizar contraseña
                $password_hash = hash('sha256', $new_password);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->execute([$password_hash, $reset['user_id']]);

                // Marcar el código como usado
                $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE reset_code = ?");
                $stmt->execute([$reset_code]);

                // Limpiar sesión
                unset($_SESSION['verified_reset_code']);

                writeLog("Contraseña restablecida para: {$reset['email']}");
                setNotification('success', 'Contraseña actualizada correctamente. Por favor, inicia sesión con tu nueva contraseña.');
                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                setNotification('error', 'Error al cambiar la contraseña');
                writeLog("Error en forgot_password.php: " . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar contraseña - Simbio</title>
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
    <main class="login-contenedor">
        <h2>Recuperar Contrasenya</h2>

        <?php if ($step === '1'): ?>
            <p>Introduce el código de 6 dígitos que te hemos enviado al correo.</p>
            <form class="login-form" action="forgot_password.php?step=1" method="POST">
                <label for="reset_code">Codi de 6 dígits</label>
                <input type="text" id="reset_code" name="reset_code" maxlength="6" required />
                <button type="submit">Verificar Codi</button>
            </form>

        <?php elseif ($step === '2' && $reset_code): ?>
            <form class="login-form" action="forgot_password.php?step=2" method="POST">
                <label for="new_password">Nova contrasenya:</label>
                <input type="password" id="new_password" name="new_password" required />
                
                <label for="confirm_password">Confirmar contrasenya:</label>
                <input type="password" id="confirm_password" name="confirm_password" required />
                
                <button type="submit">Canviar contrasenya</button>
            </form>
        <?php else: ?>
            <p style="text-align: center; color: #dc3545;">El codi ha expirat o no és vàlid. Torna al login per intentar-ho de nou.</p>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php" style="color: #007bff; text-decoration: none; font-size: 14px;">Tornar al login</a>
        </div>

        <br>
        <?php showNotification(); ?>
    </main>
</body>
</html>