<?php
// api/get_all_projects.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    // Obtener todos los proyectos
    $stmt = $pdo->prepare('SELECT project_id, user_id, title, image_path FROM project ORDER BY project_id DESC');
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($projects);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}
?>
