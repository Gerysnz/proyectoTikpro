<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $project_id = $data['project_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$project_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing project_id']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count FROM likes 
            WHERE user_id = ? AND project_id = ?
        ");
        $stmt->execute([$user_id, $project_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'is_liked' => $result['count'] > 0
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'DB error']);
    }
}
?>
