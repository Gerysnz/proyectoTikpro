<?php
session_start();

// Headers para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . "/../api/db.php";
require_once __DIR__ . "/logs.php";

// Verificar autenticación
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    session_destroy();
    session_unset();
    header("Location: login.php");
    exit();
}

// Validar que el usuario aún existe en la BD
$stmt = $pdo->prepare("SELECT admin_id FROM admin_users WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
if (!$stmt->fetch()) {
    session_destroy();
    session_unset();
    header("Location: login.php");
    exit();
}

// Obtener estadísticas del sistema
$stats = [];

// Total de usuarios
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de proyectos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM project");
$stats['projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de categorías
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$stats['categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Últimos proyectos
$stmt = $pdo->query("SELECT p.project_id, p.title, u.entity_name FROM project p JOIN users u ON p.user_id = u.user_id");
$recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Dashboard d'Administració</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="../styles.css?t=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="admin-page admin-dashboard">
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1>📊 Dashboard d'Administració</h1>
                <p>Benvingut al panell de control de Simbio</p>
            </div>
            <div class="header-info">
                <span><strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></span>
                <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
                <a href="logout.php" class="logout-btn">Tancar Sessió</a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>👥 Usuaris</h3>
                <p class="number"><?php echo $stats['users']; ?></p>
            </div>
            <div class="stat-card">
                <h3>📹 Projectes</h3>
                <p class="number"><?php echo $stats['projects']; ?></p>
            </div>
            <div class="stat-card">
                <h3>📂 Categories</h3>
                <p class="number"><?php echo $stats['categories']; ?></p>
            </div>
        </div>

        <!-- Menú d'Administració -->
        <div class="menu-grid">
            <a href="projects.php" class="menu-card">
                <div class="icon">📹</div>
                <h3>Gestionar Projectes</h3>
                <p>Veure, editar i eliminar projectes del sistema</p>
            </a>
        </div>
    </div>
</body>
</html>
