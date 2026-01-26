<?php
session_start();

// Headers para evitar caché en Chrome y otros navegadores
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0");
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

require_once __DIR__ . "/../api/db.php";
require_once __DIR__ . "/logs.php";

// Verificar que el admin está autenticado
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    // Limpiar sesión completamente
    session_destroy();
    session_unset();
    header("Location: login.php");
    exit();
}

// Validar que el usuario aún existe en la BD
$stmt = $pdo->prepare("SELECT admin_id FROM admin_users WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
if (!$stmt->fetch()) {
    // Usuario no existe, logout
    session_destroy();
    session_unset();
    header("Location: login.php");
    exit();
}

// Obtener estadísticas de la plataforma
$stats = [];

// Total de usuarios
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
$stmt->execute();
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de proyectos
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM project");
$stmt->execute();
$stats['total_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de categorías
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM categories");
$stmt->execute();
$stats['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de likes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM likes");
$stmt->execute();
$stats['total_likes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Usuarios por tipo
$stmt = $pdo->prepare("SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
$stmt->execute();
$user_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Últimos 5 proyectos
$stmt = $pdo->prepare("SELECT p.project_id, p.title, p.description, u.entity_name FROM project p JOIN users u ON p.user_id = u.user_id ORDER BY p.project_id DESC LIMIT 5");
$stmt->execute();
$recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener últimos logs
$logDir = __DIR__ . "/logs";
$logFiles = [];
if (is_dir($logDir)) {
    $files = scandir($logDir, SCANDIR_SORT_DESCENDING);
    $logFiles = array_filter($files, function($f) { return $f !== '.' && $f !== '..' && pathinfo($f, PATHINFO_EXTENSION) === 'txt'; });
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Panel d'Administració</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="../styles.css?=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .admin-header h1 {
            margin: 0;
            color: #333;
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-user-info span {
            color: #666;
            font-size: 14px;
        }

        .logout-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #ff5252;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.9;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
        }

        .user-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .user-type-badge {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .user-type-badge .type-name {
            font-weight: 600;
            color: #333;
            text-transform: capitalize;
        }

        .user-type-badge .type-count {
            font-size: 24px;
            color: #667eea;
            font-weight: 700;
            margin-top: 10px;
        }

        .projects-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .projects-list li {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .projects-list li:last-child {
            border-bottom: none;
        }

        .project-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .project-info p {
            margin: 0;
            font-size: 13px;
            color: #999;
        }

        .project-entity {
            color: #667eea;
            font-weight: 600;
            font-size: 13px;
        }

        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }

        .menu-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }

        .menu-item:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .menu-item h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .menu-item p {
            margin: 0;
            font-size: 13px;
            opacity: 0.8;
        }

        .logs-section {
            max-height: 400px;
            overflow-y: auto;
        }

        .log-file {
            background: #f5f5f5;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .log-file:hover {
            background: #e0e0e0;
        }

        .log-file a {
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body style="background-color: #f5f7fa;">
    <div class="admin-container">
        <div class="admin-header">
            <h1>🎛️ Panel d'Administració - Simbio</h1>
            <div class="admin-user-info">
                <span>Benvingut/a, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></span>
                <form action="logout.php" method="POST" style="margin: 0;">
                    <button type="submit" class="logout-btn">Tancar Sessió</button>
                </form>
            </div>
        </div>

        <!-- Menú de Administración -->
        <div class="admin-menu">
            <a href="users.php" class="menu-item">
                <h3>👥 Usuaris</h3>
                <p>Gestionar usuaris i centres</p>
            </a>
            <a href="projects.php" class="menu-item">
                <h3>📁 Projectes</h3>
                <p>Gestionar tots els projectes</p>
            </a>
            <a href="categories.php" class="menu-item">
                <h3>🏷️ Categories</h3>
                <p>Gestionar categories</p>
            </a>
            <a href="logs.php" class="menu-item">
                <h3>📋 Logs</h3>
                <p>Visualitzar logs del sistema</p>
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total d'Usuaris</h3>
                <div class="number"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Total de Projectes</h3>
                <div class="number"><?php echo $stats['total_projects']; ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>Total de Likes</h3>
                <div class="number"><?php echo $stats['total_likes']; ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>Total de Categories</h3>
                <div class="number"><?php echo $stats['total_categories']; ?></div>
            </div>
        </div>

        <!-- Usuaris per tipus -->
        <div class="section">
            <h2>Usuaris per Tipus</h2>
            <div class="user-types">
                <?php foreach ($user_types as $type): ?>
                    <div class="user-type-badge">
                        <div class="type-name">
                            <?php echo $type['user_type'] === 'center' ? '🏫 Centres' : '🏢 Empreses'; ?>
                        </div>
                        <div class="type-count"><?php echo $type['count']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Projectes recents -->
        <div class="section">
            <h2>Últims Projectes</h2>
            <?php if (!empty($recent_projects)): ?>
                <ul class="projects-list">
                    <?php foreach ($recent_projects as $project): ?>
                        <li>
                            <div class="project-info">
                                <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                                <p class="project-entity">📌 <?php echo htmlspecialchars($project['entity_name']); ?></p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-size: 12px; color: #999;">
                                    ID: <?php echo $project['project_id']; ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color: #999; text-align: center;">No hi ha projectes..</p>
            <?php endif; ?>
        </div>

        <!-- Logs recents -->
        <div class="section">
            <h2>Fitxers de Logs Disponibles</h2>
            <div class="logs-section">
                <?php if (!empty($logFiles)): ?>
                    <?php foreach (array_slice($logFiles, 0, 10) as $file): ?>
                        <div class="log-file">
                            <a href="view_log.php?file=<?php echo urlencode($file); ?>">
                                📄 <?php echo htmlspecialchars($file); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #999;">No hi ha logs disponibles.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        // Validar que el admin está autenticado
        if (!<?php echo isset($_SESSION['admin_id']) ? 'true' : 'false'; ?>) {
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>
