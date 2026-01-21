<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Buscar etiquetas por texto (query param: q)
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        if (strlen($q) < 3) {
            echo json_encode([]);
            exit();
        }
        $stmt = $pdo->prepare('SELECT Category_ID as id, Name as name FROM categories WHERE Name LIKE ? LIMIT 10');
        $stmt->execute(['%' . $q . '%']);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case 'PUT':
        // Añadir etiqueta al usuario
        $input = json_decode(file_get_contents('php://input'), true);
        $label_id = $input['label_id'] ?? null;
        if (!$label_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing label_id']);
            exit();
        }
        $stmt = $pdo->prepare('INSERT IGNORE INTO user_category (user_id, category_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $label_id]);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        // Eliminar etiqueta del usuario
        $input = json_decode(file_get_contents('php://input'), true);
        $label_id = $input['label_id'] ?? null;
        if (!$label_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing label_id']);
            exit();
        }
        $stmt = $pdo->prepare('DELETE FROM user_category WHERE user_id = ? AND category_id = ?');
        $stmt->execute([$user_id, $label_id]);
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
