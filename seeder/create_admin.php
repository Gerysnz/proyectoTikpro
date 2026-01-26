<?php
require_once __DIR__ . '/../api/db.php';

try {
    echo "Conectado a la base de datos project_platform...\n";

    // Crear tabla admin_users si no existe
    echo "\nCreando tabla admin_users...\n";
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        admin_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        admin_name VARCHAR(100) NOT NULL,
        admin_email VARCHAR(150) NOT NULL UNIQUE,
        admin_password CHAR(64) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3";

    $pdo->exec($sql);
    echo "✓ Tabla admin_users creada/verificada\n";

    // Crear usuario admin "profe"
    echo "\nCreando usuario administrator 'profe'...\n";
    
    $admin_name = "Profe";
    $admin_email = "profe@simbio.cat";
    $admin_password = "profe123"; // Contraseña para auditar
    $password_hash = hash('sha256', $admin_password);

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE admin_email = ?");
    $stmt->execute([$admin_email]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($exists > 0) {
        echo "⚠️  El usuario '$admin_email' ya existe\n";
        // Actualizar la contraseña
        $stmt = $pdo->prepare("UPDATE admin_users SET admin_password = ? WHERE admin_email = ?");
        $stmt->execute([$password_hash, $admin_email]);
        echo "✓ Contraseña actualizada para: $admin_email\n";
    } else {
        // Insertar nuevo admin
        $stmt = $pdo->prepare("INSERT INTO admin_users (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
        $stmt->execute([$admin_name, $admin_email, $password_hash]);
        echo "✓ Usuario administrator creado exitosamente\n";
    }

    echo "\n========================================\n";
    echo "Credenciales de Administrador:\n";
    echo "========================================\n";
    echo "Email:      $admin_email\n";
    echo "Contraseña: $admin_password\n";
    echo "========================================\n";
    echo "\nPuede acceder al panel en: /admin/login.php\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
