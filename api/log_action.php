<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once __DIR__ . "/../admin/logs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $action = $data['action'] ?? '';
    $user_email = $_SESSION['user_email'] ?? 'unknown';

    if ($action === 'like' || $action === 'nope') {
        writeLog("User $user_email le ha dado $action a un video.");
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}
?>