<?php
require_once __DIR__ . '/../api/db.php';

try {
    // Crear tabla admin_users si no existe
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        admin_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        admin_name VARCHAR(100) NOT NULL,
        admin_email VARCHAR(150) NOT NULL UNIQUE,
        admin_password CHAR(64) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3";

    $pdo->exec($sql);

    // Crear usuario admin "profe"
    $admin_name = "Profe";
    $admin_email = "profe@simbio.cat";
    $admin_password = "profe123";
    $password_hash = hash('sha256', $admin_password);

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE admin_email = ?");
    $stmt->execute([$admin_email]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($exists === 0) {
        // Insertar nuevo admin
        $stmt = $pdo->prepare("INSERT INTO admin_users (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
        $stmt->execute([$admin_name, $admin_email, $password_hash]);
    }

} catch (Exception $e) {
    // Error silencioso
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
            <h1>✅ Simbio Admin</h1>
            <p>La configuración del panel de administración ha sido completada.</p>
            <p>Para acceder al panel, ve a:</p>
            <a href="../admin/login.php">Login Admin</a>
        </div>
    </div>
</body>
</html>

