
<?php



require_once 'db.php';
header('Content-Type: application/json');


$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 0;
$my_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Log temporal para depuración
$log_fecha = date('Y-m-d_H-i-s');
$log_file = __DIR__ . '/../admin/logs/chat_debug_' . $log_fecha . '.txt';
$log_line = "project_id=$project_id | partner_id=$partner_id | my_user_id=$my_user_id\n";
file_put_contents($log_file, $log_line, FILE_APPEND);

if (!$project_id || !$partner_id || !$my_user_id) {
    echo json_encode(['error' => 'Missing project_id, partner_id o user_id']);
    exit;
}

try {
    // Obtener mensajes del chat
    // $my_user_id ya viene de GET
    $stmt = $pdo->prepare("SELECT m.message_id, m.remitent_id, m.destination_id, m.content, m.created_at, u.user_name as remitent_name
        FROM message m
        JOIN users u ON m.remitent_id = u.user_id
        WHERE m.project_id = ?
          AND ((m.remitent_id = ? AND m.destination_id = ?)
            OR (m.remitent_id = ? AND m.destination_id = ?))
        ORDER BY m.created_at ASC");
    $stmt->execute([$project_id, $my_user_id, $partner_id, $partner_id, $my_user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos del partner
    $stmt2 = $pdo->prepare("SELECT user_id, user_name, entity_name FROM users WHERE user_id = ?");
    $stmt2->execute([$partner_id]);
    $partner = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Obtener datos del proyecto
    $stmt3 = $pdo->prepare("SELECT project_id, title, image_path FROM project WHERE project_id = ?");
    $stmt3->execute([$project_id]);
    $project = $stmt3->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'messages' => $messages,
        'partner' => $partner,
        'project' => $project
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
