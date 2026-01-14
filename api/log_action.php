<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once "../admin/logs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $project_id = $_POST['project_id'] ?? '';
    $user_email = $_SESSION['user_email'] ?? 'unknown';

    if ($action && $project_id) {
        $mensaje = "User $user_email $action project $project_id";
        writeLog($mensaje);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>