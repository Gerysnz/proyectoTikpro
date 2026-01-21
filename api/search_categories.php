<?php
// api/search_categories.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($q) < 3) {
    echo json_encode([]);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener IDs de categorías ya asignadas al usuario
    $stmt = $pdo->prepare('SELECT category_id FROM user_category WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $asignadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $asignadas = $asignadas ? $asignadas : [0]; // Para evitar error en IN si está vacío

    // Buscar categorías que coincidan y no estén asignadas
    $in = str_repeat('?,', count($asignadas) - 1) . '?';
    $sql = "SELECT Category_ID, Name FROM categories WHERE Name LIKE ? AND Category_ID NOT IN ($in) ORDER BY Name LIMIT 20";
    $params = array_merge(['%' . $q . '%'], $asignadas);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}
