<?php
// api/get_profile.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datos del usuario
    $stmt = $pdo->prepare('SELECT user_name, user_surname, entity_name, email, phone_number FROM users WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit();
    }

    // Etiquetas/categorías del usuario
    $stmt = $pdo->prepare('SELECT c.Category_ID as id, c.Name as name FROM user_category uc JOIN categories c ON uc.category_id = c.Category_ID WHERE uc.user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Proyectos del usuario (SOLO LOS NO ELIMINADOS)
    $stmt = $pdo->prepare('SELECT project_id, title, image_path FROM project WHERE user_id = ? AND is_deleted = FALSE');
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'user' => $user,
        'tags' => $tags,
        'projects' => $projects
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}