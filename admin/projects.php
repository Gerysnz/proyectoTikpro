<?php
session_start();
require_once __DIR__ . "/../api/db.php";
require_once __DIR__ . "/logs.php";

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $project_id = $_POST['project_id'] ?? 0;
    
    if ($action === 'soft_delete' && $project_id > 0) {
        $stmt = $pdo->prepare("UPDATE project SET is_deleted = TRUE WHERE project_id = ?");
        $stmt->execute([$project_id]);
        writeLog($_SESSION['admin_email'] . " hizo soft-delete del proyecto #" . $project_id);
    }
    
    header("Location: projects.php");
    exit();
}

// Obtener proyectos
$stmt = $pdo->query("SELECT p.project_id, p.title, p.description, p.video_path, u.entity_name, p.is_deleted FROM project p JOIN users u ON p.user_id = u.user_id ORDER BY p.is_deleted, p.project_id DESC");
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
            <h1 style="margin: 0; font-size: 28px;">Projectes</h1>
            <div class="admin-user-info" style="margin-top: 15px; display: flex; align-items: center; gap: 15px;">
                <span style="font-size: 16px; opacity: 0.9;"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                <a href="logout.php" class="logout-btn" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.3s;">Tancar Sessió</a>
            </div>
        </div>

        <!-- Projects List -->
        <div class="section">
            <h2>Projectes per a Moderar</h2>
            
            <?php if (count($projects) > 0): ?>
                <div style="overflow-x: auto; border-radius: 12px; background: white; padding: 5px;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 180px;">Títol</th>
                                <th style="width: 150px;">Entitat</th>
                                <th style="flex: 1; min-width: 200px;">Descripció</th>
                                <th style="width: 120px;">Vídeo</th>
                                <th style="width: 120px;">Estat</th>
                                <th style="width: 140px;">Accions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr class="<?php echo $project['is_deleted'] == 1 ? 'deleted-row' : ''; ?>">
                                    <td style="font-weight: bold; color: #667eea;">#<?php echo $project['project_id']; ?></td>
                                    <td><strong style="color: #333;"><?php echo htmlspecialchars(substr($project['title'], 0, 25)); ?></strong></td>
                                    <td style="color: #666;"><?php echo htmlspecialchars(substr($project['entity_name'], 0, 20)); ?></td>
                                    <td>
                                        <small style="color: #888;"><?php echo htmlspecialchars(substr($project['description'], 0, 60)); ?><?php echo strlen($project['description']) > 60 ? '...' : ''; ?></small>
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
                                        <?php if ($project['is_deleted'] == 1): ?>
                                            <span class="status-badge status-deleted">
                                                🗑️ ELIMINAT
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-active">
                                                ✅ ACTIU
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($project['is_deleted'] == 0): ?>
                                            <form method="POST" style="display: inline; margin: 0;">
                                                <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                                                <button type="submit" name="action" value="soft_delete" 
                                                        class="btn-soft-delete"
                                                        onclick="return confirm('¿Soft-delete aquest projecte? S\'ocultarà dels usuaris.')">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: #f8f9ff; border-radius: 12px;">
                    <p style="color: #999; font-size: 16px;">No hi ha projectes per a moderar.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Video -->
    <div id="videoModal" class="video-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8);">
        <div class="video-modal-content" style="position: relative; background: white; margin: 5% auto; padding: 20px; width: 80%; max-width: 800px; border-radius: 12px;">
            <span class="video-modal-close" style="position: absolute; right: 20px; top: 15px; font-size: 28px; cursor: pointer; color: #333;">&times;</span>
            <video id="modalVideo" width="100%" height="auto" controls style="border-radius: 8px;">
                <source id="videoSource" type="video/mp4">
                El teu navegador no suporta vídeos.
            </video>
        </div>
    </div>

    <script>
    // Script para ver videos
    document.querySelectorAll('.video-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoPath = this.dataset.video;
            const modal = document.getElementById('videoModal');
            const videoSource = document.getElementById('videoSource');
            const modalVideo = document.getElementById('modalVideo');
            
            videoSource.src = videoPath;
            modalVideo.load();
            modal.style.display = 'block';
            
            // Cerrar modal
            document.querySelector('.video-modal-close').onclick = function() {
                modal.style.display = 'none';
                modalVideo.pause();
            };
            
            // Cerrar al hacer click fuera
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    modalVideo.pause();
                }
            };
        });
    });
    </script>
</body>
</html>