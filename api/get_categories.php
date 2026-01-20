<?php
// api/get_categories.php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    $sql = "SELECT DISTINCT name FROM categories ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}
?>
