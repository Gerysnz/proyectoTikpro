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
    
    // SOFT-DELETE: Solo marcar como eliminado, NO borrar archivos
    $stmt = $pdo->prepare('UPDATE project SET is_deleted = TRUE WHERE project_id = ? AND user_id = ?');
    $stmt->execute([$project_id, $user_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Proyecto eliminado (soft-delete). Se ha ocultado pero no se ha borrado permanentemente.'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>