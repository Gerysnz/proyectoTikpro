<?php
// api/get_project.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de proyecto inválido']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $project_id = (int)$_GET['project_id'];
    
    // Obtener datos del proyecto
    $stmt = $pdo->prepare('SELECT project_id, user_id, title, description, image_path, video_path FROM project WHERE project_id = ?');
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        http_response_code(404);
        echo json_encode(['error' => 'Proyecto no encontrado']);
        exit();
    }
    
    // Verificar si el usuario es el propietario
    if ($project['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'No ets el propietari del projecte']);
        exit();
    }
    
    // Obtener categorías del proyecto
    $cat_stmt = $pdo->prepare('
        SELECT c.category_id, c.name 
        FROM project_category pc 
        JOIN categories c ON pc.category_id = c.category_id 
        WHERE pc.project_id = ?
    ');
    $cat_stmt->execute([$project_id]);
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $project['categories'] = $categories;
    
    echo json_encode($project);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}
?>
