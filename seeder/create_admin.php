<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../api/db.php';

try {
    echo "<p>Conectando a la base de datos...</p>";
    
    // Crear tabla admin_users si no existe
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        admin_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        admin_name VARCHAR(100) NOT NULL,
        admin_email VARCHAR(150) NOT NULL UNIQUE,
        admin_password CHAR(64) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);
    echo "<p>✅ Tabla creada/verificada</p>";

    // Crear usuario admin "profe"
    $admin_name = "Profe";
    $admin_email = "profe1@simbio.cat";
    $admin_password = "profe123";
    $password_hash = hash('sha256', $admin_password);
    
    echo "<p>Hash de la contraseña: " . $password_hash . "</p>";

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE admin_email = ?");
    $stmt->execute([$admin_email]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<p>Usuario existe? " . ($exists ? 'Sí' : 'No') . "</p>";

    if ($exists == 0) {
        // Insertar nuevo admin
        $stmt = $pdo->prepare("INSERT INTO admin_users (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
        $result = $stmt->execute([$admin_name, $admin_email, $password_hash]);
        
        if ($result) {
            echo "<p>✅ Usuario admin creado exitosamente</p>";
            echo "<p><strong>Credenciales:</strong></p>";
            echo "<p>Email: profe@simbio.cat</p>";
            echo "<p>Contraseña: profe123</p>";
        } else {
            echo "<p>❌ Error al insertar usuario</p>";
        }
    } else {
        echo "<p>⚠️ El usuario ya existe</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " - Línea: " . $e->getLine() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Admin Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="../styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <div class="setup-container">
        <div class="setup-box">
            <h1>Simbio Admin Setup</h1>
            <div id="debug-info">
                <!-- Aquí aparecerán los mensajes de debug -->
            </div>
        </div>
    </div>
</body>
</html>