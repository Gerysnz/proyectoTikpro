<?php
session_start();

// Headers para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . "/../api/db.php";
require_once __DIR__ . "/../feedback.php";
require_once __DIR__ . "/../admin/logs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';
    $password_hash = hash('sha256', $admin_password);

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE admin_email = ?");
    $stmt->execute([$admin_email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && hash_equals($admin['admin_password'], $password_hash)) {
        // Guardar admin en sesión
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_email'] = $admin['admin_email'];
        $_SESSION['admin_name'] = $admin['admin_name'];
        writeLog($_SESSION['admin_email'] . " ha iniciado sesión en el panel de administración");
        session_write_close();
        header("Location: projects.php");
        exit();
    } else {
        setNotification('error', 'Email o contraseña de administrador incorrectos');
        writeLog("Intento de login fallido en admin con email: " . $admin_email);
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="../styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="login-page">
    <header class="login-header">Simbio - Panel d'Administració</header>
    <main class="login-contenedor">
        <h2>Accés Administrator</h2>
        <form id="adminLoginForm" class="login-form" action="login.php" method="POST">
            <label for="admin_email">Email Administrator</label>
            <input type="email" id="admin_email" name="admin_email" required />
            <label for="admin_password">Contrasenya</label>
            <input type="password" id="admin_password" name="admin_password" required />
            <button type="submit">Accedir al Panel</button>
        </form>
        <br>
        <p class="admin-login-link">
            <a href="../login.php">Tornar a Simbio</a>
        </p>
        <?php showNotification(); ?>
    </main>
    <script src="../js/admin-login.js?t=<?php echo time(); ?>"></script>
</body>
</html>
