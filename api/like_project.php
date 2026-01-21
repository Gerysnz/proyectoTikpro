<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . "/../admin/logs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';
    $project_id = $data['proyectoId'] ?? null;
    $user_email = $_SESSION['user_email'] ?? 'unknown';
    $user_id = $_SESSION['user_id'];

    if (($action === 'like' || $action === 'nope') && $project_id) {
        try {
            if ($action === 'like') {
                $stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, project_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $project_id]);
            } else if ($action === 'nope') {
                // Si quieres guardar los 'nope', crea una tabla o ignora
            }

            // Obtener título del proyecto
            $stmt = $pdo->prepare("SELECT title FROM project WHERE project_id = ?");
            $stmt->execute([$project_id]);
            $title = $stmt->fetchColumn();

            // Log con discover.php como origen
            $accion_txt = $action === 'like' ? 'le ha dado like' : 'le ha dado nope';
            writeLog("[discover.php] User $user_email $accion_txt al video $project_id ($title)");

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DB error', 'msg' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}
?>
