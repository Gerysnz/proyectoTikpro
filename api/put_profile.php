<?php
// api/put_profile.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit();
}

// Validación básica
$user_name = trim($data['user_name'] ?? '');
$user_surname = trim($data['user_surname'] ?? '');
$entity_name = trim($data['entity_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone_number = trim($data['phone_number'] ?? '');

if ($user_name === '' || $user_surname === '' || $entity_name === '' || $email === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('UPDATE users SET user_name = ?, user_surname = ?, entity_name = ?, email = ?, phone_number = ? WHERE user_id = ?');
    $stmt->execute([$user_name, $user_surname, $entity_name, $email, $phone_number, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor']);
}
