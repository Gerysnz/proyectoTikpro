<?php
session_start();
require_once __DIR__ . '/feedback.php';
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/admin/logs.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el código de un único uso
    $code = trim($_POST['reset_code'] ?? '');
    
    writeLog("DEBUG: Código recibido: '$code' (longitud: " . strlen($code) . ")");

    if (empty($code)) {
        setNotification('error', 'El código es obligatorio');
    } elseif (strlen($code) !== 6 || !ctype_digit($code)) {
        setNotification('error', 'El código debe tener exactamente 6 dígitos numéricos');
        writeLog("ERROR: Código inválido - longitud: " . strlen($code) . ", es numérico: " . (ctype_digit($code) ? 'sí' : 'no'));
    } else {
        try {
            // Verificar que el código es válido y no ha expirado
            $stmt = $pdo->prepare("
                SELECT pr.user_id, u.email 
                FROM password_resets pr
                JOIN users u ON pr.user_id = u.user_id
                WHERE pr.reset_code = ? 
                AND pr.used = FALSE 
                AND pr.expires_at > NOW()
            ");
            $stmt->execute([$code]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reset) {
                setNotification('error', 'El código no és vàlid o ha expirat');
                writeLog("ERROR: Código no encontrado o expirado - código enviado: $code");
            } else {
                // Marcar el código como usado
                $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE reset_code = ?");
                $stmt->execute([$code]);

                // Iniciar sesión del usuario
                $_SESSION['user_id'] = $reset['user_id'];
                $_SESSION['user_email'] = $reset['email'];
                
                writeLog("✓ SUCCESS: {$reset['email']} inició sesión con código de recuperación");
                session_write_close();
                
                // Redirigir a discover.php
                header("Location: discover.php");
                exit();
            }
        } catch (PDOException $e) {
            setNotification('error', 'Error al verificar el código');
            writeLog("ERROR: Excepción PDO en forgot_password.php: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Accés - Simbio</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
    <header class="login-header">Simbio</header>
    <main class="login-contenedor">
        <h2>Verificar Codi de Recuperació</h2>
        
        <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;">
            Hem enviat un codi de 6 dígits al teu correu electrònic. Introdueix-lo a continuació. El codi caducará en 15 minuts.
        </p>

        <form class="login-form" action="forgot_password.php" method="POST" id="codeForm">
            <label for="reset_code">Codi de 6 dígits:</label>
            <input 
                type="text" 
                id="reset_code" 
                name="reset_code" 
                placeholder="000000" 
                maxlength="6" 
                inputmode="numeric" 
                pattern="[0-9]{6}"
                required 
                style="font-size: 24px; text-align: center; letter-spacing: 5px;"
            />
            <button type="submit" id="submitBtn">Verificar Codi</button>
        </form>

        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php" style="color: #007bff; text-decoration: none; font-size: 14px;">Tornar al login</a>
        </div>

        <br>
        <?php showNotification(); ?>
    </main>
    
    <script>
        const codeForm = document.getElementById('codeForm');
        const resetCodeInput = document.getElementById('reset_code');
        const submitBtn = document.getElementById('submitBtn');

        // Permitir solo números
        resetCodeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validar antes de enviar
        codeForm.addEventListener('submit', function(e) {
            const code = resetCodeInput.value.trim();
            
            if (code.length !== 6) {
                e.preventDefault();
                alert('El código debe tener 6 dígits');
                return false;
            }
            
            if (!/^\d{6}$/.test(code)) {
                e.preventDefault();
                alert('El código debe contener solo números');
                return false;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verificando...';
        });
    </script>
</body>
</html>
