<?php
session_start();

// Headers para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0");
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

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

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $project_id = $_POST['project_id'] ?? 0;
    
    if ($action === 'delete' && $project_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM project WHERE project_id = ?");
        $stmt->execute([$project_id]);
        writeLog($_SESSION['admin_email'] . " eliminó el proyecto #" . $project_id);
    }
    
    // Redirigir sin parámetros POST
    header("Location: projects.php");
    exit();
}

// Obtener proyectos
$stmt = $pdo->query("SELECT p.project_id, p.title, p.description, p.video_path, u.entity_name FROM project p JOIN users u ON p.user_id = u.user_id ORDER BY p.project_id DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simbio - Moderación de Proyectos</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="../styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="admin-page">
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>🎬 Moderación de Proyectos</h1>
            <div class="admin-user-info">
                <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <form action="logout.php" method="POST" class="admin-logout-form" style="display: inline;">
                    <button type="submit" class="logout-btn">Tancar Sessió</button>
                </form>
            </div>
        </div>

        <!-- Projects List -->
        <div class="section">
            <h2>Projectes per a Moderar</h2>
            <?php if (count($projects) > 0): ?>
                <div style="overflow-x: auto; border-radius: 8px;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 180px;">Títol</th>
                                <th style="width: 150px;">Entitat</th>
                                <th style="flex: 1; min-width: 200px;">Descripció</th>
                                <th style="width: 120px;">Vídeo</th>
                                <th style="width: 100px;">Estat</th>
                                <th style="width: 120px;">Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><strong>#<?php echo $project['project_id']; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars(substr($project['title'], 0, 25)); ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr($project['entity_name'], 0, 20)); ?></td>
                                    <td>
                                        <small style="color: #666;"><?php echo htmlspecialchars(substr($project['description'], 0, 60)); ?><?php echo strlen($project['description']) > 60 ? '...' : ''; ?></small>
                                    </td>
                                    <td>
                                        <?php if ($project['video_path']): ?>
                                            <button class="video-btn" data-video="<?php echo htmlspecialchars($project['video_path']); ?>">
                                                ▶ Veure
                                            </button>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="background: #e3f2fd; padding: 5px 10px; border-radius: 5px; font-size: 11px; color: #1976d2; font-weight: 500;">Pendent</span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                                            <button type="submit" name="action" value="delete" class="delete-btn" onclick="return confirm('Estàs segur que vols eliminar aquest projecte?')" style="font-size: 12px;">✕ Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">No hi ha projectes per a moderar.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Video -->
    <div id="videoModal" class="video-modal" style="display: none;">
        <div class="video-modal-content">
            <span class="video-modal-close">&times;</span>
            <video id="modalVideo" width="100%" height="auto" controls>
                <source id="videoSource" type="video/mp4">
                El teu navegador no suporta vídeos.
            </video>
        </div>
    </div>

    <script src="../js/admin-projects.js?t=<?php echo time(); ?>"></script>
</body>
</html>
