<?php
// api/delete_project.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $user_id = $_SESSION['user_id'];
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    
    if (!$project_id) {
        echo json_encode(['error' => 'ID de proyecto requerido']);
        exit();
    }
    
    // Verificar que el proyecto pertenece al usuario
    $stmt = $pdo->prepare('SELECT image_path, video_path FROM project WHERE project_id = ? AND user_id = ?');
    $stmt->execute([$project_id, $user_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        http_response_code(403);
        echo json_encode(['error' => 'Proyecto no encontrado o no tienes permisos']);
        exit();
    }
    
    // Eliminar archivos
    if ($project['image_path']) {
        $image_file = __DIR__ . '/..' . $project['image_path'];
        if (file_exists($image_file)) {
            unlink($image_file);
        }
    }
    
    if ($project['video_path']) {
        $video_file = __DIR__ . '/..' . $project['video_path'];
        if (file_exists($video_file)) {
            unlink($video_file);
        }
    }
    
    // Eliminar categorías asociadas (se eliminan automáticamente por FK)
    // Eliminar el proyecto
    $stmt = $pdo->prepare('DELETE FROM project WHERE project_id = ? AND user_id = ?');
    $stmt->execute([$project_id, $user_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Proyecto eliminado correctamente'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
