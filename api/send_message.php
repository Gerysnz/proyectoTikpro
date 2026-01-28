<?php
require_once 'db.php';
header('Content-Type: application/json');

// Recoger datos por POST
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$partner_id = isset($_POST['partner_id']) ? intval($_POST['partner_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if (!$project_id || !$partner_id || !$user_id || $content === '') {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

// El destinatario es el partner
$remitent_id = $user_id;
$destination_id = $partner_id;

try {
    $stmt = $pdo->prepare("INSERT INTO message (remitent_id, destination_id, project_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$remitent_id, $destination_id, $project_id, $content]);

    // Log detallado
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);
    // Obtener emails/nombres de usuarios
    $remitent_email = '';
    $destination_email = '';
    $remitent_name = '';
    $destination_name = '';
    $project_title = '';
    try {
        $stmtU = $pdo->prepare("SELECT email, user_name FROM users WHERE user_id = ?");
        $stmtU->execute([$remitent_id]);
        $u1 = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($u1) { $remitent_email = $u1['email']; $remitent_name = $u1['user_name']; }
        $stmtU->execute([$destination_id]);
        $u2 = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($u2) { $destination_email = $u2['email']; $destination_name = $u2['user_name']; }
        $stmtP = $pdo->prepare("SELECT title FROM project WHERE project_id = ?");
        $stmtP->execute([$project_id]);
        $p = $stmtP->fetch(PDO::FETCH_ASSOC);
        if ($p) { $project_title = $p['title']; }
    } catch (Exception $e) {}
    $linea = "[$fecha] [chat.php] Mensaje: '$content' | Proyecto: $project_title ($project_id) | De: $remitent_name <$remitent_email> ($remitent_id) | Para: $destination_name <$destination_email> ($destination_id)" . PHP_EOL;
    $logfile = __DIR__ . "/../admin/logs/" . date('Y-m-d') . ".txt";
    file_put_contents($logfile, $linea, FILE_APPEND);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
